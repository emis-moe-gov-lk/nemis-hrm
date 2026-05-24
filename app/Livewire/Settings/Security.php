<?php

namespace App\Livewire\Settings;

use App\Jobs\Auth\SendLoginOtpSmsJob;
use App\Models\TrustedDevice;
use App\Models\UserMfaMethod;
use App\Services\Auth\MfaManager;
use App\Services\Auth\OtpChallengeService;
use App\Services\Auth\TotpService;
use App\Services\Auth\TrustedDeviceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use RuntimeException;

class Security extends Component
{
    public string $security_password = '';

    public string $totp_secret = '';

    public string $totp_code = '';

    public bool $show_totp_enrollment = false;

    public array $generated_recovery_codes = [];

    public string $sms_phone = '';

    public string $sms_code = '';

    public ?string $sms_enrollment_challenge = null;

    public bool $sms_verification_pending = false;

    public bool $account_mfa_enabled = false;

    public bool $system_mfa_required = false;

    public function mount(): void
    {
        $user = $this->user();
        app(MfaManager::class)->syncEmailMethod($user);
        $this->refreshMfaPolicyState();

        $smsMethod = $user->mfaMethods()->where('method', UserMfaMethod::METHOD_SMS)->first();
        $this->sms_phone = $smsMethod?->destination_snapshot ?: $user->contact;
    }

