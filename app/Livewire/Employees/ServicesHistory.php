<?php

namespace App\Livewire\Employees;

use App\Models\{
    EmployerAppointment,
    EmployerAppointmentHistory,
    Institution,
    InstitutionCategory,
    OfficeLevel,
    People,
    Position,
    Service,
    ServiceRank,
    Teacher,
    Workplaces,
    ZonalEducationOffice
};

use Illuminate\Validation\Rule;
use Livewire\Component;

class ServicesHistory extends Component
{
    // -------------------------------------------------
    // Public properties (used in the Livewire template)
    // -------------------------------------------------
    public $peopleId;
    public $canCreate;
    public $canDelete;
    public $people;

    // Dropdown options (collections for select boxes)
    public $userServicesOptions = [];
    public $ranksOptions = [];
    public $positionOption = [];
    public $officeLevelOption = [];
    public $zonalEducationOfficeOption = [];
    public $institutionCategoryOption = [];
    public $workingPlaceOption = [];

    public $subjectOption = [], $currentInstitutionOption = [];

    // Selected dropdown values / input fields
    public $recordType;
    public $service;
    public $rank;
    public $position;
    public $officeLevel;
    public $institutionCategory;
    public $zonalEducationOffice;
    public $workingPlace;
    public $appointDate;
    public $endedDate;


    // -------------------------
    // Validation Rules
    // -------------------------


    protected function rules()
    {
        return [
            'recordType' => ['required', Rule::in(['0', '1', '2', '3'])],
            'service' => ['required'],
            'rank' => ['required', 'exists:service_ranks,rank_id'],
            'position' => ['required', 'exists:positions,position_id'],
            'officeLevel' => ['required', 'exists:office_levels,office_level_id'],
            'zonalEducationOffice' => ['nullable', 'exists:zonal_education_offices,workplace_id'],
            'institutionCategory' => ['nullable', 'exists:institution_categories,institution_category_id'],
            'workingPlace' => ['required', 'exists:workplaces,workplace_id'],
            'appointDate' => ['required', 'date', 'before_or_equal:today'],
            'endedDate' => ['required', 'date', 'after:appointDate'],
        ];
    }

    protected function messages()
    {
        return [
            'recordType.required' => 'Please select the update type.',
            'recordType.in' => 'Invalid update type selected.',

            'service.required' => 'Please select a service.',
            'service.exists' => 'The selected service does not exist.',

            'rank.required' => 'Please select a rank.',
            'rank.exists' => 'The selected rank does not exist.',

            'position.required' => 'Please select a position.',
            'position.exists' => 'The selected position does not exist.',

            'officeLevel.required' => 'Please select the office level.',
            'officeLevel.exists' => 'Invalid office level selected.',

            'workingPlace.required' => 'Please select a working place.',
            'workingPlace.exists' => 'The selected working place does not exist.',

            'appointDate.required' => 'Please enter the appointed date.',
            'appointDate.date' => 'Appointed date must be a valid date.',
            'appointDate.before_or_equal' => 'Appointed date cannot be in the future.',

            'endedDate.date' => 'Ended date must be a valid date.',
            'endedDate.after' => 'Ended date must be after the appointed date.',
        ];
    }

    // -------------------------
    // Live Validation on Field Update
    // -------------------------
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($peopleId)
    {
        $this->peopleId = $peopleId;
        // $this->canCreate = $canCreate;
        // $this->canDelete = $canDelete;
        // Load teacher details and current appointment
        $this->people = People::with('currentAppointment')->where('people_id', $this->peopleId)->first();
        if ($this->people) {
            // Load available service records for this employee
            $this->userServicesOptions = EmployerAppointment::where('employee_id', $this->people->people_id)->get();
        }

        // Load dropdown data
        $this->officeLevelOption = OfficeLevel::all();
        $this->zonalEducationOfficeOption = ZonalEducationOffice::all();
        $this->institutionCategoryOption = InstitutionCategory::all();
    }

    /**
     * When 'service' dropdown changes,
     * dynamically update ranks and positions.
     */
    public function updatedService($value)
    {
        $this->ranksOptions = ServiceRank::where('service_id', $value)->get();
        $this->positionOption = Position::where('service_id', $value)->get();
    }

