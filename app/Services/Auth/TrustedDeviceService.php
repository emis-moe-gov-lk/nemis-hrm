<?php

namespace App\Services\Auth;

use App\Models\TrustedDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie as HttpCookie;

class TrustedDeviceService
{
    public function cookieName(): string
    {
        return config('mfa.trusted_devices.cookie_name', 'trusted_device');
    }

    public function validForUser(Request $request, User $user): bool
    {
        [$selector, $verifier] = $this->parseCookie($request->cookie($this->cookieName()));

        if (! $selector || ! $verifier) {
            return false;
        }

        $device = TrustedDevice::query()
            ->where('user_id', $user->id)
            ->where('selector', $selector)
            ->first();

        if (! $device || ! $device->isActive() || ! Hash::check($verifier, $device->token_hash)) {
            return false;
        }

        $device->forceFill([
            'last_used_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 65535, ''),
        ])->save();

        return true;
    }

    public function remember(User $user, Request $request, ?string $deviceName = null): TrustedDevice
    {
        $selector = Str::random(40);
        $verifier = Str::random(64);
        $expiresAt = now()->addDays((int) config('mfa.trusted_devices.lifetime_days', 30));

        $device = $user->trustedDevices()->create([
            'selector' => $selector,
            'token_hash' => Hash::make($verifier),
            'device_name' => $deviceName ?: $this->detectDeviceName((string) $request->userAgent()),
            'user_agent' => Str::limit((string) $request->userAgent(), 65535, ''),
            'ip_address' => $request->ip(),
            'last_used_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        $this->enforceDeviceCap($user);
        Cookie::queue($this->makeCookie($selector, $verifier, $expiresAt));

        return $device;
    }

    public function revoke(TrustedDevice $device): void
    {
        $device->forceFill([
            'revoked_at' => now(),
        ])->save();
    }

    public function revokeAll(User $user): void
    {
        $user->trustedDevices()
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    public function forgetCookie(): void
    {
        Cookie::queue(Cookie::forget($this->cookieName()));
    }

    protected function enforceDeviceCap(User $user): void
    {
        $max = (int) config('mfa.trusted_devices.max_devices', 5);

        $devices = $user->trustedDevices()
            ->whereNull('revoked_at')
            ->orderByDesc('last_used_at')
            ->orderByDesc('id')
            ->get();

        if ($devices->count() <= $max) {
            return;
        }

        $devices->slice($max)->each(fn(TrustedDevice $device) => $this->revoke($device));
    }

    protected function makeCookie(string $selector, string $verifier, \Illuminate\Support\Carbon $expiresAt): HttpCookie
    {
        $minutes = now()->diffInMinutes($expiresAt);

        return cookie(
            $this->cookieName(),
            $selector.'|'.$verifier,
            $minutes,
            '/',
            config('session.domain'),
            (bool) (config('session.secure') ?? request()->isSecure()),
            true,
            false,
            config('session.same_site', 'lax'),
        );
    }

    protected function parseCookie(?string $value): array
    {
        if (! $value || ! str_contains($value, '|')) {
            return [null, null];
        }

        return explode('|', $value, 2);
    }

    protected function detectDeviceName(string $userAgent): string
    {
        $browser = 'Browser';
        $platform = 'Device';

        if (str_contains($userAgent, 'Edg/')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'Chrome/')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox/')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Safari/')) {
            $browser = 'Safari';
        }

        if (str_contains($userAgent, 'Windows')) {
            $platform = 'Windows';
        } elseif (str_contains($userAgent, 'Mac OS')) {
            $platform = 'macOS';
        } elseif (str_contains($userAgent, 'Android')) {
            $platform = 'Android';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            $platform = 'iOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            $platform = 'Linux';
        }

        return sprintf('%s on %s', $browser, $platform);
    }
}
