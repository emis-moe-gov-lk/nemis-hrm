<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Ministry of Education Offices"
        subtitle="Manage and monitor decentralized administrative tiers across Sri Lanka"
        icon="building-office-2"
        :breadcrumbs="[
            'Administrative Hierarchy' => route('offices.index'),
            'Ministry of Education' => route('offices.moe.list')
        ]"
    >
        <x-slot:actions>
             @can('office.moe.create')
                <a href="{{ route('offices.moe.create') }}" wire:navigate>
                    <flux:button variant="primary" icon="plus" class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200/50 dark:shadow-none border-none">
                        Create New Office
                    </flux:button>
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <flux:separator variant="subtle" />

    {{-- Horizontal Cards Stack --}}
    <div class="space-y-4">
        @forelse ($educationMinistries as $data)
            <div class="group bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-3xl p-2 pr-6 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 hover:border-indigo-500/30">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                    
                    {{-- Office Identity Section with Status --}}
                    <a href="{{route('offices.pmoe.list')}}" wire:navigate>
                        <div class="flex items-center gap-5 p-3 lg:w-96 bg-slate-50 dark:bg-slate-800/50 rounded-2xl transition-all">
                            <div class="relative shrink-0">
                                <div class="h-16 w-16 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700">
                                    <flux:icon.building-office class="size-8 text-slate-500 dark:text-slate-500" />
                                </div>
                                
                                {{-- Status Indicator Dot --}}
                                <div class="absolute -top-2 -right-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-700">
                                        <span class="h-2.5 w-2.5 rounded-full {{ $data->active_status ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-slate-300' }}"></span>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="overflow-hidden">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.15em]">
                                        ID: {{ $data->workplace_id }}
                                    </span>
                                    {{-- Status Label --}}
                                    <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded {{ $data->active_status ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-500' }}">
                                        {{ $data->active_status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white truncate">
                                    {{ $data->name }}
                                </h3>
                            </div>
                        </div>
                    </a>

                    {{-- Details Grid Section --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-8 px-4 lg:px-0">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2 text-slate-500">
                                <flux:icon.map-pin variant="micro" class="size-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Address</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed italic">
                                {{ $data->address }}
                            </p>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2 text-slate-500">
                                <flux:icon.phone variant="micro" class="size-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Contact</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                {{ $data->phone ?? 'Not Provided' }}
                            </p>
                        </div>
                    </div>

                    {{-- Actions Section --}}
                    <div class="flex items-center gap-2 pl-4 lg:pl-0">
                        @can('office.moe.profile.overview.view')
                            <flux:button href="{{ route('offices.moe.profile.overview', $data->id) }}" variant="filled" size="sm" class="rounded-xl">
                                View
                            </flux:button>
                        @endcan

                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" square />
                            <flux:menu>
                                @can('office.moe.edit')
                                    <flux:menu.item icon="pencil-square">Edit</flux:menu.item>
                                @endcan
                                @can('office.moe.delete')
                                    <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-24 bg-slate-50 dark:bg-slate-800/50 rounded-[2.5rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl shadow-sm mb-4">
                    <flux:icon.magnifying-glass class="size-8 text-slate-300" />
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">No offices found</h3>
                <p class="text-slate-500 text-sm mt-1">Try adjusting your filters or creating a new record.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination Section --}}
    <div class="mt-8 px-2">
        {{ $educationMinistries->links() }}
    </div>
</div>