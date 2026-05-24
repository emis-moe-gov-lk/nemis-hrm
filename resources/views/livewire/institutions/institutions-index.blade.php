<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <x-page-header
        title="Institution Directory"
        subtitle="Manage institution profiles, accounts, and regional assignments"
        icon="building-office-2"
        :count="$institutions->total()"
        countLabel="Institutions Registered"
        :breadcrumbs="[
            'Institutions' => '#',
            'Institution Directory' => route('find-institutions.index')
        ]">
        <x-slot:actions>
            @can('institution.create')
            <flux:button href="{{ route('institutions.create') }}" icon="plus" class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200/50 dark:shadow-none border-none">
                Create Institution
            </flux:button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <flux:separator variant="subtle" />

    {{-- Filter Section --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-2xl p-5 shadow-sm mb-6 transition-all">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6 items-center w-full">
            {{-- Authority --}}
            <flux:select wire:model.live="authority" class="w-full">
                <flux:select.option value="">All authorities</flux:select.option>
                @foreach ($authorityOption as $authority)
                <flux:select.option value="{{ $authority->authority_id }}">{{ $authority->authority_name }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Province --}}
            <flux:select wire:model.live="province" class="w-full">
                <flux:select.option value="">All provinces</flux:select.option>
                @foreach ($provinceOption as $prov)
                <flux:select.option value="{{ $prov->workplace_id }}">{{ $prov->short_name }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Zonal --}}
            <flux:select wire:model.live="zone" class="w-full" :disabled="empty($zoneOption)">
                <flux:select.option value="">All zonal offices</flux:select.option>
                @foreach ($zoneOption as $zone)
                <flux:select.option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Divisional --}}
            <flux:select wire:model.live="division" class="w-full" :disabled="empty($divisionOption)">
                <flux:select.option value="">All divisional offices</flux:select.option>
                @foreach ($divisionOption as $division)
                <flux:select.option value="{{ $division->workplace_id }}">{{ $division->short_name }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Status --}}
            <flux:select wire:model.live="status" class="w-full">
                <flux:select.option value="">Any status</flux:select.option>
                @foreach ($statusOption as $status)
                <flux:select.option value="{{ $status->id }}">{{ $status->name }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Search --}}
            <flux:input wire:model.live.debounce.400ms="query" class="w-full" placeholder="Name or census no..." icon="magnifying-glass" />
        </div>
    </div>

    {{-- Summary Data --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2 px-1">
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-500">
            Total Institutions: <span class="text-slate-900 dark:text-white font-bold bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">{{ $institutions->total() }}</span>
        </div>
    </div>

    {{-- Horizontal Row Layout --}}
    <div class="space-y-3">
        @forelse ($institutions as $key => $institution)
        <a href="{{ route('find-institutions.basic.view', $institution->id) }}" class="block group">
            <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-2xl p-4 transition-all duration-300 hover:shadow-md hover:border-indigo-400/50 dark:hover:border-indigo-500/30">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">

                    {{-- 1. Index & Identity Profile --}}
                    <div class="flex items-center gap-4 min-w-[280px] lg:w-4/12 shrink-0">
                        {{-- Serial Number Badge --}}
                        <div class="shrink-0">
                            <div class="h-10 w-12 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center transition-colors group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10">
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                    {{ str_pad($institutions->firstItem() + $key, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>

                        <div class="relative shrink-0">
                            <div class="h-12 w-12 rounded-full ring-2 ring-slate-100 dark:ring-slate-800 bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                                <flux:icon.academic-cap variant="mini" class="text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full border-2 border-white dark:border-slate-900 {{ $institution->active_status ? 'bg-emerald-500' : 'bg-rose-500' }}" title="{{ $institution->active_status ? 'Active' : 'Inactive' }}"></span>
                        </div>

                        <div class="overflow-hidden flex-1">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $institution->name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                    CENSUS: <span class="text-slate-500 dark:text-slate-300">{{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) }}</span>
                                </p>
                                @if($institution->phone)
                                <span class="text-slate-300 dark:text-slate-600 hidden sm:inline">•</span>
                                <span class="text-[10px] font-semibold text-slate-500 hidden sm:inline">{{ $institution->phone }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. Education Offices (Zonal & Divisional) --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:gap-8 flex-1">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 shrink-0">
                                <flux:icon.building-office-2 variant="micro" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Zonal (ZEO)</span>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[150px]">
                                    {{ $institution->zonalEducationOffice->short_name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <div class="hidden sm:flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400 shrink-0">
                                <flux:icon.map-pin variant="micro" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Divisional (DEO)</span>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[150px]">
                                    {{ $institution->divisionalEducationOffice->short_name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Contact & Address & Action --}}
                    <div class="flex items-center gap-4 justify-between w-full lg:w-auto mt-2 lg:mt-0 pt-3 lg:pt-0 border-t lg:border-0 border-slate-200 dark:border-slate-700">

                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-500">
                            <flux:icon.map-pin variant="micro" class="shrink-0 text-slate-500" />
                            <span class="text-[11px] font-medium truncate max-w-[140px] hidden sm:block">
                                {{ $institution->address ?: 'Address pending...' }}
                            </span>
                        </div>

                        <div class="shrink-0 ml-auto flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full border border-slate-300 dark:border-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-500 group-hover:border-indigo-300 dark:group-hover:border-indigo-600/50 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10 transition-all shadow-sm group-hover:shadow">
                                <flux:icon.chevron-right variant="micro" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </a>
        @empty
        <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-2xl py-16 px-4 text-center transition-all hover:border-slate-300 dark:hover:border-slate-700">
            <div class="mx-auto size-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <flux:icon.building-office-2 class="size-8 text-slate-500 dark:text-slate-500" />
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                No institutions found
            </h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-500 max-w-sm mx-auto">
                We couldn't locate any institutions matching your criteria. Try adjusting your search filters.
            </p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8 px-2 sm:px-4">
        {{ $institutions->links() }}
    </div>
</div>