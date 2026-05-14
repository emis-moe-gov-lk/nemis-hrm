<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use App\Models\Service;
use Livewire\Component;
use App\Models\Position;
use App\Models\Workplaces;
use App\Models\Institution;
use App\Models\OfficeLevel;
use App\Models\ServiceRank;
use Illuminate\Support\Facades\DB;
use App\Models\InstitutionCategory;
use App\Models\ZonalEducationOffice;

class FirstAppointment extends Component
{
    public $peopleId;
    public $canEdit;
    public $employee;

    // Form fields
    public $appointmentDate;
    public $appointmentLetterNo;
    public $service;
    public $serviceRank;
    public $position;
    public $officeLevel;
    public $zonalEducationOffice;
    public $institutionCategory;
    public $workingPlace;

    // Dropdown options (always collections)
    public $userServicesOptions;
    public $ranksOptions;
    public $positionOption;
    public $officeLevelOption;
    public $zonalEducationOfficeOption;
    public $institutionCategoryOption;
    public $workingPlaceOption;

    public function rules()
    {
        return [
            'appointmentDate'      => ['required', 'date'],
            'appointmentLetterNo'  => ['nullable', 'string'],
            'service'              => ['required', 'exists:services,service_id'],
            'serviceRank'          => ['required', 'exists:service_ranks,rank_id'],
            'position'             => ['nullable', 'exists:positions,position_id'],
            'officeLevel'          => ['required', 'exists:office_levels,office_level_id'],
            'zonalEducationOffice' => ['nullable', 'exists:workplaces,workplace_id'],
            'institutionCategory'  => ['nullable', 'exists:institution_categories,institution_category_id'],
            'workingPlace'         => ['required', 'exists:workplaces,workplace_id'],
        ];
    }

    // Live validation per-field
    public function updated($property)
    {
        $this->validateOnly($property);
    }

    /**
     * MOUNT → Load initial data + current appointment information
     */
    public function mount()
    {
        //$this->peopleId = $peopleId;
        //$this->canEdit = $canEdit;

        // initialize collections to avoid null issues
        $this->userServicesOptions = collect();
        $this->ranksOptions = collect();
        $this->positionOption = collect();
        $this->officeLevelOption = collect();
        $this->zonalEducationOfficeOption = collect();
        $this->institutionCategoryOption = collect();
        $this->workingPlaceOption = collect();

        try {
            $this->employee = People::with('appointment')->where('people_id', $this->peopleId)->first();

            // Dropdowns (always load even if employee missing)
            $this->userServicesOptions = Service::govService()->active()->get();
            $this->officeLevelOption = OfficeLevel::all();
            $this->zonalEducationOfficeOption = ZonalEducationOffice::all();
            $this->institutionCategoryOption = InstitutionCategory::all();

            // if appointment present, load values
            if ($this->employee && $this->employee->appointment) {
                $a = $this->employee->appointment;

                $this->appointmentDate     = optional($a->first_appointment_date)->format('Y-m-d');
                $this->appointmentLetterNo = $a->appointment_letter_no;
                $this->service             = $a->service_id;
                $this->serviceRank         = $a->rank_id;
                $this->position            = $a->position_id;
                $this->officeLevel         = $a->office_level_id;
                $this->workingPlace        = $a->workplace_id;

                // Load ranks for the service
                $this->ranksOptions = ServiceRank::where('service_id', $this->service)->get();
                $this->positionOption = Position::where('service_id', $this->service)->get();

                // If institution (school), set ZEO + category for initial filtering
                if ($a->office_level_id === 'OLID006') {
                    $inst = Institution::where('workplace_id', $a->workplace_id)->first();
                    if ($inst) {
                        $this->zonalEducationOffice = $inst->zeo_wp_id;
                        $this->institutionCategory  = $inst->institution_category_id;
                    }
                }
            } else {
                // defaults when there is no appointment
                $this->appointmentDate = now()->format('Y-m-d');
                $this->service = null;
                $this->serviceRank = null;
                $this->officeLevel = null;
                $this->zonalEducationOffice = null;
                $this->institutionCategory = null;
                $this->workingPlace = null;
            }

            // Finally load working places based on restored state
            $this->loadWorkingPlaces();
        } catch (Exception $e) {
            // Fail gracefully
            session()->flash('error', 'Failed to load appointment data: ' . $e->getMessage());
            // Keep collections safe
            $this->userServicesOptions = $this->userServicesOptions ?? collect();
            $this->ranksOptions = $this->ranksOptions ?? collect();
            $this->officeLevelOption = $this->officeLevelOption ?? collect();
            $this->zonalEducationOfficeOption = $this->zonalEducationOfficeOption ?? collect();
            $this->institutionCategoryOption = $this->institutionCategoryOption ?? collect();
            $this->workingPlaceOption = $this->workingPlaceOption ?? collect();
        }
    }

    /**
     * Update service -> refresh ranks list
     */
    public function updatedService($value)
    {
        $this->ranksOptions = ServiceRank::where('service_id', $value)->get();
        $this->positionOption = Position::where('service_id', $value)->get();
        $this->serviceRank = null;
        $this->position = null;
    }

