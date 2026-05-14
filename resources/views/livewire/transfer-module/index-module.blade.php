<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
    {{-- Refined Professional Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-10 border-b border-slate-100 dark:border-zinc-800">
        <div class="space-y-2">
            <flux:heading size="xl" level="1" class="font-black! tracking-tight">
                {{ __('Transfer Management') }}
            </flux:heading>
            <flux:subheading class="max-w-2xl text-lg leading-relaxed font-medium">
                {{ __('Centralized oversight for national, provincial, and zonal employee transfers. Manage policies, coordinate boards, and monitor application flows.') }}
            </flux:subheading>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <flux:button href="{{ route('transfer.transfer-policy') }}" variant="primary" icon="plus" size="sm" class="bg-indigo-600! hover:bg-indigo-700! border-none text-white font-bold px-4">
                {{ __('New Policy') }}
            </flux:button>
        </div>
    </div>

    {{-- System Statistics (Metric Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Applications --}}
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Total Applications') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_applications']) }}</h3>
                </div>
                <div class="p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                    <flux:icon name="clipboard-document-list" size="lg" />
                </div>
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="clipboard-document-list" class="w-24 h-24" />
            </div>
        </div>

        {{-- Pending Actions --}}
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Pending Processing') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['pending_applications']) }}</h3>
                </div>
                <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                    <flux:icon name="clock" size="lg" />
                </div>
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="clock" class="w-24 h-24" />
            </div>
        </div>

        {{-- Active Policies --}}
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Published Policies') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['active_policies']) }}</h3>
                </div>
                <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                    <flux:icon name="document-check" size="lg" />
                </div>
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="document-check" class="w-24 h-24" />
            </div>
        </div>

        {{-- Total Policies --}}
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Total Definitions') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_policies']) }}</h3>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">
                    <flux:icon name="document-duplicate" size="lg" />
                </div>
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="document-duplicate" class="w-24 h-24" />
            </div>
        </div>
    </div>

    {{-- Transfer Oversight Boards --}}
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Transfer Oversight Boards') }}</h2>
            <div class="h-px flex-1 bg-slate-100 dark:bg-zinc-800"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- National Board --}}
            <a href="{{ route('transfer-board.ntional-teacher-transfer') }}" class="group relative p-8 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-indigo-400 transition-all duration-300 overflow-hidden">
                <div class="relative z-10">
                    <div class="mb-6 w-14 h-14 bg-indigo-50 dark:bg-indigo-950/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-all duration-300">
                        <flux:icon name="globe-alt" class="text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">{{ __('National Board') }}</h3>
                    <p class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed group-hover:text-slate-600 dark:group-hover:text-zinc-300 transition-colors">
                        {{ __('Oversee inter-provincial transfers and national level policy implementation.') }}
                    </p>
                </div>
                <div class="absolute -right-10 -bottom-10 opacity-[0.02] dark:opacity-[0.04] group-hover:scale-125 transition-all duration-700">
                    <flux:icon name="globe-alt" class="w-40 h-40" />
                </div>
            </a>

            {{-- Provincial Board --}}
            <a href="{{ route('transfer-board.province-teacher-transfer') }}" class="group relative p-8 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-blue-400 transition-all duration-300 overflow-hidden">
                <div class="relative z-10">
                    <div class="mb-6 w-14 h-14 bg-blue-50 dark:bg-blue-950/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-all duration-300">
                        <flux:icon name="building-office-2" class="text-blue-600 dark:text-blue-400" />
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">{{ __('Provincial Board') }}</h3>
                    <p class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed group-hover:text-slate-600 dark:group-hover:text-zinc-300 transition-colors">
                        {{ __('Manage intra-provincial teacher transfers across all zonal departments.') }}
                    </p>
                </div>
                <div class="absolute -right-10 -bottom-10 opacity-[0.02] dark:opacity-[0.04] group-hover:scale-125 transition-all duration-700">
                    <flux:icon name="building-office-2" class="w-40 h-40" />
                </div>
            </a>

            {{-- Zonal Board --}}
            <a href="{{ route('transfer-board.zone-teacher-transfer') }}" class="group relative p-8 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-emerald-400 transition-all duration-300 overflow-hidden">
                <div class="relative z-10">
                    <div class="mb-6 w-14 h-14 bg-emerald-50 dark:bg-emerald-950/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-all duration-300">
                        <flux:icon name="map-pin" class="text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">{{ __('Zonal Board') }}</h3>
                    <p class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed group-hover:text-slate-700 dark:group-hover:text-zinc-300 transition-colors">
                        {{ __('Coordinate school-to-school transfers within a specific educational zone.') }}
                    </p>
                </div>
                <div class="absolute -right-10 -bottom-10 opacity-[0.02] dark:opacity-[0.04] group-hover:scale-125 transition-all duration-700">
                    <flux:icon name="map-pin" class="w-40 h-40" />
                </div>
            </a>
        </div>
    </div>

    {{-- Administrative Tools --}}
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Administrative Tools') }}</h2>
            <div class="h-px flex-1 bg-slate-100 dark:bg-zinc-800"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Policy Management Card --}}
            <a href="{{ route('transfer.transfer-policies') }}" class="group flex items-center gap-8 p-8 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-[2.5rem] transition-all duration-300 hover:shadow-xl hover:border-indigo-100 dark:hover:border-zinc-700">
                <div class="shrink-0 flex items-center justify-center w-20 h-20 rounded-3xl bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <flux:icon name="document-text" class="w-10 h-10" />
                </div>
                <div class="flex-1 space-y-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ __('Policy Definitions') }}</h3>
                        <flux:icon.arrow-right variant="mini" class="text-slate-300 group-hover:text-indigo-600 transition-all group-hover:translate-x-1" />
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-zinc-400 leading-snug group-hover:text-slate-600 dark:group-hover:text-zinc-300 transition-colors">
                        {{ __('Configure transfer rules, point rankings, and eligibility criteria for different categories.') }}
                    </p>
                </div>
            </a>

            {{-- Request Portal Card --}}
            <a href="{{ route('transfer.teacher-transfer-request') }}" class="group flex items-center gap-8 p-8 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-[2.5rem] transition-all duration-300 hover:shadow-xl hover:border-blue-100 dark:hover:border-zinc-700">
                <div class="shrink-0 flex items-center justify-center w-20 h-20 rounded-3xl bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <flux:icon name="arrows-right-left" class="w-10 h-10" />
                </div>
                <div class="flex-1 space-y-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ __('Request Pipeline') }}</h3>
                        <flux:icon.arrow-right variant="mini" class="text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1" />
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-zinc-400 leading-snug group-hover:text-slate-600 dark:group-hover:text-zinc-300 transition-colors">
                        {{ __('Handle internal department requests, administrative transfers, and special recommendation queues.') }}
                    </p>
                </div>
            </a>
        </div>
    </div>
</div>