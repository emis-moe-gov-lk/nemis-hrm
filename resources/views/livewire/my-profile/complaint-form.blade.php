<div class="space-y-6">
    <div>
        <flux:heading size="lg">Request Update Profile</flux:heading>
        <flux:text class="mt-2">Make changes to your personal details.</flux:text>
        <flux:text class="mt-2"></flux:text>
        <p>
            NIC: {{ auth()->user()->nic ?? 'N/A' }}
        </p>
    </div>

    <div class="mb-4">
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

    <form wire:submit.prevent="save" class="space-y-4">
        @csrf
        <flux:input label="Subject" wire:model.live="subject" placeholder="subject" />

        <flux:textarea label="Request/Complaint" wire:model.live="complaint"
            placeholder="Write your appeal clearly and concisely..." />

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="primary">Submit</flux:button>
        </div>
    </form>
</div>
