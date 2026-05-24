<?php

namespace App\Livewire\Settings;

use App\Services\Auth\MfaManager;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';
    public string $contact = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->contact = $user->contact;
        $this->email = $user->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => [
                'required',
                'string',
                'max:10',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->fill($validated);
        $emailChanged = $user->isDirty('email');
        $contactChanged = $user->isDirty('contact');

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        $mfaManager = app(MfaManager::class);

        if ($emailChanged) {
            $mfaManager->ensureCurrentStateAfterEmailChange($user);
        }

        if ($contactChanged) {
            $mfaManager->ensureCurrentStateAfterContactChange($user);
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}
