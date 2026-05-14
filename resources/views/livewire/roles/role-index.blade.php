<div class="max-w-7xl mx-left px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    {{-- Header Section --}}
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                {{ __('Roles List') }}
            </flux:heading>
            <flux:subheading size="lg" class="mt-1">
                {{ __('Manage role profiles and permissions') }}
            </flux:subheading>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('roles.create') }}" wire:navigate>
                <flux:button variant="primary" icon="plus" class="shadow-sm w-full md:w-auto">
                    Create New Role
                </flux:button>
            </a>
        </div>
    </header>

    <flux:separator variant="subtle" />

    {{-- Session Message --}}
    @if (session('message'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    {{-- Cards Grid (Role List) --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse ($roles as $role)
            <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 transition-all duration-300 hover:shadow-xl">
                <div class="flex flex-col space-y-4">
                    
                    {{-- Role Identity Header --}}
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg">
                                <flux:icon.shield-check variant="micro" class="size-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">{{ $role->name }}</h3>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ID: {{ $role->id }}</span>
                            </div>
                        </div>

                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" square />
                            <flux:menu>
                                <flux:menu.item icon="pencil-square" href="{{ route('roles.edit', $role->id) }}">Edit</flux:menu.item>
                                <flux:menu.item variant="danger" icon="trash" wire:click="delete({{ $role->id }})">Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    {{-- Collapsible Permissions Section (Alpine.js) --}}
                    <div x-data="{ open: false }" class="border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden">
                        <button 
                            @click="open = !open" 
                            class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        >
                            <div class="flex items-center gap-2">
                                <flux:icon.key variant="micro" class="size-4 text-slate-400" />
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                                    {{ $role->permissions->count() }} Permissions
                                </span>
                            </div>
                            <flux:icon.chevron-down 
                                variant="micro" 
                                class="size-4 text-slate-400 transition-transform duration-300" 
                                ::class="open ? 'rotate-180' : ''" 
                            />
                        </button>

                        <div 
                            x-show="open" 
                            x-collapse 
                            class="p-4 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800"
                        >
                            @if ($role->permissions->count())
                                @php
                                    $grouped = $role->permissions->sortBy('name')->groupBy(fn($perm) => explode('.', $perm->name)[0]);
                                @endphp

                                <div class="space-y-4">
                                    @foreach ($grouped as $prefix => $permissions)
                                        <div>
                                            <div class="text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-2">
                                                {{ $prefix }}
                                            </div>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach ($permissions as $permission)
                                                    <flux:badge size="sm" variant="subtle" class="!text-[10px] py-0 px-2 font-medium">
                                                        {{ str_replace($prefix.'.', '', $permission->name) }}
                                                    </flux:badge>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs italic text-slate-400">No permissions assigned to this role.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 bg-slate-50 dark:bg-slate-800/50 rounded-[2.5rem] border-2 border-dashed border-slate-200 dark:border-slate-800 text-center">
                <flux:icon.shield-exclamation class="size-12 text-slate-300 mb-4" />
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Roles Defined</h3>
                <p class="text-slate-500 text-sm mt-1">Get started by creating your first security role.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8 px-2">
        {{ $roles->links() }}
    </div>
</div>