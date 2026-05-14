<div class="space-y-6">
    {{-- Section Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-amber-100 dark:bg-amber-900/30 rounded-xl">
                <flux:icon.banknotes class="size-5 text-amber-600 dark:text-amber-400" />
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">W&OP & Payment Details</h2>
                <p class="text-xs sm:text-sm text-gray-500">Pension contributions & payroll identification</p>
            </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-2 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100 dark:border-gray-800">
            @if ($canEdit)
                <flux:modal.trigger name="edit-profile-pension-payment">
                    <flux:button variant="ghost" icon="pencil-square" size="sm" class="rounded-full">Edit Details</flux:button>
                </flux:modal.trigger>
            @endif
        </div>
    </div>

    {{-- Data Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        {{-- W&OP Number Card --}}
        <div class="group relative p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl transition-all hover:shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-lg group-hover:bg-amber-50 dark:group-hover:bg-amber-900/20 transition-colors">
                    <flux:icon.document-text class="size-5 text-gray-400 group-hover:text-amber-500" />
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">W&OP Number</p>
                    <p class="text-base font-mono font-bold text-gray-900 dark:text-white">
                        {{ $employee->appointment->w_op_no ?? 'NOT ASSIGNED' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Pay Sheet Number Card --}}
        <div class="group relative p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl transition-all hover:shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-lg group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20 transition-colors">
                    <flux:icon.credit-card class="size-5 text-gray-400 group-hover:text-blue-500" />
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pay Sheet Number</p>
                    <p class="text-base font-mono font-bold text-gray-900 dark:text-white">
                        {{ $employee->appointment->pay_sheet_no ?? 'NOT ASSIGNED' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    @if ($canEdit)
    <flux:modal name="edit-profile-pension-payment" class="md:w-110">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update Payment Details</flux:heading>
                <flux:text class="mt-2">Update your Widows' & Orphans' Pension and payroll identification numbers.</flux:text>
            </div>

            <form wire:submit.prevent="updatePensionPayment" class="space-y-4">
                <flux:field>
                    <flux:input 
                        wire:model.live="wopNo" 
                        label="W&OP No" 
                        placeholder="E.g. 1234567-X" 
                        icon="document-text"
                    />
                </flux:field>

                <flux:field>
                    <flux:input 
                        wire:model.live="paySheetNo" 
                        label="Pay Sheet No" 
                        placeholder="E.g. PS-8821" 
                        icon="credit-card"
                    />
                </flux:field>

                <div class="flex pt-2">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Save Changes</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif
</div>