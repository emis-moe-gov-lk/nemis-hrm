<?php

namespace App\Livewire\InstitutionGroups;

use App\Models\EmployerCurrentAppointment;
use App\Models\InstitutionGroup;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PrincipleList extends Component
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

        $institutionGroupMap = [];
        $principals = collect();

        if ($hasAssignedGroups) {
            $institutionIds = DB::table('institution_group_institutions')
                ->whereIn('group_code', $groupCodes)
                ->pluck('institution_id')
                ->unique()
                ->values()
                ->all();

            $institutionGroupMap = DB::table('institution_group_institutions as igi')
                ->join('institution_groups as ig', 'ig.group_code', '=', 'igi.group_code')
                ->whereIn('ig.group_code', $groupCodes)
                ->select('igi.institution_id', 'ig.group_name')
                ->get()
                ->groupBy('institution_id')
                ->map(fn($rows) => $rows->pluck('group_name')->unique()->values()->all())
                ->toArray();

            $principals = EmployerCurrentAppointment::query()
                ->with([
                    'employee:people_id,name_with_initials',
                    'institution:workplace_id,name,census_no',
                    'position:position_id,position_name',
                ])
                ->whereIn('workplace_id', $institutionIds)
                ->where('position_id', 'POS002')
                ->orderByDesc('appoint_date')
                ->paginate(25);
        }

        return view('livewire.institution-groups.principle-list', [
            'hasAssignedGroups' => $hasAssignedGroups,
            'principals' => $principals,
            'institutionGroupMap' => $institutionGroupMap,
        ]);
    }
}
