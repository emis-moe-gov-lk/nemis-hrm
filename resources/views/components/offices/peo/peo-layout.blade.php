<div>
    <x-page-header
        :title="$peo->short_name"
        subtitle="Provincial Education Office"
        icon="building-office"
        :breadcrumbs="[
                    'Administrative Hierarchy' => route('offices.index'),
                    'PEO' => route('offices.peo.list'),
                    'Profile' => '#'
                ]" />
    <div class="flex items-start max-md:flex-col">

        <div class="me-10 w-full py-4 md:w-55">
            <flux:navlist>
                <flux:navlist.item :href="route('offices.peo.profile.overview', $officeId)"
                    :current="request()->routeIs('offices.peo.profile.overview')" wire:navigate>{{ __('Overview') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('offices.peo.profile.zeo-list', $officeId)"
                    :current="request()->routeIs('offices.peo.profile.zeo-list')" wire:navigate>{{ __('Zonal office') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('offices.peo.profile.profile', $officeId)"
                    :current="request()->routeIs('offices.peo.profile.profile')" wire:navigate>{{ __('Profile') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('offices.peo.profile.staff', $officeId)"
                    :current="request()->routeIs('offices.peo.profile.staff')" wire:navigate>{{ __('Staff') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('offices.peo.profile.dms-cadre-summary', $officeId)"
                    :current="request()->routeIs('offices.peo.profile.dms-cadre-summary')" wire:navigate>{{ __('DMS Cadre Summary') }}
                </flux:navlist.item>
            </flux:navlist>
        </div>

        <flux:separator class="md:hidden" />

        <div class="flex-1 self-stretch max-md:pt-6">
            <div class="mt-5 w-full max-w-5xl">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>