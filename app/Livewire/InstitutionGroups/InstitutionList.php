<?php

namespace App\Livewire\InstitutionGroups;

use App\Models\InstitutionGroup;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class InstitutionList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $peopleId = auth()->user()?->people_id;
        $groupCodes = [];

        if (!empty($peopleId)) {
            $groupCodes = InstitutionGroup::query()
                ->where('is_assigned', $peopleId)
                ->where('active_status', true)
                ->pluck('group_code')
                ->all();
        }

        $hasAssignedGroups = !empty($groupCodes);

        $institutions = collect();

        if ($hasAssignedGroups) {
            $institutions = DB::table('institutions')
                ->join('institution_group_institutions as igi', 'igi.institution_id', '=', 'institutions.workplace_id')
                ->join('institution_groups as ig', 'ig.group_code', '=', 'igi.group_code')
                ->leftJoin('employer_current_appointments as teacher_eca', function ($join) {
                    $join->on('teacher_eca.workplace_id', '=', 'institutions.workplace_id')
                        ->where('teacher_eca.position_id', '=', 'POS001');
                })
                ->leftJoin('employer_current_appointments as principal_eca', function ($join) {
                    $join->on('principal_eca.workplace_id', '=', 'institutions.workplace_id')
                        ->where('principal_eca.position_id', '=', 'POS002');
                })
                ->whereIn('ig.group_code', $groupCodes)
                ->select(
                    'institutions.workplace_id',
                    'institutions.census_no',
                    'institutions.name',
                    'ig.group_name',
                    DB::raw('COUNT(DISTINCT teacher_eca.employee_id) as total_teachers'),
                    DB::raw('COUNT(DISTINCT principal_eca.employee_id) as total_principals')
                )
                ->groupBy(
                    'institutions.workplace_id',
                    'institutions.census_no',
                    'institutions.name',
                    'ig.group_name'
                )
                ->orderBy('ig.group_name')
                ->orderBy('institutions.name')
                ->paginate(25);
        }

        return view('livewire.institution-groups.institution-list', [
            'hasAssignedGroups' => $hasAssignedGroups,
            'institutions' => $institutions,
        ]);
    }
}
