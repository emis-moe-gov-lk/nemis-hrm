<div>
    <form wire:submit.prevent="submit">
        @csrf
        <div class="space-y-6">
            {{-- Header section --}}
            <div>
                <flux:heading size="lg">{{ __('Verify profile data') }}</flux:heading>
                <flux:text class="mt-2 text-balance">
                    {{ __('To verify these profile data, please verify your identity by entering your current password.') }}
                </flux:text>
            </div>

            <flux:separator variant="subtle" />

            {{-- Alert Section --}}
            <div class="empty:hidden">
                @if (session('error'))
                    <x-alert type="error" dismissible class="mb-4">
                        {{ session('error') }}
                    </x-alert>
                @endif

                @if (session('warning'))
                    <x-alert type="warning" dismissible class="mb-4">
                        {{ session('warning') }}
                    </x-alert>
                @endif

                @if (session('info'))
                    <x-alert type="info" dismissible class="mb-4">
                        {{ session('info') }}
                    </x-alert>
                @endif
            </div>

            {{-- Input Section --}}
            <div class="space-y-4">
                <flux:input 
                    label="{{ __('Password') }}" 
                    wire:model.live="password" 
                    placeholder="{{ __('Your password') }}" 
                    type="password" 
                    icon="shield-check" {{-- Matches your icon-centric timeline style --}}
                    required
                />
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center">
                <flux:spacer />

                <div class="flex gap-3">
                    {{-- Assuming this is in a modal/slideover, a cancel button is helpful --}}
                    <flux:button variant="ghost" x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button 
                        type="submit" 
                        variant="primary" 
                        wire:loading.attr="disabled"
                    >
                        {{-- Loading indicator for UX --}}
                        <span wire:loading.remove>{{ __('Verify & Save') }}</span>
                        <span wire:loading>{{ __('Verifying...') }}</span>
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>