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
                    <p class="text-[10px] font-bold text-blue-300 uppercase tracking-[0.4em]">Email Verification</p>
                </div>
            </a>
            <p class="hidden lg:block text-indigo-100/60 text-sm leading-relaxed max-w-xs font-medium">
                Verify your <span class="text-white font-bold">official identity</span>. Check your inbox for the activation link to begin your journey with the national EMIS network.
            </p>
        </div>

        <div class="lg:w-1/2 w-full max-w-[420px] relative">
            <div class="bg-white/5 backdrop-blur-[35px] rounded-[3.5rem] p-8 sm:p-12 shadow-2xl border border-white/10 relative overflow-hidden">

                <div class="absolute top-0 inset-x-0 h-px bg-linear-to-r from-transparent via-blue-400/30 to-transparent"></div>

                <div class="mb-8">
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ __('Verify Email') }}</h3>
                    <p class="text-xs text-indigo-200/50 font-medium mt-2 leading-relaxed">
                        {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
                    </p>
                </div>

                @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
                    <p class="text-xs font-bold text-emerald-400 text-center">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                </div>
                @endif

                <div class="flex flex-col items-center gap-6">
                    <button wire:click="sendVerification" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white rounded-[1.8rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 transition-all hover:-translate-y-1 active:scale-95">
                        {{ __('Resend verification email') }}
                    </button>

                    <button wire:click="logout" class="text-[10px] font-bold text-indigo-300/50 uppercase tracking-widest hover:text-white transition-colors">
                        {{ __('Log out') }}
                    </button>
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
