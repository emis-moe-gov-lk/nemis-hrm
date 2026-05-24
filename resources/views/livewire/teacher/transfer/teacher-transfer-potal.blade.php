<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Transfer Services"
        subtitle="Select the appropriate transfer category to begin your application process."
        icon="arrows-right-left"
        :breadcrumbs="[
            'Transfer Portal' => route('my-transfer')
        ]"
    >
        <x-slot:actions>
            <flux:button variant="subtle" size="sm" class="h-11 font-bold">View History</flux:button>
        </x-slot:actions>
    </x-page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Annual Transfer Card --}}
            @can('transfer.annual.view')
            <a href="{{ route('my-transfer.teacher-annual-transfer') }}" class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2.5rem] p-8 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
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
            @else
            <div class="relative bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-8 opacity-60 cursor-not-allowed select-none" title="{{ __('You do not have permission to access Annual Transfer.') }}">
                <div class="absolute top-0 right-0 p-8">
                    <flux:icon name="lock-closed" variant="mini" class="text-slate-400" />
                </div>
                <div class="space-y-6">
                    <div class="h-16 w-16 rounded-3xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center shadow-sm">
                        <flux:icon.calendar-days variant="mini" class="text-slate-500 dark:text-slate-400 h-8 w-8" />
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-2xl font-extrabold text-slate-400 dark:text-slate-500">Annual Transfer</h3>
                        <p class="text-sm font-semibold text-slate-400 dark:text-slate-500 leading-relaxed">
                            National service movement cycle following standard policy guidelines. Mandatory for tenure-based rotations.
                        </p>
                    </div>
                    <div class="pt-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Locked') }}</span>
                    </div>
                </div>
            </div>
            @endcan

            {{-- Mutual Transfer Card --}}
            @can('transfer.mutual.view')
            <a href="{{ route('my-transfer.teacher-mutual-transfer') }}" class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2.5rem] p-8 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
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
            @else
            <div class="relative bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-8 opacity-60 cursor-not-allowed select-none" title="{{ __('You do not have permission to access Mutual Transfer.') }}">
                <div class="absolute top-0 right-0 p-8">
                    <flux:icon name="lock-closed" variant="mini" class="text-slate-400" />
                </div>
                <div class="space-y-6">
                    <div class="h-16 w-16 rounded-3xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center shadow-sm">
                        <flux:icon.users variant="mini" class="text-slate-500 dark:text-slate-400 h-8 w-8" />
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-2xl font-extrabold text-slate-400 dark:text-slate-500">Mutual Transfer</h3>
                        <p class="text-sm font-semibold text-slate-400 dark:text-slate-500 leading-relaxed">
                            Direct position swap between professionals of the same grade and subject. Requires bilateral agreement.
                        </p>
                    </div>
                    <div class="pt-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Locked') }}</span>
                    </div>
                </div>
            </div>
            @endcan

            {{-- Special Transfer Card --}}
            @can('transfer.special.view')
            <a href="{{ route('my-transfer.teacher-special-request') }}" class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2.5rem] p-8 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
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
            @else
            <div class="relative bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-8 opacity-60 cursor-not-allowed select-none" title="{{ __('You do not have permission to access Special Transfer.') }}">
                <div class="absolute top-0 right-0 p-8">
                    <flux:icon name="lock-closed" variant="mini" class="text-slate-400" />
                </div>
                <div class="space-y-6">
                    <div class="h-16 w-16 rounded-3xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center shadow-sm">
                        <flux:icon.lifebuoy variant="mini" class="text-slate-500 dark:text-slate-400 h-8 w-8" />
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-2xl font-extrabold text-slate-400 dark:text-slate-500">Special Request</h3>
                        <p class="text-sm font-semibold text-slate-400 dark:text-slate-500 leading-relaxed">
                            Urgent movement based on humanitarian grounds (Medical, Security, Marital). Requires verified documentation.
                        </p>
                    </div>
                    <div class="pt-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Locked') }}</span>
                    </div>
                </div>
            </div>
            @endcan
        </div>

    {{-- Announcements Section --}}
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <flux:icon name="bell-alert" variant="mini" class="text-rose-500" />
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Recent Announcements') }}</h2>
        </div>

        <div class="space-y-4">
            @forelse($announcements as $announcement)
            @php
            $typeColor = match($announcement->type) {
            'urgent' => 'rose',
            'general' => 'blue',
            'policy' => 'indigo',
            default => 'zinc'
            };
            $typeGradient = match($announcement->type) {
            'urgent' => 'bg-linear-to-br from-rose-600 to-rose-700 text-white shadow-xl shadow-rose-500/20 dark:shadow-none',
            'general' => 'bg-linear-to-br from-blue-600 to-blue-700 text-white shadow-xl shadow-blue-500/20 dark:shadow-none',
            'policy' => 'bg-linear-to-br from-indigo-600 to-indigo-700 text-white shadow-xl shadow-indigo-500/20 dark:shadow-none',
            default => 'bg-linear-to-br from-slate-600 to-slate-700 text-white shadow-xl shadow-slate-500/20 dark:shadow-none'
            };
            $badgeBg = match($announcement->type) {
            'urgent' => 'bg-rose-600',
            'general' => 'bg-blue-600',
            'policy' => 'bg-indigo-700',
            default => 'bg-slate-600'
            };
            @endphp
            <div class="group p-6 bg-linear-to-br from-slate-100/90 to-white dark:from-zinc-800/60 dark:to-zinc-900/60 border border-slate-300 dark:border-zinc-700 rounded-3xl flex gap-6 items-start hover:border-indigo-600 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500">
                {{-- Date Badge --}}
                <div class="shrink-0 h-16 w-16 {{ $typeGradient }} rounded-3xl flex flex-col items-center justify-center transition-transform duration-500 group-hover:scale-110">
                    <span class="text-[10px] font-bold leading-none uppercase opacity-80">{{ $announcement->created_at->format('M') }}</span>
                    <span class="text-xl font-black leading-tight">{{ $announcement->created_at->format('d') }}</span>
                </div>

                <div class="space-y-1 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-black px-2 py-0.5 {{ $badgeBg }} text-white rounded uppercase tracking-widest">{{ $announcement->type }}</span>
                        <span class="text-[10px] font-bold text-slate-500 tracking-wider uppercase leading-none">{{ $announcement->created_at->format('Y') }}</span>
                    </div>

                    <h4 class="text-md font-bold text-slate-800 dark:text-white group-hover:text-indigo-600 transition-colors">
                        {{ $announcement->title }}
                    </h4>

                    <p class="text-sm font-semibold text-slate-500 leading-relaxed">
                        {{ $announcement->content }}
                    </p>

                    @if($announcement->link_route)
                    <div class="pt-2">
                        <a href="{{ $announcement->link_route }}" class="text-[11px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest hover:underline">
                            {{ $announcement->link_text ?: __('View Details') }} &rarr;
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-12 flex flex-col items-center justify-center bg-slate-50/30 dark:bg-zinc-800/20 border-2 border-dashed border-slate-200 dark:border-zinc-700 rounded-3xl">
                <flux:icon name="chat-bubble-left-right" class="w-8 h-8 text-slate-300 mb-3" />
                <h3 class="text-sm font-bold text-slate-500">{{ __('No announcements found') }}</h3>
            </div>
            @endforelse
        </div>
    </div>
</div>
