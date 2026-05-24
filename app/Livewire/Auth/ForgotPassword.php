<?php

namespace App\Livewire\Auth;

use App\Services\Auth\TurnstileVerificationService;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    public string $email = '';

    public string $turnstileToken = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        if (! app(TurnstileVerificationService::class)->verify($this->turnstileToken, request()->ip())) {
            throw ValidationException::withMessages([
                'turnstile' => 'Captcha verification failed. Please try again.',
            ]);
        }

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('If an account exists for this email address, a password reset link has been sent. Please check your email inbox (and spam folder) to continue.'));
    }
}
