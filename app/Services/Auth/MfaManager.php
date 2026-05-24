<?php

namespace App\Services\Auth;

use App\Jobs\Auth\SendLoginOtpEmailJob;
use App\Jobs\Auth\SendLoginOtpSmsJob;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserMfaMethod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MfaManager
{
    public const SETTING_REQUIRE_ALL = 'security.mfa.require_all';

    public function __construct(
        protected OtpChallengeService $otpChallengeService,
        protected TotpService $totpService,
        protected TrustedDeviceService $trustedDeviceService,
    ) {
    }

    public function featureEnabled(): bool
    {
        return (bool) config('mfa.enabled', true);
    }

    public function systemMfaRequired(): bool
    {
        if (! $this->featureEnabled()) {
            return false;
        }

        return SystemSetting::bool(
            self::SETTING_REQUIRE_ALL,
            (bool) config('mfa.require_all', false),
        );
    }

    public function requiresMfa(User $user): bool
    {
        return $this->featureEnabled()
            && ($this->systemMfaRequired() || (bool) $user->mfa_enabled);
    }

    public function smsEnabled(): bool
    {
        return (bool) config('services.mobitel.enabled', false);
    }

    public function enableForUser(User $user): void
    {
        $user->forceFill(['mfa_enabled' => true])->save();
        $this->syncEmailMethod($user);
    }

    public function disableForUser(User $user): void
    {
        if ($this->systemMfaRequired()) {
            throw ValidationException::withMessages([
                'account_mfa_enabled' => 'MFA is required for all users by the system administrator.',
            ]);
        }

        $user->forceFill(['mfa_enabled' => false])->save();
        $this->trustedDeviceService->revokeAll($user);
    }

    public function setSystemMfaRequired(bool $required): void
    {
        SystemSetting::setBool(self::SETTING_REQUIRE_ALL, $required);
    }

    public function syncEmailMethod(User $user): UserMfaMethod
    {
        $method = $user->mfaMethods()->updateOrCreate(
            ['method' => UserMfaMethod::METHOD_EMAIL],
            [
                'enabled' => true,
                'verified_at' => $user->email_verified_at ?? now(),
                'destination_snapshot' => $user->email,
            ],
        );

        if (! $user->mfaMethods()->where('preferred', true)->exists()) {
            $user->mfaMethods()->update(['preferred' => false]);
            $method->forceFill(['preferred' => true])->save();
        }

        return $method;
    }

    public function enabledMethods(User $user): Collection
    {
        if (! $user->mfaMethods()->where('method', UserMfaMethod::METHOD_EMAIL)->exists()) {
            $this->syncEmailMethod($user);
        }

        return $user->mfaMethods()
            ->where('enabled', true)
            ->orderByDesc('preferred')
            ->get()
            ->reject(fn (UserMfaMethod $method) => $method->isSms() && ! $this->smsEnabled())
            ->values();
    }

    public function preferredMethod(User $user): UserMfaMethod
    {
        $methods = $this->enabledMethods($user);
        $preferredMethod = $methods->firstWhere('preferred', true) ?? $methods->first();

        return $preferredMethod ?? $this->syncEmailMethod($user);
    }

    public function methodForUser(User $user, string $method): ?UserMfaMethod
    {
        if ($method === UserMfaMethod::METHOD_EMAIL) {
            return $user->mfaMethods()->where('method', UserMfaMethod::METHOD_EMAIL)->first()
                ?? $this->syncEmailMethod($user);
        }

        if ($method === UserMfaMethod::METHOD_SMS && ! $this->smsEnabled()) {
            return null;
        }

        return $user->mfaMethods()
            ->where('method', $method)
            ->where('enabled', true)
            ->first();
    }

    public function setPreferredMethod(User $user, string $method): void
    {
        $target = $this->methodForUser($user, $method);

        if (! $target) {
            throw ValidationException::withMessages([
                'preferred_method' => 'The selected MFA method is not enabled for this account.',
            ]);
        }

        $user->mfaMethods()->update(['preferred' => false]);
        $target->forceFill(['preferred' => true])->save();
    }

    public function createOrUpdateTotpMethod(User $user, string $secret): UserMfaMethod
    {
        return $user->mfaMethods()->updateOrCreate(
            ['method' => UserMfaMethod::METHOD_TOTP],
            [
                'enabled' => false,
                'preferred' => false,
                'secret_encrypted' => Crypt::encryptString($secret),
                'destination_snapshot' => $user->email,
                'meta' => [
                    'issuer' => config('mfa.totp.issuer', config('app.name')),
                ],
            ],
        );
    }

    public function enableTotp(User $user, string $secret): array
    {
        $method = $user->mfaMethods()->updateOrCreate(
            ['method' => UserMfaMethod::METHOD_TOTP],
            [
                'enabled' => true,
                'verified_at' => now(),
                'secret_encrypted' => Crypt::encryptString($secret),
                'destination_snapshot' => $user->email,
            ],
        );

        if (! $user->mfaMethods()->where('preferred', true)->where('enabled', true)->exists()) {
            $this->setPreferredMethod($user, UserMfaMethod::METHOD_TOTP);
        }

        $this->enableForUser($user);

        return $this->totpService->generateRecoveryCodes($user);
    }

    public function disableMethod(User $user, string $method): void
    {
        if ($method === UserMfaMethod::METHOD_EMAIL) {
            throw ValidationException::withMessages([
                'method' => 'Email OTP is the baseline login factor and cannot be disabled.',
            ]);
        }

        $record = $user->mfaMethods()->where('method', $method)->first();

        if (! $record) {
            return;
        }

        $record->forceFill([
            'enabled' => false,
            'preferred' => false,
            'verified_at' => null,
            'secret_encrypted' => $method === UserMfaMethod::METHOD_TOTP ? null : $record->secret_encrypted,
        ])->save();

        $this->trustedDeviceService->revokeAll($user);
        $this->syncEmailMethod($user);
        $this->setPreferredMethod($user, $this->preferredMethod($user)->method);
    }

    public function enrollSms(User $user, string $destination): UserMfaMethod
    {
        if (! $this->smsEnabled()) {
            throw ValidationException::withMessages([
                'sms_phone' => 'SMS OTP is disabled by the system administrator.',
            ]);
        }

        return $user->mfaMethods()->updateOrCreate(
            ['method' => UserMfaMethod::METHOD_SMS],
            [
                'enabled' => false,
                'preferred' => false,
                'verified_at' => null,
                'destination_snapshot' => $destination,
            ],
        );
    }

    public function activateSms(User $user, string $destination): UserMfaMethod
    {
        if (! $this->smsEnabled()) {
            throw ValidationException::withMessages([
                'sms_code' => 'SMS OTP is disabled by the system administrator.',
            ]);
        }

        $method = $user->mfaMethods()->updateOrCreate(
            ['method' => UserMfaMethod::METHOD_SMS],
            [
                'enabled' => true,
                'verified_at' => now(),
                'destination_snapshot' => $destination,
            ],
        );

        $this->enableForUser($user);

        return $method;
    }

    public function sendChallenge(User $user, string $challengeId, string $method): void
    {
        $record = $this->methodForUser($user, $method);

        if (! $record) {
            throw ValidationException::withMessages([
                'code' => 'The selected MFA method is not available.',
            ]);
        }

        if ($record->isTotp()) {
            return;
        }

        $code = $this->otpChallengeService->issueCode($challengeId, $method);

        if ($record->isEmail()) {
            $this->dispatchEmailOtp($user, $code);
        } elseif ($record->isSms()) {
            $this->dispatchSmsOtp($record->destination_snapshot ?: $user->contact, $code);
        }

        Log::info('mfa.challenge.sent', [
            'user_id' => $user->id,
            'method' => $method,
            'challenge_id' => $challengeId,
        ]);
    }

    public function verifyChallenge(User $user, string $challengeId, string $method, string $code, bool $useRecoveryCode = false): bool
    {
        $record = $this->methodForUser($user, $method);

        if (! $record) {
            return false;
        }

        $verified = false;

        if ($useRecoveryCode) {
            $verified = $this->totpService->consumeRecoveryCode($user, $code);
        } elseif ($record->isTotp()) {
            $secret = Crypt::decryptString((string) $record->secret_encrypted);
            $verified = $this->totpService->verifyCode($secret, $code);
        } else {
            $verified = $this->otpChallengeService->verify($challengeId, $method, $code);
        }

        Log::info('mfa.challenge.verified', [
            'user_id' => $user->id,
            'method' => $method,
            'challenge_id' => $challengeId,
            'success' => $verified,
            'used_recovery_code' => $useRecoveryCode,
        ]);

        return $verified;
    }

    public function maskDestination(UserMfaMethod $method, User $user): ?string
    {
        if ($method->isTotp()) {
            return null;
        }

        $destination = $method->destination_snapshot ?: ($method->isEmail() ? $user->email : $user->contact);

        if ($method->isEmail()) {
            [$local, $domain] = explode('@', $destination, 2);
            $maskedLocal = substr($local, 0, 2).str_repeat('*', max(strlen($local) - 2, 1));

            return $maskedLocal.'@'.$domain;
        }

        return str_repeat('*', max(strlen($destination) - 4, 0)).substr($destination, -4);
    }

    public function ensureCurrentStateAfterEmailChange(User $user): void
    {
        $this->syncEmailMethod($user);
        $this->trustedDeviceService->revokeAll($user);
    }

    public function ensureCurrentStateAfterContactChange(User $user): void
    {
        $user->mfaMethods()
            ->where('method', UserMfaMethod::METHOD_SMS)
            ->update([
                'enabled' => false,
                'preferred' => false,
                'verified_at' => null,
            ]);

        $this->trustedDeviceService->revokeAll($user);
        $this->syncEmailMethod($user);

        if (! $user->mfaMethods()->where('preferred', true)->where('enabled', true)->exists()) {
            $this->setPreferredMethod($user, UserMfaMethod::METHOD_EMAIL);
        }
    }

    public function handleCredentialSensitiveChange(User $user): void
    {
        $this->trustedDeviceService->revokeAll($user);
    }

    public function otpSecondsUntilResend(string $challengeId, string $method): int
    {
        return $this->otpChallengeService->secondsUntilResend($challengeId, $method);
    }

    public function invalidateChallenge(string $challengeId, array $methods): void
    {
        $this->otpChallengeService->invalidateAll($challengeId, $methods);
    }

    protected function dispatchEmailOtp(User $user, string $code): void
    {
        if ($this->shouldDispatchOtpSynchronously()) {
            SendLoginOtpEmailJob::dispatchSync($user, $code);

            return;
        }

        SendLoginOtpEmailJob::dispatch($user, $code)->afterResponse();
    }

    protected function dispatchSmsOtp(string $destination, string $code): void
    {
        if ($this->shouldDispatchOtpSynchronously()) {
            SendLoginOtpSmsJob::dispatchSync($destination, $code);

            return;
        }

        SendLoginOtpSmsJob::dispatch($destination, $code)->afterResponse();
    }

    protected function shouldDispatchOtpSynchronously(): bool
    {
        return app()->environment(['local', 'testing'])
            || config('queue.default') === 'sync';
    }
}
