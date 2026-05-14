<section class="w-full">
    <div class="relative mb-8 w-full">
        <flux:heading size="xl" level="1" class="text-gray-900 dark:text-white font-bold">
            {{ __('Transfer Requests') }}
        </flux:heading>
        <flux:subheading size="lg" class="mb-6 text-gray-600 dark:text-gray-300">
            {{ __('View the list of staff members and their transfer requests.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-institutions.institution-layout :institutionId="$id" :institution="$institution">
        <div class="mt-6">
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700 shadow-sm transition-all duration-300 hover:shadow-md">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-slate-300 uppercase tracking-wider border-r border-gray-200 dark:border-slate-700 last:border-r-0">
                                {{ __('#') }}
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-slate-300 uppercase tracking-wider border-r border-gray-200 dark:border-slate-700 last:border-r-0">
                                {{ __('Applicant') }}
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-slate-300 uppercase tracking-wider border-r border-gray-200 dark:border-slate-700 last:border-r-0">
                                {{ __('Service Years') }}
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-slate-300 uppercase tracking-wider border-r border-gray-200 dark:border-slate-700 last:border-r-0">
                                {{ __('Teaching subjects') }}
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-slate-300 uppercase tracking-wider bg-slate-50 dark:bg-slate-800">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                        @forelse ($transferRequests as $key=>$request )
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors duration-200 group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-slate-700 last:border-r-0">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-full text-xs font-black ring-1 ring-slate-200 dark:ring-slate-700 shadow-sm">
                                    {{ $key + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200 dark:border-slate-700 last:border-r-0 text-sm">
                                <div class="flex flex-col">
                                    <a href="{{route('teacher.profile.index', $request->employee->id)}}" wire:navigate class="cursor-pointer">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $request->employee->full_name ?? 'N/A' }}</span>
                                            <span class="text-xs text-slate-500 font-medium">{{ $request->employee->nic ?? 'N/A' }}</span>
                                            <span class="text-xs text-slate-500 font-medium">{{ $request->transfer_application_id ?? 'N/A' }}</span>
                                        </div>
                                    </a>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200 dark:border-slate-700 last:border-r-0 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20" title="Total duration at current station">
                                        {{ $request->current_workplace_service_years ?? 'N/A' }}
                                    </span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Appoinment Date: {{ $request->current_workplace_join_date?->format('Y-m-d') ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200 dark:border-slate-700 last:border-r-0 text-sm">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-900 dark:text-white">
                                        {{ $request->teacher->mainSubject->subject_name ?? $request->teacher->appointmentSubject->subject_name ?? 'Not Set' }}
                                    </span>
                                    @if($request->teacher?->secondary_subject)
                                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                        {{ $request->teacher->secondarySubject->subject_name ?? '' }}
                                    </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                @if($request->recommendations->isNotEmpty())
                                <flux:button size="sm" variant="ghost" icon="check-circle" wire:click="openRecommendationModal('{{ $request->id }}')" class="text-emerald-600 dark:text-emerald-400">
                                    {{ __('Update') }}
                                </flux:button>
                                @else
                                <flux:button size="sm" variant="ghost" icon="check-circle" wire:click="openRecommendationModal('{{ $request->id }}')">
                                    {{ __('Recommend') }}
                                </flux:button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="h-12 w-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        <flux:icon.table-cells class="size-6 text-slate-400" />
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('No Incoming Requests') }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto">
                                        {{ __('There are no teacher transfer applications targeting this institution at the moment.') }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-institutions.institution-layout>

    <!-- Recommendation Modal -->
    <flux:modal wire:model="showRecommendationModal" class="md:w-[600px] space-y-6">
        <div>
            <flux:heading size="lg">
                {{ __('Transfer Recommendation') }}
            </flux:heading>
            <flux:subheading>
                {{ __('Provide your formal recommendation for') }} <span class="font-bold text-slate-900 dark:text-white">{{ $selectedApplication?->employee?->full_name }}</span>
            </flux:subheading>
        </div>

        <div class="space-y-6">
            <flux:select wire:model="recommendationDecision" label="{{ __('Recommendation Decision') }}" placeholder="{{ __('Select a decision...') }}">
                @foreach($recommendationOptions as $option)
                    <flux:select.option value="{{ $option->transfer_recommendation_list_id }}">
                        {{ $option->decision }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="recommendationRemarks" label="{{ __('Remarks / Comments') }}" placeholder="{{ __('Enter any additional notes or justification...') }}" rows="4" />
        </div>

        <div class="flex justify-end gap-3">
            <flux:button variant="ghost" wire:click="closeRecommendationModal">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button variant="primary" wire:click="submitRecommendation">
                {{ __('Submit Recommendation') }}
            </flux:button>
        </div>
    </flux:modal>
</section>