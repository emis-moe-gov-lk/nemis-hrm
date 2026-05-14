<div class="px-6 py-10 lg:px-12 max-w-7xl mx-auto space-y-12 pb-20">

    {{-- Transfer Categories Section --}}
    <div class="space-y-8">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Transfer Services</h2>
                <p class="text-sm font-semibold text-slate-500">Select the appropriate transfer category to begin your application process.</p>
            </div>
            <flux:button variant="subtle" size="sm" class="font-bold">View History</flux:button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Annual Transfer Card --}}
            <a href="{{ route('my-transfer.teacher-annual-transfer') }}" class="group relative bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-8 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                <div class="absolute top-0 right-0 p-8">
                    <flux:icon.arrow-up-right variant="mini" class="text-slate-300 group-hover:text-indigo-600 transition-colors" />
                </div>
                <div class="space-y-6">
                    <div class="h-16 w-16 rounded-3xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200 dark:shadow-none transition-transform group-hover:scale-110">
                        <flux:icon.calendar-days variant="mini" class="text-white h-8 w-8" />
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">Annual Transfer</h3>
                        <p class="text-sm font-semibold text-slate-500 leading-relaxed">
                            National service movement cycle following standard policy guidelines. Mandatory for tenure-based rotations.
                        </p>
                    </div>
                    <div class="pt-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Apply Now</span>
                        <div class="h-1 flex-1 bg-slate-50 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-600 w-0 group-hover:w-full transition-all duration-1000"></div>
                        </div>
                    </div>
                </div>
            </a>

            {{-- Mutual Transfer Card --}}
            <a href="{{ route('my-transfer.teacher-mutual-transfer') }}" class="group relative bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-8 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                <div class="absolute top-0 right-0 p-8">
                    <flux:icon.arrow-up-right variant="mini" class="text-slate-300 group-hover:text-blue-600 transition-colors" />
                </div>
                <div class="space-y-6">
                    <div class="h-16 w-16 rounded-3xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-none transition-transform group-hover:scale-110">
                        <flux:icon.users variant="mini" class="text-white h-8 w-8" />
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">Mutual Transfer</h3>
                        <p class="text-sm font-semibold text-slate-500 leading-relaxed">
                            Direct position swap between professionals of the same grade and subject. Requires bilateral agreement.
                        </p>
                    </div>
                    <div class="pt-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Find Match</span>
                        <div class="h-1 flex-1 bg-slate-50 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 w-0 group-hover:w-full transition-all duration-1000"></div>
                        </div>
                    </div>
                </div>
            </a>

            {{-- Special Transfer Card --}}
            <a href="{{ route('my-transfer.teacher-special-request') }}" class="group relative bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-8 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                <div class="absolute top-0 right-0 p-8">
                    <flux:icon.arrow-up-right variant="mini" class="text-slate-300 group-hover:text-amber-600 transition-colors" />
                </div>
                <div class="space-y-6">
                    <div class="h-16 w-16 rounded-3xl bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-200 dark:shadow-none transition-transform group-hover:scale-110">
                        <flux:icon.lifebuoy variant="mini" class="text-white h-8 w-8" />
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">Special Request</h3>
                        <p class="text-sm font-semibold text-slate-500 leading-relaxed">
                            Urgent movement based on humanitarian grounds (Medical, Security, Marital). Requires verified documentation.
                        </p>
                    </div>
                    <div class="pt-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-600 uppercase tracking-widest">Submit Request</span>
                        <div class="h-1 flex-1 bg-slate-50 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 w-0 group-hover:w-full transition-all duration-1000"></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Bottom Grid: Notices --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-12">
        {{-- Recent Notices --}}
        <div class="xl:col-span-3 space-y-6">
            <div class="flex items-center gap-3">
                <flux:icon.bell-alert variant="mini" class="text-rose-500" />
                <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Recent Announcements</h2>
            </div>

            <div class="space-y-4">
                <div class="group p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl flex gap-5 items-start hover:border-indigo-500 transition-colors shadow-sm">
                    <div class="shrink-0 h-14 w-14 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex flex-col items-center justify-center text-rose-600">
                        <span class="text-xs font-bold leading-none">APR</span>
                        <span class="text-xl font-extrabold leading-tight">12</span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold px-2 py-0.5 bg-rose-500 text-white rounded uppercase tracking-widest">Urgent</span>
                            <span class="text-xs font-semibold text-slate-400 tracking-wider uppercase leading-none">Circular No. 2026/04</span>
                        </div>
                        <h4 class="text-md font-bold text-slate-800 dark:text-white group-hover:text-indigo-600 transition-colors cursor-pointer">Extension of Annual Transfer Application Deadline</h4>
                        <p class="text-sm font-semibold text-slate-500">The deadline for submitting 2026 annual transfer applications has been extended...</p>
                    </div>
                </div>

                <div class="group p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl flex gap-5 items-start hover:border-indigo-500 transition-colors shadow-sm">
                    <div class="shrink-0 h-14 w-14 bg-blue-50 dark:bg-blue-500/10 rounded-2xl flex flex-col items-center justify-center text-blue-600">
                        <span class="text-xs font-bold leading-none">APR</span>
                        <span class="text-xl font-extrabold leading-tight">08</span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold px-2 py-0.5 bg-blue-500 text-white rounded uppercase tracking-widest">General</span>
                            <span class="text-xs font-semibold text-slate-400 tracking-wider uppercase leading-none">Guidelines v3.2</span>
                        </div>
                        <h4 class="text-md font-bold text-slate-800 dark:text-white group-hover:text-indigo-600 transition-colors cursor-pointer">Updated Point Scoring System for Rural Service</h4>
                        <p class="text-sm font-semibold text-slate-500">New scoring metrics for teachers serving in difficult zones have been approved by MOE...</p>
                    </div>
                </div>
            </div>
            <flux:button variant="subtle" size="sm" class="w-full font-bold">View All Announcements</flux:button>
        </div>
    </div>
</div>