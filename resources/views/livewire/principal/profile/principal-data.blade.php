<div class="space-y-8">
    {{-- Section Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/20 rounded-xl shrink-0">
                <flux:icon.academic-cap class="size-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">{{ __('Principal Information') }}</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">{{ __('Administrative category & cadre details') }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-3 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-200 dark:border-zinc-700">
            @can('principal.profile.employment.principal-information.update')
            <flux:modal.trigger name="principal-data-edit">
                <flux:button variant="ghost" size="sm" icon="pencil-square" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-4 text-slate-600 dark:text-zinc-300 hover:border-indigo-400 hover:text-indigo-600 transition-all">{{ __('Edit Details') }}</flux:button>
            </flux:modal.trigger>
            @endcan
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

        {{-- Recruitment Category --}}
        <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
            <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Recruitment Category') }}</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase">{{ $principalData->recruitmentCategory->category_name ?? 'N/A' }}</span>
        </div>

        {{-- Cadre Medium --}}
        <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
            <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Cadre Medium') }}</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase">{{ $cadreData->medium->name ?? 'N/A' }}</span>
        </div>

        {{-- Cadre Subject --}}
        <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
            <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('Cadre Subject') }}</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase">{{ $cadreData->mainSubject->name_en ?? 'N/A' }}</span>
        </div>

    </div>

    {{-- Update Modal --}}
    @can('principal.profile.employment.principal-information.update')
    <flux:modal name="principal-data-edit" class="md:w-150">
        <div class="space-y-6">
            <div class="pb-4 border-b border-slate-200 dark:border-zinc-700">
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">{{ __('Update Principal Data') }}</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">{{ __('Update recruitment categories and cadre details below') }}</p>
            </div>

            <form wire:submit.prevent="save" class="space-y-6">
                @csrf
                
                <flux:select label="{{ __('Principal Recruitment Category') }}" wire:model.live="principalCategory">
                    <option value="">{{ __('Select') }}</option>
                    @foreach ($principalCategoriesOption as $data)
                        <option value="{{ $data->category_id }}">{{ $data->category_name }}</option>
                    @endforeach
                </flux:select>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select label="{{ __('Cadre Medium') }}" wire:model.live="cadreMedium">
                        <option value="">{{ __('Select') }}</option>
                        @foreach ($mediumOption as $medium)
                            <option value="{{ $medium->medium_id }}">{{ $medium->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="{{ __('Cadre Subject') }}" wire:model.live="cadreSubject">
                        <option value="">{{ __('Select') }}</option>
                        @foreach ($subjectOption as $data)
                            <option value="{{ $data->subject_id }}">{{ $data->name_en }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-200 dark:border-zinc-700">
                    <flux:button type="button" variant="ghost" wire:click="resetFields" class="font-bold rounded-xl h-11">{{ __('Reset') }}</flux:button>
                    <flux:button type="submit" variant="primary" class="font-black rounded-xl h-11 bg-indigo-600 dark:bg-white text-white dark:text-slate-900">{{ __('Save Changes') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endcan
</div>