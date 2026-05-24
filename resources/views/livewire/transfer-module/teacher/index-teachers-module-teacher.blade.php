<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Teacher Transfer Portal"
        subtitle="Find the right policy and manage your own transfer requests. Open a policy to see only your requests for that transfer cycle."
        icon="arrows-right-left"
        :breadcrumbs="[
            'Transfer Portal' => route('my-transfer')
        ]"
    ></x-page-header>

    <div class="space-y-8">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ __('Active Transfer Policies') }}</h2>
            <div class="h-px flex-1 bg-slate-100 dark:bg-zinc-800"></div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 xl:grid-cols-3">
            @forelse($activePolicies as $policy)
                <a href="{{ route('transfer.teacher-policy.requests', ['policyId' => $policy->policy_id]) }}" wire:navigate
                    class="group relative overflow-hidden rounded-[2.5rem] border border-slate-300/60 bg-slate-50/50 p-8 transition-all duration-500 hover:-translate-y-2 hover:border-transparent hover:bg-white hover:shadow-[0_30px_70px_-20px_rgba(0,0,0,0.15)] dark:border-zinc-700 dark:bg-zinc-900/50 dark:hover:bg-zinc-900">
                    <div class="absolute inset-0 -z-10 rounded-[2.5rem] bg-linear-to-br from-indigo-500 to-blue-600 p-px opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="absolute inset-px -z-10 rounded-[2.45rem] bg-inherit"></div>
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-linear-to-br from-indigo-500 to-blue-600 opacity-[0.05] blur-3xl transition-opacity duration-700 group-hover:opacity-[0.15]"></div>

                    <div class="relative flex h-full flex-col">
                        <div class="mb-8 flex items-center justify-between">
                            <div class="relative">
                                <div class="absolute inset-0 scale-150 rounded-full bg-linear-to-br from-indigo-500 to-blue-600 opacity-20 blur-2xl transition-opacity duration-500 group-hover:opacity-40"></div>
                                <div class="relative inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 text-white shadow-lg shadow-indigo-200 transition-all duration-500 group-hover:rotate-3 group-hover:scale-110">
                                    <flux:icon name="document-text" variant="mini" class="h-7 w-7" />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <flux:badge variant="neutral" size="sm" class="font-black uppercase tracking-widest">{{ $policy->policy_year }}</flux:badge>
                                <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                            </div>
                        </div>

                        <div class="flex-1 space-y-4">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                    {{ $policy->title }}
                                </h3>
                                <div class="flex h-7 w-7 -translate-x-4 items-center justify-center rounded-full bg-slate-50 opacity-0 shadow-sm transition-all duration-500 group-hover:translate-x-0 group-hover:opacity-100 dark:bg-zinc-800">
                                    <flux:icon name="arrow-right" variant="micro" class="text-indigo-600" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-zinc-400">
                                    <flux:icon name="building-office" variant="micro" />
                                    {{ $policy->authority->office_name ?? __('National Education Department') }}
                                </p>
                                <div class="flex flex-wrap items-center gap-4 text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-400">
                                    <span class="flex items-center gap-1">
                                        <flux:icon name="calendar" variant="micro" />
                                        {{ optional($policy->application_end_date)->format('M d, Y') ?? __('N/A') }}
                                    </span>
                                    <span class="capitalize">{{ $policy->transfer_type }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center rounded-[2.5rem] border border-dashed border-slate-300 bg-slate-50 py-16 dark:border-zinc-700 dark:bg-zinc-950/30">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-zinc-800">
                        <flux:icon name="document-text" class="text-slate-500" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('No active transfer policies right now') }}</h3>
                    <p class="mt-2 max-w-md text-center text-sm text-slate-500 dark:text-zinc-400">
                        {{ __('Once a policy is activated, it will appear here so you can open it and manage your requests for that cycle.') }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="space-y-8">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ __('All Policies') }}</h2>
            <div class="h-px flex-1 bg-slate-100 dark:bg-zinc-800"></div>
        </div>

        <div class="overflow-hidden rounded-4xl border border-slate-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="divide-y divide-slate-100 dark:divide-zinc-800">
                @forelse($allPolicies as $policy)
                    <a href="{{ route('transfer.teacher-policy.requests', ['policyId' => $policy->policy_id]) }}" wire:navigate class="group flex flex-col gap-4 px-6 py-5 transition hover:bg-slate-50/80 dark:hover:bg-zinc-800/40 md:flex-row md:items-center md:justify-between">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-bold text-slate-900 transition-colors group-hover:text-indigo-600 dark:text-white">{{ $policy->title }}</h3>
                                <flux:badge variant="neutral" size="sm" class="font-black uppercase tracking-widest">{{ $policy->policy_year }}</flux:badge>
                                @if($policy->active_status)
                                    <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">{{ __('Closed') }}</flux:badge>
                                @endif
                                @if($policy->is_locked)
                                    <flux:badge color="amber" size="sm">{{ __('Locked') }}</flux:badge>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-slate-500 dark:text-zinc-400">
                                <span class="flex items-center gap-2">
                                    <flux:icon name="building-office" variant="micro" />
                                    {{ $policy->authority->office_name ?? __('National Education Department') }}
                                </span>
                                <span class="flex items-center gap-2">
                                    <flux:icon name="calendar" variant="micro" />
                                    {{ optional($policy->application_start_date)->format('M d, Y') ?? __('N/A') }}
                                    <span class="text-slate-300 dark:text-zinc-600">-</span>
                                    {{ optional($policy->application_end_date)->format('M d, Y') ?? __('N/A') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-sm font-bold text-indigo-600 dark:text-indigo-400">
                            <span>{{ __('Open Policy') }}</span>
                            <flux:icon name="arrow-right" variant="micro" class="transition-transform group-hover:translate-x-1" />
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-14 text-center text-sm font-medium text-slate-500 dark:text-zinc-400">
                        {{ __('No transfer policies are available yet.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <flux:icon name="bell-alert" variant="mini" class="text-rose-500" />
            <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ __('Recent Announcements') }}</h2>
        </div>

        <div class="space-y-4">
            @forelse($announcements as $announcement)
                @php
                    $typeGradient = match($announcement->type) {
                        'urgent' => 'from-rose-600 to-rose-700',
                        'general' => 'from-blue-600 to-blue-700',
                        'policy' => 'from-indigo-600 to-indigo-700',
                        default => 'from-slate-600 to-slate-700',
                    };
                    $badgeBg = match($announcement->type) {
                        'urgent' => 'bg-rose-600',
                        'general' => 'bg-blue-600',
                        'policy' => 'bg-indigo-700',
                        default => 'bg-slate-600',
                    };
                @endphp

                <div class="group flex items-start gap-6 rounded-3xl border border-slate-300 bg-linear-to-br from-slate-100/90 to-white p-6 transition-all duration-500 hover:border-indigo-600 hover:shadow-2xl hover:shadow-indigo-500/10 dark:border-zinc-700 dark:from-zinc-800/60 dark:to-zinc-900/60">
                    <div class="h-16 w-16 shrink-0 rounded-3xl bg-linear-to-br {{ $typeGradient }} text-white shadow-xl shadow-slate-500/20 transition-transform duration-500 group-hover:scale-110 dark:shadow-none">
                        <div class="flex h-full flex-col items-center justify-center">
                            <span class="text-[10px] font-bold uppercase opacity-80">{{ $announcement->created_at->format('M') }}</span>
                            <span class="text-xl font-black leading-tight">{{ $announcement->created_at->format('d') }}</span>
                        </div>
                    </div>

                    <div class="flex-1 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="rounded px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-white {{ $badgeBg }}">{{ $announcement->type }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider leading-none text-slate-500">{{ $announcement->created_at->format('Y') }}</span>
                        </div>
                        <h4 class="text-md font-bold text-slate-800 transition-colors group-hover:text-indigo-600 dark:text-white">{{ $announcement->title }}</h4>
                        <p class="text-sm font-semibold leading-relaxed text-slate-500">{{ $announcement->content }}</p>
                        @if($announcement->link_route)
                            <div class="pt-2">
                                <a href="{{ $announcement->link_route }}" class="text-[11px] font-black uppercase tracking-widest text-indigo-600 hover:underline dark:text-indigo-400">
                                    {{ $announcement->link_text ?: __('View Details') }} &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/30 py-12 dark:border-zinc-700 dark:bg-zinc-800/20">
                    <flux:icon name="chat-bubble-left-right" class="mb-3 h-8 w-8 text-slate-300" />
                    <h3 class="text-sm font-bold text-slate-500">{{ __('No announcements found') }}</h3>
                </div>
            @endforelse
        </div>
    </div>
</div>
