<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Page Header --}}
    <x-page-header
        title="Change Workplace"
        subtitle="Reassign {{ $person->title?->title_name }} {{ $person->name_with_initials ?? $person->full_name }} to a new workplace."
        icon="arrows-right-left"
        :breadcrumbs="[
            'Home'               => route('teacher.overview'),
            'Changing Workplace' => route('employees.changing-workplace'),
            'Change'             => '#',
        ]">
        <x-slot:actions>
            <a href="{{ route('employees.changing-workplace') }}"
                class="h-11 inline-flex justify-center items-center rounded-xl bg-white dark:bg-zinc-900 px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-zinc-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                <flux:icon name="arrow-left" variant="micro" class="mr-2" />
                Back to List
            </a>
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

    {{-- ── Employee Profile Card ───────────────────────────────────────── --}}
    <div class="bg-white dark:bg-zinc-900 rounded-4xl shadow-xs border border-slate-200 dark:border-zinc-700 p-6 md:p-8 relative overflow-hidden group">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-linear-to-br from-indigo-50 to-transparent dark:from-indigo-900/20 dark:to-transparent rounded-full blur-3xl opacity-50 pointer-events-none"></div>

        <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2">
            <flux:icon name="user-circle" class="w-5 h-5 text-indigo-500" />
            Current Profile Overview
        </h2>

        <div class="flex flex-col md:flex-row items-start gap-8 relative z-10">
            {{-- Avatar --}}
            @php
            $name = $person->name_with_initials ?? $person->full_name ?? '—';
            $initials = strtoupper(substr($name, 0, 2));
            @endphp
            <div class="w-20 h-20 rounded-3xl bg-indigo-600 flex items-center justify-center text-white font-extrabold text-2xl shadow-lg shrink-0">
                {{ $initials }}
            </div>

            {{-- Details --}}
            <div class="flex-1 space-y-4 w-full">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white leading-tight">
                        {{ $person->title?->title_name ?? '' }} {{ $name }}
                    </h3>
                    <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mt-1 uppercase tracking-wider">
                        {{ $person->people_id }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    {{-- Rank --}}
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700/50">
                        <div class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-700 shadow-sm flex items-center justify-center shrink-0">
                            <flux:icon name="star" class="w-4 h-4 text-slate-500" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Service & Rank</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate">
                                <span class="text-indigo-600 dark:text-indigo-400">{{ $person->currentAppointment?->service?->service_name ?? '—' }}</span>
                                <span class="text-slate-300 dark:text-slate-600 mx-1">&bull;</span>
                                {{ $person->currentAppointment?->rank?->rank_name ?? '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Position --}}
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700/50">
                        <div class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-700 shadow-sm flex items-center justify-center shrink-0">
                            <flux:icon name="briefcase" class="w-4 h-4 text-slate-500" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Position</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate" title="{{ $person->currentAppointment?->position?->position_name ?? '—' }}">
                                {{ $person->currentAppointment?->position?->position_name ?? '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Current Workplace --}}
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-700/50">
                        <div class="w-9 h-9 mt-0.5 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0">
                            <flux:icon name="building-office-2" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider mb-1">Current Workplace</p>

                            @php
                            $wp = $person->currentAppointment?->workplace;
                            $hierarchy = [];
                            if ($wp) {
                            $curr = $wp;
                            $depth = 0;
                            while ($curr->parent && $depth < 4) {
                                array_unshift($hierarchy, $curr->parent->office_name);
                                $curr = $curr->parent;
                                $depth++;
                                }
                                }
                                @endphp

                                @if(!empty($hierarchy))
                                <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                    @foreach($hierarchy as $parentName)
                                    <span class="text-[10px] font-bold text-amber-600/70 dark:text-amber-500/70 flex items-center gap-1.5">
                                        {{ $parentName }}
                                        <flux:icon name="chevron-right" variant="micro" class="w-2.5 h-2.5 text-amber-400/70" />
                                    </span>
                                    @endforeach
                                </div>
                                @endif

                                <p class="text-sm font-black text-amber-900 dark:text-amber-200 truncate" title="{{ $wp?->office_name ?? 'Not assigned' }}">
                                    {{ $wp?->office_name ?? 'Not assigned' }}
                                </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Change Workplace Form ────────────────────────────────────────── --}}
    <form wire:submit.prevent="confirmChange">
        <div class="bg-white dark:bg-zinc-900 rounded-4xl shadow-xs border border-slate-200 dark:border-zinc-700 p-6 md:p-8">

            <h2 class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2">
                <flux:icon name="arrows-right-left" class="w-5 h-5 text-indigo-500" />
                New Workplace Assignment
            </h2>

            <div class="space-y-5">

                {{-- Office Level --}}
                <flux:select label="Working Place Level" wire:model.live="officeLevel">
                    <option value="">Select</option>
                    @foreach ($officeLevelOption as $level)
                    <option value="{{ $level->office_level_id }}">{{ $level->office_level_name }}</option>
                    @endforeach
                </flux:select>

                {{-- Institution sub-filters (only for OLID006) --}}
                @if ($officeLevel == 'OLID006')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-zinc-800/40 border border-slate-200 dark:border-zinc-700/50">
                    <flux:select label="Zonal Education Office" wire:model.live="zonalEducationOffice">
                        <option value="">Select</option>
                        @foreach ($zonalEducationOfficeOption as $zone)
                        <option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select label="Institution Category" wire:model.live="institutionCategory">
                        <option value="">Select</option>
                        @foreach ($institutionCategoryOption as $data)
                        <option value="{{ $data->institution_category_id }}">{{ $data->institution_category_name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                @endif

                {{-- Final Workplace --}}
                @if ($officeLevel)
                <div class="relative">
                    {{-- Loading skeleton overlay --}}
                    <div
                        wire:loading
                        wire:target="updatedOfficeLevel,updatedZonalEducationOffice,updatedInstitutionCategory"
                        class="absolute inset-0 z-10 flex items-center gap-3 px-4 rounded-xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 animate-pulse">
                        <svg class="animate-spin w-4 h-4 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Loading workplaces…</span>
                    </div>

                    <div wire:loading.class="opacity-0 pointer-events-none"
                        wire:target="updatedOfficeLevel,updatedZonalEducationOffice,updatedInstitutionCategory">
                        <flux:select label="Working Place" wire:model.live="workingPlace">
                            <option value="">Select</option>
                            @foreach ($workingPlaceOption as $office)
                            <option value="{{ $office->workplace_id }}">{{ $office->office_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="workingPlace" />
                    </div>
                </div>
                @endif

                <flux:separator />

                {{-- Ref Letter & Date --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <flux:field>
                        <flux:label>Reference Letter No <span class="text-rose-500">*</span></flux:label>
                        <flux:input wire:model="refLetterNo" placeholder="e.g. ED/01/2025" />
                        <flux:error name="refLetterNo" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Effective Date <span class="text-rose-500">*</span></flux:label>
                        <flux:input type="date" wire:model="effectiveDate" />
                        <flux:error name="effectiveDate" />
                    </flux:field>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-zinc-800">
                    <a href="{{ route('employees.changing-workplace') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white dark:bg-zinc-900 border border-slate-300 dark:border-zinc-600 text-sm font-bold text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-sm font-bold text-white shadow-sm transition-all hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmChange">
                            <flux:icon name="arrows-right-left" variant="micro" class="w-4 h-4 inline-block mr-1" />
                            Confirm Transfer
                        </span>
                        <span wire:loading wire:target="confirmChange" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="mt-8">
        @livewire('employees.working-place-history', ['peopleId' => $person->people_id])
    </div>

</div>