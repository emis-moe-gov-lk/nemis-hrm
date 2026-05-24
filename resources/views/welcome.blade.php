<x-layouts.public title="EMIS - National Education Management System">
    <style>
        @keyframes gradient-x {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-gradient-x {
            background-size: 200% 200%;
            animation: gradient-x 10s ease infinite;
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 0.1;
            }

            50% {
                opacity: 0.2;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes fade-in-up {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }
    </style>

    <!-- Hero Section -->
    <section class="relative overflow-hidden min-h-dvh bg-gray-950 text-white flex items-center pt-24">
        <div aria-hidden="true" class="absolute inset-0 z-0 bg-linear-to-br from-gray-900 via-indigo-950 to-blue-950 opacity-95"></div>

        <!-- Animated Background Elements -->
        <div aria-hidden="true" class="absolute top-0 right-0 w-full h-full lg:w-2/3 transform skew-y-3 origin-top-right bg-linear-to-bl from-blue-600/10 to-transparent"></div>
        <div aria-hidden="true" class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500 rounded-full opacity-10 blur-3xl animate-pulse-slow"></div>
        <div aria-hidden="true" class="absolute bottom-0 right-0 w-full h-px bg-linear-to-r from-transparent via-blue-500 to-transparent opacity-30"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 w-full relative z-20 flex flex-col lg:flex-row items-center gap-16 py-20">
            <div class="flex-1 text-center lg:text-left animate-fade-in-up">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-300 text-xs font-bold tracking-widest uppercase mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    Next-Gen Education Infrastructure
                </div>

                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white leading-[1.1] tracking-tight mb-6">
                    Transforming <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-400 to-cyan-300">National Education</span> Through Data
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-indigo-100/80 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                    The Government Education Management System provides a unified, secure, and intelligent platform for data-driven policy making and institutional excellence across Sri Lanka.
                </p>

                <div class="mt-10 flex flex-wrap justify-center lg:justify-start gap-4 sm:gap-6">
                    <a href="/login" class="group relative px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-lg shadow-lg shadow-blue-900/20 transition-all duration-300 hover:-translate-y-1">
                        <span class="flex items-center gap-3">
                            Enter Portal
                            <flux:icon name="arrow-right" class="w-5 h-5 transition-transform group-hover:translate-x-1" />
                        </span>
                    </a>
                    <a href="#features" class="px-8 py-4 rounded-xl bg-white/5 border border-white/10 text-white font-semibold text-lg backdrop-blur-md hover:bg-white/10 transition-all duration-300">
                        Explore Features
                    </a>
                </div>
            </div>

            <div class="flex-1 w-full max-w-2xl animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="relative group">
                    <!-- Decorative Frame -->
                    <div class="absolute -inset-1 bg-linear-to-r from-blue-500 to-cyan-500 rounded-2xl blur-md opacity-25 group-hover:opacity-40 transition duration-1000"></div>

                    <div class="relative bg-gray-900 border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
                        <!-- Terminal-like Header -->
                        <div class="flex items-center gap-2 px-4 py-3 bg-white/5 border-b border-white/5">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-500/50"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-500/50"></div>
                            </div>
                            <div class="mx-auto text-[10px] font-mono text-gray-500 tracking-widest uppercase">System Status: Active</div>
                        </div>

                        <!-- Content Placeholder for Premium Look -->
                        <div class="p-8 space-y-6">
                            <div class="flex items-center justify-between">
                                <div class="space-y-2">
                                    <div class="h-2 w-24 bg-blue-500/20 rounded-full"></div>
                                    <div class="h-4 w-48 bg-white/10 rounded-full"></div>
                                </div>
                                <div class="h-10 w-10 bg-blue-500/10 rounded-lg border border-blue-500/20 flex items-center justify-center">
                                    <div class="w-4 h-4 text-blue-400">📊</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-white/5 border border-white/5 rounded-xl">
                                    <div class="text-xs text-gray-400 mb-1">Total Institutions</div>
                                    <div class="text-2xl font-black text-white">10,150+</div>
                                </div>
                                <div class="p-4 bg-white/5 border border-white/5 rounded-xl">
                                    <div class="text-xs text-gray-400 mb-1">Active Educators</div>
                                    <div class="text-2xl font-black text-white">245k+</div>
                                </div>
                            </div>

                            <div class="p-4 bg-linear-to-r from-blue-600/20 to-transparent border-l-4 border-blue-500 rounded-r-xl">
                                <div class="text-[10px] font-bold text-blue-400 uppercase tracking-tighter mb-1">Live Intelligence</div>
                                <div class="text-sm text-indigo-100">National resource distribution optimized for {{ now()->year }}.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-24 bg-gray-50 dark:bg-zinc-800/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-blue-600 dark:text-blue-400 font-bold tracking-widest uppercase text-sm mb-4">The EMIS Advantage</h2>
                <p class="text-4xl font-black text-gray-900 dark:text-white sm:text-5xl tracking-tight">Everything you need to manage educational excellence.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8">
                <!-- Staff Enrolment -->
                <div class="group p-8 bg-white dark:bg-zinc-900 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm hover:shadow-xl hover:border-blue-500/50 transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <flux:icon name="user-group" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Staff Enrolment</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Register teaching and non-academic staff with verified personal, appointment, service, and workplace records.</p>
                </div>

                <!-- Staff Promotions -->
                <div class="group p-8 bg-white dark:bg-zinc-900 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm hover:shadow-xl hover:border-blue-500/50 transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <flux:icon name="arrow-trending-up" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Staff Promotions</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Manage promotion eligibility, service history, approval steps, and appointment updates through a structured workflow.</p>
                </div>

                <!-- Teacher Transfer -->
                <div class="group p-8 bg-white dark:bg-zinc-900 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm hover:shadow-xl hover:border-blue-500/50 transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <flux:icon name="arrows-up-down" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Teacher Transfer</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Support annual transfer requests, category-based preferences, policy rules, and transparent application tracking.</p>
                </div>

                <!-- Transfer Boards and Appeal Boards -->
                <div class="group p-8 bg-white dark:bg-zinc-900 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm hover:shadow-xl hover:border-blue-500/50 transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <flux:icon name="clipboard-document-list" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Transfer Boards &amp; Appeal Boards</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Coordinate zonal, provincial, and ministry board decisions, appeal reviews, attendance, and final reports.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-gray-950 overflow-hidden relative">
        <div aria-hidden="true" class="absolute inset-0 bg-linear-to-br from-blue-600/10 via-indigo-600/5 to-transparent opacity-50"></div>
        <div aria-hidden="true" class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-500 rounded-full opacity-10 blur-3xl animate-pulse-slow"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-4xl sm:text-5xl font-black text-white mb-8 tracking-tight">Ready to modernize your institution?</h2>
            <p class="text-xl text-blue-100/70 mb-10 max-w-2xl mx-auto">Join the national initiative to digitize and empower the Sri Lankan education system.</p>
            <div class="flex flex-wrap justify-center gap-6">
                <a href="/login" class="px-10 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-500 shadow-lg shadow-blue-900/20 transition-all duration-300 hover:-translate-y-1">Get Started Now</a>
                <a href="#contact-us" class="px-10 py-4 bg-white/5 border border-white/10 text-white font-bold rounded-xl backdrop-blur-md hover:bg-white/10 transition-all duration-300">Contact Support</a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact-us" class="py-24 bg-white dark:bg-zinc-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-16">
                <div class="flex-1">
                    <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-6">Contact the Ministry</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-10">Have questions about the EMIS platform? Our technical team and the Ministry of Education are here to assist you.</p>

                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                                <flux:icon name="map-pin" class="w-5 h-5" />
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 font-medium leading-relaxed">Isurupaya, Pelawatta, Battaramulla, Sri Lanka</div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                                <flux:icon name="envelope" class="w-5 h-5" />
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 font-medium leading-relaxed">info@moe.gov.lk</div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                                <flux:icon name="phone" class="w-5 h-5" />
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 font-medium leading-relaxed">+94 112 785 141</div>
                        </div>
                    </div>
                </div>

                <div class="flex-1">
                    <div class="p-8 bg-gray-50 dark:bg-zinc-800 rounded-3xl border border-gray-200 dark:border-white/5">
                        <livewire:contact-form />
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
