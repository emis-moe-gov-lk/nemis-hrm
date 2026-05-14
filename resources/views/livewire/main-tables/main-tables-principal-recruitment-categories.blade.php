<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase">
                        {{ __('Recruitment Categories') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.user-plus variant="micro" class="text-slate-400" />
                        {{ __('Manage principal recruitment streams and entry types') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-principal-recruitment-category">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add Category') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Notifications --}}
            @if (session()->has('message'))
                <div class="mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- CATEGORY LIST --}}
            <div class="space-y-3">
                @forelse ($principalrecruitmentcategories as $key => $data)
                    <div class="group flex flex-col md:flex-row md:items-center justify-between p-4 md:p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] hover:border-indigo-500/40 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 gap-4">
                        
                        <div class="flex items-center gap-6">
                            {{-- Index --}}
                            <div class="hidden md:flex shrink-0 w-12 h-12 items-center justify-center rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-xs font-black text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                {{ $principalrecruitmentcategories->firstItem() + $key }}
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded-md">
                                        {{ $data->category_id }}
                                    </span>
                                    <span class="inline-flex h-1.5 w-1.5 rounded-full {{ $data->active_status ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                </div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">
                                    {{ $data->category_name }}
                                </h3>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2 md:opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <flux:modal.trigger wire:click="editPrincipalRecruitmentCategory({{ $data->id }})" name="edit-principal-recruitment-category">
                                <flux:button variant="ghost" icon="pencil-square" class="!rounded-xl text-slate-500 hover:!bg-indigo-50 hover:!text-indigo-600">
                                    {{ __('Modify') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to toggle this category?"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                color="{{ $data->active_status ? 'red' : 'primary' }}"
                                variant="subtle"
                                class="!rounded-xl"
                            >
                                {{ $data->active_status ? __('Deactivate') : __('Activate') }}
                            </flux:button>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center bg-slate-50/50 dark:bg-slate-900/50 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
                        <flux:icon.user-plus size="xl" class="mx-auto text-slate-200 mb-4" />
                        <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest">{{ __('No Categories Defined') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-10 px-4">
                {{ $principalrecruitmentcategories->links() }}
            </div>
        </div>

        {{-- MODAL: ADD --}}
        <flux:modal wire:model="showModelNewPrincipalRecruitmentCategory" name="add-new-principal-recruitment-category" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white">
                        <flux:icon.plus />
                    </div>
                    <flux:heading size="lg" class="!font-black tracking-tight uppercase leading-none">{{ __('New Category') }}</flux:heading>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 text-rose-800 text-xs font-bold border border-rose-100">{{ session('error') }}</div>
                @endif

                <form wire:submit.prevent="addNewPrincipalRecruitmentCategory" class="space-y-4">
                    @csrf
                    <flux:field>
                        <flux:input label="Category ID" wire:model.live="principalRecruitmentCategoryId" placeholder="PRC001" mask="PRC999" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Category Name" wire:model.live="principalRecruitmentCategoryName" placeholder="e.g. Open Competitive" class="!rounded-xl" />
                    </flux:field>

                    <div class="flex gap-2 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl">{{ __('Create Category') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditPrincipalRecruitmentCategory" name="edit-principal-recruitment-category" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white">
                        <flux:icon.pencil-square />
                    </div>
                    <flux:heading size="lg" class="!font-black tracking-tight uppercase leading-none">{{ __('Edit Category') }}</flux:heading>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 text-rose-800 text-xs font-bold border border-rose-100">{{ session('error') }}</div>
                @endif

                <form wire:submit.prevent="updatePrincipalRecruitmentCategoryList" class="space-y-4">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Category ID" wire:model.live="updatePrincipalRecruitmentCategoryId" class="!rounded-xl bg-slate-50 opacity-60" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Category Name" wire:model.live="updatePrincipalRecruitmentCategoryName" class="!rounded-xl" />
                    </flux:field>

                    <div class="flex gap-2 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-main-tables.layout>
</section>