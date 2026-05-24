<div>
    <x-page-header
        :title="$zeo->short_name"
        subtitle="Zonal Education Office"
        icon="building-office"
        :breadcrumbs="[
                    'Administrative Hierarchy' => route('offices.index'),
                    'ZEO' => route('offices.zeo.list'),
                    'Profile' => '#'
                ]" />
    <div class="flex items-start max-md:flex-col">
        <div class="me-10 w-full py-4 md:w-55">
            <flux:navlist>
                <flux:navlist.item
                    :href="route('offices.zeo.profile.overview', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.overview')"
                    wire:navigate>
                    {{ __('Overview') }}
                </flux:navlist.item>

                <flux:navlist.item
                    :href="route('offices.zeo.profile.profile', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.profile')"
                    wire:navigate>
                    {{ __('Profile') }}
                </flux:navlist.item>

                <flux:navlist.item
                    :href="route('offices.zeo.profile.staff', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.staff')"
                    wire:navigate>
                    {{ __('Staff') }}
                </flux:navlist.item>

                <flux:navlist.item
                    :href="route('offices.zeo.profile.institutions-list', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.institutions-list')"
                    wire:navigate>
                    {{ __('Institutions') }}
                </flux:navlist.item>

                <flux:navlist.item
                    :href="route('offices.zeo.profile.institution-groups', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.institution-groups*') || request()->routeIs('offices.zeo.profile.institution-group.create')"
                    wire:navigate>
                    {{ __('Institution Groups') }}
                </flux:navlist.item>

                <flux:navlist.item
                    :href="route('offices.zeo.profile.teachers-list', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.teachers-list')"
                    wire:navigate>
                    {{ __('Teachers') }}
                </flux:navlist.item>

                <flux:navlist.item
                    :href="route('offices.zeo.profile.zeo-principals-list', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.zeo-principals-list')"
                    wire:navigate>
                    {{ __('Principals') }}
                </flux:navlist.item>

                <flux:navlist.item
                    :href="route('offices.zeo.profile.dms-cadre-summary', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.dms-cadre-summary')"
                    wire:navigate>
                    {{ __('DMS Cadre Summary') }}
                </flux:navlist.item>

                @if(\App\Support\Transfer\TransferAccess::canViewZonalRequests(auth()->user(), $zeo?->workplace))
                <flux:navlist.item
                    :href="route('offices.zeo.profile.teachers-transfer-requests', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.teachers-transfer-requests')"
                    wire:navigate>
                    {{ __('Teachers Transfer Requests') }}
                </flux:navlist.item>
                @endif

                <flux:navlist.item
                    :href="route('offices.zeo.profile.report-module', $officeId)"
                    :current="request()->routeIs('offices.zeo.profile.report-module')"
                    wire:navigate>
                    {{ __('Report Module') }}
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