<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto space-y-8 pb-20">
    {{-- Portal Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-8 border-b border-slate-200 dark:border-slate-700">
        <div class="space-y-4">
            <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-indigo-500">
                <a href="{{ route('my-transfer') }}" wire:navigate class="hover:text-indigo-600 transition-colors">Portal</a>
                <flux:icon.chevron-right variant="micro" class="h-3 w-3 text-slate-300" />
                <span class="text-slate-500">Annual Transfer</span>
            </nav>

            <div class="space-y-1">
                <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Annual Transfer Window
                </h1>
                <p class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl leading-relaxed">
                    Manage your yearly professional transition. Review eligibility, track application progress, and explore transfer opportunities for the upcoming {{ $currentCycleYear }} academic cycle.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <flux:button href="{{ route('my-transfer.teacher-transfer-application') }}" wire:navigate variant="primary" icon="plus" size="sm" class="font-bold bg-indigo-600! hover:bg-indigo-700! border-none text-white">Apply Now</flux:button>
            <flux:button href="{{ route('my-transfer.teacher-transfer-guidelines') }}" wire:navigate variant="subtle" icon="question-mark-circle" size="sm" class="font-bold border-slate-300">Guidelines</flux:button>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="space-y-8">
        {{-- Your Applications List --}}
        <div class="space-y-8">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Your Transfer Applications</h2>
                    <p class="text-sm font-semibold text-slate-500">Track and manage your current transfer requests.</p>
                </div>
            </div>

            <div class="overflow-hidden bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700/80 rounded-4xl shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/50">
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">Application Details</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">Applied Date</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">Status</th>
                                <th class="px-6 py-5 text-right text-[11px] font-bold uppercase tracking-widest text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            @forelse($applications as $app)
                            @php
                                $badge = $this->statusBadge($app->status);
                                $policyYear = $app->policy?->policy_year ?? $app->created_at->year;
                                $policyTitle = $app->policy?->title ?? 'Annual Transfer ' . $policyYear;
                            @endphp
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-indigo-600/50 transition-colors">
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 transition-transform group-hover:scale-110">
                                            <flux:icon.table-cells variant="mini" />
                                        </div>
                                        <a href="{{ route('transfer.teacher-transfer-application.view', ['id' => $app->transfer_application_id]) }}" wire:navigate class="block group/link">
                                            <div class="text-[13px] font-bold text-slate-900 dark:text-white group-hover/link:text-indigo-600 transition-colors">{{ $policyTitle }}</div>
                                            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5 group-hover/link:text-slate-500 transition-colors">Reference: #{{ $app->transfer_application_id }} | Policy Year: {{ $policyYear }}</div>
                                            <div class="mt-2">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-500/20">
                                                    {{ $app->display_category_name }}
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-sm font-semibold text-slate-600 dark:text-slate-500">{{ $app->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-6">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }} ring-4 {{ $badge['ring'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-right space-x-2">
                                    @if($app->status !== 'draft')
                                    <flux:button href="{{ route('transfer.teacher-transfer-application.download', ['id' => $app->transfer_application_id]) }}" variant="ghost" size="sm" icon="arrow-down-tray" class="font-bold text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10" tooltip="Download PDF"></flux:button>
                                    @endif

                                    @if($app->is_editable)
                                    <flux:button href="{{ route('my-transfer.teacher-transfer-application', ['id' => $app->transfer_application_id]) }}" wire:navigate variant="subtle" size="sm" class="font-bold" icon="pencil-square">Edit</flux:button>
                                    @if($app->status === 'draft')
                                    <flux:button wire:click="deleteApplication('{{ $app->transfer_application_id }}')" wire:confirm="Are you sure you want to delete this draft application?" variant="ghost" size="sm" class="font-bold text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10" icon="trash">Delete</flux:button>
                                    @endif
                                    @else
                                    <flux:button href="{{ route('transfer.teacher-transfer-application.view', ['id' => $app->transfer_application_id]) }}" wire:navigate variant="subtle" size="sm" class="font-bold" icon="eye">View</flux:button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="h-16 w-16 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-200">
                                            <flux:icon.document-plus variant="mini" class="h-8 w-8" />
                                        </div>
                                        <div class="space-y-1">
                                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">No applications found</h3>
                                            <p class="text-sm font-medium text-slate-500 max-w-xs mx-auto">You haven't submitted any transfer requests for the {{ $currentCycleYear }} cycle yet.</p>
                                        </div>
                                        <flux:button href="{{ route('my-transfer.teacher-transfer-application') }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-4">Start New Application</flux:button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Empty State (Example if no applications exist) --}}
                {{--
                <div class="p-20 flex flex-col items-center text-center space-y-6">
                    <div class="h-20 w-20 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-300">
                        <flux:icon.document-plus variant="mini" class="h-10 w-10" />
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">No active applications found</h3>
                        <p class="text-sm font-semibold text-slate-500 max-w-xs text-center">You haven't submitted any annual transfer requests for the 2026 cycle yet.</p>
                    </div>
                </div> 
                --}}
            </div>
        </div>

    </div>
</div>
