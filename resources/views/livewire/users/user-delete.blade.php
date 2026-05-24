<div class="max-w-7xl mx-auto pb-10 px-0 md:px-4">
    {{-- 1. Profile Header Section --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border-x border-b border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        {{-- Cover Photo --}}
        <div class="h-32 md:h-52 bg-linear-to-r from-blue-600 to-indigo-600 relative">
            <div class="absolute inset-0 opacity-100 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>

        {{-- Profile Info Area --}}
        <div class="px-4 md:px-8 pb-2">
            <div class="relative flex flex-col md:flex-row items-center md:items-end -mt-12 md:-mt-16 mb-4 gap-4">
                {{-- Profile Image --}}
                <div class="relative">
                    @if ($myprofile->gender_id == 'G02')
                        <img src="{{ asset('images/profile_f.png') }}" alt="Profile" class="w-28 h-28 md:w-40 md:h-40 rounded-full border-4 border-white dark:border-gray-900 shadow-lg object-cover bg-white" />
                    @else
                        <img src="{{ asset('images/profile_m.png') }}" alt="Profile" class="w-28 h-28 md:w-40 md:h-40 rounded-full border-4 border-white dark:border-gray-900 shadow-lg object-cover bg-white" />
                    @endif
                    <span class="absolute bottom-2 right-2 md:bottom-3 md:right-3 block h-4 w-4 md:h-5 md:w-5 rounded-full border-2 border-white dark:border-gray-900 {{ $myprofile->appointment->is_confirmed ? 'bg-green-500' : 'bg-red-500' }}"></span>
                </div>

                <div class="flex-1 text-center md:text-left md:mb-4">
                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tighter">
                        {{ $myprofile->title->title_name }} {{ $myprofile->name_with_initials }}
                    </h1>
                    
                    <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-3 mt-1">
                        <p class="text-indigo-600 dark:text-indigo-400 font-bold text-xs md:text-sm uppercase tracking-widest">
                            {{ $myprofile->currentAppointment->service->service_name }}
                        </p>

                        <span class="hidden md:block w-1 h-1 bg-slate-300 rounded-full"></span>

                        <p class="text-slate-500 dark:text-slate-500 font-medium text-sm md:text-base flex items-center justify-center md:justify-start gap-1.5">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $myprofile->currentAppointment->workplace->office()->name ?? 'No Workplace Assigned' }}
                        </p>
                    </div>
                </div>

                {{-- Header Actions with Original Logos --}}
                <div class="flex gap-2 mb-4 w-full md:w-auto justify-center px-4 md:px-0">
                    @can('user.delete') 
                    <flux:modal.trigger name="delete-profile">
                        <button class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 bg-white dark:bg-red-800 border border-red-300 dark:border-red-600 rounded-lg text-xs md:text-sm font-medium text-red-700 dark:text-red-200 hover:bg-red-50 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            {{ __('Delete User') }}
                        </button>
                    </flux:modal.trigger>
                    @endcan
                </div>
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
                        <p class="text-sm font-mono font-medium text-gray-900 dark:text-gray-200">{{ $myprofile->nic }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Employee ID</p>
                        <p class="text-sm font-mono font-medium text-gray-900 dark:text-gray-200">{{ $myprofile->people_id }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">W&OP No</p>
                        <p class="text-sm font-mono font-medium text-gray-900 dark:text-gray-200">{{ $myprofile->appointment->w_op_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Pay Sheet No</p>
                        <p class="text-sm font-mono font-medium text-gray-900 dark:text-gray-200">{{ $myprofile->appointment->pay_sheet_no ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT AREA --}}
        <div class="order-2 lg:order-1 flex-1 min-w-0">
            {{-- SLOT --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-3 shadow-sm space-y-6">
                {{-- 1. Personal & Socio-Cultural Details --}}
                <livewire:employees.personal-cultural :peopleId="$myprofile->people_id" :canEdit="false" />

                {{-- 2. Health information --}}
                <livewire:employees.health-information :peopleId="$myprofile->people_id" :canEdit="false" />

                {{-- 3. Contact Information --}}
                <livewire:employees.contact-information :peopleId="$myprofile->people_id" :canEdit="false" />

                {{-- 4. Location Information --}}
                <livewire:employees.location-information :peopleId="$myprofile->people_id" :canEdit="false" />

                {{-- 5. Temporary Location Information --}}
                <livewire:employees.temporary-location-information :peopleId="$myprofile->people_id" :canEdit="false" />

                {{-- 6. Educational Qualification --}}
                <livewire:employees.educational-qualification :peopleId="$myprofile->people_id" :canCreate="false" :canDelete="false" />

                {{-- 7. Current Employment --}}
                <livewire:employees.appointment-current-status :peopleId="$myprofile->people_id" :canEdit="false" />

                {{-- 8. First Employment --}}
                <livewire:employees.first-appointment :peopleId="$myprofile->people_id" :canEdit="false" />

                {{-- 9. Previous Services --}}
                <livewire:employees.previous-services-reg :peopleId="$myprofile->people_id" :canCreate="false" :canDelete="false"/>
                    
                {{-- 10. Employment Service History --}}
                <livewire:employees.services-history :peopleId="$myprofile->people_id" :canCreate="false" :canDelete="false" />

                {{-- 11. Pension Payment --}}
                <livewire:employees.pension-payment :peopleId="$myprofile->people_id" :canEdit="false"/>

                {{-- 12. Family Information --}}
                <livewire:employees.family-information :peopleId="$myprofile->people_id" :canCreate="false" :canDelete="false"/>

                <section class="mt-8 pt-6 border-t border-dashed border-gray-200 dark:border-gray-800">
                    <div class="bg-gray-50 dark:bg-gray-900/40 rounded-xl p-4 flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            {{-- Security Shield Icon --}}
                            <div class="p-2 bg-gray-200/50 dark:bg-gray-800 rounded-lg">
                                <flux:icon.shield-check variant="micro" class="size-4 text-gray-500 dark:text-gray-400" />
                            </div>
                            
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">System Identifier</p>
                                <p class="mt-1 text-xs font-mono font-medium text-gray-600 dark:text-gray-400 break-all">
                                    {{ $myprofile->nic_hash }}
                                </p>
                            </div>
                        </div>

                        {{-- Copy Button for System Key --}}
                        <flux:button 
                            variant="ghost" 
                            size="sm" 
                            icon="clipboard" 
                            class="opacity-0 group-hover:opacity-100 transition-opacity"
                            x-on:click="window.navigator.clipboard.writeText('{{ $myprofile->nic_hash }}'); Flux.toast({ variant: 'success', text: 'Hash copied to clipboard' })"
                        />
                    </div>
                    
                    <p class="mt-2 px-1 text-[9px] text-gray-400 italic">
                        * This hash is a unique encrypted key used for system-wide identification.
                    </p>
                </section>

            </div>
        </div>
    </div>

    @can('user.delete')
        <flux:modal name="delete-profile" class="max-w-3xl w-full">
            <div class="space-y-6">
                <form wire:submit.prevent="confirmDelete">
                    @csrf

                    <div class="space-y-6">

                        {{-- Header --}}
                        <div>
                            <flux:heading size="lg" class="text-red-600 text-center font-black">
                                {{ __('Delete User Account') }}
                            </flux:heading>

                            <flux:text class="mt-2 text-balance text-center text-red-600">
                                {{ __('This action will permanently remove the user’s system access. Please read all warnings carefully before proceeding.') }}
                            </flux:text>
                        </div>

                        <flux:separator variant="subtle" />

                        {{-- Critical Warning --}}
                        <x-alert type="error" class="mb-4">
                            <strong>{{ __('Warning:') }}</strong><br>
                            {{ __('Deleting a user is a sensitive operation. Once completed, this action cannot be undone.') }}
                        </x-alert>

                        {{-- User Summary (Read-only) --}}
                        <div class="rounded-xl border border-slate-300 bg-slate-50 p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase">Name</p>
                                    <p class="font-semibold text-slate-800">{{ $myprofile->name_with_initials }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase">NIC</p>
                                    <p class="font-mono text-slate-800">{{ $myprofile->nic }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase">Employee ID</p>
                                    <p class="font-mono text-slate-800">{{ $myprofile->people_id }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase">Current Service</p>
                                    <p class="text-slate-800">
                                        {{ $myprofile->currentAppointment->service->service_name ?? '-' }}
                                    </p>
                                </div>

                                <div class="sm:col-span-2">
                                    <p class="text-xs font-bold text-gray-400 uppercase">Current Workplace</p>
                                    <p class="text-slate-800">
                                        {{ $myprofile->currentAppointment->workplace->office()->name ?? 'No Workplace Assigned' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase">Account Status</p>
                                    <p class="font-semibold {{ $myprofile->active_status ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $myprofile->active_status ? __('Active') : __('Inactive') }}
                                    </p>
                                </div>

                            </div>
                        </div>

                        {{-- Dependency Warning (ONLY when true) --}}
                        <x-alert type="warning">
                            {{ __('This user has linked records (appointments, approvals, or historical data). Permanent deletion is NOT recommended. Please consider deactivation instead.') }}
                        </x-alert>
                       

                        {{-- Delete Reason --}}
                        <div class="space-y-2">
                            <flux:select
                                label="{{ __('Reason for Deletion') }}"
                                wire:model.live="deleteReason"
                                required
                            >
                                <option value="">{{ __('Select a reason') }}</option>
                                <option value="duplicate">{{ __('Duplicate Account') }}</option>
                                <option value="wrong_registration">{{ __('Wrong Registration') }}</option>
                                <option value="transferred">{{ __('Transferred / Retired') }}</option>
                                <option value="system_error">{{ __('System Error') }}</option>
                                <option value="other">{{ __('Other') }}</option>
                            </flux:select>

                            {{-- Additional notes ONLY when reason = other --}}
                            @if($deleteReason === 'other')
                                <flux:textarea
                                    label="{{ __('Additional Notes') }}"
                                    wire:model.live="deleteNote"
                                    placeholder="{{ __('Provide additional details') }}"
                                    rows="3"
                                />
                            @endif
                        </div>

                        {{-- Identity Verification --}}
                        <flux:input
                            label="{{ __('Confirm with Your Password') }}"
                            wire:model.live="password"
                            placeholder="{{ __('Enter your current password') }}"
                            type="password"
                            icon="shield-exclamation"
                            required
                        />

                        {{-- Final Confirmation --}}
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                wire:model.live="confirmDeleteCheck"
                                class="rounded border-slate-300"
                                required
                            >
                            <flux:text class="text-sm text-slate-700">
                                {{ __('I understand this action is irreversible and I take full responsibility.') }}
                            </flux:text>
                        </div>

                        {{-- Alerts --}}
                        <div class="empty:hidden space-y-2">
                            @if (session('error'))
                                <x-alert type="error" dismissible>{{ session('error') }}</x-alert>
                            @endif

                            @if (session('warning'))
                                <x-alert type="warning" dismissible>{{ session('warning') }}</x-alert>
                            @endif

                            @if (session('success'))
                                <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
                            @endif
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center">
                            <flux:spacer />

                            <div class="flex gap-3">
                                <flux:modal.close>
                                    <flux:button variant="ghost" type="button">
                                        {{ __('Cancel') }}
                                    </flux:button>
                                </flux:modal.close>

                                <flux:button
                                    type="submit"
                                    variant="danger"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove>{{ __('Delete User') }}</span>
                                    <span wire:loading>{{ __('Deleting...') }}</span>
                                </flux:button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </flux:modal>
    @endcan
</div>