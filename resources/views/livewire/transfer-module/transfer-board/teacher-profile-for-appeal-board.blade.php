<div class="p-6 lg:p-8 space-y-8">
    @php
        $appealStatus = $this->statusBadge($appeal->appeal_status);
        $applicationStatus = $application->boardRecommendation?->recommendation_status;
        $applicationDecisionColor = $applicationStatus === 'rejected' ? 'rose' : 'emerald';
        $teacher = $application->teacher;
    @endphp

    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div class="space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <flux:badge variant="neutral" size="sm" class="uppercase tracking-widest">{{ __('Appeal Board') }}</flux:badge>
                <flux:badge :color="$appealStatus['color']" size="sm" class="uppercase tracking-widest">{{ $appealStatus['label'] }}</flux:badge>
            </div>

            <div>
                <flux:heading size="xl" level="1">{{ __('Teacher Transfer Appeal Review') }}</flux:heading>
                <flux:subheading size="lg">{{ __('Review the submitted appeal, original transfer decision, and record the final appeal board outcome for this application.') }}</flux:subheading>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <flux:button href="{{ $this->backRoute }}" variant="ghost" icon="chevron-left" size="sm">{{ __($this->backLabel) }}</flux:button>

            @if($this->selectedAppealBoard?->isClosed())
                <flux:button icon="lock-closed" size="sm" disabled>{{ __('Board Closed') }}</flux:button>
            @else
                <flux:button wire:click="prepareDecisionDraft" icon="check-circle" size="sm" class="border-transparent !bg-blue-600 !text-white hover:!bg-blue-500">
                    {{ $appeal->appeal_status === 'pending' ? __('Make Appeal Decision') : __('Edit Appeal Decision') }}
                </flux:button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 xl:col-span-1">
            <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Appeal Request') }}</p>
            <h2 class="mt-3 text-xl font-black text-slate-900 dark:text-white">{{ $appeal->appeal_id }}</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ __('Appeal #:number | Application :application', ['number' => $appeal->number_of_appeals, 'application' => $appeal->transfer_application_id]) }}</p>

            <div class="mt-5 space-y-4 text-sm text-slate-700 dark:text-zinc-300">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Submitted Date') }}</p>
                    <p class="mt-1 font-semibold">{{ $appeal->created_at?->format('Y-m-d') ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Appeal Reason') }}</p>
                    <p class="mt-1 font-semibold">{{ $appeal->appeal_reason }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Teacher Remarks') }}</p>
                    <p class="mt-1 leading-6">{{ $appeal->appeal_remarks ?: __('No additional remarks were submitted.') }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 xl:col-span-2">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Original Transfer Board Decision') }}</p>
                    <h2 class="mt-3 text-xl font-black text-slate-900 dark:text-white">{{ $application->transfer_application_id }}</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $application->employee?->full_name ?? __('Teacher') }}</p>
                </div>

                @if($application->boardRecommendation)
                    <flux:badge :color="$applicationDecisionColor" size="sm" class="uppercase tracking-tighter">
                        {{ $application->boardRecommendation->recommendationList?->decision ?? ucfirst($applicationStatus) }}
                    </flux:badge>
                @endif
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Current Station') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $application->currentWorkplace?->office_name ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Target Province') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $application->targetProvince?->name ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Main Subject') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $teacher?->mainSubject?->name_en ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Selected School') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $application->boardRecommendation?->selectedSchool?->name ?? __('Not recorded') }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50/70 p-5 text-sm leading-6 text-slate-700 dark:border-zinc-800 dark:bg-zinc-800/30 dark:text-zinc-300">
                {{ $application->boardRecommendation?->recommendation_remarks ?: __('No original transfer board remarks were recorded.') }}
            </div>
        </div>
    </div>

    @if($appeal->appeal_status !== 'pending')
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Recorded Appeal Decision') }}</p>
                    <h3 class="mt-3 text-lg font-black text-slate-900 dark:text-white">{{ ucfirst($appeal->appeal_status) }}</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">{{ $appeal->decision_remarks ?: __('No additional appeal-board note recorded.') }}</p>
                </div>

                <flux:badge :color="$appealStatus['color']" size="sm" class="uppercase tracking-tighter">
                    {{ $appealStatus['label'] }}
                </flux:badge>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('School Selection') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $appeal->school_selection_type ? \Illuminate\Support\Str::headline($appeal->school_selection_type) : __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Selected Zone') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $appeal->selectedZone?->name ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Selected School') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $appeal->selectedSchool?->name ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Effective Date') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $appeal->transfer_effective_date?->format('Y-m-d') ?? __('N/A') }}</p>
                </div>
            </div>

            @if($appeal->rejection_reason)
                <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">
                    <span class="font-bold">{{ __('Rejection Reason') }}:</span> {{ ucfirst($appeal->rejection_reason) }}
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 xl:col-span-2">
            <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Teacher And Transfer Context') }}</p>

            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Full Name') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ ($application->employee?->title?->title_name ?? '') . ' ' . ($application->employee?->full_name ?? __('N/A')) }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('NIC / Employee ID') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $application->employee?->nic ?? __('N/A') }} / {{ $application->employee_id }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Teacher Category') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $teacher?->teacherCategory?->name ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Current Teaching Subject') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $teacher?->currentTeachingSubject?->name_en ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Transfer Category') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $application->category?->transfer_category_name ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Reason') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-zinc-100">{{ $application->reason?->title ?? __('N/A') }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Board Scope') }}</p>
            <h3 class="mt-3 text-lg font-black text-slate-900 dark:text-white">{{ $this->selectedAppealBoard?->board_name ?? __('Appeal Board') }}</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">{{ $this->selectedAppealBoard?->board_id ?? __('Not selected') }}</p>

            <div class="mt-5 space-y-4 text-sm text-slate-700 dark:text-zinc-300">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Policy') }}</p>
                    <p class="mt-1 font-semibold">{{ $this->selectedAppealBoard?->policy?->title ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Category') }}</p>
                    <p class="mt-1 font-semibold">{{ $this->selectedAppealBoard?->category?->transfer_category_name ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Board Date') }}</p>
                    <p class="mt-1 font-semibold">{{ $this->selectedAppealBoard?->start_date?->format('Y-m-d') ?? __('N/A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/70 p-6 dark:border-zinc-800 dark:bg-zinc-800/30">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Preferred Schools') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/50">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Order') }}</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Zone') }}</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('School') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @forelse($application->preferences as $preference)
                        @php
                            $destinationInstitution = $preference->institution?->office();
                        @endphp
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $preference->preference_order }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-300">{{ $preference->zonalOffice?->office_name ?? __('N/A') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-300">{{ $destinationInstitution->name ?? $preference->institution?->office_name ?? __('N/A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-zinc-400">{{ __('No preferred schools were recorded in this application.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal wire:model="showDecisionModal" name="appeal-decision-modal" class="w-full max-w-4xl rounded-[2rem] border border-slate-200/70 dark:border-zinc-800">
        <div class="space-y-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Make Appeal Decision') }}</flux:heading>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter font-black">{{ $appeal->appeal_id }}</flux:badge>
                    <flux:badge :color="$appealStatus['color']" size="sm" class="uppercase tracking-tighter font-black">{{ $appealStatus['label'] }}</flux:badge>
                    <flux:badge color="blue" size="sm" class="uppercase tracking-tighter font-black">{{ $application->employee?->full_name ?? __('Teacher') }}</flux:badge>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[16rem,1fr]">
                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50/70 p-5 dark:border-zinc-800 dark:bg-zinc-900/70">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">{{ __('Decision Path') }}</p>
                    <h3 class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ __('Choose the appeal outcome') }}</h3>

                    <div class="mt-5 space-y-3">
                        <button type="button" wire:click="$set('decisionOutcome', 'approved')" class="w-full rounded-[1.25rem] border px-4 py-4 text-left transition-all duration-200 {{ $decisionOutcome === 'approved' ? 'border-emerald-500 bg-emerald-50 shadow-sm shadow-emerald-500/10 dark:border-emerald-500/70 dark:bg-emerald-500/10' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-zinc-700' }}">
                            <p class="text-sm font-black text-slate-900 dark:text-white">{{ __('Approve Appeal') }}</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ __('Record the appeal as successful and allocate the school outcome.') }}</p>
                        </button>

                        <button type="button" wire:click="$set('decisionOutcome', 'rejected')" class="w-full rounded-[1.25rem] border px-4 py-4 text-left transition-all duration-200 {{ $decisionOutcome === 'rejected' ? 'border-rose-500 bg-rose-50 shadow-sm shadow-rose-500/10 dark:border-rose-500/70 dark:bg-rose-500/10' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-zinc-700' }}">
                            <p class="text-sm font-black text-slate-900 dark:text-white">{{ __('Reject Appeal') }}</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ __('Keep the original decision and record the appeal-board reason.') }}</p>
                        </button>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    @if($decisionOutcome === 'approved')
                        <div class="space-y-6">
                            <div class="rounded-[1.25rem] border border-emerald-200 bg-emerald-50/80 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-emerald-700 dark:text-emerald-200">{{ __('Appeal Approval') }}</p>
                                <p class="mt-2 text-sm leading-6 text-emerald-700 dark:text-emerald-100">{{ __('Choose either one of the teacher\'s preferred schools or another school from the provincial zone and school list.') }}</p>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="md:col-span-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <button type="button" wire:click="$set('decisionSchoolSelectionType', 'preferred')" class="rounded-[1.25rem] border px-4 py-4 text-left transition-all {{ $decisionSchoolSelectionType === 'preferred' ? 'border-blue-500 bg-blue-50 shadow-sm shadow-blue-500/10 dark:border-blue-500/70 dark:bg-blue-500/10' : 'border-slate-200 bg-slate-50 hover:border-slate-300 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-zinc-700' }}">
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ __('Preferred School') }}</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ __('Choose from the schools submitted by the teacher.') }}</p>
                                    </button>

                                    <button type="button" wire:click="$set('decisionSchoolSelectionType', 'other')" class="rounded-[1.25rem] border px-4 py-4 text-left transition-all {{ $decisionSchoolSelectionType === 'other' ? 'border-blue-500 bg-blue-50 shadow-sm shadow-blue-500/10 dark:border-blue-500/70 dark:bg-blue-500/10' : 'border-slate-200 bg-slate-50 hover:border-slate-300 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-zinc-700' }}">
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ __('Other School') }}</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ __('Select a zone first, then select a school outside the preference list.') }}</p>
                                    </button>
                                </div>

                                @if($decisionSchoolSelectionType === 'preferred')
                                    <flux:field class="md:col-span-2">
                                        <flux:select label="{{ __('Preferred Institution For Placement') }}" wire:model.live="decisionPreferenceInstitution">
                                            <option value="">{{ __('Select one of the teacher\'s preference schools') }}</option>
                                            @foreach($application->preferences as $preference)
                                                @php
                                                    $destinationInstitution = $preference->institution?->office();
                                                @endphp
                                                @if($preference->ins_wp_id)
                                                    <option value="{{ $preference->ins_wp_id }}">
                                                        {{ __('Preference :order - :name', ['order' => $preference->preference_order, 'name' => $destinationInstitution->name ?? $preference->institution?->office_name ?? __('Institution')]) }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </flux:select>
                                        @error('decisionPreferenceInstitution') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                    </flux:field>
                                @else
                                    <flux:field>
                                        <flux:select label="{{ __('Zone') }}" wire:model.live="decisionOtherZoneId">
                                            <option value="">{{ __('Select zone...') }}</option>
                                            @foreach($this->decisionZones as $zone)
                                                <option value="{{ $zone->workplace_id }}">{{ $zone->name }}</option>
                                            @endforeach
                                        </flux:select>
                                        @error('decisionOtherZoneId') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                    </flux:field>

                                    <flux:field>
                                        <flux:select label="{{ __('School') }}" wire:model.live="decisionOtherSchoolId">
                                            <option value="">{{ __('Select school...') }}</option>
                                            @foreach($this->decisionSchools as $school)
                                                <option value="{{ $school->workplace_id }}">{{ $school->name }}</option>
                                            @endforeach
                                        </flux:select>
                                        @error('decisionOtherSchoolId') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                    </flux:field>
                                @endif

                                <flux:field class="md:col-span-2">
                                    <flux:input label="{{ __('Transfer Effective Date') }}" type="date" wire:model.live="decisionEffectiveDate" />
                                    @error('decisionEffectiveDate') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                </flux:field>

                                <flux:field class="md:col-span-2">
                                    <flux:textarea label="{{ __('Appeal Board Note') }}" wire:model.live="decisionNote" rows="4" placeholder="{{ __('Example: Appeal accepted after review of the station context and the updated vacancy position.') }}" />
                                </flux:field>
                            </div>
                        </div>
                    @else
                        <div class="space-y-6">
                            <div class="rounded-[1.25rem] border border-rose-200 bg-rose-50/80 p-4 dark:border-rose-500/30 dark:bg-rose-500/10">
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-rose-700 dark:text-rose-200">{{ __('Appeal Rejection') }}</p>
                                <p class="mt-2 text-sm leading-6 text-rose-700 dark:text-rose-100">{{ __('Record the appeal-board reason and retain the original board outcome for this application.') }}</p>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <flux:field>
                                    <flux:select label="{{ __('Reason For Rejection') }}" wire:model.live="decisionRejectionReason">
                                        <option value="">{{ __('Select a reason') }}</option>
                                        <option value="criteria">{{ __('Does not fulfil the required criteria') }}</option>
                                        <option value="documents">{{ __('Inadequate supporting documents') }}</option>
                                        <option value="eligibility">{{ __('Not eligible at this time') }}</option>
                                    </flux:select>
                                    @error('decisionRejectionReason') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                </flux:field>

                                <flux:field class="md:col-span-2">
                                    <flux:textarea label="{{ __('Appeal Board Note') }}" wire:model.live="decisionNote" rows="5" placeholder="{{ __('Example: Appeal rejected because the original transfer-board decision remains valid after reviewing the submitted grounds.') }}" />
                                </flux:field>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-200 pt-5 dark:border-zinc-800">
                <div class="flex items-center justify-end gap-3">
                    <flux:modal.close>
                        <flux:button variant="ghost" wire:click="closeDecisionModal" type="button">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="button" wire:click="submitDecision" class="border-transparent !bg-blue-600 !text-white hover:!bg-blue-500">
                        {{ __('Submit') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
