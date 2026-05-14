<div class="max-w-5xl mx-left px-4 sm:px-6 lg:px-8 py-10">
    {{-- Page Header --}}
    <header class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-indigo-600 mb-2">
                <flux:icon.user-plus variant="micro" />
                {{ __('Account Provisioning') }}
            </div>
            <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                {{ __('Create New User') }}
            </flux:heading>
            <flux:subheading>
                {{ __('Onboard a new member to the system and assign access roles.') }}
            </flux:subheading>
        </div>
        
        <flux:button icon="arrow-uturn-left" variant="ghost" size="sm" href="{{ route('users.index') }}" wire:navigate>
            {{ __('Back to Directory') }}
        </flux:button>
    </header>

    <flux:separator variant="subtle" class="mb-10" />

    {{-- Session Error Feedback --}}
    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-700 text-sm flex items-center gap-3">
            <flux:icon.exclamation-triangle variant="micro" class="size-5" />
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="createUser" class="space-y-12">
        
        {{-- Section 1: Identity & Contact --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Identity Info') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('Provide the legal name and identification. Ensure the NIC is unique and formatted correctly.') }}
                </p>
            </aside>

            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm space-y-5">
                <flux:input wire:model.blur="name" :label="__('Full Name')" icon="user" autofocus placeholder="e.g. Michael Perera" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <flux:input wire:model.blur="nic" :label="__('NIC Number')" icon="identification" placeholder="19XXXXXXXXXX" />
                    <flux:input wire:model.blur="contact" :label="__('Contact Number')" icon="phone" placeholder="07XXXXXXXX" />
                </div>
            </div>
        </section>

        {{-- Section 2: Security & Credentials --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Security') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('Set the login email and initial password. The user can change their password later.') }}
                </p>
            </aside>

            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm space-y-5">
                <flux:input wire:model.blur="email" :label="__('Email Address')" type="email" icon="envelope" placeholder="email@example.com" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <flux:input wire:model.blur="password" :label="__('Password')" type="password" icon="lock-closed" viewable />
                    <flux:input wire:model.blur="password_confirmation" :label="__('Confirm Password')" type="password" icon="lock-closed" viewable />
                </div>
            </div>
        </section>

        {{-- Section 3: Access Roles --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <aside>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ __('Permissions') }}</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    {{ __('Assign one or more roles to define what this user can see and do.') }}
                </p>
            </aside>

            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm space-y-4">
                <flux:label>{{ __('Select Roles') }}</flux:label>
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
        </section>

        {{-- Submit Action --}}
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
            <flux:button variant="ghost" href="{{ route('users.index') }}" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" icon="user-plus" class="px-10 shadow-lg shadow-indigo-500/20">
                {{ __('Create User Account') }}
            </flux:button>
        </div>
    </form>
</div>