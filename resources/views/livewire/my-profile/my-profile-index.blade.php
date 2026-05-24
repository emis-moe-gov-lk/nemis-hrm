<section class="w-full">
    <x-my-profile.my-profile-layout>
        <div class="space-y-8">
            {{-- Primary Identity Section --}}
            <section class="space-y-8">
                {{-- 1. Personal & Socio-Cultural Details --}}
                <livewire:employees.personal-cultural :peopleId="$peopleId" :canEdit="auth()->user()->can('my-profile.personal-cultural.update')" />

                {{-- 2. Health information --}}
                <livewire:employees.health-information :peopleId="$peopleId" :canEdit="auth()->user()->can('my-profile.health.update')" />

                {{-- 3. Contact Information --}}
                <livewire:employees.contact-information :peopleId="$peopleId" :canEdit="auth()->user()->can('my-profile.contact-information.update')" />

                {{-- 4. Location Information --}}
                <livewire:employees.location-information :peopleId="$peopleId" :canEdit="auth()->user()->can('my-profile.location-information.update')" />

                {{-- 4. Temporary Location Information --}}
                <livewire:employees.temporary-location-information :peopleId="$peopleId" :canEdit="auth()->user()->can('my-profile.temporary-location-information.update')" />
            </section>
        </div>
    </x-my-profile.my-profile-layout>
</section>