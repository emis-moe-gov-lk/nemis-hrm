<?php

namespace App\Livewire\Offices\Zeo\Profile;

use App\Models\Institution;
use App\Models\InstitutionGroup;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InstitutionGroupView extends Component
{
    public $officeId;
    public $workplaceId;
    public $groupCode;
    public $group;

    public function mount($id, $groupCode)
    {
        $this->officeId = $id;
        $this->groupCode = $groupCode;

        $office = ZonalEducationOffice::find($this->officeId);

        if (!$office) {
            abort(404, 'Zonal Education Office not found');
        }

        $this->workplaceId = $office->workplace_id;

        $this->group = InstitutionGroup::query()
            ->with(['assignedPerson:people_id,name_with_initials'])
            ->where('parent_office_id', $this->workplaceId)
            ->where('group_code', $this->groupCode)
            ->first();

        if (!$this->group) {
            abort(404, 'Institution group not found');
        }
    }

    public function render()
    {
        $institutions = Institution::query()
            ->join('institution_group_institutions as igi', 'igi.institution_id', '=', 'institutions.workplace_id')
            ->leftJoin('employer_current_appointments as eca', function ($join) {
                $join->on('eca.workplace_id', '=', 'institutions.workplace_id')
                    ->where('eca.position_id', '=', 'POS001');
            })
            ->where('igi.group_code', $this->groupCode)
            ->select(
                'institutions.workplace_id',
                'institutions.census_no',
                'institutions.name',
                DB::raw('COUNT(DISTINCT eca.employee_id) as total_teachers')
            )
            ->groupBy('institutions.workplace_id', 'institutions.census_no', 'institutions.name')
            ->orderBy('institutions.name')
            ->get();

        $totalSchools = $institutions->count();
        $totalTeachers = (int) $institutions->sum('total_teachers');

        return view('livewire.offices.zeo.profile.institution-group-view', [
            'group' => $this->group,
            'institutions' => $institutions,
            'totalSchools' => $totalSchools,
            'totalTeachers' => $totalTeachers,
        ]);
    }

    public function deleteGroup()
    {
        try {
            $group = InstitutionGroup::query()
                ->where('parent_office_id', $this->workplaceId)
                ->where('group_code', $this->groupCode)
                ->firstOrFail();

            DB::transaction(function () use ($group) {
                $group->delete();
            });

            session()->flash('success', 'Institution Group deleted successfully.');

            return redirect()->route('offices.zeo.profile.institution-groups', $this->officeId);
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Unable to delete this institution group.');
        }
    }
}
