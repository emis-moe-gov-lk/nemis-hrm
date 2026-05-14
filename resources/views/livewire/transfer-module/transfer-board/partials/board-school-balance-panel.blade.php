@php
    $neededSchools = collect($boardSchoolBalance['needed'] ?? []);
    $excessSchools = collect($boardSchoolBalance['excess'] ?? []);
    $schoolBalanceNote = $schoolBalanceNote ?? ($boardSchoolBalance['note'] ?? '');
@endphp

<div class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
    <div class="grid grid-cols-1 gap-4 p-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-rose-200 bg-rose-50/60 p-5 dark:border-rose-500/30 dark:bg-rose-500/10">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">{{ __('Needed Schools') }}</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">
                        {{ __('Schools where adjusted filled staff is below approved cadre for the board subject.') }}
                    </p>
                </div>
                <flux:button
                    href="{{ route('transfer.transfer-board.school-balance-report.download', ['boardId' => $selectedBoard->board_id, 'type' => 'needed']) }}"
                    target="_blank"
                    variant="filled"
                    icon="arrow-down-tray"
                    size="sm">
                    {{ __('PDF Report') }}
                </flux:button>
            </div>

            <div class="mt-4 max-h-[28rem] space-y-3 overflow-y-auto pr-1">
                @forelse($neededSchools as $row)
                    <div class="rounded-xl border border-rose-100 bg-white p-4 dark:border-rose-500/20 dark:bg-zinc-900/80">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $row['school_name'] }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $row['zone_name'] }} | {{ $row['subject_name'] }}</p>
                                <p class="mt-2 text-[11px] font-semibold text-slate-500 dark:text-zinc-400">
                                    {{ __('Approved') }} {{ $row['approved_posts'] }} |
                                    {{ __('Filled') }} {{ $row['filled_posts'] }} |
                                    {{ __('In') }} {{ $row['incoming_transfers'] }} |
                                    {{ __('Out') }} {{ $row['outgoing_transfers'] }}
                                </p>
                            </div>
                            <flux:badge color="rose" size="xs" class="uppercase tracking-tighter">
                                {{ trans_choice(':count need|:count need', $row['need_count'], ['count' => $row['need_count']]) }}
                            </flux:badge>
                        </div>
                        <div class="mt-3 space-y-2 border-t border-rose-100 pt-3 dark:border-rose-500/20">
                            @foreach(($row['medium_rows'] ?? [$row]) as $mediumRow)
                                <div class="rounded-lg bg-rose-50/70 px-3 py-2 text-[11px] dark:bg-rose-500/10">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="font-black text-slate-800 dark:text-zinc-100">{{ $mediumRow['subject_name'] }} | {{ $mediumRow['medium_name'] }}</span>
                                        <span class="font-black text-rose-700 dark:text-rose-200">{{ trans_choice(':count need|:count need', $mediumRow['need_count'], ['count' => $mediumRow['need_count']]) }}</span>
                                    </div>
                                    <p class="mt-1 font-semibold text-slate-500 dark:text-zinc-400">
                                        {{ __('Approved') }} {{ $mediumRow['approved_posts'] }} |
                                        {{ __('Filled') }} {{ $mediumRow['filled_posts'] }} |
                                        {{ __('In') }} {{ $mediumRow['incoming_transfers'] }} |
                                        {{ __('Out') }} {{ $mediumRow['outgoing_transfers'] }} |
                                        {{ __('Adjusted') }} {{ $mediumRow['adjusted_filled_posts'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                        {{ $schoolBalanceNote ?: __('No needed schools found for this board subject and scope.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-5 dark:border-amber-500/30 dark:bg-amber-500/10">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">{{ __('Excess Schools') }}</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">
                        {{ __('Schools where adjusted filled staff is above approved cadre for the board subject.') }}
                    </p>
                </div>
                <flux:button
                    href="{{ route('transfer.transfer-board.school-balance-report.download', ['boardId' => $selectedBoard->board_id, 'type' => 'excess']) }}"
                    target="_blank"
                    variant="filled"
                    icon="arrow-down-tray"
                    size="sm">
                    {{ __('PDF Report') }}
                </flux:button>
            </div>

            <div class="mt-4 max-h-[28rem] space-y-3 overflow-y-auto pr-1">
                @forelse($excessSchools as $row)
                    <div class="rounded-xl border border-amber-100 bg-white p-4 dark:border-amber-500/20 dark:bg-zinc-900/80">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $row['school_name'] }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $row['zone_name'] }} | {{ $row['subject_name'] }}</p>
                                <p class="mt-2 text-[11px] font-semibold text-slate-500 dark:text-zinc-400">
                                    {{ __('Approved') }} {{ $row['approved_posts'] }} |
                                    {{ __('Filled') }} {{ $row['filled_posts'] }} |
                                    {{ __('In') }} {{ $row['incoming_transfers'] }} |
                                    {{ __('Out') }} {{ $row['outgoing_transfers'] }}
                                </p>
                            </div>
                            <flux:badge color="amber" size="xs" class="uppercase tracking-tighter">
                                {{ trans_choice(':count excess|:count excess', $row['excess_count'], ['count' => $row['excess_count']]) }}
                            </flux:badge>
                        </div>
                        <div class="mt-3 space-y-2 border-t border-amber-100 pt-3 dark:border-amber-500/20">
                            @foreach(($row['medium_rows'] ?? [$row]) as $mediumRow)
                                <div class="rounded-lg bg-amber-50/70 px-3 py-2 text-[11px] dark:bg-amber-500/10">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="font-black text-slate-800 dark:text-zinc-100">{{ $mediumRow['subject_name'] }} | {{ $mediumRow['medium_name'] }}</span>
                                        <span class="font-black text-amber-700 dark:text-amber-200">{{ trans_choice(':count excess|:count excess', $mediumRow['excess_count'], ['count' => $mediumRow['excess_count']]) }}</span>
                                    </div>
                                    <p class="mt-1 font-semibold text-slate-500 dark:text-zinc-400">
                                        {{ __('Approved') }} {{ $mediumRow['approved_posts'] }} |
                                        {{ __('Filled') }} {{ $mediumRow['filled_posts'] }} |
                                        {{ __('In') }} {{ $mediumRow['incoming_transfers'] }} |
                                        {{ __('Out') }} {{ $mediumRow['outgoing_transfers'] }} |
                                        {{ __('Adjusted') }} {{ $mediumRow['adjusted_filled_posts'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                        {{ $schoolBalanceNote ?: __('No excess schools found for this board subject and scope.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
