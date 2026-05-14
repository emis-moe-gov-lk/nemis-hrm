<div>
    <section>
        {{-- Header matching your unified design --}}
        <div class="flex items-center justify-between mb-5 px-1">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white">Temporary Location</h2>
                <p class="text-sm text-gray-500">Current residential address</p>
            </div>
            @if($canEdit)
                <flux:modal.trigger name="edit-temporary-location-info">
                    <flux:button variant="ghost" icon="pencil-square" class="rounded-full">
                        Edit Details
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        <div class="space-y-4">
            {{-- Map Logic Fallback for Temporary Address --}}
            @php
                // Check if at least the first line of the address exists
                $hasTemporaryAddress = !empty($employee->t_address_line1);

                if ($hasTemporaryAddress) {
                    $tAddressString = implode(', ', array_filter([
                        $employee->t_address_line1,
                        $employee->t_address_line2,
                        $employee->t_address_line3,
                        $employee->t_postal_code
                    ]));
                    $tMapUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($tAddressString);
                } else {
                    $tMapUrl = "javascript:void(0)"; // Prevents page jump or refresh
                }
            @endphp

            {{-- Secondary Address Card (Linked to Map) --}}
            <a href="{{ $tMapUrl }}" target="_blank" 
               class="block bg-gradient-to-br from-white to-teal-50/30 dark:from-gray-800 dark:to-teal-900/10 p-5 rounded-2xl border border-teal-100 dark:border-gray-700 shadow-sm transition-all hover:shadow-md hover:border-teal-300 group">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-teal-100 dark:bg-teal-900/40 rounded-xl group-hover:scale-105 transition-transform">
                        <flux:icon.home-modern class="size-6 text-teal-600 dark:text-teal-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Residential Address</p>
                            <flux:icon.arrow-top-right-on-square class="size-3 text-teal-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                        </div>
                        
                        @if($employee->t_address_line1)
                            <div class="mt-1 text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100 leading-relaxed">
                                {{ $employee->t_address_line1 }}<br>
                                @if ($employee->t_address_line2) {{ $employee->t_address_line2 }}<br> @endif
                                @if ($employee->t_address_line3) {{ $employee->t_address_line3 }}<br> @endif
                                <span class="text-teal-600 dark:text-teal-400 font-bold uppercase text-xs">{{ $employee->t_postal_code }}</span>
                            </div>
                        @else
                            <p class="mt-1 text-sm italic text-gray-400">No temporary address provided.</p>
                        @endif
                    </div>
                </div>
            </a>

            {{-- Helpful Note --}}
            <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-800">
                <p class="text-[10px] text-gray-500 text-center uppercase tracking-tighter">
                    Only provide details if different from your <span class="font-bold">Permanent Location</span>.
                </p>
            </div>
        </div>
    </section>

    {{-- Edit Modal --}}
    @if($canEdit)
        <flux:modal wire:model="showModalTemporaryLocationInfo" name="edit-temporary-location-info" class="md:w-130">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" badge="Temporary">Residential Update</flux:heading>
                    <flux:text class="mt-1 text-sm text-gray-500">Update your current living address if you are working away from home.</flux:text>
                </div>

                <form wire:submit.prevent="editTemporaryLocationInfo" class="space-y-4">
                    <flux:input label="Address Line 1" wire:model.live="tAddressLine1" placeholder="Building / Street" />
                    <flux:input label="Address Line 2" wire:model.live="tAddressLine2" placeholder="Locality" />

                    <div class="flex gap-4">
                        <div class="flex-[2]">
                            <flux:input label="Address Line 3" wire:model.live="tAddressLine3" placeholder="City" />
                        </div>
                        <div class="flex-1">
                            <flux:input label="Postal Code" wire:model.live="tPostalCode" placeholder="Zip" />
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost" class="flex-1">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-1 shadow-lg shadow-teal-500/20">Save changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>