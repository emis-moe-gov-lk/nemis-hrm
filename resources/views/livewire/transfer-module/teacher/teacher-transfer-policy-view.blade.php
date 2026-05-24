<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        :title="$policy->title"
        subtitle="{{ __('Detailed summary of transfer policy rules and approval workflow.') }}"
        icon="document-text"
        :breadcrumbs="[
            'Policies' => route('transfer.transfer-policies'),
            'View' => '#'
        ]"
    >
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <flux:button
                    x-on:click.prevent="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ route('transfer.transfer-policies') }}'; }"
                    variant="subtle"
                    icon="chevron-left"
                    class="h-11 font-bold"
                >
                    {{ __('Back') }}
                </flux:button>
                @if($canManageActions)
                    @if(!$policy->is_locked)
                        <flux:button href="{{ route('transfer.transfer-policy.edit', $policy->policy_id) }}" variant="subtle" icon="pencil-square" class="h-11 font-bold">{{ __('Edit Policy') }}</flux:button>
                    @endif

                    <flux:button wire:click="toggleLock" wire:confirm="{{ $policy->is_locked ? __('Are you sure you want to unlock this policy?') : __('Are you sure you want to lock this policy? Locked policies cannot be edited or deleted.') }}" variant="subtle" color="{{ $policy->is_locked ? 'orange' : 'slate' }}" icon="{{ $policy->is_locked ? 'lock-closed' : 'lock-open' }}" class="h-11 font-bold">{{ $policy->is_locked ? __('Unlock') : __('Lock') }}</flux:button>

                    @if(!$policy->is_locked)
                        <flux:button
                            wire:click="deletePolicy"
                            wire:confirm="{{ __('Are you sure you want to delete this policy?') }}"
                            variant="subtle"
                            color="red"
                            icon="trash"
                            class="h-11 font-bold"
                            :disabled="!$canDeletePolicy"
                        >
                            {{ __('Delete') }}
                        </flux:button>
                    @endif
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 animate-in fade-in duration-500">
            <div class="flex items-center gap-3">
                <flux:icon name="check-circle" variant="micro" class="text-green-600 dark:text-green-400" />
                <p class="text-green-800 dark:text-green-200 font-bold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 animate-in fade-in duration-500">
            <div class="flex items-center gap-3">
                <flux:icon name="x-circle" variant="micro" class="text-red-600 dark:text-red-400" />
                <p class="text-red-800 dark:text-red-200 font-bold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="flex items-center gap-2">
        <flux:badge variant="neutral" size="sm" class="uppercase tracking-widest">{{ $policy->policy_year }}</flux:badge>
        <flux:separator vertical />
        <span class="text-sm font-bold text-slate-500 uppercase tracking-widest">{{ $policy->circular_number }}</span>
    </div>

    @if($canManageActions && !$canDeletePolicy)
    <div class="mb-8 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
        {{ __('This policy cannot be deleted while it has linked transfer records.') }}
        <span class="font-semibold">
            {{ __('Applications: :applications, Boards: :boards, Appeals: :appeals', $deleteDependencyCounts) }}
        </span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Policy Details --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- General Info Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 p-8 shadow-sm">
                <flux:heading size="lg" class="mb-6 flex items-center gap-2">
                    <flux:icon name="information-circle" variant="mini" />
                    {{ __('General Information') }}
                </flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                    <div class="space-y-4">
                        <div>
                            <p class="text-slate-500 dark:text-zinc-400 mb-1 font-medium">{{ __('Transfer Type') }}</p>
                            <flux:badge variant="neutral" class="uppercase tracking-tighter">{{ __($policy->transfer_type) }}</flux:badge>
                        </div>
                        <div>
                            <p class="text-slate-500 dark:text-zinc-400 mb-1 font-medium">{{ __('Effective Date') }}</p>
                            <p class="font-bold text-slate-900 dark:text-zinc-100">{{ $policy->effective_date->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-slate-500 dark:text-zinc-400 mb-1 font-medium">{{ __('Transfer Authority') }}</p>
                            <p class="font-bold text-slate-900 dark:text-zinc-100">{{ $policy->authority->office_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 dark:text-zinc-400 mb-1 font-medium">{{ __('Status') }}</p>
                            @if($policy->active_status)
                            <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                            @else
                            <flux:badge color="zinc" size="sm">{{ __('Draft') }}</flux:badge>
                            @endif
                        </div>
                        <div>
                            <p class="text-slate-500 dark:text-zinc-400 mb-1 font-medium">{{ __('Application Period') }}</p>
                            <p class="font-bold text-slate-900 dark:text-zinc-100">
                                {{ $policy->application_start_date->format('Y-m-d') }} to {{ $policy->application_end_date->format('Y-m-d') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($policy->description)
                <div class="border-t border-slate-200 dark:border-zinc-700 mt-8 pt-6">
                    <p class="text-slate-500 dark:text-zinc-400 mb-2 font-medium">{{ __('Description') }}</p>
                    <p class="text-slate-700 dark:text-zinc-300 leading-relaxed">{{ $policy->description }}</p>
                </div>
                @endif
            </div>

            {{-- Transfer Categories Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 p-8 shadow-sm">
                <flux:heading size="lg" class="mb-6 flex items-center gap-2">
                    <flux:icon name="queue-list" variant="mini" />
                    {{ __('Transfer Categories') }}
                </flux:heading>

                <div class="space-y-4">
                    @forelse($policy->categories as $category)
                    <div class="p-4 border border-slate-200 dark:border-zinc-700 rounded-2xl bg-slate-50/50 dark:bg-zinc-800/30">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ $category->display_name }}</span>
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:badge variant="neutral" size="xs">{{ $category->officeLevel->office_level_name ?? 'N/A' }}</flux:badge>
                            </div>
                        </div>
                        @if($category->description)
                        <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">{{ $category->description }}</p>
                        @endif
                    </div>
                    @empty
                    <div class="py-12 border-2 border-dashed border-slate-200 dark:border-zinc-700 rounded-2xl flex flex-col items-center justify-center text-slate-500">
                        <flux:icon name="no-symbol" size="lg" class="mb-2" />
                        <p class="text-sm italic font-medium">{{ __('No transfer categories defined.') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Service Rules Card --}}
            <div class="bg-indigo-50/30 dark:bg-indigo-950/20 rounded-3xl border border-indigo-100 dark:border-indigo-900/50 p-8 shadow-sm">
                <flux:heading size="lg" class="mb-6 flex items-center gap-2 text-indigo-900 dark:text-indigo-100">
                    <flux:icon name="scale" variant="mini" />
                    {{ __('Minimum Service Requirements') }}
                </flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-indigo-100 dark:border-indigo-900 shadow-sm flex items-center gap-4">
                        <div class="bg-indigo-100 dark:bg-indigo-900/50 p-3 rounded-xl text-indigo-600 dark:text-indigo-400">
                            <flux:icon name="building-office-2" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Current Institution') }}</p>
                            <p class="text-2xl font-black text-slate-900 dark:text-zinc-100">{{ $policy->min_service_current_school }} <span class="text-sm font-medium text-slate-500">{{ __('Years') }}</span></p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-indigo-100 dark:border-indigo-900 shadow-sm flex items-center gap-4">
                        <div class="bg-indigo-100 dark:bg-indigo-900/50 p-3 rounded-xl text-indigo-600 dark:text-indigo-400">
                            <flux:icon name="users" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Total Service') }}</p>
                            <p class="text-2xl font-black text-slate-900 dark:text-zinc-100">{{ $policy->min_service_total }} <span class="text-sm font-medium text-slate-500">{{ __('Years') }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scoring Rules Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 p-8 shadow-sm">
                <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                    <div>
                        <flux:heading size="lg" class="flex items-center gap-2">
                            <flux:icon name="chart-bar" variant="mini" />
                            {{ __('Scoring Rules') }}
                        </flux:heading>
                        <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ __('Decision-support criteria configured for this policy.') }}</p>
                    </div>
                    <flux:badge color="blue" size="sm" class="uppercase tracking-tighter">
                        {{ trans_choice(':count enabled criterion|:count enabled criteria', $policy->scoreRules->where('active_status', true)->count(), ['count' => $policy->scoreRules->where('active_status', true)->count()]) }}
                    </flux:badge>
                </div>

                <div class="space-y-4">
                    @forelse($policy->scoreRules->where('active_status', true) as $scoreRule)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $scoreRule->criterion?->name ?? \Illuminate\Support\Str::headline($scoreRule->criteria_key) }}</p>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $scoreRule->criterion?->description }}</p>
                                </div>
                                @if($scoreRule->score_per_unit !== null)
                                    <flux:badge color="amber" size="xs">{{ __('Score / unit') }}: {{ number_format((float) $scoreRule->score_per_unit, 2) }}</flux:badge>
                                @elseif($scoreRule->base_value !== null)
                                    <flux:badge color="emerald" size="xs">{{ __('Base') }}: {{ number_format((float) $scoreRule->base_value, 0) }}</flux:badge>
                                @endif
                            </div>

                            @if(in_array($scoreRule->criteria_key, ['current_difficulty_years', 'previous_difficulty_years'], true))
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach($policy->facilityScoreRules->where('criteria_key', $scoreRule->criteria_key) as $facilityRule)
                                        <flux:badge variant="neutral" size="xs">
                                            {{ $facilityRule->facility?->name ?? $facilityRule->facilities_id }}: {{ number_format((float) $facilityRule->score_per_year, 2) }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            @endif

                            @if($scoreRule->criteria_key === 'achievements')
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach($policy->achievementLevelScores as $achievementRule)
                                        <flux:badge variant="neutral" size="xs">
                                            {{ \Illuminate\Support\Str::headline($achievementRule->achievement_level) }}: {{ number_format((float) $achievementRule->score_per_achievement, 2) }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-12 border-2 border-dashed border-slate-200 dark:border-zinc-700 rounded-2xl flex flex-col items-center justify-center text-slate-500">
                            <flux:icon name="chart-bar" size="lg" class="mb-2" />
                            <p class="text-sm italic font-medium">{{ __('No scoring rules configured for this policy.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Right Column: Approval Steps --}}
        <div>
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 p-8 shadow-sm h-full">
                <flux:heading size="lg" class="mb-8 flex items-center gap-2">
                    <flux:icon name="arrows-right-left" variant="mini" />
                    {{ __('Approval Workflow') }}
                </flux:heading>

                <div class="relative space-y-8">
                    {{-- Timeline line --}}
                    <div class="absolute left-6 top-3 bottom-3 w-px bg-slate-200 dark:bg-zinc-800"></div>

                    @foreach($policy->steps->sortBy('step_order') as $step)
                    <div class="relative pl-12">
                        {{-- Step Dot --}}
                        <div class="absolute left-6 top-2 -translate-x-1/2 w-6 h-6 rounded-full border-4 border-white dark:border-zinc-900 bg-indigo-500 z-10"></div>

                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-black text-indigo-500 uppercase tracking-tighter">{{ __('Step') }} {{ $step->step_order }}</span>
                                <flux:badge variant="neutral" size="xs" class="ml-1 px-1.5 py-0.5">{{ $step->officeLevel->office_level_rank }}</flux:badge>
                            </div>
                            <flux:heading size="md" class="mb-2">{{ $step->officeLevel->office_level_name }}</flux:heading>

                            @if($step->start_date && $step->end_date)
                            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-zinc-400">
                                <flux:icon name="calendar" size="micro" />
                                {{ $step->start_date->format('M d') }} - {{ $step->end_date->format('M d, Y') }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($policy->steps->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-slate-500">
                    <flux:icon name="no-symbol" size="lg" class="mb-2" />
                    <p class="text-sm italic font-medium">{{ __('No approval steps defined.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
