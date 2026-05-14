<div>
    <div class="relative mb-6 w-full">
        <div class="flex items-center justify-between mb-6">
            <div>
                <flux:heading size="xl" level="1">{{ $policyId ? __('Edit Transfer Policy') : __('Create Transfer Policy') }}</flux:heading>
                <flux:subheading size="lg">{{ __('Set rules and approval workflow for institutional transfers') }}</flux:subheading>
            </div>
            <flux:button href="{{ route('transfer.transfer-policies') }}" variant="ghost" icon="chevron-left" size="sm">
                {{ __('Back to Policies') }}
            </flux:button>
        </div>
        <flux:separator variant="subtle" />

        <div class="my-6">
            <flux:badge variant="neutral" size="sm" class="px-3 py-1">
                <span class="text-slate-500 dark:text-zinc-400 mr-1 font-medium">{{ __('Issuing Office:') }}</span>
                <span class="font-bold text-slate-700 dark:text-zinc-200">{{ $myOfficeLevel }}</span>
            </flux:badge>
        </div>

        <form wire:submit.prevent="save" class="my-6 max-w-4xl space-y-8">
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

            {{-- General Information Section --}}
            <section class="space-y-6">
                <div>
                    <flux:heading level="2" size="lg" class="mb-1">{{ __('General Information') }}</flux:heading>
                    <flux:subheading>{{ __('Basic details about the transfer policy and circular.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:select label="{{ __('Applicable Service') }}" wire:model="service" :invalid="$errors->has('service')">
                            <option value="">{{ __('Select Service...') }}</option>
                            @foreach($serviceOptions as $serviceItem)
                            <option value="{{ $serviceItem->service_id }}">{{ $serviceItem->service_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:select label="{{ __('Transfer Type') }}" wire:model="transferType" :invalid="$errors->has('transferType')">
                            <option value="">{{ __('Select Transfer Type...') }}</option>
                            @foreach($transferTypeOptions as $type)
                            <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:input label="{{ __('Policy Year') }}" type="number" wire:model="policyYear" placeholder="e.g. 2026" :invalid="$errors->has('policyYear')" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="{{ __('Circular Number') }}" type="text" wire:model="circularNumber" placeholder="e.g. ED/POL/2026/01" :invalid="$errors->has('circularNumber')" />
                        <flux:error name="circularNumber" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:input label="{{ __('Policy Title') }}" type="text" wire:model="title" placeholder="{{ __('Enter a descriptive title for this policy') }}" :invalid="$errors->has('title')" />
                </flux:field>

                <flux:field>
                    <flux:textarea label="{{ __('Description (Optional)') }}" wire:model="description" placeholder="{{ __('Provide additional details or notes about this policy...') }}" rows="3" :invalid="$errors->has('description')" />
                </flux:field>
            </section>

            <flux:separator variant="subtle" />

            {{-- Transfer Categories --}}
            <section class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading level="2" size="lg" class="mb-1">{{ __('Transfer Categories') }}</flux:heading>
                        <flux:subheading>{{ __('Add transfer categories for this policy') }}</flux:subheading>
                    </div>
                    <flux:button wire:click="addTransferCategory" variant="outline" color="primary" icon="plus" size="sm">
                        {{ __('Add Category') }}
                    </flux:button>
                </div>

                <div class="space-y-4">
                    @forelse($transferCategories as $index => $category)
                    <div class="flex items-center justify-between p-4 border border-slate-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 shadow-sm">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ $category['name'] }}</span>
                            <span class="text-xs text-slate-500 dark:text-zinc-400">
                                @php
                                $levelName = collect($hierarchyOptions)->firstWhere('office_level_id', $category['office_level'])->office_level_name ?? __('Unknown Level');
                                @endphp
                                {{ __('Office Level:') }} {{ $levelName }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="editTransferCategory({{ $index }})" variant="ghost" icon="pencil" size="xs" />
                            <flux:button wire:click="removeTransferCategory({{ $index }})" variant="ghost" color="danger" icon="trash" size="xs" />
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center p-8 border border-dashed border-slate-200 dark:border-zinc-800 rounded-xl bg-slate-50/50 dark:bg-zinc-900/50 text-center">
                        <svg class="w-10 h-10 text-slate-300 dark:text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="text-sm text-slate-500 dark:text-zinc-400">{{ __('No transfer categories added yet.') }}</span>
                        <flux:button wire:click="addTransferCategory" variant="ghost" size="sm" class="mt-4">{{ __('Add the first category') }}</flux:button>
                    </div>
                    @endforelse
                </div>

                {{-- Category Modal --}}
                <flux:modal wire:model="showCategoryModal" name="category-modal" class="min-w-[400px]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ $editingCategoryIndex !== null ? __('Edit Category') : __('Add Category') }}</flux:heading>
                            <flux:subheading>{{ __('Define the details for this transfer category.') }}</flux:subheading>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <flux:field>
                                <flux:select label="{{ __('Transfer Board Office Level') }}" wire:model="categoryModalOfficeLevel" placeholder="{{ __('Select Office Level...') }}" :invalid="$errors->has('categoryModalOfficeLevel')">
                                    <option value="">{{ __('Select Office Level...') }}</option>
                                    @foreach($hierarchyOptions as $level)
                                    <option value="{{ $level->office_level_id }}">{{ $level->office_level_name }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <flux:field>
                                <flux:input label="{{ __('Transfer Category Name') }}" wire:model="categoryModalName" placeholder="e.g. Annual Transfer" :invalid="$errors->has('categoryModalName')" />
                            </flux:field>

                            <flux:field>
                                <flux:textarea label="{{ __('Description (Optional)') }}" wire:model="categoryModalDescription" placeholder="{{ __('Provide additional details or notes...') }}" rows="3" :invalid="$errors->has('categoryModalDescription')" />
                            </flux:field>
                        </div>

                        <div class="flex gap-3 justify-end mt-4">
                            <flux:modal.close>
                                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                            </flux:modal.close>
                            <flux:button wire:click="saveCategoryModal" variant="primary">{{ __('Save Category') }}</flux:button>
                        </div>
                    </div>
                </flux:modal>

                <flux:error name="transferCategories" />
            </section>

            {{-- Instiution special category national school only--}}
            <section class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                    <div class="flex lg:items-center lg:justify-between">
                        <div class="">
                            <flux:checkbox wire:model.live="isNsCategoryConsidered" label="{{ __('Enable Institution(National Schools) Special Categories') }}" />
                            <p class="mt-1 ml-6 text-sm text-slate-500 dark:text-zinc-400">A special category provided only for National Schools. It is considered when making various appointments or transfers to schools. <br>(A, B, C, D')</p>
                        </div>
                    </div>
                </div>
            </section>

            <flux:separator variant="subtle" />

            {{-- Service Requirements Section --}}
            <section class="space-y-6">
                <div>
                    <flux:heading level="2" size="lg" class="mb-1">{{ __('Service Requirements') }}</flux:heading>
                    <flux:subheading>{{ __('Define the minimum service criteria for eligibility.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <flux:field>
                        <flux:input label="{{ __('Min. Service in Current School (Years)') }}" type="number" wire:model="minServiceCurrentSchool" placeholder="5" :invalid="$errors->has('minServiceCurrentSchool')" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="{{ __('Min. Total Service (Years)') }}" type="number" wire:model="minServiceTotal" placeholder="10" :invalid="$errors->has('minServiceTotal')" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="{{ __('Maximum School Preferences') }}" type="number" wire:model="maxPreferences" placeholder="5" min="1" max="10" :invalid="$errors->has('maxPreferences')" />
                    </flux:field>
                </div>
            </section>

            <flux:separator variant="subtle" />

            {{-- Scoring Rules Section --}}
            <section class="space-y-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                    <div>
                        <flux:heading level="2" size="lg" class="mb-1">{{ __('Scoring Rules') }}</flux:heading>
                        <flux:subheading>{{ __('Optional policy-level criteria used by transfer boards to support decisions.') }}</flux:subheading>
                    </div>
                    <flux:badge color="blue" size="sm" class="uppercase tracking-tighter">{{ __('Decision Support') }}</flux:badge>
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <flux:checkbox wire:model.live="scoreRules.distance_current_workplace.enabled" label="{{ __('Distance from permanent address to current workplace') }}" />
                                <p class="mt-1 ml-6 text-sm text-slate-500 dark:text-zinc-400">{{ __('Road kilometres are multiplied by this score per km.') }}</p>
                            </div>
                            <div class="grid min-w-full grid-cols-1 gap-3 sm:grid-cols-[auto,12rem] lg:min-w-88">
                                <flux:input type="number" step="0.01" min="0" wire:model="scoreRules.distance_current_workplace.score_per_unit" label="{{ __('Score / km') }}" />
                            </div>
                        </div>
                    </div>

                    @foreach(['current_difficulty_years' => __('Current workplace difficulty'), 'previous_difficulty_years' => __('Previous difficult-area service')] as $criteriaKey => $criteriaLabel)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-xl">
                                <flux:checkbox wire:model.live="scoreRules.{{ $criteriaKey }}.enabled" label="{{ $criteriaLabel }}" />
                                <p class="mt-1 ml-6 text-sm text-slate-500 dark:text-zinc-400">{{ __('Each facility score is multiplied by service years.') }}</p>
                            </div>
                        </div>

                        @if($scoreRules[$criteriaKey]['enabled'] ?? false)
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                            @foreach($facilityScoreOptions as $facility)
                            <flux:input
                                type="number"
                                step="0.01"
                                min="0"
                                wire:model="scoreRules.{{ $criteriaKey }}.facility_scores.{{ $facility['id'] }}"
                                label="{{ $facility['name'] }}" />
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <flux:checkbox wire:model.live="scoreRules.age.enabled" label="{{ __('Age') }}" />
                                    <p class="mt-1 ml-6 text-sm text-slate-500 dark:text-zinc-400">{{ __('Base age gives 1 point; each additional full year adds 1.') }}</p>
                                </div>
                            </div>
                            @if($scoreRules['age']['enabled'] ?? false)
                            <div class="mt-4 max-w-xs">
                                <flux:input type="number" step="1" min="0" wire:model="scoreRules.age.base_value" label="{{ __('Base Age') }}" />
                            </div>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <flux:checkbox wire:model.live="scoreRules.current_station_years.enabled" label="{{ __('Current-station years') }}" />
                                    <p class="mt-1 ml-6 text-sm text-slate-500 dark:text-zinc-400">{{ __('Base years gives 1 point; each additional full year adds 1.') }}</p>
                                </div>
                            </div>
                            @if($scoreRules['current_station_years']['enabled'] ?? false)
                            <div class="mt-4 max-w-xs">
                                <flux:input type="number" step="1" min="0" wire:model="scoreRules.current_station_years.base_value" label="{{ __('Base Years') }}" />
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <flux:checkbox wire:model.live="scoreRules.achievements.enabled" label="{{ __('Achievements') }}" />
                                <p class="mt-1 ml-6 text-sm text-slate-500 dark:text-zinc-400">{{ __('Teacher-entered achievements are scored by level. Board users can include or exclude each achievement later.') }}</p>
                            </div>
                        </div>

                        @if($scoreRules['achievements']['enabled'] ?? false)
                        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                            @foreach($achievementLevels as $levelKey => $levelLabel)
                            <flux:input
                                type="number"
                                step="0.01"
                                min="0"
                                wire:model="scoreRules.achievements.level_scores.{{ $levelKey }}"
                                label="{{ __($levelLabel . ' Score') }}" />
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </section>

            <flux:separator variant="subtle" />

            <flux:separator variant="subtle" />

            {{-- Important Dates Section --}}
            <section class="space-y-6">
                <div>
                    <flux:heading level="2" size="lg" class="mb-1">{{ __('Important Dates') }}</flux:heading>
                    <flux:subheading>{{ __('Key dates for policy implementation and application window.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <flux:field>
                        <flux:input label="{{ __('Effective Date') }}" type="date" wire:model="effectiveDate" :invalid="$errors->has('effectiveDate')" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="{{ __('Application Start Date') }}" type="date" wire:model="startDate" :invalid="$errors->has('startDate')" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="{{ __('Application End Date') }}" type="date" wire:model="endDate" :invalid="$errors->has('endDate')" />
                    </flux:field>
                </div>
            </section>

            <flux:separator variant="subtle" />

            {{-- Approval Workflow Section --}}
            <section class="space-y-6">
                <div>
                    <flux:heading level="2" size="lg" class="mb-1">{{ __('Approval Workflow Path') }}</flux:heading>
                    <flux:subheading>{{ __('Select the sequential workflow steps for transfer approval. Steps will be processed in the order shown.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($hierarchyOptions as $option)
                    @php
                    $isSelected = in_array($option->office_level_id, $approvalSteps);
                    $stepNumber = array_search($option->office_level_id, $approvalSteps) !== false ? array_search($option->office_level_id, $approvalSteps) + 1 : null;
                    @endphp
                    <div class="relative flex flex-col items-start p-4 transition-all duration-200 border rounded-2xl group {{ $isSelected ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-500/10 dark:border-indigo-500' : 'border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm' }}">
                        <div wire:click="toggleApprovalStep('{{ $option->office_level_id }}')" class="flex flex-col items-start w-full cursor-pointer">
                            <div class="flex items-center justify-between w-full mb-2">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ $isSelected ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-zinc-800 text-slate-400' }}">
                                    @if ($isSelected)
                                    <span class="text-xs font-bold">{{ $stepNumber }}</span>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    @endif
                                </div>

                                @if ($isSelected)
                                <span class="flex items-center gap-1 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ __('Step') }} {{ $stepNumber }}
                                </span>
                                @endif
                            </div>

                            <span class="text-sm font-bold {{ $isSelected ? 'text-indigo-900 dark:text-indigo-100' : 'text-slate-700 dark:text-zinc-300' }}">
                                {{ $option->office_level_name }} ({{ $option->short_name }})
                            </span>
                        </div>

                        @if ($isSelected)
                        <div class="mt-4 space-y-3 w-full border-t border-indigo-100 dark:border-indigo-900/50 pt-4" onclick="event.stopPropagation()">
                            <flux:field>
                                <flux:input label="{{ __('Processing Window Start') }}" type="date" size="sm" wire:model="stepDates.{{ $option->office_level_id }}.start" />
                            </flux:field>
                            <flux:field>
                                <flux:input label="{{ __('Processing Window End') }}" type="date" size="sm" wire:model="stepDates.{{ $option->office_level_id }}.end" />
                            </flux:field>
                        </div>
                        @else
                        <div class="mt-2 text-[10px] text-slate-400 dark:text-zinc-500 italic">
                            {{ __('Click to add to workflow') }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <flux:error name="approvalSteps" />

                @if(!empty($approvalSteps))
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-100 dark:border-zinc-800">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">{{ __('Calculated Approval Sequence') }}</div>
                    <div class="flex flex-wrap items-center gap-3">
                        @foreach ($approvalSteps as $index => $stepId)
                        @php
                        $stepName = collect($hierarchyOptions)->firstWhere('office_level_id', $stepId)->office_level_name;
                        @endphp
                        <div class="flex items-center gap-2">
                            <div class="px-3 py-1.5 rounded-full bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 text-xs font-semibold text-slate-700 dark:text-zinc-300 shadow-sm">
                                {{ $stepName }}
                            </div>
                            @if (!$loop->last)
                            <svg class="w-4 h-4 text-slate-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </section>

            <flux:separator variant="subtle" />

            {{-- Policy Status --}}
            <section class="space-y-6">
                <flux:field>
                    <flux:select label="{{ __('Policy Status') }}" wire:model="status" :invalid="$errors->has('status')">
                        <option value="1">{{ __('Active') }}</option>
                        <option value="0">{{ __('Draft / Inactive') }}</option>
                    </flux:select>
                    <flux:error name="status" class="mt-2 text-[11px] font-semibold bg-red-50 dark:bg-red-950/20 px-2 py-1 rounded-md border-l-2 border-red-500" />
                </flux:field>
            </section>

            <div class="flex items-center justify-end gap-3 pt-4">
                <flux:button href="{{ route('transfer.transfer-policies') }}" variant="ghost">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" icon="check" class="px-8" wire:loading.attr="disabled">
                    {{ $policyId ? __('Update Policy') : __('Create Policy') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>