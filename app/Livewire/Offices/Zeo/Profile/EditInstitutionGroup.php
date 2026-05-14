<?php

namespace App\Livewire\Offices\Zeo\Profile;

use App\Models\EmployerCurrentAppointment;
use App\Models\Institution;
use App\Models\InstitutionGroup;
use App\Models\InstitutionGroupInstitution;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditInstitutionGroup extends Component
{
    public $officeId;
    public $workplaceId;
    public $groupCode;

    public $groupName;
    public $description;
    public $assignedPerson;
    public $selectedInstitutions = [];
    public $institutionId;

    public $officersList;
    public $institutionList = [];

    public function mount($id, $groupCode)
    {
        $this->officeId = $id;
        $this->groupCode = $groupCode;

        $office = ZonalEducationOffice::find($this->officeId);

        if (!$office) {
            abort(404, 'Zonal Education Office not found');
        }

        $this->workplaceId = $office->workplace_id;

        $group = InstitutionGroup::query()
            ->with('institutions:workplace_id,census_no,name')
            ->where('parent_office_id', $this->workplaceId)
            ->where('group_code', $this->groupCode)
            ->first();

        if (!$group) {
            abort(404, 'Institution group not found');
        }

        $this->groupName = $group->group_name;
        $this->description = $group->group_description;
        $this->assignedPerson = $group->is_assigned;

        $this->selectedInstitutions = $group->institutions
            ->map(fn($institution) => [
                'workplace_id' => $institution->workplace_id,
                'name' => $institution->name,
                'census_no' => $institution->census_no,
            ])->values()->toArray();

        $this->officersList = EmployerCurrentAppointment::with('employee')
            ->where('workplace_id', $this->workplaceId)
            ->orderBy('appoint_date', 'asc')
            ->get();

        if (empty($this->assignedPerson)) {
            $this->assignedPerson = $this->officersList->first()?->employee_id;
        }

        $this->institutionList = Institution::where('zeo_wp_id', $this->workplaceId)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function addInstitution()
    {
        $this->validate([
            'institutionId' => 'required',
        ]);

        $institution = Institution::find($this->institutionId);

        if (!$institution) {
            return;
        }

        $alreadyAdded = collect($this->selectedInstitutions)
            ->contains('workplace_id', $institution->workplace_id);

        if ($alreadyAdded) {
            $this->addError('institutionId', 'This institution is already added.');
            return;
        }

        $this->selectedInstitutions[] = [
            'workplace_id' => $institution->workplace_id,
            'name' => $institution->name,
            'census_no' => $institution->census_no,
        ];

        $this->institutionId = '';
    }

    public function removeInstitution($index)
    {
        unset($this->selectedInstitutions[$index]);
        $this->selectedInstitutions = array_values($this->selectedInstitutions);
    }

    public function updateInstitutionGroup()
    {
        $this->validate([
            'groupName' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignedPerson' => 'required|exists:people,people_id',
            'selectedInstitutions' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $group = InstitutionGroup::query()
                ->where('parent_office_id', $this->workplaceId)
                ->where('group_code', $this->groupCode)
                ->firstOrFail();

            $group->update([
                'group_name' => $this->groupName,
                'group_description' => $this->description,
                'is_assigned' => $this->assignedPerson,
            ]);

            InstitutionGroupInstitution::where('group_code', $this->groupCode)->delete();

            foreach ($this->selectedInstitutions as $institution) {
                InstitutionGroupInstitution::create([
                    'group_code' => $this->groupCode,
                    'institution_id' => $institution['workplace_id'],
                ]);
            }

            DB::commit();

            session()->flash('success', 'Institution Group updated successfully.');

            return redirect()->route('offices.zeo.profile.institution-groups.view', [
                'id' => $this->officeId,
                'groupCode' => $this->groupCode,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            session()->flash('error', 'Something went wrong while updating the group.');
        }
    }

    public function render()
    {
        return view('livewire.offices.zeo.profile.edit-institution-group');
    }
}
