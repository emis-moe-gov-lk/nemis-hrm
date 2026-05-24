<form wire:submit.prevent="sendMessage" class="space-y-5">
    {{-- Name --}}
    <div>
        <flux:input
            id="name"
            type="text"
            wire:model.defer="name"
            label="Full Name"
            placeholder="Enter your name"
        />
    </div>

    {{-- Email --}}
    <div>
        <flux:input
            id="email"
            type="email"
            wire:model.defer="email"
            label="Work Email"
            placeholder="you@example.com"
        />
    </div>

    {{-- Message --}}
    <div>
        <flux:textarea
            id="message"
            rows="4"
            wire:model.defer="message"
            label="Your Message"
            placeholder="How can we help?"
        />
    </div>

    <flux:button
        type="submit"
        variant="primary"
        class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-base rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-300 hover:-translate-y-1"
    >
        <span wire:loading.remove wire:target="sendMessage">Send Message</span>
        <span wire:loading wire:target="sendMessage" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Sending…
        </span>
    </flux:button>
</form>
