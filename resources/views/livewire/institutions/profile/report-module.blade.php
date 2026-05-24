<section class="w-full">
    {{-- 1. Header Section --}}
    <header class="mb-10">
        <flux:heading size="xl" level="1" class="!text-3xl font-black tracking-tight text-slate-900 dark:text-white leading-none mb-3">
            {{ __('Report Repository') }}
        </flux:heading>
        <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
            {{ __('Access and download institutional analytics, staff directories, and administrative summaries.') }}
        </flux:subheading>
    </header>

    <x-institutions.institution-layout :institutionId="$id">
        <div class="mt-8">
            <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 w-16 text-center">ID</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Report Description</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-right">Download Options</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-all group">
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 rounded-lg text-[10px] font-black">1</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors">
                                    Institution / School Staff Master Directory
                                </div>
                                <div class="text-[10px] font-black text-slate-500 uppercase tracking-tight mt-0.5">
                                    Comprehensive list of all assigned teachers and administrative staff
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="subtle" size="sm" class="rounded-lg! text-emerald-600 hover:bg-emerald-50!">
                                        <div class="flex items-center gap-2">
                                            <flux:icon name="table-cells" variant="micro" />
                                            <span class="text-[10px] font-black uppercase tracking-widest">Excel</span>
                                        </div>
                                    </flux:button>
                                    
                                    <flux:button href="{{ route('pdf.institutions-teachers-list', $institution->id) }}" target="_blank" variant="subtle" size="sm" class="rounded-lg! text-rose-600 hover:bg-rose-50!">
                                        <div class="flex items-center gap-2">
                                            <flux:icon name="document-text" variant="micro" />
                                            <span class="text-[10px] font-black uppercase tracking-widest">PDF</span>
                                        </div>
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            {{-- Analytics Teaser --}}
            <div class="mt-8 p-8 bg-linear-to-br from-indigo-600 to-indigo-800 rounded-[2.5rem] relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 text-white/10 transition-transform duration-700 group-hover:scale-110 group-hover:-rotate-6">
                    <flux:icon name="presentation-chart-line" variant="solid" class="w-64 h-64" />
                </div>
                <div class="relative z-10 max-w-lg">
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-2">Advanced Analytics coming soon</h3>
                    <p class="text-indigo-100 font-medium mb-6">We are building a powerful dashboard to help you visualize staff performance, cadre trends, and institutional growth.</p>
                    <flux:button disabled variant="subtle" class="bg-white/10! border-white/20! text-white! rounded-xl! font-black uppercase tracking-widest text-[10px]">Development in Progress</flux:button>
                </div>
            </div>
        </div>
    </x-institutions.institution-layout>
</section>