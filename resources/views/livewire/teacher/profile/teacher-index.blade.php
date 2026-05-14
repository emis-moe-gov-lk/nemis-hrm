<section class="w-full">

    <x-teachers.teacher-profile-layout :teacherid="$id">
        <div>
            <div class="antialiased min-h-screen">

                {{-- Main Dual-Mode Card (Read-Only Container) --}}
                <div class="bg-white dark:bg-gray-800 p-2 rounded-b-lg">

                    {{-- Main Content Grid --}}
                    <div class="space-y-6">

                        {{-- 1. Personal & Socio-Cultural Details --}}
                        <livewire:employees.personal-cultural :peopleId="$teacher->people_id" :canEdit="auth()->user()->can('teacher.profile.personal-cultural.update')" />

                        {{-- 2. Health information --}}
                        <livewire:employees.health-information :peopleId="$teacher->people_id" :canEdit="auth()->user()->can('teacher.profile.health.update')" />

                        {{-- 3. Contact Information --}}
                        <livewire:employees.contact-information :peopleId="$teacher->people_id" :canEdit="auth()->user()->can('teacher.profile.contact-information.update')" />

                        {{-- 4. Location Information --}}
                        <livewire:employees.location-information :peopleId="$teacher->people_id" :canEdit="auth()->user()->can('teacher.profile.location-information.update')" />

                        {{-- 4. Temporary Location Information --}}
                        <livewire:employees.temporary-location-information :peopleId="$teacher->people_id" :canEdit="auth()->user()->can('teacher.profile.temporary-location-information.update')" />

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
                                            {{ $teacher->nic_hash }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Copy Button for System Key --}}
                                <flux:button 
                                    variant="ghost" 
                                    size="sm" 
                                    icon="clipboard" 
                                    class="opacity-0 group-hover:opacity-100 transition-opacity"
                                    x-on:click="window.navigator.clipboard.writeText('{{ $teacher->nic_hash }}'); Flux.toast({ variant: 'success', text: 'Hash copied to clipboard' })"
                                />
                            </div>
                            
                            <p class="mt-2 px-1 text-[9px] text-gray-400 italic">
                                * This hash is a unique encrypted key used for system-wide identification.
                            </p>
                        </section>

                    </div>

                </div>
            </div>
        </div>

    </x-teachers.teacher-profile-layout>
</section>
