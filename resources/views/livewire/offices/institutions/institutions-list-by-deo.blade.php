<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Contextual Header --}}
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <flux:heading size="xl" level="1" class="font-black! tracking-tight text-slate-900 dark:text-white">
                    {{ __('Institutions') }}
                </flux:heading>
                <flux:badge variant="ghost" size="sm" class="bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold border-indigo-100 dark:border-indigo-500/20">
                    {{ $division->name ?? 'Division' }}
                </flux:badge>
            </div>
            <flux:subheading size="lg">
                {{ __('Administrative oversight for all service points within this division.') }}
            </flux:subheading>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-[10px] font-bold uppercase text-slate-500 tracking-widest">Total Schools</p>
                <p class="text-xl font-black text-slate-900 dark:text-white">{{ $institutions->total() }}</p>
            </div>
        </div>
    </header>

    <flux:separator variant="subtle" />

    {{-- Cards Stack --}}
    <div class="space-y-4">
        @forelse ($institutions as $key => $institution)
            <div class="group bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl p-2 pr-6 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 hover:border-indigo-500/30">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                    
                    {{-- Identity Block --}}
                    <div class="flex items-center gap-4 p-3 lg:w-96 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
                        <div class="h-14 w-14 shrink-0 flex items-center justify-center rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 shadow-sm text-slate-500 group-hover:text-indigo-500 transition-colors">
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
                            
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white truncate">
                                @can('office.institution.profile.overview.view')
                                    <a href="{{ route('offices.institutions.profile.overview', $institution->id) }}" class="hover:text-indigo-600 transition-colors">
                                        {{ $institution->name }}
                                    </a>
                                @else
                                    {{ $institution->name }}
                                @endcan
                            </h3>
                        </div>
                    </div>

                    {{-- Localized Info Grid --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6 px-4 lg:px-0">
                        {{-- Location --}}
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2 text-slate-500 mb-1">
                                <flux:icon.map-pin variant="micro" class="size-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Address</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-500 line-clamp-1 italic">
                                {{ $institution->address }}
                            </p>
                        </div>

                        {{-- Parent Chain --}}
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2 text-slate-500 mb-1">
                                <flux:icon.link variant="micro" class="size-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Parent Org</span>
                            </div>
                            <div class="flex items-center gap-1 text-[11px] font-bold">
                                <span class="text-slate-700 dark:text-slate-200 uppercase">{{ $institution->zonalEducationOffice->short_name ?? 'N/A' }}</span>
                                <flux:icon.chevron-right variant="micro" class="size-2.5 text-slate-300" />
                                <span class="text-indigo-500 uppercase">{{ $division->short_name ?? 'DIV' }}</span>
                            </div>
                        </div>

                        {{-- Status & Quick Contact --}}
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="h-2 w-2 rounded-full {{ $institution->active_status ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : 'bg-rose-500' }}"></span>
                                <span class="text-[10px] font-bold uppercase tracking-widest {{ $institution->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $institution->active_status ? 'Active' : 'Offline' }}
                                </span>
                            </div>
                            <p class="text-xs font-semibold text-slate-500">{{ $institution->phone ?? 'No Phone' }}</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pl-4 lg:pl-0">
                        @can('office.institution.profile.overview.view')
                            <flux:button href="{{ route('offices.institutions.profile.overview', $institution->id) }}" variant="filled" size="sm" class="rounded-xl">
                                View
                            </flux:button>
                        @endcan

                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-vertical" square />
                            <flux:menu>
                                @can('office.institution.update')
                                    <flux:menu.item icon="pencil-square">Update Details</flux:menu.item>
                                @endcan
                                <flux:menu.item :href="route('offices.institutions.profile.student-enrollment', $institution->id)" icon="user-group" wire:navigate>{{ __('Student Population') }}</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item variant="danger" icon="archive-box">Deactivate</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 bg-slate-50 dark:bg-slate-800/50 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                <flux:icon.magnifying-glass class="size-12 text-slate-300 mb-4" />
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Institutions found in {{ $division->name }}</h3>
                <p class="text-slate-500 text-sm">New records can be added through the divisional registration portal.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $institutions->links() }}
    </div>
</div>