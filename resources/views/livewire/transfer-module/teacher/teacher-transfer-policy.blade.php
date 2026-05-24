<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        :title="$policyId ? __('Edit Transfer Policy') : __('Create Transfer Policy')"
        subtitle="{{ __('Set rules and approval workflow for institutional transfers') }}"
        icon="document-text"
        :breadcrumbs="[
            'Policies' => route('transfer.transfer-policies'),
            $policyId ? __('Edit') : __('Create') => '#'
        ]"
    >
        <x-slot:actions>
            <flux:button href="{{ route('transfer.transfer-policies') }}" variant="subtle" icon="chevron-left" size="sm" class="h-11 font-bold">
                {{ __('Back to Policies') }}
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="flex items-center gap-3">
        <flux:badge variant="neutral" size="sm" class="px-3 py-1 rounded-xl">
            <span class="text-slate-500 dark:text-zinc-400 mr-1 font-medium">{{ __('Issuing Office:') }}</span>
            <span class="font-bold text-slate-700 dark:text-zinc-200">{{ $myOfficeLevel }}</span>
        </flux:badge>
    </div>

    <form wire:submit.prevent="save" class="space-y-12">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 animate-in fade-in duration-500">
                <div class="flex items-center gap-3">
                    <flux:icon name="check-circle" variant="micro" class="text-green-600 dark:text-green-400" />
                    <p class="text-green-800 dark:text-green-200 font-bold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 animate-in fade-in duration-500">
                <div class="flex items-center gap-3">
                    <flux:icon name="x-circle" variant="micro" class="text-red-600 dark:text-red-400" />
                    <p class="text-red-800 dark:text-red-200 font-bold">{{ session('error') }}</p>
                </div>
            </div>
        @endif

            {{-- General Information Section --}}
            <section class="space-y-6">
                <div>
                    <flux:heading level="2" size="lg" class="mb-1">{{ __('General Information') }}</flux:heading>
                    <flux:subheading>{{ __('Basic details about the transfer policy and circular.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

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
                        <flux:input label="{{ __('Policy Year') }}" type="number" wire:model.live="policyYear" placeholder="e.g. 2026" min="2020" step="1" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" :invalid="$errors->has('policyYear')" />
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
                <div>
                    <flux:heading level="2" size="lg" class="mb-1">{{ __('Categories') }}</flux:heading>
                    <flux:subheading>{{ __('Select the transfer categories that should be available for this policy, then choose the transfer board level for each selected category.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                    @foreach($transferSubCategoryOptions as $subCategory)
                        @php
                            $isSelected = $this->isTransferSubCategorySelected($subCategory->transfer_sub_category_id);
                            $isFixedBoardLevel = $this->isFixedBoardLevelSubCategory($subCategory);
                            $boardLevelOptions = $this->policyBoardLevelOptionsForSubCategory($subCategory);
                            $selectedBoardOfficeLevel = $this->selectedBoardOfficeLevelForSubCategory($subCategory);
                        @endphp

                        <div
                            @class([
                                'w-full rounded-3xl border p-4 text-left transition-all duration-200',
                                'border-blue-500/70 bg-blue-50/80 shadow-lg shadow-blue-500/10 ring-1 ring-blue-500/15 dark:border-blue-400/60 dark:bg-blue-950/30 dark:shadow-blue-950/30 dark:ring-blue-400/15' => $isSelected,
                                'border-slate-200 bg-white/95 shadow-sm hover:border-slate-300 hover:shadow-md dark:border-zinc-700/80 dark:bg-zinc-900 dark:hover:border-zinc-500' => !$isSelected,
                            ])
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-black text-slate-950 dark:text-zinc-100">{{ $subCategory->name }}</span>
                                        @if($subCategory->active_status)
                                            <flux:badge color="green" size="xs" class="uppercase tracking-tighter">{{ __('Available') }}</flux:badge>
                                        @endif
                                    </div>
                                    <p class="text-sm leading-6 text-slate-600 dark:text-zinc-400">{{ $this->descriptionForSubCategory($subCategory) }}</p>
                                </div>

                                <button
                                    type="button"
                                    wire:click="toggleTransferSubCategory('{{ $subCategory->transfer_sub_category_id }}')"
                                    class="inline-flex shrink-0 items-center gap-2 rounded-full border px-3 py-1 text-[11px] font-black uppercase tracking-[0.22em] transition {{ $isSelected ? 'border-blue-600 bg-blue-600 text-white shadow-sm shadow-blue-500/20 hover:bg-blue-700 dark:border-blue-400 dark:bg-blue-500 dark:hover:bg-blue-400' : 'border-slate-300 bg-white text-slate-600 hover:border-blue-500 hover:text-blue-700 dark:border-zinc-700 dark:bg-zinc-900/80 dark:text-zinc-300 dark:hover:border-blue-400 dark:hover:text-blue-200' }}"
                                >
                                    <span @class([
                                        'flex h-4 w-4 items-center justify-center rounded-full border',
                                        'border-white bg-white text-blue-600' => $isSelected,
                                        'border-slate-400 dark:border-zinc-500' => !$isSelected,
                                    ])>
                                        @if($isSelected)
                                            <svg class="h-2.5 w-2.5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                <path d="M5 10.5l3 3 7-7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        @endif
                                    </span>
                                    {{ $isSelected ? __('Selected') : __('Select') }}
                                </button>
                            </div>

                            <div @class([
                                'mt-4 rounded-2xl border p-3 transition-all duration-200',
                                'border-blue-200 bg-blue-50/60 shadow-sm shadow-blue-500/10 dark:border-blue-400/30 dark:bg-blue-950/25 dark:shadow-blue-950/20' => $isSelected,
                                'border-slate-200 bg-slate-50/70 dark:border-zinc-700/70 dark:bg-zinc-950/45' => !$isSelected,
                            ])>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-blue-700 dark:text-blue-300">
                                        {{ __('Transfer Board Level') }}
                                    </p>

                                    @if($isFixedBoardLevel)
                                        <span class="rounded-full border border-slate-300 bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-slate-600 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200">{{ __('Fixed') }}</span>
                                    @elseif(!$isSelected)
                                        <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-slate-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-400">{{ __('Select category first') }}</span>
                                    @endif
                                </div>

                                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                    @foreach($boardLevelOptions as $option)
                                        @php
                                            $isCurrentBoardLevel = $selectedBoardOfficeLevel === $option['id'];
                                            $showCurrentBoardLevel = $isSelected && $isCurrentBoardLevel;
                                            $boardLevelHintClass = match (true) {
                                                $showCurrentBoardLevel && !$isFixedBoardLevel => 'text-blue-100 dark:text-blue-200',
                                                $showCurrentBoardLevel && $isFixedBoardLevel => 'text-slate-600 dark:text-zinc-300',
                                                default => 'text-slate-500 dark:text-zinc-500',
                                            };
                                        @endphp

                                        <button
                                            type="button"
                                            wire:click.stop="setTransferCategoryBoardLevel('{{ $subCategory->transfer_sub_category_id }}', '{{ $option['id'] }}')"
                                            @disabled(!$isSelected || $isFixedBoardLevel)
                                            @class([
                                                'group flex min-h-12 w-full items-center justify-between gap-3 rounded-xl border px-3 py-2 text-left transition focus:outline-none focus:ring-2 focus:ring-blue-500/30',
                                                'border-blue-600 bg-blue-600 text-white shadow-sm shadow-blue-500/25 ring-1 ring-blue-500/20 dark:border-sky-400 dark:bg-sky-500/20 dark:text-sky-50 dark:ring-sky-300/20' => $showCurrentBoardLevel && !$isFixedBoardLevel,
                                                'cursor-not-allowed border-slate-400 bg-slate-100 text-slate-800 shadow-sm ring-1 ring-slate-300/70 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 dark:ring-zinc-700/80' => $showCurrentBoardLevel && $isFixedBoardLevel,
                                                'border-slate-300 bg-white text-slate-800 hover:border-blue-500 hover:bg-blue-50 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:border-sky-400/80 dark:hover:bg-sky-500/10' => !$showCurrentBoardLevel && $isSelected && !$isFixedBoardLevel,
                                                'cursor-not-allowed border-slate-200 bg-white/60 text-slate-400 opacity-65 dark:border-zinc-800 dark:bg-zinc-950/70 dark:text-zinc-500 dark:opacity-100' => !$isSelected || ($isFixedBoardLevel && !$showCurrentBoardLevel),
                                            ])
                                        >
                                            <span class="min-w-0">
                                                <span class="block text-[13px] font-black leading-5">{{ $option['name'] }}</span>
                                                <span class="mt-0.5 block text-[10px] font-black uppercase tracking-[0.18em] {{ $boardLevelHintClass }}">
                                                    {{ $this->boardOfficeLevelHint($option['id']) }}
                                                </span>
                                            </span>

                                            <span @class([
                                                'flex h-6 w-6 shrink-0 items-center justify-center rounded-full border text-[0px]',
                                                'border-white bg-white text-blue-600 dark:border-sky-300 dark:bg-sky-400 dark:text-blue-950' => $showCurrentBoardLevel,
                                                'border-slate-400 text-slate-500 dark:border-zinc-500 dark:bg-zinc-950 dark:text-zinc-500' => !$showCurrentBoardLevel,
                                            ])>
                                                @if($showCurrentBoardLevel)
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                        <path d="M5 10.5l3 3 7-7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </button>
                                    @endforeach
                                </div>

                                <div class="mt-2 grid gap-2 lg:grid-cols-[0.7fr_1.3fr]">
                                    <div class="rounded-xl border border-slate-200 bg-white/80 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950/70">
                                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-blue-700/75 dark:text-sky-300/80">{{ __('Stage') }}</p>
                                        <p class="mt-0.5 text-sm font-black text-slate-950 dark:text-zinc-100">{{ $this->boardWorkflowLabelForSubCategory($subCategory) }}</p>
                                    </div>

                                    <div class="rounded-xl border border-slate-200 bg-white/80 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950/70">
                                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-blue-700/75 dark:text-sky-300/80">{{ __('Targeting') }}</p>
                                        <p class="mt-0.5 text-sm font-bold leading-5 text-slate-950 dark:text-zinc-100">{{ $this->zoneScopeSummaryForSubCategory($subCategory) }}</p>
                                    </div>
                                </div>
                            </div>

                            <flux:error name="selectedTransferCategoryBoardLevels.{{ $subCategory->transfer_sub_category_id }}" />
                        </div>
                    @endforeach
                </div>

                @if($this->selectedTransferSubCategories->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/35">
                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-500">{{ __('Selected Categories') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($this->selectedTransferSubCategories as $subCategory)
                                <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter">{{ $subCategory->name }}</flux:badge>
                            @endforeach
                        </div>
                    </div>
                @endif

                <flux:error name="selectedTransferSubCategoryIds" />
                <flux:error name="selectedTransferSubCategoryIds.*" />
            </section>

            {{-- Instiution special category national school only--}}
            <section class="space-y-6">
                <div class="rounded-2xl border border-slate-300 bg-slate-50/60 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">
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
                        <flux:input label="{{ __('Min. Service in Current School (Years)') }}" type="number" wire:model.live="minServiceCurrentSchool" placeholder="5" min="0" step="1" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" :invalid="$errors->has('minServiceCurrentSchool')" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="{{ __('Min. Total Service (Years)') }}" type="number" wire:model.live="minServiceTotal" placeholder="10" min="0" step="1" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" :invalid="$errors->has('minServiceTotal')" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="{{ __('Maximum School Preferences') }}" type="number" wire:model.live="maxPreferences" placeholder="5" min="1" max="10" step="1" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" :invalid="$errors->has('maxPreferences')" />
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
                    <div class="rounded-2xl border border-slate-300 bg-slate-50/60 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">
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
                    <div class="rounded-2xl border border-slate-300 bg-slate-50/60 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">
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
                        <div class="rounded-2xl border border-slate-300 bg-slate-50/60 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">
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

                        <div class="rounded-2xl border border-slate-300 bg-slate-50/60 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">
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

                    <div class="rounded-2xl border border-slate-300 bg-slate-50/60 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">
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
                    <div class="relative flex flex-col items-start p-4 transition-all duration-200 border rounded-2xl group {{ $this->isApprovalStepSelected($option->office_level_id) ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-500/10 dark:border-indigo-500' : 'border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm' }}">
                        <div wire:click="toggleApprovalStep('{{ $option->office_level_id }}')" class="flex flex-col items-start w-full cursor-pointer">
                            <div class="flex items-center justify-between w-full mb-2">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ $this->isApprovalStepSelected($option->office_level_id) ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-zinc-800 text-slate-500' }}">
                                    @if ($this->isApprovalStepSelected($option->office_level_id))
                                    <span class="text-xs font-bold">{{ $this->approvalStepNumber($option->office_level_id) }}</span>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    @endif
                                </div>

                                @if ($this->isApprovalStepSelected($option->office_level_id))
                                <span class="flex items-center gap-1 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ __('Step') }} {{ $this->approvalStepNumber($option->office_level_id) }}
                                </span>
                                @endif
                            </div>

                            <span class="text-sm font-bold {{ $this->isApprovalStepSelected($option->office_level_id) ? 'text-indigo-900 dark:text-indigo-100' : 'text-slate-700 dark:text-zinc-300' }}">
                                {{ $option->office_level_name }} ({{ $option->short_name }})
                            </span>
                        </div>

                        @if ($this->isApprovalStepSelected($option->office_level_id))
                        <div class="mt-4 space-y-3 w-full border-t border-indigo-100 dark:border-indigo-900/50 pt-4" onclick="event.stopPropagation()">
                            <flux:field>
                                <flux:input label="{{ __('Processing Window Start') }}" type="date" size="sm" wire:model="stepDates.{{ $option->office_level_id }}.start" />
                            </flux:field>
                            <flux:field>
                                <flux:input label="{{ __('Processing Window End') }}" type="date" size="sm" wire:model="stepDates.{{ $option->office_level_id }}.end" />
                            </flux:field>
                        </div>
                        @else
                        <div class="mt-2 text-[10px] text-slate-500 dark:text-zinc-400 italic">
                            {{ __('Click to add to workflow') }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <flux:error name="approvalSteps" />

                @if(!empty($approvalSteps))
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">{{ __('Calculated Approval Sequence') }}</div>
                    <div class="flex flex-wrap items-center gap-3">
                        @foreach ($approvalSteps as $index => $stepId)
                        @php
                        $stepName = collect($hierarchyOptions)->firstWhere('office_level_id', $stepId)->office_level_name;
                        @endphp
                        <div class="flex items-center gap-2">
                            <div class="px-3 py-1.5 rounded-full bg-white dark:bg-zinc-900 border border-slate-300 dark:border-zinc-700 text-xs font-semibold text-slate-700 dark:text-zinc-300 shadow-sm">
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

            <div class="flex items-center justify-end gap-3 pt-8 border-t border-slate-200 dark:border-zinc-700">
                <flux:button href="{{ route('transfer.transfer-policies') }}" variant="ghost" class="h-11 font-bold">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" icon="check" class="h-11 px-8 font-bold bg-indigo-600! hover:bg-indigo-700! border-none text-white shadow-lg shadow-indigo-200 dark:shadow-none" wire:loading.attr="disabled">
                    {{ $policyId ? __('Update Policy') : __('Create Policy') }}
                </flux:button>
            </div>
        </form>
    </div>
