<section class="w-full">
    {{-- 1. Header Section --}}
    <header class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <flux:heading size="xl" level="1" class="text-3xl! font-black tracking-tight text-slate-900 dark:text-white leading-none mb-3">
                {{ __('Student Population & Enrollment') }}
            </flux:heading>
            <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
                {{ __('Manage institution grade levels, classroom sections, and maintain accurate gender-based student counts.') }}
            </flux:subheading>
        </div>
    </header>

    <x-institutions.institution-layout :institutionId="$id" :institution="$institution">
        <div class="mt-8 space-y-10">

            <div class="flex flex-col items-end gap-2">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 mr-2">{{ __('Select Academic Year') }}</span>
                <div class="flex items-center gap-4 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-300 dark:border-slate-700 shadow-sm">
                    <flux:select wire:model.live="academicYear" class="min-w-[180px] font-black! text-indigo-600 dark:text-indigo-400">
                        @for($y = date('Y')+1; $y >= 2020; $y--)
                        <flux:select.option value="{{ $y }}">{{ $y }} {{ $y == date('Y') ? '(' . __('Current') . ')' : '' }}</flux:select.option>
                        @endfor
                    </flux:select>
                    @can('institution.student_enrollment.update')
                    <flux:button wire:click="copyFromPreviousYear" icon="document-duplicate" variant="subtle" size="sm" class="rounded-xl! font-bold bg-indigo-50! text-indigo-600! border-indigo-100!">
                        {{ __('Copy Structure') }}
                    </flux:button>
                    @endcan
                </div>
            </div>
            {{-- 2. Setup Controls (Grades & Classes) --}}
            <div class="grid grid-cols-1 @can('institution.student_enrollment.update') lg:grid-cols-2 @endcan gap-8">
                {{-- Grade Management --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <flux:icon name="academic-cap" variant="solid" class="size-24" />
                    </div>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl">
                            <flux:icon name="academic-cap" variant="mini" class="text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ __('Grade Setup (' . $academicYear . ')') }}</h3>
                    </div>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label class="font-black! uppercase tracking-widest text-[10px] text-slate-500 mb-2">{{ __('Academic Year Context') }}</flux:label>
                            <flux:select wire:model.live="academicYear">
                                <flux:select.option value="">{{ __('Select Year') }}</flux:select.option>
                                @for($y = date('Y')+1; $y >= 2020; $y--)
                                <flux:select.option value="{{ $y }}">{{ $y }} {{ $y == date('Y') ? '(' . __('Current') . ')' : '' }}</flux:select.option>
                                @endfor
                            </flux:select>
                        </flux:field>

                        @can('institution.student_enrollment.update')
                        <div class="flex gap-3 items-end">
                            <flux:field class="flex-1">
                                <flux:label class="font-black! uppercase tracking-widest text-[10px] text-slate-500 mb-2">{{ __('Add Grade Level') }}</flux:label>
                                <flux:select wire:model="selectedGradeListId">
                                    <flux:select.option value="">{{ __('Choose grade...') }}</flux:select.option>
                                    @foreach($globalGrades as $gg)
                                    <flux:select.option value="{{ $gg->id }}">{{ $gg->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <flux:button variant="primary" wire:click="addGrade" class="bg-indigo-600! rounded-xl! h-[42px]">
                                <flux:icon name="plus" variant="mini" />
                            </flux:button>
                        </div>
                        @endcan
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach($grades as $grade)
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl group hover:border-indigo-200 transition-colors">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $grade->gradeList->name ?? 'N/A' }}</span>
                            @can('institution.student_enrollment.update')
                            <button wire:click="deleteGrade({{ $grade->id }})" wire:confirm="Are you sure? This will delete all classes in this grade for this year." class="text-slate-500 hover:text-rose-500 transition-colors">
                                <flux:icon name="x-mark" variant="micro" />
                            </button>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Class Management --}}
                @can('institution.student_enrollment.update')
                <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <flux:icon name="user-group" variant="solid" class="size-24" />
                    </div>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl">
                            <flux:icon name="user-group" variant="mini" class="text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ __('Class Management') }}</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label class="font-black! uppercase tracking-widest text-[10px] text-slate-500 mb-2">{{ __('Select Grade Level') }}</flux:label>
                            <flux:select wire:model="selectedGradeId">
                                <flux:select.option value="">{{ __('Choose grade...') }}</flux:select.option>
                                @foreach($grades as $grade)
                                <flux:select.option value="{{ $grade->id }}">{{ $grade->gradeList->name ?? 'N/A' }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="selectedGradeId" />
                        </flux:field>

                        <flux:field>
                            <flux:label class="font-black! uppercase tracking-widest text-[10px] text-slate-500 mb-2">{{ __('Class Section Name') }}</flux:label>
                            <flux:input wire:model="newClassName" placeholder="{{ __('e.g., A, B, or Lotus') }}" />
                            <flux:error name="newClassName" />
                        </flux:field>
                    </div>

                    <div class="mt-8">
                        <flux:field>
                            <flux:label class="font-black! uppercase tracking-widest text-[10px] text-slate-500 mb-3">{{ __('Medium of Instruction') }}</flux:label>
                            <flux:radio.group wire:model="selectedMediumId" variant="cards" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <flux:radio value="0" label="Sinhala" class="flex-col items-center! justify-center! py-4 rounded-2xl!" />
                                <flux:radio value="1" label="Tamil" class="flex-col items-center! justify-center! py-4 rounded-2xl!" />
                                <flux:radio value="2" label="English" class="flex-col items-center! justify-center! py-4 rounded-2xl!" />
                                <flux:radio value="3" label="Bilingual" class="flex-col items-center! justify-center! py-4 rounded-2xl!" />
                            </flux:radio.group>
                            <flux:error name="selectedMediumId" />
                        </flux:field>
                    </div>

                    <flux:button variant="primary" wire:click="addClass" class="mt-10 w-full bg-emerald-600! rounded-[1.25rem]! font-black uppercase tracking-[0.2em] text-[11px] py-5 shadow-lg shadow-emerald-200">
                        {{ __('Register New Class Section') }}
                    </flux:button>
                </div>
                @endcan
            </div>

            {{-- 3. Enrollment Table --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] shadow-sm overflow-hidden border-b-4 border-b-indigo-500">
                <div class="p-8 border-b border-slate-200 dark:border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ __('Enrollment Registry: ' . $academicYear) }}</h3>
                        <p class="text-sm text-slate-500 font-medium">{{ __('Record student population counts by gender for each classroom section.') }}</p>
                    </div>

                    <div class="flex items-center gap-4 bg-indigo-50 dark:bg-indigo-900/20 px-6 py-3 rounded-2xl border border-indigo-100 dark:border-indigo-800">
                        <div class="flex flex-col items-center border-r border-indigo-100 dark:border-indigo-800 pr-4">
                            <span class="text-[9px] font-black uppercase tracking-widest text-indigo-400 mb-1">{{ __('Total Students') }}</span>
                            <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 leading-none">
                                @php
                                $totalStudents = collect($enrollments)->sum(fn($e) => (int)($e['male'] ?? 0) + (int)($e['female'] ?? 0));
                                @endphp
                                {{ number_format($totalStudents) }}
                            </span>
                        </div>
                        <flux:icon name="user-group" variant="mini" class="text-indigo-400 opacity-50" />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">{{ __('Class / Section') }}</th>
                                <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">{{ __('Medium') }}</th>
                                <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">{{ __('Male') }}</th>
                                <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">{{ __('Female') }}</th>
                                <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">{{ __('Total') }}</th>
                                <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            @forelse($grades as $grade)
                            <tr class="bg-slate-50/30 dark:bg-slate-800/20">
                                <td colspan="6" class="px-8 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-500">{{ $grade->gradeList->name ?? 'N/A' }}</span>
                                        <span class="h-px flex-1 bg-indigo-100 dark:bg-indigo-900/20"></span>
                                    </div>
                                </td>
                            </tr>
                            @foreach($grade->classes as $class)
                            <tr class="hover:bg-indigo-50/20 transition-all group">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-black text-xs text-slate-500 group-hover:text-indigo-600 transition-colors shadow-xs">
                                            {{ $class->class_name }}
                                        </div>
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Class</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg">{{ $this->getMediumName($class->medium_id) }}</span>
                                </td>
                                <td class="px-8 py-4">
                                    @can('institution.student_enrollment.update')
                                    <flux:input wire:model="enrollments.{{ $class->id }}.male" type="number" class="text-center font-bold! border-blue-100! dark:border-blue-900/30!" />
                                    @else
                                    <div class="text-center font-bold text-slate-700 dark:text-slate-200">{{ $enrollments[$class->id]['male'] ?? 0 }}</div>
                                    @endcan
                                </td>
                                <td class="px-8 py-4">
                                    @can('institution.student_enrollment.update')
                                    <flux:input wire:model="enrollments.{{ $class->id }}.female" type="number" class="text-center font-bold! border-rose-100! dark:border-rose-900/30!" />
                                    @else
                                    <div class="text-center font-bold text-slate-700 dark:text-slate-200">{{ $enrollments[$class->id]['female'] ?? 0 }}</div>
                                    @endcan
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-10 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-black ring-1 ring-slate-200 dark:ring-slate-700 shadow-sm">
                                        {{ (int)($enrollments[$class->id]['male'] ?? 0) + (int)($enrollments[$class->id]['female'] ?? 0) }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    @can('institution.student_enrollment.update')
                                    <button wire:click="deleteClass({{ $class->id }})" wire:confirm="Are you sure you want to delete this class?" class="text-slate-300 hover:text-rose-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <flux:icon name="trash" variant="micro" />
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                            @empty
                            <tr>
                                <td colspan="6" class="py-24">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-6 bg-slate-50 dark:bg-slate-800 rounded-full mb-4">
                                            <flux:icon name="document-plus" variant="outline" class="w-12 h-12 text-slate-300" />
                                        </div>
                                        <h4 class="text-lg font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight">{{ __('No Configuration for ' . $academicYear) }}</h4>
                                        <p class="mt-2 text-sm text-slate-500 max-w-xs text-center">{{ __('Start by copying from previous year or adding grades manually.') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @can('institution.student_enrollment.update')
                @if(count($enrollments) > 0)
                <div class="p-8 bg-slate-50/50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                    <flux:button variant="primary" wire:click="saveEnrollment" class="bg-indigo-600! px-10! rounded-xl! font-black uppercase tracking-[0.2em] text-[11px] py-4 shadow-xl shadow-indigo-200">
                        {{ __('Persist Enrollment Data') }}
                    </flux:button>
                </div>
                @endif
                @endcan
            </div>
        </div>
    </x-institutions.institution-layout>
</section>