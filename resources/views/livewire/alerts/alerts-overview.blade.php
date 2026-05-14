<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Alerts Overview') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage Alerts!') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-alerts.layout>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    
            @canany([
                'teacher.profile.confirm',
                'principal.profile.confirm',
                'dos.profile.confirm',
                'mso.profile.confirm',
                'sleas.profile.confirm',
                'sltas.profile.confirm',
                'sltes.profile.confirm'
            ])
                @if ($pendingConfirmationCount > 0)
                    <div class="group relative aspect-square flex flex-col items-center justify-center rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                        <!-- Background Gradient Effect -->
                        <div class="absolute inset-0 bg-linear-to-br from-green-50 to-white dark:from-green-900/20 dark:to-zinc-900 opacity-50 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Top accent border -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-green-500 dark:bg-green-400"></div>

                        <div class="relative z-10 flex flex-col items-center p-4 w-full h-full justify-center">
                            <div class="mt-2 p-3.5 bg-green-100 dark:bg-green-500/20 rounded-full mb-4 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3 ring-4 ring-green-50 dark:ring-green-900/30">
                                <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            
                            <span class="text-5xl font-black text-zinc-900 dark:text-white tracking-tight mb-2">{{ $pendingConfirmationCount }}</span>
                            <span class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 mb-5 text-center">Pending Confirmation</span>
                            
                            <a href="{{ route('alerts.pending-confirmation') }}">
                                <flux:button variant="primary" size="sm" class="bg-green-600 hover:bg-green-700 text-white border-transparent dark:bg-green-500 dark:hover:bg-green-400 dark:text-green-950 transition-colors shadow-sm" icon="eye">
                                    View
                                </flux:button>
                            </a>
                        </div>
                    </div>
                @endif
            @endcanany

            @canany([
                'teacher.profile.verify',
                'principal.profile.verify',
                'dos.profile.verify',
                'mso.profile.verify',
                'sleas.profile.verify',
                'sltas.profile.verify',
                'sltes.profile.verify'
            ])
                @if ($pendingVerificationCount > 0)
                    <div class="group relative aspect-square flex flex-col items-center justify-center rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                        <!-- Background Gradient Effect -->
                        <div class="absolute inset-0 bg-linear-to-br from-yellow-50 to-white dark:from-yellow-900/20 dark:to-zinc-900 opacity-50 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Top accent border -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-yellow-500 dark:bg-yellow-400"></div>

                        <div class="relative z-10 flex flex-col items-center p-4 w-full h-full justify-center">
                            <div class="mt-2 p-3.5 bg-yellow-100 dark:bg-yellow-500/20 rounded-full mb-4 transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3 ring-4 ring-yellow-50 dark:ring-yellow-900/30">
                                <svg class="w-7 h-7 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            
                            <span class="text-5xl font-black text-zinc-900 dark:text-white tracking-tight mb-2">{{ $pendingVerificationCount }}</span>
                            <span class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 mb-5 text-center">Pending Verification</span>
                            
                            <a href="{{ route('alerts.pending-verification') }}">
                                <flux:button variant="primary" size="sm" class="bg-yellow-500 hover:bg-yellow-600 text-white border-transparent dark:bg-yellow-500 dark:hover:bg-yellow-400 dark:text-yellow-950 transition-colors shadow-sm" icon="eye">
                                    View
                                </flux:button>
                            </a>
                        </div>
                    </div>
                @endif
            @endcanany

        </div>

    </x-alerts.layout>
</section>
