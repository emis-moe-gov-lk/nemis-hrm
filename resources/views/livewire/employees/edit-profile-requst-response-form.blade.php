<div>
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <flux:heading size="lg">{{ __('Review Profile Update') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Review and respond to the user update request below.') }}</flux:text>
        </div>

        {{-- Request Summary Card --}}
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Reference</span>
                <flux:badge size="sm" variant="pill" color="indigo" inset="top bottom">
                    {{ $complaint?->complaint_request_ref ?? 'N/A' }}
                </flux:badge>
            </div>

            <div class="p-4 space-y-3">
                @php $req = $complaint->requested_changes ?? []; @endphp
                
                <div>
                    <p class="text-[10px] font-bold uppercase text-blue-600 dark:text-blue-400">Subject</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $req['subject'] ?? 'No subject provided' }}
                    </p>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase text-red-600 dark:text-red-400">User Complaint / Request</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $req['complaint'] ?? 'No details provided' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        <div class="empty:hidden">
            @if (session('error')) <x-alert type="error" dismissible class="mb-4">{{ session('error') }}</x-alert> @endif
            @if (session('warning')) <x-alert type="warning" dismissible class="mb-4">{{ session('warning') }}</x-alert> @endif
            @if (session('info')) <x-alert type="info" dismissible class="mb-4">{{ session('info') }}</x-alert> @endif
        </div>

        {{-- Action Form --}}
        <form wire:submit.prevent="submit" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:select label="Decision (Status)" wire:model.live="response" placeholder="Select status...">
                    <flux:select.option value="">Select</flux:select.option>
                    @foreach ($responseOption as $key => $data)
                        <flux:select.option value="{{ $key }}">{{ $data }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select label="Quick Reply Template" wire:model.live="reply" placeholder="Choose a reply...">
                    <flux:select.option value="">Select</flux:select.option>
                    @foreach ($replyOption as $key => $data)
                        <flux:select.option value="{{ $key }}">{{ $data }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            {{-- Optional: Add a custom comment area if the dropdown isn't enough --}}
            {{-- <flux:textarea label="Additional Comments" wire:model="custom_comment" /> --}}

            <div class="flex items-center pt-4 border-t border-gray-100 dark:border-gray-800">
                <flux:spacer />
                <div class="flex gap-3">
                    <flux:button variant="ghost" x-on:click="$dispatch('close')">Cancel</flux:button>
                    <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                        {{ __('Confirm Action') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </div>    
</div>