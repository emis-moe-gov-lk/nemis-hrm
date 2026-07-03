@php
$status = $this->statusBadge($application->status);
$isIncoming = $this->isIncomingApplication($application);
$teacher = $application->teacher;
$boardDecision = collect($application->boardRecommendations ?? [])
->firstWhere('transfer_board_id', $selectedBoard->board_id)
?? $application->boardRecommendation;
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
        <div class="flex min-w-0 items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-slate-300 bg-slate-100 text-xs font-bold text-slate-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                {{ $loop->iteration }}
            </div>

            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200 leading-6">
                    {{ $application->employee?->name_with_initials ?? __('Teacher') }}
                </p>
                <p class="text-xs font-medium mt-0.5 text-slate-500 dark:text-zinc-400">{{ $application->employee?->nic ?? __('NIC unavailable') }}</p>
                <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs font-mono uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                    <span class="font-bold">{{ $application->transfer_application_id }}</span>
                    <span class="text-slate-300 dark:text-zinc-700">•</span>
                    <span>{{ $application->created_at?->format('Y-M-d') }}</span>
                </div>
                @if(filled($application->additional_notes))
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
        <div class="space-y-2 gap-2">
            <flux:badge :color="$status['color']" size="sm" inset="top bottom" class="capitalize mb-1">
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
        <div class="flex items-center justify-end gap-2 whitespace-nowrap">
            <flux:button
                href="{{ route('transfer-board.teacher-profile-for-transfer-board', ['id' => $application->transfer_application_id, 'board' => $boardRouteScope, 'selectedBoardId' => $selectedBoard->board_id]) }}"
                variant="{{ ($isReadOnlyScopeObserver ?? false) || $boardDecision ? 'ghost' : 'primary' }}"
                icon="{{ ($isReadOnlyScopeObserver ?? false) || $boardDecision ? 'eye' : 'check-circle' }}"
                size="sm">
                {{ ($isReadOnlyScopeObserver ?? false)
                    ? ($boardDecision ? __('View Decision') : __('View Application'))
                    : ($selectedBoard->isClosed() ? __('View Decision') : ($boardDecision ? __('Edit Decision') : __('Make Decision'))) }}
            </flux:button>

            <flux:button
                href="{{ route('transfer.teacher-transfer-application.download', ['id' => $application->transfer_application_id]) }}"
                variant="ghost"
                icon="arrow-down-tray"
                size="sm" />
        </div>
    </td>
</tr>
