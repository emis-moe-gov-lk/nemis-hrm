<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Divisional Offices"
        subtitle="Manage localized administrative units under the {{ $zone->name }}."
        icon="building-office-2"
        :breadcrumbs="[
            'Administrative Hierarchy' => route('offices.index'),
            'Divisional Education' => route('offices.deo.list')
        ]"
    >
        <x-slot:actions>
            @can('office.deo.list.create')
                <a href="{{ route('offices.deo.create') }}" wire:navigate>
                    <flux:button variant="primary" icon="plus" class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200/50 dark:shadow-none border-none">
                        Create Office
                    </flux:button>
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Divisional Cards Stack --}}
    <div class="space-y-4">
        @forelse ($divisionalEducationOffices as $data)
            <div class="group bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl p-2 pr-6 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 hover:border-indigo-500/30">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                    
                    {{-- Profile Section --}}
                    <div class="flex items-center gap-4 p-3 lg:w-80 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
                        <div class="relative">
                            <div class="h-14 w-14 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700">
                                <flux:icon.building-office class="size-8 text-slate-500 dark:text-slate-500" />
                            </div>
                            
                            {{-- Status Pulse --}}
                            <div class="absolute -top-1.5 -right-1.5">
                                <div class="w-3 h-3 rounded-full {{ $data->active_status ? 'bg-indigo-500 shadow-[0_0_8px_rgba(79,70,229,0.5)]' : 'bg-slate-300' }}"></div>
                            </div>
                        </div>
                        
                        <div class="overflow-hidden">
                            <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-0.5">
                                WPID: {{ $data->workplace_id }}
                            </p>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white truncate">
                                <flux:link href="{{ route('offices.institutions.by-devision', $data->id) }}" variant="ghost" class="!p-0 hover:text-indigo-600">
                                    {{ $data->name }}
                                </flux:link>
                            </h3>
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-8 px-4 lg:px-0">
                        {{-- Address Detail --}}
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2 text-slate-500 mb-1">
                                <flux:icon.map-pin variant="micro" class="size-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Mailing Address</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-1 italic">
                                {{ $data->address }}
                            </p>
                        </div>

                        {{-- Contact Detail --}}
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-2 text-slate-500 mb-1">
                                <flux:icon.phone variant="micro" class="size-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Contact Line</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                {{ $data->phone ?? 'Not Registered' }}
                            </p>
                        </div>
                    </div>

                    {{-- Actions Section --}}
                    <div class="flex items-center gap-2 pl-4 lg:pl-0">
                        @can('office.deo.profile.overview.view')
                            <flux:button href="{{ route('offices.deo.profile.overview', $data->id) }}" variant="filled" size="sm" class="rounded-xl">
                                View
                            </flux:button>
                        @endcan

                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" square />
                            <flux:menu>
                                @can('office.deo.update')
                                    <flux:menu.item icon="pencil-square">Edit Division</flux:menu.item>
                                @endcan
                                <flux:menu.item icon="users">Manage Staff</flux:menu.item>
                                <flux:menu.separator />
                                @can('office.deo.delete')
                                    <flux:menu.item variant="danger" icon="trash">Delete Record</flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 bg-slate-50 dark:bg-slate-800/50 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                <flux:icon.information-circle class="size-10 text-slate-300 mb-4" />
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Divisions in {{ $zone->short_name }}</h3>
                <p class="text-slate-500 text-sm">This zone currently has no sub-divisions assigned.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $divisionalEducationOffices->links() }}
    </div>
</div>