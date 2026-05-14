<section class="w-full">
    {{-- Page Header --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Provincial Department of Education') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Statistics about provincial education structure and staff distribution.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.peo.peo-layout :officeId="$officeId">

        {{-- Table --}}
        <div class="space-y-2">
            @foreach ($zonalServiceCounts as $zeoName => $services)
            <div class="border-b border-gray-200">
                <!-- Accordion Trigger -->
                <button class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
                    onclick="toggleAccordion(this)">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $zeoName }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ count($services) }} services • {{ number_format(array_sum($services)) }} total</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0 ml-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Accordion Content -->
                <div class="hidden px-4 pb-4">
                    <div class="pt-2">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($services as $serviceId => $count)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 whitespace-nowrap">
                                        <span class="text-gray-700">{{ $serviceId }}</span>
                                    </td>
                                    <td class="py-2 whitespace-nowrap text-right">
                                        <span class="font-medium text-gray-900">{{ number_format($count) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <script>
            function toggleAccordion(button) {
                const content = button.nextElementSibling;
                const icon = button.querySelector('svg');

                // Close other open accordions (optional)
                document.querySelectorAll('.accordion-content').forEach(item => {
                    if (item !== content && !item.classList.contains('hidden')) {
                        item.classList.add('hidden');
                        const otherIcon = item.previousElementSibling.querySelector('svg');
                        if (otherIcon) otherIcon.classList.remove('rotate-180');
                    }
                });

                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            }
        </script>



    </x-offices.peo.peo-layout>
</section>