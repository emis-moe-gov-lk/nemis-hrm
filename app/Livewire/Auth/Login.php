<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\UserMfaMethod;
use App\Services\Auth\MfaManager;
use App\Services\Auth\TrustedDeviceService;
use App\Services\Auth\TurnstileVerificationService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public string $turnstileToken = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();
        $this->ensureTurnstileIsValid();

        $user = User::query()
            ->where('email', $this->email)
            ->where('active_status', 1)
            ->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            $this->hitRateLimiters();

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->clearRateLimiters();

        $mfaManager = app(MfaManager::class);

        if (! $mfaManager->requiresMfa($user)) {
            Auth::login($user, $this->remember);
            Session::regenerate();
            Session::regenerateToken();

            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        if (app(TrustedDeviceService::class)->validForUser(request(), $user)) {
            Auth::login($user, $this->remember);
            Session::regenerate();
            Session::regenerateToken();

            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        $availableMethods = $mfaManager->enabledMethods($user)->pluck('method')->values()->all();
        $preferredMethod = $mfaManager->preferredMethod($user);

        Session::put('auth.mfa.pending', [
            'challenge_id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'remember' => $this->remember,
            'available_methods' => $availableMethods,
            'current_method' => $preferredMethod->method ?: UserMfaMethod::METHOD_EMAIL,
            'sent_methods' => [],
            'initiated_at' => now()->toIso8601String(),
        ]);

        $this->redirectRoute('mfa.challenge', navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        $emailKey = $this->throttleKey();
        $ipKey = $this->ipThrottleKey();
        $emailLimit = (int) config('mfa.rate_limits.login_email_max_attempts', 5);
        $ipLimit = (int) config('mfa.rate_limits.login_ip_max_attempts', 20);

        if (! RateLimiter::tooManyAttempts($emailKey, $emailLimit) && ! RateLimiter::tooManyAttempts($ipKey, $ipLimit)) {
            return;
        }

        event(new Lockout(app(Request::class)));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.app(Request::class)->ip());
    }

    protected function ipThrottleKey(): string
    {
        return 'login-ip|'.app(Request::class)->ip();
    }

    protected function hitRateLimiters(): void
    {
        $decay = (int) config('mfa.rate_limits.decay_seconds', 60);

        RateLimiter::hit($this->throttleKey(), $decay);
        RateLimiter::hit($this->ipThrottleKey(), $decay);
    }

    protected function clearRateLimiters(): void
    {
        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->ipThrottleKey());
    }

    protected function ensureTurnstileIsValid(): void
    {
        if (! app(TurnstileVerificationService::class)->verify($this->turnstileToken, app(Request::class)->ip())) {
            $this->hitRateLimiters();

            throw ValidationException::withMessages([
                'turnstile' => 'Captcha verification failed. Please try again.',
            ]);
        }
    }
}
