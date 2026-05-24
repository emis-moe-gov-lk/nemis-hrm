<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 min-h-screen">
        <x-page-header
            title="Teachers Directory"
            subtitle="{{ Auth::user()->workplace?->office_name ?? 'Ministry of Education' }}"
            icon="building-office"
            :count="$employees->total()"
            countLabel="Profiles Registered"
            :breadcrumbs="[
                'Teacher' => route('teacher.overview'),
                'Teachers Directory' => route('teacher.list')
            ]">
            <x-slot:actions>
                @can('teacher.bulk.upload')
                <flux:button href="{{ route('teacher.bulk.upload') }}" variant="subtle" icon="arrow-up-tray"
                    class="h-11 bg-white! dark:bg-slate-900! shadow-sm">
                    Bulk Upload
                </flux:button>
                @endcan

                @can('teacher.create')
                <flux:button href="{{ route('teacher.create') }}" icon="plus"
                    class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200/50 dark:shadow-none border-none">
                    Add Teacher
                </flux:button>
                @endcan
            </x-slot:actions>
        </x-page-header>

        {{-- Filter & Search Toolbar --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 bg-white dark:bg-zinc-900/50 border border-slate-300 dark:border-zinc-700 rounded-2xl shadow-sm mb-8">

            {{-- Flow Map / Hierarchical Filters --}}
            <div class="flex items-center gap-3 overflow-x-auto pb-2 lg:pb-0 hide-scrollbar w-full lg:w-auto">
                <div class="flex items-center gap-2 text-xs font-black text-slate-500 dark:text-slate-500 uppercase tracking-widest shrink-0 ml-1">
                    <flux:icon.funnel variant="mini" class="w-4 h-4" /> Filter By
                </div>

                <div class="flex py-2 items-center gap-2 shrink-0">
                    {{-- Province --}}
                    <div class="w-40 shrink-0">
                        <flux:select wire:model.live="selectedProvince" class="w-full h-10 shadow-none! border-slate-300 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800/50" placeholder="All Provinces" :disabled="$isProvinceLocked">
                            <flux:select.option value="">All Provinces</flux:select.option>
                            @foreach ($provinceOption as $prov)
                            <flux:select.option value="{{ $prov->workplace_id }}">{{ $prov->short_name ?? $prov->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="shrink-0 text-slate-300 dark:text-zinc-700">
                        <flux:icon.chevron-right variant="mini" class="w-4 h-4" />
                    </div>

                    {{-- Zone --}}
                    <div class="w-40 shrink-0">
                        <flux:select wire:model.live="selectedZone" class="w-full h-10 shadow-none! border-slate-300 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800/50" :disabled="$isZoneLocked || empty($selectedProvince)" placeholder="All Zones">
                            <flux:select.option value="">All Zones</flux:select.option>
                            @foreach ($zonalOption as $zone)
                            <flux:select.option value="{{ $zone->workplace_id }}">{{ $zone->short_name ?? $zone->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="shrink-0 text-slate-300 dark:text-zinc-700">
                        <flux:icon.chevron-right variant="mini" class="w-4 h-4" />
                    </div>

                    {{-- Division --}}
                    <div class="w-40 shrink-0">
                        <flux:select wire:model.live="selectedDivision" class="w-full h-10 shadow-none! border-slate-300 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800/50" :disabled="$isDivisionLocked || empty($selectedZone)" placeholder="All Divisions">
                            <flux:select.option value="">All Divisions</flux:select.option>
                            @foreach ($divisionOption as $div)
                            <flux:select.option value="{{ $div->workplace_id }}">{{ $div->short_name ?? $div->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="shrink-0 text-slate-300 dark:text-zinc-700">
                        <flux:icon.chevron-right variant="mini" class="w-4 h-4" />
                    </div>

                    {{-- Institution --}}
                    <div class="w-48 shrink-0">
                        <flux:select wire:model.live="selectedInstitution" class="w-full h-10 shadow-none! border-slate-300 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800/50" :disabled="$isInstitutionLocked || empty($selectedDivision)" placeholder="All Institutions">
                            <flux:select.option value="">All Institutions</flux:select.option>
                            @foreach ($institutionOption as $inst)
                            <flux:select.option value="{{ $inst->workplace_id }}">{{ $inst->short_name ?? $inst->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </div>

            {{-- Interactive Search Trigger --}}
            <div class="shrink-0 border-t lg:border-t-0 lg:border-l border-slate-200 dark:border-zinc-700 pt-3 lg:pt-0 lg:pl-4 mt-1 lg:mt-0 flex justify-end w-full lg:w-auto">
                <flux:modal.trigger name="search-profile">
                    <flux:button variant="filled" icon="magnifying-glass" class="h-10 w-10! px-0! bg-slate-50! hover:bg-indigo-50! dark:bg-zinc-800! text-slate-500! hover:text-indigo-600! dark:text-slate-500! dark:hover:text-indigo-400! border border-slate-300 dark:border-zinc-700 shadow-sm transition-all" />
                </flux:modal.trigger>
            </div>
        </div>

        {{-- Main List (Horizontal Cards) --}}
        <div class="space-y-4">
            @forelse($employees as $employee)
            @php
            $current = $employee->currentAppointment;
            $name = $employee?->name_with_initials ?? $employee?->full_name ?? '—';
            $title = $employee?->title?->title_name ?? '';
            $initials = strtoupper(substr($name, 0, 2));

            // Deterministic colour from rank_id — same rank always = same colour, no DB needed
            $rankPalette = [
            ['badge' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-300 dark:border-slate-600', 'avatar' => 'from-slate-500 to-slate-600'],
            ['badge' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-700', 'avatar' => 'from-blue-500 to-indigo-600'],
            ['badge' => 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-700', 'avatar' => 'from-cyan-500 to-sky-600'],
            ['badge' => 'bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-700', 'avatar' => 'from-teal-500 to-emerald-600'],
            ['badge' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700', 'avatar' => 'from-emerald-500 to-teal-600'],
            ['badge' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-700', 'avatar' => 'from-amber-500 to-orange-600'],
            ['badge' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-orange-700', 'avatar' => 'from-orange-500 to-rose-600'],
            ['badge' => 'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-700', 'avatar' => 'from-rose-500 to-pink-600'],
            ['badge' => 'bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-700', 'avatar' => 'from-violet-500 to-purple-600'],
            ];
            $rankIdx = abs(crc32($current->rank_id ?? '')) % count($rankPalette);
            $rankTheme = $rankPalette[$rankIdx];
            @endphp

            <div class="group relative overflow-hidden bg-linear-to-br from-indigo-200/40 via-indigo-50/30 to-white dark:from-indigo-950/30 dark:via-zinc-900 dark:to-zinc-900 border border-indigo-100/80 dark:border-indigo-900/40 hover:border-indigo-400 dark:hover:border-indigo-500 rounded-[1.75rem] p-5 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl flex flex-col md:flex-row items-center gap-5">

                {{-- Subtle hover glow --}}
                <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-linear-to-br {{ $rankTheme['avatar'] }} opacity-0 group-hover:opacity-[0.06] transition-opacity duration-500 blur-2xl pointer-events-none"></div>

                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div class="w-14 h-14 rounded-2xl bg-linear-to-br {{ $rankTheme['avatar'] }} flex items-center justify-center text-white font-extrabold text-lg shadow-md transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-2 select-none">
                        {{ $initials }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 block h-4 w-4 rounded-full {{ $current?->appointment?->is_confirmed ? 'bg-emerald-400' : 'bg-amber-400' }} ring-2 ring-white dark:ring-zinc-900 shadow-sm"></span>
                </div>

                {{-- Main Info --}}
                <div class="flex-1 min-w-0 space-y-1.5 text-center md:text-left">
                    <p class="font-extrabold text-slate-900 dark:text-white text-[15px] leading-tight truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                        {{$title}} {{ $name }}
                    </p>

                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                        {{-- NIC --}}
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-500 border-slate-300 dark:border-zinc-700 tracking-wide">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                            </svg>
                            {{ $employee->nic ?? '—' }}
                        </span>

                        {{-- Rank --}}
                        @if($current?->rank_id)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $rankTheme['badge'] }} tracking-wide">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            {{ $current->rank?->rank_name ?? $current->rank_id }}
                        </span>
                        @endif

                        {{-- Position --}}
                        @if($current?->position_id)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800 tracking-wide">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $current->position?->position_name ?? 'Position' }}
                        </span>
                        @endif
                    </div>

                    {{-- Workplace --}}
                    <p class="text-[12px] text-slate-500 dark:text-zinc-400 font-semibold truncate flex items-center justify-center md:justify-start gap-1.5 mt-2">
                        <svg class="w-3.5 h-3.5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        {{ $current?->workplace?->office_name ?? 'Workplace not assigned' }}
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 truncate flex items-center justify-center md:justify-start gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0 text-slate-500 opacity-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                        {{ $current?->workplace?->office()?->address ?? 'Address not listed' }}
                    </p>
                </div>

                {{-- Clickable Contact Info --}}
                <div class="w-full md:w-auto flex flex-col items-center md:items-start gap-2 md:gap-1 md:border-l border-indigo-100 dark:border-zinc-700 pt-4 md:pt-0 md:pl-5 pr-2">
                    <a href="mailto:{{ $employee->email }}"
                        class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 group/link transition-colors">
                        <flux:icon.envelope variant="micro" class="text-slate-500 group-hover/link:text-indigo-500" />
                        <span class="truncate">{{ $employee->email ?? 'no-email@registry.com' }}</span>
                    </a>
                    <a href="tel:{{ $employee->phone }}"
                        class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 group/link transition-colors">
                        <flux:icon.phone variant="micro" class="text-slate-500 group-hover/link:text-blue-500" />
                        <span>{{ $employee->phone ?? 'Not Provided' }}</span>
                    </a>
                </div>

                {{-- Final Actions --}}
                <div class="flex items-center justify-center md:justify-end gap-2 w-full md:w-auto border-t md:border-t-0 border-indigo-100 dark:border-zinc-700 pt-4 md:pt-0">
                    <flux:button href="{{ route('teacher.profile.index', $employee->id) }}" size="sm" class="w-full md:w-auto rounded-xl! bg-indigo-600! hover:bg-indigo-700! text-white! border-none font-bold px-4 hover:shadow-lg hover:shadow-indigo-200/50 dark:hover:shadow-none transition-all hover:-translate-y-0.5">View Profile</flux:button>
                    <flux:dropdown>
                        <flux:button icon="ellipsis-vertical" size="sm" variant="ghost" class="rounded-xl!" />
                        <flux:menu>
                            @can('teacher.profile.id.view')
                            <flux:menu.item href="{{ route('teacher.id.pdf', $employee->id) }}" download icon="identification">Print ID</flux:menu.item>
                            @endcan
                            @can('teacher.profile.pdf.view')
                            <flux:menu.item href="{{ route('teacher.profile.pdf', $employee->id) }}" download icon="document-text">Export PDF</flux:menu.item>
                            @endcan
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
            @empty
            <div class="py-20 flex flex-col items-center justify-center bg-white dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-[3rem]">
                {{-- Animated SVG Icon Container --}}
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full scale-150 blur-2xl opacity-50"></div>
                    <div class="relative p-6 bg-linear-to-b from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-full shadow-inner">
                        <svg class="w-16 h-16 text-slate-500 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Text Content --}}
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200">No Teachers Found</h3>
                <p class="text-slate-500 dark:text-slate-500 mt-2 max-w-xs text-center font-medium">
                    We couldn't find any profiles matching.
                </p>

                {{-- Action --}}
                @can('teacher.create')
                <flux:button href="{{ route('teacher.create') }}" icon="plus" class="h-11 bg-indigo-600! mt-6 hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200 border-none">
                    Add New Teacher
                </flux:button>
                @endcan
            </div>
            @endforelse
        </div>

        <div class="mt-10">{{ $employees->links() }}</div>

        {{-- Search Flyout --}}
        <flux:modal name="search-profile" variant="flyout" class="space-y-6">
            <flux:heading size="lg" class="flex items-center gap-2">
                <flux:icon.magnifying-glass variant="mini" /> Search Registry
            </flux:heading>

            {{-- Use .live to trigger search on every keystroke --}}
            <flux:input
                wire:model.live.debounce.300ms="query"
                placeholder="Type name or NIC..."
                class="rounded-xl! shadow-sm"
                clearable />

            <div class="space-y-2 mt-4">
                @if(!empty($query))
                @forelse($results as $teacher)
                <a href="{{ route('teacher.profile.index', $teacher->id) }}"
                    class="flex items-center gap-4 p-4 rounded-2xl hover:bg-indigo-50 dark:hover:bg-indigo-900/10 border border-transparent hover:border-indigo-100">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                        {{ substr($teacher->name_with_initials, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white leading-tight">{{ $teacher->name_with_initials }}
                        </p>
                        <p class="text-xs text-slate-500">{{ $teacher->nic }}</p>
                        <p class="text-xs text-indigo-400">{{ $teacher->currentAppointment?->workplace?->office()?->name }}
                        </p>
                    </div>
                </a>
                @empty
                <p class="text-center text-slate-500 text-sm italic">No results found for "{{ $query }}"</p>
                @endforelse
                @else
                <p class="text-center text-slate-500 text-sm">Start typing to see results...</p>
                @endif
            </div>
        </flux:modal>
</div>