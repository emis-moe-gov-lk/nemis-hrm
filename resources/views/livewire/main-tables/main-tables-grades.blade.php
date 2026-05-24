<section class="w-full">
    <header class="mb-10 flex justify-between items-center">
        <div>
            <flux:heading size="xl" level="1" class="!text-3xl font-black tracking-tight text-slate-900 dark:text-white leading-none mb-3">
                {{ __('Global Grade Registry') }}
            </flux:heading>
            <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
                {{ __('Define and manage the standard grade levels available across the entire educational system.') }}
            </flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus" class="bg-indigo-600! rounded-xl!">
            {{ __('Add New Grade') }}
        </flux:button>
    </header>

    <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">{{ __('Order') }}</th>
                        <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">{{ __('Grade Name') }}</th>
                        <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">{{ __('Status') }}</th>
                        <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                    @foreach($grades as $grade)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-indigo-700/30 transition-colors group">
                            <td class="px-8 py-4">
                                <span class="text-xs font-black text-slate-500">#{{ $grade->order }}</span>
                            </td>
                            <td class="px-8 py-4">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $grade->name }}</span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <flux:switch wire:click="toggleStatus({{ $grade->id }})" :checked="$grade->active_status" />
                            </td>
                            <td class="px-8 py-4 text-right space-x-2">
                                <flux:button wire:click="edit({{ $grade->id }})" variant="subtle" size="sm" icon="pencil-square" square />
                                <flux:button wire:click="delete({{ $grade->id }})" wire:confirm="Are you sure?" variant="subtle" size="sm" icon="trash" square class="hover:text-rose-500" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-200 dark:border-slate-700">
            {{ $grades->links() }}
        </div>
    </div>

    {{-- Edit Modal --}}
    <flux:modal wire:model="showModal" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="!font-black uppercase tracking-tight">{{ $editId ? 'Edit Grade level' : 'New Grade level' }}</flux:heading>
                <flux:subheading>{{ __('Standardized grade identifier used in institution configurations.') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="name" label="{{ __('Grade Name') }}" placeholder="e.g., Grade 1" />
                <flux:input wire:model="order" type="number" label="{{ __('Display Order') }}" placeholder="1" />
            </div>

            <div class="flex gap-3">
                <flux:spacer />
                <flux:button wire:click="$set('showModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
                <flux:button wire:click="save" variant="primary" class="bg-indigo-600!">{{ __('Save Changes') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
