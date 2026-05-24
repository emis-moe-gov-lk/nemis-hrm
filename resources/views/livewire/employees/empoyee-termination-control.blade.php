<div class="px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="max-w-7xl mx-auto space-y-6">
        {{-- Flash Messages --}}
        @if(session('message'))
        <div class="p-4 mb-4 rounded-2xl bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 animate-in fade-in duration-500">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="text-green-800 dark:text-green-200 font-bold">{{ session('message') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 mb-4 rounded-2xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 animate-in fade-in duration-500">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="text-red-800 dark:text-red-200 font-bold">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <div>
            {{-- Header --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-8 border-b border-slate-200 dark:border-slate-700">
                <div class="space-y-1">
                    <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        Process Termination / Pension
                    </h1>
                    <p class="text-slate-500 dark:text-slate-500 font-medium">
                        Configure termination details for <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $person->title?->title_name }} {{ $person->name_with_initials ?? 'Employee' }}</span>
                    </p>
                </div>
                <div>
                    <a href="javascript:history.back()" class="inline-flex justify-center items-center rounded-xl bg-white dark:bg-zinc-900 px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-zinc-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                        <svg class="w-5 h-5 mr-2 -ml-1 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            {{-- Current Profile Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-4xl shadow-xs border border-slate-300 dark:border-zinc-700 p-6 md:p-8 mt-8 relative overflow-hidden group hover:shadow-md transition-shadow">
                {{-- Decorative background element --}}
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-linear-to-br from-indigo-50 to-transparent dark:from-indigo-900/20 dark:to-transparent rounded-full blur-3xl opacity-50 pointer-events-none"></div>

                <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Current Profile Overview
                </h2>

                <div class="flex flex-col md:flex-row items-start gap-8 relative z-10">
                    {{-- Avatar --}}
                    <div class="w-24 h-24 md:w-[100px] md:h-[100px] rounded-3xl bg-indigo-600 flex items-center justify-center text-white font-extrabold text-3xl shadow-lg shrink-0 mt-1">
                        {{ strtoupper(substr($person->name_with_initials ?? '—', 0, 2)) }}
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 space-y-5 w-full">
                        <div>
                            <h3 class="text-[1.75rem] font-extrabold text-slate-900 dark:text-white leading-tight">
                                {{ $person->title?->title_name ?? '' }} {{ $person->name_with_initials ?? 'Unknown' }}
                            </h3>
                            <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mt-1.5 uppercase tracking-wider">
                                {{ $person->people_id }}
                            </p>
                        </div>

                        <div class="space-y-4 pt-1">
                            {{-- Top Row: 3 Items --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                {{-- NIC Info --}}
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700/50">
                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-700 shadow-sm flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-slate-500 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">NIC Number</p>
                                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate">{{ $person->nic ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                {{-- Rank Info --}}
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700/50">
                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-700 shadow-sm flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-slate-500 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Service & Rank</p>
                                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate" title="{{ $person->currentAppointment?->service?->service_name ?? 'N/A' }} - {{ $person->currentAppointment?->rank?->rank_name ?? 'N/A' }}">
                                            <span class="text-indigo-600 dark:text-indigo-400">{{ $person->currentAppointment?->service?->service_name ?? 'N/A' }}</span>
                                            <span class="text-slate-300 dark:text-slate-600 mx-1">&bull;</span>
                                            {{ $person->currentAppointment?->rank?->rank_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Position Info --}}
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700/50">
                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-700 shadow-sm flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-slate-500 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Current Position</p>
                                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate" title="{{ $person->currentAppointment?->position?->position_name ?? 'N/A' }}">{{ $person->currentAppointment?->position?->position_name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Bottom Row: Workplace Full Width --}}
                            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700/50">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-700 shadow-sm flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-slate-500 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Current Workplace</p>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate" title="{{ $person->currentAppointment?->workplace?->office_name ?? 'N/A' }}">{{ $person->currentAppointment?->workplace?->office_name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Termination Form --}}
            <div class="bg-white dark:bg-zinc-900 rounded-4xl shadow-xs border border-rose-200 dark:border-rose-900/50 p-6 md:p-8 mt-8">
                <h2 class="text-lg font-extrabold text-rose-600 dark:text-rose-500 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 11l2 2m0 0l2-2m-2 2v6" />
                    </svg>
                    Termination Actions
                </h2>

                @php
                $age = $person->date_of_birth ? \Carbon\Carbon::parse($person->date_of_birth)->age : null;
                $firstAppointmentDate = $person->currentAppointment?->appointment?->first_appointment_date;
                $serviceYears = $firstAppointmentDate ? (int) \Carbon\Carbon::parse($firstAppointmentDate)->diffInYears(now()) : null;
                @endphp
                <div class="flex flex-col lg:flex-row p-6 rounded-3xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50 mb-8 divide-y lg:divide-y-0 lg:divide-x divide-indigo-200 dark:divide-indigo-800">
                    {{-- Date of Birth --}}
                    <div class="flex items-center gap-4 flex-1 py-4 lg:py-0 lg:px-6 first:pt-0 lg:first:pl-0 last:pb-0 lg:last:pr-0">
                        <div class="w-12 h-12 rounded-full bg-sky-100 dark:bg-sky-900/50 flex items-center justify-center text-sky-600 dark:text-sky-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Date of Birth</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white mt-0.5">
                                {{ $person->date_of_birth ? \Carbon\Carbon::parse($person->date_of_birth)->format('Y-m-d') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    {{-- Current Age --}}
                    <div class="flex items-center gap-4 flex-1 py-4 lg:py-0 lg:px-6 last:pb-0 lg:last:pr-0">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Current Age</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">
                                {{ $age ?? 'N/A' }} <span class="text-sm font-bold text-slate-500">Yrs</span>
                            </p>
                        </div>
                    </div>

                    {{-- First Appointment Date --}}
                    <div class="flex items-center gap-4 flex-1 py-4 lg:py-0 lg:px-6 last:pb-0 lg:last:pr-0">
                        <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Appt. Date</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white mt-0.5">
                                {{ $firstAppointmentDate ? \Carbon\Carbon::parse($firstAppointmentDate)->format('Y-m-d') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    {{-- Total Service --}}
                    <div class="flex items-center gap-4 flex-1 py-4 lg:py-0 lg:px-6 last:pb-0 lg:last:pr-0">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Total Service</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">
                                {{ $serviceYears ?? 'N/A' }} <span class="text-sm font-bold text-slate-500">Yrs</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="my-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
                        {{-- Reason Selection --}}
                        <flux:field>
                            <flux:label>Reason for Termination <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="termination_reason">
                                <flux:select.option value="">Select Reason</flux:select.option>
                                @foreach($reasons as $reason)
                                <flux:select.option value="{{ $reason->termination_id }}">{{ $reason->reason }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="termination_reason" />
                        </flux:field>

                        {{-- Effective Date --}}
                        <flux:field>
                            <flux:label>Effective Date <span class="text-rose-500">*</span></flux:label>
                            <flux:input type="date" wire:model="termination_date" />
                            <flux:error name="termination_date" />
                        </flux:field>

                        {{-- Remarks --}}
                        <flux:field class="md:col-span-2 lg:col-span-3">
                            <flux:label>Additional Remarks (Optional)</flux:label>
                            <flux:textarea wire:model="remarks" rows="3" placeholder="Enter any additional notes..." />
                            <flux:error name="remarks" />
                        </flux:field>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-zinc-700 flex items-center justify-end gap-3">
                        <flux:button variant="ghost" class="font-bold rounded-xl" href="javascript:history.back()">Cancel</flux:button>
                        <button type="button" wire:click="processTermination" class="inline-flex justify-center items-center rounded-xl bg-rose-600 px-8 py-2.5 border border-transparent text-sm font-bold text-white shadow-sm hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-600 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-all">
                            Confirm Termination
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>