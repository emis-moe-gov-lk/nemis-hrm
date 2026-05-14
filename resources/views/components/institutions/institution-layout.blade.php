<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('institutions.profile.overview', $institutionId)"
                :current="request()->routeIs('institutions.profile.overview')" wire:navigate>{{ __('Overview') }}
            </flux:navlist.item>
            @can('office.institution.profile.profile.view')
            <flux:navlist.item :href="route('institutions.profile.profile', $institutionId)"
                :current="request()->routeIs('institutions.profile.profile')" wire:navigate>{{ __('Profile') }}
            </flux:navlist.item>
            @endcan

            @can('office.institution.profile.staff.view')
            <flux:navlist.item :href="route('institutions.profile.staff', $institutionId)"
                :current="request()->routeIs('institutions.profile.staff')" wire:navigate>{{ __('Staff') }}
            </flux:navlist.item>
            @endcan



            @can('office.institution.profile.cadre-dms-approved.view')
            <flux:navlist.item :href="route('institutions.profile.cadre-dms-approved', $institutionId)"
                :current="request()->routeIs('institutions.profile.cadre-dms-approved')" wire:navigate>{{ __('DMS Approved Cadre') }}
            </flux:navlist.item>
            @endcan

            <flux:navlist.item :href="route('institutions.institution-transfer-requests', $institutionId)"
                :current="request()->routeIs('institutions.institution-transfer-requests')" wire:navigate>{{ __('Transfer requests') }}
            </flux:navlist.item>

            @can('office.institution.profile.report-module.view')
            <flux:navlist.item :href="route('institutions.profile.report-module', $institutionId)"
                :current="request()->routeIs('institutions.profile.report-module')" wire:navigate>{{ __('Reports') }}
            </flux:navlist.item>
            @endcan



        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-2">
        <div class="w-full max-w-5xl">
            <div>
                <x-institutions.institution-profile-card :institution="$institution" />
            </div>

            <div>
                @if (session('success'))
                <x-alert type="success" dismissible class="mb-4">
                    {{ session('success') }}
                </x-alert>
                @endif

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

            {{ $slot }}
        </div>
    </div>
</div>