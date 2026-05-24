<div class="space-y-8">
    <section>
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Location Details</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Residency & Mapping Records</p>
            </div>
            @if($canEdit)
            <flux:modal.trigger name="edit-location-info">
                <flux:button variant="ghost" size="sm" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-orange-400 hover:text-orange-600 transition-all w-fit">
                    Edit Details
                </flux:button>
            </flux:modal.trigger>
            @endif
        </div>

        {{-- Map Logic --}}
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

        {{-- Data Table --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

            {{-- Address --}}
            <div class="flex flex-col sm:flex-row sm:items-start border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest sm:pt-0.5">Home Address</span>
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-zinc-100 leading-relaxed">
                        {{ $employee->address_line1 }}
                        @if ($employee->address_line2), {{ $employee->address_line2 }}@endif
                        @if ($employee->address_line3), {{ $employee->address_line3 }}@endif
                    </p>
                    <a href="{{ $mapUrl }}" target="_blank" class="mt-1.5 inline-flex items-center gap-1 text-[10px] font-black text-orange-500 hover:text-orange-700 uppercase tracking-widest transition-colors">
                        <flux:icon.map-pin variant="micro" class="size-3" />
                        View on Map
                    </a>
                </div>
            </div>

            {{-- Postal Code --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Postal Code</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 font-mono">{{ $employee->postal_code ?? '—' }}</span>
            </div>

            {{-- District --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">District</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->district->district_name ?? '—' }}</span>
            </div>

            {{-- GN Division --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">GN Division</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">
                    <span class="text-slate-500 font-mono mr-1">{{ $employee->gnDivision->gn_division_code ?? '' }}</span>
                    {{ $employee->gnDivision->gn_division_name ?? '—' }}
                </span>
            </div>

            {{-- GPS Coordinates --}}
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">GPS Coordinates</span>
                @if($hasCoords)
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-zinc-100">
                    {{ $employee->latitude }}, {{ $employee->longitude }}
                </span>
                @else
                <span class="text-sm text-slate-500 dark:text-zinc-400 italic">Not set — address fallback active</span>
                @endif
            </div>

        </div>
    </section>

    {{-- Edit Modal --}}
    @if($canEdit)
    <flux:modal wire:model="showModalLocationInfo" name="edit-location-info" class="md:w-160">
        <div class="space-y-8">
            <div>
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Update Residency</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Provide accurate address and GPS details</p>
            </div>

            <form wire:submit.prevent="editLocationInfo" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:select label="District" wire:model.live="district" icon="map" class="font-bold">
                        <option value="">Select District</option>
                        @foreach ($districtOption as $data)
                        <option value="{{ $data->district_id }}">{{ $data->district_name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="DS Office" wire:model.live="divisionalDecretaryOffice" class="font-bold">
                        <option value="">Select DS Office</option>
                        @foreach ($divisionalSecretaryofficeOption as $data)
                        <option value="{{ $data->dso_id }}">{{ $data->dso_name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:select label="GN Division" wire:model.live="gnDivision" class="font-bold">
                    <option value="">Select GN Division</option>
                    @foreach ($gnDivisionOption as $data)
                    <option value="{{ $data->gn_division_id }}">
                        ({{ $data->gn_division_code }}) {{ $data->gn_division_name }}
                    </option>
                    @endforeach
                </flux:select>

                <div class="space-y-4">
                    <flux:input label="Address Line 1" wire:model.live="addressLine1" placeholder="House No / Street" class="font-bold" />
                    <flux:input label="Address Line 2" wire:model.live="addressLine2" placeholder="Locality / Village" class="font-bold" />

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <flux:input label="Address Line 3" wire:model.live="addressLine3" placeholder="City" class="font-bold" />
                        </div>
                        <flux:input label="Postal Code" wire:model.live="postalCode" placeholder="Zip" class="font-bold" />
                    </div>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-zinc-800/50 rounded-2xl border border-slate-200 dark:border-zinc-700">
                    <p class="text-[10px] font-black text-slate-500 uppercase mb-4 tracking-widest">GPS Coordinates (Optional)</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input label="Latitude" wire:model.live="latitude" placeholder="e.g. 6.9271" class="font-bold" />
                        <flux:input label="Longitude" wire:model.live="longitude" placeholder="e.g. 79.8612" class="font-bold" />
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-1 font-black rounded-xl h-12 bg-indigo-600 dark:bg-white text-white dark:text-slate-900 hover:scale-[1.02] active:scale-95 transition-all">
                        Save Location Records
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif
</div>