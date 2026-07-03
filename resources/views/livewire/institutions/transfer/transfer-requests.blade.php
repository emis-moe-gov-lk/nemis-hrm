<section class="w-full">
    {{-- 1. Header Section --}}
    <header class="mb-10">
        <flux:heading size="xl" level="1" class="text-3xl! font-black tracking-tight text-slate-900 dark:text-white leading-none mb-3">
            {{ __('Transfer Requests') }}
        </flux:heading>
        <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
            {{ __('Review and manage teacher transfer applications targeting this institution.') }}
        </flux:subheading>
    </header>

    <x-institutions.institution-layout :institutionId="$id" :institution="$institution" :wide="true">
        <div class="mt-8 space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">{{ __('Institution Approval Queue') }}</h3>
                    <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-500">
                        {{ __('Review transfer requests from your institution and record the institution-level approval.') }}
                    </p>
                </div>
                <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-slate-500 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                    {{ $transferRequests->count() }} {{ __('Requests') }}
                </div>
            </div>

            <div class="overflow-hidden rounded-4xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-950/60">
                <div class="hidden grid-cols-[3rem_minmax(260px,1.2fr)_minmax(230px,0.9fr)_minmax(360px,1.2fr)_minmax(180px,auto)] items-center gap-5 border-b border-slate-200 bg-slate-50/80 px-5 py-4 text-xs font-black uppercase tracking-[0.2em] text-slate-500 dark:border-slate-800 dark:bg-slate-900/70 xl:grid">
                    <div class="text-center">#</div>
                    <div>{{ __('Teacher Name') }}</div>
                    <div>{{ __('Category') }}</div>
                    <div>{{ __('Approval Status') }}</div>
                    <div class="text-right">{{ __('Actions') }}</div>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800/70">
                    @forelse ($transferRequests as $key=>$request )
                    @php
                    $institutionApproval = $request->recommendations->first(function ($recommendation) {
                    return filled($recommendation->approved_by)
                    && filled($recommendation->transfer_recommendation_list_id)
                    && (bool) $recommendation->recommendation_status
                    && (bool) $recommendation->active_status;
                    });
                    $institutionStep = $this->institutionApprovalStep($request);
                    $canEditApproval = $this->canEditInstitutionApproval($request);
                    $decisionText = $institutionApproval?->recommendation?->decision;
                    $hasDecision = filled($decisionText);
                    $isRejectedDecision = \Illuminate\Support\Str::contains(
                    strtolower($decisionText ?? ''),
                    ['reject', 'cannot', "can't", 'can t', 'not recommended']
                    );
                    $statusColor = $institutionApproval
                    ? ($isRejectedDecision ? 'rose' : 'emerald')
                    : ($canEditApproval ? 'amber' : 'slate');
                    $statusLabel = $institutionApproval
                    ? ($hasDecision ? $decisionText : __('Institution approval recorded'))
                    : ($canEditApproval ? __('Pending Institution Approval') : __('Approval Window Closed'));
                    $categoryLabel = $request->display_category_name ?? __('N/A');
                    $policyLabel = $request->policy?->title ?? __('N/A');
                    $closingLabel = $institutionStep?->end_date?->format('M d, Y') ?? __('N/A');
                    @endphp
                    <div class="grid gap-4 px-5 py-5 transition-colors hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 xl:grid-cols-[3rem_minmax(260px,1.2fr)_minmax(230px,0.9fr)_minmax(360px,1.2fr)_minmax(180px,auto)] xl:items-start">
                        <div class="flex items-center gap-3 xl:block xl:text-center">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-sm font-black text-indigo-600 shadow-inner dark:bg-indigo-900/30 dark:text-indigo-300">
                                {{ $key + 1 }}
                            </span>
                            <span class="text-xs font-black uppercase tracking-[0.18em] text-slate-500 xl:hidden">{{ __('Request') }}</span>
                        </div>

                        <div class="min-w-0">
                            <div class="flex min-w-0 items-start gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('transfer.teacher-transfer-application.view', $request->transfer_application_id) }}" wire:navigate class="block text-sm font-black leading-6 text-slate-900 transition-colors hover:text-indigo-600 dark:text-white">
                                        {{ $request->employee->title->title_name ?? '' }} {{ $request->employee->name_with_initials ?? 'N/A' }}
                                    </a>
                                    <div class="mt-1 space-y-1 text-sm font-mono uppercase tracking-wider text-slate-500 dark:text-slate-500">
                                        <div>{{ $request->employee->nic ?? 'N/A' }}</div>
                                        <div>{{ $request->transfer_application_id }}</div>
                                    </div>
                                    @if(filled($request->additional_notes))
                                    <flux:badge color="blue" size="xs" class="mt-2 uppercase tracking-tighter">{{ __('Additional Notes') }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="min-w-0">
                            <div class="text-sm font-black leading-6 text-slate-900 dark:text-white">
                                {{ $categoryLabel }}
                            </div>
                            <div class="mt-1 text-sm font-semibold leading-5 text-slate-500 dark:text-slate-500">
                                {{ $policyLabel }}
                            </div>
                        </div>

                        <div class="min-w-0">
                            <div @class([ 'inline-flex max-w-full items-start rounded-2xl px-3 py-2 text-sm font-black leading-5' , 'bg-emerald-500/15 text-emerald-300'=> $statusColor === 'emerald',
                                'bg-rose-500/15 text-rose-300' => $statusColor === 'rose',
                                'bg-amber-500/15 text-amber-300' => $statusColor === 'amber',
                                'bg-slate-500/15 text-slate-300' => $statusColor === 'slate',
                                ])>
                                {{ $statusLabel }}
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-semibold text-slate-500 dark:text-slate-500">
                                @if($institutionApproval)
                                <span>{{ __('By') }} {{ $institutionApproval->approver?->name_with_initials ?? __('N/A') }}</span>
                                <span class="text-slate-600">|</span>
                                <span>{{ $institutionApproval->updated_at?->format('M d, Y') ?? __('N/A') }}</span>
                                @else
                                <span>{{ __('No institution approval recorded') }}</span>
                                @endif
                                <span class="text-slate-600">|</span>
                                <span>{{ __('Closes') }} {{ $closingLabel }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 xl:justify-end">
                            @if($institutionApproval)
                            @if($canEditApproval)
                            <flux:button size="sm" variant="subtle" icon="pencil-square" wire:click="openRecommendationModal('{{ $request->id }}')" class="rounded-lg! whitespace-nowrap text-emerald-600 dark:text-emerald-400">
                                {{ __('Edit') }}
                            </flux:button>
                            @else
                            <flux:button size="sm" variant="subtle" icon="check-circle" wire:click="viewRecommendationModal('{{ $request->id }}')" class="rounded-lg! whitespace-nowrap">
                                {{ __('View') }}
                            </flux:button>
                            @endif
                            @elseif($canEditApproval)
                            <flux:button size="sm" variant="primary" icon="shield-check" wire:click="openRecommendationModal('{{ $request->id }}')" class="rounded-lg! border-none bg-indigo-600! whitespace-nowrap">
                                {{ __('Approve') }}
                            </flux:button>
                            @else
                            <flux:button size="sm" variant="subtle" icon="lock-closed" disabled class="rounded-lg! whitespace-nowrap opacity-60">
                                {{ __('Approval Closed') }}
                            </flux:button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="py-24">
                        <div class="flex flex-col items-center justify-center">
                            <div class="relative mb-6">
                                <div class="absolute inset-0 scale-150 rounded-full bg-slate-100 opacity-50 blur-2xl dark:bg-slate-800"></div>
                                <div class="relative rounded-full bg-linear-to-b from-slate-50 to-slate-100 p-6 shadow-inner dark:from-slate-800 dark:to-slate-900">
                                    <flux:icon name="inbox" variant="solid" class="h-16 w-16 text-slate-300 dark:text-slate-600" />
                                </div>
                            </div>
                            <h4 class="text-lg font-bold uppercase tracking-tight text-slate-800 dark:text-slate-200">{{ __('No Incoming Requests') }}</h4>
                            <p class="mt-2 max-w-xs text-center text-sm font-medium text-slate-500 dark:text-slate-500">
                                {{ __('There are no teacher transfer applications targeting this institution at the moment.') }}
                            </p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </x-institutions.institution-layout>

    <!-- Institution Approval Modal -->
    <flux:modal wire:model="showRecommendationModal" class="md:w-[600px] rounded-4xl! p-8!">
        <div class="space-y-8">
            <header>
                <div class="p-3 bg-indigo-600 rounded-2xl w-fit shadow-lg shadow-indigo-200 mb-4">
                    <flux:icon name="shield-check" variant="mini" class="text-white" />
                </div>
                <flux:heading size="xl" class="font-black tracking-tight">
                    {{ $approvalReadOnly ? __('View Institution Approval') : __('Teacher Transfer Institution Approval') }}
                </flux:heading>
                <flux:subheading class="font-medium text-slate-500">
                    {{ $approvalReadOnly ? __('Review the institution-level approval for') : __('Submit the institution-level approval for') }} <span class="text-indigo-600 dark:text-indigo-400 font-bold underline decoration-2 underline-offset-4">{{ $selectedApplication?->employee?->full_name }}</span>
                </flux:subheading>
            </header>

            @if($approvalWindowMessage)
            <div @class([ 'rounded-2xl border px-4 py-3 text-sm font-semibold' , 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200'=> $approvalReadOnly,
                'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200' => !$approvalReadOnly,
                ])>
                {{ $approvalWindowMessage }}
                @if($approvalReadOnly)
                {{ __('This approval is shown in read-only mode.') }}
                @endif
            </div>
            @endif

            @if(filled($selectedApplication?->additional_notes))
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 px-4 py-3 text-sm dark:border-indigo-500/20 dark:bg-indigo-500/10">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-indigo-600 dark:text-indigo-300">{{ __('Teacher Additional Notes') }}</p>
                <p class="mt-2 whitespace-pre-line wrap-break-word font-medium leading-relaxed text-slate-700 dark:text-zinc-200">
                    {{ $selectedApplication->additional_notes }}
                </p>
            </div>
            @endif

            <div class="space-y-6">
                <flux:select wire:model="recommendationDecision" label="{{ __('Approval Decision') }}" class="font-bold" :disabled="$approvalReadOnly">
                    <x-slot:placeholder>{{ __('Select institution approval decision...') }}</x-slot:placeholder>
                    @foreach($recommendationOptions as $option)
                    <flux:select.option value="{{ $option->transfer_recommendation_list_id }}">
                        {{ $option->decision }}
                    </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:textarea wire:model="recommendationRemarks" label="{{ __('Institution Remarks / Justification') }}" placeholder="{{ __('Enter any additional institution-level notes or justification...') }}" rows="5" class="rounded-2xl!" :disabled="$approvalReadOnly" />
            </div>

            <div class="flex gap-3">
                <flux:button variant="subtle" wire:click="closeRecommendationModal" class="flex-1 rounded-xl! py-3 font-bold uppercase tracking-widest text-[11px]">
                    {{ $approvalReadOnly ? __('Close') : __('Dismiss') }}
                </flux:button>
                @unless($approvalReadOnly)
                <flux:button variant="primary" wire:click="submitRecommendation" class="flex-2 rounded-xl! py-3 font-bold uppercase tracking-widest text-[11px] bg-indigo-600!">
                    {{ __('Submit Institution Approval') }}
                </flux:button>
                @endunless
            </div>
        </div>
    </flux:modal>
</section>
