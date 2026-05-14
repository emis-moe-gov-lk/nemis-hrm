<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-slate-100 bg-slate-50/70 p-6 dark:border-zinc-800 dark:bg-zinc-800/30">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:badge color="blue" size="sm" class="uppercase tracking-widest">{{ filled($editingBoardId) ? __('Edit Flow') : __('Create Flow') }}</flux:badge>
                        <flux:badge variant="neutral" size="sm" class="uppercase tracking-widest">{{ __('Step') }} {{ $createBoardStep }} / {{ count($createBoardSteps) }}</flux:badge>
                        @if(filled($editingBoardId))
                            <flux:badge color="amber" size="sm" class="uppercase tracking-widest">{{ $editingBoardId }}</flux:badge>
                        @endif
                    </div>

                    <div>
                        <flux:heading size="lg">{{ filled($editingBoardId) ? ($isAppealBoard ? __('Edit :scope Appeal Board', ['scope' => $boardScopeTitle]) : __('Edit :scope Transfer Board', ['scope' => $boardScopeTitle])) : ($isAppealBoard ? __('Create :scope Appeal Board', ['scope' => $boardScopeTitle]) : __('Create :scope Transfer Board', ['scope' => $boardScopeTitle])) }}</flux:heading>
                        <flux:subheading>{{ filled($editingBoardId) ? __('Update the board configuration step by step, then save the revised board from the final review step.') : __('Complete each step in sequence, then create the board from the final review step.') }}</flux:subheading>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Board Scope') }}</p>
                    <p class="mt-2 font-semibold text-slate-900 dark:text-zinc-100">{{ $currentWorkplace?->office_name ?? __('N/A') }}</p>
                    <p class="mt-1 text-slate-500 dark:text-zinc-400">{{ $currentWorkplace?->officeLevel?->office_level_name ?? __(':scope Office', ['scope' => $boardScopeTitle]) }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-4">
                @foreach($createBoardSteps as $stepNumber => $step)
                    @php
                        $isActive = $createBoardStep === $stepNumber;
                        $isCompleted = $createBoardStep > $stepNumber;
                    @endphp

                    <div class="rounded-2xl border px-4 py-4 transition-all {{ $isActive ? 'border-blue-200 bg-blue-50/70 dark:border-blue-900/60 dark:bg-blue-950/20' : ($isCompleted ? 'border-emerald-200 bg-emerald-50/70 dark:border-emerald-900/60 dark:bg-emerald-950/20' : 'border-slate-200 bg-slate-50/60 dark:border-zinc-800 dark:bg-zinc-800/30') }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-black {{ $isActive ? 'bg-blue-600 text-white dark:bg-blue-500' : ($isCompleted ? 'bg-emerald-600 text-white dark:bg-emerald-500' : 'bg-slate-200 text-slate-700 dark:bg-zinc-700 dark:text-zinc-200') }}">
                                {{ $stepNumber }}
                            </div>

                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ __($step['title']) }}</p>
                                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ __($step['description']) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="p-6">
            @if($createBoardStep === 1)
                <div class="space-y-6">
                    <div>
                        <flux:heading size="base">{{ __('Step 1: Board Setup') }}</flux:heading>
                        <flux:subheading>{{ $isAppealBoard ? __('Choose the board date, active transfer policy, :scope appeal category, and editable board name.', ['scope' => $boardScopeAdjectiveLower]) : __('Choose the board date, active transfer policy, :scope transfer category, and editable board name.', ['scope' => $boardScopeAdjectiveLower]) }}</flux:subheading>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <flux:field>
                            <flux:input type="date" label="{{ __('Board Date') }}" wire:model.live="createBoardDate" :invalid="$errors->has('createBoardDate')" />
                        </flux:field>

                        <flux:field>
                            <flux:select label="{{ __('Transfer Policy') }}" wire:model.live="createPolicyId" :invalid="$errors->has('createPolicyId')">
                                <option value="">{{ __('Select active transfer policy...') }}</option>
                                @foreach($availablePolicies as $policy)
                                    <option value="{{ $policy->policy_id }}">{{ $policy->title }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:select label="{{ $isAppealBoard ? __('Appeal Board Category') : __('Transfer Board Category') }}" wire:model.live="createTransferCategoryId" :invalid="$errors->has('createTransferCategoryId')">
                                <option value="">{{ __('Select transfer category...') }}</option>
                                @foreach($availableCategories as $category)
                                    <option value="{{ $category->transfer_category_id }}">{{ $category->transfer_category_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:input
                            label="{{ $isAppealBoard ? __('Appeal Board Name') : __('Transfer Board Name') }}"
                            wire:model.live="createBoardName"
                            placeholder="{{ $suggestedCreateBoardName ?: __('Auto generated after selecting date and category') }}"
                            :invalid="$errors->has('createBoardName')"
                        />
                        <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">
                            {{ __('You can edit this name. If left blank, the system will use the suggested :scope/category/date name.', ['scope' => $boardScopeNameLower]) }}
                        </p>
                    </flux:field>

                    @if(filled($createPolicyId) && $availableCategories->isEmpty())
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
                            {{ __('No transfer categories are configured for :scope under the selected policy yet. Choose another :scopeType scope or configure the category setup first.', ['scope' => $currentWorkplace?->office_name ?? __('this ' . $boardScopeNameLower), 'scopeType' => $boardScopeAdjectiveLower]) }}
                        </div>
                    @endif

                    @error('createBoardDate') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    @error('createBoardName') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    @error('createPolicyId') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    @error('createTransferCategoryId') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror

                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Board Name') }}</p>
                            <p class="mt-2 text-base font-semibold text-slate-900 dark:text-zinc-100">{{ $createBoardName ?: ($suggestedCreateBoardName ?: __('Not named yet')) }}</p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Chosen Date') }}</p>
                            <p class="mt-2 text-base font-semibold text-slate-900 dark:text-zinc-100">{{ $createBoardDate ?: __('Not selected yet') }}</p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Transfer Policy') }}</p>
                            <p class="mt-2 text-base font-semibold text-slate-900 dark:text-zinc-100">{{ $selectedCreatePolicy?->title ?? __('Select a policy') }}</p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Transfer Category') }}</p>
                            <p class="mt-2 text-base font-semibold text-slate-900 dark:text-zinc-100">{{ $selectedCreateCategory?->transfer_category_name ?? __('Select a category') }}</p>
                        </div>
                    </div>
                </div>
            @elseif($createBoardStep === 2)
                <div class="space-y-6">
                    <div>
                        <flux:heading size="base">{{ $isAppealBoard ? __('Step 2: Appeal Board Subjects') : __('Step 2: Transfer Board Subjects') }}</flux:heading>
                        <flux:subheading>{{ __('Select the subjects for this board. Application matching later will use teacher main subject only.') }}</flux:subheading>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                        <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ __('Selected Subjects') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @forelse($selectedCreateSubjects as $subject)
                                <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">{{ $subject->name_en }}</flux:badge>
                            @empty
                                <span class="text-sm text-slate-500 dark:text-zinc-400">{{ __('No subjects selected yet.') }}</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="grid max-h-80 grid-cols-1 gap-3 overflow-y-auto rounded-3xl border border-slate-200 bg-slate-50/70 p-4 md:grid-cols-2 xl:grid-cols-3 dark:border-zinc-800 dark:bg-zinc-800/30">
                        @foreach($availableSubjects as $subject)
                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-slate-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-zinc-500">
                                <flux:checkbox wire:model="selectedSubjectIds" value="{{ $subject->subject_id }}" />
                                <span>{{ $subject->name_en }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('selectedSubjectIds') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    @error('selectedSubjectIds.*') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </div>
            @elseif($createBoardStep === 3)
                <div class="space-y-6">
                    <div>
                        <flux:heading size="base">{{ __('Step 3: Core Officers') }}</flux:heading>
                        <flux:subheading>{{ __('Search globally by NIC and attach the chairman and secretary before moving to the final step.') }}</flux:subheading>
                    </div>

                    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                        <div class="space-y-4 rounded-3xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                            <div>
                                <flux:heading size="base">{{ __('Board Chairman') }}</flux:heading>
                                <flux:subheading>{{ __('Use the same NIC lookup method already used elsewhere in the system.') }}</flux:subheading>
                            </div>

                            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                                <flux:field class="flex-1">
                                    <flux:input label="{{ __('Chairman NIC') }}" wire:model="chairmanNic" placeholder="{{ __('Enter NIC number') }}" :invalid="$errors->has('chairmanNic')" />
                                </flux:field>

                                <flux:button wire:click="searchChairmanNic" variant="primary">{{ __('Search NIC') }}</flux:button>
                            </div>

                            @error('chairmanNic') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror

                            @if(!empty($chairmanCandidate))
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 dark:border-emerald-900/60 dark:bg-emerald-950/30">
                                    <p class="text-sm font-bold text-emerald-900 dark:text-emerald-300">{{ $chairmanCandidate['full_name'] }}</p>
                                    <p class="mt-1 text-sm text-emerald-800 dark:text-emerald-200">{{ $chairmanCandidate['nic'] }}</p>
                                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700 dark:text-emerald-400">{{ __('Current Workplace') }}</p>
                                    <p class="mt-1 text-sm text-emerald-900 dark:text-emerald-200">{{ $chairmanCandidate['workplace_name'] }}</p>
                                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700 dark:text-emerald-400">{{ __('Office Level') }}</p>
                                    <p class="mt-1 text-sm text-emerald-900 dark:text-emerald-200">{{ $chairmanCandidate['office_level_name'] }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4 rounded-3xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                            <div>
                                <flux:heading size="base">{{ __('Board Secretary') }}</flux:heading>
                                <flux:subheading>{{ __('The secretary is searched globally too and must be different from the chairman.') }}</flux:subheading>
                            </div>

                            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                                <flux:field class="flex-1">
                                    <flux:input label="{{ __('Secretary NIC') }}" wire:model="secretaryNic" placeholder="{{ __('Enter NIC number') }}" :invalid="$errors->has('secretaryNic')" />
                                </flux:field>

                                <flux:button wire:click="searchSecretaryNic" variant="primary">{{ __('Search NIC') }}</flux:button>
                            </div>

                            @error('secretaryNic') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror

                            @if(!empty($secretaryCandidate))
                                <div class="rounded-2xl border border-blue-200 bg-blue-50/80 p-4 dark:border-blue-900/60 dark:bg-blue-950/30">
                                    <p class="text-sm font-bold text-blue-900 dark:text-blue-300">{{ $secretaryCandidate['full_name'] }}</p>
                                    <p class="mt-1 text-sm text-blue-800 dark:text-blue-200">{{ $secretaryCandidate['nic'] }}</p>
                                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-blue-700 dark:text-blue-400">{{ __('Current Workplace') }}</p>
                                    <p class="mt-1 text-sm text-blue-900 dark:text-blue-200">{{ $secretaryCandidate['workplace_name'] }}</p>
                                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-blue-700 dark:text-blue-400">{{ __('Office Level') }}</p>
                                    <p class="mt-1 text-sm text-blue-900 dark:text-blue-200">{{ $secretaryCandidate['office_level_name'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    <div>
                        <flux:heading size="base">{{ __('Step 4: Members & Review') }}</flux:heading>
                        <flux:subheading>{{ filled($editingBoardId) ? __('Update optional members if needed, then review the full board setup before saving the changes.') : __('Add any optional members you need, then review the complete board setup before creating it.') }}</flux:subheading>
                    </div>

                    <div class="space-y-4 rounded-3xl border border-slate-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                        <div>
                            <flux:heading size="base">{{ __('Optional Board Members') }}</flux:heading>
                            <flux:subheading>{{ __('Search by NIC and add any additional members you need for the board.') }}</flux:subheading>
                        </div>

                        <div class="flex flex-col gap-3 md:flex-row md:items-end">
                            <flux:field class="flex-1">
                                <flux:input label="{{ __('Member NIC') }}" wire:model="memberNic" placeholder="{{ __('Enter NIC number') }}" :invalid="$errors->has('memberNic')" />
                            </flux:field>

                            <flux:button wire:click="searchMemberNic" variant="ghost">{{ __('Search NIC') }}</flux:button>
                            <flux:button wire:click="addCreateMember" variant="primary">{{ __('Add Member') }}</flux:button>
                        </div>

                        @error('memberNic') <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror

                        @if(!empty($memberCandidate))
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-zinc-700 dark:bg-zinc-800/40">
                                <p class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ $memberCandidate['full_name'] }}</p>
                                <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">{{ $memberCandidate['nic'] }}</p>
                                <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Current Workplace') }}</p>
                                <p class="mt-1 text-sm text-slate-800 dark:text-zinc-200">{{ $memberCandidate['workplace_name'] }}</p>
                                <p class="mt-3 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Office Level') }}</p>
                                <p class="mt-1 text-sm text-slate-800 dark:text-zinc-200">{{ $memberCandidate['office_level_name'] }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @forelse(($createMembers ?? []) as $member)
                                <div class="flex items-start justify-between rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ $member['full_name'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $member['nic'] }}</p>
                                        <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">{{ $member['workplace_name'] }}</p>
                                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $member['office_level_name'] }}</p>
                                    </div>

                                    <flux:button wire:click="removeCreateMember('{{ $member['people_id'] }}')" variant="ghost" icon="trash" size="sm" />
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 p-4 text-sm text-slate-500 dark:border-zinc-800 dark:bg-zinc-800/30 dark:text-zinc-400 md:col-span-2">
                                    {{ __('No optional members added yet.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                        <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Board Summary') }}</p>
                            <div class="mt-4 space-y-3 text-sm text-slate-700 dark:text-zinc-300">
                                <p><span class="font-bold text-slate-500 dark:text-zinc-400">{{ __('Name') }}:</span> {{ $createBoardName ?: ($suggestedCreateBoardName ?: __('Not named')) }}</p>
                                <p><span class="font-bold text-slate-500 dark:text-zinc-400">{{ __('Date') }}:</span> {{ $createBoardDate ?: __('Not selected') }}</p>
                                <p><span class="font-bold text-slate-500 dark:text-zinc-400">{{ __('Policy') }}:</span> {{ $selectedCreatePolicy?->title ?? __('Not selected') }}</p>
                                <p><span class="font-bold text-slate-500 dark:text-zinc-400">{{ __('Category') }}:</span> {{ $selectedCreateCategory?->transfer_category_name ?? __('Not selected') }}</p>
                                <p><span class="font-bold text-slate-500 dark:text-zinc-400">{{ __('Chairman') }}:</span> {{ $chairmanCandidate['full_name'] ?? __('Not selected') }}</p>
                                <p><span class="font-bold text-slate-500 dark:text-zinc-400">{{ __('Secretary') }}:</span> {{ $secretaryCandidate['full_name'] ?? __('Not selected') }}</p>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Selected Subjects') }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse($selectedCreateSubjects as $subject)
                                    <flux:badge variant="neutral" size="xs" class="uppercase tracking-tighter">{{ $subject->name_en }}</flux:badge>
                                @empty
                                    <span class="text-sm text-slate-500 dark:text-zinc-400">{{ __('No subjects selected yet.') }}</span>
                                @endforelse
                            </div>

                            <p class="mt-5 text-xs font-bold uppercase tracking-[0.28em] text-slate-500 dark:text-zinc-400">{{ __('Optional Members') }}</p>
                            <p class="mt-2 text-sm text-slate-700 dark:text-zinc-300">{{ count($createMembers) }} {{ __('member(s) added') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="border-t border-slate-100 bg-slate-50/70 p-6 dark:border-zinc-800 dark:bg-zinc-800/30">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <flux:button wire:click="closeCreateBoardFlow" variant="ghost">{{ __('Cancel') }}</flux:button>

                <div class="flex flex-wrap gap-3">
                    @if($createBoardStep > 1)
                        <flux:button wire:click="previousCreateBoardStep" variant="ghost" icon="arrow-left">{{ __('Back') }}</flux:button>
                    @endif

                    @if($createBoardStep < count($createBoardSteps))
                        <flux:button wire:click="nextCreateBoardStep" variant="primary" icon="arrow-right">{{ __('Next Step') }}</flux:button>
                    @else
                        <flux:button wire:click="saveBoard" variant="primary" icon="check">{{ filled($editingBoardId) ? ($isAppealBoard ? __('Update Appeal Board') : __('Update Transfer Board')) : ($isAppealBoard ? __('Create Appeal Board') : __('Create Transfer Board')) }}</flux:button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
