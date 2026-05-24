<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto space-y-8 pb-20">
    {{-- Header Section --}}
    <div class="space-y-4">
        <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-indigo-500">
            <a href="{{ route('my-transfer') }}" wire:navigate class="hover:text-indigo-600 transition-colors">Portal</a>
            <flux:icon.chevron-right variant="micro" class="h-3 w-3 text-slate-300" />
            <a href="{{ route('my-transfer.teacher-annual-transfer') }}" wire:navigate class="hover:text-indigo-600 transition-colors">Annual Transfer</a>
            <flux:icon.chevron-right variant="micro" class="h-3 w-3 text-slate-300" />
            <span class="text-slate-500">Guidelines</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1">
                <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                    Guidelines & <span class="text-indigo-600 dark:text-indigo-500">Policy</span>
                </h1>
                <p class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl leading-relaxed">
                    Official rules, eligibility criteria, and procedural instructions for the 2026 Teacher Annual Transfer cycle.
                </p>
            </div>
            <flux:button href="{{ route('my-transfer.teacher-annual-transfer') }}" wire:navigate variant="subtle" icon="arrow-left" size="sm" class="font-bold">
                Back to Dashboard
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        {{-- Sticky Navigation Sidebar --}}
        <div class="lg:col-span-3 sticky top-10 space-y-8 hidden lg:block">
            <div class="space-y-3">
                <h3 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">Policy Sections</h3>
                <nav class="flex flex-col space-y-1">
                    <a href="#eligibility" class="px-4 py-2 rounded-xl text-sm font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20">1. Eligibility Criteria</a>
                    <a href="#procedural" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-indigo-600 transition-colors">2. Procedural Logic</a>
                    <a href="#scoring" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-indigo-600 transition-colors">3. Scoring Matrix</a>
                    <a href="#documents" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-indigo-600 transition-colors">4. Required Documents</a>
                </nav>
            </div>

            <div class="p-6 bg-linear-to-br from-indigo-600 to-purple-700 rounded-3xl text-white shadow-xl shadow-indigo-100 dark:shadow-none">
                <h4 class="font-bold mb-2">Need Direct Help?</h4>
                <p class="text-xs text-indigo-100 leading-relaxed mb-4">Our dedicated support desk is available for complex humanitarian queries.</p>
                <flux:button size="xs" variant="primary" class="bg-white text-indigo-600 hover:bg-slate-50 w-full font-bold">Contact Support</flux:button>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-9 space-y-16">
            {{-- Section 1: Eligibility --}}
            <section id="eligibility" class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600">
                        <flux:icon.clipboard variant="mini" />
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">1. Eligibility Criteria</h2>
                </div>

                <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700/80 rounded-4xl p-8 shadow-sm space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <h4 class="text-sm font-extrabold uppercase tracking-widest text-slate-500">Service Tenure</h4>
                            <p class="text-slate-600 dark:text-slate-500 text-sm leading-relaxed">
                                Professionals must have completed a minimum of <span class="font-bold text-slate-900 dark:text-white">three (03) years</span> of continuous service at their current station as of December 31, 2025.
                            </p>
                        </div>
                        <div class="space-y-3">
                            <h4 class="text-sm font-extrabold uppercase tracking-widest text-slate-500">Subject Specialization</h4>
                            <p class="text-slate-600 dark:text-slate-500 text-sm leading-relaxed">
                                Movement is restricted to vacant positions that align exactly with the professional's primary and secondary subject codes.
                            </p>
                        </div>
                    </div>

                    <div class="p-6 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50 rounded-2xl flex gap-4">
                        <flux:icon.exclamation-triangle class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                        <div class="space-y-1">
                            <h5 class="text-sm font-bold text-amber-900 dark:text-amber-400">Important Restriction</h5>
                            <p class="text-xs text-amber-700 dark:text-amber-500/80 leading-relaxed">
                                Professionals with pending disciplinary inquiries or those currently on overseas leave without prior clearance are ineligible for the 2026 cycle.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 2: Procedural Logic --}}
            <section id="procedural" class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600">
                        <flux:icon.arrow-path variant="mini" />
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">2. Procedural Logic</h2>
                </div>

                <div class="space-y-4">
                    @foreach([
                    ['title' => 'Digital Drafting', 'desc' => 'Complete the interactive form within the teacher portal. Ensure your current station details are updated.'],
                    ['title' => 'Zonal Validation', 'desc' => 'Your application is first routed to your Zonal Education Office for service verification.'],
                    ['title' => 'Board Selection', 'desc' => 'Final placements are decided by the Transfer Board based on point rankings and vacancy availability.']
                    ] as $index => $step)
                    <div class="flex gap-6 p-6 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-3xl group hover:border-indigo-100 transition-colors">
                        <div class="shrink-0 h-12 w-12 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center font-black text-slate-300 group-hover:text-indigo-400 transition-colors">
                            0{{ $index + 1 }}
                        </div>
                        <div class="space-y-1">
                            <h4 class="font-bold text-slate-900 dark:text-white">{{ $step['title'] }}</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- Section 4: Required Documents --}}
            <section id="documents" class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                        <flux:icon.document-duplicate variant="mini" />
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">3. Required Documents</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-8 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-4xl space-y-4">
                        <h4 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Standard Requirements
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-500">
                                <flux:icon.check variant="micro" class="text-emerald-500 h-4 w-4" />
                                Updated Service Letter
                            </li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-500">
                                <flux:icon.check variant="micro" class="text-emerald-500 h-4 w-4" />
                                Verification of NIC
                            </li>
                        </ul>
                    </div>

                    <div class="p-8 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-4xl space-y-4">
                        <h4 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            Humanitarian Grounds
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-500">
                                <flux:icon.check variant="micro" class="text-indigo-500 h-4 w-4" />
                                Medical Board Reports
                            </li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-500">
                                <flux:icon.check variant="micro" class="text-indigo-500 h-4 w-4" />
                                Marriage Certificate (Attested)
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>