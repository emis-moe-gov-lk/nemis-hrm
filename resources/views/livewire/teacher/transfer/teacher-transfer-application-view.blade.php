<div class="space-y-8 pb-12">
    @php
        $currentApprovalStep = $application->policy?->steps?->firstWhere('step_order', $application->current_step);
        $isInstitutionApprovalStep = $currentApprovalStep?->office_level_id === 'OLID006';
        $approvalActionLabel = $isInstitutionApprovalStep ? __('Submit Institution Approval') : __('Submit Recommendation');
        $approvalModalTitle = $isInstitutionApprovalStep ? __('Teacher Transfer Institution Approval') : __('Transfer Recommendation');
        $approvalModalDescription = $isInstitutionApprovalStep
            ? __('Submit the institution-level approval or release decision for this transfer application.')
            : __('Submit the formal workflow recommendation for this transfer application.');
        $isApplicantViewing = auth()->user()?->people_id === $application->employee_id;
        $backRoute = $isApplicantViewing
            ? ($application->policy_id ? route('transfer.teacher-policy.requests', ['policyId' => $application->policy_id]) : route('transfer.index-module'))
            : \App\Support\Transfer\TransferAccess::recommendationRedirectRoute(auth()->user());
    @endphp

    {{-- Header Section --}}
    <div class="bg-white dark:bg-zinc-900 rounded-xl p-8 border border-zinc-200 dark:border-zinc-700 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-400">
                <flux:icon name="document-text" class="h-6 w-6" />
            </div>

            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ __('Application Details') }}
                    </h1>
                    @php $badge = $this->statusBadge($application->status); @endphp
                    <flux:badge :color="$badge['color']" size="sm" class="font-semibold uppercase tracking-wider">
                        {{ $badge['label'] }}
                    </flux:badge>
                </div>
                <div class="flex items-center gap-4 text-sm text-zinc-400 dark:text-zinc-400">
                    <span class="font-mono bg-zinc-50 dark:bg-zinc-800/50 px-2 py-0.5 rounded border border-zinc-200 dark:border-zinc-700">#{{ $application->transfer_application_id }}</span>
                    <span class="flex items-center gap-1.5">
                        <flux:icon name="calendar" variant="mini" class="h-4 w-4" />
                        {{ __('Submitted on') }} {{ $application->created_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($this->canRecommend)
                <flux:button wire:click="openRecommendationModal" variant="primary" icon="shield-check" class="h-10 px-6 font-semibold">
                    {{ $approvalActionLabel }}
                </flux:button>
            @endif

            <flux:button href="{{ route('transfer.teacher-transfer-application.download', $application->transfer_application_id) }}" target="_blank" variant="filled" icon="arrow-down-tray" class="h-10 px-6 font-semibold">
                {{ __('Download PDF') }}
            </flux:button>

            <flux:button href="{{ $backRoute }}" wire:navigate variant="ghost" icon="chevron-left" class="h-10 px-4 font-semibold">
                {{ __('Back') }}
            </flux:button>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Applicant & Transfer Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Applicant Summary --}}
            <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 flex items-center gap-3">
                    <flux:icon name="user" class="h-4 w-4 text-zinc-400" />
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Applicant Information') }}</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Full Name') }}</p>
                        <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ ($application->employee->title->title_name ?? '') . ' ' . $application->employee->full_name }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('NIC / Employee ID') }}</p>
                        <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $application->employee->nic }} <span class="text-zinc-300 dark:text-zinc-600 mx-2">|</span> {{ $application->employee_id }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Gender') }}</p>
                        <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $application->employee->gender->gender_name ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Teaching Information --}}
            <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 flex items-center gap-3">
                    <flux:icon name="academic-cap" class="h-4 w-4 text-zinc-400" />
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Teaching Information') }}</h3>
                </div>
                @php $teacher = $application->teacher; @endphp
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Teacher Category') }}</p>
                        <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $teacher->teacherCategory->name ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Appointment Subject') }}</p>
                        <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $teacher->appointmentSubject->name_en ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Medium') }}</p>
                        <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $teacher->medium->name ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Current Teaching Subject') }}</p>
                        <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $teacher->currentTeachingSubject->name_en ?? 'N/A' }}</p>
                    </div>
                </div>
            </section>

            {{-- Transfer Details --}}
            <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 flex items-center gap-3">
                    <flux:icon name="arrows-right-left" class="h-4 w-4 text-zinc-400" />
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Transfer Details') }}</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Policy Type') }}</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $application->policy->title ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Category') }}</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $application->display_category_name }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Target Province') }}</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $application->targetProvince->name ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight">{{ __('Reason for Transfer') }}</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $application->reason->title ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($application->transfer_reason)
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-tight mb-2">{{ __('Detailed Statement') }}</p>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-medium">
                            {{ $application->transfer_reason }}
                        </p>
                    </div>
                    @endif
                </div>
            </section>

            {{-- Station Preferences --}}
            <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 flex items-center gap-3">
                    <flux:icon name="building-office-2" class="h-4 w-4 text-zinc-400" />
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Station Preferences') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-50/30 dark:bg-zinc-800/30 text-[10px] font-bold uppercase tracking-widest text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                                <th class="px-6 py-3 w-16 text-center">{{ __('Order') }}</th>
                                <th class="px-6 py-4">{{ __('Zonal Office') }}</th>
                                <th class="px-6 py-4">{{ __('Institution / School Details') }}</th>
                                <th class="px-6 py-4 text-center">{{ __('Distance') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                            @foreach($application->preferences as $pref)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-zinc-100 dark:bg-zinc-800 text-xs font-bold text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                        {{ $pref->preference_order }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $pref->zonalOffice->office_name ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        @if(isset($pref->institution) && $pref->institution->office() && $pref->institution->office()->id)
                                        <a href="{{ route('find-institutions.basic.view', $pref->institution->office()->id) }}" target="_blank" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ $pref->institution->office_name ?? 'N/A' }}
                                        </a>
                                        @else
                                        <span class="text-sm font-bold text-zinc-900 dark:text-white">
                                            {{ $pref->institution->office_name ?? 'N/A' }}
                                        </span>
                                        @endif
                                        <div class="flex items-center gap-2 text-[10px] text-zinc-400 font-medium">
                                            <span>{{ __('Census') }}: {{ str_pad($pref->institution->office()->census_no ?? '0', 5, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-xs font-semibold text-zinc-400">
                                    {{ __('Calculating...') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Workflow History --}}
            <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 flex items-center gap-3">
                    <flux:icon name="queue-list" class="h-4 w-4 text-zinc-400" />
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Workflow Timeline') }}</h3>
                </div>
                <div class="p-8">
                    <div class="relative">
                        {{-- Professional Vertical Line --}}
                        <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-zinc-200 dark:bg-zinc-800"></div>

                        @php
                        $steps = $application->policy->steps->sortBy('step_order');
                        $recommendations = $application->recommendations()->recommended()->get();
                        @endphp

                        <div class="space-y-8">
                            {{-- Initial Submission --}}
                            <div class="relative pl-12">
                                <div class="absolute left-0 w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/10 border-2 border-emerald-500 flex items-center justify-center z-10">
                                    <flux:icon name="check" class="text-emerald-600 h-5 w-5" />
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">{{ __('Application Submitted') }}</h4>
                                        <p class="text-xs text-zinc-400 dark:text-zinc-400 mt-1">{{ __('Initial Dossier received and workflow initiated.') }}</p>
                                    </div>
                                    <span class="text-xs font-mono text-zinc-400 bg-zinc-50 dark:bg-zinc-800 px-2 py-1 rounded border border-zinc-200 dark:border-zinc-700">
                                        {{ $application->created_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Workflow Steps --}}
                            @foreach($steps as $step)
                            @php
                            $rec = $recommendations->first(function($r) use ($step) {
                            return $r->workplace?->office_level_id === $step->office_level_id;
                             });
                             $isCurrent = $application->current_step == $step->step_order && ($application->status == 'submitted' || $application->status == 'processing');
                             $isFuture = $application->current_step < $step->step_order;
                             $recommendationDecisionText = strtolower($rec->recommendation?->decision ?? '');
                             $isNegativeRecommendation = Str::contains($recommendationDecisionText, ['reject', 'cannot', 'not recommended']);
                                 @endphp

                                 <div class="relative pl-12 font-medium">
                                     {{-- Status Dot/Icon --}}
                                     <div @class([ 'absolute left-0 w-10 h-10 rounded-full border-2 flex items-center justify-center z-10 bg-white dark:bg-zinc-900' , 'border-emerald-500 shadow-sm shadow-emerald-500/10'=> $rec && !$isNegativeRecommendation,
                                         'border-rose-500 shadow-sm shadow-rose-500/10' => $rec && $isNegativeRecommendation,
                                         'border-indigo-500 ring-4 ring-indigo-500/10' => $isCurrent,
                                         'bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700' => $isFuture && !$rec,
                                         ])>
                                         @if($rec)
                                         <flux:icon :name="$isNegativeRecommendation ? 'x-mark' : 'check'" class="h-5 w-5 {{ $isNegativeRecommendation ? 'text-rose-500' : 'text-emerald-500' }}" />
                                        @elseif($isCurrent)
                                        <flux:icon name="arrow-path" class="h-5 w-5 text-indigo-600 animate-spin" />
                                        @else
                                        <div class="h-2 w-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-4">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <div>
                                                <h4 @class([ 'text-sm font-bold' , 'text-zinc-400 dark:text-zinc-600'=> $isFuture && !$rec,
                                                    'text-zinc-900 dark:text-white' => !$isFuture || $rec,
                                                    ])>
                                                    {{ $step->officeLevel->office_level_name }} Review
                                                </h4>
                                                @if($rec)
                                                <p class="text-xs text-zinc-400 dark:text-zinc-400 mt-0.5">{{ $rec->workplace->office_name ?? '' }}</p>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                 @if($rec)
                                                 <flux:badge :color="$isNegativeRecommendation ? 'rose' : 'emerald'" size="sm" class="font-bold uppercase tracking-tight">
                                                    {{ $rec->recommendation?->decision }}
                                                </flux:badge>
                                                <span class="text-xs font-mono text-zinc-400 bg-zinc-50 dark:bg-zinc-800 px-2 py-1 rounded border border-zinc-200 dark:border-zinc-700">
                                                    {{ $rec->created_at->format('M d, Y H:i') }}
                                                </span>
                                                @elseif($isCurrent)
                                                <flux:badge color="indigo" size="sm" class="font-bold uppercase tracking-tight animate-pulse">{{ __('Pending Decision') }}</flux:badge>
                                                @endif
                                            </div>
                                        </div>

                                        @if($rec)
                                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/30 rounded-lg border border-zinc-200 dark:border-zinc-700 relative">
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed italic">
                                                "{{ $rec->remarks ?: __('No remarks recorded.') }}"
                                            </p>
                                            <div class="mt-3 flex items-center gap-2 text-[11px] font-bold text-zinc-400 uppercase">
                                                <flux:icon name="user" variant="mini" class="h-3.5 w-3.5" />
                                                {{ $rec->approver->name_with_initials ?? '' }}
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- Transfer Board Decision --}}
            @php
            $boardOwner = $application->category->transferOwnerWorkplace ?? null;
            $boardDecision = $application->boardRecommendation;
            $boardDecisionLabel = $boardDecision?->recommendationList?->decision ?? ($boardDecision ? ucfirst($boardDecision->recommendation_status) : null);
            $boardDecisionRejected = $boardDecision?->recommendation_status === 'rejected';

            // Determine if all recommendation steps are complete
            $maxStepOrder = isset($steps) ? $steps->max('step_order') : 0;
            $isStepsComplete = $application->current_step > $maxStepOrder || $application->status === 'approved' || $application->status === 'rejected';
            @endphp
            <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden border-t-4 border-t-indigo-600">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <flux:icon name="building-library" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Transfer Board Final Decision') }}</h3>
                    </div>
                    <div>
                        @if($boardDecision)
                        <flux:badge :color="$boardDecisionRejected ? 'rose' : 'emerald'" size="sm" class="font-bold uppercase tracking-tight px-3">
                            {{ $boardDecisionLabel }}
                        </flux:badge>
                        @elseif($isStepsComplete)
                        <flux:badge color="amber" size="sm" class="font-bold uppercase tracking-tight animate-pulse">{{ __('Awaiting Board Review') }}</flux:badge>
                        @else
                        <flux:badge color="slate" size="sm" class="font-bold uppercase tracking-tight opacity-60">{{ __('Workflow Active') }}</flux:badge>
                        @endif
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        {{-- Board Context --}}
                        <div class="lg:col-span-4 space-y-4">
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-5 border border-zinc-200 dark:border-zinc-700">
                                <p class="text-[10px] uppercase font-bold text-zinc-400 mb-1">{{ __('Presiding Authority') }}</p>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $boardOwner->office_name ?? __('N/A') }}</p>
                                <p class="text-[10px] text-zinc-400 mt-1 uppercase">{{ $boardOwner->officeLevel->office_level_name ?? '' }}</p>

                                <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                    <p class="text-[10px] uppercase font-bold text-zinc-400 mb-1">{{ __('Transfer Category') }}</p>
                                    <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ $application->display_category_name }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Decision Content --}}
                        <div class="lg:col-span-8">
                        @if($boardDecision)
                        <div class="space-y-6">
                            <div class="p-6 bg-white dark:bg-zinc-800/20 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm relative">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ __('Official Remarks') }}</span>
                                    <span class="text-[10px] font-mono text-zinc-400">{{ $boardDecision->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <p class="text-base text-zinc-700 dark:text-zinc-300 leading-relaxed font-medium">
                                    {{ $boardDecision->recommendation_remarks ?: __('Decision rendered without additional remarks.') }}
                                </p>

                                @if($boardDecision->recommendation_status === 'approved')
                                <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900/40">
                                        <p class="text-[10px] uppercase font-bold text-zinc-400 mb-1">{{ __('School Selection') }}</p>
                                        <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ \Illuminate\Support\Str::headline($boardDecision->school_selection_type ?? 'preferred') }}</p>
                                    </div>
                                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900/40">
                                        <p class="text-[10px] uppercase font-bold text-zinc-400 mb-1">{{ __('Selected Zone') }}</p>
                                        <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $boardDecision->selectedZone?->name ?? __('N/A') }}</p>
                                    </div>
                                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900/40">
                                        <p class="text-[10px] uppercase font-bold text-zinc-400 mb-1">{{ __('Selected School') }}</p>
                                        <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $boardDecision->selectedSchool?->name ?? __('N/A') }}</p>
                                        <p class="mt-2 text-[10px] uppercase font-bold text-zinc-400">{{ __('Effective Date') }}: {{ $boardDecision->transfer_effective_date?->format('M d, Y') ?? __('N/A') }}</p>
                                    </div>
                                </div>
                                @elseif($boardDecision->rejection_reason)
                                <div class="mt-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">
                                    <span class="font-bold">{{ __('Rejection Reason') }}:</span> {{ $boardDecision->rejection_reason }}
                                </div>
                                @endif
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 grayscale group hover:grayscale-0 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                            <flux:icon name="user" class="h-5 w-5 text-zinc-400" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-zinc-400 uppercase leading-none mb-1">{{ __('Reviewing Official') }}</p>
                                        <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $boardDecision->creator->name_with_initials ?? __('N/A') }}</p>
                                    </div>
                                </div>
                                <div @class([ 'px-4 py-1.5 rounded font-bold text-xs uppercase' , 'bg-emerald-100 text-emerald-700'=> !$boardDecisionRejected,
                                    'bg-rose-100 text-rose-700' => $boardDecisionRejected,
                                    ])>
                                    {{ __('Conclusion') }}: {{ $boardDecisionLabel }}
                                </div>
                            </div>
                        </div>
                            @elseif($isStepsComplete)
                            <div class="h-full flex flex-col items-center justify-center py-12 px-6 bg-amber-50 dark:bg-amber-500/5 rounded-lg border border-dashed border-amber-200 dark:border-amber-500/20">
                                <flux:icon name="clock" class="h-10 w-10 text-amber-500 mb-4" />
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white mb-1">{{ __('Evaluation in Progress') }}</h4>
                                <p class="text-xs text-zinc-400 dark:text-zinc-400 text-center max-w-sm">
                                    {{ __('The Transfer Board is currently reviewing the final recommendation. Results will be updated shortly.') }}
                                </p>
                            </div>
                            @else
                            <div class="h-full flex flex-col items-center justify-center py-12 px-6 bg-zinc-50 dark:bg-zinc-800/10 rounded-lg border border-dashed border-zinc-200 dark:border-zinc-700">
                                <flux:icon name="lock-closed" class="h-8 w-8 text-zinc-300 dark:text-zinc-600 mb-4" />
                                <p class="text-xs text-zinc-400 dark:text-zinc-400 font-medium text-center">
                                    {{ __('Workflow pending completion for board review.') }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>


        </div>
        {{-- Right Column: Service & Status --}}
        <div class="space-y-6">
            {{-- Service Details --}}
            <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 flex items-center gap-3">
                    <flux:icon name="academic-cap" class="h-4 w-4 text-zinc-400" />
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Service Portfolio') }}</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-400 uppercase">{{ __('First Appointment') }}</p>
                        <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $application->first_appointment_date ? $application->first_appointment_date->format('M d, Y') : 'N/A' }}</p>
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded text-[10px] font-bold">
                            <flux:icon name="clock" class="h-3 w-3" />
                            {{ $application->total_service_years }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-400 uppercase">{{ __('Current Station Entry') }}</p>
                        <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $application->current_workplace_join_date ? $application->current_workplace_join_date->format('M d, Y') : 'N/A' }}</p>
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded text-[10px] font-bold">
                            <flux:icon name="building-office" class="h-3 w-3" />
                            {{ $application->current_workplace_service_years }}
                        </div>
                    </div>

                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-700/60">
                        <p class="text-xs font-semibold text-zinc-400 uppercase mb-1">{{ __('Active School') }}</p>
                        <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300 leading-tight">{{ $application->currentWorkplace->office_name ?? 'N/A' }}</p>
                    </div>
                </div>
            </section>

            {{-- Appeal History --}}
            @php
            $appeals = $application->appeals;
            $latestAppeal = $appeals->sortByDesc('number_of_appeals')->first();
            @endphp
            <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden border-t-4 border-t-amber-500">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <flux:icon name="megaphone" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Appeal Process') }}</h3>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if($boardDecision)
                            <flux:badge color="{{ $this->hasPendingAppeal ? 'amber' : 'zinc' }}" size="sm" class="font-bold uppercase tracking-tight px-3">
                                {{ __('Remaining Appeals') }}: {{ $this->remainingAppeals }}
                            </flux:badge>
                        @endif

                        @if($this->canSubmitAppeal)
                            <flux:button wire:click="openAppealModal" variant="primary" size="sm" icon="plus" class="font-bold">
                                {{ __('Submit Appeal') }}
                            </flux:button>
                        @endif
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    @if(!$boardDecision)
                        <div class="rounded-2xl border border-dashed border-zinc-200 bg-zinc-50/80 px-5 py-4 text-sm text-zinc-400 dark:border-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-400">
                            {{ __('You can submit an appeal after the transfer board records a decision for this application.') }}
                        </div>
                    @elseif($this->hasPendingAppeal)
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/80 px-5 py-4 text-sm text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
                            {{ __('An appeal is already pending review. Wait until the appeal board records the current appeal decision before submitting another appeal.') }}
                        </div>
                    @elseif($this->remainingAppeals === 0)
                        <div class="rounded-2xl border border-rose-200 bg-rose-50/80 px-5 py-4 text-sm text-rose-800 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-200">
                            {{ __('The maximum number of appeals has already been reached for this application.') }}
                        </div>
                    @elseif($boardDecision)
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 px-5 py-4 text-sm text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200">
                            {{ __('If you need the appeal board to review this transfer-board decision, you can submit a formal appeal here.') }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        @forelse($appeals as $appeal)
                            @php
                                $appealBadge = $this->statusBadge($appeal->appeal_status);
                            @endphp
                            <div class="rounded-2xl border border-zinc-200 bg-zinc-50/70 p-5 dark:border-zinc-700 dark:bg-zinc-800/30">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="break-all font-mono text-sm font-bold text-zinc-900 dark:text-white">{{ $appeal->appeal_id }}</p>
                                        <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-400">{{ __('Appeal #:number', ['number' => $appeal->number_of_appeals]) }}</p>
                                    </div>
                                    <flux:badge :color="$appealBadge['color']" size="sm" class="w-fit uppercase tracking-tighter">{{ $appealBadge['label'] }}</flux:badge>
                                </div>

                                <div class="mt-4 space-y-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-zinc-400 dark:text-zinc-400">{{ __('Appeal Reason') }}</p>
                                        <p class="mt-1 whitespace-pre-line wrap-break-word leading-relaxed">{{ $appeal->appeal_reason }}</p>
                                    </div>

                                    @if($appeal->appeal_remarks)
                                        <div>
                                            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-zinc-400 dark:text-zinc-400">{{ __('Teacher Remarks') }}</p>
                                            <p class="mt-1 whitespace-pre-line wrap-break-word leading-relaxed">{{ $appeal->appeal_remarks }}</p>
                                        </div>
                                    @endif

                                    @if($appeal->decision_remarks)
                                        <div>
                                            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-zinc-400 dark:text-zinc-400">{{ __('Appeal Board Note') }}</p>
                                            <p class="mt-1 whitespace-pre-line wrap-break-word leading-relaxed">{{ $appeal->decision_remarks }}</p>
                                        </div>
                                    @endif

                                    <div class="text-xs text-zinc-400 dark:text-zinc-400">
                                        {{ __('Submitted on :date', ['date' => $appeal->created_at?->format('M d, Y') ?? __('N/A')]) }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-zinc-200 bg-zinc-50/70 px-5 py-4 text-sm text-zinc-400 dark:border-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-400">
                                {{ __('No appeals have been submitted for this application yet.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- Authenticity Statement --}}
            <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden border-l-4 border-l-emerald-500">
                <div class="p-6">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg text-emerald-600 dark:text-emerald-400">
                            <flux:icon name="shield-check" class="h-4 w-4" />
                        </div>
                        <div class="space-y-1">
                            <h4 class="font-bold text-zinc-900 dark:text-white text-xs uppercase tracking-tight">{{ __('Authenticity Statement') }}</h4>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-400 leading-relaxed">
                                {{ __('Applicant has formally declared provided data as accurate.') }}
                            </p>
                        </div>
                    </div>

                    @if($application->has_disciplinary_actions)
                    <div class="p-4 bg-rose-50 dark:bg-rose-500/10 rounded-lg border border-rose-200 dark:border-rose-500/20">
                        <div class="flex items-center gap-1.5 mb-2 text-rose-700 dark:text-rose-400">
                            <flux:icon name="exclamation-triangle" class="h-3 w-3" />
                            <span class="text-[10px] font-bold uppercase">{{ __('Disciplinary Record') }}</span>
                        </div>
                        <p class="text-xs text-rose-600 dark:text-rose-300 font-medium">
                            "{{ $application->disciplinary_actions_details }}"
                        </p>
                    </div>
                    @else
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 rounded text-[10px] font-bold text-emerald-700 dark:text-emerald-400">
                        <flux:icon name="check" class="h-3.5 w-3.5" />
                        {{ __('No Disciplinary Actions') }}
                    </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <flux:modal wire:model="showRecommendationModal" name="teacher-transfer-workflow-approval" class="w-full max-w-3xl rounded-4xl border border-zinc-200/70 dark:border-zinc-700">
        <div class="space-y-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-black uppercase tracking-[0.22em] text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                    <flux:icon name="shield-check" variant="mini" class="h-4 w-4" />
                    {{ $isInstitutionApprovalStep ? __('Institution Level') : __('Workflow Level') }}
                </div>
                <flux:heading size="lg" class="font-black! tracking-tight">{{ $approvalModalTitle }}</flux:heading>
                <flux:subheading>{{ $approvalModalDescription }}</flux:subheading>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-zinc-50/80 px-5 py-4 text-sm text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-zinc-400">{{ __('Applicant') }}</p>
                        <p class="mt-1 font-bold text-zinc-900 dark:text-white">{{ $application->employee?->full_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-zinc-400">{{ __('Current Step') }}</p>
                        <p class="mt-1 font-bold text-zinc-900 dark:text-white">{{ $currentApprovalStep?->officeLevel?->office_level_name ?? __('N/A') }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <flux:field>
                    <flux:select wire:model="recommendationDecision" label="{{ $isInstitutionApprovalStep ? __('Approval Decision') : __('Recommendation Decision') }}">
                        <x-slot:placeholder>{{ $isInstitutionApprovalStep ? __('Select institution approval decision...') : __('Select recommendation decision...') }}</x-slot:placeholder>
                        @foreach($recommendationOptions as $option)
                            <flux:select.option value="{{ $option->transfer_recommendation_list_id }}">
                                {{ $option->decision }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('recommendationDecision') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:textarea
                        label="{{ $isInstitutionApprovalStep ? __('Institution Remarks / Justification') : __('Remarks / Justification') }}"
                        wire:model.live="recommendationRemarks"
                        rows="5"
                        placeholder="{{ $isInstitutionApprovalStep ? __('Enter any institution-level approval notes or release justification.') : __('Enter any workflow recommendation notes.') }}"
                    />
                    @error('recommendationRemarks') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </flux:field>
            </div>

            <div class="flex justify-end gap-3 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="ghost" wire:click="closeRecommendationModal" type="button">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="button" wire:click="submitRecommendation" wire:loading.attr="disabled" wire:target="submitRecommendation" class="border-transparent bg-indigo-600! text-white! hover:bg-indigo-500!">
                    <span wire:loading.remove wire:target="submitRecommendation">{{ $approvalActionLabel }}</span>
                    <span wire:loading wire:target="submitRecommendation">{{ __('Submitting...') }}</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="showAppealModal" name="teacher-transfer-appeal" class="w-full max-w-3xl rounded-4xl border border-zinc-200/70 dark:border-zinc-700">
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg" class="font-black! tracking-tight">{{ __('Submit Appeal') }}</flux:heading>
                <flux:subheading>{{ __('Explain why the transfer-board decision should be reviewed by the appeal board.') }}</flux:subheading>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
                {{ __('Appeals are reviewed within the relevant appeal board workflow. Submit only if you need the official decision to be reconsidered.') }}
            </div>

            <flux:field>
                <flux:textarea
                    label="{{ __('Appeal Reason') }}"
                    wire:model.live="appealReason"
                    rows="4"
                    placeholder="{{ __('Example: The final school allocation does not match the documented station hardship and available preference options.') }}"
                />
                @error('appealReason') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
            </flux:field>

            <flux:field>
                <flux:textarea
                    label="{{ __('Additional Remarks') }}"
                    wire:model.live="appealRemarks"
                    rows="5"
                    placeholder="{{ __('Add any supporting explanation you want the appeal board to read.') }}"
                />
                @error('appealRemarks') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
            </flux:field>

            <div class="flex justify-end gap-3 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="ghost" wire:click="closeAppealModal" type="button">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="button" wire:click="submitAppeal" wire:loading.attr="disabled" wire:target="submitAppeal" class="border-transparent bg-amber-600! text-white! hover:bg-amber-500!">
                    <span wire:loading.remove wire:target="submitAppeal">{{ __('Submit Appeal') }}</span>
                    <span wire:loading wire:target="submitAppeal">{{ __('Submitting...') }}</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
