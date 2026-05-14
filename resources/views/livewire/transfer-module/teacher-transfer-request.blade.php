<div class="space-y-8">
    {{-- Header Banner --}}
    <div class="relative bg-slate-900 rounded-2xl p-8 overflow-hidden border border-slate-800 shadow-2xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <flux:heading size="xl" class="text-white font-black tracking-tight mb-2">
                    {{ __('Transfer Requests') }}
                </flux:heading>
                <flux:subheading class="text-slate-400 max-w-lg">
                    {{ __('Track and manage your submitted teacher transfer applications.') }}
                </flux:subheading>
            </div>
        </div>
    </div>

    {{-- Requests List --}}
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-zinc-50/30 dark:bg-zinc-800/20">
            <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                {{ __('Total Applications:') }} <span class="text-zinc-900 dark:text-white font-bold ml-1">{{ $applications->total() }}</span>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                {{-- Transfer Policy Filter --}}
                <flux:select wire:model.live="filterPolicy" size="sm" class="w-full sm:w-56" placeholder="{{ __('All Policies') }}">
                    <option value="">{{ __('All Policies') }}</option>
                    @foreach($policies as $policy)
                    <option value="{{ $policy->policy_id }}">{{ $policy->title }}</option>
                    @endforeach
                </flux:select>

                {{-- Transfer Category Filter --}}
                <flux:select wire:model.live="filterCategory" size="sm" class="w-full sm:w-56" placeholder="{{ __('All Categories') }}">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($transferCategories as $category)
                    <option value="{{ $category->transfer_category_id }}">{{ $category->transfer_category_name }}</option>
                    @endforeach
                </flux:select>

                {{-- Current Working Zone Filter --}}
                <flux:select wire:model.live="filterZone" size="sm" class="w-full sm:w-56" placeholder="{{ __('All Working Zones') }}">
                    <option value="">{{ __('All Working Zones') }}</option>
                    @foreach($zones as $zone)
                    <option value="{{ $zone->workplace_id }}">{{ $zone->name }}</option>
                    @endforeach
                </flux:select>

                <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-700 mx-1"></div>

                <flux:button wire:click="exportExcel" size="sm" variant="ghost" icon="arrow-down-tray" class="w-full sm:w-auto text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 border border-emerald-200 dark:border-emerald-800/30 dark:hover:bg-emerald-900/20">
                    {{ __('Export') }}
                </flux:button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400 text-[11px] uppercase tracking-wider font-bold">
                        <th class="px-6 py-4">{{ __('Transfer Type') }}</th>
                        @if(auth()->user()->hasRole('super admin') || auth()->user()->workplace_id)
                        <th class="px-6 py-4">{{ __('Applicant') }}</th>
                        @endif
                        <th class="px-6 py-4">{{ __('Target Province') }}</th>
                        <th class="px-6 py-4">{{ __('Submitted Date') }}</th>
                        <th class="px-6 py-4">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($applications as $app)
                    @php $badge = $this->statusBadge($app->status); @endphp
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <span class="font-bold text-zinc-900 dark:text-white block truncate max-w-[200px]">
                                {{ $app->policy->title ?? 'N/A' }}
                            </span>
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $app->category->transfer_category_name ?? 'N/A' }}
                            </span>
                        </td>

                        @if(auth()->user()->hasRole('super admin') || auth()->user()->workplace_id)
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-zinc-900 dark:text-white">
                                    {{ ($app->employee->title->title_name ?? '') . ' ' . $app->employee->full_name }}
                                </span>
                                <span class="text-[10px] text-zinc-500 font-mono uppercase tracking-tighter">
                                    {{ $app->employee_id }}/ {{ $app->employee->nic}}
                                </span>
                            </div>
                        </td>
                        @endif

                        <td class="px-6 py-4">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $app->targetProvince->short_name ?? 'N/A' }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="text-sm text-zinc-500 dark:text-zinc-500">
                                {{ $app->created_at?->format('M d, Y') }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <flux:badge :color="$badge['color']" size="sm" inset="top bottom">
                                {{ $badge['label'] }}
                            </flux:badge>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <flux:button href="{{ route('transfer.teacher-transfer-application.view', $app->transfer_application_id) }}" variant="ghost" size="sm" icon="eye" class="text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/30">
                                {{ __('View') }}
                            </flux:button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ (auth()->user()->hasRole('super admin') || auth()->user()->workplace_id) ? 7 : 6 }}" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                                    <flux:icon name="document-text" class="h-8 w-8 text-zinc-400" />
                                </div>
                                <h3 class="text-zinc-900 dark:text-white font-bold mb-1">{{ __('No Applications Found') }}</h3>
                                <p class="text-zinc-500 text-sm max-w-xs mb-6">{{ __('You haven\'t submitted any transfer applications yet.') }}</p>
                                <flux:button href="{{ route('my-transfer.teacher-transfer-application') }}" variant="filled" size="sm">
                                    {{ __('Start New Application') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($applications->hasPages())
    <div class="mt-6">
        {{ $applications->links() }}
    </div>
    @endif
</div>