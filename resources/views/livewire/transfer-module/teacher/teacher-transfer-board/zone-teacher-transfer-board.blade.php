<div class="p-6 lg:p-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Zonal Teacher Transfer Board') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Oversee and manage teacher transfer applications across the zone') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:button icon="arrow-path" wire:click="$refresh" variant="ghost" size="sm">{{ __('Refresh') }}</flux:button>
            <flux:button icon="printer" variant="ghost" size="sm">{{ __('Export Report') }}</flux:button>
        </div>
    </div>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-300 dark:border-zinc-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Total Applications') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <flux:icon name="clipboard-document-list" size="lg" />
                </div>
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="clipboard-document-list" class="w-24 h-24" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-300 dark:border-zinc-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Incoming Requests') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['incoming']) }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                    <flux:icon name="arrow-down-tray" size="lg" />
                </div>
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="arrow-down-tray" class="w-24 h-24" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-300 dark:border-zinc-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Outgoing Requests') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['outgoing']) }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                    <flux:icon name="arrow-up-tray" size="lg" />
                </div>
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="arrow-up-tray" class="w-24 h-24" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-300 dark:border-zinc-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Pending Action') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['pending']) }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400">
                    <flux:icon name="exclamation-circle" size="lg" />
                </div>
            </div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="exclamation-circle" class="w-24 h-24" />
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 shadow-sm overflow-hidden">
        {{-- Filters Bar --}}
        <div class="p-6 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-200 dark:border-zinc-700">
            <div class="flex flex-col lg:flex-row items-center gap-4">
                <div class="w-full lg:flex-1">
                    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search by ID, Name or NIC...') }}" />
                </div>
                <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                    <flux:select wire:model.live="status" class="w-full md:w-48">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="submitted">{{ __('Submitted') }}</option>
                        <option value="processing">{{ __('Processing') }}</option>
                        <option value="approved">{{ __('Approved') }}</option>
                        <option value="rejected">{{ __('Rejected') }}</option>
                    </flux:select>
                    <flux:select wire:model.live="category" class="w-full md:w-56">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $categoryOption)
                        <option value="{{ $categoryOption->transfer_category_id }}">{{ $categoryOption->transfer_category_name }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/50">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Application ID & Date') }}</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Teacher Details') }}</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Transfer Info') }}</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Institution/Zone') }}</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @forelse($applications as $app)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-all duration-200">
                        <td class="px-6 py-4">
                            <span class="block text-sm font-black text-slate-900 dark:text-white">{{ $app->transfer_application_id }}</span>
                            <span class="block text-xs text-slate-500 dark:text-zinc-400">{{ $app->created_at->format('Y-M-d') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center border border-slate-300 dark:border-zinc-700">
                                    <span class="text-xs font-bold text-slate-600 dark:text-zinc-400">{{ substr($app->employee->full_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $app->employee->full_name }}</span>
                                    <span class="block text-xs text-slate-500 dark:text-zinc-400">{{ $app->employee->nic }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <flux:badge variant="neutral" size="xs" class="w-fit uppercase tracking-tighter">
                                    {{ \Illuminate\Support\Str::headline($app->policy->transfer_type ?? $app->transfer_type) }}
                                </flux:badge>
                                <span class="text-xs font-medium text-slate-600 dark:text-zinc-400">{{ $app->display_category_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="block text-sm font-medium text-slate-700 dark:text-zinc-300 truncate max-w-[200px]" title="{{ $app->currentWorkplace->office_name ?? 'N/A' }}">
                                {{ $app->currentWorkplace->office_name ?? 'N/A' }}
                            </span>
                            @if($activeTab === 'incoming')
                            <span class="text-[10px] text-blue-500 dark:text-blue-400 font-bold uppercase tracking-wider mt-1 flex items-center gap-1">
                                <flux:icon name="chevron-right" size="micro" />
                                {{ __('Preferred This Zone') }}
                            </span>
                            @else
                            <span class="text-[10px] text-amber-500 dark:text-amber-400 font-bold uppercase tracking-wider mt-1 flex items-center gap-1">
                                <flux:icon name="paper-airplane" size="micro" />
                                {{ __('Moving Out') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                            $statusColor = match($app->status) {
                            'submitted' => 'blue',
                            'processing' => 'amber',
                            'approved' => 'green',
                            'rejected' => 'rose',
                            default => 'zinc'
                            };
                            @endphp
                            <flux:badge :color="$statusColor" size="sm" inset="top bottom" class="capitalize">
                                {{ __($app->status) }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button href="{{ route('transfer-board.teacher-profile-for-transfer-board', ['id' => $app->transfer_application_id, 'board' => 'zone']) }}" variant="ghost" icon="eye" size="sm" />
                                <flux:button href="{{ route('transfer.teacher-transfer-application.download', $app->transfer_application_id) }}" variant="ghost" icon="arrow-down-tray" size="sm" />
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 rounded-full bg-slate-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4">
                                    <flux:icon name="document-magnifying-glass" size="lg" class="text-slate-300 dark:text-zinc-600" />
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-zinc-200 mb-1">{{ __('No applications found') }}</h3>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 max-w-xs mx-auto">
                                    {{ __('We couldn\'t find any transfer applications for the current zone level criteria.') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-6 bg-slate-50 dark:bg-zinc-800/30 border-t border-slate-200 dark:border-zinc-700">
            {{ $applications->links() }}
        </div>
    </div>
</div>
