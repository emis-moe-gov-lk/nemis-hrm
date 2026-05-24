<?php

namespace App\Services\Auth;

use App\Services\MobitelSmsService;
use Illuminate\Support\Facades\Log;

class SmsGatewayService
{
    public function __construct(
        protected MobitelSmsService $mobitelSmsService
    ) {
    }

    public function enabled(): bool
    {
        return (bool) config('services.mobitel.enabled', false);
    }

    public function sendOtp(string $mobile, string $code): bool
    {
        $message = sprintf(
            '%s login verification code: %s. This code expires in %d minutes.',
            config('app.name', 'EMIS'),
            $code,
            (int) config('mfa.otp.ttl_minutes', 10),
        );

        if (! $this->enabled() || app()->environment('testing')) {
            Log::info('mfa.sms.delivery_skipped', [
                'mobile' => $mobile,
                'reason' => 'sms_disabled_or_testing',
            ]);

            return true;
        }

        $result = $this->mobitelSmsService->send($mobile, $message);

        Log::info('mfa.sms.delivered', [
            'mobile' => $mobile,
            'success' => $result['success'] ?? false,
            'status' => $result['status'] ?? null,
        ]);

        return (bool) ($result['success'] ?? false);
    }
}
