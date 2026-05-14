<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>EducationApp</title>

    {{-- <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png"> --}}

    <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
    <link rel="shortcut icon" href="/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
    <link rel="manifest" href="/favicon/site.webmanifest" />

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

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

        @keyframes fade-in-left {
            0% {
                opacity: 0;
                transform: translateX(-20px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-left {
            animation: fade-in-left 0.8s ease-out forwards;
        }

        @keyframes fade-in-right {
            0% {
                opacity: 0;
                transform: translateX(20px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-right {
            animation: fade-in-right 0.8s ease-out forwards;
        }
    </style>

    <style>
        .animated-border {
            background: linear-gradient(270deg,
                    #ff0080,
                    #7928ca,
                    #2afadf,
                    #ffcd00);
            background-size: 600% 600%;
            animation: borderMove 6s ease infinite;
        }

        @keyframes borderMove {
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
    </style>


</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <div>
        @include('header')
    </div>

    <main>
        <section
            class="relative overflow-hidden min-h-dvh bg-gray-950 text-white pt-32 pb-32 lg:pt-40 lg:pb-40 flex items-center">
            <div aria-hidden="true"
                class="absolute inset-0 z-0 bg-gradient-to-br from-gray-900 to-indigo-950 opacity-90"></div>
            <div aria-hidden="true"
                class="absolute top-0 right-0 w-full h-full lg:w-2/3 transform skew-y-3 origin-top-right bg-gradient-to-bl from-blue-900/50 to-indigo-900/50">
            </div>
            <div aria-hidden="true"
                class="absolute top-1/4 right-1/4 w-96 h-96 bg-blue-500 rounded-full opacity-10 blur-3xl animate-pulse-slow z-0 hidden lg:block">
            </div>
            <div
                class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-blue-400 via-indigo-500 to-blue-400 animate-gradient-x z-30">
            </div>


            <div
                class="max-w-7xl mx-auto px-6 lg:px-8 w-full relative z-20 flex flex-col lg:flex-row items-center gap-16">

                <div class="flex-1 text-center lg:text-left pt-10 lg:pt-0 animate-fade-in-left">
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white leading-tight tracking-tighter mb-4"
                        style="text-shadow: 0 0 5px rgba(59, 130, 246, 0.5);">
                        <span
                            class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300">National
                            Data</span>
                        for Public Education
                    </h1>
                    <p
                        class="mt-6 text-lg sm:text-xl text-indigo-200 max-w-2xl mx-auto lg:mx-0 opacity-90 border-l-4 border-blue-500 pl-4">
                        The **Government Education Management System** ensures transparent, secure, and unified data
                        collection, empowering administrators and policy-makers to drive national educational success.
                    </p>
                    <div class="mt-10 lg:mt-12 flex flex-wrap justify-center lg:justify-start gap-4 sm:gap-5">
                        <a href="/login"
                            class="group relative overflow-hidden px-6 sm:px-8 py-3.5 sm:py-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-base sm:text-lg font-extrabold shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                            <span class="relative z-10 flex items-center gap-2">Access Portal
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </span>

                        </a>
                        <a href="#features"
                            class="px-6 sm:px-8 py-3.5 sm:py-4 rounded-full bg-white/10 text-blue-300 border border-blue-500/50 hover:bg-white/20 text-base sm:text-lg font-semibold backdrop-blur-sm transition-colors duration-300">
                            View Policy Brief
                        </a>
                    </div>
                    <div
                        class="mt-10 lg:mt-16 text-xs text-indigo-300 flex justify-center lg:justify-start items-center space-x-3">
                        <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="uppercase tracking-widest font-medium">An initiative of the Ministry of
                            Education</span>
                    </div>
                </div>

                <div
                    class="flex-1 flex flex-col justify-center items-center relative lg:pl-16 mt-16 lg:mt-10 animate-fade-in-right">

                    <div
                        class="relative w-full max-w-sm sm:max-w-md lg:max-w-[600px] h-64 sm:h-72 lg:h-96 transform rotate-2">

                        <div
                            class="absolute inset-0 border border-indigo-600/50 bg-gray-900/60 rounded-xl shadow-2xl shadow-indigo-500/20">

                            <div class="absolute inset-x-0 top-0 h-1 bg-blue-400/50 blur-sm"></div>

                            <div class="absolute inset-y-0 left-4 w-1 bg-gray-800/50"></div>
                            <div class="absolute inset-y-0 right-4 w-1 bg-gray-800/50"></div>

                            <div
                                class="absolute left-6 right-6 top-6 h-8 bg-blue-800/40 border border-blue-400/50 flex items-center justify-between px-3 text-xs">
                                <span class="text-blue-300 font-mono">// HRMS-MODULE-01</span>
                                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse-slow"></div>
                            </div>

                            <div
                                class="absolute left-6 right-6 top-[4.5rem] h-8 bg-blue-800/40 border border-blue-400/50 flex items-center justify-between px-3 text-xs">
                                <span class="text-blue-300 font-mono">// SCHOOLS-MODULE-02</span>
                                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse-slow"></div>
                            </div>

                            <div
                                class="absolute left-6 right-6 top-[8rem] h-8 bg-indigo-800/40 border border-indigo-400/50 flex items-center justify-between px-3 text-xs">
                                <span class="text-indigo-300 font-mono">// ASSESMENT-MODULE-03</span>
                                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                            </div>

                            <div
                                class="absolute inset-0 bg-gradient-to-tr from-blue-900/10 to-indigo-900/10 opacity-70">
                            </div>

                        </div>

                        <div
                            class="absolute top-5 left-1/2 transform -translate-x-1/2 p-2 bg-gray-800/80 backdrop-blur-sm rounded-lg border border-blue-400/50 shadow-lg transition-transform duration-300 hover:scale-105">
                            <p class="text-xs font-semibold text-blue-400">NATIONAL DMS CORE</p>
                        </div>

                    </div>


                    <div
                        class="w-full max-w-xs sm:max-w-md p-6 bg-gray-800/60 backdrop-blur-xl rounded-2xl shadow-3xl shadow-blue-500/10 border border-blue-400/30 transform -rotate-1 transition-all duration-500 hover:rotate-0 group 
                       relative mt-10 lg:absolute lg:-bottom-16 sm:left-4 lg:left-0 z-30">

                        <p class="text-sm font-semibold text-blue-400 mb-4 uppercase tracking-widest">Key Performance
                            Metrics</p>
                        <div class="grid grid-cols-2 gap-4">

                            <div
                                class="flex flex-col p-3 bg-gray-900/50 rounded-lg group-hover:bg-gray-700/50 transition-colors">
                                <span class="text-xs text-gray-400">Student Retention</span>
                                <span class="text-3xl font-extrabold text-blue-400">92.5%</span>
                            </div>

                            <div
                                class="flex flex-col p-3 bg-gray-900/50 rounded-lg group-hover:bg-gray-700/50 transition-colors">
                                <span class="text-xs text-gray-400">Annual Growth</span>
                                <span class="text-3xl font-extrabold text-blue-400">+12%</span>
                            </div>

                            <div
                                class="col-span-2 mt-2 flex items-center justify-between p-3 bg-gray-900/50 rounded-lg group-hover:bg-gray-700/50 transition-colors">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-400">Total System Users</span>
                                    <span class="text-2xl font-extrabold text-blue-400">4.3M+</span>
                                </div>

                                <div
                                    class="w-16 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full opacity-70">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative py-20 sm:py-28 bg-cover bg-center"
            style="background-image: url('/images/education-bg.png');">

            <div
                class="absolute inset-0 bg-gradient-to-br from-blue-50/90 via-white/90 to-blue-100/90 dark:from-gray-900/90 dark:via-gray-950/90 dark:to-blue-950/90">
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-lg text-blue-700 dark:text-cyan-400 font-semibold tracking-wider uppercase">
                        EMIS PROFESSIONAL SUITE
                    </h2>
                    <p class="mt-2 text-4xl font-extrabold text-blue-900 dark:text-white tracking-tight sm:text-5xl">
                        A Unified Platform for Educational Excellence
                    </p>
                    <p class="mt-4 max-w-3xl text-xl text-gray-700 dark:text-gray-300 lg:mx-auto">
                        Our modular, enterprise-grade system provides integrated management tools across every facet of
                        your institution.
                    </p>
                </div>

                <div class="mt-16 grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-3">

                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-cyan-400 rounded-lg opacity-0 group-hover:opacity-100 transition duration-300">
                        </div>
                        <div
                            class="relative p-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 h-full transform group-hover:shadow-2xl transition duration-300">
                            <div class="p-3 inline-block bg-blue-600 rounded-md shadow-lg">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l9-5-9-5-9 5 9 5zm0 0v8" />
                                </svg>
                            </div>
                            <h3
                                class="mt-6 text-xl font-bold tracking-tight text-blue-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition duration-300">
                                Student Data Management
                            </h3>
                            <p class="mt-4 text-base text-gray-700 dark:text-gray-400">
                                Centralized records for enrollment, attendance, demographics, and secure health
                                information tracking.
                            </p>
                        </div>
                    </div>

                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-cyan-400 rounded-lg opacity-0 group-hover:opacity-100 transition duration-300">
                        </div>
                        <div
                            class="relative p-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 h-full transform group-hover:shadow-2xl transition duration-300">
                            <div class="p-3 inline-block bg-blue-600 rounded-md shadow-lg">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m4 0h6m-3-6v6m4-6V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v6m-4-6V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v6" />
                                </svg>
                            </div>
                            <h3
                                class="mt-6 text-xl font-bold tracking-tight text-blue-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition duration-300">
                                Performance & Reporting
                            </h3>
                            <p class="mt-4 text-base text-gray-700 dark:text-gray-400">
                                Generate instant reports on academic progress, resource allocation, and budget tracking
                                for informed strategy.
                            </p>
                        </div>
                    </div>

                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-cyan-400 rounded-lg opacity-0 group-hover:opacity-100 transition duration-300">
                        </div>
                        <div
                            class="relative p-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 h-full transform group-hover:shadow-2xl transition duration-300">
                            <div class="p-3 inline-block bg-blue-600 rounded-md shadow-lg">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20v-2c0-.656-.126-1.283-.356-1.857M9 20H4v-2a3 3 0 015-2.236M9 20v-2a3 3 0 00-3.14-2.813M15 16.5a4.5 4.5 0 10-9 0M7.25 18H5a2 2 0 00-2 2v2M19 18h-2.25M12 11a4.5 4.5 0 100-9 4.5 4.5 0 000 9z" />
                                </svg>
                            </div>
                            <h3
                                class="mt-6 text-xl font-bold tracking-tight text-blue-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition duration-300">
                                HR & Staff Management
                            </h3>
                            <p class="mt-4 text-base text-gray-700 dark:text-gray-400">
                                Manage staff profiles, payroll data, leave requests, performance appraisals, and
                                professional development.
                            </p>
                        </div>
                    </div>

                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-cyan-400 rounded-lg opacity-0 group-hover:opacity-100 transition duration-300">
                        </div>
                        <div
                            class="relative p-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 h-full transform group-hover:shadow-2xl transition duration-300">
                            <div class="p-3 inline-block bg-blue-600 rounded-md shadow-lg">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944c-1.285.0-2.5.25-3.618.729m7.236 0c.345-.164.707-.29 1.082-.375M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3
                                class="mt-6 text-xl font-bold tracking-tight text-blue-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition duration-300">
                                Exams & Assessment
                            </h3>
                            <p class="mt-4 text-base text-gray-700 dark:text-gray-400">
                                Automated tools for secure exam scheduling, online testing, grading, and result
                                publication.
                            </p>
                        </div>
                    </div>

                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-cyan-400 rounded-lg opacity-0 group-hover:opacity-100 transition duration-300">
                        </div>
                        <div
                            class="relative p-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 h-full transform group-hover:shadow-2xl transition duration-300">
                            <div class="p-3 inline-block bg-blue-600 rounded-md shadow-lg">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m-5 0v-6a2 2 0 012-2h2a2 2 0 012 2v6m0-2h4m-4 0h4m-4 0v2m4-2v2" />
                                </svg>
                            </div>
                            <h3
                                class="mt-6 text-xl font-bold tracking-tight text-blue-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition duration-300">
                                Education Office Integration
                            </h3>
                            <p class="mt-4 text-base text-gray-700 dark:text-gray-400">
                                Facilitate seamless data aggregation, compliance reporting, and communication with
                                regional/national education offices.
                            </p>
                        </div>
                    </div>

                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-cyan-400 rounded-lg opacity-0 group-hover:opacity-100 transition duration-300">
                        </div>
                        <div
                            class="relative p-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 h-full transform group-hover:shadow-2xl transition duration-300">
                            <div class="p-3 inline-block bg-blue-600 rounded-md shadow-lg">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 14v3m4-3v3m4-3v3M3 21h18M5 4h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />
                                </svg>
                            </div>
                            <h3
                                class="mt-6 text-xl font-bold tracking-tight text-blue-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition duration-300">
                                Institution & Facility
                            </h3>
                            <p class="mt-4 text-base text-gray-700 dark:text-gray-400">
                                Oversight for infrastructure, asset inventory, facility scheduling, and resource
                                maintenance across campus.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div id="contact-us"
                class="overflow-hidden bg-white py-16 px-4 dark:bg-slate-900 sm:px-6 lg:px-8 lg:py-24">
                <div class="relative mx-auto max-w-xl">

                    <!-- Background Pattern -->
                    <svg class="absolute left-full translate-x-1/2 transform" width="404" height="404"
                        fill="none" viewBox="0 0 404 404" aria-hidden="true">
                        <defs>
                            <pattern id="pattern-left" x="0" y="0" width="20" height="20"
                                patternUnits="userSpaceOnUse">
                                <rect x="0" y="0" width="4" height="4"
                                    class="text-gray-200 dark:text-slate-600" fill="currentColor" />
                            </pattern>
                        </defs>
                        <rect width="404" height="404" fill="url(#pattern-left)" />
                    </svg>

                    <svg class="absolute right-full bottom-0 -translate-x-1/2 transform" width="404"
                        height="404" fill="none" viewBox="0 0 404 404" aria-hidden="true">
                        <defs>
                            <pattern id="pattern-right" x="0" y="0" width="20" height="20"
                                patternUnits="userSpaceOnUse">
                                <rect x="0" y="0" width="4" height="4"
                                    class="text-gray-200 dark:text-slate-800" fill="currentColor" />
                            </pattern>
                        </defs>
                        <rect width="404" height="404" fill="url(#pattern-right)" />
                    </svg>

                    <!-- Section Title -->
                    <div class="text-center">
                        <h2
                            class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-slate-200 sm:text-4xl">
                            Contact Us
                        </h2>
                        <p class="mt-4 text-lg leading-6 text-gray-500 dark:text-slate-400">
                            Please use the form below to contact us. Thank you!
                        </p>
                    </div>

                    <!-- Contact Form -->
                    <div class="mt-12">
                        <form wire:submit.prevent="sendMessage"
                            class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">

                            <!-- Name -->
                            <div class="sm:col-span-2">
                                <flux:input id="name" type="text" wire:model.defer="name" label="Name"
                                    placeholder="Your full name" />
                                <flux:error name="name" />
                            </div>

                            <!-- Email -->
                            <div class="sm:col-span-2">
                                <flux:input id="email" type="email" wire:model.defer="email" label="Email"
                                    placeholder="you@example.com" />
                                <flux:error name="email" />
                            </div>

                            <!-- Message -->
                            <div class="sm:col-span-2">
                                <flux:textarea id="message" rows="4" wire:model.defer="message"
                                    label="Message" placeholder="Write your message here..." />
                                <flux:error name="message" />
                            </div>

                            <!-- Submit -->
                            <div class="flex justify-end sm:col-span-2">
                                <flux:button type="submit" variant="primary" class="px-6">
                                    Send Message
                                </flux:button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </section>


    </main>

    @if (Route::has('login'))
    <div class="h-14.5 hidden lg:block"></div>
    @endif

    <div>
        @include('footer')
    </div>

    <!-- happy new year card -->
    <!-- <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 9000)"
        x-show="show"
        x-transition.opacity.duration.1000ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">

        <div class="relative pointer-events-auto">
            
            <div class="animated-border rounded-2xl p-[3px]">
                <div class="p-2 rounded-2xl">
                    <img
                        src="{{ asset('images/new-year-2025.png') }}"
                        alt="Happy New Year"
                        class="max-w-md w-full rounded-xl shadow-2xl">
                </div>
            </div>

            
            <button
                type="button"
                @click="show = false"
                class="absolute -top-4 -right-4 z-50
                   w-9 h-9 rounded-full bg-white text-gray-800
                   flex items-center justify-center
                   shadow-lg hover:bg-gray-100
                   focus:outline-none cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>

            </button>
        </div>
    </div> -->

    @include('back-to-top')
    @fluxScripts
</body>

</html>