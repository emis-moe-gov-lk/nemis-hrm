<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Security')" :subheading="__('Manage multi-factor authentication, trusted devices, and recovery options')">
        <div class="space-y-8">
            <div class="rounded-3xl border border-slate-300 bg-white p-5 dark:border-slate-700 dark:bg-slate-900/60">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Security confirmation</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-500">Enter your current password before changing MFA methods or recovery settings.</p>
                <div class="mt-4">
                    <flux:input wire:model="security_password" :label="__('Current password')" type="password" autocomplete="current-password" />
                    @error('security_password')
                    <p class="mt-2 text-xs font-bold uppercase tracking-widest text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if ($isSuperAdmin)
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/50 dark:bg-amber-950/30">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="text-xs font-black uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">Super Admin Policy</div>
                        <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Require MFA for all users</h3>
                        <p class="mt-1 text-sm text-amber-800/80 dark:text-amber-100/80">
                            When enabled, every active user must complete MFA at login. User-level MFA settings cannot override this system policy.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-widest {{ $system_mfa_required ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                            {{ $system_mfa_required ? 'Enabled' : 'Disabled' }}
                        </span>
                        @if ($system_mfa_required)
                        <flux:button wire:click="disableSystemMfaRequirement" variant="filled">Disable for All</flux:button>
                        @else
                        <flux:button wire:click="enableSystemMfaRequirement" variant="primary">Enable for All</flux:button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="rounded-3xl border border-slate-300 bg-white p-5 dark:border-slate-700 dark:bg-slate-900/60">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Account MFA Requirement</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-500">
                            Choose whether this account must use MFA during login. Email OTP is available as the baseline method.
                        </p>
                        @if ($system_mfa_required)
                        <p class="mt-2 text-sm font-semibold text-amber-600 dark:text-amber-300">
                            MFA is currently required by the system administrator, so this account cannot turn it off.
                        </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-widest {{ $mfaRequiredForLogin ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                            {{ $mfaRequiredForLogin ? 'MFA Required' : 'MFA Optional' }}
                        </span>
                        @if ($system_mfa_required)
                        <flux:button variant="filled" disabled>Controlled by Admin</flux:button>
                        @elseif ($account_mfa_enabled)
                        <flux:button wire:click="disableAccountMfa" variant="danger">Disable MFA</flux:button>
                        @else
                        <flux:button wire:click="enableAccountMfa" variant="primary">Enable MFA</flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-300 bg-white p-5 dark:border-slate-700 dark:bg-slate-900/60">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Email OTP</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-500">Available for every active account and used as the baseline fallback factor.</p>
                        <p class="mt-2 text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-500">{{ $emailMethod->destination_snapshot }}</p>
                    </div>
                    @if ($emailMethod->preferred)
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Preferred</span>
                    @else
                    <flux:button wire:click="setPreferredMethod('email')" variant="primary">Set Preferred</flux:button>
                    @endif
                </div>
            </div>

            <div class="rounded-3xl border border-slate-300 bg-white p-5 dark:border-slate-700 dark:bg-slate-900/60">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Authenticator App (TOTP)</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-500">Use Google Authenticator, Microsoft Authenticator, or another compatible app.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($totpMethod?->preferred)
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Preferred</span>
                        @endif

                        @if ($totpMethod?->enabled)
                        @if (! $totpMethod->preferred)
                        <flux:button wire:click="setPreferredMethod('totp')" variant="primary">Set Preferred</flux:button>
                        @endif
                        <flux:button wire:click="regenerateRecoveryCodes" variant="filled">Regenerate Recovery Codes</flux:button>
                        <flux:button wire:click="disableTotp" variant="danger">Disable</flux:button>
                        @else
                        <flux:button wire:click="startTotpEnrollment" variant="primary">Enroll</flux:button>
                        @endif
                    </div>
                </div>

                @if ($show_totp_enrollment && $totpQrSvg)
                <div class="mt-6 grid gap-6 lg:grid-cols-[320px,1fr]">
                    <div class="flex flex-col items-center justify-center rounded-3xl border border-slate-300 bg-white p-6 dark:border-slate-700 dark:bg-white">
                        <div class="rounded-2xl bg-white p-2">
                            {!! $totpQrSvg !!}
                        </div>
                        <p class="mt-4 text-center text-xs font-semibold uppercase tracking-widest text-slate-500">
                            Scan with Google Authenticator
                        </p>
                    </div>
                    <div class="space-y-4">
                        <p class="text-sm text-slate-500 dark:text-slate-500">
                            If scanning does not work, choose manual setup in the authenticator app and enter the secret below.
                        </p>
                        <div class="rounded-2xl border border-slate-300/70 bg-slate-50 px-4 py-3 text-xs font-bold uppercase tracking-widest text-slate-500 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-500">
                            Secret: {{ $totp_secret }}
                        </div>
                        <flux:input wire:model="totp_code" :label="__('Authenticator code')" type="text" autocomplete="one-time-code" />
                        @error('totp_code')
                        <p class="text-xs font-bold uppercase tracking-widest text-red-500">{{ $message }}</p>
                        @enderror
                        <div class="flex gap-3">
                            <flux:button wire:click="confirmTotpEnrollment" variant="primary">Verify & Enable</flux:button>
                            <flux:button wire:click="cancelTotpEnrollment" variant="filled">Cancel</flux:button>
                        </div>
                    </div>
                </div>
                @endif

                @if ($generated_recovery_codes)
                <div class="mt-6 rounded-3xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/50 dark:bg-amber-950/30">
                    <h4 class="text-sm font-black uppercase tracking-[0.2em] text-amber-700 dark:text-amber-300">Recovery Codes</h4>
                    <p class="mt-2 text-sm text-amber-700/90 dark:text-amber-200/90">Store these codes securely. Each code can be used only once.</p>
                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach ($generated_recovery_codes as $code)
                        <div class="rounded-2xl bg-white px-4 py-3 font-mono text-sm font-bold tracking-widest text-slate-900 dark:bg-slate-900 dark:text-white">
                            {{ $code }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="rounded-3xl border border-slate-300 bg-white p-5 dark:border-slate-700 dark:bg-slate-900/60">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">SMS OTP</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-500">
                            {{ $smsEnabled ? 'Send login codes to a verified mobile number.' : 'SMS OTP is currently disabled by the system administrator.' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if (! $smsEnabled)
                        <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-slate-600 dark:bg-slate-800 dark:text-slate-300">Disabled</span>
                        @endif
                        @if ($smsEnabled && $smsMethod?->preferred)
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Preferred</span>
                        @endif
                        @if ($smsEnabled && $smsMethod?->enabled && ! $smsMethod->preferred)
                        <flux:button wire:click="setPreferredMethod('sms')" variant="primary">Set Preferred</flux:button>
                        @endif
                        @if ($smsMethod?->enabled)
                        <flux:button wire:click="disableSms" variant="danger">Disable</flux:button>
                        @endif
                    </div>
                </div>

                @if ($smsEnabled)
                <div class="mt-6 grid gap-4 lg:grid-cols-[1fr,auto]">
                    <flux:input wire:model="sms_phone" :label="__('Mobile number')" type="text" autocomplete="tel" />
                    <div class="flex items-end">
                        <flux:button wire:click="sendSmsEnrollmentCode" variant="primary">
                            {{ $sms_verification_pending ? 'Resend Verification Code' : 'Send Verification Code' }}
                        </flux:button>
                    </div>
                </div>

                @if ($sms_verification_pending)
                <div class="mt-4 space-y-4 rounded-3xl border border-slate-300 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-950/40">
                    <flux:input wire:model="sms_code" :label="__('SMS verification code')" type="text" autocomplete="one-time-code" />
                    @error('sms_code')
                    <p class="text-xs font-bold uppercase tracking-widest text-red-500">{{ $message }}</p>
                    @enderror
                    <div>
                        <flux:button wire:click="verifySmsEnrollment" variant="primary">Verify & Enable SMS OTP</flux:button>
                    </div>
                </div>
                @endif
                @endif
            </div>

            <div class="rounded-3xl border border-slate-300 bg-white p-5 dark:border-slate-700 dark:bg-slate-900/60">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Trusted Devices</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-500">Devices remembered after a successful MFA challenge.</p>
                    </div>
                    <flux:button wire:click="revokeAllTrustedDevices" variant="filled">Revoke All</flux:button>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($trustedDevices as $device)
                    <div class="flex items-start justify-between gap-4 rounded-2xl border border-slate-300/70 px-4 py-4 dark:border-slate-700">
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $device->device_name ?: 'Trusted device' }}</div>
                            <div class="mt-1 text-sm text-slate-500 dark:text-slate-500">
                                Last used: {{ optional($device->last_used_at)->format('Y-m-d H:i') ?: 'Never' }} |
                                Expires: {{ optional($device->expires_at)->format('Y-m-d H:i') }}
                            </div>
                            @if ($device->ip_address)
                            <div class="mt-1 text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-500">{{ $device->ip_address }}</div>
                            @endif
                        </div>
                        <flux:button wire:click="revokeTrustedDevice({{ $device->id }})" variant="danger">Revoke</flux:button>
                    </div>
                    @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-500">
                        No trusted devices recorded.
                    </div>
                    @endforelse
                </div>
            </div>

            <x-action-message class="me-3" on="security-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </x-settings.layout>
</section>