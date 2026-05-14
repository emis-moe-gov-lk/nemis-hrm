<div class="max-w-5xl mx-auto px-4 py-8">
    {{-- Header Banner --}}
    <div class="relative overflow-hidden mb-8 rounded-2xl 
            bg-white dark:bg-slate-900 
            p-8 md:p-10 shadow-lg 
            border border-slate-200 dark:border-slate-800">

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">

            <div>
                <flux:heading size="xl" level="1"
                    class="font-bold tracking-tight mb-2 
                       text-slate-900 dark:text-white">
                    @if($applicationId)
                    {{ __('Edit Transfer Application') }} <span class="text-indigo-600 dark:text-indigo-500 font-extrabold ml-1">#{{ $applicationId }}</span>
                    @else
                    {{ __('SLTS Transfer Application') }} <span class="text-slate-400 dark:text-slate-500 font-medium ml-1">({{ __('New Request') }})</span>
                    @endif
                </flux:heading>

                <flux:subheading
                    class="max-w-2xl text-base leading-relaxed 
                       text-slate-600 dark:text-slate-400">
                    {{ __('Submit your application for institutional transfer. Please ensure all details are accurate before final submission.') }}
                </flux:subheading>
            </div>

            <div class="shrink-0">
                <flux:badge size="lg"
                    class="bg-indigo-100 text-indigo-700 border-indigo-200
                       dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20
                       backdrop-blur-md">
                    {{ __('Application Window Open') }}
                </flux:badge>
            </div>

        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
    <x-alert type="success" dismissible class="mb-6">
        {{ session('success') }}
    </x-alert>
    @endif

    @if (session('error'))
    <x-alert type="error" dismissible class="mb-6">
        {{ session('error') }}
    </x-alert>
    @endif

    @if($step === 1)
    <form wire:submit.prevent="submitApplication">
        <div class="space-y-8">

            {{-- Section 1: Teacher Profile Synopsis --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 p-8 shadow-sm">
                <flux:heading size="lg" class="mb-6 flex items-center gap-2">
                    <flux:icon name="user-circle" variant="mini" class="text-indigo-500" />
                    {{ __('Applicant Profile Synopsis') }}
                </flux:heading>

                <div class="grid grid-cols-1">
                    <div class="bg-slate-50 dark:bg-zinc-800/50 p-4 rounded-2xl border border-slate-100 dark:border-zinc-800">
                        <p class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-1">{{ __('Name') }}</p>
                        <p class="font-bold text-slate-900 dark:text-zinc-100">{{ $teacherName }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ __('Employee ID: ') . $employeeId }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ __('NIC: ') . $nic }}</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mt-2">
                            <p class="text-sm text-slate-500 mt-1">{{ __('Date of Birth: ') . $dateOfBirth }}</p>
                            <p class="text-sm text-slate-500 mt-1">{{ __('Gender: ') . $gender }}</p>
                        </div>

                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                    <div class="bg-slate-50 dark:bg-zinc-800/50 p-4 rounded-2xl border border-slate-100 dark:border-zinc-800">
                        <p class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-1">{{ __('Current Station') }}</p>
                        <p class="font-bold text-slate-900 dark:text-zinc-100">{{ $currentWorkplaceName }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ $currentWorkplaceAddress }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ __('Appt:') }} {{ $currentWorkplaceJoinDate }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <flux:badge size="xs" color="indigo">{{ $currentServiceStationTotal }} {{ __('here') }}</flux:badge>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-zinc-800/50 p-4 rounded-2xl border border-slate-100 dark:border-zinc-800">
                        <p class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-1">{{ __('Service Details') }}</p>
                        <p class="font-bold text-slate-900 dark:text-zinc-100">{{ $firstWorkplaceName }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ $firstWorkplaceAddress }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ __('1st Appt:') }} {{ $firstAppointmentDate }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <flux:badge size="xs" color="green">{{ $serviceTotal }} {{ __('Total') }}</flux:badge>
                        </div>
                    </div>
                </div>

                <div class="mt-2 pt-2 border-t border-slate-100 dark:border-zinc-800 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-indigo-50/30 dark:bg-indigo-900/10 p-4 rounded-2xl border border-indigo-100/50 dark:border-indigo-900/20">
                        <p class="text-xs font-bold text-indigo-500/70 dark:text-indigo-400 uppercase tracking-widest mb-1">{{ __('Permanent Address') }}</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-zinc-100 italic">{{ $permanentAddress }}</p>
                    </div>

                    <div class="bg-indigo-50/30 dark:bg-indigo-900/10 p-4 rounded-2xl border border-indigo-100/50 dark:border-indigo-900/20">
                        <p class="text-xs font-bold text-indigo-500/70 dark:text-indigo-400 uppercase tracking-widest mb-1">{{ __('Temporary Address') }}</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-zinc-100 italic">{{ $this->resolvedTemporaryAddress }}</p>
                    </div>
                </div>
            </div>

            {{-- Section 1B: Address Confirmation for Application --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 p-8 shadow-sm">
                <flux:heading size="lg" class="mb-2 flex items-center gap-2">
                    <flux:icon name="map-pin" variant="mini" class="text-indigo-500" />
                    {{ __('Address for This Application') }}
                </flux:heading>
                <p class="text-sm text-slate-500 mb-6">
                    {{ __('Confirm or update the address details to be captured with this transfer application. This does not change your profile record.') }}
                </p>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div x-data="{ showMap: false }" class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 dark:border-zinc-800 dark:bg-zinc-800/30">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ __('Permanent Address') }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ __('Used for transfer processing and distance-based review.') }}</p>
                            </div>
                            <flux:button type="button" variant="ghost" size="sm" x-on:click="showMap = !showMap">
                                <span x-show="!showMap">{{ __('Mark on Map') }}</span>
                                <span x-show="showMap" style="display:none;">{{ __('Hide Map') }}</span>
                            </flux:button>
                        </div>

                        <div class="mt-4 space-y-4">
                            <flux:textarea
                                wire:model="permanentAddress"
                                label="{{ __('Address') }}"
                                rows="3"
                                placeholder="{{ __('Enter the permanent address used for this transfer application...') }}"
                                :invalid="$errors->has('permanentAddress')" />
                            <flux:error name="permanentAddress" />

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <flux:input
                                        wire:model.live.debounce.200ms="permanentLatitude"
                                        label="{{ __('Latitude') }}"
                                        type="number"
                                        step="0.000001"
                                        placeholder="{{ __('e.g. 6.927100') }}"
                                        :invalid="$errors->has('permanentLatitude')" />
                                    <flux:error name="permanentLatitude" />
                                </div>

                                <div>
                                    <flux:input
                                        wire:model.live.debounce.200ms="permanentLongitude"
                                        label="{{ __('Longitude') }}"
                                        type="number"
                                        step="0.000001"
                                        placeholder="{{ __('e.g. 79.861200') }}"
                                        :invalid="$errors->has('permanentLongitude')" />
                                    <flux:error name="permanentLongitude" />
                                </div>
                            </div>

                            <div class="rounded-xl border border-dashed border-slate-200 px-3 py-2 text-xs font-medium text-slate-500 dark:border-zinc-700 dark:text-zinc-400">
                                {{ __('Pinned coordinates') }}: <span class="font-mono text-slate-700 dark:text-zinc-200">{{ $this->resolvedPermanentCoordinates }}</span>
                            </div>

                            <div x-show="showMap" x-cloak class="rounded-2xl border border-slate-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                                <x-maps.leaflet-picker
                                    lat="permanentLatitude"
                                    lng="permanentLongitude"
                                    height="h-56"
                                    helpText="Click the map or drag the pin to mark the permanent location." />
                            </div>
                        </div>
                    </div>

                    <div x-data="{ showMap: false }" class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 dark:border-zinc-800 dark:bg-zinc-800/30">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ __('Temporary Address') }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ __('Leave blank if it is the same as the permanent address.') }}</p>
                            </div>
                            <flux:button type="button" variant="ghost" size="sm" x-on:click="showMap = !showMap">
                                <span x-show="!showMap">{{ __('Mark on Map') }}</span>
                                <span x-show="showMap" style="display:none;">{{ __('Hide Map') }}</span>
                            </flux:button>
                        </div>

                        <div class="mt-4 space-y-4">
                            <flux:textarea
                                wire:model="temporaryAddress"
                                label="{{ __('Address') }}"
                                rows="3"
                                placeholder="{{ __('Enter a temporary address only if it differs from the permanent address...') }}"
                                :invalid="$errors->has('temporaryAddress')" />
                            <flux:error name="temporaryAddress" />

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <flux:input
                                        wire:model.live.debounce.200ms="temporaryLatitude"
                                        label="{{ __('Latitude') }}"
                                        type="number"
                                        step="0.000001"
                                        placeholder="{{ __('Optional') }}"
                                        :invalid="$errors->has('temporaryLatitude')" />
                                    <flux:error name="temporaryLatitude" />
                                </div>

                                <div>
                                    <flux:input
                                        wire:model.live.debounce.200ms="temporaryLongitude"
                                        label="{{ __('Longitude') }}"
                                        type="number"
                                        step="0.000001"
                                        placeholder="{{ __('Optional') }}"
                                        :invalid="$errors->has('temporaryLongitude')" />
                                    <flux:error name="temporaryLongitude" />
                                </div>
                            </div>

                            <div class="rounded-xl border border-dashed border-slate-200 px-3 py-2 text-xs font-medium text-slate-500 dark:border-zinc-700 dark:text-zinc-400">
                                {{ __('Pinned coordinates') }}: <span class="font-mono text-slate-700 dark:text-zinc-200">{{ $this->resolvedTemporaryCoordinates }}</span>
                            </div>

                            <div x-show="showMap" x-cloak class="rounded-2xl border border-slate-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                                <x-maps.leaflet-picker
                                    lat="temporaryLatitude"
                                    lng="temporaryLongitude"
                                    height="h-56"
                                    helpText="Click the map or drag the pin to mark the temporary location." />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Transfer Type & Reason --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 p-8 shadow-sm">
                <flux:heading size="lg" class="mb-6 flex items-center gap-2">
                    <flux:icon name="document-text" variant="mini" class="text-indigo-500" />
                    {{ __('Transfer Details') }}
                </flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="max-w-md">
                        <flux:select wire:model.live="policyId" label="{{ __('Transfer Type') }}" placeholder="{{ __('Select transfer type...') }}" :invalid="$errors->has('policyId')">
                            @foreach($transferPolicies as $id => $title)
                            <option value="{{ $id }}">{{ __($title) }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="max-w-md">
                        <flux:select wire:model.live="transferReasonTypeId" label="{{ __('Reason Category') }}" placeholder="{{ __('Select reason category...') }}" :invalid="$errors->has('transferReasonTypeId')">
                            @foreach($transferReasonTypes as $reasonType)
                            <option value="{{ $reasonType['id'] }}">{{ __($reasonType['name']) }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    @if($transferReasonTypeId === 'other')
                    <div>
                        <flux:textarea wire:model="transferReason" label="{{ __('Detailed Reason') }}" rows="4" placeholder="{{ __('Provide a detailed explanation for your transfer request. Minimum 20 characters.') }}" :invalid="$errors->has('transferReason')" />
                    </div>
                    @endif
                </div>
                <div class="mt-6">
                    <p class="text-xs text-slate-500">{{ __('Please attach relevant supporting documents below if applying under Medical or Humanitarian grounds.') }}</p>

                    <div class="bg-orange-50/50 dark:bg-orange-950/20 p-5 rounded-2xl border border-orange-100 dark:border-orange-900/50 mt-2">
                        <flux:checkbox wire:model.live="hasDisciplinaryActions" label="{{ __('I have pending or past disciplinary actions against me.') }}" />

                        @if($hasDisciplinaryActions)
                        <div class="mt-4 pl-7">
                            <flux:textarea wire:model="disciplinaryDetails" label="{{ __('Disciplinary Details') }}" rows="3" placeholder="{{ __('Briefly explain the nature of the disciplinary action and its current status...') }}" />
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Section 3: Transfer catogory --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 p-8 shadow-sm">
                <flux:heading size="lg" class="mb-2 flex items-center gap-2">
                    <flux:icon name="map-pin" variant="mini" class="text-indigo-500" />
                    {{ __('Transfer Category') }}
                </flux:heading>
                <p class="text-sm text-slate-500 mb-6">{{ __('Select the appropriate transfer category and target location.') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <flux:select wire:model.live="transferCategoryId" label="{{ __('Category') }}" placeholder="{{ __('Select Category...') }}">
                        @foreach($transferCatagory as $catagory)
                        <option value="{{ $catagory['id'] }}">{{ $catagory['name'] }}</option>
                        @endforeach
                    </flux:select>

                    @if($this->shouldChooseTargetProvince)
                    <flux:select wire:model.live="selectedProvinceId" label="{{ __('Target Province') }}" placeholder="{{ __('Select Province...') }}">
                        @foreach($provincialEducationOffices as $province)
                        <option value="{{ $province->workplace_id }}">{{ $province->name }}</option>
                        @endforeach
                    </flux:select>
                    @elseif($transferCategoryId)
                    <flux:input label="{{ __('Target Province') }}" value="{{ $this->resolvedTargetProvinceName }}" disabled />
                    @endif
                </div>
            </div>

            {{-- Section 3: Preferences --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 p-8 shadow-sm">
                <flux:heading size="lg" class="mb-2 flex items-center gap-2">
                    <flux:icon name="map-pin" variant="mini" class="text-indigo-500" />
                    {{ __('Station Preferences') }}
                </flux:heading>
                <p class="text-sm text-slate-500 mb-6">{{ __('You may select up to :count preferred schools. At least one preference is required.', ['count' => $maxPreferences]) }}</p>

                <div class="space-y-6">
                    @php
                    $ordinals = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'];
                    @endphp

                    @foreach (range(1, $maxPreferences) as $i)
                    @php
                    $ordinal = $ordinals[$i - 1] ?? "#{$i}";
                    $isFirst = $i === 1;
                    @endphp

                    <div wire:key="pref-slot-{{ $i }}" class="grid grid-cols-1 md:grid-cols-5 gap-6 items-start p-4 rounded-2xl border border-slate-100 dark:border-zinc-800 bg-slate-50/30 dark:bg-zinc-900/10 transition-colors hover:border-indigo-100 dark:hover:border-indigo-900/30">
                        {{-- Target Zone --}}
                        <flux:field class="md:col-span-2">
                            <flux:label>{{ __('Target Zone for :ord Preference', ['ord' => $ordinal]) }}</flux:label>
                            <flux:select wire:model.live="selectedZones.{{ $i }}" placeholder="{{ $isFirst ? __('Select Zone...') : __('Select Zone (Optional)...') }}" :invalid="$errors->has('selectedZones.'.$i)">
                                @foreach($zonalEducationOffices as $zone)
                                <option value="{{ $zone->workplace_id }}">{{ $zone->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="selectedZones.{{ $i }}" />
                        </flux:field>

                        {{-- School Preference --}}
                        <flux:field class="md:col-span-2">
                            <flux:label>{{ __(':ord Preference', ['ord' => $ordinal]) }}{{ !$isFirst ? ' ('.__('Optional').')' : '' }}</flux:label>
                            <flux:select
                                wire:model.live="preferences.{{ $i }}"
                                placeholder="{{ ($selectedZones[$i] ?? '') ? __('Select Choice...') : __('Select a Zone first...') }}"
                                :disabled="empty($selectedZones[$i] ?? '')"
                                :invalid="$errors->has('preferences.'.$i)">
                                @foreach($institutionsLists[$i] ?? [] as $institution)
                                <option value="{{ $institution->workplace_id }}">{{ $institution->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="preferences.{{ $i }}" />
                        </flux:field>

                        {{-- teacher permanent address to institution distance in km --}}
                        <flux:field class="md:col-span-1">
                            <flux:label>{{ __('Distance (km)') }}</flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="distanceInKm.{{ $i }}" placeholder="{{ __('0.00') }}" :invalid="$errors->has('distanceInKm.'.$i)" />
                            <p class="mt-1 text-[11px] leading-snug text-slate-500 dark:text-zinc-400">
                                {{ __('Auto-calculated by nearest road route when possible. You can edit it if needed.') }}
                            </p>
                            <flux:error name="distanceInKm.{{ $i }}" />
                        </flux:field>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Section 4: Achievements --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 p-8 shadow-sm">
                <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <flux:heading size="lg" class="mb-2 flex items-center gap-2">
                            <flux:icon name="trophy" variant="mini" class="text-indigo-500" />
                            {{ __('Achievements for Transfer Score') }}
                        </flux:heading>
                        <p class="text-sm text-slate-500">{{ __('Add zonal, provincial, or national achievements to support the board scoring review. This section is optional.') }}</p>
                    </div>
                    <flux:button type="button" wire:click="addAchievement" variant="ghost" icon="plus" size="sm">{{ __('Add Achievement') }}</flux:button>
                </div>

                <div class="space-y-4">
                    @foreach($achievements as $index => $achievement)
                        <div wire:key="achievement-{{ $index }}" class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-800/30">
                            <div class="mb-4 flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-slate-500 dark:text-zinc-400">{{ __('Achievement') }} {{ $index + 1 }}</p>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-zinc-400">{{ __('Uncheck to keep the record but exclude it from transfer scoring.') }}</p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-indigo-100 bg-white/90 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50/60 dark:border-indigo-900/40 dark:bg-zinc-900/80 dark:text-zinc-200 dark:hover:border-indigo-800 dark:hover:bg-indigo-950/20">
                                        <flux:checkbox wire:model.live="achievements.{{ $index }}.is_included" />
                                        <span>{{ __('Enable') }}</span>
                                    </label>

                                    @if(count($achievements) > 1)
                                        <flux:button type="button" wire:click="removeAchievement({{ $index }})" variant="ghost" icon="trash" size="xs">{{ __('Remove') }}</flux:button>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <flux:field>
                                    <flux:select wire:model.live="achievements.{{ $index }}.achievement_type" label="{{ __('Type') }}" placeholder="{{ __('Select type...') }}">
                                        @foreach($achievementTypes as $typeKey => $typeLabel)
                                            <option value="{{ $typeKey }}">{{ __($typeLabel) }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="achievements.{{ $index }}.achievement_type" />
                                </flux:field>

                                <flux:field>
                                    <flux:select wire:model="achievements.{{ $index }}.achievement_level" label="{{ __('Level') }}" placeholder="{{ __('Select level...') }}">
                                        @foreach($achievementLevels as $levelKey => $levelLabel)
                                            <option value="{{ $levelKey }}">{{ __($levelLabel) }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="achievements.{{ $index }}.achievement_level" />
                                </flux:field>

                                <flux:field class="xl:col-span-2">
                                    <flux:input wire:model="achievements.{{ $index }}.title" label="{{ __('Title / Award') }}" placeholder="{{ __('Example: Provincial ICT Innovation Award') }}" />
                                    <flux:error name="achievements.{{ $index }}.title" />
                                </flux:field>

                                <flux:field class="md:col-span-2">
                                    <flux:input wire:model="achievements.{{ $index }}.event_name" label="{{ __('Event / Competition') }}" placeholder="{{ __('Optional event or competition name') }}" />
                                    <flux:error name="achievements.{{ $index }}.event_name" />
                                </flux:field>

                                <flux:field>
                                    <flux:input type="date" wire:model="achievements.{{ $index }}.achievement_date" label="{{ __('Date') }}" />
                                    <flux:error name="achievements.{{ $index }}.achievement_date" />
                                </flux:field>

                                <flux:field class="md:col-span-2 xl:col-span-3">
                                    <flux:input wire:model="achievements.{{ $index }}.details" label="{{ __('Details') }}" placeholder="{{ __('Briefly mention what, where, and when.') }}" />
                                    <flux:error name="achievements.{{ $index }}.details" />
                                </flux:field>

                                @if(($achievement['achievement_type'] ?? '') === 'student')
                                    <flux:field class="md:col-span-2 xl:col-span-4">
                                        <flux:textarea wire:model="achievements.{{ $index }}.contribution_details" rows="2" label="{{ __('Teacher Contribution') }}" placeholder="{{ __('Required for student achievements. Explain your major contribution.') }}" />
                                        <flux:error name="achievements.{{ $index }}.contribution_details" />
                                    </flux:field>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Section 5: Declarations --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 p-8 shadow-sm">
                <flux:heading size="lg" class="mb-6 flex items-center gap-2">
                    <flux:icon name="check-badge" variant="mini" class="text-indigo-500" />
                    {{ __('Declarations') }}
                </flux:heading>

                <div class="space-y-4">
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-3 bg-slate-50 dark:bg-zinc-800/50 p-4 rounded-xl {{ $errors->has('declarationTrue') ? 'border border-red-200 dark:border-red-900/50 bg-red-50/30' : '' }}">
                            <div class="pt-0.5">
                                <flux:checkbox wire:model="declarationTrue" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-zinc-300">{{ __('I declare that the information provided above is true and accurate to the best of my knowledge.') }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ __('I understand that providing false information may result in the rejection of this application and potential disciplinary action.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-4 pb-12">
                <flux:button type="button" wire:click="saveDraft" variant="outline" class="px-6 border-slate-300">{{ __('Save Draft') }}</flux:button>
                <flux:button type="submit" variant="primary" class="px-8 bg-indigo-600 hover:bg-indigo-700 w-full sm:w-auto">
                    {{ __('Preview Application') }}
                </flux:button>
            </div>
        </div>
    </form>
    @else
    {{-- Step 2: Application Summary & Confirmation --}}
    <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200 dark:border-zinc-800 p-8 shadow-sm">
            <div class="flex items-center justify-between mb-8 border-b border-slate-100 dark:border-zinc-800 pb-6">
                <div>
                    <flux:heading size="lg" class="flex items-center gap-2">
                        <flux:icon name="clipboard-document-check" variant="mini" class="text-indigo-500" />
                        {{ __('Review Your Application') }}
                    </flux:heading>
                    <p class="text-sm text-slate-500 mt-1">{{ __('Please verify all details before final submission.') }}</p>
                </div>
                <flux:button wire:click="goBack" variant="ghost" icon="pencil-square" size="sm">{{ __('Edit Application') }}</flux:button>
            </div>

            {{-- Detailed Summary Grid --}}
            <div class="space-y-10">
                {{-- Profile Info --}}
                <section>
                    <h3 class="text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-1 h-4 bg-indigo-500 rounded-full"></span>
                        {{ __('Applicant Information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Full Name') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $teacherName }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('EMP ID / NIC') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $employeeId }} | {{ $nic }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Current Station') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $currentWorkplaceName }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Service at Current') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $currentServiceStationTotal }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Total Service') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $serviceTotal }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Permanent Address') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word italic text-sm">{{ $permanentAddress }}</p>
                            <p class="mt-1 text-[11px] font-mono text-slate-500 dark:text-zinc-400">{{ $this->resolvedPermanentCoordinates }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Temporary Address') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word italic text-sm">{{ $this->resolvedTemporaryAddress }}</p>
                            <p class="mt-1 text-[11px] font-mono text-slate-500 dark:text-zinc-400">{{ $this->resolvedTemporaryCoordinates }}</p>
                        </div>
                    </div>
                </section>

                {{-- Transfer Logic --}}
                <section>
                    <h3 class="text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-1 h-4 bg-indigo-500 rounded-full"></span>
                        {{ __('Transfer Request Details') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Transfer Type') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $transferPolicies[$policyId] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Reason Category') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ collect($transferReasonTypes)->firstWhere('id', $transferReasonTypeId)['name'] ?? 'N/A' }}</p>
                        </div>
                        @if($transferReasonTypeId === 'other')
                        <div class="md:col-span-2">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Detailed Reason') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $transferReason }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Transfer Category') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $this->selectedTransferCategoryName ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Target Province') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $this->resolvedTargetProvinceName }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ __('Target Zone') }}</p>
                            <p class="font-medium text-slate-900 dark:text-zinc-100 wrap-break-word">{{ $zonalEducationOffices->where('workplace_id', $this->selectedZoneId)->first()->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </section>

                {{-- Preferences --}}
                <section>
                    <h3 class="text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-1 h-4 bg-indigo-500 rounded-full"></span>
                        {{ __('Station Preferences') }}
                    </h3>
                    <div class="space-y-3">
                        @foreach(range(1, $maxPreferences) as $i)
                        @if(!empty($preferences[$i] ?? ''))
                        <div class="flex items-center gap-4 p-3 bg-slate-50 dark:bg-zinc-800/40 rounded-xl border border-slate-100 dark:border-zinc-800">
                            <span class="shrink-0 w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">{{ $i }}</span>
                            <span class="font-medium text-slate-800 dark:text-zinc-200 wrap-break-word">{{ ($institutionsLists[$i] ?? collect())->where('workplace_id', $preferences[$i])->first()->name ?? 'N/A' }}</span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </section>

                {{-- Achievements --}}
                @php
                    $filledAchievements = collect($achievements)->filter(function ($achievement) {
                        return filled($achievement['title'] ?? '') || filled($achievement['achievement_level'] ?? '') || filled($achievement['details'] ?? '');
                    });
                @endphp
                @if($filledAchievements->isNotEmpty())
                <section>
                    <h3 class="text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-1 h-4 bg-indigo-500 rounded-full"></span>
                        {{ __('Achievements for Transfer Score') }}
                    </h3>
                    <div class="space-y-3">
                        @foreach($filledAchievements as $achievement)
                            <div class="p-3 bg-slate-50 dark:bg-zinc-800/40 rounded-xl border border-slate-100 dark:border-zinc-800">
                                <div class="flex flex-wrap items-center gap-2">
                                    <flux:badge color="blue" size="xs">{{ $achievementLevels[$achievement['achievement_level'] ?? ''] ?? __('Level N/A') }}</flux:badge>
                                    <flux:badge variant="neutral" size="xs">{{ $achievementTypes[$achievement['achievement_type'] ?? ''] ?? __('Type N/A') }}</flux:badge>
                                </div>
                                <p class="mt-2 font-medium text-slate-900 dark:text-zinc-100">{{ $achievement['title'] ?? __('Untitled achievement') }}</p>
                                @if(filled($achievement['details'] ?? null))
                                    <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $achievement['details'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- Disciplinary --}}
                @if($hasDisciplinaryActions)
                <section class="bg-red-50/50 dark:bg-red-950/10 p-6 rounded-2xl border border-red-100 dark:border-red-900/30">
                    <h3 class="text-red-700 dark:text-red-400 font-bold text-xs uppercase tracking-widest mb-3 flex items-center gap-2">
                        <flux:icon name="no-symbol" variant="mini" />
                        {{ __('Disciplinary Actions Declared') }}
                    </h3>
                    <p class="text-sm text-red-600 dark:text-red-300 leading-relaxed">{{ $disciplinaryDetails }}</p>
                </section>
                @endif
            </div>

            {{-- Final Confirmation Banner --}}
            <div class="mt-12 p-6 bg-indigo-600 rounded-2xl text-white shadow-xl shadow-indigo-200 dark:shadow-none">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4 text-center md:text-left">
                        <div class="p-3 bg-white/20 rounded-full">
                            <flux:icon name="shield-check" class="text-white h-6 w-6" />
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">{{ __('Ready to Finalize?') }}</h4>
                            <p class="text-indigo-100 text-sm">{{ __('By clicking confirm, you agree that all information provided is accurate.') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3 w-full md:w-auto">
                        <flux:button wire:click="goBack" variant="ghost" class="text-white hover:bg-white/10 w-full md:w-auto px-6">{{ __('Back to Edit') }}</flux:button>
                        <flux:button wire:click="confirmSubmission" variant="primary" class="bg-white text-indigo-600 hover:bg-slate-100 w-full md:w-auto px-8 font-bold">
                            {{ __('Confirm & Submit') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
