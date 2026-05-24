<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="font-black! tracking-tight text-slate-900 dark:text-white uppercase">
                        {{ __('Transfer Categories') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.arrows-right-left variant="micro" class="text-indigo-500" />
                        {{ __('Maintain the fixed teacher transfer workflow categories used by policies, boards, and appeals') }}
                    </flux:subheading>
                </div>
            </div>

            <div class="mb-6 rounded-4xl border border-amber-200/60 bg-amber-50/80 px-5 py-4 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-200">
                <div class="flex items-start gap-3">
                    <flux:icon.information-circle variant="micro" class="mt-0.5 shrink-0" />
                    <div class="space-y-1">
                        <p class="font-bold">{{ __('System codes are fixed for transfer logic.') }}</p>
                        <p>{{ __('You can update the visible name, description, office levels, routing rules, and display order here, but the internal category ID and workflow code are protected.') }}</p>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                @forelse ($transferSubCategories as $key => $category)
                    <div class="relative flex flex-col bg-white dark:bg-slate-900 border {{ $category->active_status ? 'border-slate-300 dark:border-slate-700' : 'border-rose-300/70 dark:border-rose-900/60' }} rounded-[2.5rem] p-7 shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="flex justify-between items-start gap-4 mb-6">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-700 font-black text-xs text-slate-500">
                                        {{ $key + 1 }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 leading-none">{{ __('System ID') }}</span>
                                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mt-1 uppercase leading-none">{{ $category->transfer_sub_category_id }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 pl-13">
                                    <flux:badge size="sm" variant="pill" color="slate" class="font-black! uppercase tracking-widest text-[9px]">
                                        {{ strtoupper(str_replace('_', ' ', $category->code)) }}
                                    </flux:badge>
                                    <flux:badge size="sm" variant="pill" color="{{ $category->active_status ? 'green' : 'red' }}" class="font-black! uppercase tracking-widest text-[9px]">
                                        {{ $category->active_status ? __('Available') : __('Inactive') }}
                                    </flux:badge>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-[10px] font-black uppercase tracking-[0.18em] text-slate-500">{{ __('Display Order') }}</span>
                                <div class="text-2xl font-black text-slate-900 dark:text-white tabular-nums mt-1">{{ $category->display_order }}</div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">
                                {{ $category->name }}
                            </h3>
                            <p class="mt-3 text-sm text-slate-500 dark:text-slate-500 leading-relaxed">
                                {{ $category->description ?: __('No description provided.') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
                            <div class="rounded-[1.75rem] border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-4">
                                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 mb-2">{{ __('Policy Office') }}</div>
                                <div class="text-lg font-black text-slate-900 dark:text-white leading-tight">
                                    {{ $category->policyOfficeLevel?->office_level_name ?? __('Not linked') }}
                                </div>
                            </div>
                            <div class="rounded-[1.75rem] border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-4">
                                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 mb-2">{{ __('Board Workflow') }}</div>
                                <div class="text-lg font-black text-slate-900 dark:text-white leading-tight">
                                    {{ $category->firstBoardOfficeLevel?->office_level_name ?? __('Not linked') }}
                                    @if ($category->secondBoardOfficeLevel)
                                        <span class="block text-base text-indigo-600 dark:text-indigo-400 mt-1">&rarr; {{ $category->secondBoardOfficeLevel->office_level_name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="rounded-[1.75rem] border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-4">
                                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 mb-2">{{ __('Targeting Rule') }}</div>
                                <div class="text-lg font-black text-slate-900 dark:text-white leading-tight">
                                    {{ $this->zoneScopeLabel($category->zone_scope_mode) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-6">
                            <flux:badge size="sm" variant="pill" color="{{ $category->requires_target_province_selection ? 'indigo' : 'slate' }}" class="font-black! uppercase tracking-widest text-[9px]">
                                {{ $category->requires_target_province_selection ? __('Target Province Required') : __('Target Province Locked') }}
                            </flux:badge>
                            <flux:badge size="sm" variant="pill" color="sky" class="font-black! uppercase tracking-widest text-[9px]">
                                {{ $this->institutionScopeLabel($category->institution_scope_mode) }}
                            </flux:badge>
                        </div>

                        <div class="flex gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <flux:modal.trigger name="edit-transfer-category" wire:click="editTransferCategory({{ $category->id }})" class="flex-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="w-full rounded-xl! border border-slate-300 dark:border-slate-700">
                                    {{ __('Edit Details') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:button
                                wire:click="toggleStatus({{ $category->id }})"
                                wire:confirm="Are you sure you want to change the availability of this transfer category?"
                                size="sm"
                                variant="filled"
                                color="{{ $category->active_status ? 'red' : 'primary' }}"
                                icon="{{ $category->active_status ? 'no-symbol' : 'check' }}"
                                class="rounded-xl! shadow-sm"
                            >
                                {{ $category->active_status ? __('Disable') : __('Enable') }}
                            </flux:button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-4xl bg-slate-50 dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 mb-4 text-slate-300">
                            <flux:icon.arrows-right-left size="xl" />
                        </div>
                        <h3 class="text-lg font-black text-slate-500 uppercase tracking-widest">{{ __('No Transfer Categories Found') }}</h3>
                    </div>
                @endforelse
            </div>
        </div>

        <flux:modal wire:model="showEditTransferCategoryModal" name="edit-transfer-category" class="w-full max-w-3xl rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-3xl flex items-center justify-center text-indigo-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-black! tracking-tight uppercase">{{ __('Edit Transfer Category') }}</flux:heading>
                        <flux:text>{{ __('Update the name, workflow levels, and targeting rules for this fixed category.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateTransferCategory" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input disabled label="System Category ID" wire:model.live="updateTransferSubCategoryId" class="rounded-xl! opacity-70" />
                        </flux:field>

                        <flux:field>
                            <flux:input disabled label="Workflow Code" wire:model.live="updateCode" class="rounded-xl! opacity-70" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input label="Category Name" wire:model.live="updateName" class="rounded-xl!" />
                        </flux:field>

                        <flux:field>
                            <flux:input type="number" min="1" step="1" inputmode="numeric" label="Display Order" wire:model.live="updateDisplayOrder" class="rounded-xl!" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:textarea label="Description" wire:model.live="updateDescription" rows="3" class="rounded-xl!" />
                    </flux:field>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:select label="Policy Office Level" wire:model.live="updatePolicyOfficeLevelId" class="rounded-xl!">
                                <option value="">{{ __('Select policy office level') }}</option>
                                @foreach ($officeLevels as $officeLevel)
                                    <option value="{{ $officeLevel->office_level_id }}">{{ $officeLevel->office_level_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:select label="First Board Office Level" wire:model.live="updateFirstBoardOfficeLevelId" class="rounded-xl!">
                                <option value="">{{ __('Select first board office level') }}</option>
                                @foreach ($officeLevels as $officeLevel)
                                    <option value="{{ $officeLevel->office_level_id }}">{{ $officeLevel->office_level_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:select label="Second Board Office Level" wire:model.live="updateSecondBoardOfficeLevelId" class="rounded-xl!">
                                <option value="">{{ __('No second stage') }}</option>
                                @foreach ($officeLevels as $officeLevel)
                                    <option value="{{ $officeLevel->office_level_id }}">{{ $officeLevel->office_level_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field class="flex items-center gap-3 rounded-3xl border border-slate-300 dark:border-slate-700 px-4 py-3">
                            <flux:checkbox wire:model.live="updateRequiresTargetProvinceSelection" />
                            <flux:label class="mb-0!">{{ __('Require target province selection') }}</flux:label>
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:select label="Zone Scope Mode" wire:model.live="updateZoneScopeMode" class="rounded-xl!">
                                <option value="">{{ __('Select zone scope') }}</option>
                                @foreach ($zoneScopeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:select label="Institution Scope Mode" wire:model.live="updateInstitutionScopeMode" class="rounded-xl!">
                                <option value="">{{ __('Select institution scope') }}</option>
                                @foreach ($institutionScopeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    @if ($updateCode !== \App\Support\Transfer\TransferSubCategoryRules::CODE_NATIONAL_SCHOOL)
                        <div class="rounded-2xl border border-slate-300/80 bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                            {{ __('Second board stage is only used when this category needs a follow-up board workflow, such as National School transfers.') }}
                        </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full rounded-xl!">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-2 rounded-xl! shadow-lg shadow-indigo-500/20">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>
