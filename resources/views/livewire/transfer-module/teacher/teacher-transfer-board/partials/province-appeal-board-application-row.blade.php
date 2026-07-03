@php
    $appeal = $application;
    $transferApplication = $appeal->application;
    $teacher = $transferApplication?->teacher;
    $status = $this->statusBadge($appeal->appeal_status);
    $isIncoming = $this->isIncomingApplication($transferApplication);
    $boardDecision = $transferApplication?->boardRecommendation;
    $boardDecisionStatus = $boardDecision?->recommendation_status;
    $boardDecisionColor = $boardDecisionStatus === 'rejected' ? 'rose' : 'emerald';
    $appealDecisionColor = $appeal->appeal_status === 'approved' ? 'emerald' : ($appeal->appeal_status === 'rejected' ? 'rose' : 'amber');
@endphp

<tr class="transition-all duration-200 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20">
    <td class="px-6 py-5 align-top">
        <div class="flex min-w-0 items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-slate-300 bg-slate-100 text-xs font-bold text-slate-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                {{ $loop->iteration }}
            </div>

            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200 leading-6">
                    {{ $transferApplication?->employee?->name_with_initials ?? ($transferApplication?->employee?->full_name ?? __('Teacher')) }}
                </p>
                <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs font-mono uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                    <span>{{ $transferApplication?->employee?->nic ?? __('NIC unavailable') }}</span>
                    <span class="text-slate-300 dark:text-zinc-700">•</span>
                    <span class="font-bold">{{ $appeal->appeal_id }}</span>
                    <span class="text-slate-300 dark:text-zinc-700">•</span>
                    <span class="font-bold">{{ $appeal->transfer_application_id }}</span>
                    <span class="text-slate-300 dark:text-zinc-700">•</span>
                    <span>{{ $appeal->created_at?->format('Y-M-d') }}</span>
                </div>
                @if(filled($transferApplication?->additional_notes))
                    <flux:badge color="blue" size="xs" class="mt-2 uppercase tracking-tighter">{{ __('Additional Notes') }}</flux:badge>
                @endif
            </div>
        </div>
    </td>

    <td class="px-6 py-5 align-top">
        <div class="space-y-2">
            <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $teacher?->mainSubject?->name_en ?? __('Main subject not set') }}</p>
            <flux:badge color="blue" size="xs" class="uppercase tracking-tighter">{{ __('Board Match Subject') }}</flux:badge>
        </div>
    </td>

    <td class="px-6 py-5 align-top">
        <div class="space-y-2 text-sm text-slate-600 dark:text-zinc-300">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Secondary') }}</span>
                <p class="mt-1 font-medium">{{ $teacher?->secondarySubject?->name_en ?? __('N/A') }}</p>
            </div>

            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Current Teaching') }}</span>
                <p class="mt-1 font-medium">{{ $teacher?->currentTeachingSubject?->name_en ?? __('N/A') }}</p>
            </div>

            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Appointment') }}</span>
                <p class="mt-1 font-medium">{{ $teacher?->appointmentSubject?->name_en ?? __('N/A') }}</p>
            </div>
        </div>
    </td>

    <td class="px-6 py-5 align-top">
        <div class="space-y-2">
            <flux:badge :color="$isIncoming ? 'emerald' : 'amber'" size="sm" class="uppercase tracking-tighter font-black">
                {{ $isIncoming ? __('Incoming') : __('Outgoing') }}
            </flux:badge>

            @if($boardDecision)
                <div class="space-y-1">
                    <flux:badge :color="$boardDecisionColor" size="xs" class="uppercase tracking-tighter">
                        {{ $boardDecision->recommendationList?->decision ?? ucfirst($boardDecisionStatus) }}
                    </flux:badge>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">
                        {{ $boardDecision->selectedSchool?->name ?? __('No selected school recorded') }}
                    </p>
                </div>
            @else
                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ __('Original transfer board decision not available yet') }}</p>
            @endif
        </div>
    </td>

    <td class="px-6 py-5 align-top">
        <div class="space-y-2">
            <flux:badge :color="$status['color']" size="sm" inset="top bottom" class="capitalize">
                {{ $status['label'] }}
            </flux:badge>

            <div class="space-y-1">
                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $appeal->appeal_reason }}</p>

                @if($appeal->appeal_status !== 'pending')
                    <flux:badge :color="$appealDecisionColor" size="xs" class="uppercase tracking-tighter">
                        {{ ucfirst($appeal->appeal_status) }}
                    </flux:badge>

                    @if($appeal->selectedSchool)
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $appeal->selectedSchool->name }}</p>
                    @endif
                @endif
            </div>
        </div>
    </td>

    <td class="px-6 py-5 align-top">
        <div class="flex items-center justify-end gap-2 whitespace-nowrap">
            <flux:button
                href="{{ route('transfer-board.teacher-profile-for-appeal-board', ['id' => $appeal->appeal_id, 'board' => $boardRouteScope, 'selectedBoardId' => $selectedBoard->board_id]) }}"
                variant="{{ ($isReadOnlyScopeObserver ?? false) || $appeal->appeal_status !== 'pending' ? 'ghost' : 'primary' }}"
                icon="{{ ($isReadOnlyScopeObserver ?? false) || $appeal->appeal_status !== 'pending' ? 'eye' : 'check-circle' }}"
                size="sm">
                {{ ($isReadOnlyScopeObserver ?? false)
                    ? ($appeal->appeal_status === 'pending' ? __('View Appeal') : __('View Decision'))
                    : ($selectedBoard->isClosed() ? __('View Decision') : ($appeal->appeal_status === 'pending' ? __('Make Decision') : __('Edit Decision'))) }}
            </flux:button>

            @if($transferApplication)
                <flux:button
                    href="{{ route('transfer.teacher-transfer-application.download', ['id' => $transferApplication->transfer_application_id]) }}"
                    variant="ghost"
                    icon="arrow-down-tray"
                    size="sm" />
            @endif
        </div>
    </td>
</tr>
