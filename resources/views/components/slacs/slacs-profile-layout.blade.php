<div class="max-w-7xl mx-left pb-10 px-0 md:px-4">
    {{-- 1. Profile Header Section --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border-x border-b border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        {{-- Cover Photo --}}
        <div class="h-32 md:h-52 bg-gradient-to-r from-blue-600 to-indigo-600 relative">
            <div class="absolute inset-0 opacity-100 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>

        {{-- Profile Info Area --}}
        <div class="px-4 md:px-8 pb-2">
            <div class="relative flex flex-col md:flex-row items-center md:items-end -mt-12 md:-mt-16 mb-4 gap-4">
                {{-- Profile Image --}}
                <div class="relative">
                    @if ($slacs->gender_id == 'G02')
                        <img src="{{ asset('images/profile_f.png') }}" alt="Profile" class="w-28 h-28 md:w-40 md:h-40 rounded-full border-4 border-white dark:border-gray-900 shadow-lg object-cover bg-white" />
                    @else
                        <img src="{{ asset('images/profile_m.png') }}" alt="Profile" class="w-28 h-28 md:w-40 md:h-40 rounded-full border-4 border-white dark:border-gray-900 shadow-lg object-cover bg-white" />
                    @endif
                    <span class="absolute bottom-2 right-2 md:bottom-3 md:right-3 block h-4 w-4 md:h-5 md:w-5 rounded-full border-2 border-white dark:border-gray-900 {{ $slacs->appointment->is_confirmed ? 'bg-green-500' : 'bg-red-500' }}"></span>
                </div>

                <div class="flex-1 text-center md:text-left md:mb-4">
                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tighter">
                        {{ $slacs->title->title_name }} {{ $slacs->name_with_initials }}
                    </h1>
                    
                    <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-3 mt-1">
                        <p class="text-indigo-600 dark:text-indigo-400 font-bold text-xs md:text-sm tracking-widest">
                            {{ $slacs->currentAppointment->service->service_name }}
                        </p>

                        <span class="hidden md:block w-1 h-1 bg-slate-300 rounded-full"></span>

                        <p class="text-slate-500 dark:text-slate-400 font-medium text-sm md:text-base flex items-center justify-center md:justify-start gap-1.5">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $slacs->currentAppointment->workplace->office()->name ?? 'No Workplace Assigned' }}
                        </p>
                    </div>
                </div>

                {{-- Header Actions with Original Logos --}}
                <div class="flex gap-2 mb-4 w-full md:w-auto justify-center px-4 md:px-0">
                    @can('slacs.profile.edit-request.create')
                    <flux:modal.trigger name="edit-profile">
                        <button class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-xs md:text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            {{ __('Send Edit Request') }}
                        </button>
                    </flux:modal.trigger>
                    @endcan
                    
                    @can('slacs.profile.pdf.view')
                    <a href="{{ route('teacher.profile.pdf', $slacs->id) }}" download class="flex-1 md:flex-none">
                        <button class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-xs md:text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5l5 5v2m0 0h-5"></path>
                            </svg>
                            {{ __('Get Document') }}
                        </button>
                    </a>
                    @endcan
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            {{-- 2. Horizontal Navigation --}}
            <div class="w-full mt-2">
                <nav class="flex justify-between md:justify-start gap-1 md:gap-5 p-1 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                    {{-- General --}}
                    <a href="{{ route('slacs.profile.index', $slacsid) }}" wire:navigate 
                    class="flex flex-1 md:flex-none flex-col md:flex-row items-center justify-center py-2.5 px-3 rounded-lg text-sm font-bold transition-all duration-200 
                    {{ request()->routeIs('slacs.profile.index') 
                            ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-sm' 
                            : 'text-gray-500 hover:bg-gray-200/60 dark:hover:bg-gray-700/50 hover:text-gray-700 dark:hover:text-white' }}">
                        <flux:icon.user class="w-6 h-6 md:w-5 md:h-5 md:mr-2" />
                        <span class="hidden md:inline">{{ __('General') }}</span>
                    </a>

                    {{-- Qualification --}}
                    @can('slacs.profile.qualification.view')
                    <a href="{{ route('slacs.profile.qualification', $slacsid) }}" wire:navigate 
                    class="flex flex-1 md:flex-none flex-col md:flex-row items-center justify-center py-2.5 px-3 rounded-lg text-sm font-bold transition-all duration-200 
                    {{ request()->routeIs('slacs.profile.qualification') 
                            ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-sm' 
                            : 'text-gray-500 hover:bg-gray-200/60 dark:hover:bg-gray-700/50 hover:text-gray-700 dark:hover:text-white' }}">
                        <flux:icon.academic-cap class="w-6 h-6 md:w-5 md:h-5 md:mr-2" />
                        <span class="hidden md:inline">{{ __('Qualification') }}</span>
                    </a>
                    @endcan

                    {{-- Employment --}}
                    @can('slacs.profile.employment.view')
                    <a href="{{ route('slacs.profile.employment', $slacsid) }}" wire:navigate 
                    class="flex flex-1 md:flex-none flex-col md:flex-row items-center justify-center py-2.5 px-3 rounded-lg text-sm font-bold transition-all duration-200 
                    {{ request()->routeIs('slacs.profile.employment') 
                            ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-sm' 
                            : 'text-gray-500 hover:bg-gray-200/60 dark:hover:bg-gray-700/50 hover:text-gray-700 dark:hover:text-white' }}">
                        <flux:icon.briefcase class="w-6 h-6 md:w-5 md:h-5 md:mr-2" />
                        <span class="hidden md:inline">{{ __('Employment') }}</span>
                    </a>
                    @endcan

                    {{-- W&OP --}}
                    @can('slacs.profile.pension-and-payment.view')
                    <a href="{{ route('slacs.profile.pension-and-payment', $slacsid) }}" wire:navigate 
                    class="flex flex-1 md:flex-none flex-col md:flex-row items-center justify-center py-2.5 px-3 rounded-lg text-sm font-bold transition-all duration-200 
                    {{ request()->routeIs('slacs.profile.pension-and-payment') 
                            ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-sm' 
                            : 'text-gray-500 hover:bg-gray-200/60 dark:hover:bg-gray-700/50 hover:text-gray-700 dark:hover:text-white' }}">
                        <flux:icon.credit-card class="w-6 h-6 md:w-5 md:h-5 md:mr-2" />
                        <span class="hidden md:inline">{{ __('W&OP') }}</span>
                    </a>
                    @endcan

                    {{-- Family --}}
                    @can('slacs.profile.family.view')
                    <a href="{{ route('slacs.profile.family', $slacsid) }}" wire:navigate 
                    class="flex flex-1 md:flex-none flex-col md:flex-row items-center justify-center py-2.5 px-3 rounded-lg text-sm font-bold transition-all duration-200 
                    {{ request()->routeIs('slacs.profile.family') 
                            ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-sm' 
                            : 'text-gray-500 hover:bg-gray-200/60 dark:hover:bg-gray-700/50 hover:text-gray-700 dark:hover:text-white' }}">
                        <flux:icon.users class="w-6 h-6 md:w-5 md:h-5 md:mr-2" />
                        <span class="hidden md:inline">{{ __('Family') }}</span>
                    </a>
                    @endcan

                    {{-- Edit Request --}}
                    @can('slacs.profile.edit-request.view')
                    <a href="{{ route('slacs.profile.edit-request', $slacsid) }}" wire:navigate 
                    class="flex flex-1 md:flex-none flex-col md:flex-row items-center justify-center py-2.5 px-3 rounded-lg text-sm font-bold transition-all duration-200 
                    {{ request()->routeIs('slacs.profile.edit-request') 
                            ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-sm' 
                            : 'text-gray-500 hover:bg-gray-200/60 dark:hover:bg-gray-700/50 hover:text-gray-700 dark:hover:text-white' }}">
                        <flux:icon.pencil-square class="w-6 h-6 md:w-5 md:h-5 md:mr-2" />
                        <span class="hidden md:inline text-center">{{ __('Edit Request') }}</span>
                    </a>
                    @endcan
                </nav>
            </div>
        </div>
    </div>

    {{-- 3. Main Grid --}}
    <div class="mt-4 md:mt-6 flex flex-col lg:flex-row gap-6 md:px-0">
        
        {{-- INTRO SECTION: Top on Mobile (2 columns), Right on Desktop --}}
        <div class="order-1 lg:order-2 w-full lg:w-[320px] space-y-4">
            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <h2 class="text-lg font-bold mb-4 dark:text-white">Intro</h2>
                <div class="grid grid-cols-2 lg:grid-cols-1 gap-y-6 gap-x-4">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">National ID</p>
                        <p class="text-sm font-mono font-medium text-gray-900 dark:text-gray-200">{{ $slacs->nic }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Employee ID</p>
                        <p class="text-sm font-mono font-medium text-gray-900 dark:text-gray-200">{{ $slacs->people_id }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">W&OP No</p>
                        <p class="text-sm font-mono font-medium text-gray-900 dark:text-gray-200">{{ $slacs->appointment->w_op_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Pay Sheet No</p>
                        <p class="text-sm font-mono font-medium text-gray-900 dark:text-gray-200">{{ $slacs->appointment->pay_sheet_no ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT AREA --}}
        <div class="order-2 lg:order-1 flex-1 min-w-0">
            
            {{-- RESTORED: Verify and Confirm Alerts (Original Logic) --}}
            @if (!$slacs->appointment?->is_confirmed && !$slacs->appointment?->is_verified)
                @can('slacs.profile.verify')
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
            @elseif(!$slacs->appointment?->is_confirmed && $slacs->appointment?->is_verified)
                @can('slacs.profile.confirm')
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
            @if ($slacs->profileEditRequests->where('status', 1)->isNotEmpty())
                @can('slacs.profile.edit-request.response')
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
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-3 shadow-sm">
                {{ $slot }}
            </div>
        </div>
    </div>

    @can('slacs.profile.edit-request.create')
    <!-- Edit Profile Modal -->
    <flux:modal name="edit-profile" class="md:w-150">
        <livewire:slacs.complaint-form :peopleId="$slacs->people_id" />
    </flux:modal>
    @endcan

    @can('slacs.profile.confirm')
    <!-- Confirm Profile Modal -->
    <flux:modal name="confirm-profile" class="md:w-96">
        <div class="space-y-6">
            <livewire:employees.confirm-profile-form :peopleId="$slacs->people_id" />
        </div>
    </flux:modal>
    @endcan

    @can('slacs.profile.verify')
    <!-- Verify Profile Modal -->
    <flux:modal name="verify-profile" class="md:w-96">
        <div class="space-y-6">
            <livewire:employees.verify-profile-form :peopleId="$slacs->people_id" />
        </div>
    </flux:modal>
    @endcan

    @can('slacs.profile.edit-request.response')
    <flux:modal name="edit-profile-request-response" class="md:w-150">
        <livewire:employees.edit-profile-requst-response-form :peopleId="$slacs->people_id" />
    </flux:modal>
    @endcan
</div>