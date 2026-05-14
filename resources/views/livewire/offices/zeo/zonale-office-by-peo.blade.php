<div class="max-w-7xl mx-left px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Header Section --}}
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                    {{ __('Zonal Offices') }}
                </flux:heading>
                <flux:badge variant="ghost" size="sm" class="bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 font-bold border-amber-100 dark:border-amber-500/20 uppercase tracking-tighter">
                    {{ $province->name }} Province
                </flux:badge>
            </div>
            <flux:subheading size="lg">
                {{ __('Administrative hubs governing divisional clusters within') }} {{ $province->short_name }}.
            </flux:subheading>
        </div>

        <div class="flex items-center gap-3">
            @can('office.zeo.create')
                <flux:button href="{{ route('offices.zeo.create') }}" variant="primary" icon="plus" wire:navigate>
                    New Zonal Office
                </flux:button>
            @endcan
        </div>
    </header>

    <flux:separator variant="subtle" />

    {{-- Zonal Office List --}}
    <div class="space-y-4">
        @forelse ($zonalEducationOffices as $data)
            <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-2 pr-6 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 hover:border-indigo-500/30">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                    
                    {{-- Identity Block --}}
                    <div class="flex items-center gap-4 p-3 lg:w-80 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
                        <div class="relative">
                            <div class="h-14 w-14 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700">
                                <flux:icon.building-office class="size-8 text-slate-500 dark:text-slate-400" />
                            </div>
                            
                            {{-- Status Pulse --}}
                            <div class="absolute -top-1.5 -right-1.5">
                                <div class="w-3 h-3 rounded-full {{ $data->active_status ? 'bg-indigo-500 shadow-[0_0_8px_rgba(79,70,229,0.5)]' : 'bg-slate-300' }}"></div>
                            </div>
                        </div>
                        
                        <div class="overflow-hidden">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                WPID: {{ $data->workplace_id }}
                            </p>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white wrap">
                                <flux:link href="{{ route('offices.deo.by-zone', $data->id) }}" variant="ghost" class="!p-0 hover:text-indigo-600">
                                    {{ $data->name }}
                                </flux:link>
                            </h3>
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 lg:px-0">
                        {{-- Address --}}
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2 text-slate-400 mb-1">
                                <flux:icon.map-pin variant="micro" class="size-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Zone HQ</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-1 italic">
                                {{ $data->address }}
                            </p>
                        </div>

                        {{-- Hierarchy --}}
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2 text-slate-400 mb-1">
                                <flux:icon.globe-asia-australia variant="micro" class="size-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Provincial Link</span>
                            </div>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                {{ $data->provincialEducationOffice->short_name }} Office
                            </p>
                        </div>

                        {{-- Status --}}
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="h-2 w-2 rounded-full {{ $data->active_status ? 'bg-indigo-500 shadow-[0_0_8px_rgba(79,70,229,0.5)]' : 'bg-slate-300' }}"></span>
                                <span class="text-[10px] font-bold uppercase tracking-widest {{ $data->active_status ? 'text-indigo-600' : 'text-slate-500' }}">
                                    {{ $data->active_status ? 'Verified Hub' : 'Inactive' }}
                                </span>
                            </div>
                            <p class="text-xs font-mono text-slate-500">{{ $data->phone ?? 'No Direct Line' }}</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pl-4 lg:pl-0">
                        @can('office.zeo.profile.overview.view')
                            <flux:button href="{{ route('offices.zeo.profile.overview', $data->id) }}" variant="filled" size="sm" class="rounded-xl">
                                View
                            </flux:button>
                        @endcan

                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" square />
                            <flux:menu>
                                @can('office.zeo.update')
                                    <flux:menu.item icon="pencil-square">Edit Zonal Profile</flux:menu.item>
                                @endcan
                                <flux:menu.item icon="chart-bar">View Statistics</flux:menu.item>
                                <flux:menu.separator />
                                @can('office.zeo.delete')
                                    <flux:menu.item variant="danger" icon="trash">Remove Record</flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-24 bg-slate-50 dark:bg-slate-800/40 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
                <flux:icon.map class="size-16 text-slate-200 dark:text-slate-700 mb-4" />
                <h3 class="text-xl font-black text-slate-900 dark:text-white">No Zonal Hubs Found</h3>
                <p class="text-slate-500 text-sm max-w-xs text-center">We couldn't find any zonal offices under the {{ $province->name }} province.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $zonalEducationOffices->links() }}
    </div>
</div>