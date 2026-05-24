<section class="w-full">
    <x-my-profile.my-profile-layout>
        <div class="space-y-6">
            <livewire:employees.appointment-current-status :peopleId="$people->people_id" :canEdit="auth()->user()->can('my-profile.employment.current-appointment.update')" />
            <livewire:employees.first-appointment :peopleId="$people->people_id" :canEdit="auth()->user()->can('my-profile.employment.first-appointment.update')" />
            <livewire:my-profile.my-data :peopleId="$people->people_id" />
            <livewire:employees.previous-services-reg :peopleId="$people->people_id" :canCreate="auth()->user()->can('my-profile.employment.previous-service.create')" :canDelete="auth()->user()->can('my-profile.employment.previous-service.delete')" />
            <livewire:employees.working-place-history :peopleId="$people->people_id" :canCreate="auth()->user()->can('my-profile.employment.working-place-history.create')" :canDelete="auth()->user()->can('my-profile.employment.working-place-history.delete')" />
            <livewire:employees.position-history :peopleId="$people->people_id" :canCreate="auth()->user()->can('my-profile.employment.position-history.create')" :canDelete="auth()->user()->can('my-profile.employment.position-history.delete')" />
        </div>
    </x-my-profile.my-profile-layout>
</section>