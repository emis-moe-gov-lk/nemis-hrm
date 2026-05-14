<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase">
                        {{ __('Teacher Categories') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.tag variant="micro" class="text-slate-400" />
                        {{ __('Define and organize professional staff groupings') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-teacher-category">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add New Category') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Success Notification --}}
            @if (session()->has('message'))
                <div class="mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- CARD GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($teacherCategories as $key => $data)
                    <div class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 shadow-sm hover:shadow-2xl hover:border-indigo-500/30 transition-all duration-300 flex flex-col">
                        
                        {{-- Top Row: Index & Status --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center border border-slate-100 dark:border-slate-700 font-black text-xs text-slate-400">
                                    {{ $teacherCategories->firstItem() + $key }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 leading-none">System ID</span>
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mt-1 uppercase leading-none">{{ $data->categories_id }}</span>
                                </div>
                            </div>
                            <div class="flex items-center h-6">
                                <span class="inline-flex h-2 w-2 rounded-full {{ $data->active_status ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-rose-500' }}" title="{{ $data->active_status ? 'Active' : 'Inactive' }}"></span>
                            </div>
                        </div>

                        {{-- Category Name --}}
                        <div class="mb-3">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight leading-tight group-hover:text-indigo-600 transition-colors">
                                {{ $data->name }}
                            </h3>
                        </div>

                        {{-- Description --}}
                        <div class="flex-grow mb-6">
                            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-3 group-hover:line-clamp-none transition-all duration-300">
                                {{ $data->description ?? __('No description provided.') }}
                            </p>
                        </div>

                        {{-- Actions Area --}}
                        <div class="flex items-center gap-2 pt-4 border-t border-slate-50 dark:border-slate-800">
                            <flux:modal.trigger wire:click="editTeacherCategory({{ $data->id }})" name="edit-teacher-category">
                                <flux:button variant="ghost" icon="pencil-square" class="flex-1 !rounded-xl !text-slate-500 hover:!text-indigo-600">
                                    {{ __('Edit') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to change the status of this category?"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                color="{{ $data->active_status ? 'red' : 'primary' }}"
                                variant="subtle"
                                class="flex-1 !rounded-xl"
                            >
                                {{ $data->active_status ? __('Disable') : __('Enable') }}
                            </flux:button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center bg-slate-50/50 dark:bg-slate-900/50 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
                        <flux:icon.tag size="xl" class="mx-auto text-slate-200 mb-4" />
                        <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest">{{ __('No Categories Found') }}</h3>
                        <p class="text-xs text-slate-400 mt-2 italic">{{ __('Start by clicking "Add New Category" above') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $teacherCategories->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewTeacherCategory" name="add-new-teacher-category" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-xl shadow-indigo-500/30">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('New Category') }}</flux:heading>
                        <flux:text>{{ __('Categorize staff by their professional track.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewTeacherCategory" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input label="Teacher Category ID" wire:model.live="categories_id" placeholder="TCAT0000" mask="TCAT9999" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Category Name" wire:model.live="name" placeholder="e.g. Permanent Staff" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Description" wire:model.live="description" placeholder="Briefly describe the scope of this category..." class="!rounded-xl" rows="4" />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Add Category') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditTeacherCategory" name="edit-teacher-category" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-amber-500 rounded-[1.5rem] flex items-center justify-center text-white shadow-xl shadow-amber-500/30">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('Edit Category') }}</flux:heading>
                        <flux:text>{{ __('Update category name or description.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateTeacherCategory" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Teacher Category ID" wire:model.live="update_categories_id" mask="TCAT9999" class="!rounded-xl opacity-60 bg-slate-50" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Category Name" wire:model.live="update_name" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Description" wire:model.live="update_description" class="!rounded-xl" rows="4" />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>