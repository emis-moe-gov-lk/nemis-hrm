<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Refined Professional Header --}}
    <x-page-header
        title="Teacher Transfer Management"
        subtitle="Centralized oversight for national, provincial, and zonal employee transfers. Manage policies and coordinate boards."
        icon="arrows-right-left"
        :breadcrumbs="[
            'Transfer Management' => route('transfer.index-module')
        ]">
        <x-slot:actions>
            @if($canManagePolicies)
            <flux:button href="{{ route('transfer.transfer-policy') }}" variant="primary" icon="plus" class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200/50 dark:shadow-none border-none">
                {{ __('Start New Transfer') }}
            </flux:button>
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Active Transfer Policies --}}
    @if($canBrowseActivePolicies)
    <div class="space-y-8">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 flex-1">
                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Active Transfer Policies') }}</h2>
                <div class="h-px flex-1 bg-slate-100 dark:bg-zinc-800"></div>
            </div>
            @if($canViewPolicies)
            <flux:button href="{{ route('transfer.transfer-policies') }}" variant="ghost" size="sm" icon-trailing="chevron-right" class="font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30">
                {{ __('View All Policies') }}
            </flux:button>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            @forelse($policies as $policy)
            <a href="{{ route('transfer.teacher-transfer-controller', $policy->id) }}" wire:navigate
                class="group relative overflow-hidden bg-slate-50/50 dark:bg-zinc-900/50 hover:bg-white dark:hover:bg-zinc-900 border border-slate-300/60 dark:border-zinc-700 hover:border-transparent rounded-[2.5rem] p-8 transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_30px_70px_-20px_rgba(0,0,0,0.15)]">

                {{-- Gradient Border Effect --}}
                <div class="absolute inset-0 p-px rounded-[2.5rem] bg-linear-to-br from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                <div class="absolute inset-px rounded-[2.45rem] bg-inherit -z-10"></div>
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-linear-to-br from-indigo-500 to-blue-600 opacity-[0.05] group-hover:opacity-[0.15] transition-opacity duration-700 blur-3xl"></div>

                <div class="relative flex flex-col h-full">
                    <div class="flex items-center justify-between mb-8">
                        {{-- Icon Module --}}
                        <div class="relative">
                            <div class="absolute inset-0 bg-linear-to-br from-indigo-500 to-blue-600 opacity-20 blur-2xl group-hover:opacity-40 transition-opacity duration-500 rounded-full scale-150"></div>
                            <div class="relative inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-200 text-white transform transition-all duration-500 group-hover:scale-110 group-hover:rotate-3">
                                <flux:icon name="document-text" variant="mini" class="w-7 h-7" />
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <flux:badge variant="neutral" size="sm" class="uppercase tracking-widest font-black">{{ $policy->policy_year }}</flux:badge>
                            @if($policy->active_status)
                            <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                            @else
                            <flux:badge color="zinc" size="sm">{{ __('Draft') }}</flux:badge>
                            @endif
                        </div>
                    </div>

                    {{-- Text Content --}}
                    <div class="flex-1 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                                {{ $policy->title }}
                            </h3>
                            <div class="h-7 w-7 rounded-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center opacity-0 group-hover:opacity-100 -translate-x-4 group-hover:translate-x-0 transition-all duration-500 shadow-sm">
                                <flux:icon name="arrow-right" variant="micro" class="text-indigo-600" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 flex items-center gap-2">
                                <flux:icon name="building-office" variant="micro" />
                                {{ $policy->authority->office_name ?? 'National Education Department' }}
                            </p>
                            <div class="flex items-center gap-4 text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest">
                                <span class="flex items-center gap-1">
                                    <flux:icon name="calendar" variant="micro" />
                                    {{ $policy->application_end_date->format('M d, Y') }}
                                </span>
                                <span>•</span>
                                <span class="capitalize">{{ $policy->transfer_type }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="lg:col-span-3 py-16 flex flex-col items-center justify-center bg-slate-50 dark:bg-zinc-950/30 border border-dashed border-slate-300 dark:border-zinc-700 rounded-[2.5rem]">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center mb-4">
                    <flux:icon name="document-text" class="text-slate-500" />
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('No Transfer Policies Found') }}</h3>
                <p class="text-sm text-slate-500 dark:text-zinc-400 mb-6">{{ __('Define your first policy to begin managing teacher transfers.') }}</p>
                <flux:button href="{{ route('transfer.transfer-policy') }}" variant="primary" size="sm" icon="plus">{{ __('Create First Policy') }}</flux:button>
            </div>
            @endforelse
        </div>
    </div>
    @endif

    {{-- Administrative Tools --}}
    @if(!empty($tools))
    <div class="space-y-8">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Administrative Tools') }}</h2>
            <div class="h-px flex-1 bg-slate-100 dark:bg-zinc-800"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($tools as $tool)
            <a href="{{ $tool['route'] }}" wire:navigate
                class="group relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 hover:border-transparent rounded-[2.5rem] p-8 transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_30px_70px_-20px_rgba(0,0,0,0.15)]">

                {{-- Gradient Border Effect --}}
                <div class="absolute inset-0 p-px rounded-[2.5rem] bg-linear-to-br {{ $tool['gradient'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                <div class="absolute inset-px rounded-[2.45rem] bg-white dark:bg-zinc-900 -z-10"></div>

                <div class="flex items-center gap-8">
                    <div class="shrink-0 relative">
                        <div class="absolute inset-0 bg-linear-to-br {{ $tool['gradient'] }} opacity-20 blur-2xl group-hover:opacity-40 transition-opacity duration-500 rounded-full scale-150"></div>
                        <div class="relative flex items-center justify-center w-20 h-20 rounded-4xl bg-linear-to-br {{ $tool['gradient'] }} text-white shadow-lg {{ $tool['shadow'] }} group-hover:scale-105 transition-all duration-500">
                            <flux:icon :icon="$tool['icon']" class="w-10 h-10" />
                        </div>
                    </div>

                    <div class="flex-1 space-y-2">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $tool['label'] }}</h3>
                            <flux:icon name="arrow-right" variant="mini" class="text-slate-300 group-hover:{{ $tool['text'] }} transition-all group-hover:translate-x-1" />
                        </div>
                        <p class="text-[15px] font-medium text-slate-500 dark:text-zinc-400 leading-snug group-hover:text-slate-600 dark:group-hover:text-zinc-300 transition-colors">
                            {{ $tool['desc'] }}
                        </p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>