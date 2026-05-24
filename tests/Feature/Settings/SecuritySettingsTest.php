<?php

use App\Models\User;
use App\Models\UserMfaMethod;
use App\Services\Auth\MfaManager;
use App\Services\Auth\OtpChallengeService;
use App\Services\Auth\TotpService;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

use App\Livewire\Settings\Security;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function totpCodeForSettings(string $secret): string
{
    $service = app(TotpService::class);
    $reflection = new ReflectionClass($service);
    $generator = $reflection->getMethod('generateCode');
    $generator->setAccessible(true);
    $counter = (int) floor(time() / (int) config('mfa.totp.period', 30));

    return $generator->invoke($service, $secret, $counter);
}

test('security settings page can be rendered for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/settings/security');

    $response->assertOk();
    $response->assertSee('Security');
    $response->assertSee('Trusted Devices');
});

test('authenticated users can enable and disable account mfa', function () {
    $user = User::factory()->create(['mfa_enabled' => false]);

    Livewire::actingAs($user)
        ->test(Security::class)
        ->set('security_password', 'password')
        ->call('enableAccountMfa')
        ->assertHasNoErrors();

    expect($user->fresh()->mfa_enabled)->toBeTrue();

    Livewire::actingAs($user->fresh())
        ->test(Security::class)
        ->set('security_password', 'password')
        ->call('disableAccountMfa')
        ->assertHasNoErrors();

    expect($user->fresh()->mfa_enabled)->toBeFalse();
});

test('superadmin can require mfa for all users', function () {
    $superAdmin = User::factory()->create(['mfa_enabled' => false]);
    Role::findOrCreate('super admin');
    $superAdmin->assignRole('super admin');

    Livewire::actingAs($superAdmin)
        ->test(Security::class)
        ->set('security_password', 'password')
        ->call('enableSystemMfaRequirement')
        ->assertHasNoErrors();

    expect(app(MfaManager::class)->systemMfaRequired())->toBeTrue();
    expect(app(MfaManager::class)->requiresMfa($superAdmin->fresh()))->toBeTrue();

    Livewire::actingAs($superAdmin->fresh())
        ->test(Security::class)
        ->set('security_password', 'password')
        ->call('disableSystemMfaRequirement')
        ->assertHasNoErrors();

    expect(app(MfaManager::class)->systemMfaRequired())->toBeFalse();
});

test('users cannot disable account mfa while superadmin policy is enforced', function () {
    $user = User::factory()->create(['mfa_enabled' => true]);
    app(MfaManager::class)->setSystemMfaRequired(true);

    Livewire::actingAs($user)
        ->test(Security::class)
        ->set('security_password', 'password')
        ->call('disableAccountMfa')
        ->assertHasErrors('account_mfa_enabled');

    expect($user->fresh()->mfa_enabled)->toBeTrue();
});

test('authenticated users can enroll totp from security settings', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(Security::class)
        ->set('security_password', 'password')
        ->call('startTotpEnrollment')
        ->assertSet('show_totp_enrollment', true);

    $secret = $component->get('totp_secret');

    $component
        ->set('totp_code', totpCodeForSettings($secret))
        ->call('confirmTotpEnrollment')
        ->assertHasNoErrors()
        ->assertSet('show_totp_enrollment', false);

    expect($user->fresh()->mfaMethods()->where('method', UserMfaMethod::METHOD_TOTP)->value('enabled'))->toBeTrue();
    expect($user->fresh()->mfa_enabled)->toBeTrue();
    expect($user->fresh()->recoveryCodes()->count())->toBe((int) config('mfa.totp.recovery_codes', 8));
});

test('authenticated users can enroll sms from security settings', function () {
    config(['services.mobitel.enabled' => true]);

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(Security::class)
        ->set('security_password', 'password')
        ->set('sms_phone', '0771234567')
        ->call('sendSmsEnrollmentCode')
        ->assertHasNoErrors()
        ->assertSet('sms_verification_pending', true);

    $challengeId = $component->get('sms_enrollment_challenge');
    $code = app(OtpChallengeService::class)->peekTestingCode($challengeId, UserMfaMethod::METHOD_SMS);

    expect($code)->not->toBeNull();

    $component
        ->set('sms_code', $code)
        ->call('verifySmsEnrollment')
        ->assertHasNoErrors()
        ->assertSet('sms_verification_pending', false);

    expect($user->fresh()->mfaMethods()->where('method', UserMfaMethod::METHOD_SMS)->value('enabled'))->toBeTrue();
    expect($user->fresh()->mfa_enabled)->toBeTrue();
});

test('sms enrollment is unavailable when sms is disabled', function () {
    config(['services.mobitel.enabled' => false]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Security::class)
        ->set('security_password', 'password')
        ->set('sms_phone', '0771234567')
        ->call('sendSmsEnrollmentCode')
        ->assertHasErrors('sms_phone');

    expect(app(MfaManager::class)->enabledMethods($user->fresh())->pluck('method')->all())
        ->not->toContain(UserMfaMethod::METHOD_SMS);
});

test('authenticated users can revoke all trusted devices from security settings', function () {
    $user = User::factory()->create();

    $user->trustedDevices()->create([
        'selector' => 'selector-one',
        'token_hash' => bcrypt('verifier-one'),
        'device_name' => 'Chrome on Windows',
        'user_agent' => 'PHPUnit',
        'ip_address' => '127.0.0.1',
        'last_used_at' => now(),
        'expires_at' => now()->addDays(30),
    ]);

    Livewire::actingAs($user)
        ->test(Security::class)
        ->call('revokeAllTrustedDevices')
        ->assertHasNoErrors();

    expect($user->fresh()->trustedDevices()->whereNull('revoked_at')->count())->toBe(0);
});
