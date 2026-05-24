<?php

namespace App\Livewire\Auth;

use App\Services\Auth\MfaManager;
use App\Services\Auth\TurnstileVerificationService;
use App\Models\User;
use App\Models\People;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rules;
use App\Rules\UniqueHashedNicUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Events\Registered;
use App\Helpers\NicHelper;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $nic = '';
    public string $email = '';
    public string $contact = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $turnstileToken = '';

    public function register(): void
    {
        $this->nic = strtoupper($this->nic);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'nic' => ['required', 'string', 'regex:/^(\d{9}[vVxX]|\d{12})$/', new UniqueHashedNicUser()],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'contact' => ['required', 'regex:/^[0-9]{10}$/', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        if (! app(TurnstileVerificationService::class)->verify($this->turnstileToken, request()->ip())) {
            throw ValidationException::withMessages([
                'turnstile' => 'Captcha verification failed. Please try again.',
            ]);
        }

        // Check if NIC exists in people table
        $person = People::where('nic_hash', NicHelper::hash($validated['nic']))->first();

        if (! $person) {
            $this->addError('nic', 'This NIC is not registered in the system.');
            return;
        }

        if ($person->email !== $validated['email']) {
            $this->addError('email', 'This email is not registered in the system.');
            return;
        }

        if ($person->phone !== $validated['contact']) {
            $this->addError('contact', 'This contact number is not registered in the system.');
            return;
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Clean NIC
        //$nic = strtoupper(trim($validated['nic']));

        $nic = NicHelper::normalize($validated['nic']);

        // Create user
        $user = User::create([
            'name'            => $validated['name'],
            'nic'             => $nic,
            'nic_hash'        => NicHelper::hash($nic),
            'email'           => $validated['email'],
            'contact'         => $validated['contact'],
            'password'        => $validated['password'],
            'profile_picture' => 'default.png',
            'remember_token'  => Str::random(10),
        ]);

        event(new Registered($user));

        $mfaManager = app(MfaManager::class);

        if (! $mfaManager->requiresMfa($user)) {
            Auth::login($user);
            Session::regenerate();

            $this->redirect(route('dashboard', absolute: false), navigate: true);

            return;
        }

        $availableMethods = $mfaManager->enabledMethods($user)->pluck('method')->values()->all();
        $preferredMethod = $mfaManager->preferredMethod($user);

        Session::put('auth.mfa.pending', [
            'challenge_id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'remember' => false,
            'available_methods' => $availableMethods,
            'current_method' => $preferredMethod->method,
            'sent_methods' => [],
            'initiated_at' => now()->toIso8601String(),
        ]);

        $this->redirectRoute('mfa.challenge', navigate: true);
    }
}
