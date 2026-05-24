<div class="min-h-screen flex items-center justify-center relative bg-gray-950 text-white font-sans px-4 py-12 overflow-hidden">

    <div class="absolute inset-0 z-0 overflow-hidden">
        <div aria-hidden="true" class="absolute inset-0 z-0 bg-linear-to-br from-gray-900 via-indigo-950 to-blue-950 opacity-95"></div>
        <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] bg-linear-to-br from-blue-600/10 to-transparent rounded-full blur-[140px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] bg-linear-to-tl from-indigo-600/10 to-transparent rounded-full blur-[120px]"></div>
    </div>

    <div class="relative z-10 w-full max-w-[1000px] flex flex-col lg:flex-row items-center gap-12">

        <div class="lg:w-2/5 text-center lg:text-left space-y-6">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center lg:items-start gap-4" wire:navigate>
                <div class="w-20 h-20 flex items-center justify-center bg-white/5 backdrop-blur-xl shadow-2xl rounded-2xl border border-white/10 transition-transform hover:scale-105">
                    <x-app-logo-icon class="w-12 h-auto fill-current text-white" />
                </div>
                <div class="space-y-1">
                    <h2 class="text-4xl font-black text-white tracking-tighter">
                        EMIS <span class="text-blue-400">{{ ! empty($systemVersion ?? null) ? 'V' . ltrim($systemVersion, 'vV') : 'V1.3a' }}</span>
                    </h2>
                    <p class="text-[10px] font-bold text-blue-300 uppercase tracking-[0.4em]">Personnel Enrollment</p>
                </div>
            </a>
            <p class="hidden lg:block text-indigo-100/60 text-sm leading-relaxed max-w-xs font-medium">
                Join the <span class="text-white font-bold">national unified network</span>. Create your administrative identity to begin managing educational data with precision.
            </p>
        </div>

        <div class="lg:w-3/5 w-full max-w-[550px] relative">
            <div class="absolute -top-4 -right-4 w-16 h-16 bg-blue-500/5 rounded-2xl rotate-12"></div>

            <div class="bg-white/5 backdrop-blur-[35px] rounded-[3.5rem] p-8 md:p-12 shadow-2xl border border-white/10 relative overflow-hidden">

                <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-blue-400/30 to-transparent"></div>

                <div class="mb-8 text-center lg:text-left">
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ __('Create an account') }}</h3>
                    <p class="text-xs text-indigo-200/50 font-medium mt-1">{{ __('Enter your details below to create your account') }}</p>
                </div>

                <x-auth-session-status class="mb-6 text-center text-xs font-bold text-blue-400" :status="session('status')" />

                <form wire:submit.prevent="register" class="space-y-4">
                    <div class="group">
                        <input type="text" wire:model="name" required autofocus placeholder="Full Name"
                            class="w-full px-6 py-4 bg-white/5 border {{ $errors->has('name') ? 'border-red-500/50' : 'border-white/10' }} rounded-3xl text-sm font-medium text-white placeholder:text-indigo-200/30 transition-all group-focus-within:bg-white/10 group-focus-within:border-blue-500/50 group-focus-within:ring-4 group-focus-within:ring-blue-500/5 outline-none shadow-sm">
                        @error('name') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="group">
                            <input type="text" wire:model="nic" required placeholder="NIC Number"
                                class="w-full px-6 py-4 bg-white/5 border {{ $errors->has('nic') ? 'border-red-500/50' : 'border-white/10' }} rounded-3xl text-sm font-medium text-white placeholder:text-indigo-200/30 transition-all group-focus-within:bg-white/10 group-focus-within:border-blue-500/50 group-focus-within:ring-4 group-focus-within:ring-blue-500/5 outline-none shadow-sm">
                            @error('nic') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div class="group">
                            <input type="text" wire:model="contact" required placeholder="Contact Number"
                                class="w-full px-6 py-4 bg-white/5 border {{ $errors->has('contact') ? 'border-red-500/50' : 'border-white/10' }} rounded-3xl text-sm font-medium text-white placeholder:text-indigo-200/30 transition-all group-focus-within:bg-white/10 group-focus-within:border-blue-500/50 group-focus-within:ring-4 group-focus-within:ring-blue-500/5 outline-none shadow-sm">
                            @error('contact') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="group">
                        <input type="email" wire:model="email" required placeholder="Email Address"
                            class="w-full px-6 py-4 bg-white/5 border {{ $errors->has('email') ? 'border-red-500/50' : 'border-white/10' }} rounded-3xl text-sm font-medium text-white placeholder:text-indigo-200/30 transition-all group-focus-within:bg-white/10 group-focus-within:border-blue-500/50 group-focus-within:ring-4 group-focus-within:ring-blue-500/5 outline-none shadow-sm">
                        @error('email') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ show: false }">
                        <div class="group relative">
                            <input :type="show ? 'text' : 'password'" wire:model="password" required placeholder="Password"
                                class="w-full px-6 py-4 bg-white/5 border {{ $errors->has('password') ? 'border-red-500/50' : 'border-white/10' }} rounded-3xl text-sm font-medium text-white placeholder:text-indigo-200/30 transition-all group-focus-within:bg-white/10 group-focus-within:border-blue-500/50 group-focus-within:ring-4 group-focus-within:ring-blue-500/5 outline-none shadow-sm">
                            @error('password') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div class="group relative">
                            <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" required placeholder="Confirm"
                                class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-3xl text-sm font-medium text-white placeholder:text-indigo-200/30 transition-all group-focus-within:bg-white/10 group-focus-within:border-blue-500/50 group-focus-within:ring-4 group-focus-within:ring-blue-500/5 outline-none shadow-sm">

                            <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 {{ $errors->has('password') ? 'text-red-400' : 'text-indigo-300/50' }} hover:text-blue-400 transition-colors">
                                <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m10.735 10.65a4.5 4.5 0 01-6.837-5.658M12 9c1.556 0 2.964.633 3.978 1.655M21.121 21.121L3 3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if (config('services.turnstile.enabled'))
                    <div class="space-y-2">
                        <div
                            wire:ignore
                            x-data
                            x-init="
                                    const renderTurnstile = () => {
                                        if (! window.turnstile || $refs.turnstile.innerHTML.trim() !== '') return;

                                        window.turnstile.render($refs.turnstile, {
                                            sitekey: '{{ config('services.turnstile.site_key') }}',
                                            callback: (token) => $wire.set('turnstileToken', token),
                                            'expired-callback': () => $wire.set('turnstileToken', ''),
                                            'error-callback': () => $wire.set('turnstileToken', ''),
                                        });
                                    };

                                    renderTurnstile();
                                    document.addEventListener('emis:turnstile-ready', renderTurnstile);
                                    document.addEventListener('livewire:navigated', renderTurnstile, { once: true });
                                ">
                            <div x-ref="turnstile"></div>
                        </div>
                        @error('turnstile')
                        <p class="text-[10px] font-black uppercase tracking-widest text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white rounded-[1.8rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 transition-all hover:-translate-y-1 active:scale-95 mt-4">
                        {{ __('Create account') }}
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-white/10 text-center">
                    <p class="text-sm text-indigo-100/60 font-medium">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('login') }}" wire:navigate class="ml-1 text-white font-bold hover:text-blue-400 transition-colors">
                            {{ __('Log in') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-6 inset-x-0 flex justify-center px-6 pointer-events-none">
        <div class="px-5 py-2.5 bg-white/5 backdrop-blur-md rounded-full border border-white/10 shadow-sm">
            <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-[0.3em] md:tracking-[0.6em] text-indigo-200/30 whitespace-nowrap text-center">
                &copy; {{ date('Y') }} EMIS <span class="mx-1 opacity-20">|</span> All rights reserved
            </p>
        </div>
    </div>
</div>

@if (config('services.turnstile.enabled'))
<script>
    window.emisRenderTurnstile = function() {
        document.dispatchEvent(new CustomEvent('emis:turnstile-ready'));
    };
</script>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=emisRenderTurnstile" async defer></script>
@endif
