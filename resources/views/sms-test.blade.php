<x-layouts.app :title="__('Test SMS')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <x-page-header
            title="SMS Alert Check Form"
            subtitle="Test sending an SMS message to a specific mobile number."
            icon="device-phone-mobile">
        </x-page-header>

        <div class="max-w-3xl">
            <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-3xl p-6 md:p-8 shadow-sm">
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="mb-6 p-4 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20 text-sm font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('sms.test.send') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="mobile" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Mobile Number</label>
                        <input type="text" id="mobile" name="mobile" 
                            class="block w-full rounded-lg border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 transition-colors @error('mobile') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            placeholder="e.g. 07XXXXXXXX or 947XXXXXXXX" 
                            value="{{ old('mobile') }}" required>
                        @error('mobile')
                            <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Message</label>
                        <textarea id="message" name="message" rows="4" 
                            class="block w-full rounded-lg border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 transition-colors @error('message') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            required>{{ old('message', 'Test message from CEMIS') }}</textarea>
                        @error('message')
                            <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                            class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all">
                            <flux:icon.paper-airplane variant="micro" />
                            Send SMS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
