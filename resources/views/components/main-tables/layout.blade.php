<div class="space-y-8 px-2 py-8 max-w-7xl mx-left">
    {{-- Header --}}
    <div class="flex items-center justify-between px-6">
        <div>
            <flux:heading size="xl" level="1" class="!font-black tracking-tight">{{ __('System Master Data') }}</flux:heading>
            <flux:subheading>{{ __('Central repository for all system lookup tables and configurations.') }}</flux:subheading>
        </div>
    </div>

    {{-- The Mega Navigation Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- 1. Administrative & Geography (7 Tables) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4 px-2">
                <flux:icon.map-pin variant="micro" class="text-indigo-600" />
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">{{ __('Geography & Admin') }}</h3>
            </div>
            <div class="grid grid-cols-2 gap-x-2 space-y-0.5">
                <flux:navlist.item :href="route('main-tables.provinces-lists')" :current="request()->routeIs('main-tables.provinces-lists')" wire:navigate>{{ __('Provinces') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.district')" :current="request()->routeIs('main-tables.district')" wire:navigate>{{ __('Districts') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.ds-office')" :current="request()->routeIs('main-tables.ds-office')" wire:navigate>{{ __('DS Offices') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.gn-divisions')" :current="request()->routeIs('main-tables.gn-divisions')" wire:navigate>{{ __('GN Divisions') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.city-list')" :current="request()->routeIs('main-tables.city-list')" wire:navigate>{{ __('Cities') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.police-stations')" :current="request()->routeIs('main-tables.police-stations')" wire:navigate>{{ __('Police') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.office-levels')" :current="request()->routeIs('main-tables.office-levels')" wire:navigate>{{ __('Office Levels') }}</flux:navlist.item>
            </div>
        </div>

        {{-- 2. Institutional Framework (8 Tables) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4 px-2">
                <flux:icon.building-library variant="micro" class="text-indigo-600" />
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">{{ __('Institutions') }}</h3>
            </div>
            <div class="grid grid-cols-2 gap-x-2 space-y-0.5">
                <flux:navlist.item :href="route('main-tables.authorities')" :current="request()->routeIs('main-tables.authorities')" wire:navigate>{{ __('Main Authorities') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.institution-authorities')" :current="request()->routeIs('main-tables.institution-authorities')" wire:navigate>{{ __('Ins. Authorities') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.institution-categories')" :current="request()->routeIs('main-tables.institution-categories')" wire:navigate>{{ __('Categories') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.institution-types')" :current="request()->routeIs('main-tables.institution-types')" wire:navigate>{{ __('Types') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.institutional-facilities')" :current="request()->routeIs('main-tables.institutional-facilities')" wire:navigate>{{ __('Facilities') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.institution-grade-spans')" :current="request()->routeIs('main-tables.institution-grade-spans')" wire:navigate>{{ __('Grade Spans') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.institution-ethnicities')" :current="request()->routeIs('main-tables.institution-ethnicities')" wire:navigate>{{ __('Ins. Ethnicities') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.institution-genders')" :current="request()->routeIs('main-tables.institution-genders')" wire:navigate>{{ __('Ins. Genders') }}</flux:navlist.item>
            </div>
        </div>

        {{-- 3. Human Resources & Services (8 Tables) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4 px-2">
                <flux:icon.identification variant="micro" class="text-indigo-600" />
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">{{ __('Staff & Services') }}</h3>
            </div>
            <div class="grid grid-cols-2 gap-x-2 space-y-0.5">
                <flux:navlist.item :href="route('main-tables.positions')" :current="request()->routeIs('main-tables.positions')" wire:navigate>{{ __('Positions') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.services')" :current="request()->routeIs('main-tables.services')" wire:navigate>{{ __('Services') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.service-ranks')" :current="request()->routeIs('main-tables.service-ranks')" wire:navigate>{{ __('Service Ranks') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.occupations-lists')" :current="request()->routeIs('main-tables.occupations-lists')" wire:navigate>{{ __('Occupations') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.sleas-categories')" :current="request()->routeIs('main-tables.sleas-categories')" wire:navigate>{{ __('SLEAS Cat.') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.teacher-categories')" :current="request()->routeIs('main-tables.teacher-categories')" wire:navigate>{{ __('Teacher Cat.') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.teacher-types')" :current="request()->routeIs('main-tables.teacher-types')" wire:navigate>{{ __('Teacher Types') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.principal-recruitment-categories')" :current="request()->routeIs('main-tables.principal-recruitment-categories')" wire:navigate>{{ __('Principal Rec.') }}</flux:navlist.item>
            </div>
        </div>

        {{-- 4. Academic & Curriculum (7 Tables) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4 px-2">
                <flux:icon.academic-cap variant="micro" class="text-indigo-600" />
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">{{ __('Academic') }}</h3>
            </div>
            <div class="grid grid-cols-2 gap-x-2 space-y-0.5">
                <flux:navlist.item :href="route('main-tables.education-qualifications')" :current="request()->routeIs('main-tables.education-qualifications')" wire:navigate>{{ __('Qualifications') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.educational-qualification-grades')" :current="request()->routeIs('main-tables.educational-qualification-grades')" wire:navigate>{{ __('Qual. Grades') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.sleas-subjects')" :current="request()->routeIs('main-tables.sleas-subjects')" wire:navigate>{{ __('SLEAS Subjects') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.appointed-subjects')" :current="request()->routeIs('main-tables.appointed-subjects')" wire:navigate>{{ __('Appointed Sub.') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.teaching-subjects')" :current="request()->routeIs('main-tables.teaching-subjects')" wire:navigate>{{ __('Teaching Sub.') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.medium-of-instructions')" :current="request()->routeIs('main-tables.medium-of-instructions')" wire:navigate>{{ __('Instruction Med.') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.institution-languages')" :current="request()->routeIs('main-tables.institution-languages')" wire:navigate>{{ __('Ins. Languages') }}</flux:navlist.item>
            </div>
        </div>

        {{-- 5. Demographics (5 Tables) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4 px-2">
                <flux:icon.user-circle variant="micro" class="text-indigo-600" />
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">{{ __('Demographics') }}</h3>
            </div>
            <div class="grid grid-cols-2 gap-x-2 space-y-0.5">
                <flux:navlist.item :href="route('main-tables.ethnicities')" :current="request()->routeIs('main-tables.ethnicities')" wire:navigate>{{ __('Ethnicities') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.religions')" :current="request()->routeIs('main-tables.religions')" wire:navigate>{{ __('Religions') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.genders')" :current="request()->routeIs('main-tables.genders')" wire:navigate>{{ __('Genders') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.blood-group')" :current="request()->routeIs('main-tables.blood-group')" wire:navigate>{{ __('Blood Groups') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.civil-status')" :current="request()->routeIs('main-tables.civil-status')" wire:navigate>{{ __('Civil Status') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.titles')" :current="request()->routeIs('main-tables.titles')" wire:navigate>{{ __('Titles') }}</flux:navlist.item>
            </div>
        </div>

        {{-- 6. System & Governance (4 Tables) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4 px-2">
                <flux:icon.adjustments-horizontal variant="micro" class="text-indigo-600" />
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">{{ __('System & Logs') }}</h3>
            </div>
            <div class="grid grid-cols-2 gap-x-2 space-y-0.5">
                <flux:navlist.item :href="route('main-tables.versions')" :current="request()->routeIs('main-tables.versions')" wire:navigate>{{ __('Versions') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.change-logs')" :current="request()->routeIs('main-tables.change-logs')" wire:navigate>{{ __('Change Logs') }}</flux:navlist.item>
                <flux:navlist.item :href="route('main-tables.cadre-circulars')" :current="request()->routeIs('main-tables.cadre-circulars')" wire:navigate>{{ __('Cadre Circulars') }}</flux:navlist.item>
            </div>
        </div>
    </div>

    {{-- The Active Data View --}}
    <main class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[3rem] p-8 shadow-xl min-h-[60vh] transition-all duration-300">
        {{ $slot }}
    </main>
</div>