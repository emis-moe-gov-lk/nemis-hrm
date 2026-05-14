<div>
    <section>
        {{-- Header Section --}}
        <div class="mb-6">
            <div class="flex items-center justify-between pb-3">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-100 dark:bg-indigo-900/30 p-2 rounded-lg">
                        <flux:icon name="academic-cap" variant="outline" class="text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <flux:heading size="lg">{{ __('SLEAS Info') }}</flux:heading>
                        <flux:badge variant="pill" size="sm" color="blue" class="mt-1">
                            {{ $employee->appointment->service->service_name ?? 'N/A' }}
                        </flux:badge>
                    </div>
                </div>

                @can('sleas.profile.employment.sleas-information.update')
                    <flux:modal.trigger name="sleas-data-edit">
                        <flux:button icon="pencil-square" size="sm" variant="ghost">Edit Details</flux:button>
                    </flux:modal.trigger>
                @endcan
            </div>
            <flux:separator variant="subtle" />
        </div>

        {{-- Information Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {{-- Recruitment Category --}}
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Recruitment Category') }}</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ $sleasData->recruitmentCategory?->category_name ?? 'N/A' }}
                </p>
            </div>

            {{-- Recruitment Subject --}}
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Recruitment Subject') }}</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ $sleasData->serviceSubject?->subject ?? 'N/A' }}
                </p>
            </div>

            {{-- Cadre Medium --}}
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Cadre Medium') }}</p>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $cadreData->medium?->name ?? 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Cadre Subject --}}
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Cadre Subject') }}</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ $cadreData->mainSubject?->name_en ?? 'N/A' }}
                </p>
            </div>
        </div>
    </section>

    {{-- Update Modal --}}
    @can('sleas.profile.employment.sleas-information.update')
    <flux:modal name="sleas-data-edit" class="md:w-150">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Edit SLEAS Data') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Update recruitment categories, subjects, and cadre details below.') }}</flux:text>
            </div>

            <flux:separator variant="subtle" />

            <form wire:submit.prevent="save" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:select label="{{ __('SLEAS Recruitment Category') }}" wire:model.live.debounce.150ms="sleasCategory" placeholder="Select category...">
                            <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                            @foreach ($sleasCategoriesOption as $data)
                                <flux:select.option value="{{ $data->category_id }}">{{ $data->category_name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:select label="{{ __('SLEAS Recruitment Subject') }}" wire:model.live.debounce.150ms="sleasSubject" placeholder="Select subject...">
                            <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                            @foreach ($sleasSubjectsOption as $data)
                                <flux:select.option value="{{ $data->eas_subject_id }}">{{ $data->subject }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:select label="{{ __('Cadre Medium') }}" wire:model.live.debounce.150ms="cadreMedium" placeholder="Select medium...">
                            <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                            @foreach ($mediumOption as $medium)
                                <flux:select.option value="{{ $medium->medium_id }}">{{ $medium->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:select label="{{ __('Cadre Subject') }}" wire:model.live.debounce.150ms="cadreSubject" placeholder="Select subject...">
                            <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                            @foreach ($subjectOption as $data)
                                <flux:select.option value="{{ $data->subject_id }}">{{ $data->name_en }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <div class="flex items-center pt-4 border-t border-gray-100 dark:border-gray-800">
                    <flux:spacer />
                    
                    <div class="flex gap-3">
                        <flux:button type="button" variant="ghost" wire:click="resetFields">{{ __('Reset') }}</flux:button>
                        <flux:button type="submit" variant="primary" icon="check">{{ __('Save Changes') }}</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>
    @endcan
</div>