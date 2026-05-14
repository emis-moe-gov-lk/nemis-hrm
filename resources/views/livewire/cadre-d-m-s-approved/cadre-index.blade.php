<div class="space-y-6">
    {{-- Header Section --}}
    <header class="flex flex-col gap-1">
        <flux:heading size="xl" level="1" class="text-2xl! font-black tracking-tight text-zinc-800 dark:text-zinc-100 uppercase">{{ __('DMS Approved Cadre') }}</flux:heading>
        <flux:subheading size="lg" class="text-zinc-500 dark:text-zinc-400">{{ __('Manage and filter institution cadre details') }}</flux:subheading>
    </header>

    <flux:separator variant="subtle" class="dark:bg-zinc-800" />

    @if (! $activeCircular)
    <section class="rounded-2xl border border-amber-300/40 bg-amber-50 p-4 text-amber-900 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-100">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 rounded-xl bg-amber-100 p-2 text-amber-700 dark:bg-amber-500/20 dark:text-amber-200">
                <flux:icon.exclamation-triangle class="h-5 w-5" />
            </div>
            <div class="space-y-1">
                <h2 class="text-sm font-black uppercase tracking-widest">{{ __('No Active DMS Circular') }}</h2>
                <p class="text-sm leading-6 text-amber-800/80 dark:text-amber-100/80">
                    {{ __('This page is available to you, but no active DMS approved-cadre circular exists yet. Create or activate a cadre circular to start listing approved cadre details.') }}
                </p>
            </div>
        </div>
    </section>
    @endif

    {{-- Filter Section --}}
    <section class="bg-zinc-50/50 dark:bg-zinc-900/50 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <flux:select wire:model.live="authority" label="Authority">
                <flux:select.option value="">All authorities</flux:select.option>
                @foreach ($authorityOption as $authority)
                <flux:select.option value="{{ $authority->authority_id }}">{{ $authority->authority_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="province" label="Province">
                <flux:select.option value="">All provinces</flux:select.option>
                @foreach ($provinceOption as $prov)
                <flux:select.option value="{{ $prov->workplace_id }}">{{ $prov->short_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="zone" label="Zone" :disabled="empty($zoneOption)">
                <flux:select.option value="">All zonal offices</flux:select.option>
                @foreach ($zoneOption as $zone)
                <flux:select.option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="division" label="Division" :disabled="empty($divisionOption)">
                <flux:select.option value="">All divisional offices</flux:select.option>
                @foreach ($divisionOption as $division)
                <flux:select.option value="{{ $division->workplace_id }}">{{ $division->short_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="status" label="Status">
                <flux:select.option value="">Any status</flux:select.option>
                @foreach ($statusOption as $status)
                <flux:select.option value="{{ $status->id }}">{{ $status->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input
                wire:model.live.debounce.400ms="query"
                label="Search"
                placeholder="Name or Census..."
                icon="magnifying-glass" />
        </div>
    </section>

    {{-- Results Info --}}
    <div class="flex items-center justify-between px-2">
        <span class="text-xs font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
            {{ __('Total Institutions') }}: <span class="text-zinc-900 dark:text-white">{{ $institutions->total() }}</span>
        </span>
    </div>

    {{-- Institutions List --}}
    <div class="space-y-3">
        @forelse ($institutions as $key => $institution)
        <a href="{{ route('cadre-dms-approved.view', $institution->id) }}" class="block group">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 transition-all duration-300 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700/50">
                <div class="flex flex-col lg:flex-row gap-4">

                    {{-- Left Side: Identity --}}
                    <div class="flex items-start gap-4 flex-1">
                        <div class="relative">
                            <div class="h-12 w-12 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden bg-zinc-50 dark:bg-zinc-800 shadow-sm transition-transform duration-300 group-hover:scale-105">
                                <img src="{{ asset('storage/images/institution/'. $institution->logo) }}"
                                    alt=""
                                    class="w-full h-full object-cover" />
                            </div>
                            <span class="absolute -top-2 -left-2 h-5 w-5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black flex items-center justify-center rounded-full border border-white dark:border-zinc-900 shadow-sm text-zinc-600 dark:text-zinc-300">
                                {{ $institutions->firstItem() + $key }}
                            </span>
                        </div>

                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $institution->name }}
                                </h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $institution->active_status ? 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-500/20' : 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-500/20' }}">
                                    {{ $institution->active_status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                                <span class="flex items-center gap-1">
                                    <flux:icon.identification variant="mini" class="opacity-50" />
                                    {{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <flux:icon.phone variant="mini" class="opacity-50" />
                                    {{ $institution->phone ?: 'No Contact' }}
                                </span>
                                <span class="hidden sm:flex items-center gap-1 truncate">
                                    <flux:icon.map-pin variant="mini" class="opacity-50" />
                                    {{ $institution->address }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Cadre Stats --}}
                    <div class="grid grid-cols-3 lg:flex items-center gap-2 lg:gap-3">
                        <div class="flex flex-col items-center lg:items-end justify-center px-3 py-2 bg-blue-50/50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-900/30 transition-colors group-hover:border-blue-200 dark:group-hover:border-blue-800/50 group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20">
                            <span class="text-[9px] uppercase font-black text-blue-500 dark:text-blue-500/80 tracking-widest">{{ __('Teacher') }}</span>
                            <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ $institution->approved_teacher_cadre ?? 0 }}</span>
                        </div>
                        <div class="flex flex-col items-center lg:items-end justify-center px-3 py-2 bg-purple-50/50 dark:bg-purple-900/10 rounded-xl border border-purple-100 dark:border-purple-900/30 transition-colors group-hover:border-purple-200 dark:group-hover:border-purple-800/50 group-hover:bg-purple-50 dark:group-hover:bg-purple-900/20">
                            <span class="text-[9px] uppercase font-black text-purple-500 dark:text-purple-500/80 tracking-widest">{{ __('Principal') }}</span>
                            <span class="text-sm font-bold text-purple-700 dark:text-purple-400">{{ $institution->approved_non_principal_cadre ?? 0 }}</span>
                        </div>
                        <div class="flex flex-col items-center lg:items-end justify-center px-3 py-2 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-900/30 transition-colors group-hover:border-emerald-200 dark:group-hover:border-emerald-800/50 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20">
                            <span class="text-[9px] uppercase font-black text-emerald-500 dark:text-emerald-500/80 tracking-widest">{{ __('Other') }}</span>
                            <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ $institution->approved_other_cadre ?? 0 }}</span>
                        </div>
                        <div class="hidden lg:block pl-2">
                            <flux:icon.chevron-right class="text-zinc-300 dark:text-zinc-600 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors group-hover:translate-x-0.5 duration-300 transform" />
                        </div>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="flex flex-col items-center justify-center p-12 bg-white dark:bg-zinc-900/50 border border-dashed border-zinc-300 dark:border-zinc-800 rounded-2xl">
            <flux:icon.magnifying-glass class="h-10 w-10 text-zinc-300 dark:text-zinc-600 mb-4" />
            <h3 class="text-zinc-900 dark:text-white font-bold">{{ __('No institutions found') }}</h3>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Try adjusting your filters or search query.') }}</p>
        </div>
        @endforelse
    </div>

    <div class="py-4">
        {{ $institutions->links() }}
    </div>
</div>
