<div class="flex flex-col min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="sticky top-0 z-10 w-full bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm">
        <nav class="max-w-7xl mx-left px-4 sm:px-6 lg:px-8 flex items-left overflow-x-auto no-scrollbar" x-data>
            
            {{-- Tab: Primary/Overview --}}
            <a href="{{ route('alerts.overview') }}" wire:navigate
                class="relative flex items-center gap-4 px-6 py-4 min-w-[180px] transition-all group
                {{ request()->routeIs('alerts.overview') ? 'text-indigo-600' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                
                <flux:icon.home variant="micro" class="size-5 {{ request()->routeIs('alerts.overview') ? 'text-indigo-600' : 'text-slate-400' }}" />
                <div class="flex flex-col">
                    <span class="text-sm font-bold">{{ __('Primary') }}</span>
                    <span class="text-[11px] text-slate-400 group-hover:text-slate-500 truncate">General metrics</span>
                </div>

                @if(request()->routeIs('alerts.overview'))
                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 rounded-t-full"></div>
                @endif
            </a>

            {{-- Tab: Pending Confirmation (Promotions Style) --}}
            @canany(['teacher.profile.confirm', 'principal.profile.confirm', 'dos.profile.confirm', 'mso.profile.confirm', 'sleas.profile.confirm', 'sltas.profile.confirm', 'sltes.profile.confirm'])
                <a href="{{ route('alerts.pending-confirmation') }}" wire:navigate
                    class="relative flex items-center gap-4 px-6 py-4 min-w-[240px] transition-all group
                    {{ request()->routeIs('alerts.pending-confirmation') ? 'text-indigo-600' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    
                    <flux:icon.tag variant="micro" class="size-5 {{ request()->routeIs('alerts.pending-confirmation') ? 'text-indigo-600' : 'text-slate-400' }}" />
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold">{{ __('Confirmation') }}</span>
                            <span class="bg-emerald-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-tighter">
                                New
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400 group-hover:text-slate-500 truncate">Profiles awaiting approval</span>
                    </div>

                    @if(request()->routeIs('alerts.pending-confirmation'))
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 rounded-t-full"></div>
                    @endif
                </a>
            @endcanany

            {{-- Tab: Pending Verification (Social Style) --}}
            @canany(['teacher.profile.verify', 'principal.profile.verify', 'dos.profile.verify', 'mso.profile.verify', 'sleas.profile.verify', 'sltas.profile.verify', 'sltes.profile.verify'])
                <a href="{{ route('alerts.pending-verification') }}" wire:navigate
                    class="relative flex items-center gap-4 px-6 py-4 min-w-[240px] transition-all group
                    {{ request()->routeIs('alerts.pending-verification') ? 'text-indigo-600' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    
                    <flux:icon.shield-check variant="micro" class="size-5 {{ request()->routeIs('alerts.pending-verification') ? 'text-indigo-600' : 'text-slate-400' }}" />
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold">{{ __('Verification') }}</span>
                            <span class="bg-blue-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-tighter">
                                NEW
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400 group-hover:text-slate-500 truncate">Identity & security checks</span>
                    </div>

                    @if(request()->routeIs('alerts.pending-verification'))
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 rounded-t-full"></div>
                    @endif
                </a>
            @endcanany
        </nav>
    </div>

    {{-- Main Content Slot --}}
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="animate-in fade-in slide-in-from-bottom-4 duration-700">
            {{ $slot }}
        </div>
    </main>
</div>

<style>
    /* Hide scrollbar for the tab bar while allowing horizontal scroll on mobile */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>