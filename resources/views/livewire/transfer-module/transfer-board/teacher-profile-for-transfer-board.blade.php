<div class="p-6 lg:p-8">
    @php
        $status = $this->statusBadge($application->status);
        $teacher = $application->teacher;
        $currentInstitution = $application->currentWorkplace?->institution;
        $currentStep = $this->currentStep;
        $preferenceDistanceMap = $this->preferenceDistanceMap;
        $hasPreferenceDistanceMap = !empty($preferenceDistanceMap['schools']);
        $recommendedNeededSchools = collect($this->recommendedNeededSchools);
        $recommendedNeededSchoolsNote = $this->recommendedNeededSchoolsNote;
        $recommendedNeededSubject = $recommendedNeededSchools->first()['subject_name'] ?? $teacher?->mainSubject?->name_en ?? __('this subject');
        $transferScore = $this->transferScore;
    @endphp

    <div class="flex flex-col md:flex-row md:items-stretch justify-between mb-8 gap-4">
        <div class="max-w-4xl flex-1">
            <flux:heading size="xl" level="1">{{ __('Teacher Transfer Application Review') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Review the teacher profile, current station context, preferred institutions, and workflow progress in the same provincial board workspace.') }}</flux:subheading>

            <div class="mt-5">
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $application->employee?->full_name }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <flux:badge :color="$status['color']" size="sm" class="uppercase tracking-tighter font-black">{{ $status['label'] }}</flux:badge>
                    <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter font-black">{{ $application->transfer_application_id }}</flux:badge>
                    <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter font-black">{{ \Illuminate\Support\Str::headline($application->policy->transfer_type ?? $application->transfer_type) }}</flux:badge>
                    <flux:badge color="blue" size="sm" class="uppercase tracking-tighter font-black">
                        {{ $currentStep?->officeLevel?->office_level_name ?? __('Completed') }}
                    </flux:badge>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-start gap-4 md:min-w-[18rem] md:self-stretch md:items-end md:justify-between md:pb-2">
            <flux:button href="{{ $this->backRoute }}" variant="ghost" icon="chevron-left" size="sm">{{ __($this->backLabel) }}</flux:button>

            @if($this->selectedTransferBoard?->isClosed())
                <flux:button icon="lock-closed" size="base" disabled>
                    {{ __('Board Closed') }}
                </flux:button>
            @else
                <flux:button
                    wire:click="prepareDecisionDraft"
                    icon="check-circle"
                    size="base"
                    class="border-transparent px-6 py-3 text-sm font-bold !bg-blue-600 !text-white shadow-lg shadow-blue-500/20 hover:!bg-blue-500"
                >
                    {{ $application->boardRecommendation ? __('Edit Decision') : __('Make Decision') }}
                </flux:button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">
                    <flux:icon name="chart-bar" size="lg" />
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">{{ __('Transfer Score') }}</h2>
                        <flux:badge :color="$transferScore['color']" size="sm" class="uppercase tracking-tighter font-black">{{ $transferScore['label'] }}</flux:badge>
                    </div>
                    <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">
                        {{ __('Calculated live from this policy as at :date. It supports board decisions but does not auto-approve applications.', ['date' => $transferScore['as_of_date']]) }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 text-center dark:border-zinc-700 dark:bg-zinc-800/60">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">{{ __('Total') }}</p>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $transferScore['formatted_total'] }}</p>
                </div>
                <flux:button type="button" wire:click="toggleScoreDetails" variant="ghost" icon="list-bullet" size="sm">
                    {{ $showScoreDetails ? __('Hide Score Details') : __('View Score Details') }}
                </flux:button>
            </div>
        </div>

        @if($showScoreDetails)
            <div class="mt-5 space-y-4 border-t border-slate-100 pt-5 dark:border-zinc-800">
                @foreach($transferScore['warnings'] as $warning)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                        {{ $warning }}
                    </div>
                @endforeach

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                    @foreach($transferScore['breakdown'] as $scoreItem)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-black text-slate-900 dark:text-white">{{ $scoreItem['label'] }}</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ $scoreItem['formula'] }}</p>
                                </div>
                                <flux:badge color="blue" size="sm" class="uppercase tracking-tighter font-black">{{ $scoreItem['formatted_score'] }}</flux:badge>
                            </div>

                            @if(!empty($scoreItem['segments']))
                                <div class="mt-3 space-y-2">
                                    @foreach($scoreItem['segments'] as $segment)
                                        <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-900">
                                            <p class="font-bold text-slate-700 dark:text-zinc-200">{{ $segment['workplace'] }}</p>
                                            <p class="mt-1 text-slate-500 dark:text-zinc-400">{{ __('Facility') }}: {{ $segment['facility_id'] }} | {{ __('Years') }}: {{ number_format($segment['years'], 2) }} | {{ __('Score') }}: {{ number_format($segment['score'], 2) }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($scoreItem['achievements']))
                                <div class="mt-3 space-y-2">
                                    @foreach($scoreItem['achievements'] as $achievement)
                                        <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-900">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="font-bold text-slate-700 dark:text-zinc-200">{{ $achievement['title'] }}</p>
                                                    <p class="mt-1 text-slate-500 dark:text-zinc-400">{{ $achievement['type'] }} | {{ $achievement['level'] }} | {{ __('Score') }} {{ number_format($achievement['score'], 2) }}</p>
                                                </div>
                                                <flux:button
                                                    type="button"
                                                    wire:click="toggleAchievementInclusion({{ $achievement['id'] }})"
                                                    variant="{{ $achievement['is_included'] ? 'ghost' : 'filled' }}"
                                                    size="xs">
                                                    {{ $achievement['is_included'] ? __('Exclude') : __('Include') }}
                                                </flux:button>
                                            </div>
                                            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-[1fr,auto]">
                                                <flux:input
                                                    wire:model.defer="scoreAchievementRemarks.{{ $achievement['id'] }}"
                                                    placeholder="{{ __('Board remark for this achievement...') }}"
                                                    size="sm" />
                                                <flux:button type="button" wire:click="saveAchievementRemark({{ $achievement['id'] }})" variant="ghost" size="xs">
                                                    {{ __('Save Remark') }}
                                                </flux:button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if($application->boardRecommendation)
        @php
            $boardDecision = $application->boardRecommendation;
            $decisionColor = $boardDecision->recommendation_status === 'rejected' ? 'rose' : 'emerald';
            $selectedSchoolName = $boardDecision->selectedSchool?->name;
            $selectedZoneName = $boardDecision->selectedZone?->name;
        @endphp

        <div class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:badge :color="$decisionColor" size="sm" class="uppercase tracking-tighter font-black">
                            {{ $boardDecision->recommendationList?->decision ?? ucfirst($boardDecision->recommendation_status) }}
                        </flux:badge>

                        @if($boardDecision->school_selection_type)
                            <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter font-black">
                                {{ \Illuminate\Support\Str::headline($boardDecision->school_selection_type) }} {{ __('School') }}
                            </flux:badge>
                        @endif
                    </div>

                    <h2 class="mt-3 text-lg font-black text-slate-900 dark:text-white">{{ __('Recorded Transfer Board Decision') }}</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                        {{ $boardDecision->recommendation_remarks ?: __('No additional board note recorded.') }}
                    </p>
                </div>

                @if($boardDecision->recommendation_status === 'approved')
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 text-sm dark:border-emerald-500/30 dark:bg-emerald-500/10 lg:min-w-[18rem]">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-emerald-700 dark:text-emerald-200">{{ __('Selected School') }}</p>
                        <p class="mt-2 font-black text-emerald-950 dark:text-emerald-100">{{ $selectedSchoolName ?? __('School not recorded') }}</p>
                        <p class="mt-1 text-emerald-700 dark:text-emerald-200">{{ $selectedZoneName ?? __('Zone not recorded') }}</p>
                        <p class="mt-3 text-xs font-semibold text-emerald-700 dark:text-emerald-200">
                            {{ __('Effective') }}: {{ $boardDecision->transfer_effective_date?->format('M d, Y') ?? __('N/A') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Current Station Service') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ $application->current_workplace_service_years }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <flux:icon name="building-office" size="lg" />
                </div>
            </div>
            <div class="mt-2 relative z-10 text-xs font-medium text-slate-500 dark:text-zinc-400">
                {{ $application->current_workplace_join_date?->format('M d, Y') ?? __('No station join date') }}
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="building-office" class="w-24 h-24" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Total Service') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ $application->total_service_years }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                    <flux:icon name="calendar" size="lg" />
                </div>
            </div>
            <div class="mt-2 relative z-10 text-xs font-medium text-slate-500 dark:text-zinc-400">
                {{ $application->first_appointment_date?->format('M d, Y') ?? __('No first appointment date') }}
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="calendar" class="w-24 h-24" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Preference Schools') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($application->preferences->count()) }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                    <flux:icon name="building-office-2" size="lg" />
                </div>
            </div>
            <div class="mt-2 relative z-10 text-xs font-medium text-slate-500 dark:text-zinc-400">
                {{ __('Destination institutions selected by the teacher') }}
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="building-office-2" class="w-24 h-24" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Current Review Stage') }}</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $currentStep?->officeLevel?->office_level_name ?? __('Completed') }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400">
                    <flux:icon name="queue-list" size="lg" />
                </div>
            </div>
            <div class="mt-2 relative z-10 text-xs font-medium text-slate-500 dark:text-zinc-400">
                @if(in_array($application->status, ['submitted', 'processing']))
                    {{ __('Step') }} {{ $application->current_step }}
                @else
                    {{ __('Workflow completed') }}
                @endif
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="queue-list" class="w-24 h-24" />
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <div class="xl:col-span-8 space-y-8">
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-100 dark:border-zinc-800">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Applicant And Teaching Profile') }}</h3>
                            <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">{{ __('Identity, service stream, subject alignment, and classroom context.') }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter font-black">{{ $application->employee?->nic ?? __('NIC unavailable') }}</flux:badge>
                            @if($teacher?->medium?->name)
                                <flux:badge color="blue" size="sm" class="uppercase tracking-tighter font-black">{{ $teacher->medium->name }}</flux:badge>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Full Name') }}</p>
                        <p class="mt-1 text-base font-bold text-slate-900 dark:text-white">{{ ($application->employee?->title?->title_name ?? '') . ' ' . ($application->employee?->full_name ?? __('N/A')) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Employee ID') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $application->employee_id }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Teacher Category') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $teacher?->teacherCategory?->name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Teacher Type') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $teacher?->teacherType?->type_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Appointment Subject') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $teacher?->appointmentSubject?->name_en ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Current Teaching Subject') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $teacher?->currentTeachingSubject?->name_en ?? $teacher?->mainSubject?->name_en ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Secondary Subject') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $teacher?->secondarySubject?->name_en ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Submitted Date') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $application->created_at?->format('M d, Y') ?? __('N/A') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-100 dark:border-zinc-800">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Transfer Case Details') }}</h3>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Transfer Policy') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $application->policy?->title ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Transfer Category') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $application->category?->transfer_category_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Target Province') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $application->targetProvince?->name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Reason') }}</p>
                        <p class="mt-1 text-base font-semibold text-slate-700 dark:text-zinc-200">{{ $application->reason?->title ?? __('N/A') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <div class="rounded-2xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-800/20 p-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:badge :color="$application->is_declared ? 'green' : 'rose'" size="sm" class="uppercase tracking-tighter font-black">
                                    {{ $application->is_declared ? __('Declared') : __('Declaration Missing') }}
                                </flux:badge>
                                <flux:badge :color="$application->has_disciplinary_actions ? 'rose' : 'green'" size="sm" class="uppercase tracking-tighter font-black">
                                    {{ $application->has_disciplinary_actions ? __('Disciplinary Record Present') : __('No Disciplinary Record') }}
                                </flux:badge>
                            </div>

                            @if($application->disciplinary_actions_details)
                                <p class="mt-4 text-sm leading-relaxed text-slate-600 dark:text-zinc-300">{{ $application->disciplinary_actions_details }}</p>
                            @else
                                <p class="mt-4 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">{{ __('No disciplinary note was recorded with this application.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="xl:col-span-4 space-y-8">
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-100 dark:border-zinc-800">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Current Station Snapshot') }}</h3>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Institution / Office') }}</p>
                        <p class="mt-1 text-base font-black text-slate-900 dark:text-white">{{ $application->currentWorkplace?->office_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Authority') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $currentInstitution?->authority?->authority_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Zonal Office') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $currentInstitution?->zonalEducationOffice?->name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Divisional Office') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $currentInstitution?->divisionalEducationOffice?->name ?? __('N/A') }}</p>
                    </div>
                    <div class="pt-4 border-t border-slate-200 dark:border-zinc-800">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Working Period At Station') }}</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-white">{{ $application->current_workplace_service_years }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-100 dark:border-zinc-800">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Appointment Context') }}</h3>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Service') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $application->appointment?->service?->service_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Rank') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $application->appointment?->rank?->rank_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Position') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $application->appointment?->position?->position_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Gender') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $application->employee?->gender?->gender_name ?? __('N/A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-100 dark:border-zinc-800 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Preference Schools') }}</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">{{ __('Each selected destination opens a school-level transfer board with staffing, cadre, and inbound/outbound movements.') }}</p>
            </div>

            @if($hasPreferenceDistanceMap)
                <flux:button
                    type="button"
                    wire:click="togglePreferenceDistanceMap"
                    variant="ghost"
                    icon="map"
                    size="sm"
                    class="font-bold"
                >
                    {{ $showPreferenceDistanceMap ? __('Hide Distance Map') : __('View Distance Map') }}
                </flux:button>
            @endif
        </div>

        @if($showPreferenceDistanceMap && $hasPreferenceDistanceMap)
            <div class="border-b border-slate-100 bg-slate-50/40 p-6 dark:border-zinc-800 dark:bg-zinc-800/20">
                <x-maps.leaflet-preference-distance-map
                    :origin="$preferenceDistanceMap['origin'] ?? []"
                    :schools="$preferenceDistanceMap['schools'] ?? []"
                    height="h-[30rem]"
                />
            </div>
        @endif

        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse($application->preferences as $preference)
                @php
                    $destinationInstitution = $preference->institution?->office();
                @endphp
                <div class="rounded-2xl border border-slate-200 dark:border-zinc-800 bg-slate-50/60 dark:bg-zinc-800/20 p-5 hover:bg-slate-50 dark:hover:bg-zinc-800/35 transition-all duration-200">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 flex items-center justify-center text-sm font-black text-slate-700 dark:text-zinc-200">
                                {{ $preference->preference_order }}
                            </div>
                            <div>
                                <p class="text-base font-black text-slate-900 dark:text-white">{{ $destinationInstitution?->name ?? $preference->institution?->office_name ?? __('Institution unavailable') }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $preference->zonalOffice?->office_name ?? __('Zone unavailable') }}</p>
                                <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px] font-semibold text-slate-500 dark:text-zinc-400">
                                    <span>{{ __('Authority') }}: {{ $destinationInstitution?->authority?->authority_name ?? __('N/A') }}</span>
                                    <span class="text-slate-300 dark:text-zinc-700">|</span>
                                    <span>{{ __('Census') }}: {{ $destinationInstitution?->census_no ?? __('N/A') }}</span>
                                </div>
                            </div>
                        </div>
                        @if($destinationInstitution?->id)
                            <flux:button href="{{ route('transfer-board.institution-profile-for-transfer-board', ['applicationId' => $application->transfer_application_id, 'institutionId' => $destinationInstitution->id, 'board' => $board, 'selectedBoardId' => $selectedBoardId]) }}" variant="ghost" icon="arrow-top-right-on-square" size="sm" />
                        @endif
                    </div>

                    @if($destinationInstitution?->id)
                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-zinc-700 flex items-center justify-between gap-3">
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('School transfer insight') }}</div>
                            <flux:button href="{{ route('transfer-board.institution-profile-for-transfer-board', ['applicationId' => $application->transfer_application_id, 'institutionId' => $destinationInstitution->id, 'board' => $board, 'selectedBoardId' => $selectedBoardId]) }}" variant="filled" icon="eye" size="sm">{{ __('Open') }}</flux:button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="lg:col-span-2 px-6 py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 rounded-full bg-slate-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4">
                            <flux:icon name="building-office-2" size="lg" class="text-slate-300 dark:text-zinc-600" />
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-zinc-200 mb-1">{{ __('No preference schools found') }}</h3>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 max-w-xs mx-auto">
                            {{ __('This application does not currently have destination schools to review.') }}
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-8 bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-100 dark:border-zinc-800">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Recommended Needed Schools') }}</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">
                        {{ __('Additional schools in this board scope where :subject has deficit cadre. Teacher preference schools are excluded from this list.', ['subject' => $recommendedNeededSubject]) }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <flux:badge color="rose" size="xs" class="uppercase tracking-tighter">
                        {{ trans_choice(':count school|:count schools', $recommendedNeededSchools->count(), ['count' => $recommendedNeededSchools->count()]) }}
                    </flux:badge>
                    <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">
                        {{ __('Ranked by adjusted deficit need') }}
                    </flux:badge>
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse($recommendedNeededSchools as $neededSchool)
                <div class="rounded-2xl border border-slate-200 dark:border-zinc-800 bg-slate-50/60 dark:bg-zinc-800/20 p-5 hover:bg-slate-50 dark:hover:bg-zinc-800/35 transition-all duration-200">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:badge color="rose" size="xs" class="uppercase tracking-tighter">
                                    {{ trans_choice(':count deficit|:count deficits', $neededSchool['deficit_posts'], ['count' => $neededSchool['deficit_posts']]) }}
                                </flux:badge>
                                <flux:badge :color="$neededSchool['has_applicants'] ? 'amber' : 'emerald'" size="xs" class="uppercase tracking-tighter">
                                    {{ $neededSchool['has_applicants']
                                        ? trans_choice(':count applicant|:count applicants', $neededSchool['applicant_count'], ['count' => $neededSchool['applicant_count']])
                                        : __('No applicants yet') }}
                                </flux:badge>
                                @if($neededSchool['distance_label'])
                                    <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">
                                        {{ $neededSchool['distance_label'] }}
                                    </flux:badge>
                                @endif
                            </div>

                            <p class="mt-3 text-base font-black text-slate-900 dark:text-white">{{ $neededSchool['name'] }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $neededSchool['zone'] }}</p>

                            <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px] font-semibold text-slate-500 dark:text-zinc-400">
                                <span>{{ __('Subject') }}: {{ $neededSchool['subject_name'] }}</span>
                                <span class="text-slate-300 dark:text-zinc-700">|</span>
                                <span>{{ __('Medium') }}: {{ $neededSchool['medium_name'] }}</span>
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] font-semibold text-slate-500 dark:text-zinc-400">
                                <span>{{ __('Authority') }}: {{ $neededSchool['authority'] }}</span>
                                <span class="text-slate-300 dark:text-zinc-700">|</span>
                                <span>{{ __('Census') }}: {{ $neededSchool['census_no'] }}</span>
                            </div>

                            @if(($neededSchool['approved_incoming_transfers'] ?? 0) > 0 || ($neededSchool['approved_outgoing_transfers'] ?? 0) > 0)
                                <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px] font-semibold text-blue-600 dark:text-blue-300">
                                    <span>{{ __('Adjusted by approved decisions') }}</span>
                                    <span class="text-slate-300 dark:text-zinc-700">|</span>
                                    <span>{{ __('Incoming') }}: {{ $neededSchool['approved_incoming_transfers'] }}</span>
                                    <span class="text-slate-300 dark:text-zinc-700">|</span>
                                    <span>{{ __('Outgoing') }}: {{ $neededSchool['approved_outgoing_transfers'] }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 gap-2 md:min-w-[12rem]">
                            <div class="rounded-xl border border-slate-200 bg-white p-3 text-center dark:border-zinc-700 dark:bg-zinc-900">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Approved') }}</p>
                                <p class="mt-1 text-lg font-black text-slate-900 dark:text-white">{{ $neededSchool['approved_posts'] }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-white p-3 text-center dark:border-zinc-700 dark:bg-zinc-900">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('Filled') }}</p>
                                <p class="mt-1 text-lg font-black text-slate-900 dark:text-white">{{ $neededSchool['filled_posts'] }}</p>
                            </div>
                            <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-center dark:border-rose-500/30 dark:bg-rose-500/10">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-rose-500 dark:text-rose-300">{{ __('Need') }}</p>
                                <p class="mt-1 text-lg font-black text-rose-600 dark:text-rose-300">{{ $neededSchool['deficit_posts'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-zinc-700 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs font-semibold text-slate-500 dark:text-zinc-400">
                            {{ $neededSchool['has_applicants']
                                ? __('Another matching application has already selected this school.')
                                : __('No matching applicant has selected this school yet.') }}
                        </p>

                        <flux:button
                            href="{{ route('transfer-board.institution-profile-for-transfer-board', ['applicationId' => $application->transfer_application_id, 'institutionId' => $neededSchool['institution_id'], 'board' => $board, 'selectedBoardId' => $selectedBoardId]) }}"
                            variant="filled"
                            icon="users"
                            size="sm"
                        >
                            {{ __('View Staff Details') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-2 px-6 py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 rounded-full bg-slate-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4">
                            <flux:icon name="building-office" size="lg" class="text-slate-300 dark:text-zinc-600" />
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-zinc-200 mb-1">{{ __('No additional deficit schools found') }}</h3>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 max-w-lg mx-auto">
                            {{ $recommendedNeededSchoolsNote ?: __('There are no extra schools outside this teacher\'s preferences with a matching subject and medium deficit in the selected board scope.') }}
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-8 bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-100 dark:border-zinc-800">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Workflow Timeline') }}</h3>
        </div>

        <div class="p-6 space-y-4">
            @foreach($application->policy?->steps ?? collect() as $step)
                @php
                    $decision = $this->submittedRecommendations->first(fn($recommendation) => $recommendation->workplace?->office_level_id === $step->office_level_id);
                    $stepPending = in_array($application->status, ['submitted', 'processing']) && $application->current_step === $step->step_order && !$decision;
                @endphp
                <div class="rounded-2xl border border-slate-200 dark:border-zinc-800 p-4 {{ $stepPending ? 'bg-blue-50/60 dark:bg-blue-900/15' : 'bg-slate-50/50 dark:bg-zinc-800/20' }}">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 flex items-center justify-center text-xs font-black text-slate-700 dark:text-zinc-200">
                                {{ $step->step_order }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $step->officeLevel?->office_level_name }} {{ __('Review') }}</p>
                                @if($decision)
                                    <p class="mt-2 text-sm text-slate-600 dark:text-zinc-300">{{ $decision->remarks ?: __('No remarks captured for this step.') }}</p>
                                    <p class="mt-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ $decision->approver?->name_with_initials ?? __('Official not recorded') }}</p>
                                @elseif($stepPending)
                                    <p class="mt-2 text-sm text-blue-600 dark:text-blue-300">{{ __('This application is currently waiting at this stage.') }}</p>
                                @else
                                    <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">{{ __('Queued behind earlier review steps.') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($decision)
                                @php
                                    $decisionLabel = $decision->recommendation?->decision ?? __('Completed');
                                    $decisionColor = str_contains(strtolower($decisionLabel), 'reject') ? 'rose' : 'green';
                                @endphp
                                <flux:badge :color="$decisionColor" size="sm" class="uppercase tracking-tighter font-black">{{ $decisionLabel }}</flux:badge>
                            @elseif($stepPending)
                                <flux:badge color="blue" size="sm" class="uppercase tracking-tighter font-black">{{ __('Pending') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" class="uppercase tracking-tighter font-black">{{ __('Queued') }}</flux:badge>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <flux:modal wire:model="showDecisionModal" name="decision-modal" class="w-full max-w-4xl rounded-[2rem] border border-slate-200/70 dark:border-zinc-800">
        <div class="space-y-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Make Transfer Decision') }}</flux:heading>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter font-black">{{ $application->transfer_application_id }}</flux:badge>
                    <flux:badge :color="$status['color']" size="sm" class="uppercase tracking-tighter font-black">{{ $status['label'] }}</flux:badge>
                    <flux:badge color="blue" size="sm" class="uppercase tracking-tighter font-black">{{ $application->employee?->full_name ?? __('Teacher') }}</flux:badge>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[16rem,1fr]">
                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50/70 p-5 dark:border-zinc-800 dark:bg-zinc-900/70">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">{{ __('Decision Path') }}</p>
                        <h3 class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ __('Choose the board outcome') }}</h3>
                    </div>

                    <div class="mt-5 space-y-3">
                        <button
                            type="button"
                            wire:click="$set('decisionOutcome', 'approved')"
                            class="w-full rounded-[1.25rem] border px-4 py-4 text-left transition-all duration-200 {{ $decisionOutcome === 'approved' ? 'border-emerald-500 bg-emerald-50 shadow-sm shadow-emerald-500/10 dark:border-emerald-500/70 dark:bg-emerald-500/10' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-zinc-700' }}"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-black text-slate-900 dark:text-white">{{ __('Approve') }}</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ __('Allocate a preferred school or choose another school by zone.') }}</p>
                                </div>
                                <div class="mt-0.5 rounded-xl p-2 {{ $decisionOutcome === 'approved' ? 'bg-emerald-600 text-white dark:bg-emerald-500 dark:text-emerald-950' : 'bg-slate-100 text-slate-500 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                    <flux:icon name="check-circle" size="sm" />
                                </div>
                            </div>
                        </button>

                        <button
                            type="button"
                            wire:click="$set('decisionOutcome', 'rejected')"
                            class="w-full rounded-[1.25rem] border px-4 py-4 text-left transition-all duration-200 {{ $decisionOutcome === 'rejected' ? 'border-rose-500 bg-rose-50 shadow-sm shadow-rose-500/10 dark:border-rose-500/70 dark:bg-rose-500/10' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-zinc-700' }}"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-black text-slate-900 dark:text-white">{{ __('Reject') }}</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ __('Record a reason for non-selection and provide guidance for the applicant.') }}</p>
                                </div>
                                <div class="mt-0.5 rounded-xl p-2 {{ $decisionOutcome === 'rejected' ? 'bg-rose-600 text-white dark:bg-rose-500 dark:text-rose-950' : 'bg-slate-100 text-slate-500 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                    <flux:icon name="x-circle" size="sm" />
                                </div>
                            </div>
                        </button>
                    </div>

                </div>

                <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    @if($decisionOutcome === 'approved')
                        <div class="space-y-6">
                            <div class="rounded-[1.25rem] border border-emerald-200 bg-emerald-50/80 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-emerald-700 dark:text-emerald-200">{{ __('Approval Recommendation') }}</p>
                                <p class="mt-2 text-sm leading-6 text-emerald-700 dark:text-emerald-100">
                                    {{ __('Select either one of the teacher\'s preferred schools or another school from the provincial zone and school list.') }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="md:col-span-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <button
                                        type="button"
                                        wire:click="$set('decisionSchoolSelectionType', 'preferred')"
                                        class="rounded-[1.25rem] border px-4 py-4 text-left transition-all {{ $decisionSchoolSelectionType === 'preferred' ? 'border-blue-500 bg-blue-50 shadow-sm shadow-blue-500/10 dark:border-blue-500/70 dark:bg-blue-500/10' : 'border-slate-200 bg-slate-50 hover:border-slate-300 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-zinc-700' }}"
                                    >
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ __('Preferred School') }}</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ __('Choose from the schools submitted by the teacher.') }}</p>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="$set('decisionSchoolSelectionType', 'other')"
                                        class="rounded-[1.25rem] border px-4 py-4 text-left transition-all {{ $decisionSchoolSelectionType === 'other' ? 'border-blue-500 bg-blue-50 shadow-sm shadow-blue-500/10 dark:border-blue-500/70 dark:bg-blue-500/10' : 'border-slate-200 bg-slate-50 hover:border-slate-300 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-zinc-700' }}"
                                    >
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ __('Other School') }}</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ __('Select a zone first, then select a school outside the preference list.') }}</p>
                                    </button>
                                </div>

                                @if($decisionSchoolSelectionType === 'preferred')
                                    <flux:field class="md:col-span-2">
                                        <flux:select
                                            label="{{ __('Preferred Institution For Placement') }}"
                                            wire:model.live="decisionPreferenceInstitution"
                                        >
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
                                        <flux:select
                                            label="{{ __('Zone') }}"
                                            wire:model.live="decisionOtherZoneId"
                                        >
                                            <option value="">{{ __('Select zone...') }}</option>
                                            @foreach($this->decisionZones as $zone)
                                                <option value="{{ $zone->workplace_id }}">{{ $zone->name }}</option>
                                            @endforeach
                                        </flux:select>
                                        @error('decisionOtherZoneId') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                    </flux:field>

                                    <flux:field>
                                        <flux:select
                                            label="{{ __('School') }}"
                                            wire:model.live="decisionOtherSchoolId"
                                        >
                                            <option value="">{{ __('Select school...') }}</option>
                                            @foreach($this->decisionSchools as $school)
                                                <option value="{{ $school->workplace_id }}">{{ $school->name }}</option>
                                            @endforeach
                                        </flux:select>
                                        @error('decisionOtherSchoolId') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                    </flux:field>
                                @endif

                                <flux:field class="md:col-span-2">
                                    <flux:input
                                        label="{{ __('Transfer Effective Date') }}"
                                        type="date"
                                        wire:model.live="decisionEffectiveDate"
                                    />
                                    @error('decisionEffectiveDate') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                </flux:field>

                                <flux:field class="md:col-span-2">
                                    <flux:textarea
                                        label="{{ __('Board Note') }}"
                                        wire:model.live="decisionNote"
                                        rows="4"
                                        placeholder="{{ __('Example: Approved subject to vacancy validation and final release from the current station.') }}"
                                    />
                                </flux:field>
                            </div>
                        </div>
                    @else
                        <div class="space-y-6">
                            <div class="rounded-[1.25rem] border border-rose-200 bg-rose-50/80 p-4 dark:border-rose-500/30 dark:bg-rose-500/10">
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-rose-700 dark:text-rose-200">{{ __('Rejection Communication') }}</p>
                                <p class="mt-2 text-sm leading-6 text-rose-700 dark:text-rose-100">
                                    {{ __('Capture a concise reason and a professional note so the teacher clearly understands why the request is not being supported at this stage.') }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <flux:field>
                                    <flux:select
                                        label="{{ __('Reason For Rejection') }}"
                                        wire:model.live="decisionRejectionReason"
                                    >
                                        <option value="">{{ __('Select a reason') }}</option>
                                        <option value="criteria">{{ __('Does not fulfil the required criteria') }}</option>
                                        <option value="documents">{{ __('Inadequate supporting documents') }}</option>
                                        <option value="eligibility">{{ __('Not eligible at this time') }}</option>
                                    </flux:select>
                                    @error('decisionRejectionReason') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                </flux:field>

                                <div class="rounded-[1.25rem] border border-amber-200 bg-amber-50/80 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-amber-700 dark:text-amber-200">{{ __('Appeal Guidance') }}</p>
                                    <p class="mt-2 text-sm leading-6 text-amber-700 dark:text-amber-100">
                                        {{ __('You may submit your appeal once the appeal process is activated by the transfer authority.') }}
                                    </p>
                                </div>

                                <flux:field class="md:col-span-2">
                                    <flux:textarea
                                        label="{{ __('Board Note') }}"
                                        wire:model.live="decisionNote"
                                        rows="5"
                                        placeholder="{{ __('Example: The request cannot be recommended at this stage because the required supporting documentation is incomplete.') }}"
                                    />
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
                    <flux:button
                        type="button"
                        wire:click="submitDecision"
                        class="border-transparent !bg-blue-600 !text-white hover:!bg-blue-500"
                    >
                        {{ __('Submit') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
