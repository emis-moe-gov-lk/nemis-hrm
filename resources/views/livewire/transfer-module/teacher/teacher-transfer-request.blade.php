<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-linear-to-br from-indigo-600 via-blue-600 to-indigo-800 p-10 shadow-2xl shadow-indigo-500/20 border border-white/10">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-[60px] pointer-events-none"></div>
        <div class="absolute left-1/4 -bottom-32 w-80 h-80 bg-blue-400/20 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="hidden sm:flex items-center justify-center w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white shadow-xl">
                    <flux:icon name="document-text" variant="solid" class="w-8 h-8" />
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-2 drop-shadow-sm">
                        {{ __('Transfer Requests') }}
                    </h1>
                    <p class="text-indigo-100 font-medium text-sm md:text-base max-w-lg leading-relaxed">
                        {{ __('Track and manage your submitted teacher transfer applications.') }}
                    </p>
                </div>
            </div>
            <div class="shrink-0">
                <div class="inline-flex flex-col items-center justify-center px-6 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 shadow-xl">
                    <span class="text-indigo-100 text-xs font-bold uppercase tracking-widest mb-1">{{ __('Total') }}</span>
                    <span class="text-3xl font-black text-white leading-none">{{ $applications->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Requests List Container --}}
    <div class="bg-white dark:bg-zinc-900/60 backdrop-blur-xl rounded-4xl border border-slate-200 dark:border-zinc-700 shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
        {{-- Filters Bar --}}
        <div class="px-6 py-5 border-b border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <flux:icon name="funnel" variant="mini" class="w-4 h-4" />
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white whitespace-nowrap">{{ __('Filter Applications') }}</h3>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <flux:select wire:model.live="filterPolicy" class="w-full sm:w-48 rounded-xl!" placeholder="{{ __('All Policies') }}">
                    <option value="">{{ __('All Policies') }}</option>
                    @foreach($policies as $policy)
                    <option value="{{ $policy->policy_id }}">{{ $policy->title }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filterSubCategory" class="w-full sm:w-48 rounded-xl!" placeholder="{{ __('All Categories') }}">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($transferSubCategories as $subCategory)
                    <option value="{{ $subCategory->transfer_sub_category_id }}">{{ $subCategory->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filterZone" class="w-full sm:w-48 rounded-xl!" placeholder="{{ __('All Working Zones') }}">
                    <option value="">{{ __('All Working Zones') }}</option>
                    @foreach($zones as $zone)
                    <option value="{{ $zone->workplace_id }}">{{ $zone->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="perPage" class="w-full sm:w-32 rounded-xl!" placeholder="{{ __('Per Page') }}">
                    <option value="10">{{ __('10 / page') }}</option>
                    <option value="25">{{ __('25 / page') }}</option>
                    <option value="50">{{ __('50 / page') }}</option>
                    <option value="100">{{ __('100 / page') }}</option>
                </flux:select>

                <div class="hidden sm:block w-px h-8 bg-slate-200 dark:bg-zinc-700 mx-2"></div>

                <flux:button wire:click="exportExcel" variant="ghost" icon="arrow-down-tray" class="w-full sm:w-auto rounded-xl! text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                    {{ __('Export') }}
                </flux:button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-zinc-700 bg-slate-50/80 dark:bg-zinc-800/50">
                        <th class="w-[17%] px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Transfer Type') }}</th>
                        @if(auth()->user()->hasRole('super admin') || auth()->user()->workplace_id)
                        <th class="w-[25%] px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Applicant') }}</th>
                        @endif
                        <th class="w-[16%] px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Target Province') }}</th>
                        <th class="w-[12%] px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Submitted') }}</th>
                        <th class="w-[11%] px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Status') }}</th>
                        <th class="w-[19%] px-4 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @forelse($applications as $app)
                    @php $badge = $this->statusBadge($app->status); @endphp
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-all duration-300">
                        <td class="px-4 py-4">
                            <div class="flex min-w-0 flex-col gap-1">
                                <span class="block max-w-full truncate font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $app->policy->title ?? 'N/A' }}
                                </span>
                                <span class="block max-w-full truncate text-xs font-medium text-slate-500 dark:text-zinc-400 bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md w-fit">
                                    {{ $app->display_category_name }}
                                </span>
                            </div>
                        </td>

                        @if(auth()->user()->hasRole('super admin') || auth()->user()->workplace_id)
                        <td class="px-4 py-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs shrink-0">
                                    {{ substr($app->employee->name_with_initials ?? $app->employee->full_name ?? 'U', 0, 1) }}
                                </div>
                                <div class="flex min-w-0 flex-col">
                                    <span class="block max-w-full truncate text-sm font-bold text-slate-900 dark:text-white">
                                        {{ trim(($app->employee->title->title_name ?? '') . ' ' . ($app->employee->name_with_initials ?? $app->employee->full_name ?? '')) }}
                                    </span>
                                    <span class="block max-w-full truncate text-[10px] text-slate-400 dark:text-zinc-500 font-mono uppercase tracking-wider">
                                        {{ $app->employee_id }} • {{ $app->employee->nic ?? '' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        @endif

                        <td class="px-4 py-4">
                            <div class="flex min-w-0 items-center gap-2">
                                <flux:icon name="map-pin" variant="micro" class="w-4 h-4 text-slate-400 dark:text-zinc-500 shrink-0" />
                                <span class="block max-w-full truncate text-sm font-medium text-slate-700 dark:text-zinc-300">
                                    {{ $app->targetProvince->short_name ?? 'N/A' }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <span class="block truncate text-sm font-medium text-slate-600 dark:text-zinc-400">
                                {{ $app->created_at?->format('M d, Y') }}
                            </span>
                        </td>

                        <td class="px-4 py-4">
                            <flux:badge :color="$badge['color']" size="sm" inset="top bottom" class="font-bold tracking-wide">
                                {{ $badge['label'] }}
                            </flux:badge>
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button href="{{ route('transfer.teacher-transfer-application.view', $app->transfer_application_id) }}" variant="filled" size="sm" class="rounded-xl! shadow-sm hidden md:inline-flex">
                                    {{ __('View Details') }}
                                </flux:button>

                                @if($canDeleteRequests)
                                    <flux:button
                                        wire:click="confirmDeleteApplication('{{ $app->transfer_application_id }}')"
                                        variant="ghost"
                                        size="sm"
                                        icon="trash"
                                        class="rounded-xl! text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 hidden md:inline-flex"
                                    >
                                        {{ __('Delete') }}
                                    </flux:button>
                                @endif

                                <flux:button href="{{ route('transfer.teacher-transfer-application.view', $app->transfer_application_id) }}" variant="ghost" size="sm" icon="eye" class="md:hidden text-slate-400" />

                                @if($canDeleteRequests)
                                    <flux:button
                                        wire:click="confirmDeleteApplication('{{ $app->transfer_application_id }}')"
                                        variant="ghost"
                                        size="sm"
                                        icon="trash"
                                        class="md:hidden text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10"
                                    />
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ (auth()->user()->hasRole('super admin') || auth()->user()->workplace_id) ? 6 : 5 }}" class="px-6 py-32">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="w-24 h-24 mb-6 relative">
                                    <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full blur-xl scale-150"></div>
                                    <div class="relative w-full h-full bg-white dark:bg-zinc-800 rounded-full border border-slate-100 dark:border-zinc-700 shadow-xl flex items-center justify-center">
                                        <flux:icon name="document-magnifying-glass" class="h-10 w-10 text-indigo-500" />
                                    </div>
                                </div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">{{ __('No Applications Found') }}</h3>
                                <p class="text-slate-500 dark:text-zinc-400 text-sm max-w-sm mb-8 leading-relaxed">
                                    {{ __('You haven\'t submitted any transfer applications yet, or there are no applications matching your filters.') }}
                                </p>
                                <flux:button href="{{ route('my-transfer.teacher-transfer-application') }}" variant="filled" icon="plus" class="rounded-xl! shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
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
    <div class="mt-6 px-4">
        {{ $applications->links() }}
    </div>
    @endif

    <flux:modal wire:model="showDeleteModal" name="delete-transfer-request-confirmation" class="md:w-[460px]">
        <form wire:submit="verifyPasswordAndDelete" class="space-y-6">
            <div>
                <flux:heading size="lg" class="font-black tracking-tight">{{ __('Delete Transfer Request') }}</flux:heading>
                <flux:subheading>
                    {{ __('Enter your current login password to permanently delete this transfer request.') }}
                </flux:subheading>
            </div>

            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                {{ __('This action cannot be undone.') }}
                @if($deleteApplicationLabel)
                    <span class="font-black">{{ $deleteApplicationLabel }}</span>
                @endif
            </div>

            <flux:input
                viewable
                wire:model="deletePassword"
                type="password"
                label="{{ __('Current Password') }}"
                placeholder="{{ __('Enter your password') }}"
                class="rounded-xl!"
                required />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button wire:click="cancelDeleteConfirmation" type="button" variant="ghost" class="rounded-xl!">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger" icon="trash" class="rounded-xl!">
                    <span wire:loading.remove wire:target="verifyPasswordAndDelete">{{ __('Delete') }}</span>
                    <span wire:loading wire:target="verifyPasswordAndDelete">{{ __('Deleting...') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
