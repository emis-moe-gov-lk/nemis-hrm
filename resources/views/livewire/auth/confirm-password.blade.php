<div class="min-h-screen flex items-center justify-center relative bg-gray-950 text-white font-sans px-4 overflow-hidden">

    <div class="absolute inset-0 z-0 overflow-hidden">
        <div aria-hidden="true" class="absolute inset-0 z-0 bg-linear-to-br from-gray-900 via-indigo-950 to-blue-950 opacity-95"></div>
        <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] bg-linear-to-br from-blue-600/10 to-transparent rounded-full blur-[140px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] bg-linear-to-tl from-indigo-600/10 to-transparent rounded-full blur-[120px]"></div>
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
                    <p class="text-[10px] font-bold text-blue-300 uppercase tracking-[0.4em]">Identity Verification</p>
                </div>
            </a>
            <p class="hidden lg:block text-indigo-100/60 text-sm leading-relaxed max-w-xs font-medium">
                You are entering a <span class="text-white font-bold">protected zone</span>. To ensure your account's safety, please verify your credentials before proceeding.
            </p>
        </div>

        <div class="lg:w-1/2 w-full max-w-[420px] relative">
            <div class="absolute -top-6 -left-6 w-20 h-20 border-8 border-blue-500/10 rounded-full animate-pulse"></div>

            <div class="bg-white/5 backdrop-blur-[35px] rounded-[3.5rem] p-8 sm:p-12 shadow-2xl border border-white/10 relative overflow-hidden">

                <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-blue-400/30 to-transparent"></div>

                <div class="mb-8">
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ __('Confirm password') }}</h3>
                    <p class="text-xs text-indigo-200/50 font-medium mt-2 leading-relaxed">
                        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                    </p>
                </div>

                <x-auth-session-status class="mb-6 text-center text-xs font-bold text-blue-400" :status="session('status')" />

                <form method="POST" wire:submit="confirmPassword" class="space-y-6">
                    @csrf

                    <div class="group relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 pl-5 flex items-center {{ $errors->has('password') ? 'text-red-400' : 'text-indigo-300/50' }} pointer-events-none transition-colors group-focus-within:text-blue-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>

                        <input :type="show ? 'text' : 'password'" wire:model="password" required autocomplete="current-password"
                            class="w-full pl-12 pr-14 py-4 bg-white/5 border border-white/10 rounded-3xl text-sm font-medium transition-all group-focus-within:bg-white/10 group-focus-within:border-blue-500/50 group-focus-within:ring-4 group-focus-within:ring-blue-500/5 outline-none placeholder:text-indigo-200/30 shadow-sm"
                            placeholder="Confirm your password">

                        <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-indigo-300/50 hover:text-blue-400 transition-colors focus:outline-none">
                            <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m10.735 10.65a4.5 4.5 0 01-6.837-5.658M12 9c1.556 0 2.964.633 3.978 1.655M21.121 21.121L3 3" />
                            </svg>
                        </button>
                    </div>

                    <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white rounded-[1.8rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 transition-all hover:-translate-y-1 active:scale-95">
                        {{ __('Verify Identity') }}
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <a href="{{ route('login') }}" class="text-[10px] font-bold text-indigo-300/50 uppercase tracking-widest hover:text-white transition-colors">
                        {{ __('Cancel and Logout') }}
                    </a>
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
