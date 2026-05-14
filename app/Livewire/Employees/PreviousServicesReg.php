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
use Carbon\Carbon;

use Illuminate\Validation\Rule;
use Livewire\Component;

class PreviousServicesReg extends Component
{
    // -------------------------------------------------
    // Public properties (used in the Livewire template)
    // -------------------------------------------------
    public $peopleId;
    public $canCreate;
    public $canDelete;
    public $people;
    public $teacherAppointment;

    // Dropdown options (collections for select boxes)
    public $servicesOptions = [];
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
    public $firstAppointmentDate;
    public $appointmentLetterNo;


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
            'firstAppointmentDate' => ['required', 'date', 'before_or_equal:today'],
            'appointmentLetterNo' => ['required', 'string', 'max:255'],
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
            $this->servicesOptions = Service::govService()->get();
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
        $employeeServiceList = collect();

        if ($this->people && $this->people->currentAppointment) {
            $employeeServiceList = EmployerAppointment::where('employee_id', $this->people->currentAppointment->employee_id)
                ->with(['position', 'service', 'rank', 'workplace']) // eager load relations
                ->orderBy('first_appointment_date', 'desc') // use 'desc or asc' for newest first
                ->get();
        } else {
            $employeeServiceList = collect(); // return an empty collection if no employee
        }

        return view('livewire.employees.previous-services-reg', compact('employeeServiceList'));
    }

    /**
     * Save or update the teacher's service record.
     */
    public function saveServiceRecord()
    {
        $this->validate([
            'service' => ['required'],
            'rank' => ['required', 'exists:service_ranks,rank_id'],
            'position' => ['required', 'exists:positions,position_id'],
            'officeLevel' => ['required', 'exists:office_levels,office_level_id'],
            'zonalEducationOffice' => ['nullable', 'exists:zonal_education_offices,workplace_id'],
            'institutionCategory' => ['nullable', 'exists:institution_categories,institution_category_id'],
            'workingPlace' => ['required', 'exists:workplaces,workplace_id'],
            'firstAppointmentDate' => ['required', 'date', 'before_or_equal:today'],
            'appointmentLetterNo' => ['required', 'string', 'max:255'],
        ]);

        try {

            // Calculate retirement date (55 years from birth)
            $retirementDate = Carbon::parse($this->people->date_of_birth)->addYears(55);

            // Generate appointment ID
            $appointmentId = EmployerAppointment::generateAppointmentId($this->firstAppointmentDate);

            // Create Employer Appointment
            $appointment = EmployerAppointment::create([
                'appointment_id' => $appointmentId,
                'employee_id' => $this->people->people_id,
                'first_appointment_date' => $this->firstAppointmentDate,
                'retirement_date' => $retirementDate,
                'service_id' => $this->service,
                'rank_id' => $this->rank,
                'position_id' => $this->position,
                'office_level_id' => $this->officeLevel,
                'workplace_id' => $this->workingPlace,
                'appointment_letter_no' => $this->appointmentLetterNo,
                'appointment_letter' => 'default_letter.pdf',
                'w_op_no' => null,
                'active_status' => '0', // Inactive as it's a previous record
            ]);

            session()->flash('success', 'New service record added successfully!');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            // Handle exception and show error message
            session()->flash('error', 'An error occurred while saving the record: Dublicate entry or invalid data.');
            return $this->redirect(url()->previous(), navigate: true);
        }
    }

    // Delete a single EmployerAppointmentHistory record
    public function deleteServiceRecord($id)
    {
        $record = EmployerAppointment::find($id);

        if (!$record) {
            session()->flash('error', 'Record not found.');
            return $this->redirect(url()->previous(), navigate: true);
        }

        // Prevent deleting active record
        if ($record->active_status == 1) {
            session()->flash('error', 'Active records cannot be deleted.');
            return $this->redirect(url()->previous(), navigate: true);
        }

        // Prevent delete if current appointment exists
        if ($record->currentAppointment()->exists()) {
            session()->flash('error', 'Cannot delete record with existing current appointment.');
            return $this->redirect(url()->previous(), navigate: true);
        }

        // Prevent delete if any history exists
        if ($record->appointmentHistory()->exists()) {
            session()->flash('error', 'Cannot delete record with existing history.');
            return $this->redirect(url()->previous(), navigate: true);
        }

        // Delete the record
        $record->delete();

        session()->flash('success', 'Record deleted successfully.');
        return $this->redirect(url()->previous(), navigate: true);
    }
}
