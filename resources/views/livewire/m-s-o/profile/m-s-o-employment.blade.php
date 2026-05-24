<section class="w-full">

    <x-m-s-o.m-s-o-profile-layout :msoid="$id">
        <div>
            <div class="antialiased min-h-screen">

                {{-- Main Dual-Mode Card (Read-Only Container) --}}
                <div class="bg-white dark:bg-gray-800 p-2 rounded-b-lg">

                    {{-- Main Content Grid --}}
                    <div class="space-y-6">

                        {{-- 1. Current Employment --}}
                        <livewire:employees.appointment-current-status :peopleId="$people->people_id" :canEdit="auth()->user()->can('mso.profile.employment.current-appointment.update')" />

                        {{-- 2. First Employment --}}
                        <livewire:employees.first-appointment :peopleId="$people->people_id"
                            :canEdit="auth()->user()->can('mso.profile.employment.first-appointment.update')" />
                        <livewire:m-s-o.profile.m-s-o-data :peopleId="$people->people_id" />

                        {{-- 3. Previous Services --}}
                        <livewire:employees.previous-services-reg :peopleId="$people->people_id"
                            :canCreate="auth()->user()->can('mso.profile.employment.previous-service.create')"
                            :canDelete="auth()->user()->can('mso.profile.employment.previous-service.delete')" />

                        {{-- 4. Working Place History --}}
                        <livewire:employees.working-place-history :peopleId="$people->people_id"
                            :canCreate="auth()->user()->can('mso.profile.employment.working-place-history.create')"
                            :canDelete="auth()->user()->can('mso.profile.employment.working-place-history.delete')" />

                        {{-- 5. Position History --}}
                        <livewire:employees.position-history :peopleId="$people->people_id"
                            :canCreate="auth()->user()->can('mso.profile.employment.position-history.create')"
                            :canDelete="auth()->user()->can('mso.profile.employment.position-history.delete')" />

                        {{-- 5. Audit Data (NIC Hash) --}}
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
                                            {{ $people->nic_hash }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Copy Button for System Key --}}
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="clipboard"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity"
                                    x-on:click="window.navigator.clipboard.writeText('{{ $people->nic_hash }}'); Flux.toast({ variant: 'success', text: 'Hash copied to clipboard' })" />
                            </div>

                            <p class="mt-2 px-1 text-[9px] text-gray-400 italic">
                                * This hash is a unique encrypted key used for system-wide identification.
                            </p>
                        </section>

                    </div>

                </div>
            </div>
        </div>

    </x-m-s-o.m-s-o-profile-layout>
</section>