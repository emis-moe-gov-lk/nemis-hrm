<div>
    <form wire:submit.prevent="submit">
        @csrf
        <div class="space-y-6">
            {{-- Header section --}}
            <div class="pb-4 border-b border-slate-200 dark:border-zinc-700">
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">{{ __('Verify profile data') }}</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                    {{ __('To verify these profile data, please verify your identity by entering your current password.') }}
                </p>
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
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-200 dark:border-zinc-700">
                {{-- Assuming this is in a modal/slideover, a cancel button is helpful --}}
                <flux:button variant="ghost" x-on:click="$dispatch('close')" class="font-bold rounded-xl h-11">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button 
                    type="submit" 
                    variant="primary" 
                    wire:loading.attr="disabled"
                    class="font-black rounded-xl h-11 bg-indigo-600 dark:bg-white text-white dark:text-slate-900"
                >
                    {{-- Loading indicator for UX --}}
                    <span wire:loading.remove>{{ __('Verify & Save') }}</span>
                    <span wire:loading>{{ __('Verifying...') }}</span>
                </flux:button>
            </div>
        </div>
    </form>
</div>