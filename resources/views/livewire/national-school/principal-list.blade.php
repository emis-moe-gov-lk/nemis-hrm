<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Header Section --}}
    <x-page-header
        title="Principal Directory"
        subtitle="{{ Auth::user()->workplace?->office_name ?? 'Ministry of Education' }}"
        icon="users"
        :count="$employees->total()"
        countLabel="Principals Registered"
        :breadcrumbs="[
            'Teachers' => route('teacher.overview'),
            'Principal Directory' => route('principal.list')
        ]"
    >
        <x-slot:actions>
            <flux:modal.trigger name="search-profile">
                <flux:button variant="subtle" icon="magnifying-glass" class="h-11 bg-white! dark:bg-slate-900! shadow-sm">Search Principal...</flux:button>
            </flux:modal.trigger>

            @can('principal.bulk.upload')
            <flux:button href="{{ route('teacher.bulk.upload') }}" variant="subtle" icon="arrow-up-tray" class="h-11 bg-white! dark:bg-slate-900! shadow-sm">
                Bulk Upload
            </flux:button>
            @endcan

            @can('principal.create')
            <flux:button href="{{ route('principal.create') }}" icon="plus" class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200/50 dark:shadow-none border-none">
                Add Principal
            </flux:button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Main List (Horizontal Cards) --}}
    <div class="space-y-4">
        @forelse($employees as $employee)
        <div class="group relative bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-2xl p-5 hover:shadow-xl hover:border-indigo-200 flex flex-col lg:flex-row lg:items-center gap-6">

            {{-- 1. Identity --}}
            <div class="flex items-center gap-4 w-full lg:w-1/4 lg:min-w-[250px]">
                <div class="relative shrink-0">
                    <img class="h-14 w-14 rounded-full object-cover border-2 border-white dark:border-slate-700 shadow-md"
                        src="{{ $employee->gender_id == 'G02' ? asset('images/profile_f.png') : asset('images/profile_m.png') }}">
                    <span class="absolute -bottom-1 -right-1 block h-4 w-4 rounded-full ring-2 ring-white dark:ring-slate-900 {{ $employee->currentAppointment?->appointment?->is_confirmed ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
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
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Position & Service</p>
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300 truncate">{{ $employee->currentAppointment?->position?->position_name ?? 'N/A' }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $employee->currentAppointment?->service?->service_name ?? '' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Workplace Address</p>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-500 truncate">
                        {{ $employee->currentAppointment?->workplace?->office()?->name }}
                    </p>
                    <p class="text-[11px] text-indigo-400 font-medium">{{ $employee->currentAppointment?->workplace?->office()?->address ?? 'Address not listed' }}</p>
                </div>
            </div>

            {{-- 3. Clickable Contact Info --}}
            <div class="w-full lg:w-1/4 flex flex-col gap-2 lg:gap-1 border-t lg:border-t-0 lg:border-l border-slate-200 dark:border-slate-700 pt-4 lg:pt-0 lg:pl-6">
                <a href="mailto:{{ $employee->email }}" class="flex items-center gap-2 text-sm text-slate-600 hover:text-indigo-600group/link">
                    <flux:icon.envelope variant="micro" class="text-slate-500 group-hover/link:text-indigo-500" />
                    <span class="truncate">{{ $employee->email ?? 'no-email@registry.com' }}</span>
                </a>
                <a href="tel:{{ $employee->phone }}" class="flex items-center gap-2 text-sm text-slate-600 hover:text-blue-600 group/link">
                    <flux:icon.phone variant="micro" class="text-slate-500 group-hover/link:text-blue-500" />
                    <span>{{ $employee->phone ?? 'Not Provided' }}</span>
                </a>
            </div>

            {{-- 4. Final Actions --}}
            <div class="flex items-center gap-2 w-full lg:w-auto lg:ml-auto border-t lg:border-t-0 border-slate-200 dark:border-slate-700 pt-4 lg:pt-0">
                <flux:button href="{{ route('principal.profile.index', $employee->id) }}" size="sm" class="w-full lg:w-auto rounded-xl! bg-indigo-600! dark:bg-white! text-white! dark:text-slate-900! font-bold px-4 hover:opacity-90">View</flux:button>
                <flux:dropdown>
                    <flux:button icon="ellipsis-vertical" size="sm" variant="ghost" />
                    <flux:menu>
                        @can('principal.profile.id.view')
                        <flux:menu.item href="{{ route('teacher.id.pdf', $employee->id) }}" download icon="identification">Print ID</flux:menu.item>
                        @endcan
                        @can('principal.profile.pdf.view')
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
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200">No Principals Found</h3>
            <p class="text-slate-500 dark:text-slate-500 mt-2 max-w-xs text-center font-medium">
                We couldn't find any profiles matching.
            </p>

            {{-- Action --}}
            @can('principal.create')
            <flux:button href="{{ route('principal.create') }}" icon="plus" class="h-11 bg-indigo-600! mt-6 hover:bg-indigo-700! dark:bg-indigo-500! dark:hover:bg-indigo-600! text-white! shadow-lg shadow-indigo-200 dark:shadow-indigo-900/20 border-none">
                Add New Principal
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
            @forelse($results as $principal)
            <a href="{{ route('principal.profile.index', $principal->id) }}" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-indigo-50 dark:hover:bg-indigo-900/10 border border-transparent hover:border-indigo-100 dark:hover:border-indigo-800 transition-all">
                <div class="w-10 h-10 rounded-full bg-indigo-600 dark:bg-indigo-500 text-white flex items-center justify-center font-bold">
                    {{ substr($principal->name_with_initials, 0, 1) }}
                </div>
                <div>
                    <p class="font-bold text-slate-900 dark:text-white leading-tight">{{ $principal->name_with_initials }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-500">{{ $principal->nic }}</p>
                    <p class="text-xs text-indigo-400 dark:text-indigo-300">{{ $principal->currentAppointment?->workplace?->office()?->name }}</p>
                </div>
            </a>
            @empty
            <p class="text-center text-slate-500 dark:text-slate-500 text-sm italic">No results found for "{{ $query }}"</p>
            @endforelse
            @else
            <p class="text-center text-slate-500 dark:text-slate-500 text-sm">Start typing to see results...</p>
            @endif
        </div>
    </flux:modal>
</div>