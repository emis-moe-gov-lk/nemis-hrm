<div>
    <section>
        {{-- Header matching the Personal/Health style --}}
        <div class="flex items-center justify-between mb-5 px-1">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white">Contact Details</h2>
                <p class="text-sm text-gray-500">Communication channels</p>
            </div>
            @if($canEdit)
                <flux:modal.trigger name="edit-contact-info">
                    <flux:button variant="ghost" icon="pencil-square" class="rounded-full">
                        Edit Details
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        <div class="space-y-4">
            {{-- Primary Contact Card: Email --}}
            <a href="mailto:{{ $employee->email }}" 
            class="block bg-gradient-to-br from-white to-blue-50/30 dark:from-gray-800 dark:to-blue-900/10 p-5 rounded-2xl border border-blue-100 dark:border-gray-700 shadow-sm transition-all hover:shadow-md hover:border-blue-300 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/40 rounded-xl group-hover:scale-110 transition-transform">
                        <flux:icon.envelope class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Primary Email</p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100 truncate flex items-center gap-2">
                            {{ $employee->email }}
                            <flux:icon.arrow-top-right-on-square class="size-3 opacity-0 group-hover:opacity-100 text-blue-500 transition-opacity" />
                        </p>
                    </div>
                </div>
            </a>

            {{-- Secondary Contact Card: Phone --}}
            <a href="tel:{{ $employee->phone }}" 
            class="block bg-gradient-to-br from-white to-green-50/30 dark:from-gray-800 dark:to-green-900/10 p-5 rounded-2xl border border-green-100 dark:border-gray-700 shadow-sm transition-all hover:shadow-md hover:border-green-300 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-100 dark:bg-green-900/40 rounded-xl group-hover:scale-110 transition-transform">
                        <flux:icon.phone class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mobile Number</p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100 leading-tight flex items-center gap-2">
                            {{ $employee->phone }}
                            <flux:icon.phone-arrow-up-right class="size-3 opacity-0 group-hover:opacity-100 text-green-500 transition-opacity" />
                        </p>
                    </div>
                </div>
            </a>
        </div>
    </section>

    {{-- Mobile-First Modal --}}
    @if($canEdit)
        <flux:modal wire:model="showModalContactInfo" name="edit-contact-info" class="md:w-125">
            <div class="space-y-6">
                <flux:heading size="lg" badge="Contact">Update Channels</flux:heading>

                <form wire:submit.prevent="editContactInfo" class="space-y-5">
                    @csrf
                    
                    <flux:field>
                        <flux:input 
                            label="Mobile Phone" 
                            icon="phone" 
                            wire:model.live="contact" 
                            placeholder="e.g. 0771234567" 
                        />
                    </flux:field>

                    <flux:field>
                        <flux:input 
                            label="Email Address" 
                            type="email" 
                            icon="envelope" 
                            wire:model.live="email" 
                            placeholder="you@company.com" 
                        />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost" class="flex-1">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-1">Save Changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>