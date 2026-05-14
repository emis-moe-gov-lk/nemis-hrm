<div class="max-w-5xl mx-left px-4 sm:px-6 lg:px-8 py-10">
    {{-- Page Header --}}
    <header class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-indigo-600 mb-2">
                <flux:icon.pencil-square variant="micro" />
                {{ __('Account Settings') }}
            </div>
            <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                {{ __('Update User Profile') }}
            </flux:heading>
            <flux:subheading>
                {{ __('Modify identity details and system access permissions.') }}
            </flux:subheading>
        </div>
        
        <flux:button icon="arrow-uturn-left" variant="ghost" size="sm" href="{{ route('users.index') }}" wire:navigate>
            {{ __('Back to List') }}
        </flux:button>
    </header>

    <flux:separator variant="subtle" class="mb-10" />

    {{-- Main Form --}}
    <form wire:submit="userUpdate" class="space-y-10">
        
        {{-- Identity Section --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Identity Info') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('Legal identification details. Changing the NIC may trigger a re-verification process.') }}
                </p>
            </aside>

            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm space-y-5">
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

            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm space-y-6">
                {{-- Email --}}
                <flux:input wire:model.blur="email" :label="__('Email Address')" type="email" icon="envelope" />

                {{-- Roles Grid --}}
                <div class="space-y-3">
                    <flux:label>{{ __('Assigned Roles') }}</flux:label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($allRole as $role)
                            <label class="flex items-center p-4 rounded-2xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition-colors group">
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
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
            <flux:button variant="ghost" href="{{ route('users.index') }}" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" icon="check-circle" class="px-10 shadow-lg shadow-indigo-500/20">
                {{ __('Update Profile') }}
            </flux:button>
        </div>
    </form>
</div>