    /**
     * When office level changes
     */
    public function updatedOfficeLevel($value)
    {
        // reset dependent fields and options
        $this->zonalEducationOffice = null;
        $this->institutionCategory = null;
        $this->workingPlace = null;
        $this->workingPlaceOption = collect();

        if ($value === 'OLID006') {
            // institutions will be loaded by loadWorkingPlaces when ZEO + category are available
            $this->workingPlaceOption = collect();
        } else {
            // normal workplaces
            $this->workingPlaceOption = Workplaces::where('office_level_id', $value)->get();
        }
    }

    /**
     * ZEO changed -> Filter schools by zone + category
     */
    public function updatedZonalEducationOffice($value)
    {
        // clear previous workingPlace selection
        $this->workingPlace = null;

        // loadWorkingPlaces will only populate if both required filters exist for OLID006
        $this->loadWorkingPlaces();
    }

    /**
     * Category changed -> Filter schools by category + zone
     */
    public function updatedInstitutionCategory($value)
    {
        $this->workingPlace = null;
        $this->loadWorkingPlaces();
    }

    /**
     * Load workplace options depending on office level, ZEO and category
     */
    private function loadWorkingPlaces()
    {
        // Ensure workingPlaceOption always a collection
        $this->workingPlaceOption = collect();

        // If officeLevel not set yet, nothing to load
        if (empty($this->officeLevel)) {
            return;
        }

        // Non-institutional levels: load workplaces by office level
        if ($this->officeLevel !== 'OLID006') {
            $this->workingPlaceOption = Workplaces::where('office_level_id', $this->officeLevel)
                //->orderBy('office_name') // if column exists; otherwise remove
                ->get();
            return;
        }

        // For institutions (OLID006) we require both ZEO and category to be present
        if (empty($this->zonalEducationOffice) || empty($this->institutionCategory)) {
            // Do not clear previously-loaded options in this case — keep empty collection
            return;
        }

        // Load filtered institution workplaces
        $this->workingPlaceOption = Workplaces::where('office_level_id', 'OLID006')
            ->whereHas('institution', function ($q) {
                $q->where('zeo_wp_id', $this->zonalEducationOffice)
                    ->where('institution_category_id', $this->institutionCategory);
            })
            ->with(['institution' => function ($q) {
                $q->orderBy('name', 'asc');
            }])
            ->get()
            ->sortBy(fn($w) => optional($w->institution)->name)
            ->values();
    }

    /**
     * Save -> Update job data with error handling
     */
    public function save()
    {
        $this->validate();

        if (! $this->employee || ! $this->employee->appointment) {
            session()->flash('error', 'No appointment found for this person.');
            return;
        }

        $appointment = $this->employee->appointment;

        DB::beginTransaction();
        try {
            $appointment->update([
                'first_appointment_date'          => $this->appointmentDate,
                'appointment_letter_no' => $this->appointmentLetterNo,
                'service_id'            => $this->service,
                'rank_id'               => $this->serviceRank,
                'position_id'           => $this->position,
                'office_level_id'       => $this->officeLevel,
                'workplace_id'          => $this->workingPlace,
            ]);

            DB::commit();

            session()->flash('success', 'Employment updated successfully.');
            $this->dispatch('close-modal', name: 'employment-edit');

            // Optionally refresh internal state
            $this->mount();

            // navigate back
            return $this->redirect(url()->previous(), navigate: true);
        } catch (Exception $e) {
            DB::rollBack();
            // log if you have logger, else session
            session()->flash('error', 'Failed to update employment: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employees.first-appointment');
    }

    /**
     * Reset all fields to the initial values of the DB appointment (works reliably)
     */
    public function resetFields()
    {
        // Reload from DB and reapply the same logic as mount but minimal
        $this->employee = People::with('appointment')->where('people_id', $this->peopleId)->first();

        if (! $this->employee || ! $this->employee->appointment) {
            // safe fallback - clear form
            $this->appointmentDate = now()->format('Y-m-d');
            $this->appointmentLetterNo = null;
            $this->service = null;
            $this->serviceRank = null;
            $this->position = null;
            $this->officeLevel = null;
            $this->zonalEducationOffice = null;
            $this->institutionCategory = null;
            $this->workingPlace = null;
            $this->workingPlaceOption = collect();
            return;
        }

        $a = $this->employee->appointment;

        // Restore primary fields first
        $this->appointmentDate     = optional($a->first_appointment_date)->format('Y-m-d');
        $this->appointmentLetterNo = $a->appointment_letter_no;
        $this->service             = $a->service_id;
        $this->serviceRank         = $a->rank_id;
        $this->position            = $a->position_id;
        $this->officeLevel         = $a->office_level_id;
        $this->workingPlace        = $a->workplace_id;

        // restore ranks
        $this->ranksOptions = ServiceRank::where('service_id', $this->service)->get();
        $this->positionOption = Position::where('service_id', $this->service)->get();

        // If institution, set ZEO and category BEFORE loading workplaces
        if ($this->officeLevel === 'OLID006') {
            $inst = Institution::where('workplace_id', $a->workplace_id)->first();
            $this->zonalEducationOffice = $inst?->zeo_wp_id;
            $this->institutionCategory  = $inst?->institution_category_id;

            // Only after both are set, load workplaces
            $this->loadWorkingPlaces();
        } else {
            // Non-institution level: load workplaces directly
            $this->zonalEducationOffice = null;
            $this->institutionCategory  = null;
            $this->workingPlaceOption = Workplaces::where('office_level_id', $this->officeLevel)->get();
        }
    }
}
