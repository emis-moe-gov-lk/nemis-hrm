<x-layouts.app :title="__('Test Email')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <x-page-header
            title="Email Alert Check Form"
            subtitle="Test sending an email to verify system SMTP configuration."
            icon="envelope">
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

                <form action="{{ route('mail.test.send') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" 
                            class="block w-full rounded-lg border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 transition-colors @error('email') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            placeholder="e.g. test@example.com" 
                            value="{{ old('email') }}" required>
                        @error('email')
                            <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Subject</label>
                        <input type="text" id="subject" name="subject" 
                            class="block w-full rounded-lg border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 transition-colors @error('subject') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            placeholder="Test Email Subject" 
                            value="{{ old('subject') ?? 'System Test Email' }}" required>
                        @error('subject')
                            <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Message Content</label>
                        <textarea id="message" name="message" rows="4" 
                            class="block w-full rounded-lg border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 transition-colors @error('message') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            placeholder="Type your test message here..." required>{{ old('message') ?? 'This is a test email sent from the CEMIS system.' }}</textarea>
                        @error('message')
                            <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" 
                            class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors dark:focus:ring-offset-zinc-900 w-full sm:w-auto">
                            Send Test Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