    /**
     * When 'officeLevel' changes,
     * update workplaces accordingly.
     */
    public function updatedOfficeLevel($value)
    {
        if ($value === 'OLID006') {
            // Special case for institutions
            $this->workingPlaceOption = collect();
            $this->workingPlace = '';
        } else {
            $this->workingPlaceOption = Workplaces::where('office_level_id', $value)->get();
            $this->workingPlace = '';
        }
    }

    /**
     * When 'zonalEducationOffice' changes,
     * filter institutions by ZEO + category.
     */
    public function updatedZonalEducationOffice($value)
    {
        if ($value && $this->institutionCategory) {
            $this->workingPlaceOption = Workplaces::where('office_level_id', 'OLID006')
                ->whereHas('institution', function ($query) use ($value) {
                    $query->where('zeo_wp_id', $value)
                        ->where('institution_category_id', $this->institutionCategory);
                })
                ->with(['institution' => function ($q) {
                    $q->orderBy('name', 'asc');
                }])
                ->get()
                ->sortBy('institution.name')
                ->values(); // reset array keys
        } else {
            $this->workingPlaceOption = collect();
        }

        $this->workingPlace = '';
    }


    /**
     * When 'institutionCategory' changes,
     * filter institutions by category + ZEO.
     */
    public function updatedInstitutionCategory($value)
    {
        if ($value && $this->zonalEducationOffice) {

            $this->workingPlaceOption = Workplaces::where('office_level_id', 'OLID006')
                ->whereHas('institution', function ($query) use ($value) {
                    $query->where('institution_category_id', $value)
                        ->where('zeo_wp_id', $this->zonalEducationOffice);
                })
                ->with(['institution' => function ($q) {
                    $q->orderBy('name', 'asc'); // preload institution sorted
                }])
                ->get()
                ->sortBy('institution.name') // sort final collection
                ->values(); // reset array keys

        } else {
            $this->workingPlaceOption = collect();
        }

        $this->workingPlace = '';
    }

    public function render()
    {
        $serviceUpdate = collect();

        if ($this->people && $this->people->currentAppointment) {
            $serviceUpdate = EmployerAppointmentHistory::where('employee_id', $this->people->currentAppointment->employee_id)
                ->with(['position', 'service', 'rank', 'workplace']) // eager load relations
                ->orderBy('appoint_date', 'desc') // use 'desc or asc' for newest first
                ->get();
        } else {
            $serviceUpdate = collect(); // return an empty collection if no employee
        }
        return view('livewire.employees.services-history', ['serviceUpdate' => $serviceUpdate]);
    }

    /**
     * Save or update the teacher's service record.
     */
    public function saveServiceRecord()
    {
        $this->validate([
            'recordType' => ['required', Rule::in(['0', '1', '2', '3'])],
            'service' => ['required'],
            'rank' => ['required', 'exists:service_ranks,rank_id'],
            'position' => ['required', 'exists:positions,position_id'],
            'officeLevel' => ['required', 'exists:office_levels,office_level_id'],
            'zonalEducationOffice' => ['nullable', 'exists:zonal_education_offices,workplace_id'],
            'institutionCategory' => ['nullable', 'exists:institution_categories,institution_category_id'],
            'workingPlace' => ['required', 'exists:workplaces,workplace_id'],
            'appointDate' => ['required', 'date', 'before_or_equal:today'],
            'endedDate' => ['required', 'date', 'after:appointDate'],
        ]);

        try {
            $appointment = EmployerAppointment::where('employee_id', $this->people->people_id)
                ->where('service_id', $this->service)
                ->first();

            EmployerAppointmentHistory::create([
                'appointment_id' => $appointment->appointment_id,
                'employee_id' => $this->people->people_id,
                'appoint_date' => $this->appointDate,
                'end_date' => $this->endedDate,
                'service_id' => $this->service,
                'rank_id' => $this->rank,
                'position_id' => $this->position,
                'office_level_id' => $this->officeLevel,
                'workplace_id' => $this->workingPlace,
                'updated_type' => $this->recordType,
            ]);

            session()->flash('success', 'New service record added successfully!');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            // Handle exception and show error message
            session()->flash('error', 'An error occurred while saving the record: ' . $e->getMessage());
        }
    }

    // Delete a single EmployerAppointmentHistory record
    public function deleteServiceRecord($id)
    {
        // Find the record
        $record = EmployerAppointmentHistory::find($id);

        if ($record) {
            $record->delete();

            // Optional: show a success message
            session()->flash('success', 'Record deleted successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } else {
            session()->flash('error', 'Record not found.');
        }
    }
}
