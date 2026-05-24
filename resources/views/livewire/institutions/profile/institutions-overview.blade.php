<section class="w-full">
    {{-- 1. Header Section --}}
    <header class="mb-10">
        <flux:heading size="xl" level="1" class="text-3xl! font-black tracking-tight text-slate-900 dark:text-white leading-none mb-3">
            {{ __('Institution Overview') }}
        </flux:heading>
        <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
            {{ __('Quick snapshot of institution demographics, capacity, and key metrics.') }}
        </flux:subheading>
    </header>

    <x-institutions.institution-layout :institutionId="$id">
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Students Card --}}
            <div class="group relative overflow-hidden rounded-[2.5rem] bg-linear-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-slate-900 border border-blue-100 dark:border-blue-900/30 p-8 shadow-sm transition-all duration-500 hover:shadow-xl hover:shadow-blue-100/50 hover:scale-[1.02]">
                <div class="absolute -right-6 -top-6 text-blue-100 dark:text-blue-900/20 transition-all duration-500 group-hover:scale-110 group-hover:rotate-12">
                    <flux:icon name="academic-cap" variant="solid" class="w-32 h-32" />
                </div>

                <div class="relative z-10">
                    <div class="p-3 bg-blue-600 rounded-2xl w-fit shadow-lg shadow-blue-200 dark:shadow-none mb-6">
                        <flux:icon name="users" variant="mini" class="w-6 h-6 text-white" />
                    </div>
                    
                    <div class="space-y-1">
                        <div class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($studentCount) }}</div>
                        <div class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em]">{{ __('Students') }}</div>
                    </div>

                    <div class="flex items-center justify-between mt-10">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-slate-50 dark:bg-slate-800 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700">
                            {{ __('Total Enrolled') }}
                        </span>
                        <flux:button variant="subtle" size="sm" class="rounded-xl! font-black uppercase text-[10px] tracking-widest">{{ __('View') }}</flux:button>
                    </div>
                </div>
            </div>

            {{-- Staff Card --}}
            <div class="group relative overflow-hidden rounded-[2.5rem] bg-linear-to-br from-indigo-50 to-white dark:from-indigo-900/20 dark:to-slate-900 border border-indigo-100 dark:border-indigo-900/30 p-8 shadow-sm transition-all duration-500 hover:shadow-xl hover:shadow-indigo-100/50 hover:scale-[1.02]">
                <div class="absolute -right-6 -top-6 text-indigo-100 dark:text-indigo-900/20 transition-all duration-500 group-hover:scale-110 group-hover:rotate-12">
                    <flux:icon name="user-group" variant="solid" class="w-32 h-32" />
                </div>

                <div class="relative z-10">
                    <div class="p-3 bg-indigo-600 rounded-2xl w-fit shadow-lg shadow-indigo-200 dark:shadow-none mb-6">
                        <flux:icon name="briefcase" variant="mini" class="w-6 h-6 text-white" />
                    </div>
                    
                    <div class="space-y-1">
                        <div class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($staffCount) }}</div>
                        <div class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em]">{{ __('Staff Members') }}</div>
                    </div>

                    <div class="flex items-center justify-between mt-10">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-slate-50 dark:bg-slate-800 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700">
                            {{ __('Active Cadre') }}
                        </span>
                        <flux:button href="{{ route('offices.institutions.profile.staff', $id) }}" variant="subtle" size="sm" class="rounded-xl! font-black uppercase text-[10px] tracking-widest">{{ __('View') }}</flux:button>
                    </div>
                </div>
            </div>

            {{-- Parents Card --}}
            <div class="group relative overflow-hidden rounded-[2.5rem] bg-linear-to-br from-purple-50 to-white dark:from-purple-900/20 dark:to-slate-900 border border-purple-100 dark:border-purple-900/30 p-8 shadow-sm transition-all duration-500 hover:shadow-xl hover:shadow-purple-100/50 hover:scale-[1.02]">
                <div class="absolute -right-6 -top-6 text-purple-100 dark:text-purple-900/20 transition-all duration-500 group-hover:scale-110 group-hover:rotate-12">
                    <flux:icon name="home" variant="solid" class="w-32 h-32" />
                </div>

                <div class="relative z-10">
                    <div class="p-3 bg-purple-600 rounded-2xl w-fit shadow-lg shadow-purple-200 dark:shadow-none mb-6">
                        <flux:icon name="user-plus" variant="mini" class="w-6 h-6 text-white" />
                    </div>
                    
                    <div class="space-y-1">
                        <div class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($parentCount) }}</div>
                        <div class="text-xs font-black text-purple-600 dark:text-purple-400 uppercase tracking-[0.2em]">{{ __('Guardians') }}</div>
                    </div>

                    <div class="flex items-center justify-between mt-10">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-slate-50 dark:bg-slate-800 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700">
                            {{ __('Registered Parents') }}
                        </span>
                        <flux:button variant="subtle" size="sm" class="rounded-xl! font-black uppercase text-[10px] tracking-widest">{{ __('View') }}</flux:button>
                    </div>
                </div>
            </div>
        </div>
    </x-institutions.institution-layout>
</section>