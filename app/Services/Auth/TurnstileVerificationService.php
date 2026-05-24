<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TurnstileVerificationService
{
    private const LOCAL_DUMMY_SITE_KEY = '1x00000000000000000000AA';

    private const LOCAL_DUMMY_SECRET_KEY = '1x0000000000000000000000000000000AA';

    public function enabled(): bool
    {
        return (bool) config('services.turnstile.enabled', false);
    }

    public function verify(?string $token, ?string $ipAddress = null): bool
    {
        if (! $this->enabled() || app()->environment('testing')) {
            return true;
        }

        if (blank($token)) {
            return false;
        }

        if ($this->usingLocalDummyKeys()) {
            return true;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('services.turnstile.secret'),
                    'response' => $token,
                    'remoteip' => $ipAddress,
                ]);
        } catch (Throwable $exception) {
            Log::warning('turnstile.verify.request_failed', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }

        if (! $response->successful()) {
            Log::warning('turnstile.verify.http_failed', [
                'status' => $response->status(),
            ]);

            return false;
        }

        return (bool) data_get($response->json(), 'success', false);
    }

    private function usingLocalDummyKeys(): bool
    {
        return app()->environment('local')
            && config('services.turnstile.site_key') === self::LOCAL_DUMMY_SITE_KEY
            && config('services.turnstile.secret') === self::LOCAL_DUMMY_SECRET_KEY;
    }
}
