<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Specific Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('SLEAS Categories') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.academic-cap variant="micro" class="text-blue-500" />
                        {{ __('Education Administrator Service tiers and professional classifications') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-sleas-category">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all">
                            {{ __('Add new Category') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session()->has('message'))
                <div class="mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- CATEGORY CARDS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @forelse ($sleasCategories as $key => $data)
                    <div class="relative group bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl p-6 shadow-sm hover:shadow-xl hover:border-blue-400 dark:hover:border-blue-800 transition-all duration-300">
                        
                        {{-- Top Header: ID & Status --}}
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-500 tabular-nums tracking-widest uppercase mb-1">
                                    #{{ $sleasCategories->firstItem() + $key }}
                                </span>
                                <div class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 font-mono text-xs font-bold">
                                    <flux:icon.tag variant="micro" class="size-3" />
                                    {{ $data->category_id }}
                                </div>
                            </div>
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'blue' : 'red' }}" class="!font-black uppercase tracking-widest text-[9px]">
                                {{ $data->active_status ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </div>

                        {{-- Category Name --}}
                        <div class="mb-8">
                            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight min-h-[3.5rem] group-hover:text-blue-600 transition-colors">
                                {{ $data->category_name }}
                            </h3>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <flux:modal.trigger wire:click="editSleasCategory({{ $data->id }})" class="flex-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="w-full !rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-indigo-700">
                                    {{ __('Edit') }}
                                </flux:button>
                            </flux:modal.trigger>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to change this category status?"
                                size="sm" 
                                variant="filled" 
                                color="{{ $data->active_status == '1' ? 'red' : 'blue' }}"
                                icon="{{ $data->active_status == '1' ? 'no-symbol' : 'check' }}"
                                class="!rounded-xl shadow-sm"
                            />
                        </div>

                        {{-- Decorative background icon --}}
                        <div class="absolute bottom-6 right-6 opacity-[0.03] dark:opacity-[0.05] group-hover:opacity-10 transition-opacity">
                            <flux:icon.academic-cap size="xl" class="size-16" />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-4xl bg-slate-50 dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 mb-4 text-slate-300">
                            <flux:icon.academic-cap size="xl" />
                        </div>
                        <h3 class="text-lg font-black text-slate-500 uppercase tracking-widest">{{ __('No Categories Found') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $sleasCategories->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW CATEGORY --}}
        <flux:modal wire:model="showModelNewSleasCategory" name="add-new-sleas-category" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/30 rounded-[1.5rem] flex items-center justify-center text-blue-600">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Add New SLEAS Category') }}</flux:heading>
                        <flux:text>{{ __('Create a new professional service classification.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewSleasCategory" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input label="Category ID" wire:model.live="sleasCategoryId" mask="EASC999" placeholder="EASC000" />
                        <flux:error name="sleasCategoryId" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Category Name" wire:model.live="sleasCategory" placeholder="e.g. Grade I Administrator" />
                        <flux:error name="sleasCategory" />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-blue-500/20">{{ __('Save Category') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT CATEGORY --}}
        <flux:modal wire:model="showModelEditSleasCategory" name="edit-sleas-category" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/30 rounded-[1.5rem] flex items-center justify-center text-amber-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Edit Category') }}</flux:heading>
                        <flux:text>{{ __('Update the name or classification of this service tier.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateSleasCategoryList" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Category ID" wire:model.live="updateSleasCategoryId" mask="EASC999" class="bg-slate-50" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Category Name" wire:model.live="updateSleasCategory" />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-blue-500/20">{{ __('Update Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-main-tables.layout>
</section>