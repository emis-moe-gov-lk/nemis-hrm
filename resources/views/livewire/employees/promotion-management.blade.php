<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Promotion Management"
        subtitle="Manage career advancement for {{ $services->service_name }}. {{ $employeesList->total() }} eligible employee(s) found."
        icon="briefcase"
        :breadcrumbs="[
            'Teacher' => route('teacher.overview'),
            'Promotions' => '#'
        ]"
    >
        <x-slot:actions>
            <div class="w-full sm:w-80">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search name or NIC..." 
                    icon="magnifying-glass" 
                    clearable
                />
            </div>
        </x-slot:actions>
    </x-page-header>



        {{-- Employee Card List --}}
        <div class="grid grid-cols-1 gap-3">
            @forelse($employeesList as $appointment)
            @php
            $person = $appointment->employee;
            $current = $appointment->currentAppointment;
            $name = $person?->name_with_initials ?? $person?->full_name ?? '—';
            $title = $person?->title?->title_name ?? '';
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
            $rankIdx = abs(crc32($appointment->rank_id ?? '')) % count($rankPalette);
            $rankTheme = $rankPalette[$rankIdx];
            @endphp

            {{-- Vibrant Card Background --}}
            <a href="{{ route('employees.promotion-control', $appointment->employee_id) }}" class="group relative overflow-hidden bg-linear-to-br from-indigo-200/40 via-indigo-50/30 to-white dark:from-indigo-950/30 dark:via-zinc-900 dark:to-zinc-900 border border-indigo-100/80 dark:border-indigo-900/40 hover:border-indigo-400 dark:hover:border-indigo-500 rounded-[1.75rem] p-5 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl flex items-center gap-5">

                {{-- Subtle hover glow --}}
                <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-linear-to-br {{ $rankTheme['avatar'] }} opacity-0 group-hover:opacity-[0.06] transition-opacity duration-500 blur-2xl pointer-events-none"></div>

                {{-- Avatar — colour by rank --}}
                <div class="relative shrink-0">
                    <div class="w-14 h-14 rounded-2xl bg-linear-to-br {{ $rankTheme['avatar'] }} flex items-center justify-center text-white font-extrabold text-lg shadow-md transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-2 select-none">
                        {{ $initials }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-zinc-900 shadow-sm"></span>
                </div>

                {{-- Main Info --}}
                <div class="flex-1 min-w-0 space-y-1.5">
                    <p class="font-extrabold text-slate-900 dark:text-white text-[15px] leading-tight truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                        {{$title}} {{ $name }}
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        {{-- NIC --}}
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-500 border-slate-300 dark:border-zinc-700 tracking-wide">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                            </svg>
                            {{ $person?->nic ?? '—' }}
                        </span>

                        {{-- Rank — same rank_id always maps to same colour via crc32 hash --}}
                        @if($appointment->rank_id)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $rankTheme['badge'] }} tracking-wide">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            {{ $current?->rank?->rank_name ?? $appointment->rank_id }}
                        </span>
                        @endif
                    </div>

                    {{-- Workplace --}}
                    <p class="text-[12px] text-slate-500 dark:text-zinc-400 font-semibold truncate flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $current?->workplace?->office_name ?? $current?->workplace_id ?? 'Workplace not assigned' }}
                    </p>
                </div>

                {{-- Right meta --}}
                <div class="hidden md:flex flex-col items-end gap-2 shrink-0">
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-500 dark:text-zinc-400">
                        {{ $appointment->employee_id }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-500 border-slate-300 dark:border-zinc-700">
                        {{ \Carbon\Carbon::parse($appointment->first_appointment_date)->format('d M Y') }}
                    </span>
                    @if($appointment->service_years)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $appointment->service_years }}
                    </span>
                    @endif
                </div>

                {{-- Arrow --}}
                <div class="ml-1 shrink-0 opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                    <div class="h-8 w-8 rounded-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center shadow-sm">
                        <flux:icon.chevron-right variant="micro" class="text-indigo-500" />
                    </div>
                </div>
            </a>
            @empty
            {{-- Empty State --}}
            <div class="py-20 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-zinc-900/50 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-[2.5rem]">
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full scale-150 blur-2xl opacity-40"></div>
                    <div class="relative p-5 bg-linear-to-b from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-full shadow-inner">
                        <svg class="w-12 h-12 text-slate-500 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No Employees Found</h3>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-1 text-center font-medium">
                    No active employees found for service <span class="font-bold text-indigo-500">{{ $serviceID }}</span>.
                </p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($employeesList->hasPages())
        <div class="pt-4">
            {{ $employeesList->links() }}
        </div>
        @endif

    </div>
</div>