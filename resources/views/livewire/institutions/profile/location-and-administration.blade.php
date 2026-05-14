<div>
    <section>
        <div class="mb-3">
            <div class="flex items-baseline justify-between py-2">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Location & Administration</h2>
                @can('institution.location_administration.update')
                <flux:modal.trigger name="edit-profile-location-info">
                    <flux:button>Edit</flux:button>
                </flux:modal.trigger>
                @endcan
            </div>
            <flux:separator variant="subtle" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php
            $locationInfo = [
            'District' => $institution->district->district_name ?? 'N/A',
            'Zone' => $institution->zonalEducationOffice->short_name ?? 'N/A',
            'Division' => $institution->divisionalEducationOffice->short_name ?? 'N/A',
            'Address' => $institution->address ?? 'N/A',
            'Latitude' => $institution->latitude,
            'Longitude' => $institution->longitude,
            ];
            @endphp
            @foreach ($locationInfo as $label => $value)
            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ $label }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $value ?? 'N/A' }}</p>
            </div>
            @endforeach
        </div>

        <!-- OpenStreetMap Display -->
        <div class="mt-6 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-2 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 ml-1">Map View</h3>
            <x-maps.leaflet-display lat="{{ $institution->latitude }}" lng="{{ $institution->longitude }}" height="h-72" />
        </div>
    </section>

    <flux:modal name="edit-profile-location-info" class="md:w-150">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update profile</flux:heading>
                <flux:text class="mt-2">Make changes to your personal details.</flux:text>
            </div>

            <form wire:submit.prevent="updateLocationAndAdministration">
                @csrf
                <div class="space-y-4">

                    <div class="flex flex-col gap-4">
                        <flux:field>
                            <flux:select
                                label="District"
                                id="district"
                                wire:model.live="district"
                                class="w-full">
                                <option value="">{{ __('Select District') }}</option>
                                @foreach ($districtOption as $data)
                                <option value="{{ $data->district_id }}">
                                    {{ $data->district_name }}
                                </option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Zonal Education Office --}}
                            <div>
                                <flux:field>
                                    <flux:select
                                        label="Zonal Office"
                                        id="zonalEducationOffice"
                                        wire:model.live="zonalEducationOffice"
                                        class="w-full">
                                        <option value="">{{ __('Select Office') }}</option>
                                        @foreach ($zonalEducationOfficeOption as $data)
                                        <option value="{{ $data->workplace_id }}">
                                            {{ $data->short_name }}
                                        </option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>

                            {{-- Divisional Education Office --}}
                            <div>
                                <flux:field>
                                    <flux:select
                                        label="Divisional Office"
                                        id="divisionalEducationOffice"
                                        wire:model.live="divisionalEducationOffice"
                                        class="w-full">
                                        <option value="">{{ __('Select Office') }}</option>
                                        @foreach ($divisionalEducationOfficeOption as $data)
                                        <option value="{{ $data->workplace_id }}">
                                            {{ $data->short_name }}
                                        </option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>

                        </div>
                        <flux:field>
                            <flux:textarea
                                label="Address"
                                placeholder="Enter Address"
                                wire:model.live="address" />
                        </flux:field>


                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:input
                                label="Latitude"
                                placeholder="Enter Latitude"
                                wire:model.live="latitude" />
                        </flux:field>
                        <flux:field>
                            <flux:input
                                label="Longitude"
                                placeholder="Enter Longitude"
                                wire:model.live="longitude" />
                        </flux:field>
                    </div>

                    <!-- Interactive OpenStreetMap component -->
                    <x-maps.leaflet-picker lat="latitude" lng="longitude" />

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