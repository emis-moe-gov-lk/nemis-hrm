<div class="space-y-8">
    <section>
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Temporary Location</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Current Residential Records</p>
            </div>
            @if($canEdit)
                <flux:modal.trigger name="edit-temporary-location-info">
                    <flux:button variant="ghost" size="sm" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-teal-400 hover:text-teal-600 transition-all w-fit">
                        Edit Details
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        {{-- Map Logic --}}
        @php
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
                $tMapUrl = null;
            }
        @endphp

        {{-- Data Table --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

            {{-- Address --}}
            <div class="flex flex-col sm:flex-row sm:items-start border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest sm:pt-0.5">Residential Address</span>
                @if($hasTemporaryAddress)
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-zinc-100 leading-relaxed">
                            {{ $employee->t_address_line1 }}
                            @if($employee->t_address_line2), {{ $employee->t_address_line2 }}@endif
                            @if($employee->t_address_line3), {{ $employee->t_address_line3 }}@endif
                        </p>
                        <a href="{{ $tMapUrl }}" target="_blank" class="mt-1.5 inline-flex items-center gap-1 text-[10px] font-black text-teal-500 hover:text-teal-700 uppercase tracking-widest transition-colors">
                            <flux:icon.map-pin variant="micro" class="size-3" />
                            View on Map
                        </a>
                    </div>
                @else
                    <span class="text-sm text-slate-500 dark:text-zinc-400 italic">No temporary address on record</span>
                @endif
            </div>

            {{-- Postal Code --}}
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Postal Code</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->t_postal_code ?? '—' }}</span>
            </div>

        </div>

        {{-- Note --}}
        <div class="flex items-center gap-2 mt-4 px-1">
            <flux:icon.light-bulb variant="micro" class="size-3.5 text-amber-500 shrink-0" />
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-widest">
                Only required if different from your <span class="text-slate-600 dark:text-slate-300">Permanent Location</span> records.
            </p>
        </div>
    </section>

    {{-- Edit Modal --}}
    @if($canEdit)
        <flux:modal wire:model="showModalTemporaryLocationInfo" name="edit-temporary-location-info" class="md:w-150">
            <div class="space-y-8">
                <div>
                    <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Update Residence</h3>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Temporary residential details</p>
                </div>

                <form wire:submit.prevent="editTemporaryLocationInfo" class="space-y-6">
                    <flux:input label="Address Line 1" wire:model.live="tAddressLine1" placeholder="Building / Street" class="font-bold" />
                    <flux:input label="Address Line 2" wire:model.live="tAddressLine2" placeholder="Locality" class="font-bold" />

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <flux:input label="Address Line 3" wire:model.live="tAddressLine3" placeholder="City" class="font-bold" />
                        </div>
                        <flux:input label="Postal Code" wire:model.live="tPostalCode" placeholder="Zip" class="font-bold" />
                    </div>

                    <div class="flex gap-4 pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-1 font-black rounded-xl h-12 bg-indigo-600 dark:bg-white text-white dark:text-slate-900 hover:scale-[1.02] active:scale-95 transition-all">
                            Save Changes
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>