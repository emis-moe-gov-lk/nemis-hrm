<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('National School') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Manage National school profile and account') }}
        </flux:subheading>
        <flux:separator variant="subtle" />

        <div class="my-4 w-full">
            <div
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6 items-center w-full">
                {{-- Province --}}
                <flux:select wire:model.live="province" class="w-full">
                    <flux:select.option value="">All provinces</flux:select.option>
                    @foreach ($provinceOption as $prov)
                    <flux:select.option value="{{ $prov->workplace_id }}">
                        {{ $prov->short_name }}
                    </flux:select.option>
                    @endforeach
                </flux:select>

                {{-- Zonal --}}
                <flux:select
                    wire:model.live="zone"
                    class="w-full"
                    :disabled="empty($zoneOption)">
                    <flux:select.option value="">All zonal offices</flux:select.option>
                    @foreach ($zoneOption as $zone)
                    <flux:select.option value="{{ $zone->workplace_id }}">
                        {{ $zone->short_name }}
                    </flux:select.option>
                    @endforeach
                </flux:select>

                {{-- Divisional --}}
                <flux:select
                    wire:model.live="division"
                    class="w-full"
                    :disabled="empty($divisionOption)">
                    <flux:select.option value="">All divisional offices</flux:select.option>
                    @foreach ($divisionOption as $division)
                    <flux:select.option value="{{ $division->workplace_id }}">
                        {{ $division->short_name }}
                    </flux:select.option>
                    @endforeach
                </flux:select>

                {{-- Status --}}
                <flux:select
                    wire:model.live="status"
                    class="w-full">
                    <flux:select.option value="">Any status</flux:select.option>
                    @foreach ($statusOption as $status)
                    <flux:select.option value="{{ $status->id }}">
                        {{ $status->name }}
                    </flux:select.option>
                    @endforeach
                </flux:select>

                {{-- Search --}}
                <flux:input
                    wire:model.live.debounce.400ms="query"
                    class="w-full"
                    placeholder="Search by name or census no..." />
            </div>
        </div>


        <div class="flex items-center space-x-3 mb-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Total: {{ $institutions->total() }} Institution
            </span>
        </div>
        <div class="overflow-x-auto rounded-lg shadow-md">
            @forelse ($institutions as $key => $institution)
            <a href="{{ route('institutions.basic.view', $institution->id) }}">
                <div class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-3 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50/50 dark:hover:bg-slate-800/50 cursor-pointer m-2">
                    <div class="flex items-center gap-3">
                        <!-- Serial Number -->
                        <div class="flex-shrink-0">
                            <div class="h-10 w-12 rounded-md bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-300">
                                    {{ $institutions->firstItem() + $key }}
                                </span>
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <img
                                    src="{{ asset('storage/images/institution/'. $institution->logo) }}"
                                    alt="{{ $institution->name }}"
                                    class="w-full h-full object-cover" />
                            </div>
                        </div>

                        <!-- Main Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                    {{ $institution->name }}
                                </h3>
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $institution->active_status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    {{ $institution->active_status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    Census: {{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    Contact: {{ $institution->phone ?: 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <!-- Education Offices -->
                        <div class="flex-shrink-0 hidden md:flex items-center gap-2">
                            <div class="px-2 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                                <span class="text-xs font-medium text-blue-700 dark:text-blue-300">ZEO:</span>
                                <span class="text-xs font-semibold text-blue-800 dark:text-blue-200 ml-1">
                                    {{ $institution->zonalEducationOffice->short_name ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="px-2 py-1 bg-purple-50 dark:bg-purple-900/20 rounded-md">
                                <span class="text-xs font-medium text-purple-700 dark:text-purple-300">DEO:</span>
                                <span class="text-xs font-semibold text-purple-800 dark:text-purple-200 ml-1">
                                    {{ $institution->divisionalEducationOffice->short_name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <!-- Address (Truncated) -->
                        <div class="flex-shrink-0 hidden lg:block max-w-[200px]">
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-slate-400 dark:text-slate-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>

                                <span class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                    {{ $institution->address }}
                                </span>
                            </div>
                        </div>

                        <!-- Arrow Indicator -->
                        <div class="flex-shrink-0">
                            <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-400 dark:group-hover:text-slate-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-3 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50/50 dark:hover:bg-slate-800/50 cursor-pointer m-2 text-center">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                    No institutions found.
                </h3>
            </div>
            @endforelse
        </div>


        <div class="mt-4 mx-2">
            {{ $institutions->links() }}
        </div>
    </div>
</div>