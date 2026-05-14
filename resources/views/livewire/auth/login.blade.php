<div class="min-h-screen flex items-center justify-center relative bg-[#fdfeff] dark:bg-[#030712] font-sans px-4 overflow-hidden">

    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] bg-linear-to-br from-indigo-100/40 dark:from-indigo-900/20 to-transparent rounded-full blur-[140px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] bg-linear-to-tl from-blue-100/30 dark:from-blue-900/10 to-transparent rounded-full blur-[120px]"></div>

        <div class="absolute top-[15%] right-[10%] w-48 h-48 bg-white/40 dark:bg-slate-900/40 backdrop-blur-md rounded-4xl border border-white/80 dark:border-slate-800/80 shadow-sm rotate-12 animate-bounce [animation-duration:6s] hidden lg:block"></div>
        <div class="absolute bottom-[20%] left-[5%] w-64 h-32 bg-indigo-50/50 dark:bg-indigo-950/30 backdrop-blur-md rounded-4xl border border-white/80 dark:border-slate-800/80 shadow-sm -rotate-6 animate-pulse hidden lg:block"></div>
    </div>

    <div class="relative z-10 w-full max-w-[900px] flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

        <div class="lg:w-1/2 text-center lg:text-left space-y-6">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center lg:items-start gap-4" wire:navigate>
                <div class="p-4 bg-white dark:bg-slate-900 shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.2)] rounded-4xl border border-gray-50 dark:border-slate-800 transition-transform hover:scale-105">
                    <x-app-logo-icon class="w-12 h-auto fill-current text-black dark:text-white" />
                </div>
                <div class="space-y-1">
                    <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter italic">
                        EMIS <span class="text-indigo-600 dark:text-indigo-400">V1.3a</span>
                    </h2>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em]">Education Management Information System</p>
                </div>
            </a>
            <p class="hidden lg:block text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs font-medium opacity-80">
                A <span class="text-slate-900 dark:text-slate-200 font-bold">unified data architecture</span> built for transparency and security. Empowering leaders to drive national educational success.
            </p>
        </div>

        <div class="lg:w-1/2 w-full max-w-[420px] relative">
            <div class="absolute -top-6 -right-6 w-24 h-24 border-12 border-indigo-500/10 dark:border-indigo-400/5 rounded-full"></div>

            <div class="bg-white/60 dark:bg-slate-900/60 backdrop-blur-[35px] rounded-[3.5rem] p-8 sm:p-12 shadow-[0_40px_80px_-15px_rgba(0,0,0,0.08)] dark:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.3)] border border-white/80 dark:border-slate-800/80 relative overflow-hidden">

                <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-indigo-400/20 dark:via-indigo-400/30 to-transparent"></div>

                <div class="mb-8">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Welcome Back</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Please verify your access</p>
                </div>

                <x-auth-session-status class="mb-6 text-center text-xs font-bold text-indigo-600 dark:text-indigo-400" :status="session('status')" />

                <form method="POST" wire:submit="login" class="space-y-5">
                    @csrf

                    <div class="group">
                        <input type="email"
                            wire:model="email"
                            autofocus
                            class="
                                    w-full px-6 py-4
                                    bg-slate-100/30 dark:bg-slate-800/30
                                    border @error('email') border-red-500/50 @else @enderror
                                    rounded-3xl
                                    text-sm font-medium
                                    text-slate-900 dark:text-white
                                    placeholder:text-slate-400 dark:placeholder:text-slate-500
                                    transition-all
                                    group-focus-within:bg-white dark:group-focus-within:bg-slate-800
                                    group-focus-within:border-indigo-100 dark:group-focus-within:border-indigo-900/50
                                    group-focus-within:ring-4 group-focus-within:ring-indigo-500/5
                                    outline-none
                                    shadow-sm
                            "
                            placeholder="Email address">


                        @error('email')
                        <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-500 dark:text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="group relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 pl-5 flex items-center @error('password') text-red-400 @else @enderror pointer-events-none transition-colors group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>

                        <input
                            :type="show ? 'text' : 'password'"
                            wire:model="password"
                            class="
                                w-full pl-12 pr-24 py-4
                                bg-slate-100/30 dark:bg-slate-800/30
                                border @error('password') border-red-500/50 @else @enderror
                                rounded-3xl
                                text-sm font-medium
                                text-slate-900 dark:text-white
                                placeholder:text-slate-400 dark:placeholder:text-slate-500
                                transition-all
                                group-focus-within:bg-white dark:group-focus-within:bg-slate-800
                                group-focus-within:border-indigo-100 dark:group-focus-within:border-indigo-900/50
                                group-focus-within:ring-4 group-focus-within:ring-indigo-500/5
                                caret-indigo-600 dark:caret-indigo-400
                                outline-none
                                shadow-sm
                            "
                            placeholder="Password">


                        <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-3">
                            @error('password')
                            <svg class="w-4 h-4 text-red-500 dark:text-red-400 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            @enderror

                            <button type="button" @click="show = !show" class="text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors p-1 focus:outline-none">
                                <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m10.735 10.65a4.5 4.5 0 01-6.837-5.658M12 9c1.556 0 2.964.633 3.978 1.655M21.121 21.121L3 3" />
                                </svg>
                            </button>

                            <div class="h-3 w-px bg-slate-200 dark:bg-slate-700"></div>

                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" wire:navigate class="text-[10px] font-black text-indigo-500 dark:text-indigo-400 uppercase tracking-tighter hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">Forgot?</a>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[1.8rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-200 dark:shadow-indigo-900/20 transition-all hover:-translate-y-1 active:scale-95">
                        Sign In
                    </button>
                </form>

                <div class="mt-10 flex flex-col items-center gap-6">
                    <div class="flex items-center gap-4 w-full">
                        <div class="h-px flex-1 bg-slate-200/50 dark:bg-slate-700/50"></div>
                        <span class="text-[9px] font-bold text-slate-300 dark:text-slate-600 uppercase tracking-widest">Register</span>
                        <div class="h-px flex-1 bg-slate-200/50 dark:bg-slate-700/50"></div>
                    </div>

                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" wire:navigate
                        class="group flex items-center justify-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        Create a new account
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-6 inset-x-0 flex justify-center px-6 pointer-events-none">
        <div class="px-5 py-2.5 bg-white/10 dark:bg-black/10 backdrop-blur-md rounded-full border border-white/20 dark:border-white/10 shadow-sm">
            <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-[0.3em] md:tracking-[0.6em] text-slate-500/80 dark:text-slate-500 whitespace-nowrap text-center">
                &copy; {{ date('Y') }} EMIS <span class="mx-1 opacity-50">|</span> All rights reserved
            </p>
        </div>
    </div>
</div>