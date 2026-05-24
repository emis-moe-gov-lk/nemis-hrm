<div
    x-data="{
        showBrowserHelp: false,
        incompatibleBrowser: false,
        init: function() {
            const ua = navigator.userAgent;
            const isLegacyWindows = /Windows NT 6\.[0-3]/.test(ua);
            const getVersion = (pattern) => {
                const match = ua.match(pattern);
                return match ? parseInt(match[1], 10) : null;
            };

            const chrome = getVersion(/Chrome\/(\d+)/);
            const edge = getVersion(/Edg\/(\d+)/);
            const firefox = getVersion(/Firefox\/(\d+)/);
            const safari = ! chrome && ! edge ? getVersion(/Version\/(\d+).*Safari/) : null;

            this.incompatibleBrowser =
                isLegacyWindows ||
                (chrome !== null && chrome < 110) ||
                (edge !== null && edge < 110) ||
                (firefox !== null && firefox < 115) ||
                (safari !== null && safari < 16);
        }
    }"
    class="min-h-screen flex flex-col items-center justify-center relative bg-gray-950 text-white font-sans px-4 py-8 sm:py-10 overflow-x-hidden overflow-y-auto">

    <div class="absolute inset-0 z-0 overflow-hidden">
        <div aria-hidden="true" class="absolute inset-0 z-0 bg-linear-to-br from-gray-900 via-indigo-950 to-blue-950 opacity-95"></div>
        <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] bg-linear-to-br from-blue-600/10 to-transparent rounded-full blur-[140px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] bg-linear-to-tl from-indigo-600/10 to-transparent rounded-full blur-[120px]"></div>

        <div class="absolute top-[15%] right-[10%] w-48 h-48 bg-white/5 backdrop-blur-md rounded-4xl border border-white/10 shadow-sm rotate-12 animate-bounce [animation-duration:6s] hidden lg:block"></div>
        <div class="absolute bottom-[20%] left-[5%] w-64 h-32 bg-blue-500/5 backdrop-blur-md rounded-4xl border border-white/10 shadow-sm -rotate-6 animate-pulse hidden lg:block"></div>
    </div>

    <div class="relative z-10 my-auto w-full max-w-[900px] flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

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
                A <span class="text-white font-bold">unified data architecture</span> built for transparency and security. Empowering leaders to drive national educational success.
            </p>
        </div>

        <div class="lg:w-1/2 w-full max-w-[420px] flex flex-col items-center gap-4">
            <div class="absolute -top-6 -right-6 w-24 h-24 border-8 border-blue-500/10 rounded-full"></div>

            <div class="bg-white/5 backdrop-blur-[35px] rounded-[3.5rem] p-8 sm:p-12 shadow-2xl border border-white/10 relative overflow-hidden">

                <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-blue-400/30 to-transparent"></div>

                <div class="mb-8">
                    <h3 class="text-xl font-bold text-white tracking-tight">Welcome Back</h3>
                    <p class="text-xs text-indigo-200/50 font-medium">Please verify your access</p>
                </div>

                <x-auth-session-status class="mb-6 text-center text-xs font-bold text-blue-400" :status="session('status')" />

                <form wire:submit.prevent="login" class="space-y-5">
                    <div class="group">
                        <input type="email"
                            wire:model="email"
                            autofocus
                            class="
                                    w-full px-6 py-4
                                    bg-white/5
                                    border {{ $errors->has('email') ? 'border-red-500/50' : 'border-white/10' }}
                                    rounded-3xl
                                    text-sm font-medium
                                    text-white
                                    placeholder:text-indigo-200/30
                                    transition-all
                                    group-focus-within:bg-white/10
                                    group-focus-within:border-blue-500/50
                                    group-focus-within:ring-4 group-focus-within:ring-blue-500/5
                                    outline-none
                                    shadow-sm
                            "
                            placeholder="Email address">


                        @error('email')
                        <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="group relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 pl-5 flex items-center {{ $errors->has('password') ? 'text-red-400' : 'text-indigo-300/50' }} pointer-events-none transition-colors group-focus-within:text-blue-400">
                            <flux:icon name="lock-closed" class="h-4 w-4" />
                        </span>

                        <input
                            :type="show ? 'text' : 'password'"
                            wire:model="password"
                            class="
                                w-full pl-12 pr-14 py-4
                                bg-white/5
                                border {{ $errors->has('password') ? 'border-red-500/50' : 'border-white/10' }}
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
                            placeholder="Password">

                        <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                            @error('password')
                            <flux:icon name="exclamation-circle" class="w-4 h-4 text-red-400 animate-bounce" />
                            @enderror

                            <button type="button" @click="show = !show" class="text-indigo-300/50 hover:text-blue-400 transition-colors p-1 focus:outline-none">
                                <flux:icon x-show="!show" name="eye" class="h-4 w-4" />
                                <flux:icon x-show="show" x-cloak name="eye-slash" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    @if (Route::has('password.request'))
                    <div class="flex justify-end -mt-1">
                        <a href="{{ route('password.request') }}" wire:navigate
                            class="text-[10px] font-black text-blue-400/70 uppercase tracking-widest hover:text-blue-300 transition-colors">
                            Forgot password?
                        </a>
                    </div>
                    @endif

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

                    <template x-if="incompatibleBrowser">
                        <div class="rounded-3xl border border-amber-300/25 bg-amber-400/10 px-4 py-3 text-[11px] font-bold leading-relaxed text-amber-100">
                            This browser is not compatible with the system security check. Please use an updated browser on Windows 10/11 or later.
                        </div>
                    </template>

                    <button type="submit" wire:loading.attr="disabled" wire:target="login" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white rounded-[1.8rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2">
                        <!-- Loading Spinner -->
                        <svg wire:loading wire:target="login" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="login">Sign In</span>
                        <span wire:loading wire:target="login">Signing In...</span>
                    </button>
                </form>

                <div class="mt-10 flex flex-col items-center gap-6">
                    <div class="flex items-center gap-4 w-full">
                        <div class="h-px flex-1 bg-white/10"></div>
                        <span class="text-[9px] font-bold text-indigo-300/30 uppercase tracking-widest">Register</span>
                        <div class="h-px flex-1 bg-white/10"></div>
                    </div>

                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" wire:navigate
                        class="group flex items-center justify-center gap-2 text-sm font-bold text-indigo-100 hover:text-blue-400 transition-colors">
                        Create a new account
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    @endif
                </div>

            </div>
            {{-- Compatible Browsers Button (below the card) --}}
            <button
                type="button"
                @click="showBrowserHelp = true"
                class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.22em] text-blue-300/40 hover:text-blue-200 transition focus:outline-none">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3.75l7.5 3.75v5.25c0 4.25-2.98 7.96-7.5 9-4.52-1.04-7.5-4.75-7.5-9V7.5L12 3.75z" />
                </svg>
                Compatible Browsers
            </button>

        </div>
    </div>

    <div
        x-cloak
        x-show="showBrowserHelp"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 px-4 py-8 backdrop-blur-md"
        role="dialog"
        aria-modal="true"
        aria-labelledby="compatible-browsers-title"
        @keydown.escape.window="showBrowserHelp = false">
        <div
            x-show="showBrowserHelp"
            x-transition.scale.origin.center
            @click.outside="showBrowserHelp = false"
            class="w-full max-w-md rounded-4xl border border-blue-300/20 bg-[#071225] p-6 text-left shadow-2xl shadow-blue-950/60">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.28em] text-blue-300">Secure login</p>
                    <h4 id="compatible-browsers-title" class="mt-2 text-2xl font-black tracking-tight text-white">Compatible Browsers</h4>
                </div>

                <button
                    type="button"
                    @click="showBrowserHelp = false"
                    class="rounded-full bg-white/8 p-2 text-indigo-100/70 transition hover:bg-white/15 hover:text-white"
                    aria-label="Close compatible browsers">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-6 space-y-4 text-sm leading-relaxed text-indigo-100/75">
                <p>For secure sign in, please use an up-to-date browser:</p>
                <div class="grid gap-2 text-xs font-bold text-indigo-100">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Google Chrome 110 or newer</div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Microsoft Edge 110 or newer</div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Mozilla Firefox 115 ESR or newer</div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Safari 16 or newer</div>
                </div>
                <p class="rounded-2xl border border-amber-300/25 bg-amber-300/10 p-4 text-xs font-bold leading-relaxed text-amber-100">
                    Windows 10/11 is recommended. Windows 7/8 and Chrome 109 or older may not complete the security check.
                </p>
            </div>
        </div>
    </div>


    <div class="relative z-10 mt-3 mb-2 flex justify-center px-6 pointer-events-none">
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
