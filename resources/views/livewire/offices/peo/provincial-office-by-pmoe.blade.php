<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Provincial Education Offices"
        subtitle="Manage provincial administrative units under {{ $pmoeWorkplace->office()->name ?? 'PMOE' }}."
        icon="building-office-2"
        :breadcrumbs="[
            'Administrative Hierarchy' => route('offices.index'),
            'Provincial Education' => route('offices.peo.list')
        ]"
    >
        <x-slot:actions>
            @can('office.peo.create')
                <a href="{{ route('offices.peo.create') }}" wire:navigate>
                    <flux:button variant="primary" icon="plus" class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200/50 dark:shadow-none border-none">
                        Create Office
                    </flux:button>
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Horizontal Cards Stack --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse ($provincialEducationOffices as $data)
            <div class="group relative bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl p-2 transition-all duration-300 hover:border-indigo-500/40 hover:shadow-2xl hover:shadow-indigo-500/5">
                
                {{-- Inner Flex Container --}}
                <div class="flex flex-col lg:flex-row lg:items-center gap-4 pr-6">
                    
                    {{-- 1. Identity Block: The "Anchor" --}}
                    <div class="lg:w-[380px] shrink-0">
                        <a href="{{route('offices.zeo.by-province', $data->id)}}" wire:navigate class="flex items-center gap-4 p-3 rounded-[1.5rem] bg-slate-50 dark:bg-slate-800/40 group-hover:bg-indigo-50/50 dark:group-hover:bg-indigo-500/5 transition-colors">
                            <div class="relative shrink-0">
                                <div class="h-16 w-16 flex items-center justify-center rounded-2xl bg-slate-50 dark:bg-slate-800 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700">
                                    <flux:icon.building-office class="size-8 text-slate-500 dark:text-slate-500" />
                                </div>
                                
                                {{-- Status Pulse --}}
                                <div class="absolute -top-1.5 -right-1.5">
                                    <span class="relative flex h-5 w-5">
                                        @if($data->active_status)
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        @endif
                                        <span class="relative inline-flex rounded-full h-5 w-5 border-2 border-white dark:border-slate-900 {{ $data->active_status ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="overflow-hidden">
                                <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest block mb-0.5">
                                    Code: {{ $data->workplace_id }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white leading-tight group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition-colors">
                                    {{ $data->name }}
                                </h3>
                            </div>
                        </a>
                    </div>

                    {{-- 2. Data Strip: The "Content" --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:gap-12 gap-4 px-6 py-2">
                        {{-- Location --}}
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 opacity-50">
                                <flux:icon.map-pin variant="micro" class="size-3" />
                                <span class="text-[9px] font-bold uppercase tracking-widest">Headquarters</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-500 line-clamp-1 font-medium italic">
                                {{ $data->address }}
                            </p>
                        </div>

                        {{-- Contact --}}
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 opacity-50">
                                <flux:icon.phone variant="micro" class="size-3" />
                                <span class="text-[9px] font-bold uppercase tracking-widest">Direct Line</span>
                            </div>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                {{ $data->phone ?? 'Not Registered' }}
                            </p>
                        </div>
                    </div>

                    {{-- 3. Action Block: The "Utility" --}}
                    <div class="flex items-center gap-3 pl-6 lg:pl-0 pb-4 lg:pb-0">
                        <flux:separator vertical class="hidden lg:block h-8 mx-2" />
                        
                        @can('office.peo.profile.overview.view')
                            <flux:button href="{{ route('offices.peo.profile.overview', $data->id) }}" variant="subtle" size="sm" class="!rounded-xl font-bold">
                                Overview
                            </flux:button>
                        @endcan

                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-vertical" square class="!rounded-xl" />
                            <flux:menu class="min-w-48">
                                <flux:menu.item icon="pencil-square">Edit Profile</flux:menu.item>
                                <flux:menu.item icon="shield-check">Security</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item variant="danger" icon="trash">Archive</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-32 bg-slate-50 dark:bg-slate-900/30 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-indigo-500 blur-2xl opacity-10 animate-pulse"></div>
                    <flux:icon.magnifying-glass class="size-16 text-slate-300 relative" />
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">No Office Records</h3>
                <p class="text-slate-500 max-w-xs text-center mt-2">We couldn't find any provincial offices matching your current scope.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $provincialEducationOffices->links() }}
    </div>
</div>