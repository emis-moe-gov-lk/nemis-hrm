<div>
    <div class="relative mb-6 w-full">
        <div class="flex items-center justify-between mb-6">
            <div>
                <flux:heading size="xl" level="1">{{ __('Transfer Policies') }}</flux:heading>
                <flux:subheading size="lg">{{ __('Manage and monitor institutional transfer policies') }}</flux:subheading>
            </div>
            <flux:button href="{{ route('transfer.transfer-policy') }}" variant="primary" icon="plus" class="px-6">
                {{ __('Create New Policy') }}
            </flux:button>
        </div>

        <flux:separator variant="subtle" class="mb-8" />

        {{-- Alert Messages --}}
        @if (session('success'))
        <x-alert type="success" dismissible class="mb-4">
            {{ session('success') }}
        </x-alert>
        @endif

        @if (session('error'))
        <x-alert type="error" dismissible class="mb-4">
            {{ session('error') }}
        </x-alert>
        @endif

        {{-- Filters Section --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="md:col-span-2">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search policies by title or circular number...') }}" />
            </div>
            <flux:select wire:model.live="yearFilter" placeholder="{{ __('Filter by Year') }}">
                <option value="">{{ __('All Years') }}</option>
                @foreach($yearOptions as $year)
                <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="serviceFilter" placeholder="{{ __('Filter by Service') }}">
                <option value="">{{ __('All Services') }}</option>
                @foreach($serviceOptions as $service)
                <option value="{{ $service->service_id }}">{{ $service->service_name }}</option>
                @endforeach
            </flux:select>
        </div>

        {{-- Policies Table --}}
        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-zinc-800">
                <thead class="bg-slate-50 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Policy & Circular') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Service / Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Application Window') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-zinc-800">
                    @forelse($policies as $policy)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="font-semibold text-slate-800 dark:text-zinc-200">{{ $policy->title }}</span>
                                <span class="text-xs text-slate-500 dark:text-zinc-400">{{ $policy->circular_number }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium">{{ $policy->service->service_name ?? 'N/A' }}</span>
                                <flux:badge variant="neutral" size="xs" class="mt-1 w-fit uppercase tracking-tighter">{{ __($policy->transfer_type) }}</flux:badge>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs text-slate-600 dark:text-zinc-400 flex flex-col gap-1">
                                <span class="flex items-center gap-1">
                                    <flux:icon name="calendar" size="micro" />
                                    {{ $policy->application_start_date->format('Y-m-d') }} {{ __('to') }} {{ $policy->application_end_date->format('Y-m-d') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center">
                                <button wire:click="requestActionConfirmation('toggleStatus', '{{ $policy->policy_id }}')" class="focus:outline-none transition-transform hover:scale-105 active:scale-95">
                                    @if($policy->active_status)
                                    <flux:badge color="green" size="sm" inset="top bottom">{{ __('Active') }}</flux:badge>
                                    @else
                                    <flux:badge color="zinc" size="sm" inset="top bottom">{{ __('Draft') }}</flux:badge>
                                    @endif
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button wire:click="showCategories('{{ $policy->policy_id }}')" variant="ghost" icon="list-bullet" size="sm" tooltip="{{ __('View Categories') }}" />
                                <flux:button href="{{ route('transfer.transfer-policy.view', $policy->policy_id) }}" variant="ghost" icon="eye" size="sm" />

                                @if(!$policy->is_locked)
                                <flux:button href="{{ route('transfer.transfer-policy.edit', $policy->policy_id) }}" variant="ghost" icon="pencil-square" size="sm" />
                                @endif

                                <flux:button wire:click="requestActionConfirmation('toggleLock', '{{ $policy->policy_id }}')" variant="ghost" color="{{ $policy->is_locked ? 'orange' : 'slate' }}" icon="{{ $policy->is_locked ? 'lock-closed' : 'lock-open' }}" size="sm" />

                                @if(!$policy->is_locked)
                                <flux:button wire:click="requestActionConfirmation('deletePolicy', '{{ $policy->policy_id }}')" variant="ghost" color="red" icon="trash" size="sm" />
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-zinc-500">
                            <div class="flex flex-col items-center justify-center">
                                <flux:icon name="document-magnifying-glass" size="lg" class="mb-2" />
                                <p>{{ __('No transfer policies found matching your criteria.') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $policies->links() }}
        </div>
    </div>

    {{-- Password Confirmation Modal --}}
    <flux:modal wire:model="showPasswordModal" name="password-confirmation" class="md:w-[450px]">
        <form wire:submit="verifyPasswordAndExecute" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Confirm Action') }}</flux:heading>
                <flux:subheading>{{ __('Please enter your login password to proceed with this sensitive action.') }}</flux:subheading>
            </div>

            <flux:input
                viewable
                wire:model="adminPassword"
                type="password"
                label="{{ __('Current Password') }}"
                placeholder="{{ __('Enter your password') }}"
                required />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">
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
                <flux:heading size="lg">{{ __('Transfer Categories') }}</flux:heading>
                <flux:subheading>{{ __('Authorized categories for:') }} <br>
                    <span class="font-bold text-slate-900 dark:text-zinc-100">
                        {{ $this->viewingPolicy->title }}
                    </span>
                </flux:subheading>
            </div>

            <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($this->categories as $category)
                <div class="p-4 rounded-2xl border border-slate-200 dark:border-zinc-800 bg-slate-50/30 dark:bg-zinc-800/20 transition-colors hover:bg-slate-50 dark:hover:bg-zinc-800/40">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <div class="flex flex-col flex-1">
                            <span class="text-sm font-bold text-slate-800 dark:text-zinc-100">{{ $category->transfer_category_name }}</span>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">{{ $category->officeLevel->office_level_name ?? 'N/A' }}</flux:badge>
                            </div>
                        </div>
                    </div>
                    @if($category->description)
                    <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">{{ $category->description }}</p>
                    @endif
                </div>
                @empty
                <div class="py-12 text-center border-2 border-dashed border-slate-100 dark:border-zinc-800 rounded-2xl">
                    <flux:icon name="document-magnifying-glass" size="lg" class="mx-auto mb-3 text-slate-300 dark:text-zinc-600" />
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">{{ __('No transfer categories found for this policy.') }}</p>
                </div>
                @endforelse
            </div>

            <div class="flex justify-end pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">{{ __('Close Panel') }}</flux:button>
                </flux:modal.close>
            </div>
        </div>
        @endif
    </flux:modal>
</div>