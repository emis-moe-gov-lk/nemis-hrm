<div>
    <section>
        {{-- Header matching Personal/Health/Contact style --}}
        <div class="flex items-center justify-between mb-5 px-1">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white">Location Details</h2>
                <p class="text-sm text-gray-500">Residency and mapping data</p>
            </div>
            @if($canEdit)
                <flux:modal.trigger name="edit-location-info">
                    <flux:button variant="ghost" icon="pencil-square" class="rounded-full">
                        Edit Details
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        <div class="space-y-4">
            {{-- Map Logic: Coordinate vs Address Fallback --}}
            @php
                $hasCoords = $employee->latitude && $employee->longitude;
                if ($hasCoords) {
                    $mapUrl = "https://www.google.com/maps/search/?api=1&query={$employee->latitude},{$employee->longitude}";
                } else {
                    $addressString = implode(', ', array_filter([
                        $employee->address_line1,
                        $employee->address_line2,
                        $employee->address_line3,
                        $employee->district->district_name ?? '',
                    ]));
                    $mapUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($addressString);
                }
            @endphp

            {{-- Primary Address Card (Linked to Map) --}}
            <a href="{{ $mapUrl }}" target="_blank" 
               class="block bg-gradient-to-br from-white to-orange-50/30 dark:from-gray-800 dark:to-orange-900/10 p-5 rounded-2xl border border-orange-100 dark:border-gray-700 shadow-sm transition-all hover:shadow-md hover:border-orange-300 group">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/40 rounded-xl group-hover:scale-105 transition-transform">
                        <flux:icon.map-pin class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Permanent Address</p>
                            <flux:icon.arrow-top-right-on-square class="size-3 text-orange-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                        </div>
                        <div class="mt-1 text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100 leading-relaxed">
                            {{ $employee->address_line1 }}<br>
                            @if ($employee->address_line2) {{ $employee->address_line2 }}<br> @endif
                            @if ($employee->address_line3) {{ $employee->address_line3 }}<br> @endif
                            <span class="text-orange-600 dark:text-orange-400 font-bold uppercase text-xs">{{ $employee->postal_code }}</span>
                        </div>
                    </div>
                </div>
            </a>

            {{-- Secondary Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">District</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $employee->district->district_name ?? 'N/A' }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">GN Division</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                         <span class="text-gray-400 font-medium">({{ $employee->gnDivision->gn_division_code ?? '---' }})</span> 
                         {{ $employee->gnDivision->gn_division_name ?? 'N/A' }}
                    </p>
                </div>

                {{-- Visual Coordinate Status --}}
                <div class="sm:col-span-2 flex items-center justify-center gap-4 py-2.5 px-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                    @if($hasCoords)
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1">
                                <span class="text-[9px] font-bold text-gray-400 uppercase">Lat:</span>
                                <span class="text-[11px] font-mono font-bold text-gray-700 dark:text-gray-300">{{ $employee->latitude }}</span>
                            </div>
                            <div class="w-px h-3 bg-gray-300 dark:bg-gray-600"></div>
                            <div class="flex items-center gap-1">
                                <span class="text-[9px] font-bold text-gray-400 uppercase">Long:</span>
                                <span class="text-[11px] font-mono font-bold text-gray-700 dark:text-gray-300">{{ $employee->longitude }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-[10px] italic text-gray-400 flex items-center gap-2">
                            <flux:icon.information-circle class="size-3" />
                            GPS not set. Mapping via address fallback.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Edit Modal --}}
    @if($canEdit)
        <flux:modal wire:model="showModalLocationInfo" name="edit-location-info" class="md:w-160">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" badge="Location">Update Residency</flux:heading>
                    <flux:text class="mt-1">Provide accurate address and GPS details for logistics.</flux:text>
                </div>

                <form wire:submit.prevent="editLocationInfo" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:select label="District" wire:model.live="district" icon="map">
                            <option value="">Select District</option>
                            @foreach ($districtOption as $data)
                                <option value="{{ $data->district_id }}">{{ $data->district_name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select label="GN Division" wire:model.live="gnDivision">
                            <option value="">Select GN Division</option>
                            @foreach ($gnDivisionOption as $data)
                                <option value="{{ $data->gn_division_id }}">
                                    ({{ $data->gn_division_code }}) {{ $data->gn_division_name }}
                                </option>
                            @endforeach
                        </flux:select>
                    </div>

                    <flux:input label="Address Line 1" wire:model.live="addressLine1" placeholder="House No / Street" />
                    <flux:input label="Address Line 2" wire:model.live="addressLine2" placeholder="Locality / Village" />

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <flux:input label="Address Line 3" wire:model.live="addressLine3" placeholder="City" />
                        </div>
                        <div class="w-1/3">
                            <flux:input label="Postal Code" wire:model.live="postalCode" placeholder="Zip" />
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <p class="text-xs font-bold text-gray-400 uppercase mb-3 tracking-widest">GPS Coordinates (Optional)</p>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input label="Latitude" wire:model.live="latitude" placeholder="e.g. 6.9271" />
                            <flux:input label="Longitude" wire:model.live="longitude" placeholder="e.g. 79.8612" />
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost" class="flex-1">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-1 shadow-lg shadow-blue-500/20">Save Changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>