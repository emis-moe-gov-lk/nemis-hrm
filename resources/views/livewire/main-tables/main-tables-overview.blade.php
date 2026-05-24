<div class="max-w-[1440px] mx-auto px-4 py-8 space-y-16">
    {{-- Header Section --}}
    <x-page-header
        title="Main System Tables Overview"
        subtitle="Global configuration hub for master data records"
        icon="square-3-stack-3d" />

    <div class="pb-20 space-y-16">

        {{-- 1. GEOGRAPHY & REGIONAL --}}
        <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-6 w-1.5 bg-sky-500 rounded-full shadow-[0_0_8px_rgba(14,165,233,0.4)]"></div>
                <flux:heading size="lg" class="uppercase font-black tracking-[0.2em] text-sky-600 dark:text-sky-400 leading-none">
                    {{ __('Geography & Regional') }}
                </flux:heading>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                {{-- Province --}}
                <a href="{{ route('main-tables.provinces-lists') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-sky-500/10 hover:border-sky-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-sky-50 dark:group-hover:bg-sky-900/30 transition-colors">
                                <flux:icon.map variant="micro" class="opacity-80 text-slate-500 group-hover:text-sky-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-sky-600 transition-colors">Provinces</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Manage provincial boundaries.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-sky-500" />
                    </div>
                </a>

                {{-- District --}}
                <a href="{{ route('main-tables.district') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-sky-500/10 hover:border-sky-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-sky-50 dark:group-hover:bg-sky-900/30 transition-colors">
                                <flux:icon.map-pin variant="micro" class="opacity-80 text-slate-500 group-hover:text-sky-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-sky-600 transition-colors">Districts</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Administrative districts.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-sky-500" />
                    </div>
                </a>

                {{-- DS Office --}}
                <a href="{{ route('main-tables.ds-office') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-sky-500/10 hover:border-sky-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-sky-50 dark:group-hover:bg-sky-900/30 transition-colors">
                                <flux:icon.building-library variant="micro" class="opacity-80 text-slate-500 group-hover:text-sky-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-sky-600 transition-colors">DS Offices</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Divisional Secretariat divisions.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-sky-500" />
                    </div>
                </a>

                {{-- GN Divisions --}}
                <a href="{{ route('main-tables.gn-divisions') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-sky-500/10 hover:border-sky-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-sky-50 dark:group-hover:bg-sky-900/30 transition-colors">
                                <flux:icon.home-modern variant="micro" class="opacity-80 text-slate-500 group-hover:text-sky-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-sky-600 transition-colors">GN Divisions</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Grama Niladhari sectors.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-sky-500" />
                    </div>
                </a>

                {{-- Cities --}}
                <a href="{{ route('main-tables.city-list') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-sky-500/10 hover:border-sky-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-sky-50 dark:group-hover:bg-sky-900/30 transition-colors">
                                <flux:icon.globe-asia-australia variant="micro" class="opacity-80 text-slate-500 group-hover:text-sky-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-sky-600 transition-colors">City List</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Major cities and townships.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-sky-500" />
                    </div>
                </a>
            </div>
        </div>

        {{-- 2. IDENTITY & DEMOGRAPHICS --}}
        <div class="animate-in fade-in slide-in-from-bottom-6 duration-700">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-6 w-1.5 bg-rose-500 rounded-full shadow-[0_0_8px_rgba(244,63,94,0.4)]"></div>
                <flux:heading size="lg" class="uppercase font-black tracking-[0.2em] text-rose-600 dark:text-rose-400 leading-none">
                    {{ __('Identity & Demographics') }}
                </flux:heading>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                {{-- Titles --}}
                <a href="{{ route('main-tables.titles') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-rose-500/10 hover:border-rose-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-rose-50 dark:group-hover:bg-rose-900/30 transition-colors">
                                <flux:icon.user-circle variant="micro" class="opacity-80 text-slate-500 group-hover:text-rose-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-rose-600 transition-colors">Titles</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Salutations and prefixes.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-rose-500" />
                    </div>
                </a>

                {{-- Genders --}}
                <a href="{{ route('main-tables.genders') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-rose-500/10 hover:border-rose-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-rose-50 dark:group-hover:bg-rose-900/30 transition-colors">
                                <flux:icon.users variant="micro" class="opacity-80 text-slate-500 group-hover:text-rose-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-rose-600 transition-colors">Genders</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Gender classifications.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-rose-500" />
                    </div>
                </a>

                {{-- Blood Groups --}}
                <a href="{{ route('main-tables.blood-group') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-rose-500/10 hover:border-rose-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-rose-50 dark:group-hover:bg-rose-900/30 transition-colors">
                                <flux:icon.beaker variant="micro" class="opacity-80 text-slate-500 group-hover:text-rose-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-rose-600 transition-colors">Blood Groups</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Medical blood types.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-rose-500" />
                    </div>
                </a>

                {{-- Ethnicities --}}
                <a href="{{ route('main-tables.ethnicities') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-rose-500/10 hover:border-rose-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-rose-50 dark:group-hover:bg-rose-900/30 transition-colors">
                                <flux:icon.finger-print variant="micro" class="opacity-80 text-slate-500 group-hover:text-rose-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-rose-600 transition-colors">Ethnicities</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Cultural backgrounds.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-rose-500" />
                    </div>
                </a>

                {{-- Religions --}}
                <a href="{{ route('main-tables.religions') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-rose-500/10 hover:border-rose-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-rose-50 dark:group-hover:bg-rose-900/30 transition-colors">
                                <flux:icon.sparkles variant="micro" class="opacity-80 text-slate-500 group-hover:text-rose-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-rose-600 transition-colors">Religions</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Religious affiliations.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-rose-500" />
                    </div>
                </a>
            </div>
        </div>

        {{-- 3. INSTITUTIONAL DATA --}}
        <div class="animate-in fade-in slide-in-from-bottom-8 duration-1000">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-6 w-1.5 bg-indigo-500 rounded-full shadow-[0_0_8px_rgba(79,70,229,0.4)]"></div>
                <flux:heading size="lg" class="uppercase font-black tracking-[0.2em] text-indigo-600 dark:text-indigo-400 leading-none">
                    {{ __('Institutional Data') }}
                </flux:heading>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                {{-- Categories --}}
                <a href="{{ route('main-tables.institution-categories') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 transition-colors">
                                <flux:icon.tag variant="micro" class="opacity-80 text-slate-500 group-hover:text-indigo-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 transition-colors">Categories</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">School/Office classifications.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-indigo-500" />
                    </div>
                </a>

                {{-- Types --}}
                <a href="{{ route('main-tables.institution-types') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 transition-colors">
                                <flux:icon.squares-plus variant="micro" class="opacity-80 text-slate-500 group-hover:text-indigo-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 transition-colors">Types</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Functional facility types.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-indigo-500" />
                    </div>
                </a>

                {{-- Grades --}}
                <a href="{{ route('main-tables.grades-list') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 transition-colors">
                                <flux:icon.academic-cap variant="micro" class="opacity-80 text-slate-500 group-hover:text-indigo-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 transition-colors">Grades List</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">System grade registry.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-indigo-500" />
                    </div>
                </a>

                {{-- Facilities --}}
                <a href="{{ route('main-tables.institutional-facilities') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 transition-colors">
                                <flux:icon.wrench-screwdriver variant="micro" class="opacity-80 text-slate-500 group-hover:text-indigo-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 transition-colors">Facilities</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Infrastructure items.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-indigo-500" />
                    </div>
                </a>

                {{-- Authorities --}}
                <a href="{{ route('main-tables.authorities') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 transition-colors">
                                <flux:icon.scale variant="micro" class="opacity-80 text-slate-500 group-hover:text-indigo-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 transition-colors">Authorities</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Governing bodies master.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-indigo-500" />
                    </div>
                </a>
            </div>
        </div>

        {{-- 4. PROFESSIONAL & ACADEMIC --}}
        <div class="animate-in fade-in slide-in-from-bottom-10 duration-1000">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-6 w-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.4)]"></div>
                <flux:heading size="lg" class="uppercase font-black tracking-[0.2em] text-emerald-600 dark:text-emerald-400 leading-none">
                    {{ __('Professional & Academic') }}
                </flux:heading>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                {{-- Services --}}
                <a href="{{ route('main-tables.services') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 transition-colors">
                                <flux:icon.briefcase variant="micro" class="opacity-80 text-slate-500 group-hover:text-emerald-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 transition-colors">Services</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Organizational services.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-emerald-500" />
                    </div>
                </a>

                {{-- Positions --}}
                <a href="{{ route('main-tables.positions') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 transition-colors">
                                <flux:icon.academic-cap variant="micro" class="opacity-80 text-slate-500 group-hover:text-emerald-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 transition-colors">Positions</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Designated ranks/roles.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-emerald-500" />
                    </div>
                </a>

                {{-- Subjects --}}
                <a href="{{ route('main-tables.teaching-subjects') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 transition-colors">
                                <flux:icon.book-open variant="micro" class="opacity-80 text-slate-500 group-hover:text-emerald-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 transition-colors">Subjects</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Curriculum subjects.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-emerald-500" />
                    </div>
                </a>

                {{-- Police --}}
                <a href="{{ route('main-tables.police-stations') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 transition-colors">
                                <flux:icon.shield-check variant="micro" class="opacity-80 text-slate-500 group-hover:text-emerald-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 transition-colors">Police</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Law enforcement units.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-emerald-500" />
                    </div>
                </a>

                {{-- Circulars --}}
                <a href="{{ route('main-tables.cadre-circulars') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 transition-colors">
                                <flux:icon.document-text variant="micro" class="opacity-80 text-slate-500 group-hover:text-emerald-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 transition-colors">Circulars</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Official staff approvals.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-emerald-500" />
                    </div>
                </a>

                {{-- Transfer Categories --}}
                <a href="{{ route('main-tables.transfer-categories') }}" class="group p-5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 transition-colors">
                                <flux:icon.arrows-right-left variant="micro" class="opacity-80 text-slate-500 group-hover:text-emerald-600" />
                            </div>
                            <h4 class="font-black uppercase tracking-tighter text-[13px] text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 transition-colors">Transfer Categories</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 leading-snug italic line-clamp-2">Teacher transfer workflow categories.</p>
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-right variant="micro" class="text-emerald-500" />
                    </div>
                </a>
            </div>
        </div>

        {{-- 5. SYSTEM UTILITIES & LOGS --}}
        <div class="pt-16 border-t border-slate-300 dark:border-slate-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('main-tables.change-logs') }}" class="flex items-center gap-5 p-8 bg-slate-50 dark:bg-slate-800/40 rounded-[2.5rem] border border-slate-300 dark:border-slate-700 hover:border-indigo-500 transition-all group shadow-sm">
                    <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm text-slate-500 group-hover:text-indigo-500 transition-colors">
                        <flux:icon.clock />
                    </div>
                    <div>
                        <span class="block font-black uppercase text-xs tracking-widest text-slate-800 dark:text-white">Audit Trail</span>
                        <span class="text-xs text-slate-500 italic">Historical modifications</span>
                    </div>
                </a>

                <a href="{{ route('main-tables.versions') }}" class="flex items-center gap-5 p-8 bg-slate-50 dark:bg-slate-800/40 rounded-[2.5rem] border border-slate-300 dark:border-slate-700 hover:border-indigo-500 transition-all group shadow-sm">
                    <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm text-slate-500 group-hover:text-indigo-500 transition-colors">
                        <flux:icon.command-line />
                    </div>
                    <div>
                        <span class="block font-black uppercase text-xs tracking-widest text-slate-800 dark:text-white">Build Version</span>
                        <span class="text-xs text-slate-500 italic">v.1.0.12-stable</span>
                    </div>
                </a>

                <div class="flex items-center gap-5 p-8 bg-indigo-600 rounded-[2.5rem] text-white shadow-xl shadow-indigo-500/20">
                    <div class="p-4 bg-white/10 rounded-2xl">
                        <flux:icon.cpu-chip />
                    </div>
                    <div>
                        <span class="block font-black uppercase text-xs tracking-widest opacity-70">Integrity Check</span>
                        <span class="text-xs font-bold italic">All Masters Synced</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>