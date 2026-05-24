<?php

use App\Livewire\Auth\Register;
use App\Models\SystemSetting;
use App\Services\Auth\MfaManager;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register without mfa by default', function () {
    $person = \App\Models\People::factory()->create();

    $response = Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('nic', $person->nic)
        ->set('email', $person->email)
        ->set('contact', $person->phone)
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $user = \App\Models\User::where('email', $person->email)->first();

    expect($user)->not->toBeNull();
    expect(session('auth.mfa.pending'))->toBeNull();
    $this->assertAuthenticatedAs($user);
});

test('new users are routed to mfa when superadmin requires it for all users', function () {
    $person = \App\Models\People::factory()->create();
    SystemSetting::setBool(MfaManager::SETTING_REQUIRE_ALL, true);

    $response = Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('nic', $person->nic)
        ->set('email', $person->email)
        ->set('contact', $person->phone)
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('mfa.challenge', absolute: false));

    $user = \App\Models\User::where('email', $person->email)->first();

    expect($user)->not->toBeNull();
    expect(session('auth.mfa.pending.user_id'))->toBe($user->id);
    $this->assertGuest();
});
