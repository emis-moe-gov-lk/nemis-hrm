<div>
    <section>
        <div class="mb-3">
            <div class="flex items-baseline justify-between py-2">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Basic Information</h2>
                @can('institution.basic_information.update')
                <flux:modal.trigger name="edit-profile">
                    <flux:button>Edit</flux:button>
                </flux:modal.trigger>
                @endcan
            </div>
            <flux:separator variant="subtle" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
            $basicInfo = [
            'Category' => $institution->institutionCategory->institution_category_name ?? 'N/A',
            'Authority' => $institution->authority->authority_name ?? 'N/A',
            'Type' => $institution->institutionType->institution_types_name ?? 'N/A',
            'Language' => $institution->institutionLanguages->name ?? 'N/A',
            'Gender' => $institution->typeByGender->name ?? 'N/A',
            'Facilities' => $institution->facilities->name ?? 'N/A',
            'Grade Span' => $institution->gradeSpan->grade_span_name ?? 'N/A',
            ];
            @endphp
            @foreach ($basicInfo as $label => $value)
            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ $label }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $value }}</p>
            </div>
            @endforeach
        </div>
    </section>



    <flux:modal name="edit-profile" class="md:w-150">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update profile</flux:heading>
                <flux:text class="mt-2">Make changes to your personal details.</flux:text>
            </div>

            <form wire:submit.prevent="updateBasicInformation">
                @csrf
                <div class="space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:field>
                                <flux:input label="Cences Number" id="cences_number" type="text" wire:model.live="cencesNumber" placeholder="Cences Number" />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:input label="Established Year" id="established_year" type="number" wire:model.live="establishedYear" placeholder="Established Year" />
                            </flux:field>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <flux:field>
                            <flux:input label="Institution Name" id="institution_name" type="text" wire:model.live="institutionName" placeholder="Institution Name" />
                        </flux:field>
                    </div>

                    <div class="flex flex-col gap-4">
                        <flux:field>
                            <flux:input label="Other Name (Optional)" id="otherName" type="text" wire:model.live="otherName" placeholder="Other Name (Optional)" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Institution Category --}}
                        <div>
                            <flux:field>
                                <flux:select
                                    label="Institution Category"
                                    id="institutionCategory"
                                    wire:model.live="institutionCategory"
                                    class="w-full">
                                    <option value="">{{ __('Select Institution Category') }}</option>
                                    @foreach ($institutionCategoryOption as $data)
                                    <option value="{{ $data->institution_category_id }}">
                                        {{ $data->institution_category_name }}
                                    </option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>

                        {{-- Authority --}}
                        <div>
                            <flux:field>
                                <flux:select
                                    label="Authority"
                                    id="authority"
                                    wire:model.live="authority"
                                    class="w-full">
                                    <option value="">{{ __('Select Authority') }}</option>
                                    @foreach ($authorityOption as $data)
                                    <option value="{{ $data->authority_id }}">
                                        {{ $data->authority_name }}
                                    </option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>

                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Institution Type --}}
                        <div>
                            <flux:field>
                                <flux:select
                                    label="Institution Type"
                                    id="institutionType"
                                    wire:model.live="institutionType"
                                    class="w-full">
                                    <option value="">{{ __('Select Institution Type') }}</option>
                                    @foreach ($institutionTypeOption as $data)
                                    <option value="{{ $data->institution_types_id }}">
                                        {{ $data->institution_types_name }}
                                    </option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>

                        {{-- Institution Language --}}
                        <div>
                            <flux:field>
                                <flux:select
                                    label="Institution Language"
                                    id="institutionLanguage"
                                    wire:model.live="institutionLanguage"
                                    class="w-full">
                                    <option value="">{{ __('Select Institution Language') }}</option>
                                    @foreach ($institutionLanguageOption as $data)
                                    <option value="{{ $data->language_id }}">
                                        {{ $data->name }}
                                    </option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>

                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Type By Gender --}}
                        <div>
                            <flux:field>
                                <flux:select
                                    label="Type By Gender"
                                    id="typeByGender"
                                    wire:model.live="typeByGender"
                                    class="w-full">
                                    <option value="">{{ __('Select Type By Gender') }}</option>
                                    @foreach ($typeByGenderOption as $data)
                                    <option value="{{ $data->gender_id }}">
                                        {{ $data->name }}
                                    </option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>

                        {{-- Grade Span --}}
                        <div>
                            <flux:field>
                                <flux:select
                                    label="Grade Span"
                                    id="gradeSpan"
                                    wire:model.live="gradeSpan"
                                    class="w-full">
                                    <option value="">{{ __('Select Grade Span') }}</option>
                                    @foreach ($gradeSpanOption as $data)
                                    <option value="{{ $data->grade_span_id }}">
                                        {{ $data->grade_span_name }}
                                    </option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>

                    </div>

                    <div>
                        <flux:field>
                            <flux:select
                                label="Facilities"
                                id="facilities"
                                wire:model.live="facilities"
                                class="w-full">
                                <option value="">{{ __('Select Facilities') }}</option>
                                @foreach ($facilitiesOption as $data)
                                <option value="{{ $data->facilities_id }}">
                                    {{ $data->name }}
                                </option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                </div>

                <div class="flex mt-4">
                    <flux:spacer />

                    <div class="flex gap-2">
                        <flux:button type="button" wire:click="resetForm">Reset</flux:button>
                        <flux:button type="submit" variant="primary">Save changes</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>
</div>