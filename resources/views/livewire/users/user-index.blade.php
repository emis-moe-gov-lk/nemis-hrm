<div class="max-w-7xl mx-left px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    {{-- Header Section --}}
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                {{ __('User Directory') }}
            </flux:heading>
            <flux:subheading size="lg" class="mt-1">
                {{ __('Manage system access, roles, and security profiles') }}
            </flux:subheading>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3">
            <flux:input wire:model.live.debounce.300ms="search" 
                placeholder="Search by name, NIC..." 
                icon="magnifying-glass"
                kbd="⌘K"
                class="w-full sm:w-64" />

            @can('user.create')
                <a href="{{ route('users.create') }}" class="w-full sm:w-auto">
                    <flux:button icon="plus" variant="primary" class="w-full shadow-sm">
                        Create User
                    </flux:button>
                </a>
            @endcan
        </div>
    </header>

    <flux:separator variant="subtle" />

    {{-- Session Feedback --}}
    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm flex items-center gap-3">
            <flux:icon.check-circle variant="micro" class="size-5" />
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-700 text-sm flex items-center gap-3">
            <flux:icon.exclamation-circle variant="micro" class="size-5" />
            {{ session('error') }}
        </div>
    @endif

    {{-- Horizontal Row Layout --}}
    <div class="space-y-3">
        @foreach ($users as $user)
            <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 transition-all duration-200 hover:shadow-md hover:border-indigo-500/30">
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    
                    {{-- 1. Profile Identity --}}
                    <div class="flex items-center gap-4 min-w-[280px] lg:w-1/4">
                        <div class="relative flex-shrink-0">
                            <img class="h-12 w-12 rounded-full object-cover ring-2 ring-slate-100 dark:ring-slate-800" 
                                 src="{{ asset('images/user_profile.png') }}" alt="Avatar">
                            <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full border-2 border-white dark:border-slate-900 {{ $user->active_status ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                {{ $user->name }}
                            </h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                NIC: {{ $user->nic }}
                            </p>
                        </div>
                    </div>

                    {{-- 2. Contact Info --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:gap-8 flex-1">
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 min-w-[200px]">
                            <flux:icon.envelope variant="micro" class="size-4 text-slate-400" />
                            <span class="text-sm truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                            <flux:icon.building-office variant="micro" class="size-4 text-slate-400" />
                            <span class="text-[11px] font-bold uppercase tracking-tight truncate max-w-[150px]">
                                {{ $user->currentAppointment?->workplace?->name ?? 'No Workplace' }}
                            </span>
                        </div>
                    </div>

                    {{-- 3. Roles (Badges) --}}
                    <div class="flex flex-wrap gap-1.5 lg:w-1/6">
                        @forelse($user->roles as $role)
                            <flux:badge size="sm" variant="subtle" class="whitespace-nowrap">
                                {{ $role->name }}
                            </flux:badge>
                        @empty
                            <span class="text-[10px] text-slate-400 font-bold uppercase">No Role</span>
                        @endforelse
                    </div>

                    {{-- 4. Actions (Desktop: Visible Buttons / Mobile: Dropdown) --}}
                    <div class="flex items-center justify-end gap-2 border-t md:border-0 pt-4 md:pt-0">
                        @can('user.edit')
                            <a href="{{ route('users.edit', $user->id) }}" class="hidden lg:block">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" square />
                            </a>
                        @endcan

                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" square />
                            <flux:menu class="min-w-48">
                                <div class="lg:hidden">
                                    @can('user.edit')
                                        <flux:menu.item icon="pencil-square" href="{{ route('users.edit', $user->id) }}">Edit Profile</flux:menu.item>
                                    @endcan
                                </div>
                                @can('user.password.reset')
                                    <flux:menu.item icon="key" wire:click="resetPassword({{ $user->id }})">Reset Password</flux:menu.item>
                                @endcan
                                @can('user.status.update')
                                    <flux:menu.item icon="{{ $user->active_status ? 'no-symbol' : 'check' }}" 
                                        wire:click="toggleStatus({{ $user->id }})"
                                        variant="{{ $user->active_status ? 'danger' : 'danger' }}">
                                        {{ $user->active_status ? 'Deactivate' : 'Activate' }}
                                    </flux:menu.item>
                                @endcan
                                <flux:menu.separator />
                                @if ($user->id != Auth::user()->id && $user->id != 1)
                                    @can('user.delete')
                                        <flux:menu.item variant="danger" icon="trash" href="{{ route('users.delete', $user->id) }}">Delete User</flux:menu.item>
                                    @endcan
                                @endif
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8 px-4">
        {{ $users->links() }}
    </div>
</div>