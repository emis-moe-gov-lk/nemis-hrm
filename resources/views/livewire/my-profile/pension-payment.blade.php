<section class="w-full">
    <x-my-profile.my-profile-layout>
        <div class="space-y-6">
            <livewire:employees.pension-payment :peopleId="$peopleId" :canEdit="auth()->user()->can('my-profile.pension-and-payment.update')" />
        </div>
    </x-my-profile.my-profile-layout>
</section>
