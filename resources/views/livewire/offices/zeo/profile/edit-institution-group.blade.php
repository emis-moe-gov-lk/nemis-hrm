<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="text-slate-900 dark:text-white">{{ __('Zonal Education Office') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6 text-slate-500 dark:text-slate-400">
            {{ __('Statistics about Zonal Education Office structure and staff distribution.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.zeo.zeo-layout :officeId="$officeId">

        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                {{ __('Edit Institution Group') }}
            </h2>
            <a href="{{ route('offices.zeo.profile.institution-groups.view', ['id' => $officeId, 'groupCode' => $groupCode]) }}" wire:navigate>
                <flux:button variant="ghost" icon="arrow-left" class="shadow-sm">
                    {{ __('Back to Group') }}
                </flux:button>
            </a>
        </div>

        <div class="mx-auto">
            <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:p-10">
                <div class="absolute right-0 top-0 -z-10 h-64 w-64 rounded-bl-full bg-linear-to-bl from-blue-100 to-transparent opacity-70 dark:from-blue-900/20"></div>

                <form wire:submit.prevent="updateInstitutionGroup" class="relative z-10 space-y-8">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Group Name') }}</flux:label>
                            <flux:input wire:model="groupName" placeholder="e.g. Set 01" required />
                            <flux:error name="groupName" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Group Assigned Officer') }}</flux:label>
                            <flux:select wire:model="assignedPerson" placeholder="Select officer">
                                @foreach ($officersList as $officer)
                                    <flux:select.option :value="$officer->employee_id">{{ $officer->employee->name_with_initials }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="assignedPerson" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Description') }}</flux:label>
                        <flux:textarea wire:model="description" placeholder="Brief description of the group's characteristics..." rows="3" />
                        <flux:error name="description" />
                    </flux:field>

                    <div class="flex items-start gap-4">
                        <flux:field class="flex-1">
                            <flux:label>{{ __('Institution') }}</flux:label>
                            <flux:select wire:model="institutionId">
                                <flux:select.option value="">Select institutions</flux:select.option>
                                @foreach ($institutionList as $institution)
                                    <flux:select.option value="{{ $institution->id }}">{{ str_pad($institution['census_no'], 5, '0', STR_PAD_LEFT) }} - {{ $institution->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="institutionId" />
                        </flux:field>

                        <flux:field>
                            <flux:label class="opacity-0">{{ __('Action') }}</flux:label>
                            <flux:button type="button" wire:click="addInstitution" variant="primary" icon="plus" class="shadow-sm">
                                {{ __('Add Institution') }}
                            </flux:button>
                        </flux:field>
                    </div>

                    @if (count($selectedInstitutions) > 0)
                        <div class="space-y-3">
                            <flux:label>{{ __('Selected Institutions') }}</flux:label>
                            @foreach ($selectedInstitutions as $index => $institution)
                                <div class="mt-1 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/50">
                                    <div class="flex-1">
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $institution['name'] }}</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            Census No: {{ str_pad($institution['census_no'], 5, '0', STR_PAD_LEFT) }}
                                        </p>
                                    </div>
                                    <flux:button type="button" wire:click="removeInstitution({{ $index }})" variant="ghost" size="sm" class="shrink-0">
                                        {{ __('Remove') }}
                                    </flux:button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <flux:separator variant="subtle" />

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('offices.zeo.profile.institution-groups.view', ['id' => $officeId, 'groupCode' => $groupCode]) }}" wire:navigate>
                            <flux:button variant="ghost">
                                {{ __('Cancel') }}
                            </flux:button>
                        </a>
                        <flux:button type="submit" variant="primary" icon="check" class="shadow-sm">
                            {{ __('Update Group') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>

    </x-offices.zeo.zeo-layout>
</section>
