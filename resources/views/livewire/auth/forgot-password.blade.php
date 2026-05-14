<div class="min-h-screen flex items-center justify-center relative bg-[#fdfeff] dark:bg-[#030712] font-sans px-4 overflow-hidden">

    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] bg-linear-to-br from-indigo-100/40 dark:from-indigo-900/20 to-transparent rounded-full blur-[140px]"></div>
        <div class="absolute bottom-[10%] right-[10%] w-64 h-32 bg-indigo-50/50 dark:bg-indigo-950/30 backdrop-blur-md rounded-4xl border border-white/80 dark:border-slate-800/80 shadow-sm rotate-6 hidden lg:block"></div>
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
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em]">Recovery Portal</p>
                </div>
            </a>
            <p class="hidden lg:block text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs font-medium opacity-80">
                Forgotten your access key? No worries. Enter your <span class="text-slate-900 dark:text-slate-200 font-bold">official email</span> and we'll help you securely restore your session.
            </p>
        </div>

        <div class="lg:w-1/2 w-full max-w-[420px] relative">
            <div class="absolute -bottom-6 -left-6 w-20 h-20 border-8 border-indigo-500/10 dark:border-indigo-400/10 rounded-full"></div>

            <div class="bg-white/60 dark:bg-slate-900/60 backdrop-blur-[35px] rounded-[3.5rem] p-8 sm:p-12 shadow-[0_40px_80px_-15px_rgba(0,0,0,0.08)] dark:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.3)] border border-white/80 dark:border-slate-800/80 relative overflow-hidden">

                <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-indigo-400/20 dark:via-indigo-400/30 to-transparent"></div>

                <div class="mb-8">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Forgot password') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">{{ __('Enter your email to receive a password reset link') }}</p>
                </div>

                <x-auth-session-status class="mb-6 text-center text-xl font-bold text-indigo-600 dark:text-indigo-400" :status="session('status')" />

                @if (!session('status'))
                <form method="POST" wire:submit="sendPasswordResetLink" class="space-y-6">
                    @csrf

                    <div class="group">
                        <input type="email" wire:model="email" required autofocus
                            class="w-full px-6 py-4 bg-slate-100/30 dark:bg-slate-800/30 border border-transparent rounded-3xl text-sm font-medium text-slate-900 dark:text-white transition-all group-focus-within:bg-white dark:group-focus-within:bg-slate-800 group-focus-within:border-indigo-100 dark:group-focus-within:border-indigo-900/50 group-focus-within:ring-4 group-focus-within:ring-indigo-500/5 outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500 shadow-sm"
                            placeholder="Email address">
                    </div>

                    <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[1.8rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-200 dark:shadow-indigo-900/20 transition-all hover:-translate-y-1 active:scale-95">
                        {{ __('Send Reset Link') }}
                    </button>
                </form>
                @endif

                <div class="mt-10 pt-8 border-t border-slate-200/50 dark:border-slate-700/50 text-center">
                    <div class="flex items-center justify-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <span>{{ __('Or, return to') }}</span>
                        <a href="{{ route('login') }}" wire:navigate class="text-zinc-900 dark:text-white font-bold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            {{ __('log in') }}
                        </a>
                    </div>
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