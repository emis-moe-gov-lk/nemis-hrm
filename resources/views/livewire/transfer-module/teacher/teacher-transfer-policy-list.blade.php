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
                        {{ __('Transfer Policies') }}
                    </h1>
                    <p class="text-indigo-100 font-medium text-sm md:text-base max-w-lg leading-relaxed">
                        {{ __('Manage and monitor institutional transfer policies.') }}
                    </p>
                </div>
            </div>
            <div class="shrink-0 flex gap-3">
                @can('transfer.policy.manage')
                <flux:button href="{{ route('transfer.transfer-policy') }}" icon="plus" class="h-12 px-6 rounded-xl! bg-white/10 backdrop-blur-md text-white border-white/20 hover:bg-white/20 shadow-xl transition-all">
                    {{ __('Create New Policy') }}
                </flux:button>
                @endcan
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/30 shadow-sm animate-in fade-in duration-500">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center shrink-0">
                <flux:icon name="check-circle" variant="solid" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <p class="text-emerald-800 dark:text-emerald-200 font-bold text-sm">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if (session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/30 shadow-sm animate-in fade-in duration-500">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/50 flex items-center justify-center shrink-0">
                <flux:icon name="x-circle" variant="solid" class="w-5 h-5 text-rose-600 dark:text-rose-400" />
            </div>
            <p class="text-rose-800 dark:text-rose-200 font-bold text-sm">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Policies List Container --}}
    <div class="bg-white dark:bg-zinc-900/60 backdrop-blur-xl rounded-4xl border border-slate-200 dark:border-zinc-700 shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
        {{-- Filters Bar --}}
        <div class="px-6 py-5 border-b border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <flux:icon name="magnifying-glass" variant="mini" class="w-4 h-4" />
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white whitespace-nowrap">{{ __('Search & Filter') }}</h3>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search policies...') }}" class="w-full sm:w-64 rounded-xl!" />

                <flux:select wire:model.live="yearFilter" class="w-full sm:w-48 rounded-xl!" placeholder="{{ __('Filter by Year') }}">
                    <option value="">{{ __('All Years') }}</option>
                    @foreach($yearOptions as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-zinc-700 bg-slate-50/80 dark:bg-zinc-800/50">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Policy & Circular') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Transfer Type') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Application Window') }}</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @forelse($policies as $policy)
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-all duration-300">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[250px] group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $policy->title }}
                                </span>
                                <span class="text-xs font-medium text-slate-500 dark:text-zinc-400 bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md w-fit font-mono tracking-wider">
                                    {{ $policy->circular_number }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <flux:badge variant="neutral" size="sm" inset="top bottom" class="uppercase tracking-tighter font-bold">
                                {{ __($policy->transfer_type) }}
                            </flux:badge>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <flux:icon name="calendar" variant="micro" class="w-4 h-4 text-slate-400 dark:text-zinc-500 shrink-0" />
                                <span class="text-sm font-medium text-slate-700 dark:text-zinc-300">
                                    {{ $policy->application_start_date->format('M d, Y') }} <span class="text-slate-400 px-1">{{ __('to') }}</span> {{ $policy->application_end_date->format('M d, Y') }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center">
                                @can('transfer.policy.manage')
                                <button wire:click="requestActionConfirmation('toggleStatus', '{{ $policy->policy_id }}')" class="focus:outline-none transition-transform hover:scale-105 active:scale-95">
                                    @if($policy->active_status)
                                    <flux:badge color="emerald" size="sm" inset="top bottom" class="font-bold tracking-wide">{{ __('Active') }}</flux:badge>
                                    @else
                                    <flux:badge color="zinc" size="sm" inset="top bottom" class="font-bold tracking-wide">{{ __('Draft') }}</flux:badge>
                                    @endif
                                </button>
                                @else
                                @if($policy->active_status)
                                <flux:badge color="emerald" size="sm" inset="top bottom" class="font-bold tracking-wide">{{ __('Active') }}</flux:badge>
                                @else
                                <flux:badge color="zinc" size="sm" inset="top bottom" class="font-bold tracking-wide">{{ __('Draft') }}</flux:badge>
                                @endif
                                @endcan
                            </div>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                <flux:button wire:click="showCategories('{{ $policy->policy_id }}')" variant="ghost" icon="list-bullet" size="sm" tooltip="{{ __('View Categories') }}" class="text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400" />
                                <flux:button href="{{ route('transfer.transfer-policy.view', $policy->policy_id) }}" variant="ghost" icon="eye" size="sm" class="text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400" />

                                @can('transfer.policy.manage')
                                @if(!$policy->is_locked)
                                <flux:button href="{{ route('transfer.transfer-policy.edit', $policy->policy_id) }}" variant="ghost" icon="pencil-square" size="sm" class="text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400" />
                                @endif

                                <flux:button wire:click="requestActionConfirmation('toggleLock', '{{ $policy->policy_id }}')" variant="ghost" color="{{ $policy->is_locked ? 'orange' : 'slate' }}" icon="{{ $policy->is_locked ? 'lock-closed' : 'lock-open' }}" size="sm" class="text-slate-500 hover:text-orange-600 dark:hover:text-orange-400" />

                                @if(!$policy->is_locked)
                                <flux:button wire:click="requestActionConfirmation('deletePolicy', '{{ $policy->policy_id }}')" variant="ghost" icon="trash" size="sm" class="text-slate-500 hover:text-rose-600 dark:hover:text-rose-400" />
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-32">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="w-24 h-24 mb-6 relative">
                                    <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full blur-xl scale-150"></div>
                                    <div class="relative w-full h-full bg-white dark:bg-zinc-800 rounded-full border border-slate-100 dark:border-zinc-700 shadow-xl flex items-center justify-center">
                                        <flux:icon name="document-magnifying-glass" class="h-10 w-10 text-indigo-500" />
                                    </div>
                                </div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">{{ __('No Policies Found') }}</h3>
                                <p class="text-slate-500 dark:text-zinc-400 text-sm max-w-sm mb-8 leading-relaxed">
                                    {{ __('There are no transfer policies matching your current search criteria.') }}
                                </p>
                                @can('transfer.policy.manage')
                                <flux:button href="{{ route('transfer.transfer-policy') }}" variant="filled" icon="plus" class="rounded-xl! shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                                    {{ __('Create New Policy') }}
                                </flux:button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($policies->hasPages())
    <div class="mt-6 px-4">
        {{ $policies->links() }}
    </div>
    @endif

    {{-- Password Confirmation Modal --}}
    <flux:modal wire:model="showPasswordModal" name="password-confirmation" class="md:w-[450px]">
        <form wire:submit="verifyPasswordAndExecute" class="space-y-6">
            <div>
                <flux:heading size="lg" class="font-black tracking-tight">{{ __('Confirm Action') }}</flux:heading>
                <flux:subheading>{{ __('Please enter your login password to proceed with this sensitive action.') }}</flux:subheading>
            </div>

            <flux:input
                viewable
                wire:model="adminPassword"
                type="password"
                label="{{ __('Current Password') }}"
                placeholder="{{ __('Enter your password') }}"
                class="rounded-xl!"
                required />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="rounded-xl!">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" class="rounded-xl! shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                    {{ __('Verify & Proceed') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Categories Drawer --}}
    <flux:modal wire:model="showCategoriesDrawer" name="categories-drawer" class="md:w-[500px]">
        @if($this->viewingPolicy)
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="font-black tracking-tight">{{ __('Transfer Categories') }}</flux:heading>
                <flux:subheading>{{ __('Authorized categories for:') }} <br>
                    <span class="font-bold text-slate-900 dark:text-zinc-100">
                        {{ $this->viewingPolicy->title }}
                    </span>
                </flux:subheading>
            </div>

            <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($this->categories as $category)
                <div class="p-4 rounded-2xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/30 transition-all duration-300 hover:bg-white dark:hover:bg-zinc-800/60 hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <div class="flex flex-col flex-1">
                            <span class="text-sm font-bold text-slate-800 dark:text-zinc-100">{{ $category->display_name }}</span>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter font-bold">{{ $category->officeLevel->office_level_name ?? 'N/A' }}</flux:badge>
                            </div>
                        </div>
                    </div>
                    @if($category->description)
                    <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">{{ $category->description }}</p>
                    @endif
                </div>
                @empty
                <div class="py-12 text-center border border-dashed border-slate-300 dark:border-zinc-700 rounded-3xl bg-slate-50/50 dark:bg-zinc-800/30">
                    <flux:icon name="document-magnifying-glass" size="lg" class="mx-auto mb-3 text-slate-400 dark:text-zinc-500" />
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">{{ __('No transfer categories found for this policy.') }}</p>
                </div>
                @endforelse
            </div>

            <div class="flex justify-end pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" class="rounded-xl!">{{ __('Close Panel') }}</flux:button>
                </flux:modal.close>
            </div>
        </div>
        @endif
    </flux:modal>
</div>