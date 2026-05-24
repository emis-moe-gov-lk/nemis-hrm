<div class="space-y-8">
    <section>
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Contact Details</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Communication Channels</p>
            </div>
            @if($canEdit)
                <flux:modal.trigger name="edit-contact-info">
                    <flux:button variant="ghost" size="sm" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-emerald-400 hover:text-emerald-600 transition-all">
                        Edit Details
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        {{-- Data Table --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

            {{-- Email --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Email</span>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->email }}</span>
                    <a href="mailto:{{ $employee->email }}" class="text-indigo-500 hover:text-indigo-700 transition-colors">
                        <flux:icon.pencil-square variant="micro" class="size-3.5" />
                    </a>
                </div>
            </div>

            {{-- Phone --}}
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Phone</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->phone }}</span>
            </div>

        </div>
    </section>

    {{-- Edit Modal --}}
    @if($canEdit)
        <flux:modal wire:model="showModalContactInfo" name="edit-contact-info" class="md:w-150">
            <div class="space-y-8">
                <div>
                    <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Update Contact</h3>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Primary communication channels</p>
                </div>

                <form wire:submit.prevent="editContactInfo" class="space-y-6">
                    @csrf
                    
                    <flux:input 
                        label="Mobile Phone" 
                        icon="phone" 
                        wire:model.live="contact" 
                        placeholder="e.g. 0771234567" 
                        class="font-bold"
                    />

                    <flux:input 
                        label="Email Address" 
                        type="email" 
                        icon="envelope" 
                        wire:model.live="email" 
                        placeholder="you@company.com" 
                        class="font-bold"
                    />

                    <div class="flex gap-4 pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-1 font-black rounded-xl h-12 bg-indigo-600 dark:bg-white text-white dark:text-slate-900 hover:scale-[1.02] active:scale-95 transition-all">
                            Save Contact Changes
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>