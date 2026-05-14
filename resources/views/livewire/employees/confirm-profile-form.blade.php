<div>
    <form wire:submit.prevent="submit">
        @csrf
        <div class="space-y-6">
            {{-- Header Section --}}
            <div>
                <flux:heading size="lg">{{ __('Confirm profile data') }}</flux:heading>
                <flux:text class="mt-2">{{ __('To confirm these profile data, please verify your identity by entering your current password.') }}</flux:text>
            </div>

            <flux:separator variant="subtle" />

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
            <div class="flex items-center">
                <flux:spacer />
                
                <div class="flex gap-3">
                    {{-- Optional Cancel Button --}}
                    <flux:button variant="ghost" x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Confirm & Update') }}</span>
                        <span wire:loading>{{ __('Processing...') }}</span>
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>