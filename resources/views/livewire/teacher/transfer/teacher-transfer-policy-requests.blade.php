<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="{{ $policy->title }}"
        subtitle="{{ __('Review your applications for this policy only. Start a new application from here when the policy window is open.') }}"
        icon="document-text"
        :breadcrumbs="[
            'Transfer Portal' => route('my-transfer'),
            'Policy Requests' => '#'
        ]"
    >
        <x-slot:actions>
            <flux:button href="{{ route('transfer.transfer-policy.view', ['id' => $policy->policy_id]) }}" wire:navigate variant="subtle" icon="eye" size="sm" class="h-11 font-bold">
                {{ __('View Policy') }}
            </flux:button>

            @if($latestApplication)
                <flux:button href="{{ route('transfer.teacher-transfer-application.view', ['id' => $latestApplication->transfer_application_id]) }}" wire:navigate variant="subtle" icon="eye" size="sm" class="h-11 font-bold">
                    {{ __('View Latest Application') }}
                </flux:button>
            @endif

            @if($editableApplication)
                <flux:button href="{{ route('my-transfer.teacher-transfer-application', ['id' => $editableApplication->transfer_application_id]) }}" wire:navigate variant="primary" icon="pencil-square" size="sm" class="h-11 font-bold bg-indigo-600! hover:bg-indigo-700! border-none text-white">
                    {{ __('Continue Draft') }}
                </flux:button>
            @endif

            @if($canStartNewApplication)
                <flux:button href="{{ route('my-transfer.teacher-transfer-application', ['policy' => $policy->policy_id]) }}" wire:navigate variant="primary" icon="plus" size="sm" class="h-11 font-bold bg-indigo-600! hover:bg-indigo-700! border-none text-white">
                    {{ __('Start New Application') }}
                </flux:button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-3 text-sm font-medium text-slate-500 dark:text-slate-500 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-300 bg-slate-50/60 px-4 py-3 dark:border-slate-700 dark:bg-zinc-900/50">
            <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-500">{{ __('Authority') }}</p>
            <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $policy->authority->office_name ?? __('N/A') }}</p>
        </div>
        <div class="rounded-2xl border border-slate-300 bg-slate-50/60 px-4 py-3 dark:border-slate-700 dark:bg-zinc-900/50">
            <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-500">{{ __('Application Window') }}</p>
            <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">
                {{ optional($policy->application_start_date)->format('M d, Y') ?? __('N/A') }}
                <span class="mx-1 text-slate-300 dark:text-slate-600">-</span>
                {{ optional($policy->application_end_date)->format('M d, Y') ?? __('N/A') }}
            </p>
        </div>
        <div class="rounded-2xl border border-slate-300 bg-slate-50/60 px-4 py-3 dark:border-slate-700 dark:bg-zinc-900/50">
            <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-500">{{ __('Transfer Type') }}</p>
            <p class="mt-1 text-sm font-bold capitalize text-slate-900 dark:text-white">{{ $policy->transfer_type }}</p>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-300 bg-white/90 p-5 shadow-sm dark:border-slate-700 dark:bg-zinc-900/80">
        @if($isApplicationWindowOpen)
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-200">
                <flux:icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0" />
                <div>
                    <p class="font-bold">{{ __('Applications are open for this policy.') }}</p>
                    <p class="mt-1">{{ __('Use this page to start a new application or continue your draft for this policy only.') }}</p>
                </div>
            </div>
        @else
            <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
                <flux:icon name="clock" class="mt-0.5 h-5 w-5 shrink-0" />
                <div>
                    <p class="font-bold">{{ __('This policy is currently view-only.') }}</p>
                    <p class="mt-1">{{ __('You can still review your requests for this policy, but new applications are only available while the active window is open.') }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div class="space-y-1">
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ __('My Requests For This Policy') }}</h2>
                <p class="text-sm font-semibold text-slate-500">{{ __('Only your applications under this selected transfer policy are shown here.') }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-4xl border border-slate-200 bg-white shadow-sm dark:border-slate-700/80 dark:bg-slate-950">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-slate-700/50 dark:bg-slate-900/50">
                            <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">{{ __('Application Details') }}</th>
                            <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">{{ __('Applied Date') }}</th>
                            <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">{{ __('Status') }}</th>
                            <th class="px-6 py-5 text-right text-[11px] font-bold uppercase tracking-widest text-slate-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        @forelse($applications as $application)
                            @php($badge = $this->statusBadge($application->status))
                            <tr class="group transition-colors hover:bg-slate-50/50 dark:hover:bg-indigo-600/50">
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition-transform group-hover:scale-110 dark:bg-indigo-500/10">
                                            <flux:icon.table-cells variant="mini" />
                                        </div>
                                        <a href="{{ route('transfer.teacher-transfer-application.view', ['id' => $application->transfer_application_id]) }}" wire:navigate class="block">
                                            <div class="text-[13px] font-bold text-slate-900 transition-colors group-hover:text-indigo-600 dark:text-white">{{ __('Application #:id', ['id' => $application->transfer_application_id]) }}</div>
                                            <div class="mt-0.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $policy->title }}</div>
                                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                                <flux:badge variant="neutral" size="xs">{{ $application->display_category_name }}</flux:badge>
                                                @if(filled($application->additional_notes))
                                                    <flux:badge color="blue" size="xs">{{ __('Additional Notes') }}</flux:badge>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-sm font-semibold text-slate-600 dark:text-slate-500">{{ $application->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-6">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest ring-4 {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }} {{ $badge['ring'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <flux:button href="{{ route('transfer.teacher-transfer-application.view', ['id' => $application->transfer_application_id]) }}" wire:navigate variant="subtle" size="sm" icon="eye" class="font-bold">
                                            {{ __('View') }}
                                        </flux:button>

                                        @if($application->is_editable)
                                            <flux:button href="{{ route('my-transfer.teacher-transfer-application', ['id' => $application->transfer_application_id]) }}" wire:navigate variant="subtle" size="sm" icon="pencil-square" class="font-bold">
                                                {{ __('Edit') }}
                                            </flux:button>
                                        @endif

                                        @if($application->status !== 'draft')
                                            <flux:button href="{{ route('transfer.teacher-transfer-application.download', ['id' => $application->transfer_application_id]) }}" variant="ghost" size="sm" icon="arrow-down-tray" class="font-bold text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10" tooltip="Download PDF"></flux:button>
                                        @endif

                                        @if($application->status === 'draft')
                                            <flux:button wire:click="deleteApplication('{{ $application->transfer_application_id }}')" wire:confirm="{{ __('Are you sure you want to delete this draft application?') }}" variant="ghost" size="sm" icon="trash" class="font-bold text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                                {{ __('Delete') }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-200 dark:bg-slate-900">
                                            <flux:icon.document-plus variant="mini" class="h-8 w-8" />
                                        </div>
                                        <div class="space-y-1">
                                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('No requests found for this policy') }}</h3>
                                            <p class="mx-auto max-w-sm text-sm font-medium text-slate-500">
                                                {{ __('You have not submitted any transfer applications under this policy yet.') }}
                                            </p>
                                        </div>

                                        @if($canStartNewApplication)
                                            <flux:button href="{{ route('my-transfer.teacher-transfer-application', ['policy' => $policy->policy_id]) }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-4 font-bold bg-indigo-600! hover:bg-indigo-700! border-none text-white">
                                                {{ __('Start New Application') }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
