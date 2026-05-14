<div class="space-y-2 mb-6">
    @if($institution)
    <!-- Active Institution Card -->
    @if($institution->active_status)
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md overflow-hidden">
        <div class="p-4 sm:p-6">
            <!-- Mobile: Stacked Layout -->
            <div class="sm:hidden">
                <!-- Header Mobile -->
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                ● Active
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $institution->institutionCategory->institution_category_name ?? 'N/A' }}</span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1 capitalized">
                            {{ ucwords($institution->name) ?? 'N/A' }}
                        </h2>
                        <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">{{ ucwords(strtolower($institution->address)) ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Logo & Quick Info Mobile -->
                <div class="flex items-start space-x-4 mb-4">
                    <div class="relative flex-shrink-0">
                        <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 p-3 border border-blue-100 dark:border-blue-800/50">
                            <div class="w-full h-full flex items-center justify-center">
                                <img src="{{ $institution->logo_url }}" class="w-full h-full object-contain">
                            </div>
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Census No.</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) ?? 'N/A'}}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Established</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $institution->established_year ?? 'N/A'}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop: Flex Layout -->
            <div class="hidden sm:block">
                <div class="flex items-start justify-between">
                    <!-- Institution Info -->
                    <div class="flex items-start space-x-6">
                        <!-- Logo Container -->
                        <div class="relative">
                            <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 p-2 border border-blue-100 dark:border-blue-800/50">
                                <div class="w-full h-full flex items-center justify-center">
                                    <img src="{{ $institution->logo_url }}" class="w-full h-full object-contain">
                                </div>
                            </div>

                            <!-- Active Status Badge -->
                            <div class="absolute -top-2 -right-2">
                                <div class="flex items-center space-x-1 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold shadow-sm">
                                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                    <span>ACTIVE</span>
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="flex-1">
                            <div class="mb-1">
                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{$institution->institutionCategory->institution_category_name ?? 'N/A'}}</span>
                            </div>

                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                                {{ucwords($institution->name) ?? 'N/A'}}
                            </h2>

                            <div class="flex flex-wrap gap-6 mb-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Census No.</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) ?? 'N/A'}}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Institution ID</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{$institution->workplace_id ?? 'N/A'}}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Established</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $institution->established_year ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="flex items-center space-x-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-600 dark:text-gray-300">{{ ucwords(strtolower($institution->address ?? '')) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Panel Desktop -->
                    <div class="flex flex-col items-end space-y-3">
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Last updated</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{optional($institution->updated_at)->format('M d, Y') ?? 'N/A'}}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Status Desktop -->
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm text-green-600 dark:text-green-400">All systems operational</span>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-medium">email:</span> {{$institution->email ?? 'N/A'}}
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium">Contact:</span> {{$institution->phone ?? 'N/A'}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Inactive Institution Card -->
    <!-- Inactive Institution Card -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm opacity-90">
        <div class="relative">
            <!-- Suspended Banner -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 text-sm font-medium">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs sm:text-sm">ACCOUNT SUSPENDED</span>
                    </div>

                    <span class="text-xs opacity-90 hidden sm:block">
                        Suspended since: {{ optional($institution->updated_at)->format('M d, Y') ?? 'N/A' }}
                    </span>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <!-- Mobile Layout -->
                <div class="sm:hidden">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    ● Suspended
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $institution->institutionCategory->institution_category_name ?? 'N/A' }}
                                </span>
                            </div>

                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1 opacity-70">
                                {{ ucwords($institution->name) ?? 'N/A' }}
                            </h2>

                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                {{ $institution->short_name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <!-- Mobile Logo & Stats -->
                    <div class="flex items-start space-x-4 mb-4">
                        <div class="relative flex-shrink-0">
                            <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-gray-50 to-white
                            dark:from-gray-800 dark:to-gray-900 p-3 border border-gray-200
                            dark:border-gray-700 grayscale opacity-70">

                                <div class="w-full h-full flex items-center justify-center">
                                    <img src="{{ $institution->logo_url }}"
                                        class="w-full h-full object-contain opacity-70">
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 opacity-70">
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Census No.</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Established</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $institution->established_year ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-100 dark:border-red-800/30">
                        <div class="flex items-start space-x-2">
                            <svg class="w-4 h-4 text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-xs text-red-700 dark:text-red-400">
                                This institution's account is suspended. Contact admin to reactivate.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Desktop Layout -->
                <div class="hidden sm:block">
                    <div class="flex items-start space-x-6">
                        <!-- Logo -->
                        <div class="relative">
                            <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 p-4 border border-gray-200 dark:border-gray-700 grayscale opacity-70">
                                <img src="{{ $institution->logo_url }}" class="w-full h-full object-contain opacity-70">
                            </div>

                            <!-- Badge -->
                            <div class="absolute -top-2 -right-2">
                                <div class="flex items-center space-x-1 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold shadow-sm">
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                    <span>SUSPENDED</span>
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="flex-1 opacity-70">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $institution->institutionCategory->institution_category_name ?? 'N/A' }}
                            </span>

                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                                {{ ucwords($institution->name) }}
                            </h2>

                            <div class="flex flex-wrap gap-6 mb-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Census No.</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Institution ID</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $institution->workplace_id ?? 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Established</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $institution->established_year ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>

                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ ucwords(strtolower($institution->address)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Suspension Details Footer -->
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-start space-x-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-100 dark:border-red-800/30">
                            <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>

                            <div class="flex-1">
                                <p class="text-sm font-medium text-red-800 dark:text-red-300 mb-1">
                                    Account Suspension Notice
                                </p>

                                <p class="text-sm text-red-700 dark:text-red-400">
                                    This institution is suspended. Contact the system administrator for reactivation.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @endif
    @endif
</div>