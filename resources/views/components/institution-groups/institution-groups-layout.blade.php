@props([
    'hasAssignedGroups' => false,
])

<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('institution-groups.index')"
                :current="request()->routeIs('institution-groups.index')" wire:navigate>{{ __('Overview') }}
            </flux:navlist.item>

            <flux:navlist.item
                :href="$hasAssignedGroups ? route('institution-groups.institutions-list') : '#'"
                :current="$hasAssignedGroups && request()->routeIs('institution-groups.institutions-list')"
                :class="$hasAssignedGroups ? '' : 'pointer-events-none opacity-50'"
                wire:navigate>{{ __('Institutions List') }}
            </flux:navlist.item>

            <flux:navlist.item
                :href="$hasAssignedGroups ? route('institution-groups.principle-list') : '#'"
                :current="$hasAssignedGroups && request()->routeIs('institution-groups.principle-list')"
                :class="$hasAssignedGroups ? '' : 'pointer-events-none opacity-50'"
                wire:navigate>{{ __('Principal List') }}
            </flux:navlist.item>

            <flux:navlist.item
                :href="$hasAssignedGroups ? route('institution-groups.teachers-list') : '#'"
                :current="$hasAssignedGroups && request()->routeIs('institution-groups.teachers-list')"
                :class="$hasAssignedGroups ? '' : 'pointer-events-none opacity-50'"
                wire:navigate>{{ __('Teachers List') }}
            </flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-2">
        <div class="w-full max-w-5xl">
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
