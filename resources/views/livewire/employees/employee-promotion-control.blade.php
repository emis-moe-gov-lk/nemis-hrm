<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Process Promotion"
        subtitle="Configure promotion details for {{ $person->title?->title_name }} {{ $person->name_with_initials ?? 'Employee' }}."
        icon="briefcase"
        :breadcrumbs="[
            'Teacher' => route('teacher.overview'),
            'Promotions' => route('teacher.promotions'),
            'Process' => '#'
        ]"
    >
        <x-slot:actions>
            <a href="javascript:history.back()" class="h-11 inline-flex justify-center items-center rounded-xl bg-white dark:bg-zinc-900 px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-zinc-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                <flux:icon name="arrow-left" variant="micro" class="mr-2" />
                Back to List
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Messages --}}
    @if(session('message'))
    <div class="p-4 rounded-2xl bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 animate-in fade-in duration-500">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-green-800 dark:text-green-200 font-bold">{{ session('message') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 animate-in fade-in duration-500">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <p class="text-red-800 dark:text-red-200 font-bold">{{ session('error') }}</p>
        </div>
    </div>
    @endif

            {{-- Current Profile Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-4xl shadow-xs border border-slate-300 dark:border-zinc-700 p-6 md:p-8 relative overflow-hidden group hover:shadow-md transition-shadow">
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

        {{-- Rank History --}}
        <div class="bg-white dark:bg-zinc-900 rounded-4xl shadow-xs border border-slate-300 dark:border-zinc-700 p-6 md:p-8 mt-8">
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Rank History
            </h2>

            <div class="my-6">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
                    {{-- Next Rank Selection --}}
                    <flux:field>
                        <flux:label>Select Next Rank <span class="text-rose-500">*</span></flux:label>
                        <flux:select wire:model="NewRankId">
                            <flux:select.option value="">Select Rank</flux:select.option>
                            @foreach($availableRanks as $rank)
                            <flux:select.option value="{{ $rank->rank_id }}">{{ $rank->rank_name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="NewRankId" />
                    </flux:field>

                    {{-- Ref Letter No --}}
                    <flux:field>
                        <flux:label>Ref Letter No <span class="text-rose-500">*</span></flux:label>
                        <flux:input wire:model="rankRefLetterNo" placeholder="e.g. ED/01/2024" />
                        <flux:error name="rankRefLetterNo" />
                    </flux:field>

                    {{-- Start Date --}}
                    <flux:field>
                        <flux:label>Effective Date <span class="text-rose-500">*</span></flux:label>
                        <flux:input type="date" wire:model="rankStartDate" />
                        <flux:error name="rankStartDate" />
                    </flux:field>

                    {{-- Submit Action --}}
                    <div>
                        <label class="block text-sm font-bold mb-2 opacity-0 select-none">Action</label>
                        <button type="button" wire:click="updateRank" class="w-full inline-flex justify-center items-center rounded-xl bg-indigo-600 px-8 py-3 border border-transparent text-sm font-bold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-all">
                            Update Rank
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-300 dark:border-zinc-700">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-500">
                    <thead class="bg-slate-50 dark:bg-zinc-800/50 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700">Rank</th>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700">Ref Letter No</th>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700">Start Date</th>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700">End Date</th>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                        @forelse($rankHistory as $history)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/25 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                {{ $history->rank?->rank_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-medium">
                                {{ $history->ref_letter_no ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $history->start_date ? $history->start_date->format('Y-m-d') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                {{ $history->end_date ? $history->end_date->format('Y-m-d') : 'Present' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($history->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                    Active
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-500/10 dark:text-slate-500">
                                    Past
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-300 dark:text-zinc-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-semibold text-sm">No rank history found</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Position History --}}
        <div class="bg-white dark:bg-zinc-900 rounded-4xl shadow-xs border border-slate-300 dark:border-zinc-700 p-6 md:p-8 mt-8">
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Position History
            </h2>

            <div class="my-6">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
                    {{-- New position dropdown --}}
                    <flux:field>
                        <flux:label>Select Next Position <span class="text-rose-500">*</span></flux:label>
                        <flux:select wire:model="newPositionId">
                            <flux:select.option value="">Select Position</flux:select.option>
                            @foreach($availablePositions as $position)
                            <flux:select.option value="{{ $position->position_id }}">{{ $position->position_name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="newPositionId" />
                    </flux:field>

                    {{-- Ref Letter No --}}
                    <flux:field>
                        <flux:label>Ref Letter No <span class="text-rose-500">*</span></flux:label>
                        <flux:input wire:model="positionRefLetterNo" placeholder="e.g. ED/01/2024" />
                        <flux:error name="positionRefLetterNo" />
                    </flux:field>

                    {{-- Start Date --}}
                    <flux:field>
                        <flux:label>Effective Date <span class="text-rose-500">*</span></flux:label>
                        <flux:input type="date" wire:model="positionStartDate" />
                        <flux:error name="positionStartDate" />
                    </flux:field>

                    {{-- Submit Action --}}
                    <div>
                        <label class="block text-sm font-bold mb-2 opacity-0 select-none">Action</label>
                        <button type="button" wire:click="updatePosition" class="w-full inline-flex justify-center items-center rounded-xl bg-indigo-600 px-8 py-3 border border-transparent text-sm font-bold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-all">
                            Update Position
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-300 dark:border-zinc-700">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-500">
                    <thead class="bg-slate-50 dark:bg-zinc-800/50 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700">Position</th>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700">Ref Letter No</th>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700">Start Date</th>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700">End Date</th>
                            <th class="px-6 py-4 border-b border-slate-300 dark:border-zinc-700 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                        @forelse($positionHistory as $history)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/25 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                {{ $history->position?->position_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-medium">
                                {{ $history->ref_letter_no ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $history->start_date ? $history->start_date->format('Y-m-d') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                {{ $history->end_date ? $history->end_date->format('Y-m-d') : 'Present' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($history->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                    Active
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-500/10 dark:text-slate-500">
                                    Past
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-300 dark:text-zinc-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-semibold text-sm">No position history found</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    </div>
</div>