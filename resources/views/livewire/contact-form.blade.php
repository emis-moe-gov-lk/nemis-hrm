<div>
    {{-- Success State --}}
    @if ($sent)
        <div class="flex flex-col items-center justify-center py-12 text-center space-y-4">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-500/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Message Sent!</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                Thank you for reaching out. Our team will get back to you shortly.
            </p>
            <button
                wire:click="$set('sent', false)"
                class="mt-4 text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest hover:underline"
            >
                Send another message
            </button>
        </div>

    {{-- Error Notice --}}
    @elseif ($hasError)
        <div class="mb-6 flex items-start gap-3 px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-2xl">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium text-red-700 dark:text-red-400">
                Something went wrong. Please try again or contact us directly by phone.
            </p>
        </div>
        @include('livewire.contact-form-fields')
    @else
        @include('livewire.contact-form-fields')
    @endif
</div>
