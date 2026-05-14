<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('School Overview') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage School Overview and settings') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-institutions.institution-layout :institutionId="$id">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Students Card --}}
            <div
                class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-6 shadow-sm border border-blue-200/50 dark:border-blue-700/50 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                <!-- Background decoration -->
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-blue-200 dark:bg-blue-700 rounded-full -translate-y-10 translate-x-10 opacity-50 group-hover:opacity-70 transition-opacity">
                </div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-blue-500 rounded-xl shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $studentCount }}</div>
                                <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">Students</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <span
                            class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-800/50 px-2 py-1 rounded-full">
                            Total enrolled
                        </span>
                        <button
                            class="flex items-center space-x-1 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors group/link">
                            <span class="text-sm font-semibold">View</span>
                            <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Staff Card --}}
            <div
                class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-6 shadow-sm border border-green-200/50 dark:border-green-700/50 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                <!-- Background decoration -->
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-green-200 dark:bg-green-700 rounded-full -translate-y-10 translate-x-10 opacity-50 group-hover:opacity-70 transition-opacity">
                </div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-green-500 rounded-xl shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $staffCount }}</div>
                                <div class="text-sm font-semibold text-green-600 dark:text-green-400">Staff</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <span
                            class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-800/50 px-2 py-1 rounded-full">
                            Total staff
                        </span>
                        <a href="{{ route('institutions.profile.staff', $id) }}"
                            class="flex items-center space-x-1 text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 transition-colors group/link">
                            <span class="text-sm font-semibold">View</span>
                            <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Parents Card --}}
            <div
                class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 p-6 shadow-sm border border-purple-200/50 dark:border-purple-700/50 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                <!-- Background decoration -->
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-purple-200 dark:bg-purple-700 rounded-full -translate-y-10 translate-x-10 opacity-50 group-hover:opacity-70 transition-opacity">
                </div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-purple-500 rounded-xl shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $parentCount }}</div>
                                <div class="text-sm font-semibold text-purple-600 dark:text-purple-400">
                                    Parents/Guardians</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <span
                            class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-800/50 px-2 py-1 rounded-full">
                            Total guardians
                        </span>
                        <button
                            class="flex items-center space-x-1 text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 transition-colors group/link">
                            <span class="text-sm font-semibold">View</span>
                            <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-institutions.institution-layout>

</section>