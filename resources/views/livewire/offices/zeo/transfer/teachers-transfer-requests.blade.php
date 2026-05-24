<section class="w-full">
    <x-offices.zeo.zeo-layout :officeId="$id">
        <div class="relative mb-8 w-full">
            <flux:heading size="xl" level="1" class="text-gray-900 dark:text-white font-bold">
                {{ __('Teachers Transfer Requests') }}
            </flux:heading>
            <flux:subheading size="lg" class="mb-6 text-gray-600 dark:text-gray-300">
                {{ __('View the list of staff members and their transfer requests.') }}
            </flux:subheading>
            <flux:separator variant="subtle" />
        </div>
        <div class="mt-8 space-y-4">
            @php
            $scopeTitle = match ($requestScope) {
            'intra_zone' => __('Intra Zone Board View'),
            'all' => __('All Zonal Transfer Requests'),
            default => __('Zonal Approval Queue'),
            };
            $scopeDescription = match ($requestScope) {
            'intra_zone' => __('Intra Zone applications are shown for reference only because decisions are handled by the zonal transfer board.'),
            'all' => __('View all transfer requests in this zone, including approval-required and Intra Zone board-handled applications.'),
            default => __('Review only the teacher transfer requests that require zonal approval before provincial board processing.'),
            };
            $scopeOptions = [
            'approval_required' => __('Needs Zonal Approval'),
            'intra_zone' => __('Intra Zone - View Only'),
            'all' => __('All Requests'),
            ];
            @endphp

            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">{{ $scopeTitle }}</h3>
                    <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-500">
                        {{ $scopeDescription }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <div class="flex flex-wrap rounded-2xl border border-slate-200 bg-slate-50 p-1 shadow-sm dark:border-slate-800 dark:bg-slate-950/70">
                        @foreach ($scopeOptions as $scopeValue => $scopeLabel)
                        <button
                            type="button"
                            wire:click="$set('requestScope', '{{ $scopeValue }}')"
                            @class([ 'rounded-xl px-3 py-2 text-xs font-black uppercase tracking-[0.14em] transition' , 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20'=> $requestScope === $scopeValue,
                            'text-slate-500 hover:bg-white hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-900 dark:hover:text-white' => $requestScope !== $scopeValue,
                            ])
                            >
                            {{ $scopeLabel }}
                        </button>
                        @endforeach
                    </div>
                    <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-slate-500 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                        {{ $transferRequests->count() }} {{ __('Requests') }}
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-4xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-950/60">
                <table class="min-w-full table-fixed divide-y divide-slate-100 dark:divide-slate-800/70">
                    <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-black uppercase tracking-[0.2em] text-slate-500 dark:border-slate-800 dark:bg-slate-900/70">
                        <tr>
                            <th class="w-[28%] px-6 py-4 text-left">{{ __('Name') }}</th>
                            <th class="w-[20%] px-6 py-4 text-left">{{ __('School') }}</th>
                            <th class="w-[18%] px-6 py-4 text-left">{{ __('Transfer Category') }}</th>
                            <th class="w-[24%] px-6 py-4 text-left">{{ __('Approval Status') }}</th>
                            <th class="w-[10%] px-6 py-4 text-right">{{ __('View / Edit') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                        @forelse ($transferRequests as $key=>$request )
                        @php
                        $zonalApproval = $request->recommendations->first(function ($recommendation) {
                        return filled($recommendation->approved_by)
                        && filled($recommendation->transfer_recommendation_list_id)
                        && (bool) $recommendation->recommendation_status
                        && (bool) $recommendation->active_status;
                        });
                        $decisionText = $zonalApproval?->recommendation?->decision;
                        $hasDecision = filled($decisionText);
                        $isRejectedDecision = \Illuminate\Support\Str::contains(
                        strtolower($decisionText ?? ''),
                        ['reject', 'cannot', "can't", 'can t', 'not qualified', 'not recomemded', 'not recommended']
                        );
                        $isIntraZone = $this->isIntraZoneApplication($request);
                        $statusColor = $isIntraZone
                        ? 'sky'
                        : ($zonalApproval
                        ? ($isRejectedDecision ? 'rose' : 'emerald')
                        : 'amber');
                        $statusLabel = $isIntraZone
                        ? __('Board-handled view only')
                        : ($zonalApproval
                        ? ($hasDecision ? $decisionText : __('Zonal approval recorded'))
                        : __('Pending Zonal Approval'));
                        $categoryLabel = $request->display_category_name ?? __('N/A');
                        $schoolName = $request->currentWorkplace->office_name ?? __('N/A');
                        $schoolId = $request->currentWorkplace->workplace_id ?? '';
                        @endphp
                        <tr class="transition-colors hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5">
                            <td class="px-6 py-5 align-top">
                                <div class="flex min-w-0 items-start gap-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-sm font-black text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                        {{ $key + 1 }}
                                    </span>
                                    <div class="min-w-0">
                                        <a href="{{ route('transfer.teacher-transfer-application.view', $request->transfer_application_id) }}" wire:navigate class="block text-sm font-black leading-6 text-slate-900 transition-colors hover:text-indigo-600 dark:text-white">
                                            {{ $request->employee->name_with_initials ?? 'N/A' }}
                                        </a>
                                        <div class="mt-1 space-y-1 text-xs font-mono uppercase tracking-wider text-slate-500 dark:text-slate-500">
                                            <div>{{ $request->employee->nic ?? 'N/A' }}</div>
                                            <div>{{ $request->transfer_application_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <div class="text-sm font-black leading-6 text-slate-900 dark:text-white wrap-break-word">
                                    {{ $schoolName }}
                                </div>
                                <div class="mt-1 text-xs font-mono uppercase tracking-wider text-slate-500 dark:text-slate-500">
                                    {{ $schoolId }}
                                </div>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <div class="text-sm font-black leading-6 text-slate-900 dark:text-white wrap-break-word">
                                    {{ $categoryLabel }}
                                </div>
                                <div class="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-500">
                                    {{ $request->policy?->title ?? __('N/A') }}
                                </div>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <div @class([ 'inline-flex max-w-full items-start rounded-2xl px-3 py-2 text-sm font-black leading-5 wrap-break-word' , 'bg-emerald-500/15 text-emerald-300'=> $statusColor === 'emerald',
                                    'bg-rose-500/15 text-rose-300' => $statusColor === 'rose',
                                    'bg-amber-500/15 text-amber-300' => $statusColor === 'amber',
                                    'bg-sky-500/15 text-sky-300' => $statusColor === 'sky',
                                    ])>
                                    {{ $statusLabel }}
                                </div>

                                <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm font-semibold text-slate-500 dark:text-slate-500">
                                    @if($isIntraZone)
                                    <span>{{ __('No ZEO approval required') }}</span>
                                    <span class="text-slate-600">|</span>
                                    <span>{{ __('Handled through zonal transfer board') }}</span>
                                    @elseif($zonalApproval)
                                    <span>{{ __('By') }} {{ $zonalApproval->approver?->name_with_initials ?? __('N/A') }}</span>
                                    <span class="text-slate-600">|</span>
                                    <span>{{ $zonalApproval->updated_at?->format('M d, Y') ?? __('N/A') }}</span>
                                    @else
                                    <span>{{ __('No zonal approval recorded') }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <div class="flex flex-col items-end gap-2">
                                    <flux:button size="sm" variant="subtle" icon="eye" wire:navigate href="{{ route('transfer.teacher-transfer-application.view', $request->transfer_application_id) }}" class="rounded-lg! whitespace-nowrap">
                                        {{ __('View') }}
                                    </flux:button>
                                    @if($isIntraZone)
                                    <flux:button size="sm" variant="subtle" icon="lock-closed" disabled class="rounded-lg! whitespace-nowrap">
                                        {{ __('Board Handled') }}
                                    </flux:button>
                                    @else
                                    <flux:button size="sm" variant="{{ $zonalApproval ? 'subtle' : 'primary' }}" icon="{{ $zonalApproval ? 'pencil-square' : 'shield-check' }}" wire:click="openRecommendationModal('{{ $request->id }}')" class="rounded-lg! whitespace-nowrap {{ $zonalApproval ? 'text-emerald-600 dark:text-emerald-400' : 'border-none bg-indigo-600!' }}">
                                        {{ $zonalApproval ? __('Edit Approval') : __('Add Approval') }}
                                    </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-24">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="relative mb-6">
                                        <div class="absolute inset-0 scale-150 rounded-full bg-slate-100 opacity-50 blur-2xl dark:bg-slate-800"></div>
                                        <div class="relative rounded-full bg-linear-to-b from-slate-50 to-slate-100 p-6 shadow-inner dark:from-slate-800 dark:to-slate-900">
                                            <flux:icon name="inbox" variant="solid" class="h-16 w-16 text-slate-300 dark:text-slate-600" />
                                        </div>
                                    </div>
                                    <h4 class="text-lg font-bold uppercase tracking-tight text-slate-800 dark:text-slate-200">{{ __('No Zonal Requests Found') }}</h4>
                                    <p class="mt-2 max-w-xs text-center text-sm font-medium text-slate-500 dark:text-slate-500">
                                        {{ $requestScope === 'intra_zone'
                                        ? __('There are no Intra Zone applications available for view-only board reference.')
                                        : __('There are no teacher transfer applications within your zone currently requiring review.') }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-offices.zeo.zeo-layout>

    <!-- Recommendation Modal -->
    <flux:modal wire:model="showRecommendationModal" class="md:w-[600px] space-y-6">
        <div>
            <flux:heading size="lg">
                {{ __('Zonal Transfer Recommendation') }}
            </flux:heading>
            <flux:subheading>
                {{ __('Provide zonal-level recommendation for') }} <span class="font-bold text-slate-900 dark:text-white">{{ $selectedApplication?->employee?->full_name }}</span>
            </flux:subheading>
        </div>

        <div class="space-y-6">
            <flux:select wire:model.live="recommendationDecision" label="{{ __('Recommendation Decision') }}">
                <flux:select.option value="">{{ __('Select a decision...') }}</flux:select.option>
                @foreach($recommendationOptions as $option)
                <flux:select.option value="{{ $option->transfer_recommendation_list_id }}">
                    {{ $option->decision }}
                </flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="recommendationRemarks" label="{{ __('Remarks / Comments') }}" placeholder="{{ __('Enter additional zonal notes...') }}" rows="4" />
        </div>

        <div class="flex justify-end gap-3">
            <flux:button variant="ghost" wire:click="closeRecommendationModal">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button variant="primary" wire:click="submitRecommendation">
                {{ __('Submit Recommendation') }}
            </flux:button>
        </div>
    </flux:modal>
</section>
