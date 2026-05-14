<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-55">
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
            <div class="mb-6">
                <div
                    class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm
               dark:border-slate-700 dark:bg-slate-900
               md:flex-row md:items-center md:justify-between">

                    {{-- Left: Office Info --}}
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg
                       bg-blue-100 text-blue-600
                       dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10l9-7 9 7v11a1 1 0 01-1 1h-5a1 1 0 01-1-1V14H9v7a1 1 0 01-1 1H3a1 1 0 01-1-1V10z" />
                            </svg>
                        </div>

                        {{-- Text --}}
                        <div>
                            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">
                                {{ $peo->name }}
                            </h1>

                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                Provincial Education Office
                            </p>
                        </div>
                    </div>

                    {{-- Right: Meta Info --}}
                    <div class="flex flex-wrap items-center gap-3 text-sm">

                        {{-- Last Updated --}}
                        @if ($peo->updated_at)
                            <span
                                class="inline-flex items-center gap-1 rounded-md
                           bg-amber-100 px-3 py-1
                           text-amber-700
                           dark:bg-amber-900/30 dark:text-amber-400"
                                title="{{ $peo->updated_at->format('Y-m-d H:i:s') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Last updated {{ $peo->updated_at->diffForHumans() }}
                            </span>
                        @endif

                    </div>
                </div>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
