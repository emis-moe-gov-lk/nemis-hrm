<div class="p-4 md:p-8 max-w-8xl mx-left min-h-screen">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <flux:heading size="xl" class="text-3xl font-extrabold tracking-tight bg-clip-text text-transparent bg-linear-to-r from-indigo-600 to-blue-500">
                Teachers Directory
            </flux:heading>
            <div class="flex flex-col gap-1 mt-1">
                <p class="text-slate-500 text-sm flex items-center gap-2 font-medium">
                    <span class="p-1 bg-indigo-100 dark:bg-indigo-900/30 rounded text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                    {{ Auth::user()->workplace?->office_name ?? 'All Workplaces' }}
                    <span class="text-slate-300 mx-1">|</span>
                    <span class="text-indigo-600 font-bold">{{ $employees->total() }} Profiles Registered</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Unified Button Group --}}
            @can('teacher.bulk.upload')
            <flux:button href="{{ route('teacher.bulk.upload') }}" variant="subtle" icon="arrow-up-tray"
                class="h-11 bg-white! dark:bg-slate-900! shadow-sm">
                Bulk Upload
            </flux:button>
            @endcan

            @can('teacher.create')
            <flux:button href="{{ route('teacher.create') }}" icon="plus"
                class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200 border-none">
                Add Teacher
            </flux:button>
            @endcan
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-8 p-3 sm:p-4 bg-slate-50 dark:bg-slate-800/20 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">

        {{-- Flow Map / Hierarchical Filters --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full xl:w-auto">
            <div class="flex items-center gap-2 text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest shrink-0 ml-1">
                <flux:icon.funnel variant="mini" class="w-4 h-4" /> Filter By
            </div>

            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full">
                {{-- Province --}}
                <div class="w-[calc(50%-0.5rem)] sm:w-40 min-w-[140px] shrink-0">
                    <flux:select wire:model.live="selectedProvince" class="w-full h-10 shadow-none! border-slate-300 dark:border-slate-700" placeholder="All Provinces" :disabled="$isProvinceLocked">
                        <flux:select.option value="">All Provinces</flux:select.option>
                        @foreach ($provinceOption as $prov)
                        <flux:select.option value="{{ $prov->workplace_id }}">{{ $prov->short_name ?? $prov->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="hidden sm:block shrink-0 text-slate-300 dark:text-slate-600">
                    <flux:icon.chevron-right variant="mini" class="w-4 h-4" />
                </div>

                {{-- Zone --}}
                <div class="w-[calc(50%-0.5rem)] sm:w-40 min-w-[140px] shrink-0">
                    <flux:select wire:model.live="selectedZone" class="w-full h-10 shadow-none! border-slate-300 dark:border-slate-700" :disabled="$isZoneLocked || empty($selectedProvince)" placeholder="All Zones">
                        <flux:select.option value="">All Zones</flux:select.option>
                        @foreach ($zonalOption as $zone)
                        <flux:select.option value="{{ $zone->workplace_id }}">{{ $zone->short_name ?? $zone->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="hidden sm:block shrink-0 text-slate-300 dark:text-slate-600">
                    <flux:icon.chevron-right variant="mini" class="w-4 h-4" />
                </div>

                {{-- Division --}}
                <div class="w-[calc(50%-0.5rem)] sm:w-40 min-w-[140px] shrink-0">
                    <flux:select wire:model.live="selectedDivision" class="w-full h-10 shadow-none! border-slate-300 dark:border-slate-700" :disabled="$isDivisionLocked || empty($selectedZone)" placeholder="All Divisions">
                        <flux:select.option value="">All Divisions</flux:select.option>
                        @foreach ($divisionOption as $div)
                        <flux:select.option value="{{ $div->workplace_id }}">{{ $div->short_name ?? $div->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="hidden sm:block shrink-0 text-slate-300 dark:text-slate-600">
                    <flux:icon.chevron-right variant="mini" class="w-4 h-4" />
                </div>

                {{-- Institution --}}
                <div class="w-[calc(50%-0.5rem)] sm:flex-1 min-w-[140px]">
                    <flux:select wire:model.live="selectedInstitution" class="w-full h-10 shadow-none! border-slate-300 dark:border-slate-700" :disabled="$isInstitutionLocked || empty($selectedDivision)" placeholder="All Institutions">
                        <flux:select.option value="">All Institutions</flux:select.option>
                        @foreach ($institutionOption as $inst)
                        <flux:select.option value="{{ $inst->workplace_id }}">{{ $inst->short_name ?? $inst->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
        </div>

        {{-- Interactive Search Trigger --}}
        <div class="w-full xl:w-auto xl:ml-auto">
            <flux:modal.trigger name="search-profile">
                <flux:button variant="filled" icon="magnifying-glass" class="w-full sm:w-auto h-10 bg-white! dark:bg-slate-900! text-slate-700! dark:text-slate-300! border border-slate-200 dark:border-slate-700 shadow-sm hover:border-indigo-300! transition-all group">
                    <span class="group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Search Teachers...</span>
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    {{-- Main List (Horizontal Cards) --}}
    <div class="space-y-4">
        @forelse($employees as $employee)
        <div
            class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 hover:shadow-xl hover:border-indigo-200 flex flex-col lg:flex-row lg:items-center gap-6">

            {{-- 1. Identity --}}
            <div class="flex items-center gap-4 lg:w-1/4 min-w-[250px]">
                <div class="relative shrink-0">
                    <img class="h-14 w-14 rounded-full object-cover border-2 border-white dark:border-slate-800 shadow-md"
                        src="{{ $employee->gender_id == 'G02' ? asset('images/profile_f.png') : asset('images/profile_m.png') }}">
                    <span
                        class="absolute -bottom-1 -right-1 block h-4 w-4 rounded-full ring-2 ring-white dark:ring-slate-900 {{ $employee->currentAppointment?->appointment?->is_confirmed ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                </div>
                <div class="overflow-hidden">
                    <h3 class="font-bold text-slate-900 dark:text-white truncate text-lg">
                        {{ $employee->name_with_initials }}
                    </h3>
                    <p class="text-xs font-bold text-indigo-500 uppercase">{{ $employee->nic }}</p>
                </div>
            </div>

            {{-- 2. Professional Info & Workplace Address --}}
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Position & Service</p>
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300 truncate">
                        {{ $employee->currentAppointment?->position?->position_name ?? 'N/A' }}
                    </p>
                    <p class="text-xs text-slate-500 truncate">
                        {{ $employee->currentAppointment?->service?->service_name ?? '' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Workplace Address</p>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-400 truncate">
                        {{ $employee->currentAppointment?->workplace?->office()?->name }}
                    </p>
                    <p class="text-[11px] text-indigo-400 font-medium">
                        {{ $employee->currentAppointment?->workplace?->office()?->address ?? 'Address not listed' }}
                    </p>
                </div>
            </div>

            {{-- 3. Clickable Contact Info --}}
            <div class="lg:w-1/4 flex flex-col gap-1 border-l border-slate-100 dark:border-slate-800 pl-6">
                <a href="mailto:{{ $employee->email }}"
                    class="flex items-center gap-2 text-sm text-slate-600 hover:text-indigo-600 group/link">
                    <flux:icon.envelope variant="micro" class="text-slate-400 group-hover/link:text-indigo-500" />
                    <span class="truncate">{{ $employee->email ?? 'no-email@registry.com' }}</span>
                </a>
                <a href="tel:{{ $employee->phone }}"
                    class="flex items-center gap-2 text-sm text-slate-600 hover:text-blue-600 group/link">
                    <flux:icon.phone variant="micro" class="text-slate-400 group-hover/link:text-blue-500" />
                    <span>{{ $employee->phone ?? 'Not Provided' }}</span>
                </a>
            </div>

            {{-- 4. Final Actions --}}
            <div class="flex items-center gap-2 lg:ml-auto">
                <flux:button href="{{ route('teacher.profile.index', $employee->id) }}" size="sm" class="rounded-xl! bg-slate-900! text-white! font-bold px-4">View</flux:button>
                <flux:dropdown>
                    <flux:button icon="ellipsis-vertical" size="sm" variant="ghost" />
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
        <div class="py-20 flex flex-col items-center justify-center bg-white dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-[3rem]">
            {{-- Animated SVG Icon Container --}}
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full scale-150 blur-2xl opacity-50"></div>
                <div class="relative p-6 bg-linear-to-b from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-full shadow-inner">
                    <svg class="w-16 h-16 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                        <circle cx="9" cy="7" r="4" stroke-width="1.5"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" class="text-indigo-500 opacity-40"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l5 5" class="text-indigo-600"></path>
                    </svg>
                </div>
            </div>

            {{-- Text Content --}}
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200">No Teachers Found</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-xs text-center font-medium">
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
            <p class="text-center text-slate-400 text-sm italic">No results found for "{{ $query }}"</p>
            @endforelse
            @else
            <p class="text-center text-slate-400 text-sm">Start typing to see results...</p>
            @endif
        </div>
    </flux:modal>
</div>