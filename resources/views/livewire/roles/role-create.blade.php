<div class="max-w-6xl mx-left px-4 sm:px-6 lg:px-8 py-10">
    {{-- Header --}}
    <header class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-indigo-600 mb-2">
                <flux:icon.shield-check variant="micro" />
                {{ __('Access Control') }}
            </div>
            <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                {{ __('Create New Role') }}
            </flux:heading>
            <flux:subheading>
                {{ __('Define a job function and assign specific granular permissions.') }}
            </flux:subheading>
        </div>
        
        <flux:button icon="arrow-uturn-left" variant="ghost" size="sm" href="{{ route('roles.index') }}" wire:navigate>
            {{ __('Back to Roles') }}
        </flux:button>
    </header>

    <flux:separator variant="subtle" class="mb-10" />

    <form wire:submit.prevent="createRole" class="space-y-10">
        {{-- Role Name Section --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Basic Info') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('Give the role a clear name (e.g., "Editor" or "Regional Manager").') }}
                </p>
            </aside>

            <div class="md:col-span-2">
                <flux:input 
                    label="Role Name" 
                    wire:model.defer="role_name" 
                    placeholder="Enter unique role name..." 
                    class="max-w-md shadow-sm"
                />
            </div>
        </section>

        <flux:separator variant="subtle" />

        {{-- Permissions Section --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Permissions') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('Select the specific actions this role is allowed to perform across the system.') }}
                </p>
            </aside>

            <div class="md:col-span-2">
                <flux:checkbox.group wire:model="selectedPermissions">
                    <div class="space-y-6">
                        @php
                            $groupedPermissions = $allPermissions->groupBy(function($perm) {
                                return explode('.', $perm->name)[0];
                            });
                        @endphp

                        @foreach ($groupedPermissions as $prefix => $permissions)
                            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
                                {{-- Module Header --}}
                                <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                                        {{ $prefix }} Management
                                    </span>
                                </div>

                                {{-- Permissions Grid --}}
                                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($permissions as $item)
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <flux:checkbox 
                                                value="{{ $item->name }}" 
                                                class="transition-transform group-hover:scale-110"
                                            />
                                            <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">
                                                {{ str_replace($prefix . '.', '', $item->name) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:checkbox.group>
            </div>
        </section>

        {{-- Fixed Action Bar --}}
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
            <flux:button variant="ghost" href="{{ route('roles.index') }}" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" icon="shield-check" class="px-10 shadow-lg shadow-indigo-500/20">
                {{ __('Save Role Configuration') }}
            </flux:button>
        </div>
    </form>

    {{-- Notification --}}
    @if (session()->has('success'))
        <div class="fixed bottom-4 right-4 animate-bounce">
            <flux:badge variant="success" size="lg" icon="check-circle">
                {{ session('success') }}
            </flux:badge>
        </div>
    @endif
</div>