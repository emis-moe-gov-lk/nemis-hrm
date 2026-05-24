<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="{{ $policy->title }}"
        subtitle="Manage and monitor teacher transfer applications for this policy cycle. Coordinate board decisions and track progress."
        icon="arrows-right-left"
        :breadcrumbs="[
            'Transfer Management' => route('transfer.index-module'),
            'Control Panel' => '#'
        ]">
        <x-slot:actions>
            <flux:button href="{{ route('transfer.transfer-policy.view', $policy->policy_id) }}" variant="filled" icon="eye" size="sm" class="h-11 font-bold px-4 w-full sm:w-56 justify-center">
                {{ __('View Transfer Policy') }}
            </flux:button>
            @if($activeTab === 'appeal_boards')
            <flux:button wire:click="createAppealBoard" variant="primary" icon="plus" size="sm" class="h-11 bg-rose-600! hover:bg-rose-700! border-none text-white font-bold px-4 w-full sm:w-56 justify-center">
                {{ __('Create Appeal Board') }}
            </flux:button>
            @else
            <flux:button wire:click="createTransferBoard" variant="primary" icon="plus" size="sm" class="h-11 bg-indigo-600! hover:bg-indigo-700! border-none text-white font-bold px-4 w-full sm:w-56 justify-center">
                {{ __('Create Transfer Board') }}
            </flux:button>
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Navigation Tabs - Professional Underline Style --}}
    <div class="flex items-center gap-8 border-b border-slate-300 dark:border-zinc-700 w-full mb-8">
        <button wire:click="$set('activeTab', 'overview')"
            class="pb-4 px-1 relative group transition-all duration-300">
            <span class="text-sm font-black tracking-widest uppercase transition-colors {{ $activeTab === 'overview' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 hover:text-slate-600 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                {{ __('Overview') }}
            </span>
            @if($activeTab === 'overview')
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-600 dark:bg-indigo-400 rounded-t-full shadow-[0_-2px_10px_rgba(79,70,229,0.3)]"></div>
            @else
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-transparent group-hover:bg-slate-200 dark:group-hover:bg-zinc-700 transition-all rounded-t-full"></div>
            @endif
        </button>

        <button wire:click="$set('activeTab', 'transfer_board')"
            class="pb-4 px-1 relative group transition-all duration-300">
            <span class="text-sm font-black tracking-widest uppercase transition-colors {{ $activeTab === 'transfer_board' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 hover:text-slate-600 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                {{ __('Transfer Board') }}
            </span>
            @if($activeTab === 'transfer_board')
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-600 dark:bg-indigo-400 rounded-t-full shadow-[0_-2px_10px_rgba(79,70,229,0.3)]"></div>
            @else
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-transparent group-hover:bg-slate-200 dark:group-hover:bg-zinc-700 transition-all rounded-t-full"></div>
            @endif
        </button>

        <button wire:click="$set('activeTab', 'appeal_boards')"
            class="pb-4 px-1 relative group transition-all duration-300">
            <span class="text-sm font-black tracking-widest uppercase transition-colors {{ $activeTab === 'appeal_boards' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 hover:text-slate-600 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                {{ __('Appeal Boards') }}
            </span>
            @if($activeTab === 'appeal_boards')
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-600 dark:bg-indigo-400 rounded-t-full shadow-[0_-2px_10px_rgba(79,70,229,0.3)]"></div>
            @else
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-transparent group-hover:bg-slate-200 dark:group-hover:bg-zinc-700 transition-all rounded-t-full"></div>
            @endif
        </button>
    </div>

    @if($activeTab === 'overview')
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        {{-- Total --}}
        <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-4xl p-6 shadow-xs">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-indigo-500 opacity-[0.03]"></div>
            <div class="relative flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm">
                    <flux:icon name="users" variant="mini" class="w-7 h-7" />
                </div>
                <div>
                    <p class="text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Total Applications') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Not Recomended --}}
        <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-4xl p-6 shadow-xs">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-rose-500 opacity-[0.03]"></div>
            <div class="relative flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600 dark:text-rose-400 shadow-sm">
                    <flux:icon name="x-circle" variant="mini" class="w-7 h-7" />
                </div>
                <div>
                    <p class="text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Not Recomended') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['rejected']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Completed --}}
        <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-4xl p-6 shadow-xs">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-emerald-500 opacity-[0.03]"></div>
            <div class="relative flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-sm">
                    <flux:icon name="check-circle" variant="mini" class="w-7 h-7" />
                </div>
                <div>
                    <p class="text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Completed') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['completed']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-4xl p-6 shadow-xs">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-amber-500 opacity-[0.03]"></div>
            <div class="relative flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400 shadow-sm">
                    <flux:icon name="clock" variant="mini" class="w-7 h-7" />
                </div>
                <div>
                    <p class="text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Pending') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['pending']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Application Registry Section --}}
    <div class="space-y-8">
        <div class="flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Application Registry') }}</h2>
                <div class="h-px flex-1 bg-slate-100 dark:bg-zinc-800"></div>
            </div>

            {{-- Filter Bar --}}
            <div class="bg-slate-50/50 dark:bg-zinc-800/30 p-6 rounded-3xl border border-slate-200 dark:border-zinc-700 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <flux:field>
                    <flux:label>{{ __('Search') }}</flux:label>
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('ID or Name...') }}" icon="magnifying-glass" size="sm" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Zone') }}</flux:label>
                    <flux:select wire:model.live="filterZone" placeholder="{{ __('All Zones') }}" size="sm">
                        @foreach($zones as $zone)
                        <flux:select.option value="{{ $zone->workplace_id }}">{{ $zone->office_name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Subject') }}</flux:label>
                    <flux:select wire:model.live="filterSubject" placeholder="{{ __('All Subjects') }}" size="sm">
                        @foreach($subjects as $subject)
                        <flux:select.option value="{{ $subject->subject_id }}">{{ $subject->subject_name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Min. Service (Years)') }}</flux:label>
                    <flux:input type="number" wire:model.live="minServiceYears" placeholder="{{ __('e.g. 5') }}" size="sm" />
                </flux:field>

                <div class="flex items-end">
                    <flux:button wire:click="$set('search', ''); $set('filterZone', ''); $set('filterSubject', ''); $set('minServiceYears', null);" variant="filled" size="sm" class="text-xs font-bold uppercase tracking-widest text-slate-500 hover:text-rose-500">
                        {{ __('Reset Filters') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50">
                            <th class="pl-8 py-5 text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Application ID') }}</th>
                            <th class="px-4 py-5 text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Teacher Details') }}</th>
                            <th class="px-4 py-5 text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Current Workplace') }}</th>
                            <th class="px-4 py-5 text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Status') }}</th>
                            <th class="pr-8 py-5 text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest text-right">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-zinc-800">
                        @forelse($applications as $application)
                        <tr class="group hover:bg-slate-50/30 dark:hover:bg-zinc-800/20 transition-colors">
                            <td class="pl-8 py-5">
                                <span class="font-black text-slate-900 dark:text-white tracking-tight">{{ $application->transfer_application_id }}</span>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-500">
                                        <flux:icon name="user" variant="mini" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900 dark:text-white leading-none">{{ $application->employee?->name_with_initials ?? ($application->employee?->full_name ?? 'N/A') }}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ $application->employee->nic ?? '' }}</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400 font-black uppercase">{{ $application->teacher?->mainSubject?->name_en ?? __('No Subject') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex flex-col max-w-xs">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-zinc-300 truncate">{{ $application->currentWorkplace->office_name ?? 'N/A' }}</span>
                                    <span class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-1">{{ $application->transfer_type ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                @php
                                $color = match($application->status) {
                                'approved' => 'green',
                                'rejected' => 'red',
                                'submitted' => 'blue',
                                'processing' => 'amber',
                                default => 'zinc'
                                };
                                @endphp
                                <flux:badge color="{{ $color }}" size="sm" class="font-black uppercase tracking-widest text-[10px] px-2.5">{{ $application->status === 'rejected' ? __('Not Recomended') : ucfirst($application->status) }}</flux:badge>
                            </td>
                            <td class="pr-8 py-5 text-right">
                                <flux:button href="{{ route('transfer.teacher-transfer-application.view', $application->transfer_application_id) }}" wire:navigate variant="ghost" size="sm" icon="eye" class="text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400" />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center text-slate-300">
                                        <flux:icon name="document-text" class="w-8 h-8" />
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('No applications found') }}</h3>
                                        <p class="text-sm text-slate-500 dark:text-zinc-400">{{ __('Try adjusting your search or filters to find what you are looking for.') }}</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-200 dark:border-zinc-700">
            {{ $applications->links() }}
        </div>
    </div>
    @elseif($activeTab === 'transfer_board')
    <div class="space-y-8">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Transfer Oversight Boards') }}</h2>
            <div class="h-px flex-1 bg-slate-100 dark:bg-zinc-800"></div>
            <p class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ count($boards) }} {{ __('Active Boards') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($boards as $board)
            <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-4xl p-8 shadow-xs">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="relative">
                            <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-linear-to-br {{ $board['gradient'] }} shadow-md {{ $board['shadow'] }} text-white">
                                <flux:icon :icon="$board['icon']" variant="mini" class="w-8 h-8" />
                            </div>
                        </div>
                        <flux:badge color="{{ $board['status'] === 'closed' ? 'zinc' : 'green' }}" size="sm" class="font-black uppercase tracking-widest text-[10px] px-3 py-1">
                            {{ $board['status'] }}
                        </flux:badge>
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $board['label'] }}</h3>
                        <p class="text-sm font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-1">{{ $board['desc'] }}</p>
                        @if($board['category'] || $board['sub_category'] || $board['stage_label'])
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($board['category'])
                            <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">{{ $board['category'] }}</flux:badge>
                            @endif
                            @if($board['sub_category'])
                            <flux:badge color="blue" size="xs" class="uppercase tracking-tighter">{{ $board['sub_category'] }}</flux:badge>
                            @endif
                            @if($board['stage_label'])
                            <flux:badge color="amber" size="xs" class="uppercase tracking-tighter">{{ $board['stage_label'] }}</flux:badge>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-200 dark:border-zinc-700/50">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500">{{ $board['is_closed'] ? __('Closed Board') : __('Open Board') }}</span>
                            <flux:icon name="arrow-right" variant="micro" class="w-4 h-4 text-slate-300 group-hover:translate-x-1 transition-transform" />
                        </div>

                        <div class="flex items-center gap-2">
                            @if($board['report_route'])
                            <flux:button href="{{ $board['report_route'] }}" target="_blank" variant="ghost" icon="arrow-down-tray" size="sm">
                                {{ __('Decision PDF') }}
                            </flux:button>
                            @endif
                            <flux:button href="{{ $board['route'] }}" wire:navigate variant="primary" icon="arrow-right" size="sm">
                                {{ $board['is_closed'] ? __('View') : __('Open Board') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-24 flex flex-col items-center justify-center bg-slate-50/30 dark:bg-zinc-800/20 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-4xl">
                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-300 mb-6">
                    <flux:icon name="rectangle-stack" class="w-10 h-10" />
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">{{ __('No oversight boards found') }}</h3>
                <p class="text-slate-500 dark:text-slate-500 mb-8 max-w-sm text-center font-medium">{{ __('You haven\'t created any transfer oversight boards for this policy yet.') }}</p>
                <flux:button wire:click="createTransferBoard" variant="primary" icon="plus" class="bg-indigo-600! hover:bg-indigo-700!">
                    {{ __('Setup First Board') }}
                </flux:button>
            </div>
            @endforelse
        </div>
    </div>
    @elseif($activeTab === 'appeal_boards')
    <div class="space-y-8">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Appeal Boards') }}</h2>
            <div class="h-px flex-1 bg-slate-100 dark:bg-zinc-800"></div>
            <p class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ count($appeals) }} {{ __('Active Boards') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($appeals as $appeal)
            <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-4xl p-8 shadow-xs">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="relative">
                            <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-linear-to-br {{ $appeal['gradient'] }} shadow-md {{ $appeal['shadow'] }} text-white">
                                <flux:icon :icon="$appeal['icon']" variant="mini" class="w-8 h-8" />
                            </div>
                        </div>
                        <flux:badge color="{{ $appeal['status'] === 'closed' ? 'zinc' : 'green' }}" size="sm" class="font-black uppercase tracking-widest text-[10px] px-3 py-1">
                            {{ $appeal['status'] }}
                        </flux:badge>
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $appeal['label'] }}</h3>
                        <p class="text-sm font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-1">{{ $appeal['desc'] }}</p>
                        @if($appeal['category'] || $appeal['sub_category'] || $appeal['stage_label'])
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($appeal['category'])
                            <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">{{ $appeal['category'] }}</flux:badge>
                            @endif
                            @if($appeal['sub_category'])
                            <flux:badge color="blue" size="xs" class="uppercase tracking-tighter">{{ $appeal['sub_category'] }}</flux:badge>
                            @endif
                            @if($appeal['stage_label'])
                            <flux:badge color="amber" size="xs" class="uppercase tracking-tighter">{{ $appeal['stage_label'] }}</flux:badge>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-200 dark:border-zinc-700/50">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500">{{ $appeal['is_closed'] ? __('Closed Appeal Board') : __('Review Appeals') }}</span>
                            <flux:icon name="arrow-right" variant="micro" class="w-4 h-4 text-slate-300 group-hover:translate-x-1 transition-transform" />
                        </div>

                        <div class="flex items-center gap-2">
                            @if($appeal['report_route'])
                            <flux:button href="{{ $appeal['report_route'] }}" target="_blank" variant="ghost" icon="arrow-down-tray" size="sm">
                                {{ __('Appeal PDF') }}
                            </flux:button>
                            @endif
                            <flux:button href="{{ $appeal['route'] }}" wire:navigate variant="primary" icon="arrow-right" size="sm">
                                {{ $appeal['is_closed'] ? __('View') : __('Open Board') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-24 flex flex-col items-center justify-center bg-slate-50/30 dark:bg-zinc-800/20 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-4xl">
                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-300 mb-6">
                    <flux:icon name="chat-bubble-left-right" class="w-10 h-10" />
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">{{ __('No appeal boards found') }}</h3>
                <p class="text-slate-500 dark:text-slate-500 mb-8 max-w-sm text-center font-medium">{{ __('Appeal boards will be visible here once they are established for this policy cycle.') }}</p>
                <flux:button wire:click="createAppealBoard" variant="primary" icon="plus" class="bg-rose-600! hover:bg-rose-700! border-none text-white">
                    {{ __('Setup First Appeal Board') }}
                </flux:button>
            </div>
            @endforelse
        </div>
    </div>
    @endif
</div>
