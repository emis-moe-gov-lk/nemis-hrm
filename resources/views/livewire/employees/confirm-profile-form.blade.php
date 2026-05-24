<div>
    <form wire:submit.prevent="submit">
        @csrf
        <div class="space-y-6">
            <div class="pb-4 border-b border-slate-200 dark:border-zinc-700">
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Confirm Profile Data</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Verify your identity to save these changes</p>
            </div>

            {{-- Flash Messages --}}
            <div class="mb-4">
                @if (session('error'))
                    <x-alert type="error" dismissible>
                        {{ session('error') }}
                    </x-alert>
                @endif

                @if (session('warning'))
                    <x-alert type="warning" dismissible>
                        {{ session('warning') }}
                    </x-alert>
                @endif

                @if (session('info'))
                    <x-alert type="info" dismissible>
                        {{ session('info') }}
                    </x-alert>
                @endif
            </div>

            {{-- Input Section --}}
            <div class="space-y-4">
                <flux:input 
                    label="{{ __('Password') }}" 
                    wire:model.live="password" 
                    placeholder="{{ __('Enter your current password') }}" 
                    type="password" 
                    icon="key"
                    required
                />
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                <flux:button variant="ghost" x-on:click="$dispatch('close')" class="w-full sm:w-auto font-bold rounded-xl">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" class="w-full sm:w-auto font-black rounded-xl">
                    <span wire:loading.remove>{{ __('Confirm & Update') }}</span>
                    <span wire:loading>{{ __('Processing...') }}</span>
                </flux:button>
            </div>
        </div>
    </form>
</div>