<?php

namespace App\Livewire\InstitutionGroups;

use App\Models\EmployerCurrentAppointment;
use App\Models\InstitutionGroup;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    private function getPositionCountsByGroup(array $groupCodes, string $positionId): array
    {
        if (empty($groupCodes)) {
            return [];
        }

        return DB::table('institution_group_institutions as igi')
            ->leftJoin('employer_current_appointments as eca', function ($join) use ($positionId) {
                $join->on('eca.workplace_id', '=', 'igi.institution_id')
                    ->where('eca.position_id', '=', $positionId);
            })
            ->whereIn('igi.group_code', $groupCodes)
            ->select(
                'igi.group_code',
                DB::raw('COUNT(DISTINCT eca.employee_id) as total_count')
            )
            ->groupBy('igi.group_code')
            ->pluck('total_count', 'igi.group_code')
            ->map(fn($count) => (int) $count)
            ->toArray();
    }

    public function render()
    {
        $peopleId = auth()->user()?->people_id;
        $assignedGroups = collect();

        if (!empty($peopleId)) {
            $assignedGroups = InstitutionGroup::query()
                ->where('is_assigned', $peopleId)
                ->where('active_status', true)
                ->with([
                    'assignedPerson:people_id,name_with_initials',
                    'institutions:workplace_id',
                ])
                ->withCount(['institutions as total_schools'])
                ->latest()
                ->get();
        }

        $groupCodes = $assignedGroups->pluck('group_code')->all();

        $teacherCounts = $this->getPositionCountsByGroup($groupCodes, 'POS001');
        $principalCounts = $this->getPositionCountsByGroup($groupCodes, 'POS002');

        $officerPositionMap = EmployerCurrentAppointment::query()
            ->with('position:position_id,position_name')
            ->whereIn('employee_id', $assignedGroups->pluck('is_assigned')->filter()->unique()->values())
            ->get()
            ->keyBy('employee_id');

        $groups = $assignedGroups->map(function ($group) use ($teacherCounts, $principalCounts, $officerPositionMap) {
            $officerAppointment = $officerPositionMap->get($group->is_assigned);

            return [
                'group_code' => $group->group_code,
                'group_name' => $group->group_name,
                'description' => $group->group_description,
                'officer_name' => $group->assignedPerson?->name_with_initials ?? 'Not assigned',
                'officer_position' => $officerAppointment?->position?->position_name ?? 'N/A',
                'total_schools' => (int) $group->total_schools,
                'total_teachers' => $teacherCounts[$group->group_code] ?? 0,
                'total_principals' => $principalCounts[$group->group_code] ?? 0,
            ];
        });

        return view('livewire.institution-groups.index', [
            'hasAssignedGroups' => $groups->isNotEmpty(),
            'groups' => $groups,
        ]);
    }
}
