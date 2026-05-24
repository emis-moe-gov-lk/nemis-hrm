<?php

namespace App\Livewire\Employees;

use App\Helpers\NicHelper;
use App\Models\People;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeesOverview extends Component
{
    use WithPagination;

    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        $raw = trim($this->query);

        if ($raw === '' || strlen($raw) < 3) {
            $this->results = [];
            return;
        }

        $logged = Auth::user()->load('workplace');
        $workplace = $logged->workplace;

        if (!$workplace) {
            $this->results = [];
            return;
        }

        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        $peopleQuery = People::query()
            ->active()
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->whereIn('workplace_id', $allowedWorkplaceIds);
            });

        if (NicHelper::isValid($raw)) {
            $normalized = NicHelper::normalize($raw);
            $hash = NicHelper::hash($normalized);

            $peopleQuery->where('nic_hash', $hash);
        } else {
            $search = $raw;
            $peopleQuery->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('name_with_initials', 'like', "%{$search}%");
            });
        }

        $this->results = $peopleQuery
            ->with(['currentAppointment.service', 'currentAppointment.workplace'])
            ->limit(10)
            ->get();
    }

    public function getProfileRoute(People $person)
    {
        $serviceId = $person->currentAppointment?->appointment?->service_id;

        return match ($serviceId) {
            'SER001' => route('teacher.profile.index', $person->id),
            'SER002' => route('sltes.profile.index', $person->id),
            'SER003' => route('sltas.profile.index', $person->id),
            'SER004' => route('principal.profile.index', $person->id),
            'SER005' => route('sleas.profile.index', $person->id),
            'SER006' => route('slas.profile.index', $person->id),
            'SER007' => route('dos.profile.index', $person->id),
            'SER008' => route('mso.profile.index', $person->id),
            'SER009' => route('slacs.profile.index', $person->id),
            default => route('teacher.profile.index', $person->id),
        };
    }

    public function render()
    {
        $items = [
            [
                'label' => 'Register Employee',
                'icon' => 'plus-circle',
                'color' => 'indigo',
                'gradient' => 'from-indigo-600 to-indigo-800',
                'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',
                'text' => 'text-indigo-600 dark:text-indigo-400',
                'shadow' => 'shadow-indigo-200/50',
                'desc' => 'Standardized registration wizard for all service categories (Teachers, SLEAS, SLPS, etc).',
                'route' => route('employees.create.any'),
                'permission' => 'employee.profile.create.any',
            ],
            [
                'label' => 'Teachers',
                'icon' => 'user-group',
                'color' => 'indigo',
                'gradient' => 'from-indigo-500 to-blue-600',
                'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',
                'text' => 'text-indigo-600 dark:text-indigo-400',
                'shadow' => 'shadow-indigo-200/50',
                'desc' => 'Manage Teacher records, service records and professional profiles.',
                'route' => route('teacher.overview'),
                'permission' => 'teacher.list.view',
            ],
            [
                'label' => 'Principals',
                'icon' => 'academic-cap',
                'color' => 'emerald',
                'gradient' => 'from-emerald-500 to-teal-600',
                'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
                'text' => 'text-emerald-600 dark:text-emerald-400',
                'shadow' => 'shadow-emerald-200/50',
                'desc' => 'Manage SLPS officers (Principals) profiles and service records.',
                'route' => route('principal.list'),
                'permission' => 'principal.list.view',
            ],
            [
                'label' => 'Edu. Directors',
                'icon' => 'briefcase',
                'color' => 'blue',
                'gradient' => 'from-blue-500 to-indigo-600',
                'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                'text' => 'text-blue-600 dark:text-blue-400',
                'shadow' => 'shadow-blue-200/50',
                'desc' => 'Manage SLEAS officers profiles and service records.',
                'route' => route('sleas.list'),
                'permission' => 'sleas.list.view',
            ],
            [
                'label' => 'Edu. Secretaries',
                'icon' => 'identification',
                'color' => 'cyan',
                'gradient' => 'from-cyan-500 to-blue-600',
                'bg' => 'bg-cyan-50 dark:bg-cyan-900/20',
                'text' => 'text-cyan-600 dark:text-cyan-400',
                'shadow' => 'shadow-cyan-200/50',
                'desc' => 'Manage SLAS officers profiles and service records.',
                'route' => route('slas.list'),
                'permission' => 'slas.list.view',
            ],
            [
                'label' => 'Teacher Educators',
                'icon' => 'presentation-chart-bar',
                'color' => 'purple',
                'gradient' => 'from-purple-500 to-pink-600',
                'bg' => 'bg-purple-50 dark:bg-purple-900/20',
                'text' => 'text-purple-600 dark:text-purple-400',
                'shadow' => 'shadow-purple-200/50',
                'desc' => 'Manage SLTES officers profiles and service records.',
                'route' => route('sltes.list'),
                'permission' => 'sltes.list.view',
            ],
            [
                'label' => 'Teacher Advisers',
                'icon' => 'user-circle',
                'color' => 'amber',
                'gradient' => 'from-amber-500 to-orange-600',
                'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                'text' => 'text-amber-600 dark:text-amber-400',
                'shadow' => 'shadow-amber-200/50',
                'desc' => 'Manage SLTAS officers profiles and service records.',
                'route' => route('sltas.list'),
                'permission' => 'sltas.list.view',
            ],
            [
                'label' => 'Accountants',
                'icon' => 'banknotes',
                'color' => 'rose',
                'gradient' => 'from-rose-500 to-red-600',
                'bg' => 'bg-rose-50 dark:bg-rose-900/20',
                'text' => 'text-rose-600 dark:text-rose-400',
                'shadow' => 'shadow-rose-200/50',
                'desc' => 'Manage SLACS officers profiles and service records.',
                'route' => route('slacs.list'),
                'permission' => 'slacs.list.view',
            ],
            [
                'label' => 'Development Officers',
                'icon' => 'chart-bar',
                'color' => 'orange',
                'gradient' => 'from-orange-500 to-amber-600',
                'bg' => 'bg-orange-50 dark:bg-orange-900/20',
                'text' => 'text-orange-600 dark:text-orange-400',
                'shadow' => 'shadow-orange-200/50',
                'desc' => 'Manage Development Officers (DOS) profiles and service records.',
                'route' => route('dos.list'),
                'permission' => 'dos.list.view',
            ],
            [
                'label' => 'Mng. Assistant',
                'icon' => 'clipboard-document-list',
                'color' => 'slate',
                'gradient' => 'from-slate-500 to-zinc-600',
                'bg' => 'bg-slate-100 dark:bg-slate-800',
                'text' => 'text-slate-600 dark:text-slate-400',
                'shadow' => 'shadow-slate-200/50',
                'desc' => 'Manage Management Assistants (MSO) profiles and service records.',
                'route' => route('mso.list'),
                'permission' => 'mso.list.view',
            ],
        ];

        return view('livewire.employees.employees-overview', compact('items'))
            ->layout('components.layouts.app.sidebar');
    }
}
