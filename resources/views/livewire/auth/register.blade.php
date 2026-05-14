<div class="min-h-screen flex items-center justify-center relative bg-[#fdfeff] dark:bg-[#030712] font-sans px-4 py-12 overflow-x-hidden">

    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-linear-to-bl from-indigo-100/30 dark:from-indigo-900/20 to-transparent rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[10%] left-[-5%] w-[500px] h-[500px] bg-linear-to-tr from-blue-50/40 dark:from-blue-900/10 to-transparent rounded-full blur-[100px]"></div>
    </div>

    <div class="relative z-10 w-full max-w-[1000px] flex flex-col lg:flex-row items-center gap-12">

        <div class="lg:w-2/5 text-center lg:text-left space-y-6">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center lg:items-start gap-4" wire:navigate>
                <div class="p-4 bg-white dark:bg-slate-900 shadow-sm dark:shadow-[0_10px_40px_rgba(0,0,0,0.2)] rounded-4xl border border-gray-50 dark:border-slate-800 transition-transform hover:scale-105">
                    <x-app-logo-icon class="w-12 h-auto fill-current text-black dark:text-white" />
                </div>
                <div class="space-y-1">
                    <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter italic">
                        EMIS <span class="text-indigo-600 dark:text-indigo-400">V1.3a</span>
                    </h2>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em]">Personnel Enrollment</p>
                </div>
            </a>
            <p class="hidden lg:block text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs font-medium opacity-80">
                Join the <span class="text-slate-900 dark:text-slate-200 font-bold">national unified network</span>. Create your administrative identity to begin managing educational data with precision.
            </p>
        </div>

        <div class="lg:w-3/5 w-full max-w-[550px] relative">
            <div class="absolute -top-4 -right-4 w-16 h-16 bg-indigo-500/5 dark:bg-indigo-400/5 rounded-2xl rotate-12"></div>

            <div class="bg-white/60 dark:bg-slate-900/60 backdrop-blur-[35px] rounded-[3.5rem] p-8 md:p-12 shadow-[0_40px_80px_-15px_rgba(0,0,0,0.08)] dark:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.3)] border border-white/80 dark:border-slate-800/80 relative overflow-hidden">

                <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-indigo-400/20 dark:via-indigo-400/30 to-transparent"></div>

                <div class="mb-8 text-center lg:text-left">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Create an account') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">{{ __('Enter your details below to create your account') }}</p>
                </div>

                <x-auth-session-status class="mb-6 text-center text-xs font-bold text-indigo-600 dark:text-indigo-400" :status="session('status')" />

                <form method="POST" wire:submit="register" class="space-y-4">
                    @csrf

                    <div class="group">
                        <input type="text" wire:model="name" required autofocus placeholder="Full Name"
                            class="w-full px-6 py-4 bg-slate-100/30 dark:bg-slate-800/30 border border-transparent rounded-3xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 transition-all group-focus-within:bg-white dark:group-focus-within:bg-slate-800 group-focus-within:border-indigo-100 dark:group-focus-within:border-indigo-900/50 group-focus-within:ring-4 group-focus-within:ring-indigo-500/5 outline-none shadow-sm">
                        @error('name') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="group">
                            <input type="text" wire:model="nic" required placeholder="NIC Number"
                                class="w-full px-6 py-4 bg-slate-100/30 dark:bg-slate-800/30 border border-transparent rounded-3xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 transition-all group-focus-within:bg-white dark:group-focus-within:bg-slate-800 group-focus-within:border-indigo-100 dark:group-focus-within:border-indigo-900/50 group-focus-within:ring-4 group-focus-within:ring-indigo-500/5 outline-none shadow-sm">
                            @error('nic') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div class="group">
                            <input type="text" wire:model="contact" required placeholder="Contact Number"
                                class="w-full px-6 py-4 bg-slate-100/30 dark:bg-slate-800/30 border border-transparent rounded-3xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 transition-all group-focus-within:bg-white dark:group-focus-within:bg-slate-800 group-focus-within:border-indigo-100 dark:group-focus-within:border-indigo-900/50 group-focus-within:ring-4 group-focus-within:ring-indigo-500/5 outline-none shadow-sm">
                            @error('contact') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="group">
                        <input type="email" wire:model="email" required placeholder="Email Address"
                            class="w-full px-6 py-4 bg-slate-100/30 dark:bg-slate-800/30 border border-transparent rounded-3xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 transition-all group-focus-within:bg-white dark:group-focus-within:bg-slate-800 group-focus-within:border-indigo-100 dark:group-focus-within:border-indigo-900/50 group-focus-within:ring-4 group-focus-within:ring-indigo-500/5 outline-none shadow-sm">
                        @error('email') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ show: false }">
                        <div class="group relative">
                            <input :type="show ? 'text' : 'password'" wire:model="password" required placeholder="Password"
                                class="w-full px-6 py-4 bg-slate-100/30 dark:bg-slate-800/30 border border-transparent rounded-3xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 transition-all group-focus-within:bg-white dark:group-focus-within:bg-slate-800 group-focus-within:border-indigo-100 dark:group-focus-within:border-indigo-900/50 group-focus-within:ring-4 group-focus-within:ring-indigo-500/5 outline-none shadow-sm">
                            @error('password') <p class="mt-2 ml-4 text-[10px] font-black uppercase tracking-widest text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div class="group relative">
                            <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" required placeholder="Confirm"
                                class="w-full px-6 py-4 bg-slate-100/30 dark:bg-slate-800/30 border border-transparent rounded-3xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 transition-all group-focus-within:bg-white dark:group-focus-within:bg-slate-800 group-focus-within:border-indigo-100 dark:group-focus-within:border-indigo-900/50 group-focus-within:ring-4 group-focus-within:ring-indigo-500/5 outline-none shadow-sm">

                            <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
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

                    <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[1.8rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-200 dark:shadow-indigo-900/20 transition-all hover:-translate-y-1 active:scale-95 mt-4">
                        {{ __('Create account') }}
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-200/50 dark:border-slate-700/50 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('login') }}" wire:navigate class="ml-1 text-zinc-900 dark:text-white font-bold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            {{ __('Log in') }}
                        </a>
                    </p>
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