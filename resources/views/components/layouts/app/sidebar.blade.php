<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-900"
    x-data="{
        sidebarOpen: false,
        openGroup: null,
        toggleGroup(name){
          this.openGroup = (this.openGroup === name) ? null : name;
          localStorage.setItem('sidebar_openGroup', this.openGroup ?? '');
        },
        init(){
          const saved = localStorage.getItem('sidebar_openGroup');
          this.openGroup = saved ? saved : null;

          // auto-open group by current route
          if (@js(request()->routeIs('offices.*'))) this.openGroup = 'offices';
          if (@js(request()->routeIs('teacher.*','principal.*','sleas.*','slas.*','sltes.*','sltas.*','slacs.*','dos.*','mso.*'))) this.openGroup = 'employers';
        }
      }"
    x-init="init()">
    {{-- MOBILE OVERLAY --}}
    <div
        class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden"
        x-show="sidebarOpen"
        x-transition.opacity
        x-cloak
        @click="sidebarOpen=false"></div>

    <div class="min-h-screen lg:flex lg:gap-2 lg:px-2 lg:py-2">

        {{-- SIDEBAR (DESKTOP + MOBILE OFFCANVAS) --}}
        <aside
            class="
                fixed inset-y-0 left-0 z-50 w-75
                bg-white/90 backdrop-blur
                border border-slate-100
                dark:bg-zinc-900/90 dark:border-zinc-800
                rounded-none lg:rounded-2xl
                shadow-[0_20px_60px_-30px_rgba(15,23,42,0.25)]
                overflow-hidden
                transform transition-transform duration-300
                h-full flex flex-col 
                lg:sticky lg:top-6
                lg:h-[calc(100vh-3rem)]
            "
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            x-cloak>
            {{-- BRAND --}}
            <div class="px-5 pt-5 pb-4">
                <div class="flex items-center justify-between lg:justify-start">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3" wire:navigate>
                        <div class="h-10 w-10 rounded-2xl bg-indigo-600/10 dark:bg-indigo-500/20 flex items-center justify-center overflow-hidden">
                            <x-app-logo />
                        </div>

                        <div class="leading-tight">
                            <div class="text-sm font-extrabold tracking-tight text-slate-900 dark:text-white">
                                EMIS <span class="text-slate-400 font-semibold dark:text-zinc-500">v1.3</span>
                            </div>
                            <div class="text-xs font-semibold text-slate-400 dark:text-zinc-500">
                                Human Resource Management
                            </div>
                        </div>
                    </a>

                    {{-- MOBILE CLOSE --}}
                    <button
                        type="button"
                        class="lg:hidden rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-2 text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-zinc-700/50"
                        @click="sidebarOpen=false">
                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 h-px bg-slate-100 dark:bg-zinc-800"></div>
            </div>

            @php
            $activePill = "bg-indigo-600 text-white shadow-[0_14px_30px_-18px_rgba(79,70,229,0.95)]";
            $inactivePill = "text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-zinc-800/50";
            $iconActive = "bg-white/15 text-white";
            $iconInactive = "bg-indigo-600/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400";
            @endphp

            {{-- NAV SCROLL AREA --}}
            <div class="px-3 pb-3 overflow-y-auto flex-1 custom-scrollbar">

                {{-- PLATFORM --}}
                <div class="px-3 mb-2">
                    <div class="text-[11px] font-extrabold tracking-widest text-slate-400 uppercase">
                        Platform
                    </div>
                </div>

                <nav class="grid gap-1 px-2">
                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                       {{ request()->routeIs('dashboard') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('dashboard') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 22V12h6v10" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">Dashboard</span>
                        <span class="ms-auto h-1.5 w-1.5 rounded-full {{ request()->routeIs('dashboard') ? 'bg-white' : 'bg-transparent group-hover:bg-slate-300 dark:group-hover:bg-zinc-600' }}"></span>
                    </a>

                    @can('my-profile.general.view')
                    <a href="{{ route('my-profile.index') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                           {{ request()->routeIs('my-profile.*') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('my-profile.*') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">My Profile</span>
                    </a>
                    @endcan

                    {{-- Teacher Transfer --}}
                    @can('my-profile.general.view')
                    <a href="{{ route('my-transfer.teacher-transfer-potal') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                           {{ request()->routeIs('my-transfer.*') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('my-transfer.*') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">My Transfer</span>
                    </a>
                    @endcan

                    {{-- Alerts --}}
                    @can('alerts.overview.view')
                    <a href="{{ route('alerts.overview') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                       {{ request()->routeIs('alerts.*') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('alerts.*') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a3 3 0 0 0 6 0" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">Alerts</span>
                    </a>
                    @endcan

                    <div class="my-2 px-1">
                        <div class="h-px bg-slate-100 dark:bg-zinc-800"></div>
                    </div>

                    @role('super admin')
                    <a href="{{ route('roles.index') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                           {{ request()->routeIs('roles.*') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('roles.*') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 13a5 5 0 0 0 7.54.54l2.12-2.12a5 5 0 0 0-7.07-7.07l-1.06 1.06" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 11a5 5 0 0 0-7.54-.54L4.34 12.58a5 5 0 0 0 7.07 7.07l1.06-1.06" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">Roles</span>
                    </a>

                    <a href="{{ route('main-tables.overview') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                           {{ request()->routeIs('main-tables.*') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('main-tables.*') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v6c0 5-3 8-7 8s-7-3-7-8V7l7-4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v4" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8h.01" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">Main Tables</span>
                    </a>
                    @endrole

                    @can('user.list.view')
                    <a href="{{ route('users.index') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                           {{ request()->routeIs('users.*') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('users.*') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">Users</span>
                    </a>
                    @endcan

                    @can('cadre-dms-approved.index.view')
                    <a href="{{ route('cadre-dms-approved.index') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                           {{ request()->routeIs('cadre-dms-approved.*') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('cadre-dms-approved.*') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h6v6H3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15h6v6H3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h6v6h-6z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6v6H9z" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">DMS Approved Cadre</span>
                    </a>
                    @endcan

                    @can('institution.list.view')
                    <a href="{{ route('institutions.index') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                           {{ request()->routeIs('institutions.*') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('institutions.*') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-6 9 6" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10v10h14V10" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20v-6h6v6" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">Institutions</span>
                    </a>
                    @endcan

                    @can('institution.list.view')
                    <a href="{{ route('institution-groups.index') }}" wire:navigate
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 transition
                           {{ request()->routeIs('institution-groups.*') ? $activePill : $inactivePill }}">
                        <span class="grid place-items-center h-5 w-5 rounded-lg {{ request()->routeIs('institution-groups.*') ? $iconActive : $iconInactive }}">
                            <svg class="h-3 w-3 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25z" />
                            </svg>
                        </span>
                        <span class="text-[13px] font-semibold">Institution Groups</span>
                    </a>
                    @endcan
                </nav>

                {{-- NATIONAL SCHOOL --}}
                <div class="mt-3 px-2">
                    <button
                        type="button"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl
                               text-[11px] font-bold tracking-widest text-slate-400 dark:text-zinc-500 uppercase
                               hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition"
                        @click.stop="toggleGroup('nationalSchool')">
                        National School
                        <svg class="h-4 w-4 transition"
                            :class="openGroup === 'nationalSchool' ? 'rotate-180' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openGroup === 'nationalSchool'" x-collapse class="mt-1 space-y-1 px-2">
                        <a href="{{ route('national-school.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('national-school.list') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            School List
                        </a>
                        <a href="{{ route('national-school.teacher-list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('national-school.teacher-list') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Teacher List
                        </a>
                        <a href="{{ route('national-school.sleas-list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('national-school.sleas-list') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            SLEAS Officers List
                        </a>
                        <a href="{{ route('national-school.principal-list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('national-school.principal-list') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            SLPS Officers List
                        </a>
                        <a href="{{ route('national-school.dms-approved-cadre') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('national-school.dms-approved-cadre') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            DMS Approved Cadre
                        </a>

                    </div>
                </div>

                {{-- TRANSFER MODULE --}}
                <div class="mt-4 px-2">
                    <button
                        type="button"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl
                               text-[11px] font-bold tracking-widest text-slate-400 dark:text-zinc-500 uppercase
                               hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition"
                        @click.stop="toggleGroup('transfer')">
                        Transfer
                        <svg class="h-4 w-4 transition"
                            :class="openGroup === 'transfer' ? 'rotate-180' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openGroup === 'transfer'" x-collapse class="mt-1 space-y-1 px-2">
                        <a href="{{ route('transfer.index-module') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('transfer.index-module') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Overview
                        </a>

                        <a href="{{ route('transfer.transfer-policies') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('transfer.transfer-policies') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Transfer Policy
                        </a>
                        <a href="{{ route('transfer.teacher-transfer-request') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('transfer.teacher-transfer-request') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Teacher Transfer Request
                        </a>
                        <a href="{{ route('transfer-board.province-teacher-transfer') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('transfer-board.province-teacher-transfer') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Province Teacher Transfer Board
                        </a>
                        <a href="{{ route('transfer-board.province-teacher-appeal') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('transfer-board.province-teacher-appeal') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Provincial Teacher Appeal Board
                        </a>
                        <a href="{{ route('transfer-board.zone-teacher-transfer') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('transfer-board.zone-teacher-transfer') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Zone Teacher Transfer Board
                        </a>
                        <a href="{{ route('transfer-board.zone-teacher-appeal') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('transfer-board.zone-teacher-appeal') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Zonal Teacher Appeal Board
                        </a>
                        <a href="{{ route('transfer-board.ntional-teacher-transfer') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('transfer-board.ntional-teacher-transfer') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            National Teacher Transfer Board
                        </a>
                    </div>
                </div>

                {{-- OFFICES GROUP --}}
                <div class="mt-4 px-2">
                    <button
                        type="button"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl
                               text-[11px] font-bold tracking-widest text-slate-400 dark:text-zinc-500 uppercase
                               hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition"
                        @click.stop="toggleGroup('offices')">
                        Offices
                        <svg class="h-4 w-4 transition"
                            :class="openGroup === 'offices' ? 'rotate-180' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openGroup === 'offices'" x-collapse class="mt-1 space-y-1 px-2">
                        <a href="{{ route('offices.index') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                           {{ request()->routeIs('offices.index') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Overview
                        </a>

                        @can('office.moe.list.view')
                        <a href="{{ route('offices.moe.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('offices.moe.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Education Ministries
                        </a>
                        @endcan

                        @can('office.pmoe.list.view')
                        <a href="{{ route('offices.pmoe.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('offices.pmoe.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Provincial Ministries
                        </a>
                        @endcan

                        @can('office.peo.list.view')
                        <a href="{{ route('offices.peo.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('offices.peo.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Provincial Offices
                        </a>
                        @endcan

                        @can('office.zeo.list.view')
                        <a href="{{ route('offices.zeo.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('offices.zeo.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Zonal Offices
                        </a>
                        @endcan

                        @can('office.deo.list.view')
                        <a href="{{ route('offices.deo.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('offices.deo.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Divisional Offices
                        </a>
                        @endcan

                        @can('office.institution.list.view')
                        <a href="{{ route('offices.institutions.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('offices.institutions.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Institutions
                        </a>
                        @endcan
                    </div>
                </div>

                {{-- EMPLOYERS GROUP --}}
                <div class="mt-3 px-2">
                    <button
                        type="button"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl
                               text-[11px] font-bold tracking-widest text-slate-400 dark:text-zinc-500 uppercase
                               hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition"
                        @click.stop="toggleGroup('employers')">
                        Employers
                        <svg class="h-4 w-4 transition"
                            :class="openGroup === 'employers' ? 'rotate-180' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openGroup === 'employers'" x-collapse class="mt-1 space-y-1 px-2">
                        @can('teacher.list.view')
                        <a href="{{ route('teacher.overview') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('teacher.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Teachers
                        </a>
                        @endcan

                        @can('principal.list.view')
                        <a href="{{ route('principal.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('principal.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Principals
                        </a>
                        @endcan

                        @can('sleas.list.view')
                        <a href="{{ route('sleas.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('sleas.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Edu. Directors
                        </a>
                        @endcan

                        @can('slas.list.view')
                        <a href="{{ route('slas.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('slas.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Edu. Secretaries
                        </a>
                        @endcan

                        @can('sltes.list.view')
                        <a href="{{ route('sltes.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('sltes.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Teacher Educators
                        </a>
                        @endcan

                        @can('sltas.list.view')
                        <a href="{{ route('sltas.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('sltas.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Teacher Advisers
                        </a>
                        @endcan

                        @can('slacs.list.view')
                        <a href="{{ route('slacs.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('slacs.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Accountants
                        </a>
                        @endcan

                        @can('dos.list.view')
                        <a href="{{ route('dos.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('dos.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Development Officers
                        </a>
                        @endcan

                        @can('mso.list.view')
                        <a href="{{ route('mso.list') }}" wire:navigate
                            class="block rounded-xl px-3 py-2 text-[13px] font-semibold
                               {{ request()->routeIs('mso.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                            Mng. Assistant
                        </a>
                        @endcan
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="mt-auto px-5 pb-5">
                <div class="h-px bg-slate-100 dark:bg-zinc-800 mb-4"></div>


                {{-- Expandable user card (Alpine) --}}
                <div class="mt-4 rounded-2xl border border-slate-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 p-3"
                    x-data="{ open: false }">

                    {{-- Header row (click to expand) --}}
                    <button type="button"
                        class="w-full flex items-center gap-3 text-left"
                        @click="open = !open">
                        <div class="h-9 w-9 rounded-xl bg-slate-100 dark:bg-zinc-800 flex items-center justify-center font-extrabold text-slate-700 dark:text-slate-300">
                            {{ auth()->user()->initials() }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="truncate text-[13px] font-semibold text-slate-900 dark:text-white">
                                {{ auth()->user()->name }}
                            </div>
                            <div class="truncate text-xs font-semibold text-slate-400 dark:text-zinc-500">
                                {{ auth()->user()->email }}
                            </div>
                        </div>

                        <span class="shrink-0 grid place-items-center h-8 w-8 rounded-xl bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-zinc-700 transition">
                            <svg class="h-4 w-4 transition-transform duration-200"
                                :class="open ? '' : 'rotate-180'"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </button>

                    {{-- Expand area --}}
                    <div x-show="open" x-collapse class="mt-3 grid gap-2">
                        <a href="{{ route('settings.profile') }}" wire:navigate
                            class="flex items-center gap-2 rounded-xl bg-slate-50 dark:bg-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-700 transition
                                px-3 py-2 text-[13px] font-semibold text-slate-700 dark:text-slate-300">
                            <span class="grid place-items-center h-7 w-7 rounded-lg bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                                {{-- Settings (cog) icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </span>
                            Settings
                        </a>

                        <a href="{{ route('change-log.index') }}" wire:navigate
                            class="flex items-center gap-2 rounded-xl bg-slate-50 dark:bg-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-700 transition
                                px-3 py-2 text-[13px] font-semibold text-slate-700 dark:text-slate-300">
                            <span class="grid place-items-center h-7 w-7 rounded-lg bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                                {{-- Sparkles (hot/new) icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.456-2.455L18 2.25l.259 1.036a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                                </svg>
                            </span>
                            What's New
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 transition px-3 py-2 text-[13px] font-semibold text-white">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </aside>

        {{-- MAIN --}}
        <main class="flex-1 max-w-8xl">
            {{-- TOP BAR (mobile open button) --}}
            <div class="sticky top-0 z-30 bg-[#F6F8FF]/70 dark:bg-zinc-900/70 backdrop-blur lg:bg-transparent lg:backdrop-blur-0">
                <div class="px-4 py-4 lg:px-0 lg:py-0">
                    <div class="flex items-center gap-3 lg:hidden">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 shadow-sm"
                            @click="sidebarOpen=true">
                            <svg class="h-3 w-3 text-slate-700 dark:text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="text-sm font-extrabold dark:text-white">EMIS v1.3</div>
                    </div>
                </div>
            </div>

            <div class="px-0 lg:px-0">
                {{ $slot }}
            </div>
        </main>
    </div>

    @fluxScripts
</body>

</html>
