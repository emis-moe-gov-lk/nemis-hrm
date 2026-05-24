<?php

use App\Livewire\Auth\MfaChallenge;
use App\Models\User;
use App\Models\UserMfaMethod;
use App\Services\Auth\MfaManager;
use App\Services\Auth\OtpChallengeService;
use App\Services\Auth\TotpService;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function generateCurrentTotpCode(string $secret): string
{
    $service = app(TotpService::class);
    $reflection = new ReflectionClass($service);
    $generator = $reflection->getMethod('generateCode');
    $generator->setAccessible(true);
    $counter = (int) floor(time() / (int) config('mfa.totp.period', 30));

    return $generator->invoke($service, $secret, $counter);
}

test('mfa challenge screen can be rendered when a pending challenge exists', function () {
    $user = User::factory()->create();
    $challengeId = (string) \Illuminate\Support\Str::uuid();

    app(MfaManager::class)->syncEmailMethod($user);

    session([
        'auth.mfa.pending' => [
            'challenge_id' => $challengeId,
            'user_id' => $user->id,
            'remember' => false,
            'available_methods' => [UserMfaMethod::METHOD_EMAIL],
            'current_method' => UserMfaMethod::METHOD_EMAIL,
            'sent_methods' => [],
            'initiated_at' => now()->toIso8601String(),
        ],
    ]);

    $response = $this->get('/mfa/challenge');

    $response->assertOk();
    $response->assertSee('Multi-factor verification');
});

test('users can complete login with an email otp challenge', function () {
    $user = User::factory()->create();
    $challengeId = (string) \Illuminate\Support\Str::uuid();

    app(MfaManager::class)->syncEmailMethod($user);

    session([
        'auth.mfa.pending' => [
            'challenge_id' => $challengeId,
            'user_id' => $user->id,
            'remember' => false,
            'available_methods' => [UserMfaMethod::METHOD_EMAIL],
            'current_method' => UserMfaMethod::METHOD_EMAIL,
            'sent_methods' => [],
            'initiated_at' => now()->toIso8601String(),
        ],
    ]);

    $component = Livewire::test(MfaChallenge::class);
    $code = app(OtpChallengeService::class)->peekTestingCode($challengeId, UserMfaMethod::METHOD_EMAIL);

    expect($code)->not->toBeNull();

    $component
        ->set('code', $code)
        ->call('verify')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    expect(session('auth.mfa.pending'))->toBeNull();
});

test('trusting a device during mfa stores a trusted device record', function () {
    $user = User::factory()->create();
    $challengeId = (string) \Illuminate\Support\Str::uuid();

    app(MfaManager::class)->syncEmailMethod($user);

    session([
        'auth.mfa.pending' => [
            'challenge_id' => $challengeId,
            'user_id' => $user->id,
            'remember' => true,
            'available_methods' => [UserMfaMethod::METHOD_EMAIL],
            'current_method' => UserMfaMethod::METHOD_EMAIL,
            'sent_methods' => [],
            'initiated_at' => now()->toIso8601String(),
        ],
    ]);

    $component = Livewire::test(MfaChallenge::class);
    $code = app(OtpChallengeService::class)->peekTestingCode($challengeId, UserMfaMethod::METHOD_EMAIL);

    $component
        ->set('code', $code)
        ->set('trust_device', true)
        ->call('verify')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseCount('trusted_devices', 1);
});

test('users can complete login with a totp challenge', function () {
    $user = User::factory()->create();
    $secret = app(TotpService::class)->generateSecret();
    app(MfaManager::class)->enableTotp($user, $secret);
    app(MfaManager::class)->setPreferredMethod($user, UserMfaMethod::METHOD_TOTP);

    session([
        'auth.mfa.pending' => [
            'challenge_id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'remember' => false,
            'available_methods' => [UserMfaMethod::METHOD_TOTP, UserMfaMethod::METHOD_EMAIL],
            'current_method' => UserMfaMethod::METHOD_TOTP,
            'sent_methods' => [],
            'initiated_at' => now()->toIso8601String(),
        ],
    ]);

    Livewire::test(MfaChallenge::class)
        ->set('code', generateCurrentTotpCode($secret))
        ->call('verify')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('users can complete login with a totp recovery code', function () {
    $user = User::factory()->create();
    $secret = app(TotpService::class)->generateSecret();
    $recoveryCodes = app(MfaManager::class)->enableTotp($user, $secret);
    app(MfaManager::class)->setPreferredMethod($user, UserMfaMethod::METHOD_TOTP);

    session([
        'auth.mfa.pending' => [
            'challenge_id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'remember' => false,
            'available_methods' => [UserMfaMethod::METHOD_TOTP, UserMfaMethod::METHOD_EMAIL],
            'current_method' => UserMfaMethod::METHOD_TOTP,
            'sent_methods' => [],
            'initiated_at' => now()->toIso8601String(),
        ],
    ]);

    Livewire::test(MfaChallenge::class)
        ->set('using_recovery_code', true)
        ->set('code', $recoveryCodes[0])
        ->call('verify')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->recoveryCodes()->whereNotNull('used_at')->count())->toBe(1);
});
