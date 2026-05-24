<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Update User Profile"
        subtitle="Modify identity details and system access permissions."
        icon="pencil-square"
        :breadcrumbs="[
            'Registry' => '#',
            'Users' => route('users.index'),
            'Edit' => '#'
        ]"
    >
        <x-slot:actions>
            <flux:button icon="arrow-uturn-left" variant="ghost" size="sm" href="{{ route('users.index') }}" wire:navigate>
                {{ __('Back to List') }}
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    {{-- Main Form --}}
    <form wire:submit="userUpdate" class="space-y-8">
        
        {{-- Identity Section --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Identity Info') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('Legal identification details. Changing the NIC may trigger a re-verification process.') }}
                </p>
            </aside>

            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl p-6 shadow-sm space-y-5">
                {{-- Name --}}
                <flux:input wire:model.blur="name" :label="__('Full Name')" icon="user" autofocus />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- NIC --}}
                    <flux:input wire:model.blur="nic" :label="__('National Identity Card (NIC)')" icon="identification" />
                    
                    {{-- Contact --}}
                    <flux:input wire:model.blur="contact" :label="__('Contact Number')" icon="phone" />
                </div>
            </div>
        </section>

        {{-- Access Control Section --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Access & Roles') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('Assign system-wide permissions. Users must have at least one role to access the dashboard.') }}
                </p>
            </aside>

            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl p-6 shadow-sm space-y-6">
                {{-- Email --}}
                <flux:input wire:model.blur="email" :label="__('Email Address')" type="email" icon="envelope" />

                {{-- Roles Grid --}}
                <div class="space-y-3">
                    <flux:label>{{ __('Assigned Roles') }}</flux:label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($allRole as $role)
                            <label class="flex items-center p-4 rounded-2xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-indigo-700 cursor-pointer transition-colors group">
                                <flux:checkbox wire:model.live="roles" value="{{ $role->name }}" />
                                <span class="ml-3 text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 transition-colors">
                                    {{ $role->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <flux:error name="roles" />
                </div>
            </div>
        </section>

        {{-- Action Bar --}}
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
            <flux:button variant="ghost" href="{{ route('users.index') }}" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" icon="check-circle" class="px-10 shadow-lg shadow-indigo-500/20">
                {{ __('Update Profile') }}
            </flux:button>
        </div>
    </form>
</div>