<div class="min-h-screen flex flex-col items-center justify-center relative bg-gray-950 text-white font-sans px-4 py-10 lg:py-12 overflow-x-hidden">

    <div class="absolute inset-0 z-0 overflow-hidden">
        <div aria-hidden="true" class="absolute inset-0 z-0 bg-linear-to-br from-gray-900 via-indigo-950 to-blue-950 opacity-95"></div>
        <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] bg-linear-to-br from-blue-600/10 to-transparent rounded-full blur-[140px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] bg-linear-to-tl from-indigo-600/10 to-transparent rounded-full blur-[120px]"></div>

        <div class="absolute top-[15%] right-[10%] w-48 h-48 bg-white/5 backdrop-blur-md rounded-4xl border border-white/10 shadow-sm rotate-12 animate-bounce [animation-duration:6s] hidden lg:block"></div>
        <div class="absolute bottom-[20%] left-[5%] w-64 h-32 bg-blue-500/5 backdrop-blur-md rounded-4xl border border-white/10 shadow-sm -rotate-6 animate-pulse hidden lg:block"></div>
    </div>

    <div class="relative z-10 w-full max-w-[900px] flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

        <div class="lg:w-1/2 text-center lg:text-left space-y-6">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center lg:items-start gap-4" wire:navigate>
                <div class="w-20 h-20 flex items-center justify-center bg-white/5 backdrop-blur-xl shadow-2xl rounded-2xl border border-white/10 transition-transform hover:scale-105">
                    <x-app-logo-icon class="w-12 h-auto fill-current text-white" />
                </div>
                <div class="space-y-1">
                    <h2 class="text-4xl font-black text-white tracking-tighter">
                        EMIS <span class="text-blue-400">{{ ! empty($systemVersion ?? null) ? 'V' . ltrim($systemVersion, 'vV') : 'V1.3a' }}</span>
                    </h2>
                    <p class="text-[10px] font-bold text-blue-300 uppercase tracking-[0.4em]">Education Management Information System</p>
                </div>
            </a>
            <p class="hidden lg:block text-indigo-100/60 text-sm leading-relaxed max-w-xs font-medium">
                A <span class="text-white font-bold">unified data architecture</span> built for transparency and security. One more checkpoint keeps your EMIS workspace protected.
            </p>
        </div>

        <div class="lg:w-1/2 w-full max-w-[420px] relative">
            <div class="absolute -top-6 -right-6 w-24 h-24 border-8 border-blue-500/10 rounded-full"></div>

            <div class="bg-white/5 backdrop-blur-[35px] rounded-[3.5rem] p-8 sm:p-12 shadow-2xl border border-white/10 relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-blue-400/30 to-transparent"></div>

                <div class="mb-7">
                    <p class="mb-3 text-[10px] font-black uppercase tracking-[0.3em] text-blue-300/70">Secure Checkpoint</p>
                    <h3 class="text-xl font-bold text-white tracking-tight">Multi-factor verification</h3>
                    <p class="text-xs text-indigo-200/50 font-medium">Use your selected authentication method to continue.</p>
                </div>

                @if ($status_message)
                <div class="mb-5 rounded-3xl border border-blue-400/20 bg-blue-500/10 px-5 py-4 text-xs font-bold text-blue-200">
                    {{ $status_message }}
                </div>
                @endif

                <div class="mb-6 rounded-3xl border border-white/10 bg-white/5 px-5 py-4 shadow-sm">
                    <div class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-200/40">Current Method</div>
                    <div class="mt-2 text-lg font-black text-white">
                        {{ strtoupper($current_method) === 'TOTP' ? 'Authenticator App' : strtoupper($current_method).' OTP' }}
                    </div>
                    @if ($masked_destination)
                    <div class="mt-1 text-xs font-medium text-indigo-100/50">
                        Code destination: {{ $masked_destination }}
                    </div>
                    @endif
                </div>

                <form wire:submit="verify" class="space-y-5">
                    <div class="group">
                        <label class="mb-2 ml-1 block text-xs font-black uppercase tracking-widest text-indigo-100/80">
                            {{ $using_recovery_code ? 'Recovery Code' : 'Verification Code' }}
                        </label>
                        <input
                            type="text"
                            wire:model="code"
                            autocomplete="one-time-code"
                            class="
                                w-full px-6 py-4
                                bg-white/5
                                border {{ $errors->has('code') ? 'border-red-500/50' : 'border-white/10' }}
                                rounded-3xl
                                text-sm font-medium
                                text-white
                                placeholder:text-indigo-200/30
                                transition-all
                                group-focus-within:bg-white/10
                                group-focus-within:border-blue-500/50
                                group-focus-within:ring-4 group-focus-within:ring-blue-500/5
                                caret-blue-400
                                outline-none
                                shadow-sm
                            "
                            placeholder="{{ $using_recovery_code ? 'Enter a recovery code' : 'Enter your 6-digit code' }}">
                        @error('code')
                        <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <label class="flex items-start gap-3 rounded-3xl border border-white/10 bg-white/5 px-5 py-4 text-sm text-indigo-100/70 transition hover:bg-white/10">
                        <input type="checkbox" wire:model="trust_device" class="mt-1 rounded border-white/20 bg-white/5 text-blue-500 focus:ring-blue-500">
                        <span>
                            <span class="block font-bold text-white">Trust this device for 30 days</span>
                            <span class="mt-1 block text-xs text-indigo-200/45">Password and Turnstile will still be required on future sign-ins.</span>
                        </span>
                    </label>

                    <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white rounded-[1.8rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 transition-all hover:-translate-y-1 active:scale-95">
                        Verify and Continue
                    </button>
                </form>

                <div class="mt-6 flex flex-wrap items-center gap-4 text-sm">
                    @if ($current_method === \App\Models\UserMfaMethod::METHOD_TOTP)
                    <button type="button" wire:click="toggleRecoveryCode" class="text-[11px] font-black uppercase tracking-widest text-blue-300 transition hover:text-blue-200">
                        {{ $using_recovery_code ? 'Use authenticator code instead' : 'Use a recovery code' }}
                    </button>
                    @else
                    <button type="button" wire:click="resend" class="text-[11px] font-black uppercase tracking-widest text-blue-300 transition hover:text-blue-200">
                        Resend code
                    </button>
                    @endif

                    <button type="button" wire:click="toggleMethodOptions" class="text-[11px] font-black uppercase tracking-widest text-blue-300 transition hover:text-blue-200">
                        Try another method
                    </button>
                </div>

                @if ($show_method_options)
                <div class="mt-7 rounded-3xl border border-white/10 bg-white/5 px-5 py-5 shadow-sm">
                    <div class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-200/40">Other Methods</div>

                    @if (count($available_methods) > 1)
                    <p class="mt-2 text-xs font-medium leading-relaxed text-indigo-100/50">
                        Choose one of your enabled backup methods. Email OTP is always available as the baseline method.
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($available_methods as $method)
                        @if ($method !== $current_method)
                        <button
                            type="button"
                            wire:click="switchMethod('{{ $method }}')"
                            class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-indigo-100/80 transition hover:border-blue-400/40 hover:bg-blue-500/10 hover:text-blue-200">
                            {{ $method === \App\Models\UserMfaMethod::METHOD_TOTP ? 'Authenticator App' : strtoupper($method).' OTP' }}
                        </button>
                        @endif
                        @endforeach
                    </div>
                    @else
                    <p class="mt-2 text-xs font-medium leading-relaxed text-indigo-100/50">
                        No other MFA method is enabled for this account yet. After signing in, go to
                        <span class="font-bold text-indigo-100">Settings &gt; Security</span>
                        to add an authenticator app or SMS OTP.
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="relative z-10 mt-8 flex justify-center px-6 pointer-events-none">
        <div class="px-5 py-2.5 bg-white/5 backdrop-blur-md rounded-full border border-white/10 shadow-sm">
            <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-[0.3em] md:tracking-[0.6em] text-indigo-200/30 whitespace-nowrap text-center">
                &copy; {{ date('Y') }} EMIS <span class="mx-1 opacity-20">|</span> All rights reserved
            </p>
        </div>
    </div>
</div>
