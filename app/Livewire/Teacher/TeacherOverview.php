<?php

namespace App\Livewire\Teacher;

use App\Helpers\NicHelper;
use App\Models\People;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class TeacherOverview extends Component
{
    use WithPagination;

    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        $raw = trim($this->query);

        // empty or too short -> no results (adjust min length if you want)
        if ($raw === '' || strlen($raw) < 3) {
            $this->results = [];
            return;
        }

        // load logged user's workplace
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $logged = $user->load('workplace');
        $workplace = $logged->workplace;

        if (!$workplace) {
            $this->results = [];
            return;
        }

        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // base query: restrict to teachers in allowed workplaces
        $peopleQuery = People::query()
            ->active()
            ->whereHas('appointment', function ($q) {
                $q->where('service_id', 'SER001');
            })
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->whereIn('workplace_id', $allowedWorkplaceIds);
            });

        // If input looks like a valid NIC -> do exact NIC hash lookup
        if (NicHelper::isValid($raw)) {
            $normalized = NicHelper::normalize($raw);
            $hash = NicHelper::hash($normalized);

            $peopleQuery->where('nic_hash', $hash);
        } else {
            // Otherwise search loose on contact / email / name
            $search = $raw;
            $peopleQuery->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('name_with_initials', 'like', "%{$search}%");
            });
        }

        $this->results = $peopleQuery
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $items = [
            [
                'label' => 'Teacher Directory',
                'icon' => 'user-group',
                'color' => 'indigo',
                'bg' => 'bg-indigo-50/50',
                'text' => 'text-indigo-600',
                'hover_bg' => 'group-hover:bg-indigo-600',
                'hover_shadow' => 'group-hover:shadow-indigo-200/50',
                'hover_border' => 'group-hover:border-indigo-200',
                'accent' => 'bg-indigo-500',
                'desc' => 'Manage personal records & professional registration profiles.',
                'route' => route('teacher.list'),
            ],
            [
                'label' => 'Teacher Attachments',
                'icon' => 'academic-cap',
                'color' => 'emerald',
                'bg' => 'bg-emerald-50/50',
                'text' => 'text-emerald-600',
                'hover_bg' => 'group-hover:bg-emerald-600',
                'hover_shadow' => 'group-hover:shadow-emerald-200/50',
                'hover_border' => 'group-hover:border-emerald-200',
                'accent' => 'bg-emerald-500',
                'desc' => 'Track career milestones, duty assignments, and service grades.',
                'route' => route('teacher.attachments'),
            ],
            [
                'label' => 'Changing Workplace',
                'icon' => 'arrows-right-left',
                'color' => 'amber',
                'bg' => 'bg-amber-50/50',
                'text' => 'text-amber-600',
                'hover_bg' => 'group-hover:bg-amber-600',
                'hover_shadow' => 'group-hover:shadow-amber-200/50',
                'hover_border' => 'group-hover:border-amber-200',
                'accent' => 'bg-amber-500',
                'desc' => 'Manage transfer requests and approvals.',
                'route' => route('employees.changing-workplace'),
            ],
            [
                'label' => 'Leave Operations',
                'icon' => 'calendar-days',
                'color' => 'rose',
                'bg' => 'bg-rose-50/50',
                'text' => 'text-rose-600',
                'hover_bg' => 'group-hover:bg-rose-600',
                'hover_shadow' => 'group-hover:shadow-rose-200/50',
                'hover_border' => 'group-hover:border-rose-200',
                'accent' => 'bg-rose-500',
                'desc' => 'Efficiently handle medical leaves, duty absences, and balances.',
                'route' => route('teacher.leave-operations'),
            ],
            [
                'label' => 'Promotions',
                'icon' => 'chart-bar',
                'color' => 'blue',
                'bg' => 'bg-blue-50/50',
                'text' => 'text-blue-600',
                'hover_bg' => 'group-hover:bg-blue-600',
                'hover_shadow' => 'group-hover:shadow-blue-200/50',
                'hover_border' => 'group-hover:border-blue-200',
                'accent' => 'bg-blue-500',
                'desc' => 'Monitor professional rank elevations, tenure, and eligibility status.',
                'route' => route('teacher.promotions'),
                'permission' => 'teacher.promotion.view',
            ],
            [
                'label' => 'Pension or Termination System',
                'icon' => 'banknotes',
                'color' => 'purple',
                'bg' => 'bg-purple-50/50',
                'text' => 'text-purple-600',
                'hover_bg' => 'group-hover:bg-purple-600',
                'hover_shadow' => 'group-hover:shadow-purple-200/50',
                'hover_border' => 'group-hover:border-purple-200',
                'accent' => 'bg-purple-500',
                'desc' => 'Retirement planning tools, gratuity tracking, and pension benefits.',
                'route' => route('teacher.pension-system'),
            ],
            [
                'label' => 'Specializations',
                'icon' => 'book-open',
                'color' => 'cyan',
                'bg' => 'bg-cyan-50/50',
                'text' => 'text-cyan-600',
                'hover_bg' => 'group-hover:bg-cyan-600',
                'hover_shadow' => 'group-hover:shadow-cyan-200/50',
                'hover_border' => 'group-hover:border-cyan-200',
                'accent' => 'bg-cyan-500',
                'desc' => 'Academic subject focus, qualifications, and teacher workload.',
                'route' => route('teacher.specializations'),
            ],
            [
                'label' => 'Benefits & Health',
                'icon' => 'heart',
                'color' => 'red',
                'bg' => 'bg-red-50/50',
                'text' => 'text-red-600',
                'hover_bg' => 'group-hover:bg-red-600',
                'hover_shadow' => 'group-hover:shadow-red-200/50',
                'hover_border' => 'group-hover:border-red-200',
                'accent' => 'bg-red-500',
                'desc' => 'Access dedicated teacher insurance schemes and welfare support.',
                'route' => route('teacher.benefits-and-health'),
            ],
        ];

        return view('livewire.teacher.teacher-overview', compact('items'));
    }
}
