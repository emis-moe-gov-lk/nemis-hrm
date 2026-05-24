<div class="space-y-8">
    <section>
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">W&OP & Payment Details</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Pension contributions & payroll identification</p>
            </div>
            @if ($canEdit)
                <flux:modal.trigger name="edit-profile-pension-payment">
                    <flux:button variant="ghost" size="sm" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-amber-400 hover:text-amber-600 transition-all w-fit">
                        Edit Details
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        {{-- Data Table --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

            {{-- W&OP Number --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">W&OP Number</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-zinc-100">
                    {{ $employee->appointment->w_op_no ?? '—' }}
                </span>
            </div>

            {{-- Pay Sheet Number --}}
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Pay Sheet Number</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-zinc-100">
                    {{ $employee->appointment->pay_sheet_no ?? '—' }}
                </span>
            </div>

        </div>
    </section>

    {{-- Edit Modal --}}
    @if ($canEdit)
    <flux:modal name="edit-profile-pension-payment" class="md:w-110">
        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Update Payment Details</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Widows' & Orphans' Pension and payroll numbers</p>
            </div>

            <form wire:submit.prevent="updatePensionPayment" class="space-y-4">
                <flux:input wire:model.live="wopNo" label="W&OP No" placeholder="E.g. 1234567-X" icon="document-text" />
                <flux:input wire:model.live="paySheetNo" label="Pay Sheet No" placeholder="E.g. PS-8821" icon="credit-card" />

                <div class="flex gap-4 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-1 font-black rounded-xl h-12 bg-indigo-600 dark:bg-white text-white dark:text-slate-900">Save Changes</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif
</div>