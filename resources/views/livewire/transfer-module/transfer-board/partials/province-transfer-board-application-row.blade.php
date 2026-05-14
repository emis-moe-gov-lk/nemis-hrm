@php
    $status = $this->statusBadge($application->status);
    $isIncoming = $this->isIncomingApplication($application);
    $teacher = $application->teacher;
    $boardDecision = $application->boardRecommendation;
    $boardDecisionStatus = $boardDecision?->recommendation_status;
    $boardDecisionColor = $boardDecisionStatus === 'rejected' ? 'rose' : 'emerald';
    $transferScore = ($boardScoreCache ?? collect())->get($application->transfer_application_id)
        ?? app(\App\Services\TransferModule\TransferApplicationScoreService::class)->score($application);
    $scoreColor = $transferScore['color'];

    if (($boardScoreRange['max'] ?? null) !== null && ($boardScoreRange['max'] ?? 0) > ($boardScoreRange['min'] ?? 0)) {
        $scoreRatio = ((float) $transferScore['total'] - (float) $boardScoreRange['min'])
            / max(((float) $boardScoreRange['max'] - (float) $boardScoreRange['min']), 0.01);
        $scoreColor = match (true) {
            $scoreRatio >= 0.67 => 'emerald',
            $scoreRatio >= 0.34 => 'blue',
            (float) $transferScore['total'] > 0 => 'amber',
            default => 'zinc',
        };
    }
@endphp

<tr class="transition-all duration-200 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20">
    <td class="px-6 py-5 align-top">
        <div class="space-y-2">
            <div>
                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $application->transfer_application_id }}</p>
                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $application->created_at?->format('Y-M-d') }}</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-xs font-bold text-slate-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    {{ \Illuminate\Support\Str::substr($application->employee?->full_name ?? 'T', 0, 1) }}
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $application->employee?->full_name ?? __('Teacher') }}</p>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $application->employee?->nic ?? __('NIC unavailable') }}</p>
                </div>
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
                <span class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400 dark:text-zinc-500">{{ __('Secondary') }}</span>
                <p class="mt-1 font-medium">{{ $teacher?->secondarySubject?->name_en ?? __('N/A') }}</p>
            </div>

            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400 dark:text-zinc-500">{{ __('Current Teaching') }}</span>
                <p class="mt-1 font-medium">{{ $teacher?->currentTeachingSubject?->name_en ?? __('N/A') }}</p>
            </div>

            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400 dark:text-zinc-500">{{ __('Appointment') }}</span>
                <p class="mt-1 font-medium">{{ $teacher?->appointmentSubject?->name_en ?? __('N/A') }}</p>
            </div>
        </div>
    </td>

    <td class="px-6 py-5 align-top">
        <div class="space-y-2">
            <flux:badge :color="$isIncoming ? 'emerald' : 'amber'" size="sm" class="uppercase tracking-tighter font-black">
                {{ $isIncoming ? __('Incoming') : __('Outgoing') }}
            </flux:badge>

            <div class="space-y-1 text-sm text-slate-600 dark:text-zinc-300">
                <p><span class="font-bold text-slate-500 dark:text-zinc-400">{{ __('Current') }}:</span> {{ $application->currentWorkplace?->office_name ?? __('N/A') }}</p>
                <p><span class="font-bold text-slate-500 dark:text-zinc-400">{{ __('Destination') }}:</span> {{ $application->targetProvince?->name ?? ($application->targetProvince?->office_name ?? __('N/A')) }}</p>
            </div>
        </div>
    </td>

    <td class="px-6 py-5 align-top">
        <div class="space-y-2">
            <flux:badge :color="$status['color']" size="sm" inset="top bottom" class="capitalize">
                {{ $status['label'] }}
            </flux:badge>

            <flux:badge :color="$scoreColor" size="sm" class="uppercase tracking-tighter font-black">
                {{ __('Score') }} {{ $transferScore['formatted_total'] }}
            </flux:badge>

            @if($boardDecision)
                <div class="space-y-1">
                    <flux:badge :color="$boardDecisionColor" size="xs" class="uppercase tracking-tighter">
                        {{ $boardDecision->recommendationList?->decision ?? ucfirst($boardDecisionStatus) }}
                    </flux:badge>
                    @if($boardDecision->selectedSchool)
                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                            {{ $boardDecision->selectedSchool->name }}
                        </p>
                    @endif
                </div>
            @else
                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ __('No board decision yet') }}</p>
            @endif
        </div>
    </td>

    <td class="px-6 py-5 align-top">
        <div class="flex flex-wrap justify-end gap-2">
            <flux:button
                href="{{ route('transfer-board.teacher-profile-for-transfer-board', ['id' => $application->transfer_application_id, 'board' => $boardRouteScope, 'selectedBoardId' => $selectedBoard->board_id]) }}"
                variant="ghost"
                icon="eye"
                size="sm" />

            <flux:button
                href="{{ route('transfer-board.teacher-profile-for-transfer-board', ['id' => $application->transfer_application_id, 'board' => $boardRouteScope, 'selectedBoardId' => $selectedBoard->board_id]) }}"
                variant="{{ $boardDecision ? 'ghost' : 'primary' }}"
                icon="{{ $boardDecision ? 'pencil-square' : 'check-circle' }}"
                size="sm">
                {{ $selectedBoard->isClosed() ? __('View Decision') : ($boardDecision ? __('Edit Decision') : __('Make Decision')) }}
            </flux:button>

            <flux:button
                href="{{ route('transfer.teacher-transfer-application.download', ['id' => $application->transfer_application_id]) }}"
                variant="ghost"
                icon="arrow-down-tray"
                size="sm" />
        </div>
    </td>
</tr>
