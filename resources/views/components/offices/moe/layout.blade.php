<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item  :href="route('offices.moe.profile.overview', $officeId)" :current="request()->routeIs('offices.moe.profile.overview')" wire:navigate >{{ __('Overview') }}</flux:navlist.item>
            <flux:navlist.item  :href="route('offices.moe.profile.moeprofile', $officeId)" :current="request()->routeIs('offices.moe.profile.moeprofile')" wire:navigate >{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item  :href="route('offices.moe.profile.staff', $officeId)" :current="request()->routeIs('offices.moe.profile.staff')" wire:navigate >{{ __('Staff') }}</flux:navlist.item>
            <flux:navlist.item  :href="route('offices.moe.profile.dms-cadre-summary', $officeId)" :current="request()->routeIs('offices.moe.profile.dms-cadre-summary')" wire:navigate >{{ __('DMS Cadre Summary') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <div class="mt-5 w-full max-w-5xl">
            {{ $slot }}
        </div>
    </div>
</div>
