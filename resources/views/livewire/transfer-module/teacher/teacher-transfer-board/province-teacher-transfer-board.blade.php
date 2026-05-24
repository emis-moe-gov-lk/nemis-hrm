<div class="p-6 lg:p-8 space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <flux:badge variant="neutral" size="sm" class="uppercase tracking-widest">{{ __('Step 1') }}</flux:badge>
                @if($showCreateBoardFlow)
                <flux:badge color="blue" size="sm" class="uppercase tracking-widest">{{ __('Create Wizard') }}</flux:badge>
                @endif
                @if($selectedBoard)
                <flux:badge color="blue" size="sm" class="uppercase tracking-widest">{{ __('Step 2 Active') }}</flux:badge>
                @endif
            </div>

            <div>
                <flux:heading size="xl" level="1">{{ __($boardPageTitle) }}</flux:heading>
                <flux:subheading size="lg">
                    {{ $selectedBoard
                        ? ($isAppealBoard
                            ? __('Review appealed applications that match the selected :scope appeal board configuration.', ['scope' => $boardScopeAdjectiveLower])
                            : __('Review transfer applications that match the selected :scope board configuration.', ['scope' => $boardScopeAdjectiveLower]))
                        : ($isAppealBoard
                            ? __('Create :scope appeal boards first, then open a board to review the matching appealed applications.', ['scope' => $boardScopeAdjectiveLower])
                            : __('Create :scope transfer boards first, then open a board to review the matching applications.', ['scope' => $boardScopeAdjectiveLower])) }}
                </flux:subheading>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($selectedBoard)
            <flux:button wire:click="backToBoardList" icon="arrow-left" variant="ghost" size="sm">{{ __('Back to Boards') }}</flux:button>
            @elseif($showCreateBoardFlow)
            <flux:button wire:click="closeCreateBoardFlow" icon="arrow-left" variant="ghost" size="sm">{{ __('Back to Boards') }}</flux:button>
            @endif

            <flux:button icon="arrow-path" wire:click="$refresh" variant="ghost" size="sm">{{ __('Refresh') }}</flux:button>

            @if($currentWorkplace && !$selectedBoard && !$showCreateBoardFlow && !$isReadOnlyScopeObserver)
            <flux:button wire:click="openCreateBoardFlow" icon="plus" variant="primary" size="sm">
                {{ $isAppealBoard ? __('Create Appeal Board') : __('Create Transfer Board') }}
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

    @if($isSuperAdmin)
    <div class="rounded-3xl border border-slate-300 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="grid grid-cols-1 gap-4 p-6 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-end">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <flux:badge color="blue" size="sm" class="uppercase tracking-widest">{{ __('Super Admin Access') }}</flux:badge>
                    <flux:badge variant="neutral" size="sm" class="uppercase tracking-widest">{{ __(':scope Scope Control', ['scope' => $boardScopeTitle]) }}</flux:badge>
                </div>
                <p class="mt-3 text-sm text-slate-600 dark:text-zinc-300">
                    {{ __('Choose the :scope you want to operate in. Board listing, create flow, and matching applications will use the selected :adjective scope.', ['scope' => $boardScopeNameLower, 'adjective' => $boardScopeAdjectiveLower]) }}
                </p>
            </div>

            <flux:field>
                <flux:select label="{{ __('Operating :scope', ['scope' => $boardScopeTitle]) }}" wire:model.live="superAdminProvinceWorkplaceId">
                    <option value="">{{ __('Select :scope...', ['scope' => $boardScopeNameLower]) }}</option>
                    @foreach($availableProvincialScopes as $scopeWorkplace)
                    <option value="{{ $scopeWorkplace->workplace_id }}">{{ $scopeWorkplace->office_name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        </div>
    </div>
    @endif

    @if(!$currentWorkplace)
    <div class="rounded-3xl border border-slate-300 bg-white p-10 text-center shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 dark:bg-zinc-800">
            <flux:icon name="shield-exclamation" class="h-10 w-10 text-slate-500 dark:text-zinc-400" />
        </div>
        <flux:heading size="lg">{{ __(':scope scope not available', ['scope' => $boardScopeTitle]) }}</flux:heading>
        <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-500 dark:text-zinc-400">
            {{ $isSuperAdmin
                    ? __('Select an operating :scope above to continue with :adjective board actions.', ['scope' => $boardScopeNameLower, 'adjective' => $boardScopeAdjectiveLower])
                    : __('This page is available only for users who currently belong to a :adjective office level. Update the user appointment hierarchy and try again.', ['adjective' => $boardScopeAdjectiveLower]) }}
        </p>
    </div>
    @elseif(!$selectedBoard && !$showCreateBoardFlow)
    <div class="rounded-3xl border border-slate-300 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-slate-200 bg-slate-50/70 p-6 dark:border-zinc-700 dark:bg-zinc-800/30">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ $isAppealBoard ? __('Available Appeal Boards') : __('Available Transfer Boards') }}</flux:heading>
                    <flux:subheading>{{ $isAppealBoard ? __('List open appeal boards or create a new one.') : __('List open transfer boards or create a new one.') }}</flux:subheading>
                </div>

                <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-2 xl:w-auto">
                    <flux:field>
                        <flux:select label="{{ __('Board Status') }}" wire:model.live="boardStatusFilter">
                            <option value="open">{{ __('Open Boards') }}</option>
                            <option value="closed">{{ __('Closed Boards') }}</option>
                            <option value="all">{{ __('All Boards') }}</option>
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:input
                            label="{{ __('Search Boards') }}"
                            wire:model.live.debounce.300ms="boardSearch"
                            icon="magnifying-glass"
                            placeholder="{{ $isAppealBoard ? __('Search by appeal board name...') : __('Search by transfer board name...') }}" />
                    </flux:field>
                </div>
            </div>
        </div>

        @php
        $boardScoreCache = collect();
        $boardScoreRange = ['min' => null, 'max' => null];

        if(!$isAppealBoard && $applications && ($applications->count() ?? 0) > 0) {
        $boardScoreCache = $applications->getCollection()
        ->mapWithKeys(function ($application) {
        $score = app(\App\Services\TransferModule\TransferApplicationScoreService::class)->score($application);

        return [$application->transfer_application_id => $score];
        });

        $scoreTotals = $boardScoreCache->pluck('total');
        $boardScoreRange = [
        'min' => $scoreTotals->min(),
        'max' => $scoreTotals->max(),
        ];
        }
        @endphp

        <div class="overflow-hidden">
            <table class="w-full table-fixed text-left">
                <colgroup>
                    <col class="w-[8%]">
                    <col class="w-[18%]">
                    <col class="w-[22%]">
                    <col class="w-[16%]">
                    <col class="w-[15%]">
                    <col class="w-[21%]">
                </colgroup>
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/50">
                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Date') }}</th>
                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Subjects') }}</th>
                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Transfer Category') }}</th>
                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Board Members') }}</th>
                        <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Status') }}</th>
                        <th class="px-4 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @forelse($boards as $board)
                    @php
                    $boardStatus = $this->boardStatusBadge($board->board_status);
                    $boardMemberCount = $board->members
                    ->filter(fn ($member) => $member->active_status && strtolower((string) $member->role) === 'member')
                    ->count();
                    @endphp
                    <tr class="transition-all duration-200 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20">
                        <td class="px-4 py-5 align-top">
                            <div class="min-w-0 space-y-1">
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $board->start_date?->format('Y-m-d') ?? __('N/A') }}</p>
                                <p class="break-words text-xs text-slate-500 dark:text-zinc-400">{{ $board->board_id }}</p>
                                <p class="break-words text-xs text-slate-500 dark:text-zinc-400">{{ $board->policy?->title ?? __('Transfer Policy') }}</p>
                            </div>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <div class="flex min-w-0 flex-wrap gap-2">
                                @forelse($board->subjects as $subject)
                                <span class="inline-flex max-w-full rounded-md bg-zinc-600/80 px-3 py-1 text-xs font-semibold uppercase leading-5 tracking-tighter text-white break-words">
                                    {{ $subject->name_en }}
                                </span>
                                @empty
                                <span class="text-sm text-slate-500 dark:text-zinc-400">{{ __('No subjects linked') }}</span>
                                @endforelse
                            </div>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <div class="min-w-0 space-y-1">
                                <p class="break-words text-sm font-semibold leading-5 text-slate-800 dark:text-zinc-200">{{ $board->display_category_name }}</p>
                                <div class="flex flex-wrap gap-2">
                                    @if(filled($board->board_stage))
                                    <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">{{ \App\Support\Transfer\TransferSubCategoryRules::displayLabelForBoardStage($board->board_stage, $board->bo_office_level_id) }}</flux:badge>
                                    @endif
                                </div>
                                <p class="break-words text-xs text-slate-500 dark:text-zinc-400">{{ $board->board_name }}</p>
                            </div>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <div class="min-w-0 space-y-2">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Chairman') }}</p>
                                    <p class="break-words text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $board->chairman?->full_name ?? __('N/A') }}</p>
                                </div>

                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Secretary') }}</p>
                                    <p class="break-words text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $board->secretary?->full_name ?? __('N/A') }}</p>
                                </div>

                                <flux:badge color="blue" size="xs" class="uppercase tracking-tighter">{{ trans_choice(':count member|:count members', $boardMemberCount, ['count' => $boardMemberCount]) }}</flux:badge>
                            </div>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <div class="space-y-2">
                                <flux:badge :color="$boardStatus['color']" size="sm" inset="top bottom" class="uppercase tracking-tighter">
                                    {{ $boardStatus['label'] }}
                                </flux:badge>

                                @if($board->isClosed())
                                <p class="break-words text-xs text-slate-500 dark:text-zinc-400">{{ __('Closed boards can only be viewed.') }}</p>
                                @else
                                <p class="break-words text-xs text-slate-500 dark:text-zinc-400">{{ __('Board configuration is still editable.') }}</p>
                                @endif
                            </div>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <div class="flex min-w-0 flex-col items-end gap-2">
                                @if(!$board->isClosed())
                                <flux:button wire:click="startEditBoard('{{ $board->board_id }}')" variant="ghost" icon="pencil-square" size="sm">
                                    {{ __('Edit') }}
                                </flux:button>

                                <flux:button wire:click="openManageMembersModal('{{ $board->board_id }}')" variant="ghost" icon="users" size="sm">
                                    {{ __('Manage Members') }}
                                </flux:button>

                                <flux:button
                                    wire:click="closeBoard('{{ $board->board_id }}')"
                                    wire:confirm="{{ $isAppealBoard ? __('Are you sure you want to close this appeal board? Closed boards can only be viewed.') : __('Are you sure you want to close this transfer board? Closed boards can only be viewed.') }}"
                                    variant="ghost"
                                    icon="lock-closed"
                                    size="sm">
                                    {{ $isAppealBoard ? __('Close Appeal Board') : __('Close Board') }}
                                </flux:button>
                                @else
                                <flux:button
                                    href="{{ route($boardDecisionReportRoute, ['boardId' => $board->board_id]) }}"
                                    target="_blank"
                                    variant="ghost"
                                    icon="arrow-down-tray"
                                    size="sm">
                                    {{ $isAppealBoard ? __('Appeal PDF') : __('Decision PDF') }}
                                </flux:button>
                                @endif

                                <flux:button wire:click="openBoard('{{ $board->board_id }}')" variant="primary" icon="arrow-right" size="sm">
                                    {{ $board->isClosed() ? __('View') : __('Open Board') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="mx-auto flex max-w-lg flex-col items-center">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 dark:bg-zinc-800/50">
                                    <flux:icon name="clipboard-document-check" class="h-10 w-10 text-slate-300 dark:text-zinc-600" />
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-zinc-200">{{ $isAppealBoard ? __('No appeal boards found') : __('No transfer boards found') }}</h3>
                                <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">
                                    {{ $isAppealBoard
                                                ? __('There are no open :scope appeal boards. Create a board to continue with the next step.', ['scope' => $boardScopeAdjectiveLower])
                                                : __('There are no open :scope transfer boards. Create a board to continue with the next step.', ['scope' => $boardScopeAdjectiveLower]) }}
                                </p>
                                <flux:button wire:click="openCreateBoardFlow" variant="primary" size="sm" class="mt-5">
                                    {{ $isAppealBoard ? __('Create Appeal Board') : __('Create Transfer Board') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-slate-50/70 p-6 dark:border-zinc-700 dark:bg-zinc-800/30">
            {{ $boards->links() }}
        </div>
    </div>
    @elseif($showCreateBoardFlow)
    @include('livewire.transfer-module.teacher.teacher-transfer-board.partials.province-create-board-flow')
    @else
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-300 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Board Date') }}</p>
            <h3 class="mt-3 text-xl font-black text-slate-900 dark:text-white">{{ $selectedBoard->start_date?->format('Y-m-d') ?? __('N/A') }}</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">{{ $selectedBoard->board_id }}</p>
        </div>

        <div class="rounded-3xl border border-slate-300 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            @php
            $selectedBoardStatus = $this->boardStatusBadge($selectedBoard->board_status);
            @endphp
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Transfer Policy') }}</p>
                <flux:badge :color="$selectedBoardStatus['color']" size="sm" class="uppercase tracking-tighter">{{ $selectedBoardStatus['label'] }}</flux:badge>
            </div>
            <h3 class="mt-3 text-xl font-black text-slate-900 dark:text-white">{{ $selectedBoard->policy?->title ?? __('N/A') }}</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">{{ $selectedBoard->display_category_name }}</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @if(filled($selectedBoard->board_stage))
                <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">{{ \App\Support\Transfer\TransferSubCategoryRules::displayLabelForBoardStage($selectedBoard->board_stage, $selectedBoard->bo_office_level_id) }}</flux:badge>
                @endif
            </div>
            @if($selectedBoard->isClosed())
            <p class="mt-3 text-xs text-slate-500 dark:text-zinc-400">{{ $isAppealBoard ? __('This appeal board is closed and can only be viewed.') : __('This board is closed and can only be viewed.') }}</p>
            @endif
        </div>

        <div class="rounded-3xl border border-slate-300 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 xl:col-span-2">
            <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Board Subjects') }}</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($selectedBoard->subjects as $subject)
                <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter">{{ $subject->name_en }}</flux:badge>
                @endforeach
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                @php
                $selectedBoardMemberCount = $selectedBoard->members
                ->filter(fn ($member) => $member->active_status && strtolower((string) $member->role) === 'member')
                ->count();
                @endphp
                <flux:badge color="blue" size="xs" class="uppercase tracking-tighter">{{ __('Chairman') }}: {{ $selectedBoard->chairman?->full_name ?? __('N/A') }}</flux:badge>
                <flux:badge color="emerald" size="xs" class="uppercase tracking-tighter">{{ __('Secretary') }}: {{ $selectedBoard->secretary?->full_name ?? __('N/A') }}</flux:badge>
                <flux:badge color="zinc" size="xs" class="uppercase tracking-tighter">{{ trans_choice(':count member|:count members', $selectedBoardMemberCount, ['count' => $selectedBoardMemberCount]) }}</flux:badge>
            </div>
        </div>
    </div>

    @if(!$isAppealBoard)
    <div class="flex flex-col gap-3 rounded-3xl border border-slate-300 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">{{ __('School Balance Analysis') }}</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">
                {{ __('Load needed and excess school analysis on demand to review school balance for this board subject and scope.') }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($showSchoolBalancePanel)
            <flux:button wire:click="hideSchoolBalancePanel" variant="ghost" icon="eye-slash" size="sm">
                {{ __('Hide School Balance') }}
            </flux:button>
            @else
            <flux:button wire:click="loadSchoolBalancePanel" variant="ghost" icon="chart-bar-square" size="sm">
                {{ __('Load School Balance') }}
            </flux:button>
            @endif
        </div>
    </div>

    @if($showSchoolBalancePanel)
    @include('livewire.transfer-module.teacher.teacher-transfer-board.partials.board-school-balance-panel')
    @endif
    @endif

    @if($isReadOnlyScopeObserver)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
        {{ $isAppealBoard
            ? __('This zonal appeal board is being viewed from the provincial level in read-only mode. Decisions, attendance changes, and board configuration are disabled.')
            : __('This zonal transfer board is being viewed from the provincial level in read-only mode. Decisions, attendance changes, and board configuration are disabled.') }}
    </div>
    @endif

    <div class="rounded-3xl border border-slate-300 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-slate-200 bg-slate-50/70 p-6 dark:border-zinc-700 dark:bg-zinc-800/30">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-1 lg:max-w-xl xl:max-w-2xl flex-1">
                    <flux:heading size="lg">{{ $isAppealBoard ? __('Matching Appealed Applications') : __('Matching Transfer Applications') }}</flux:heading>
                    <flux:subheading>{{ $isAppealBoard ? __('Appealed applications are already scoped to your :scope and then filtered by the selected board policy, category, and teacher main subject.', ['scope' => $boardScopeNameLower]) : __('Applications are already scoped to your :scope and then filtered by the selected board policy, category, and teacher main subject.', ['scope' => $boardScopeNameLower]) }}</flux:subheading>
                </div>

                <div class="flex flex-wrap items-center gap-2 lg:justify-end shrink-0">
                    @if(!$selectedBoard->isClosed() && !$isReadOnlyScopeObserver)
                    <flux:button wire:click="startEditBoard('{{ $selectedBoard->board_id }}')" variant="ghost" icon="pencil-square" size="sm">{{ __('Edit Board') }}</flux:button>
                    <flux:button wire:click="openManageMembersModal('{{ $selectedBoard->board_id }}')" variant="ghost" icon="users" size="sm">{{ __('Manage Members') }}</flux:button>
                    @endif

                    @if($isReadOnlyScopeObserver)
                    <flux:button wire:click="openAttendanceModal('{{ $selectedBoard->board_id }}', true)" variant="ghost" icon="eye" size="sm">{{ __('View Attendance') }}</flux:button>
                    @else
                    <flux:dropdown>
                        <flux:button variant="ghost" icon="clipboard-document-check" size="sm">{{ __('Attendance') }}</flux:button>
                        <flux:menu class="min-w-48">
                            <flux:menu.item wire:click="openAttendanceModal('{{ $selectedBoard->board_id }}')" icon="pencil-square">
                                {{ __('Mark Attendance') }}
                            </flux:menu.item>
                            <flux:menu.item wire:click="openAttendanceModal('{{ $selectedBoard->board_id }}', true)" icon="eye">
                                {{ __('View Attendance') }}
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                    @endif

                    @if(!$selectedBoard->isClosed() && !$isReadOnlyScopeObserver)
                    <flux:button
                        wire:click="closeBoard('{{ $selectedBoard->board_id }}')"
                        wire:confirm="{{ $isAppealBoard ? __('Are you sure you want to close this appeal board? Closed boards can only be viewed.') : __('Are you sure you want to close this transfer board? Closed boards can only be viewed.') }}"
                        variant="ghost"
                        icon="lock-closed"
                        size="sm">{{ $isAppealBoard ? __('Close Appeal Board') : __('Close Board') }}</flux:button>
                    @endif

                    @if($selectedBoard->isClosed())
                    <flux:button
                        href="{{ route($boardDecisionReportRoute, ['boardId' => $selectedBoard->board_id]) }}"
                        target="_blank"
                        variant="ghost"
                        icon="arrow-down-tray"
                        size="sm">{{ $isAppealBoard ? __('Appeal Report PDF') : __('Decision Report PDF') }}</flux:button>
                    @endif
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_220px]">
                <flux:field>
                    <flux:input
                        wire:model.live.debounce.300ms="applicationSearch"
                        icon="magnifying-glass"
                        label="{{ $isAppealBoard ? __('Search Appeals') : __('Search Applications') }}"
                        placeholder="{{ $isAppealBoard ? __('Search by appeal ID, application ID, or exact NIC...') : __('Search by application ID or exact NIC...') }}" />
                </flux:field>

                <flux:field>
                    <flux:select wire:model.live="applicationStatus" label="{{ __('Status Filter') }}">
                        <option value="">{{ __('All Statuses') }}</option>
                        @if($isAppealBoard)
                        <option value="pending">{{ __('Pending') }}</option>
                        @else
                        <option value="submitted">{{ __('Submitted') }}</option>
                        <option value="processing">{{ __('Processing') }}</option>
                        @endif
                        <option value="approved">{{ __('Approved') }}</option>
                        <option value="rejected">{{ __('Rejected') }}</option>
                    </flux:select>
                </flux:field>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/50">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ $isAppealBoard ? __('Appeal & Teacher') : __('Application & Teacher') }}</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Main Subject') }}</th>
                        @if($isAppealBoard)
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Other Subjects') }}</th>
                        @endif
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ $isAppealBoard ? __('Original Transfer Decision') : __('Transfer Flow') }}</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @if(($applications?->count() ?? 0) > 0)
                    @foreach($applications as $application)
                    @include($isAppealBoard
                    ? 'livewire.transfer-module.teacher.teacher-transfer-board.partials.province-appeal-board-application-row'
                    : 'livewire.transfer-module.teacher.teacher-transfer-board.partials.province-transfer-board-application-row')
                    @endforeach
                    @else
                    <tr>
                        <td colspan="{{ $isAppealBoard ? 6 : 5 }}" class="px-6 py-20 text-center">
                            <div class="mx-auto flex max-w-lg flex-col items-center">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 dark:bg-zinc-800/50">
                                    <flux:icon name="document-magnifying-glass" class="h-10 w-10 text-slate-300 dark:text-zinc-600" />
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-zinc-200">{{ $isAppealBoard ? __('No appeals matched this board') : __('No applications matched this board') }}</h3>
                                <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">
                                    {{ $isAppealBoard
                                                ? __('No appealed applications in your :scope scope match this board policy, category, and main subject combination.', ['scope' => $boardScopeAdjectiveLower])
                                                : __('No transfer applications in your :scope scope match this board policy, category, and main subject combination.', ['scope' => $boardScopeAdjectiveLower]) }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-slate-50/70 p-6 dark:border-zinc-700 dark:bg-zinc-800/30">
            {{ $applications->links() }}
        </div>
    </div>
    @endif

    <flux:modal wire:model="showManageMembersModal" name="manage-board-members" class="w-full max-w-5xl rounded-[2.5rem] p-8">
        <div class="space-y-8">
            <div class="space-y-2">
                <flux:heading size="lg">{{ __('Manage Board Members') }}</flux:heading>
                <flux:subheading>{{ __('Search globally by NIC to add more members to the selected board. Chairman and secretary stay attached to the board configuration.') }}</flux:subheading>
            </div>

            @if($managedBoard)
            <div class="rounded-3xl border border-slate-300 bg-slate-50/70 p-5 dark:border-zinc-700 dark:bg-zinc-800/30">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Selected Board') }}</p>
                        <p class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $managedBoard->board_name }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $managedBoard->board_id }} | {{ $managedBoard->start_date?->format('Y-m-d') ?? __('N/A') }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @foreach($managedBoard->subjects as $subject)
                        <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">{{ $subject->name_en }}</flux:badge>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($managedBoard->isClosed())
            <div class="rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
                {{ __('This transfer board is closed. Members can be viewed here, but the board composition can no longer be changed.') }}
            </div>
            @endif

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-3xl border border-slate-300 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Chairman') }}</p>
                    <p class="mt-3 text-base font-bold text-slate-900 dark:text-zinc-100">{{ $managedBoard->chairman?->full_name ?? __('N/A') }}</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $managedBoard->chairman?->nic ?? __('NIC unavailable') }}</p>
                    <p class="mt-3 text-sm text-slate-600 dark:text-zinc-300">{{ $managedBoard->chairman?->currentAppointment?->workplace?->office_name ?? __('No active workplace') }}</p>
                </div>

                <div class="rounded-3xl border border-slate-300 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Secretary') }}</p>
                    <p class="mt-3 text-base font-bold text-slate-900 dark:text-zinc-100">{{ $managedBoard->secretary?->full_name ?? __('N/A') }}</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $managedBoard->secretary?->nic ?? __('NIC unavailable') }}</p>
                    <p class="mt-3 text-sm text-slate-600 dark:text-zinc-300">{{ $managedBoard->secretary?->currentAppointment?->workplace?->office_name ?? __('No active workplace') }}</p>
                </div>
            </div>

            @if(!$managedBoard->isClosed())
            <div class="space-y-4 rounded-3xl border border-slate-300 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div>
                    <flux:heading size="base">{{ __('Add Additional Board Member') }}</flux:heading>
                    <flux:subheading>{{ __('NIC search is global and does not restrict the person lookup to your :scope scope.', ['scope' => $boardScopeAdjectiveLower]) }}</flux:subheading>
                </div>

                <div class="flex flex-col gap-3 md:flex-row md:items-end">
                    <flux:field class="flex-1">
                        <flux:input label="{{ __('Member NIC') }}" wire:model="manageMemberNic" placeholder="{{ __('Enter NIC number') }}" :invalid="$errors->has('manageMemberNic')" />
                    </flux:field>

                    <flux:button wire:click="searchManageMemberNic" variant="ghost">{{ __('Search NIC') }}</flux:button>
                    <flux:button wire:click="addManagedMember" variant="primary">{{ __('Add Member') }}</flux:button>
                </div>

                @error('manageMemberNic') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror

                @if(!empty($manageMemberCandidate))
                <div class="rounded-2xl border border-slate-300 bg-slate-50/80 p-4 dark:border-zinc-700 dark:bg-zinc-800/40">
                    <p class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ $manageMemberCandidate['full_name'] }}</p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">{{ $manageMemberCandidate['nic'] }}</p>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Current Workplace') }}</p>
                    <p class="mt-1 text-sm text-slate-800 dark:text-zinc-200">{{ $manageMemberCandidate['workplace_name'] }}</p>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Office Level') }}</p>
                    <p class="mt-1 text-sm text-slate-800 dark:text-zinc-200">{{ $manageMemberCandidate['office_level_name'] }}</p>
                </div>
                @endif
            </div>
            @endif

            @php
            $additionalMembers = $managedBoard->members->reject(fn ($member) => in_array(strtolower($member->role), ['chairman', 'secretary'], true));
            @endphp

            <div class="space-y-4 rounded-3xl border border-slate-300 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div>
                    <flux:heading size="base">{{ __('Additional Members') }}</flux:heading>
                    <flux:subheading>{{ __('Remove optional members here if the board composition changes.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    @forelse($additionalMembers as $member)
                    <div class="flex items-start justify-between rounded-2xl border border-slate-300 bg-slate-50/70 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ $member->person?->full_name ?? __('N/A') }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $member->person?->nic ?? __('NIC unavailable') }}</p>
                            <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">{{ $member->person?->currentAppointment?->workplace?->office_name ?? __('No active workplace') }}</p>
                            <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $member->person?->currentAppointment?->officeLevel?->office_level_name ?? __('No active office level') }}</p>
                        </div>

                        @if(!$managedBoard->isClosed())
                        <flux:button wire:click="removeManagedMember({{ $member->id }})" variant="ghost" icon="trash" size="sm" />
                        @endif
                    </div>
                    @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-4 text-sm text-slate-500 dark:border-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-400 md:col-span-2">
                        {{ __('No additional members have been added to this board yet.') }}
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <div class="flex flex-wrap justify-end gap-3">
                <flux:button wire:click="closeManageMembersModal" variant="ghost">{{ __('Close') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="showAttendanceModal" name="board-attendance" class="w-full max-w-6xl rounded-[2.5rem] p-8">
        <div class="space-y-8">
            <div class="space-y-2">
                <flux:heading size="lg">{{ $attendanceReadOnly ? __('View Attendance') : __('Mark Attendance') }}</flux:heading>
                <flux:subheading>
                    {{ $attendanceReadOnly
                        ? __('Review the attendance recorded for chairman, secretary, and members.')
                        : __('Mark attendance for chairman, secretary, and members. You can add optional members before saving.') }}
                </flux:subheading>
            </div>

            @if($attendanceBoard)
            <div class="rounded-3xl border border-slate-300 bg-slate-50/70 p-5 dark:border-zinc-700 dark:bg-zinc-800/30">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Selected Board') }}</p>
                        <p class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $attendanceBoard->board_name }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $attendanceBoard->board_id }} | {{ $attendanceBoard->start_date?->format('Y-m-d') ?? __('N/A') }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @foreach($attendanceBoard->subjects as $subject)
                        <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">{{ $subject->name_en }}</flux:badge>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[260px_minmax(0,1fr)]">
                <flux:field>
                    <flux:input
                        type="date"
                        label="{{ __('Attendance Date Filter') }}"
                        wire:model.live="attendanceDate"
                        :disabled="!$attendanceReadOnly && $attendanceBoard->isClosed()" />
                </flux:field>

                <div class="rounded-3xl border border-slate-300 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Board Officers') }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @php
                        $attendanceBoardMemberCount = $attendanceBoard->members
                        ->filter(fn ($member) => $member->active_status && strtolower((string) $member->role) === 'member')
                        ->count();
                        @endphp
                        <flux:badge color="blue" size="xs" class="uppercase tracking-tighter">{{ __('Chairman') }}: {{ $attendanceBoard->chairman?->full_name ?? __('N/A') }}</flux:badge>
                        <flux:badge color="emerald" size="xs" class="uppercase tracking-tighter">{{ __('Secretary') }}: {{ $attendanceBoard->secretary?->full_name ?? __('N/A') }}</flux:badge>
                        <flux:badge color="zinc" size="xs" class="uppercase tracking-tighter">{{ trans_choice(':count member|:count members', $attendanceBoardMemberCount, ['count' => $attendanceBoardMemberCount]) }}</flux:badge>
                    </div>
                </div>
            </div>

            @if(!$attendanceReadOnly && !$attendanceBoard->isClosed())
            <div class="space-y-4 rounded-3xl border border-slate-300 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div>
                    <flux:heading size="base">{{ __('Add Member While Marking Attendance') }}</flux:heading>
                    <flux:subheading>{{ __('NIC search is global and does not restrict the person lookup to your :scope scope.', ['scope' => $boardScopeAdjectiveLower]) }}</flux:subheading>
                </div>

                <div class="flex flex-col gap-3 md:flex-row md:items-end">
                    <flux:field class="flex-1">
                        <flux:input label="{{ __('Member NIC') }}" wire:model="attendanceMemberNic" placeholder="{{ __('Enter NIC number') }}" :invalid="$errors->has('attendanceMemberNic')" />
                    </flux:field>

                    <flux:button wire:click="searchAttendanceMemberNic" variant="ghost">{{ __('Search NIC') }}</flux:button>
                    <flux:button wire:click="addAttendanceMember" variant="primary">{{ __('Add Member') }}</flux:button>
                </div>

                @error('attendanceMemberNic') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror

                @if(!empty($attendanceMemberCandidate))
                <div class="rounded-2xl border border-slate-300 bg-slate-50/80 p-4 dark:border-zinc-700 dark:bg-zinc-800/40">
                    <p class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ $attendanceMemberCandidate['full_name'] }}</p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">{{ $attendanceMemberCandidate['nic'] }}</p>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Current Workplace') }}</p>
                    <p class="mt-1 text-sm text-slate-800 dark:text-zinc-200">{{ $attendanceMemberCandidate['workplace_name'] }}</p>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Office Level') }}</p>
                    <p class="mt-1 text-sm text-slate-800 dark:text-zinc-200">{{ $attendanceMemberCandidate['office_level_name'] }}</p>
                </div>
                @endif
            </div>
            @endif

            <div class="overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 dark:border-zinc-700 dark:bg-zinc-800/30">
                    <flux:heading size="base">{{ __('Attendance Register') }}</flux:heading>
                    <flux:subheading>{{ __('The date above filters attendance records. Each row can also carry its own attendance date when marking.') }}</flux:subheading>
                </div>

                @error('attendanceRows') <p class="px-5 pt-4 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror

                <div class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @forelse($attendanceRows as $row)
                    @php
                    $attendanceStatusColor = match ($row['status'] ?? 'present') {
                    'absent' => 'rose',
                    'late' => 'amber',
                    'not_marked' => 'zinc',
                    default => 'emerald',
                    };
                    @endphp

                    <div class="grid grid-cols-1 gap-4 p-5 lg:grid-cols-[minmax(0,1fr)_180px_180px_minmax(220px,0.9fr)] lg:items-start">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-black text-slate-900 dark:text-zinc-100">{{ $row['name'] }}</p>
                                <flux:badge color="blue" size="xs" class="uppercase tracking-tighter">{{ $row['role'] }}</flux:badge>
                                @if($row['has_record'])
                                <flux:badge color="emerald" size="xs" class="uppercase tracking-tighter">{{ __('Saved') }}</flux:badge>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $row['nic'] }}</p>
                            <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">{{ $row['workplace'] }}</p>
                            <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $row['office_level'] }}</p>
                        </div>

                        <div>
                            @if($attendanceReadOnly)
                            <p class="mb-2 text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Date') }}</p>
                            <p class="rounded-2xl border border-slate-300 bg-slate-50/70 p-3 text-sm font-semibold text-slate-700 dark:border-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-200">
                                {{ $row['has_record'] ? $row['attendance_date'] : __('Not marked') }}
                            </p>
                            @else
                            <flux:field>
                                <flux:input
                                    type="date"
                                    wire:model="attendanceRows.{{ $row['member_id'] }}.attendance_date"
                                    label="{{ __('Date') }}" />
                            </flux:field>
                            @endif
                        </div>

                        <div>
                            @if($attendanceReadOnly)
                            <p class="mb-2 text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Status') }}</p>
                            <flux:badge :color="$attendanceStatusColor" size="sm" class="uppercase tracking-tighter">{{ __(str_replace('_', ' ', ucfirst($row['status'] ?? 'present'))) }}</flux:badge>
                            @else
                            <flux:field>
                                <flux:select wire:model="attendanceRows.{{ $row['member_id'] }}.status" label="{{ __('Status') }}">
                                    <option value="present">{{ __('Present') }}</option>
                                    <option value="absent">{{ __('Absent') }}</option>
                                    <option value="late">{{ __('Late') }}</option>
                                </flux:select>
                            </flux:field>
                            @endif
                        </div>

                        <div>
                            @if($attendanceReadOnly)
                            <p class="mb-2 text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Remarks') }}</p>
                            <p class="rounded-2xl border border-slate-300 bg-slate-50/70 p-3 text-sm text-slate-600 dark:border-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">
                                {{ filled($row['remarks'] ?? null) ? $row['remarks'] : __('No remarks') }}
                            </p>
                            @else
                            <flux:textarea wire:model="attendanceRows.{{ $row['member_id'] }}.remarks" label="{{ __('Remarks') }}" rows="2" placeholder="{{ __('Optional notes') }}" />
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-500 dark:text-zinc-400">
                        {{ __('No board members are available to mark attendance.') }}
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <div class="flex flex-wrap justify-end gap-3">
                <flux:button wire:click="closeAttendanceModal" variant="ghost">{{ __('Close') }}</flux:button>
                @if($attendanceBoard && !$attendanceReadOnly && !$attendanceBoard->isClosed())
                <flux:button wire:click="saveAttendance" variant="primary" icon="check-circle">{{ __('Save Attendance') }}</flux:button>
                @endif
            </div>
        </div>
    </flux:modal>
</div>
