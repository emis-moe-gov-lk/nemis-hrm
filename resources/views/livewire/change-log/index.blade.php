<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="What's New"
        subtitle="Follow our latest updates and improvements."
        icon="megaphone"
    />

    <div class="space-y-6">
        @foreach($versions as $version)
            @php $isExpanded = $version['is_latest'] || in_array($version['id'], $expandedVersions); @endphp

            <div class="bg-white dark:bg-zinc-900 border {{ $version['is_latest'] ? 'border-blue-500 ring-1 ring-blue-500' : 'border-zinc-200 dark:border-zinc-700' }} rounded-xl p-6 duration-200 shadow-sm">
                
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-bold text-zinc-900 dark:text-white">{{ $version['id'] }}</span>
                            
                            @if($version['is_latest'])
                                <span class="px-2.5 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full uppercase tracking-wider">
                                    Latest Release
                                </span>
                            @endif
                        </div>
                        <div class="text-sm text-zinc-400 mt-1 font-medium">{{ $version['date'] }}</div>
                    </div>
                    
                    @if(!$version['is_latest'])
                        <flux:button 
                            variant="ghost" 
                            size="sm" 
                            wire:click="toggle('{{ $version['id'] }}')"
                            class="!-mr-2 font-medium"
                        >
                            {{ $isExpanded ? 'Hide' : 'View' }}
                            <x-slot name="icon">
                                <flux:icon :name="$isExpanded ? 'chevron-up' : 'chevron-down'" variant="micro" />
                            </x-slot>
                        </flux:button>
                    @endif
                </div>

                {{-- Change Details Section --}}
                @if($isExpanded)
                    <div class="mt-6">
                        <flux:separator class="mb-6" />

                        <div class="space-y-6">
                            @foreach($version['changes'] as $type => $items)
                                <div>
                                    <h4 class="text-xs font-extrabold uppercase tracking-[0.2em] text-zinc-400 dark:text-zinc-400 mb-4 flex items-center gap-3">
                                        <span class="w-2 h-2 rounded-full {{ $type === 'added' ? 'bg-emerald-500' : ($type === 'fixed' ? 'bg-amber-500' : 'bg-blue-500') }}"></span>
                                        {{ $type }}
                                    </h4>
                                    <ul class="space-y-3 pl-5">
                                        @foreach($items as $item)
                                            <li class="relative text-zinc-600 dark:text-zinc-300 text-sm leading-relaxed before:content-[''] before:absolute before:-left-4 before:top-2 before:w-1 before:h-1 before:bg-zinc-300 before:rounded-full">
                                                {{ $item }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>