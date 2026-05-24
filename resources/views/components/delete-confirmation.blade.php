@props([
    'name', 
    'title' => 'Remove Record?', 
    'description' => 'This action cannot be undone. Are you sure you want to proceed?',
    'wireAction' => 'delete',
    'confirmText' => 'Confirm Delete',
    'cancelText' => 'Keep Record',
    'model' => null
])

<flux:modal :name="$name" :wire:model="$model" class="md:w-110">
    <div class="space-y-6">
        {{-- Header Section with Icon --}}
        <div class="flex items-center gap-4 text-rose-600">
            <div class="shrink-0 w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center">
                <flux:icon.trash class="size-6" />
            </div>
            <div>
                <h3 class="text-sm font-black tracking-widest uppercase">{{ $title }}</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Permanent Action</p>
            </div>
        </div>

        {{-- Content Body --}}
        <p class="text-sm font-medium text-slate-600 dark:text-zinc-300 leading-relaxed">
            {{ $description }}
        </p>

        {{-- Action Buttons --}}
        <div class="flex gap-3 pt-2">
            <flux:modal.close>
                <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">{{ $cancelText }}</flux:button>
            </flux:modal.close>
            
            <flux:button 
                wire:click="{{ $wireAction }}" 
                variant="danger" 
                class="flex-1 font-black rounded-xl h-12 bg-rose-600 hover:bg-rose-700 text-white border-none shadow-lg shadow-rose-200 dark:shadow-none"
            >
                {{ $confirmText }}
            </flux:button>
        </div>
    </div>
</flux:modal>
