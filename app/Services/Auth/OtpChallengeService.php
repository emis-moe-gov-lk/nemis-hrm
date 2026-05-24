<?php

namespace App\Services\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class OtpChallengeService
{
    public function issueCode(string $challengeId, string $method): string
    {
        $payload = $this->payload($challengeId, $method) ?? [
            'send_count' => 0,
            'attempts' => 0,
        ];

        $cooldown = (int) config('mfa.otp.resend_cooldown_seconds', 60);
        $maxSends = (int) config('mfa.otp.max_sends', 5);

        if (($payload['send_count'] ?? 0) >= $maxSends) {
            throw new RuntimeException('Too many OTP sends requested. Please start again.');
        }

        if (! empty($payload['last_sent_at'])) {
            $lastSentAt = Carbon::parse($payload['last_sent_at']);

            if ($lastSentAt->addSeconds($cooldown)->isFuture()) {
                throw new RuntimeException('Please wait before requesting another code.');
            }
        }

        $code = $this->generateCode();
        $expiresAt = now()->addMinutes((int) config('mfa.otp.ttl_minutes', 10));

        $nextPayload = [
            'code_hash' => Hash::make($code),
            'expires_at' => $expiresAt->toIso8601String(),
            'attempts' => 0,
            'send_count' => ($payload['send_count'] ?? 0) + 1,
            'last_sent_at' => now()->toIso8601String(),
        ];

        if (app()->environment('testing')) {
            $nextPayload['testing_code'] = $code;
        }

        Cache::put($this->cacheKey($challengeId, $method), $nextPayload, $expiresAt);

        return $code;
    }

    public function verify(string $challengeId, string $method, string $code): bool
    {
        $payload = $this->payload($challengeId, $method);

        if (! $payload) {
            return false;
        }

        if (Carbon::parse($payload['expires_at'])->isPast()) {
            Cache::forget($this->cacheKey($challengeId, $method));

            return false;
        }

        $attempts = (int) ($payload['attempts'] ?? 0);
        $maxAttempts = (int) config('mfa.otp.max_attempts', 5);

        if ($attempts >= $maxAttempts) {
            Cache::forget($this->cacheKey($challengeId, $method));

            return false;
        }

        $normalized = preg_replace('/\D+/', '', $code);

        if (Hash::check($normalized, $payload['code_hash'])) {
            Cache::forget($this->cacheKey($challengeId, $method));

            return true;
        }

        $payload['attempts'] = $attempts + 1;
        Cache::put($this->cacheKey($challengeId, $method), $payload, Carbon::parse($payload['expires_at']));

        return false;
    }

    public function peekTestingCode(string $challengeId, string $method): ?string
    {
        return data_get($this->payload($challengeId, $method), 'testing_code');
    }

    public function secondsUntilResend(string $challengeId, string $method): int
    {
        $payload = $this->payload($challengeId, $method);

        if (! $payload || empty($payload['last_sent_at'])) {
            return 0;
        }

        $lastSentAt = Carbon::parse($payload['last_sent_at']);
        $availableAt = $lastSentAt->addSeconds((int) config('mfa.otp.resend_cooldown_seconds', 60));

        return max(0, now()->diffInSeconds($availableAt, false));
    }

    public function invalidateAll(string $challengeId, array $methods): void
    {
        foreach ($methods as $method) {
            Cache::forget($this->cacheKey($challengeId, $method));
        }
    }

    protected function payload(string $challengeId, string $method): ?array
    {
        return Cache::get($this->cacheKey($challengeId, $method));
    }

    protected function cacheKey(string $challengeId, string $method): string
    {
        return sprintf('mfa:otp:%s:%s', $challengeId, $method);
    }

    protected function generateCode(): string
    {
        $length = (int) config('mfa.otp.length', 6);
        $min = 10 ** ($length - 1);
        $max = (10 ** $length) - 1;

        return (string) random_int($min, $max);
    }
}
