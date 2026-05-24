<div class="px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="max-w-7xl mx-auto space-y-6">
        {{-- Breadcrumbs --}}
        <nav class="flex" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-4">
                <li>
                    <div>
                        <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-indigo-500 dark:text-zinc-400 dark:hover:text-indigo-400 transition-colors">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Home</span>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="h-5 w-5 shrink-0 text-slate-300 dark:text-zinc-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('teacher.overview') }}" class="ml-4 text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-zinc-400 dark:hover:text-zinc-300 transition-colors">Teacher</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="h-5 w-5 shrink-0 text-slate-300 dark:text-zinc-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-4 text-sm font-extrabold text-indigo-600 dark:text-indigo-400" aria-current="page">Termination & Pension Management</span>
                    </div>
                </li>
            </ol>
        </nav>
        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-8 border-b border-slate-200 dark:border-slate-700">
            <div class="space-y-1">
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Termination & Pension Management
                </h1>
                <p class="text-slate-500 dark:text-slate-500 font-medium">
                    Review and process service termination for <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $services->service_name ?? 'Service' }}</span>
                </p>
            </div>

            <div class="w-full lg:w-96">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search by NIC, Name or Email..." class="rounded-2xl!" />
            </div>
        </div>

        {{-- Employee List --}}
        <div class="grid grid-cols-1 gap-4">
            @forelse($employees as $appointment)
            @php
            $employee = $appointment->employee;
            $current = $appointment->currentAppointment;
            $name = $employee?->name_with_initials ?? $employee?->full_name ?? '—';
            $title = $employee?->title?->title_name ?? '';
            $initials = strtoupper(substr($name, 0, 2));
            $age = $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->age : null;
            $serviceYears = $appointment->first_appointment_date ? (int) \Carbon\Carbon::parse($appointment->first_appointment_date)->diffInYears(now()) : null;

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

            <a href="{{ route('employees.termination-of-service', $employee->people_id) }}" class="group relative overflow-hidden bg-linear-to-br from-indigo-200/40 via-indigo-50/30 to-white dark:from-indigo-950/30 dark:via-zinc-900 dark:to-zinc-900 border border-indigo-100/80 dark:border-indigo-900/40 hover:border-indigo-400 dark:hover:border-indigo-500 rounded-[1.75rem] p-5 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl flex flex-col md:flex-row items-center gap-5">

                {{-- Subtle hover glow --}}
                <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-linear-to-br {{ $rankTheme['avatar'] }} opacity-0 group-hover:opacity-[0.06] transition-opacity duration-500 blur-2xl pointer-events-none"></div>

                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div class="w-14 h-14 rounded-2xl bg-linear-to-br {{ $rankTheme['avatar'] }} flex items-center justify-center text-white font-extrabold text-lg shadow-md transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-2 select-none">
                        {{ $initials }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 block h-4 w-4 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-zinc-900 shadow-sm"></span>
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
                            {{ $employee?->nic ?? '—' }}
                        </span>

                        {{-- Rank --}}
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
                    <p class="text-[12px] text-slate-500 dark:text-zinc-400 font-semibold truncate flex items-center justify-center md:justify-start gap-1.5 mt-1">
                        <svg class="w-3.5 h-3.5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $current?->workplace?->office_name ?? $current?->workplace?->name ?? 'Workplace not assigned' }}
                    </p>
                </div>

                {{-- Age & Service Info --}}
                <div class="hidden md:flex flex-col items-end gap-2 shrink-0 border-l border-slate-300/50 dark:border-zinc-700/50 pl-5 pr-2">
                    <div class="flex gap-4">
                        <div class="text-right">
                            <span class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-500 dark:text-zinc-400 block mb-0.5">
                                Age
                            </span>
                            <span class="text-sm font-extrabold text-slate-700 dark:text-slate-300">
                                {{ $age ?? 'N/A' }} <span class="text-[10px] text-slate-500">Yrs</span>
                            </span>
                        </div>
                        <div class="w-px h-8 bg-slate-200 dark:bg-zinc-700"></div>
                        <div class="text-right">
                            <span class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-500 dark:text-zinc-400 block mb-0.5">
                                Service
                            </span>
                            <span class="text-sm font-extrabold text-slate-700 dark:text-slate-300">
                                {{ $serviceYears ?? 'N/A' }} <span class="text-[10px] text-slate-500">Yrs</span>
                            </span>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $age >= 55 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800' }}">
                        {{ $age >= 55 ? 'Pension Eligible' : 'Resignation' }}
                    </span>
                </div>
            </a>
            @empty
            {{-- Empty State --}}
            <div class="py-20 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-zinc-900/50 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-[2.5rem]">
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full scale-150 blur-2xl opacity-40"></div>
                    <div class="relative p-5 bg-linear-to-b from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-full shadow-inner">
                        <svg class="w-12 h-12 text-slate-500 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No Employees Found</h3>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-1 text-center font-medium">
                    Try adjusting your search or filters.
                </p>
            </div>
            @endforelse

            <div class="mt-8">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>