    public function enableAccountMfa(): void
    {
        $this->confirmPassword();

        app(MfaManager::class)->enableForUser($this->user());
        $this->refreshMfaPolicyState();
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function disableAccountMfa(): void
    {
        $this->confirmPassword();

        app(MfaManager::class)->disableForUser($this->user());
        $this->refreshMfaPolicyState();
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function enableSystemMfaRequirement(): void
    {
        $this->authorizeSuperAdmin();
        $this->confirmPassword();

        app(MfaManager::class)->setSystemMfaRequired(true);
        $this->refreshMfaPolicyState();
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function disableSystemMfaRequirement(): void
    {
        $this->authorizeSuperAdmin();
        $this->confirmPassword();

        app(MfaManager::class)->setSystemMfaRequired(false);
        $this->refreshMfaPolicyState();
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function startTotpEnrollment(): void
    {
        $this->confirmPassword();

        $this->totp_secret = app(TotpService::class)->generateSecret();
        $this->show_totp_enrollment = true;
        $this->totp_code = '';
        $this->generated_recovery_codes = [];
    }

    public function cancelTotpEnrollment(): void
    {
        $this->show_totp_enrollment = false;
        $this->totp_secret = '';
        $this->totp_code = '';
    }

    public function confirmTotpEnrollment(): void
    {
        $this->confirmPassword();

        $this->validate([
            'totp_code' => ['required', 'string', 'max:12'],
        ]);

        $totpService = app(TotpService::class);

        if (! $totpService->verifyCode($this->totp_secret, $this->totp_code)) {
            throw ValidationException::withMessages([
                'totp_code' => 'The authenticator code is invalid.',
            ]);
        }

        $this->generated_recovery_codes = app(MfaManager::class)->enableTotp($this->user(), $this->totp_secret);
        app(MfaManager::class)->handleCredentialSensitiveChange($this->user());

        $this->show_totp_enrollment = false;
        $this->totp_code = '';
        $this->totp_secret = '';
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function disableTotp(): void
    {
        $this->confirmPassword();

        app(MfaManager::class)->disableMethod($this->user(), UserMfaMethod::METHOD_TOTP);
        $this->generated_recovery_codes = [];
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function regenerateRecoveryCodes(): void
    {
        $this->confirmPassword();

        $this->generated_recovery_codes = app(TotpService::class)->generateRecoveryCodes($this->user());
        app(MfaManager::class)->handleCredentialSensitiveChange($this->user());
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function sendSmsEnrollmentCode(): void
    {
        $this->confirmPassword();

        $validated = $this->validate([
            'sms_phone' => ['required', 'string', 'regex:/^(0\d{9}|94\d{9})$/'],
        ]);

        $challengeId = (string) Str::uuid();
        $this->sms_enrollment_challenge = $challengeId;

        app(MfaManager::class)->enrollSms($this->user(), $validated['sms_phone']);

        try {
            $code = app(OtpChallengeService::class)->issueCode($challengeId, UserMfaMethod::METHOD_SMS);
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages([
                'sms_code' => $exception->getMessage(),
            ]);
        }

        if (app()->environment(['local', 'testing']) || config('queue.default') === 'sync') {
            SendLoginOtpSmsJob::dispatchSync($validated['sms_phone'], $code);
        } else {
            SendLoginOtpSmsJob::dispatch($validated['sms_phone'], $code)->afterResponse();
        }

        $this->sms_verification_pending = true;
        $this->sms_code = '';
        $this->dispatch('security-updated');
    }

    public function verifySmsEnrollment(): void
    {
        $this->confirmPassword();

        $this->validate([
            'sms_code' => ['required', 'string', 'max:12'],
        ]);

        if (! $this->sms_enrollment_challenge) {
            throw ValidationException::withMessages([
                'sms_code' => 'No pending SMS verification challenge was found.',
            ]);
        }

        if (! app(OtpChallengeService::class)->verify($this->sms_enrollment_challenge, UserMfaMethod::METHOD_SMS, $this->sms_code)) {
            throw ValidationException::withMessages([
                'sms_code' => 'The SMS verification code is invalid or expired.',
            ]);
        }

        app(MfaManager::class)->activateSms($this->user(), $this->sms_phone);
        app(MfaManager::class)->handleCredentialSensitiveChange($this->user());

        $this->sms_verification_pending = false;
        $this->sms_enrollment_challenge = null;
        $this->sms_code = '';
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function disableSms(): void
    {
        $this->confirmPassword();

        app(MfaManager::class)->disableMethod($this->user(), UserMfaMethod::METHOD_SMS);
        $this->sms_verification_pending = false;
        $this->sms_enrollment_challenge = null;
        $this->sms_code = '';
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function setPreferredMethod(string $method): void
    {
        $this->confirmPassword();

        app(MfaManager::class)->setPreferredMethod($this->user(), $method);
        $this->resetSecurityPassword();
        $this->dispatch('security-updated');
    }

    public function revokeTrustedDevice(int $deviceId): void
    {
        $device = $this->user()->trustedDevices()->findOrFail($deviceId);
        app(TrustedDeviceService::class)->revoke($device);
        $this->dispatch('security-updated');
    }

    public function revokeAllTrustedDevices(): void
    {
        app(TrustedDeviceService::class)->revokeAll($this->user());
        app(TrustedDeviceService::class)->forgetCookie();
        $this->dispatch('security-updated');
    }

    public function render()
    {
        $user = $this->user()->load(['mfaMethods', 'trustedDevices' => fn($query) => $query->orderByDesc('last_used_at')]);
        $mfaManager = app(MfaManager::class);
        $totpMethod = $user->mfaMethods->firstWhere('method', UserMfaMethod::METHOD_TOTP);
        $smsMethod = $user->mfaMethods->firstWhere('method', UserMfaMethod::METHOD_SMS);
        $emailMethod = $mfaManager->syncEmailMethod($user);
        $this->refreshMfaPolicyState();

        return view('livewire.settings.security', [
            'emailMethod' => $emailMethod,
            'totpMethod' => $totpMethod,
            'smsMethod' => $smsMethod,
            'smsEnabled' => $mfaManager->smsEnabled(),
            'mfaRequiredForLogin' => $mfaManager->requiresMfa($user),
            'isSuperAdmin' => $this->isSuperAdmin(),
            'trustedDevices' => $user->trustedDevices->whereNull('revoked_at')->filter(fn(TrustedDevice $device) => $device->expires_at?->isFuture()),
            'totpQrSvg' => $this->show_totp_enrollment && $this->totp_secret
                ? app(TotpService::class)->qrSvg(app(TotpService::class)->getOtpAuthUrl($user, $this->totp_secret))
                : null,
        ]);
    }

    protected function confirmPassword(): void
    {
        if (! Hash::check($this->security_password, (string) $this->user()->password)) {
            throw ValidationException::withMessages([
                'security_password' => 'Please enter your current password to continue.',
            ]);
        }
    }

    protected function resetSecurityPassword(): void
    {
        $this->security_password = '';
    }

    protected function refreshMfaPolicyState(): void
    {
        $this->account_mfa_enabled = (bool) $this->user()->fresh()->mfa_enabled;
        $this->system_mfa_required = app(MfaManager::class)->systemMfaRequired();
    }

    protected function authorizeSuperAdmin(): void
    {
        abort_unless($this->isSuperAdmin(), 403);
    }

    protected function isSuperAdmin(): bool
    {
        return $this->user()->hasAnyRole(['super admin', 'superadmin']);
    }

    protected function user()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user;
    }
}
