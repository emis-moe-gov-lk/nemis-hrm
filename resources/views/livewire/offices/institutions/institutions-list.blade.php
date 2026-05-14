<div class="max-w-7xl mx-left px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    {{-- Header Section --}}
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <flux:heading size="xl" level="1" class="font-black! tracking-tight text-slate-900 dark:text-white">
                    {{ __('Institutions') }}
                </flux:heading>
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 text-xs font-bold">
                    {{ $institutions->total() }} Total
                </span>
            </div>
            <flux:subheading size="lg">
                {{ __('Manage school profiles, census data, and localized accounts') }}
            </flux:subheading>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-80">
            <flux:input 
                wire:model.live.debounce.400ms="search" 
                placeholder="Search by name or census no..." 
                icon="magnifying-glass" 
                clearable
            />
        </div>
    </header>

    <flux:separator variant="subtle" />

    {{-- Cards Stack --}}
    <div class="space-y-4">
        @forelse ($institutions as $key => $institution)
        <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-4xl p-2 pr-6 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 hover:border-indigo-500/30">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6">

                {{-- Identity Block: Census & Name --}}
                <div class="flex items-center gap-4 p-3 lg:w-96 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
                    <div class="h-14 w-14 shrink-0 flex items-center justify-center rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm text-slate-400 group-hover:text-indigo-500 transition-colors">
                        <flux:icon.academic-cap variant="outline" class="size-8" />
                    </div>

                    <div class="overflow-hidden">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">
                                Census: {{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">#{{ $institutions->firstItem() + $key }}</span>
                        </div>

                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white wrap-break-word">
                            @can('office.institution.profile.overview.view')
                            <a href="{{ route('institutions.profile.overview', $institution->id) }}" class="hover:text-indigo-600 transition-colors">
                                {{ $institution->name }}
                            </a>
                            @else
                            {{ $institution->name }}
                            @endcan
                        </h3>
                    </div>
                </div>

                {{-- Data Grid --}}
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6 px-4 lg:px-0">
                    {{-- Affiliation Path --}}
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center gap-2 text-slate-400 mb-1">
                            <flux:icon.command-line variant="micro" class="size-3.5" />
                            <span class="text-[10px] font-bold uppercase tracking-wider">Hierarchy</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $institution->zonalEducationOffice->short_name ?? 'N/A' }}</span>
                            <flux:icon.chevron-right variant="micro" class="size-3 text-slate-300" />
                            <span class="text-slate-500">{{ $institution->divisionalEducationOffice->short_name ?? 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center gap-2 text-slate-400 mb-1">
                            <flux:icon.map-pin variant="micro" class="size-3.5" />
                            <span class="text-[10px] font-bold uppercase tracking-wider">Location</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-1 italic">
                            {{ $institution->address }}
                        </p>
                    </div>

                    {{-- Status & Contact --}}
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="h-2 w-2 rounded-full {{ $institution->active_status ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest {{ $institution->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $institution->active_status ? 'Operative' : 'Inactive' }}
                            </span>
                        </div>
                        <p class="text-xs font-mono text-slate-500">{{ $institution->phone ?? 'No Contact' }}</p>
                    </div>
                </div>

                {{-- Action Group --}}
                <div class="flex items-center gap-2 pl-4 lg:pl-0">
                    @can('office.institution.profile.overview.view')
                    <flux:button href="{{ route('institutions.profile.overview', $institution->id) }}" variant="filled" size="sm" class="rounded-xl shadow-sm">
                        View
                    </flux:button>
                    @endcan

                    <flux:dropdown>
                        <flux:button size="sm" variant="ghost" icon="ellipsis-vertical" square />
                        <flux:menu>
                            @can('office.institution.update')
                            <flux:menu.item icon="pencil-square">Edit Profile</flux:menu.item>
                            @endcan
                            <flux:menu.item icon="printer">Export Data</flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item variant="danger" icon="trash">Archive</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-20 bg-slate-50 dark:bg-slate-800/50 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
            <flux:icon.academic-cap class="size-12 text-slate-300 mb-4" />
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Institutions Registered</h3>
            <p class="text-slate-500 text-sm">There are no school records matching this criteria.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination Section --}}
    <div class="mt-8 px-2">
        {{ $institutions->links() }}
    </div>
</div>