<?php

namespace App\Livewire\Offices\Zeo\Profile;

use App\Models\ZonalEducationOffice;
use App\Models\EmployerCurrentAppointment;
use App\Models\Institution;
use App\Models\InstitutionGroup;
use App\Models\InstitutionGroupInstitution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Livewire\Component;

class CreateInstitutionGroup extends Component
{
    public $officeId;
    public $workplaceId;

    public $groupName;
    public $description;
    public $assignedPerson;
    public $selectedInstitutions = [];
    public $institutionId;

    public $officersList;
    public $institutionList = [];


    public function mount($id)
    {
        $this->officeId = $id;
        // Get the workplace_id for the selected PMOE
        $office = ZonalEducationOffice::find($this->officeId);

        if (!$office) {
            abort(404, 'Zonal Education Office not found');
        }

        $this->workplaceId = $office->workplace_id;

        $this->officersList = EmployerCurrentAppointment::with([
            'employee',
        ])
            ->where('workplace_id', $this->workplaceId)
            ->orderBy('appoint_date', 'asc')
            ->get();

        // Preselect first officer to avoid empty selection state in custom select UI.
        $this->assignedPerson = $this->officersList->first()?->employee_id;

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

        // Check if already added
        $alreadyAdded = collect($this->selectedInstitutions)
            ->contains('workplace_id', $institution->workplace_id);

        if ($alreadyAdded) {
            $this->addError('institutionId', 'This institution is already added.');
            return;
        }

        // Add only if not duplicate
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

    public function createInstiutionGroup()
    {
        $this->validate([
            'groupName' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignedPerson' => 'required|exists:people,people_id',
            'selectedInstitutions' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {

            // Generate Unique Group Code
            $groupCode = $this->generateGroupCode();

            // Create Institution Group
            $group = InstitutionGroup::create([
                'group_code'        => $groupCode,
                'parent_office_id'  => $this->workplaceId,
                'group_name'        => $this->groupName,
                'group_description' => $this->description,
                'is_assigned'       => $this->assignedPerson,
                'active_status'     => true,
            ]);

            // Insert Pivot Records (Blameable will auto-fill created_by & updated_by)
            foreach ($this->selectedInstitutions as $institution) {

                InstitutionGroupInstitution::create([
                    'group_code'     => $group->group_code,
                    'institution_id' => $institution['workplace_id'],
                ]);
            }

            DB::commit();

            // Reset form
            $this->reset([
                'groupName',
                'description',
                'institutionId',
                'selectedInstitutions',
            ]);

            session()->flash('success', 'Institution Group created successfully.');

            return redirect()->route('offices.zeo.profile.institution-groups', $this->officeId);
        } catch (\Throwable $e) {

            DB::rollBack();

            report($e); // log error safely

            session()->flash('error', 'Something went wrong while creating the group.');
        }
    }

    private function generateGroupCode(): string
    {
        do {
            // group_code column is char(10), so generate exactly 10 chars.
            $groupCode = 'IG' . strtoupper(Str::random(8));
        } while (InstitutionGroup::where('group_code', $groupCode)->exists());

        return $groupCode;
    }

    public function render()
    {
        return view('livewire.offices.zeo.profile.create-institution-group');
    }
}
