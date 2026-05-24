<?php

use App\Livewire\Auth\Login;
use App\Models\SystemSetting;
use App\Models\TrustedDevice;
use App\Models\User;
use App\Services\Auth\MfaManager;
use App\Services\Auth\TurnstileVerificationService;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can login without mfa by default', function () {
    $user = User::factory()->create();

    $response = Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->set('turnstileToken', 'testing-token')
        ->call('login');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    expect(session('auth.mfa.pending'))->toBeNull();
});

test('users are redirected to mfa challenge when account mfa is enabled', function () {
    $user = User::factory()->create();
    app(MfaManager::class)->enableForUser($user);

    $response = Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->set('turnstileToken', 'testing-token')
        ->call('login');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('mfa.challenge', absolute: false));

    $this->assertGuest();
    expect(session('auth.mfa.pending.user_id'))->toBe($user->id);
});

test('superadmin enforced mfa redirects users to mfa challenge', function () {
    $user = User::factory()->create(['mfa_enabled' => false]);
    SystemSetting::setBool(MfaManager::SETTING_REQUIRE_ALL, true);

    $response = Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->set('turnstileToken', 'testing-token')
        ->call('login');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('mfa.challenge', absolute: false));

    $this->assertGuest();
    expect(session('auth.mfa.pending.user_id'))->toBe($user->id);
});

test('trusted devices bypass the mfa challenge after valid password login', function () {
    $user = User::factory()->create();
    app(MfaManager::class)->enableForUser($user);
    $selector = 'selector-token';
    $verifier = 'verifier-token';

    $user->trustedDevices()->create([
        'selector' => $selector,
        'token_hash' => Hash::make($verifier),
        'device_name' => 'Chrome on Windows',
        'user_agent' => 'PHPUnit',
        'ip_address' => '127.0.0.1',
        'last_used_at' => now()->subMinute(),
        'expires_at' => now()->addDays(30),
    ]);

    $response = Livewire::withCookies([config('mfa.trusted_devices.cookie_name') => $selector.'|'.$verifier])
        ->test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->set('turnstileToken', 'testing-token')
        ->call('login');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    expect(session('auth.mfa.pending'))->toBeNull();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->set('turnstileToken', 'testing-token')
        ->call('login');

    $response->assertHasErrors('email');

    $this->assertGuest();
});

test('login is rejected when turnstile verification fails', function () {
    $user = User::factory()->create();

    $this->app->instance(TurnstileVerificationService::class, new class extends TurnstileVerificationService {
        public function verify(?string $token, ?string $ipAddress = null): bool
        {
            return false;
        }
    });

    $response = Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->set('turnstileToken', 'bad-token')
        ->call('login');

    $response->assertHasErrors('turnstile');

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/login');

    $this->assertGuest();
});
