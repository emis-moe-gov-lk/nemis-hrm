<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\UserMfaMethod;
use App\Services\Auth\MfaManager;
use App\Services\Auth\TrustedDeviceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

#[Layout('components.layouts.auth')]
class MfaChallenge extends Component
{
    public string $code = '';

    public bool $trust_device = false;

    public bool $using_recovery_code = false;

    public string $current_method = '';

    public array $available_methods = [];

    public ?string $masked_destination = null;

    public ?string $status_message = null;

    public bool $show_method_options = false;

    public function mount(): void
    {
        if (! config('mfa.enabled', true)) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $pending = $this->pending();

        if (! $pending) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $this->current_method = $pending['current_method'];
        $this->available_methods = $pending['available_methods'];
        $this->refreshViewState();
        $this->ensureDeliveryForCurrentMethod();
    }

    public function verify(): void
    {
        $this->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        $pending = $this->requirePending();
        $user = $this->requirePendingUser($pending);
        $mfaManager = app(MfaManager::class);

        if (! $mfaManager->verifyChallenge(
            $user,
            $pending['challenge_id'],
            $this->current_method,
            $this->code,
            $this->using_recovery_code,
        )) {
            throw ValidationException::withMessages([
                'code' => 'The provided verification code is invalid or expired.',
            ]);
        }

        Auth::login($user, (bool) $pending['remember']);

        if ($this->trust_device) {
            app(TrustedDeviceService::class)->remember($user, request());
        }

        app(MfaManager::class)->invalidateChallenge(
            $pending['challenge_id'],
            $pending['available_methods'] ?? [],
        );

        Session::forget('auth.mfa.pending');
        Session::regenerate();
        Session::regenerateToken();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function switchMethod(string $method): void
    {
        $pending = $this->requirePending();

        if (! in_array($method, $pending['available_methods'], true)) {
            throw ValidationException::withMessages([
                'code' => 'The selected verification method is not available.',
            ]);
        }

        $pending['current_method'] = $method;
        Session::put('auth.mfa.pending', $pending);

        $this->current_method = $method;
        $this->code = '';
        $this->using_recovery_code = false;
        $this->status_message = null;
        $this->show_method_options = false;

        $this->refreshViewState();
        $this->ensureDeliveryForCurrentMethod(force: true);
    }

    public function resend(): void
    {
        if ($this->current_method === UserMfaMethod::METHOD_TOTP) {
            return;
        }

        $this->ensureDeliveryForCurrentMethod(force: true);
    }

    public function toggleRecoveryCode(): void
    {
        $this->using_recovery_code = ! $this->using_recovery_code;
        $this->code = '';
    }

    public function toggleMethodOptions(): void
    {
        $this->show_method_options = ! $this->show_method_options;
    }

    public function render()
    {
        return view('livewire.auth.mfa-challenge');
    }

    protected function ensureDeliveryForCurrentMethod(bool $force = false): void
    {
        if ($this->current_method === UserMfaMethod::METHOD_TOTP) {
            return;
        }

        $pending = $this->requirePending();
        $sentMethods = $pending['sent_methods'] ?? [];

        if (! $force && in_array($this->current_method, $sentMethods, true)) {
            return;
        }

        $user = $this->requirePendingUser($pending);

        try {
            app(MfaManager::class)->sendChallenge($user, $pending['challenge_id'], $this->current_method);
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages([
                'code' => $exception->getMessage(),
            ]);
        }

        if (! in_array($this->current_method, $sentMethods, true)) {
            $sentMethods[] = $this->current_method;
        }

        $pending['sent_methods'] = $sentMethods;
        Session::put('auth.mfa.pending', $pending);

        $this->status_message = 'A verification code has been sent.';
    }

    protected function refreshViewState(): void
    {
        $pending = $this->requirePending();
        $user = $this->requirePendingUser($pending);
        $method = app(MfaManager::class)->methodForUser($user, $this->current_method);

        $this->masked_destination = $method
            ? app(MfaManager::class)->maskDestination($method, $user)
            : null;
    }

    protected function pending(): ?array
    {
        return Session::get('auth.mfa.pending');
    }

    protected function requirePending(): array
    {
        $pending = $this->pending();

        if (! $pending || $this->pendingExpired($pending)) {
            Session::forget('auth.mfa.pending');
            abort(403);
        }

        return $pending;
    }

    protected function requirePendingUser(array $pending): User
    {
        $user = User::find($pending['user_id'] ?? null);

        if (! $user || ! $user->active_status) {
            Session::forget('auth.mfa.pending');
            abort(403);
        }

        return $user;
    }

    protected function pendingExpired(array $pending): bool
    {
        $initiatedAt = data_get($pending, 'initiated_at');

        if (! $initiatedAt) {
            return true;
        }

        return Carbon::parse($initiatedAt)->addMinutes((int) config('mfa.otp.ttl_minutes', 10))->isPast();
    }
}
