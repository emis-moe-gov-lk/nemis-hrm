<div class="max-w-7xl mx-auto pb-10 px-4 md:px-6">
    {{-- 1. Profile Header Section - Premium Redesign --}}
    <div class="bg-white dark:bg-zinc-900 rounded-4xl border border-slate-300/60 dark:border-zinc-700 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.05)] overflow-hidden">
        {{-- Cover Photo with multi-layer effect --}}
        <div class="h-24 md:h-36 bg-indigo-600 relative overflow-hidden">
            <div class="absolute inset-0 bg-linear-to-br from-indigo-600/90 via-blue-600/80 to-slate-900"></div>
            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-white/10 blur-3xl rounded-full"></div>
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-indigo-500/20 blur-3xl rounded-full"></div>
        </div>

        {{-- Profile Info Area --}}
        <div class="px-6 md:px-12 pb-6">
            <div class="relative flex flex-col md:flex-row items-center md:items-end -mt-16 md:-mt-20 mb-6 gap-6">
                {{-- Profile Image with glow --}}
                <div class="relative group">
                    <div class="absolute -inset-1 bg-linear-to-tr from-indigo-500 to-blue-500 rounded-full blur opacity-25 group-hover:opacity-40 transition duration-1000"></div>
                    <div class="relative">
                        @if ($myprofile->gender_id == 'G02')
                        <img src="{{ asset('images/profile_f.png') }}" alt="Profile" class="w-32 h-32 md:w-48 md:h-48 rounded-full border-4 border-white dark:border-zinc-900 shadow-2xl object-cover bg-white" />
                        @else
                        <img src="{{ asset('images/profile_m.png') }}" alt="Profile" class="w-32 h-32 md:w-48 md:h-48 rounded-full border-4 border-white dark:border-zinc-900 shadow-2xl object-cover bg-white" />
                        @endif
                        <span class="absolute bottom-3 right-3 md:bottom-5 md:right-5 block h-5 w-5 md:h-6 md:w-6 rounded-full border-4 border-white dark:border-zinc-900 {{ $myprofile->appointment->is_confirmed ? 'bg-green-500' : 'bg-rose-500' }} shadow-sm"></span>
                    </div>
                </div>

                <div class="flex-1 text-center md:text-left md:mb-6">
                    <div class="space-y-1">
                        <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tighter leading-none">
                            {{ $myprofile->title->title_name }} {{ $myprofile->name_with_initials }}
                        </h1>
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-[0.2em]">
                                {{ $myprofile->currentAppointment?->appointment?->service?->service_name ?? 'Service Not Assigned' }}
                            </span>
                            <div class="flex items-center justify-center md:justify-start gap-2 text-slate-500 dark:text-slate-500 font-bold text-sm">
                                <flux:icon.map-pin variant="micro" class="opacity-50" />
                                {{ $myprofile->currentAppointment?->workplace?->office_name ?? 'No Workplace Assigned' ?? 'No Workplace Assigned' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Header Actions --}}
                <div class="flex items-center gap-3 mb-6">
                    @can('my-profile.edit-request.create')
                    <flux:modal.trigger name="edit-profile">
                        <flux:button variant="filled" size="sm" class="font-black uppercase tracking-widest text-[10px] px-6 py-2.5 rounded-xl bg-indigo-600 dark:bg-white text-white dark:text-slate-900 hover:bg-indigo-700 transition-all active:scale-95">
                            {{ __('Send Edit Request') }}
                        </flux:button>
                    </flux:modal.trigger>
                    @endcan

                    @can('my-profile.pdf.view')
                    <a href="{{ route('my-profile.pdf') }}" download>
                        <flux:button variant="ghost" size="sm" icon="cloud-arrow-down" class="rounded-xl border border-slate-300 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all" />
                    </a>
                    @endcan
                </div>
            </div>

            {{-- 2. Modern Navigation Bar - Image Reference Style --}}
            <div class="border-t border-slate-200 dark:border-zinc-700 px-6 md:px-12 py-3 overflow-x-auto custom-scrollbar">
                <nav class="flex items-center gap-2">
                    @php
                    $navItemClass = "flex items-center gap-2.5 px-6 py-2.5 rounded-2xl text-sm font-black transition-all duration-300 whitespace-nowrap uppercase tracking-widest";
                    $activeClass = "bg-white dark:bg-zinc-900 text-indigo-600 dark:text-white shadow-sm ring-1 ring-slate-200/50 dark:ring-zinc-700 scale-[1.02]";
                    $inactiveClass = "text-slate-500 hover:text-slate-600 dark:text-zinc-400 dark:hover:text-zinc-300";
                    @endphp

                    {{-- General --}}
                    <a href="{{ route('my-profile.index', $myprofile->id) }}" wire:navigate
                        class="{{ $navItemClass }} {{ request()->routeIs('my-profile.index') ? $activeClass : $inactiveClass }}">
                        <flux:icon.user variant="micro" class="{{ request()->routeIs('my-profile.index') ? 'text-indigo-600' : 'text-slate-500' }}" />
                        <span>{{ __('General') }}</span>
                    </a>

                    @can('my-profile.qualification.view')
                    <a href="{{ route('my-profile.qualification', $myprofile->id) }}" wire:navigate
                        class="{{ $navItemClass }} {{ request()->routeIs('my-profile.qualification') ? $activeClass : $inactiveClass }}">
                        <flux:icon.academic-cap variant="micro" class="{{ request()->routeIs('my-profile.qualification') ? 'text-indigo-600' : 'text-slate-500' }}" />
                        <span>{{ __('Qualification') }}</span>
                    </a>
                    @endcan

                    @can('my-profile.employment.view')
                    <a href="{{ route('my-profile.employment', $myprofile->id) }}" wire:navigate
                        class="{{ $navItemClass }} {{ request()->routeIs('my-profile.employment') ? $activeClass : $inactiveClass }}">
                        <flux:icon.briefcase variant="micro" class="{{ request()->routeIs('my-profile.employment') ? 'text-indigo-600' : 'text-slate-500' }}" />
                        <span>{{ __('Employment') }}</span>
                    </a>
                    @endcan

                    @can('my-profile.pension-and-payment.view')
                    <a href="{{ route('my-profile.pension-and-payment', $myprofile->id) }}" wire:navigate
                        class="{{ $navItemClass }} {{ request()->routeIs('my-profile.pension-and-payment') ? $activeClass : $inactiveClass }}">
                        <flux:icon.credit-card variant="micro" class="{{ request()->routeIs('my-profile.pension-and-payment') ? 'text-indigo-600' : 'text-slate-500' }}" />
                        <span>{{ __('W&OP') }}</span>
                    </a>
                    @endcan

                    @can('my-profile.family.view')
                    <a href="{{ route('my-profile.family', $myprofile->id) }}" wire:navigate
                        class="{{ $navItemClass }} {{ request()->routeIs('my-profile.family') ? $activeClass : $inactiveClass }}">
                        <flux:icon.users variant="micro" class="{{ request()->routeIs('my-profile.family') ? 'text-indigo-600' : 'text-slate-500' }}" />
                        <span>{{ __('Family') }}</span>
                    </a>
                    @endcan

                    @can('my-profile.edit-request.view')
                    <a href="{{ route('my-profile.edit-request', $myprofile->id) }}" wire:navigate
                        class="{{ $navItemClass }} {{ request()->routeIs('my-profile.edit-request') ? $activeClass : $inactiveClass }}">
                        <flux:icon.pencil-square variant="micro" class="{{ request()->routeIs('my-profile.edit-request') ? 'text-indigo-600' : 'text-slate-500' }}" />
                        <span>{{ __('Edit Request') }}</span>
                    </a>
                    @endcan
                </nav>
            </div>
        </div>
    </div>

    {{-- 3. Main Content Grid --}}
    <div class="mt-8 flex flex-col lg:flex-row gap-8">

        {{-- SIDEBAR: Info Cards --}}
        <div class="order-1 lg:order-2 w-full lg:w-[340px] space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-300/60 dark:border-zinc-700 shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-8 w-1 bg-indigo-600 rounded-full"></div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">System Info</h2>
                </div>

                <div class="space-y-6">
                    <div class="group">
                        <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest mb-1 group-hover:text-indigo-600 transition-colors">National ID</p>
                        <p class="text-sm font-mono font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-zinc-800 px-3 py-2 rounded-xl">{{ $myprofile->nic }}</p>
                    </div>
                    <div class="group">
                        <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest mb-1 group-hover:text-indigo-600 transition-colors">Employee ID</p>
                        <p class="text-sm font-mono font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-zinc-800 px-3 py-2 rounded-xl">{{ $myprofile->people_id }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="group">
                            <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest mb-1">W&OP No</p>
                            <p class="text-xs font-mono font-bold text-slate-700 dark:text-zinc-300">{{ $myprofile->appointment->w_op_no ?? 'N/A' }}</p>
                        </div>
                        <div class="group">
                            <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest mb-1">Pay Sheet No</p>
                            <p class="text-xs font-mono font-bold text-slate-700 dark:text-zinc-300">{{ $myprofile->appointment->pay_sheet_no ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Links or Status Card --}}
            <div class="bg-indigo-600 rounded-2xl p-6 text-white overflow-hidden relative group">
                <flux:icon.sparkles class="absolute -right-4 -top-4 size-24 text-white/10 group-hover:scale-125 transition-transform duration-700" />
                <h3 class="text-lg font-black tracking-tight mb-2 relative z-10">Profile Status</h3>
                <p class="text-indigo-100 text-xs font-medium mb-4 relative z-10">Your profile information is verified and up to date.</p>
                <div class="relative z-10 inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-[10px] font-black uppercase tracking-widest">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    Live Data
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT AREA --}}
        <div class="order-2 lg:order-1 flex-1 min-w-0 space-y-6">

            {{-- RESTORED: Verify and Confirm Alerts (Original Logic) --}}
            @if (!$myprofile->appointment?->is_confirmed && !$myprofile->appointment?->is_verified)
            @can('my-profile.profile.verify')
            <div class="mb-6">
                <x-alert type="warning" :dismissible="false">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="font-semibold">Profile Not Verified</span>
                            <span>This profile has not been verified yet. Please review the details.</span>
                        </div>
                        <flux:modal.trigger name="verify-profile">
                            <button class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors text-sm font-medium whitespace-nowrap shadow-sm">Verify</button>
                        </flux:modal.trigger>
                    </div>
                </x-alert>
            </div>
            @endcan
            @elseif(!$myprofile->appointment?->is_confirmed && $myprofile->appointment?->is_verified)
            @can('my-profile.profile.confirm')
            <div class="mb-6">
                <x-alert type="warning" :dismissible="false">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="font-semibold">Profile Not Confirmed</span>
                            <span>This profile has not been confirmed yet. Please review the details.</span>
                        </div>
                        <flux:modal.trigger name="confirm-profile">
                            <button class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors text-sm font-medium whitespace-nowrap shadow-sm">Confirm</button>
                        </flux:modal.trigger>
                    </div>
                </x-alert>
            </div>
            @endcan
            @endif

            {{-- Pending Edit Request Alert --}}
            @if ($myprofile->profileEditRequests->where('status', 1)->isNotEmpty())
            @can('my-profile.profile.edit-request.response')
            <div class="mb-6">
                <x-alert type="info" :dismissible="false">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="font-semibold">Profile Edit Request Pending</span>
                            <span>You have a pending profile edit request. Please wait for approval.</span>
                        </div>
                        <flux:modal.trigger name="edit-profile-request-response">
                            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium whitespace-nowrap shadow-sm">Response</button>
                        </flux:modal.trigger>
                    </div>
                </x-alert>
            </div>
            @endcan
            @endif

            {{-- Original Session Messages --}}
            <div class="space-y-4 mb-4">
                @if (session('success')) <x-alert type="success" dismissible>{{ session('success') }}</x-alert> @endif
                @if (session('error')) <x-alert type="error" dismissible>{{ session('error') }}</x-alert> @endif
                @if (session('warning')) <x-alert type="warning" dismissible>{{ session('warning') }}</x-alert> @endif
                @if (session('info')) <x-alert type="info" dismissible>{{ session('info') }}</x-alert> @endif
            </div>

            {{-- SLOT --}}
            <div class="min-w-0">
                {{ $slot }}
            </div>
        </div>
    </div>

    @can('my-profile.edit-request.create')
    <!-- Edit Profile Modal -->
    <flux:modal name="edit-profile" class="md:w-150">
        <livewire:my-profile.complaint-form :peopleId="$myprofile->people_id" />
    </flux:modal>
    @endcan

    @can('my-profile.confirm')
    <!-- Confirm Profile Modal -->
    <flux:modal name="confirm-profile" class="md:w-96">
        <div class="space-y-6">
            <livewire:employees.confirm-profile-form :peopleId="$myprofile->people_id" />
        </div>
    </flux:modal>
    @endcan

    @can('my-profile.verify')
    <!-- Verify Profile Modal -->
    <flux:modal name="verify-profile" class="md:w-96">
        <div class="space-y-6">
            <livewire:employees.verify-profile-form :peopleId="$myprofile->people_id" />
        </div>
    </flux:modal>
    @endcan

    @can('my-profile.edit-request.response')
    <flux:modal name="edit-profile-request-response" class="md:w-150">
        <livewire:employees.edit-profile-requst-response-form :peopleId="$myprofile->people_id" />
    </flux:modal>
    @endcan
</div>
