<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Page Header --}}
    <x-page-header
        title="Changing Workplace"
        subtitle="Search for an employee and reassign them to a new workplace within your jurisdiction."
        icon="arrows-right-left"
        :breadcrumbs="[
            'Home'    => route('teacher.overview'),
            'Changing Workplace' => '#'
        ]">
        <x-slot:actions>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <flux:button x-on:click="$dispatch('modal-show', { name: 'release-employee-modal' })" wire:click="openReleaseModal" variant="filled" icon="user-minus" class="w-full sm:w-auto">Release Employee</flux:button>
                <div class="w-full sm:w-80">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by ID, NIC, phone..."
                        icon="magnifying-glass"
                        clearable />
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 animate-in fade-in duration-300">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900">
                <flux:icon name="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <p class="text-emerald-800 dark:text-emerald-200 font-bold text-sm">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 animate-in fade-in duration-300">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900">
                <flux:icon name="x-circle" class="w-5 h-5 text-red-600 dark:text-red-400" />
            </div>
            <p class="text-red-800 dark:text-red-200 font-bold text-sm">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Employee List --}}
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest flex items-center gap-2">
            Eligible Employees
            @if($employees->total() > 0)
            <span class="inline-flex items-center justify-center bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 rounded-full px-2 py-0.5 text-[10px] font-bold">
                {{ number_format($employees->total()) }}
            </span>
            @endif
        </h3>
    </div>
    <div class="grid grid-cols-1 gap-3">
        @forelse($employees as $person)
        @php
        $current = $person->currentAppointment;
        $name = $person->name_with_initials ?? $person->full_name ?? '—';
        $title = $person->title?->title_name ?? '';
        $initials = strtoupper(substr($name, 0, 2));

        $colorPalette = [
        ['avatar' => 'from-indigo-500 to-violet-600', 'badge' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-700'],
        ['avatar' => 'from-emerald-500 to-teal-600', 'badge' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700'],
        ['avatar' => 'from-amber-500 to-orange-600', 'badge' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-700'],
        ['avatar' => 'from-rose-500 to-pink-600', 'badge' => 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-700'],
        ['avatar' => 'from-sky-500 to-blue-600', 'badge' => 'bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-700'],
        ];
        $theme = $colorPalette[abs(crc32($person->people_id)) % count($colorPalette)];
        @endphp

        <div class="group relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700/60 hover:border-indigo-300 dark:hover:border-indigo-600 rounded-[1.75rem] p-5 transition-all duration-300 hover:shadow-lg flex items-center gap-5">

            {{-- Hover glow --}}
            <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-linear-to-br {{ $theme['avatar'] }} opacity-0 group-hover:opacity-[0.05] transition-opacity duration-500 blur-2xl pointer-events-none"></div>

            {{-- Avatar --}}
            <div class="relative shrink-0">
                <div class="w-14 h-14 rounded-2xl bg-linear-to-br {{ $theme['avatar'] }} flex items-center justify-center text-white font-extrabold text-lg shadow-md group-hover:scale-105 group-hover:rotate-1 transition-transform duration-300 select-none">
                    {{ $initials }}
                </div>
                <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-zinc-900"></span>
            </div>

            {{-- Main Info --}}
            <div class="flex-1 min-w-0 space-y-1.5">
                <p class="font-extrabold text-slate-900 dark:text-white text-[15px] leading-tight truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $title }} {{ $name }}
                </p>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Employee ID --}}
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-zinc-700">
                        <flux:icon name="identification" class="w-3 h-3" />
                        {{ $person->nic }}
                    </span>

                    {{-- Rank badge --}}
                    @if($current?->rank)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $theme['badge'] }}">
                        <flux:icon name="star" class="w-3 h-3" />
                        {{ $current->rank->rank_name }}
                    </span>
                    @endif

                    {{-- Service badge --}}
                    @if($current?->appointment?->service_id)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-700">
                        {{ $current->appointment->service_id }}
                    </span>
                    @endif
                </div>

                {{-- Current Workplace --}}
                <p class="text-[12px] text-slate-500 dark:text-zinc-400 font-semibold truncate flex items-center gap-1.5">
                    <flux:icon name="building-office-2" class="w-3.5 h-3.5 shrink-0" />
                    {{ $current?->workplace?->office_name ?? 'Workplace not assigned' }}
                </p>
            </div>

            {{-- Change Workplace Button --}}
            <div class="shrink-0">
                <a
                    href="{{ route('employees.changing-workplace.form', $person->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                    <flux:icon name="arrows-right-left" variant="micro" class="w-4 h-4" />
                    Change
                </a>
            </div>
        </div>
        @empty

        {{-- Empty State --}}
        <div class="py-20 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-zinc-900/50 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-[2.5rem]">
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full scale-150 blur-2xl opacity-40"></div>
                <div class="relative p-5 bg-linear-to-b from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-full shadow-inner">
                    <flux:icon name="users" class="w-12 h-12 text-slate-400 dark:text-slate-500" />
                </div>
            </div>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No Employees Found</h3>
            <p class="text-xs text-slate-500 dark:text-slate-500 mt-1 text-center font-medium max-w-xs">
                No active employees match your search within your assigned jurisdiction.
            </p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($employees->hasPages())
    <div class="pt-4">
        {{ $employees->links() }}
    </div>
    @endif

    {{-- Release to Pool Modal --}}
    <flux:modal name="release-employee-modal" class="md:w-[500px]" variant="flyout">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Release Employee to Pool</flux:heading>
                <flux:subheading>Search for an employee by NIC to release them from their current workplace.</flux:subheading>
            </div>

            {{-- Search Bar --}}
            <form wire:submit.prevent="findEmployeeToRelease" class="flex items-end gap-3">
                <div class="flex-1">
                    <flux:input wire:model="releaseSearchNic" label="Employee NIC" placeholder="Enter Employee NIC..." />
                </div>
                <flux:button type="submit" variant="primary">Find</flux:button>
            </form>

            {{-- Error Message --}}
            @if($releaseErrorMessage)
            <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900">
                <div class="flex items-center gap-3">
                    <flux:icon name="exclamation-circle" class="w-5 h-5 text-red-600 dark:text-red-400" />
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ $releaseErrorMessage }}</p>
                </div>
            </div>
            @endif

            {{-- Employee Card Info --}}
            @if($employeeToRelease)
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold shrink-0 text-lg">
                        {{ strtoupper(substr($employeeToRelease->name_with_initials ?? $employeeToRelease->full_name ?? 'A', 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-zinc-100 text-lg">
                            {{ $employeeToRelease->title?->title_name }} {{ $employeeToRelease->name_with_initials ?? $employeeToRelease->full_name }}
                        </h4>
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-zinc-400 mt-1">
                            <span class="inline-flex items-center gap-1 bg-white dark:bg-zinc-800 px-2.5 py-0.5 rounded-md border border-slate-200 dark:border-zinc-700 font-medium">
                                <flux:icon name="identification" class="w-4 h-4 text-slate-400" />
                                {{ $employeeToRelease->nic }}
                            </span>
                            @if($employeeToRelease->phone)
                            <span class="inline-flex items-center gap-1 font-medium">
                                <flux:icon name="phone" class="w-4 h-4 text-slate-400" />
                                {{ $employeeToRelease->phone }}
                            </span>
                            @endif
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-zinc-700/50">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 block mb-1">Current Workplace</span>
                            <div class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-zinc-300">
                                <flux:icon name="building-office-2" class="w-4 h-4 text-indigo-500" />
                                {{ $employeeToRelease->currentAppointment?->workplace?->office_name ?? 'Unknown Workplace' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="confirmReleaseToPool" variant="danger" icon="arrow-right-start-on-rectangle">Confirm Release</flux:button>
                </div>
            </div>
            @endif
        </div>
    </flux:modal>
</div>