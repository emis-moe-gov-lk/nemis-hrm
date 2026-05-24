<section class="w-full">
    <x-my-profile.my-profile-layout>
        <div class="space-y-6">
            <livewire:employees.educational-qualification :peopleId="$myprofile->people_id" :canCreate="auth()->user()->can('my-profile.qualification.create')" :canDelete="auth()->user()->can('my-profile.qualification.delete')" />
        </div>
    </x-my-profile.my-profile-layout>
</section>
