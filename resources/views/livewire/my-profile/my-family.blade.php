<section class="w-full">
    <x-my-profile.my-profile-layout>
        <div class="space-y-6">
            <livewire:employees.family-information :peopleId="$people->people_id" :canCreate="auth()->user()->can('my-profile.family.create')" :canDelete="auth()->user()->can('my-profile.family.delete')" />
        </div>
    </x-my-profile.my-profile-layout>
</section>
