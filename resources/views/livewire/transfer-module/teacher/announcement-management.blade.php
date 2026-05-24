<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Transfer Announcements"
        subtitle="{{ $canManageAnnouncements ? __('Manage active system updates and notifications for the transfer process') : __('View active system updates and notifications for the transfer process') }}"
        icon="megaphone"
        :breadcrumbs="[
            'Transfer Management' => route('transfer.index-module'),
            'Announcements' => route('transfer.announcements')
        ]"
    />

    @if(session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-bold text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
        {{ session('success') }}
    </div>
    @endif

    {{-- Action Bar --}}
    @if($canManageAnnouncements)
    <div class="flex justify-end">
        <flux:button wire:click="toggleForm" icon="{{ $showForm ? 'chevron-up' : 'plus' }}" variant="primary" class="bg-indigo-600! dark:bg-white! dark:text-slate-900! text-white! border-none! h-12 px-8 rounded-2xl shadow-lg hover:scale-[1.02] transition-all">
            {{ $showForm ? __('Hide Form') : __('Create Announcement') }}
        </flux:button>
    </div>
    @endif

    {{-- Form Section --}}
    @if($canManageAnnouncements && $showForm)
    <div class="bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-slate-200 dark:border-zinc-700 shadow-sm space-y-8 animate-in fade-in slide-in-from-top-4 duration-500">
        <div class="space-y-1">
            <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $editingAnnouncement ? __('Edit Announcement') : __('New Announcement') }}</h3>
            <p class="text-sm text-slate-500">{{ __('Fill in the details below to publish a new announcement') }}</p>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field class="md:col-span-2">
                    <flux:label>{{ __('Announcement Title') }}</flux:label>
                    <flux:input wire:model="title" placeholder="{{ __('e.g., National School Transfer Board 2026') }}" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field class="md:col-span-2">
                    <flux:label>{{ __('Content') }}</flux:label>
                    <flux:textarea wire:model="content" placeholder="{{ __('Provide detailed information...') }}" rows="4" />
                    <flux:error name="content" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Type') }}</flux:label>
                    <flux:select wire:model="type">
                        <flux:select.option value="info">{{ __('Information (Blue)') }}</flux:select.option>
                        <flux:select.option value="warning">{{ __('Warning (Amber)') }}</flux:select.option>
                        <flux:select.option value="danger">{{ __('Critical (Red)') }}</flux:select.option>
                        <flux:select.option value="success">{{ __('Success (Green)') }}</flux:select.option>
                    </flux:select>
                    <flux:error name="type" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Display Order') }}</flux:label>
                    <flux:input type="number" wire:model="display_order" />
                    <flux:error name="display_order" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Publish Date') }}</flux:label>
                    <flux:input type="datetime-local" wire:model="publish_date" />
                    <flux:error name="publish_date" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Expiry Date') }}</flux:label>
                    <flux:input type="datetime-local" wire:model="expiry_date" />
                    <flux:error name="expiry_date" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Link Text (Optional)') }}</flux:label>
                    <flux:input wire:model="link_text" placeholder="{{ __('e.g., View Details') }}" />
                    <flux:error name="link_text" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Link Route/URL (Optional)') }}</flux:label>
                    <flux:input wire:model="link_route" placeholder="{{ __('e.g., /transfer/policies/1') }}" />
                    <flux:error name="link_route" />
                </flux:field>

                <div class="flex items-center gap-4 py-2">
                    <flux:checkbox wire:model="is_active" label="{{ __('Mark as Active') }}" />
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <flux:button type="submit" variant="primary" class="bg-indigo-600! text-white! border-none! px-8 rounded-xl font-bold uppercase tracking-widest text-xs h-11">
                    {{ $editingAnnouncement ? __('Update Announcement') : __('Save Announcement') }}
                </flux:button>
                <flux:button wire:click="resetFields" variant="ghost" class="px-8 rounded-xl font-bold uppercase tracking-widest text-xs h-11">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </form>
    </div>
    @endif

    {{-- Announcements Table --}}
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-700">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Order') }}</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Announcement') }}</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Status') }}</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">{{ __('Publish Window') }}</th>
                        @if($canManageAnnouncements)
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400 text-right">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-slate-50/30 dark:hover:bg-zinc-800/10 transition-all duration-200 group/row">
                        <td class="px-8 py-5">
                            <span class="text-sm font-black text-slate-500 group-hover/row:text-slate-600">#{{ $announcement->display_order }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="space-y-1">
                                <div class="flex items-center gap-3">
                                    @php
                                    $typeColor = match($announcement->type) {
                                    'warning' => 'bg-amber-500',
                                    'danger' => 'bg-rose-500',
                                    'success' => 'bg-emerald-500',
                                    default => 'bg-indigo-500'
                                    };
                                    @endphp
                                    <div class="w-2 h-2 rounded-full {{ $typeColor }}"></div>
                                    <span class="text-sm font-black text-slate-800 dark:text-zinc-100 uppercase tracking-tight">{{ $announcement->title }}</span>
                                </div>
                                <p class="text-xs text-slate-500 line-clamp-1 max-w-md">{{ $announcement->content }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            @if($announcement->is_active)
                            <flux:badge color="green" size="sm" class="uppercase font-black text-[9px] tracking-widest">{{ __('Active') }}</flux:badge>
                            @else
                            <flux:badge color="zinc" size="sm" class="uppercase font-black text-[9px] tracking-widest">{{ __('Inactive') }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="space-y-1">
                                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Start: ') }} {{ $announcement->publish_date?->format('Y-M-d H:i') ?? 'ASAP' }}</span>
                                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('End: ') }} {{ $announcement->expiry_date?->format('Y-M-d H:i') ?? 'Forever' }}</span>
                            </div>
                        </td>
                        @if($canManageAnnouncements)
                        <td class="px-8 py-5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-opacity">
                                <flux:button wire:click="edit({{ $announcement->id }})" variant="ghost" icon="pencil-square" size="sm" class="hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-xl" />
                                <flux:button wire:click="delete({{ $announcement->id }})" wire:confirm="{{ __('Are you sure you want to delete this announcement?') }}" variant="ghost" icon="trash" size="sm" class="hover:bg-rose-50 text-rose-500 rounded-xl" />
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $canManageAnnouncements ? 5 : 4 }}" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-24 h-24 rounded-3xl bg-slate-50 dark:bg-zinc-800/50 flex items-center justify-center mb-6 shadow-inner">
                                    <flux:icon name="megaphone" class="size-10 text-slate-300 dark:text-zinc-600" />
                                </div>
                                <h3 class="text-xl font-black text-slate-800 dark:text-zinc-100 uppercase tracking-tight mb-2">{{ __('No announcements yet') }}</h3>
                                <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 max-w-xs mx-auto">
                                    {{ __('Click the button above to create your first transfer announcement.') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
        <div class="px-8 py-5 border-t border-slate-200 dark:border-zinc-700">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</div>
