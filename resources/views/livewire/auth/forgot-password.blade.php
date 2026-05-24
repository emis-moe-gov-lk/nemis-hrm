<div class="min-h-screen flex items-center justify-center relative bg-gray-950 text-white font-sans px-4 overflow-hidden">
    <div aria-hidden="true" class="absolute inset-0 z-0 bg-linear-to-br from-gray-900 via-indigo-950 to-blue-950 opacity-95"></div>

    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] bg-linear-to-br from-indigo-100/40 dark:from-indigo-900/20 to-transparent rounded-full blur-[140px]"></div>
        <div class="absolute bottom-[10%] right-[10%] w-64 h-32 bg-indigo-50/50 dark:bg-indigo-950/30 backdrop-blur-md rounded-4xl border border-white/80 dark:border-slate-700/80 shadow-sm rotate-6 hidden lg:block"></div>
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
                    <p class="text-[10px] font-bold text-blue-300 uppercase tracking-[0.4em]">Recovery Portal</p>
                </div>
            </a>
            <p class="hidden lg:block text-indigo-100/60 text-sm leading-relaxed max-w-xs font-medium">
                Forgotten your access key? No worries. Enter your <span class="text-white font-bold">official email</span> and we'll help you securely restore your session.
            </p>
        </div>

        <div class="lg:w-1/2 w-full max-w-[420px] relative">
            <div class="absolute -bottom-6 -left-6 w-20 h-20 border-8 border-indigo-500/10 dark:border-indigo-400/10 rounded-full"></div>

            <div class="bg-white/5 backdrop-blur-[35px] rounded-[3.5rem] p-8 sm:p-12 shadow-2xl border border-white/10 relative overflow-hidden">

                <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-blue-400/30 to-transparent"></div>

                <div class="mb-8">
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ __('Forgot password') }}</h3>
                    <p class="text-xs text-indigo-200/50 font-medium mt-1">{{ __('Enter your email to receive a password reset link') }}</p>
                </div>

                <x-auth-session-status class="mb-6 text-center text-xl font-bold text-blue-400" :status="session('status')" />

                @if (!session('status'))
                <form wire:submit.prevent="sendPasswordResetLink" class="space-y-6">
                    <div class="group">
                        <input type="email" wire:model="email" required autofocus
                            class="w-full px-6 py-4 bg-white/5 border {{ $errors->has('email') ? 'border-red-500/50' : 'border-white/10' }} rounded-3xl text-sm font-medium text-white transition-all group-focus-within:bg-white/10 group-focus-within:border-blue-500/50 group-focus-within:ring-4 group-focus-within:ring-blue-500/5 outline-none placeholder:text-indigo-200/30 shadow-sm"
                            placeholder="Email address">
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

                    <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white rounded-[1.8rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 transition-all hover:-translate-y-1 active:scale-95">
                        {{ __('Send Reset Link') }}
                    </button>
                </form>
                @endif

                <div class="mt-10 pt-8 border-t border-white/10 text-center">
                    <div class="flex items-center justify-center gap-2 text-sm font-medium text-indigo-100/60">
                        <span>{{ __('Or, return to') }}</span>
                        <a href="{{ route('login') }}" wire:navigate class="text-white font-bold hover:text-blue-400 transition-colors">
                            {{ __('log in') }}
                        </a>
                    </div>
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
