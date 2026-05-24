<div>
    <x-page-header
        :title="$deo->short_name"
        subtitle="Divisional Education Office"
        icon="building-office"
        :breadcrumbs="[
            'Administrative Hierarchy' => route('offices.index'),
            'DEO' => route('offices.deo.list'),
            'Profile' => '#'
                ]" />
    <div class="flex items-start max-md:flex-col">
        <div class="me-10 w-full py-4 md:w-[220px]">
            <flux:navlist>
                <flux:navlist.item :href="route('offices.deo.profile.overview', $officeId)"
                    :current="request()->routeIs('offices.deo.profile.overview')" wire:navigate>{{ __('Overview') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('offices.deo.profile.profile', $officeId)"
                    :current="request()->routeIs('offices.deo.profile.profile')" wire:navigate>{{ __('Profile') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('offices.deo.profile.dms-cadre-summary', $officeId)"
                    :current="request()->routeIs('offices.deo.profile.dms-cadre-summary')" wire:navigate>{{ __('DMS Cadre Summary') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('offices.deo.profile.staff', $officeId)"
                    :current="request()->routeIs('offices.deo.profile.staff')" wire:navigate>{{ __('Staff') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('offices.deo.profile.report-module', $officeId)"
                    :current="request()->routeIs('offices.deo.profile.report-module')" wire:navigate>
                    {{ __('Report Module') }}
                </flux:navlist.item>
            </flux:navlist>
        </div>

        <flux:separator class="md:hidden" />

        <div class="flex-1 self-stretch max-md:pt-6">
            <div class="mt-5 w-full max-w-5xl">
                <div class="mt-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>