<div class="max-w-6xl mx-left px-4 sm:px-6 lg:px-8 py-10">
    {{-- Header Section --}}
    <header class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-indigo-600 mb-2">
                <flux:icon.shield-check variant="micro" />
                {{ __('Role Authorization') }}
            </div>
            <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                {{ __('Edit Role Permissions') }}
            </flux:heading>
            <flux:subheading>
                {{ __('Modifying roles will immediately affect all users currently assigned to this profile.') }}
            </flux:subheading>
        </div>
        
        <flux:button icon="arrow-uturn-left" variant="ghost" size="sm" href="{{ route('roles.index') }}" wire:navigate>
            {{ __('Back to Roles') }}
        </flux:button>
    </header>

    <flux:separator variant="subtle" class="mb-10" />

    <form wire:submit.prevent="updateRole" class="space-y-12">
        
        {{-- Section 1: Role Naming --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Identity') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('The role name should reflect the administrative level or job function.') }}
                </p>
            </aside>

            <div class="md:col-span-2">
                <flux:input 
                    label="Role Name" 
                    wire:model.defer="role_name" 
                    placeholder="e.g. Senior Auditor" 
                    class="max-w-md shadow-sm"
                />
            </div>
        </section>

        <flux:separator variant="subtle" />

        {{-- Section 2: Permission Grid --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Permissions') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('Toggle specific capabilities. Permissions are grouped by system module.') }}
                </p>
            </aside>

            <div class="md:col-span-2">
                <flux:checkbox.group wire:model="selectedPermissions">
                    <div class="space-y-6">
                        @php
                            // Grouping permissions by prefix (e.g., 'user', 'role', 'report')
                            $groupedPermissions = $allPermissions->sortBy('name')->groupBy(function($perm) {
                                return explode('.', $perm->name)[0];
                            });
                        @endphp

                        @foreach ($groupedPermissions as $prefix => $permissions)
                            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] overflow-hidden shadow-sm transition-all hover:border-indigo-500/30">
                                {{-- Module Header --}}
                                <div class="px-6 py-3 bg-slate-50 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                                    <span class="text-[11px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                                        {{ $prefix }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400">
                                        {{ $permissions->count() }} {{ __('Capabilities') }}
                                    </span>
                                </div>

                                {{-- Permission Items --}}
                                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6">
                                    @foreach ($permissions as $item)
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <flux:checkbox 
                                                value="{{ $item->name }}" 
                                                class="transition-transform group-hover:scale-110"
                                            />
                                            <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors capitalize">
                                                {{ str_replace(['.', $prefix], [' ', ''], $item->name) }}
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
        <div class="sticky bottom-6 z-10 flex items-center justify-end gap-3 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl">
            <flux:button variant="ghost" href="{{ route('roles.index') }}" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" icon="check-circle" class="px-12">
                {{ __('Update Role') }}
            </flux:button>
        </div>
    </form>

    {{-- Success Toast --}}
    @if (session()->has('success'))
        <div class="fixed top-10 left-1/2 -translate-x-1/2 z-50">
            <flux:badge variant="success" size="lg" icon="check-circle" class="shadow-xl px-6 py-2">
                {{ session('success') }}
            </flux:badge>
        </div>
    @endif
</div>