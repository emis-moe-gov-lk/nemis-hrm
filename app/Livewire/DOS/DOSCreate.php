<?php

namespace App\Livewire\DOS;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Title;
use App\Models\People;
use App\Models\Service;

use Livewire\Component;
use App\Models\Position;
use App\Models\Religion;
use App\Models\Ethnicity;
use App\Helpers\NicHelper;
use App\Models\BloodGroup;
use App\Models\GenderList;
use App\Models\GnDivision;
use App\Models\Workplaces;
use App\Models\CivilStatus;
use App\Models\Institution;
use App\Models\OfficeLevel;
use App\Models\ServiceRank;
use App\Models\SubjectList;
use Illuminate\Support\Str;
use App\Models\DistrictsList;
use App\Mail\SendUserPassword;
use App\Rules\UniqueHashedNic;
use App\Models\ApointedSubject;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\EmployerAppointment;

use App\Models\InstitutionCategory;
use App\Models\MediumOfInstruction;
use Illuminate\Support\Facades\Log;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Rules\UniqueEmailAcrossTables;
use App\Rules\UniquePhoneAcrossTables;
use App\Models\EmployerCurrentAppointment;
use App\Models\DivisionalSecretariatOffice;

class DOSCreate extends Component
{
    public $nicCheck = false;
    public $step = 1;
    public $maxStep = 5;
    public $peopleId; // Added for unique validation ignore

    // -------------------------
    // Personal Details
    // -------------------------
    public $nic, $title, $fullName, $gender, $birthday, $religion;
    public $ethnicity, $civilStatus, $bloodGroup, $healthCondition, $healthProblem;
    public $district, $divisionalDecretaryOffice, $gnDivision;

    // -------------------------
    // Contact Details
    // -------------------------
    public $contact, $email;
    public $addressLine1, $addressLine2, $addressLine3, $postalCode, $latitude, $longitude;
    public $tAddressLine1, $tAddressLine2, $tAddressLine3, $tPostalCode;

    // -------------------------
    // Appointment Details
    // -------------------------
    public $firstAppointmentDate, $appointmentLetterNo;
    public $service, $serviceRank;
    public $zonalEducationOffice, $institutionCategory, $institution;
    public $position;

    // -------------------------
    // Current Appointment
    // -------------------------
    public $dosRegType = 'existing', $currentAppointmentDate, $currentAppointmentLetterNo, $currentService, $currentServiceRank;
    public $firstOfficeLevel, $firstZonalEducationOffice, $firstInstitutionCategory, $firstWorkingPlace;
    public $currentOfficeLevel, $currentZonalEducationOffice, $currentInstitutionCategory, $currentWorkingPlace;
    public $currentPosition;

    // -------------------------
    // Dropdown Options
    // -------------------------
    public $titleOptions = [], $religionOptions = [], $genderOptions = [], $ethnicityOptions = [], $civilStatusOptions = [];
    public $bloodGroupOptions = [], $healthConditionOptions = [];
    public $districtOption = [], $divisionalSecretaryofficeOption = [], $gnDivisionOption = [];
    public $servicesOption = [], $ranksOption = [], $currentRanksOption = [];
    public $firstOfficeLevelOption = [], $firstZonalEducationOfficeOption = [], $firstInstitutionCategoryOption = [], $firstWorkingPlaceOption = [];
    public $currentOfficeLevelOption = [], $currentZonalEducationOfficeOption = [], $currentInstitutionCategoryOption = [], $currentWorkingPlaceOption = [];
    public $positionOption = [], $currentPositionOption = [];

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rulesForCurrentStep()
    {
        switch ($this->step) {

            // ------------------------------------
            // STEP 1 → NIC ONLY
            // ------------------------------------
            case 1:
                return [
                    'nic' => [
                        'required',
                        'string',
                        'min:10',
                        'max:12',
                        new UniqueHashedNic($this->peopleId),
                        'regex:/^(?:[0-9]{9}[vVxX]|[0-9]{12})$/'
                    ],
                ];

                // ------------------------------------
                // STEP 2 → PERSONAL DETAILS
                // ------------------------------------
            case 2:
                return [
                    'title' => 'required|string',
                    'fullName' => 'required|string|max:255',
                    'gender' => 'required|string',
                    'birthday' => [
                        'required',
                        'date',
                        'before:today',
                        'after:' . now()->subYears(60)->toDateString(),
                    ],
                    'religion' => 'required|string',
                    'ethnicity' => 'required|string',
                    'civilStatus' => 'required|string',
                    'bloodGroup' => 'required|string',
                    'healthCondition' => 'required|boolean',
                    'healthProblem' => 'nullable|required_if:healthCondition,false|string|max:1000',
                    'district' => 'required|string',
                    'divisionalDecretaryOffice' => 'required|string',
                    'gnDivision' => 'required|string',
                ];

                // ------------------------------------
                // STEP 3 → CONTACT DETAILS
                // ------------------------------------
            case 3:
                return [
                    'contact' => [
                        'required',
                        'string',
                        'min:10',
                        'max:10',
                        new UniquePhoneAcrossTables($this->peopleId),
                        'regex:/^0\d{9}$/'
                    ],
                    'email' => [
                        'required',
                        'email',
                        new UniqueEmailAcrossTables($this->peopleId),
                    ],
                    'addressLine1' => 'required|string|max:255',
                    'addressLine2' => 'required|string|max:255',
                    'addressLine3' => 'nullable|string|max:255',
                    'postalCode' => 'required|string|max:10',
                    'latitude' => 'nullable|numeric|between:-90,90',
                    'longitude' => 'nullable|numeric|between:-180,180',
                    'tAddressLine1' => 'nullable|string|max:255',
                    'tAddressLine2' => 'nullable|string|max:255',
                    'tAddressLine3' => 'nullable|string|max:255',
                    'tPostalCode' => 'nullable|string|max:10',
                ];

                // ------------------------------------
                // STEP 4 → FIRST APPOINTMENT
                // ------------------------------------
            case 4:
                return [
                    'firstAppointmentDate' => 'required|date|before_or_equal:today',
                    'appointmentLetterNo' => 'required|string|max:255',
                    'service' => 'required|string',
                    'serviceRank' => 'required|string',
                    'firstOfficeLevel' => 'required|string',
                    'firstZonalEducationOffice' => 'nullable|required_if:firstOfficeLevel,OLID006|string',
                    'firstInstitutionCategory' => 'nullable|required_if:firstOfficeLevel,OLID006|string',
                    'firstWorkingPlace' => 'required|string',
                    'position' => 'required|string',
                ];

                // ------------------------------------
                // STEP 5 → CURRENT APPOINTMENT
                // ------------------------------------
            case 5:
                return [
                    'dosRegType' => 'required|string|in:new,existing',
                    'currentAppointmentDate' => 'required|date|before_or_equal:today',
                    'currentAppointmentLetterNo' => 'nullable|string|max:255',
                    'currentService' => 'required|string',
                    'currentServiceRank' => 'required|string',
                    'currentOfficeLevel' => 'required|string',
                    'currentZonalEducationOffice' => 'nullable|required_if:currentOfficeLevel,OLID006|string',
                    'currentInstitutionCategory' => 'nullable|required_if:currentOfficeLevel,OLID006|string',
                    'currentWorkingPlace' => 'required|string',
                    'currentPosition' => 'required|string',
                ];

            default:
                return [];
        }
    }

    protected $messages = [
        'nic.required' => 'NIC is required',
        'nic.regex' => 'Please enter a valid NIC number',
        'fullName.required' => 'Full Name is required',
        'email.required' => 'Email is required',
        'email.email' => 'Enter a valid email',
        'email.unique' => 'This email is already registered',
        'contact.required' => 'Contact number is required',
        'contact.min' => 'Contact number should be 10 digits',
        'contact.max' => 'Contact number should be 10 digits',
        'contact.regex' => 'Please enter a valid Contact number',
        'healthProblem.required_if' => 'Please provide health problem details when health condition is "No"',
        'appointmentLetterNo.required' => 'Appointment letter number is required',
        'birthday.before' => 'Birthday must be a past date',
        'firstAppointmentDate.before_or_equal' => 'First appointment date cannot be in the future',
        'currentAppointmentDate.before_or_equal' => 'Current appointment date cannot be in the future',
    ];

    // -------------------------
    // Live Validation on Update
    // -------------------------
    public function updated($propertyName)
    {
        $rules = $this->rulesForCurrentStep();
        if (array_key_exists($propertyName, $rules)) {
            $this->validateOnly($propertyName, $rules);
        }
    }



    // -------------------------
    // Step Navigation
    // -------------------------
    public function nextStep()
    {
        $this->validate($this->rulesForCurrentStep());
        if ($this->step < $this->maxStep) {
            $this->step++;
            $this->resetValidation();
            $this->dispatch('scroll-top');
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
            $this->resetValidation();
            $this->dispatch('scroll-top');
        }
    }

    public function mount()
    {
        $this->titleOptions = Title::active()->get();
        $this->genderOptions = GenderList::active()->get();
        $this->religionOptions = Religion::active()->get();
        $this->ethnicityOptions = Ethnicity::active()->get();
        $this->civilStatusOptions = CivilStatus::active()->get();
        $this->bloodGroupOptions = BloodGroup::all();
        $this->healthConditionOptions = [true => 'Yes', false => 'No'];
        $this->districtOption = DistrictsList::active()->orderBy('district_name', 'asc')->get();
        $this->servicesOption = Service::where('service_id', 'SER007')->active()->get();
        $this->ranksOption = collect();
        $this->currentRanksOption = collect();
        $this->healthCondition = true;

        $this->firstOfficeLevelOption = OfficeLevel::all();
        $this->firstZonalEducationOfficeOption = ZonalEducationOffice::active()->orderBy('short_name', 'asc')->get();
        $this->firstInstitutionCategoryOption = InstitutionCategory::active()->get();


        // Load logged-in user + workplace + office level
        $user = Auth::user()->load('workplace.officeLevel');

        $workplace = $user->workplace;
        $officeLevelRank = OfficeLevel::where('office_level_id', $workplace->office_level_id)->first();
        
        if (!$workplace) {
            abort(403, 'You do not have a registered workplace.');
        }

        // Get hierarchy child workplaces (FAST BFS)
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        $this->currentOfficeLevelOption = OfficeLevel::where('office_level_id', '>=', $officeLevelRank->office_level_id)->get();


        $this->currentZonalEducationOfficeOption = ZonalEducationOffice::whereIn(
            'workplace_id',
            $allowedWorkplaceIds
        )->active()->orderBy('short_name', 'asc')->get();

        $this->currentInstitutionCategoryOption = InstitutionCategory::active()->get();

        if ($this->peopleId) {
        }
    }

    public function updatedNic($value)
    {
        $this->nicCheck = false;
        $value = strtoupper(trim($value));

        // Only check when NIC is complete (Sri Lankan NIC = 10 or 12 characters)
        if (strlen($value) !== 10 && strlen($value) !== 12) {
            return;
        }

        // ----------------------------
        // 1. Validate NIC format ONLY
        // ----------------------------
        $this->validateOnly('nic', [
            'nic' => ['required', 'regex:/^(?:[0-9]{9}[VvXx]|[0-9]{12})$/'],
        ]);

        // Check database
        $nic_hash = hash('sha256', $value);
        $existingPerson = People::where('nic_hash', $nic_hash)->first();

        if ($existingPerson) {
            $this->peopleId = $existingPerson->people_id;
        }
    }

    public function searchNic()
    {
        // Validate NIC format
        $this->validateOnly('nic', [
            'nic' => ['required', 'regex:/^(?:[0-9]{9}[VvXx]|[0-9]{12})$/'],
        ]);

        $this->nicCheck = false;
        $nic = NicHelper::normalize($this->nic);

        if (!$nic || strlen($nic) < 10) {
            $this->addError('nic', 'Please enter a valid NIC before searching.');
            return;
        }

        // Check NIC validity
        if (!NicHelper::checkNicValid($nic)) {
            $this->addError('nic', 'Invalid NIC number');
            return;
        }

        // Check database
        $nic_hash = NicHelper::hash($nic);
        $existingPerson = People::where('nic_hash', $nic_hash)->first();

        if ($existingPerson) {
            $employementStatus = EmployerCurrentAppointment::where('employee_id', $existingPerson->people_id)->first();
            if ($employementStatus) {
                session()->flash('error', 'This person already have active employment. If you want to add as a new employee, please disable the current active employment.');
                return;
            }
            
            $this->peopleId = $existingPerson->people_id;

            // Autofill Step 1 & Step 2 fields
            $this->fillExistingPerson($existingPerson);

            session()->flash('info', 'Existing person found. Details auto-filled.');
        } else {
            // Reset all values except NIC
            $this->resetExcept('nic');
            $this->peopleId = null;
            $this->mount();

            session()->flash('success', 'New NIC detected. You may continue entering details.');
        }
        $this->nicCheck = true;
    }

    private function fillExistingPerson($p)
    {
        // Step 1
        $this->title = $p->title_id;
        $this->fullName = $p->full_name;
        $this->gender = $p->gender_id;
        $this->birthday = $p->date_of_birth;
        $this->religion = $p->religion_id;
        $this->ethnicity = $p->ethnicity_id;
        $this->civilStatus = $p->civil_status_id;
        $this->bloodGroup = $p->blood_group_id;
        $this->district = $p->district_id;
        $this->gnDivision = $p->gn_division_id;

        // Load dependent dropdowns
        $this->divisionalSecretaryofficeOption = DivisionalSecretariatOffice::where('district_id', $p->district_id)->get();
        $findGnDivision = GnDivision::where('gn_division_id', $p->gn_division_id)->first();
        $this->divisionalDecretaryOffice = $findGnDivision->dso_id;
        $this->gnDivisionOption = GnDivision::where('dso_id', $this->divisionalDecretaryOffice)->get();

        // Step 2
        $this->contact = $p->phone;
        $this->email = $p->email;

        $this->addressLine1 = $p->address_line1;
        $this->addressLine2 = $p->address_line2;
        $this->addressLine3 = $p->address_line3;
        $this->postalCode = $p->postal_code;
        $this->latitude = $p->latitude;
        $this->longitude = $p->longitude;

        $this->tAddressLine1 = $p->t_address_line1;
        $this->tAddressLine2 = $p->t_address_line2;
        $this->tAddressLine3 = $p->t_address_line3;
        $this->tPostalCode = $p->t_postal_code;
    }


    public function updatedDistrict($value)
    {
        $this->divisionalSecretaryofficeOption = DivisionalSecretariatOffice::where('district_id', $value)->orderBy('dso_name')->get();
        $this->divisionalDecretaryOffice = '';
        $this->gnDivision = '';
        $this->gnDivisionOption = collect();
    }

    public function updatedDivisionalDecretaryOffice($value)
    {
        $this->gnDivisionOption = GnDivision::where('dso_id', $value)->orderBy('gn_division_name')->get();
        $this->gnDivision = '';
    }

    public function updatedService($value)
    {
        $this->ranksOption = ServiceRank::where('service_id', $value)->get();
        $this->serviceRank = '';

        $this->positionOption = Position::where('service_id', $value)->get();
        $this->position = '';
    }

    public function updatedCurrentService($value)
    {
        $this->currentRanksOption = ServiceRank::where('service_id', $value)->get();
        $this->currentServiceRank = '';

        $this->currentPositionOption = Position::where('service_id', $value)->get();
        $this->currentPosition = '';
    }

    public function updatedFirstOfficeLevel($value)
    {
        // reset dependent fields and options
        $this->firstZonalEducationOffice = null;
        $this->firstInstitutionCategory = null;
        $this->firstWorkingPlace = null;
        $this->firstWorkingPlaceOption = collect();

        if ($value === 'OLID006') {
            // institutions will be loaded by loadWorkingPlaces when ZEO + category are available
            $this->firstWorkingPlaceOption = collect();
        } else {
            // normal workplaces
            $this->firstWorkingPlaceOption = Workplaces::where('office_level_id', $value)->get();
        }
    }

    /**
     * ZEO changed -> Filter schools by zone + category
     */
    public function updatedFirstZonalEducationOffice($value)
    {
        // clear previous workingPlace selection
        $this->firstWorkingPlace = null;

        // loadWorkingPlaces will only populate if both required filters exist for OLID006
        $this->loadFirstWorkingPlaces();
    }

    /**
     * Category changed -> Filter schools by category + zone
     */
    public function updatedFirstInstitutionCategory($value)
    {
        $this->workingPlace = null;
        $this->loadFirstWorkingPlaces();
    }

    /**
     * Load workplace options depending on office level, ZEO and category
     */
    private function loadFirstWorkingPlaces()
    {
        // Ensure workingPlaceOption always a collection
        $this->firstWorkingPlaceOption = collect();

        // If officeLevel not set yet, nothing to load
        if (empty($this->firstOfficeLevel)) {
            return;
        }

        // Non-institutional levels: load workplaces by office level
        if ($this->firstOfficeLevel !== 'OLID006') {
            $this->firstWorkingPlaceOption = Workplaces::where('office_level_id', $this->firstOfficeLevel)
                //->orderBy('office_name') // if column exists; otherwise remove
                ->get();
            return;
        }

        // For institutions (OLID006) we require both ZEO and category to be present
        if (empty($this->firstZonalEducationOffice) || empty($this->firstInstitutionCategory)) {
            // Do not clear previously-loaded options in this case — keep empty collection
            return;
        }

        // Load filtered institution workplaces
        $this->firstWorkingPlaceOption = Workplaces::where('office_level_id', 'OLID006')
            ->whereHas('institution', function ($q) {
                $q->where('zeo_wp_id', $this->firstZonalEducationOffice)
                  ->where('institution_category_id', $this->firstInstitutionCategory);
            })
            ->with(['institution' => function ($q) {
                $q->orderBy('name', 'asc');
            }])
            ->get()
            ->sortBy(fn($w) => optional($w->institution)->name)
            ->values();
    }


    public function updatedCurrentOfficeLevel($value)
    {
        // reset dependent fields and options
        $this->currentZonalEducationOffice = null;
        $this->currentInstitutionCategory = null;
        $this->currentWorkingPlace = null;
        $this->currentWorkingPlaceOption = collect();

        if ($value === 'OLID006') {
            // institutions will be loaded by loadWorkingPlaces when ZEO + category are available
            $this->currentWorkingPlaceOption = collect();
        } else {
            // Load logged-in user + workplace + office level
            $user = Auth::user()->load('workplace.officeLevel');

            $workplace = $user->workplace;
            
            if (!$workplace) {
                abort(403, 'You do not have a registered workplace.');
            }

            // Get hierarchy child workplaces (FAST BFS)
            $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();
            
            // normal workplaces
            $this->currentWorkingPlaceOption = Workplaces::where('office_level_id', $value)
            ->whereIn('workplace_id', $allowedWorkplaceIds)
            ->get();
        }
    }

    /**
     * ZEO changed -> Filter schools by zone + category
     */
    public function updatedCurrentZonalEducationOffice($value)
    {
        // clear previous workingPlace selection
        $this->currentWorkingPlace = null;

        // loadWorkingPlaces will only populate if both required filters exist for OLID006
        $this->loadCurrentWorkingPlaces();
    }

    /**
     * Category changed -> Filter schools by category + zone
     */
    public function updatedCurrentInstitutionCategory($value)
    {
        $this->workingPlace = null;
        $this->loadCurrentWorkingPlaces();
    }

    /**
     * Load workplace options depending on office level, ZEO and category
     */
    private function loadCurrentWorkingPlaces()
    {
        // Ensure workingPlaceOption always a collection
        $this->currentWorkingPlaceOption = collect();

        // If officeLevel not set yet, nothing to load
        if (empty($this->currentOfficeLevel)) {
            return;
        }

        // Non-institutional levels: load workplaces by office level
        if ($this->currentOfficeLevel !== 'OLID006') {
            $this->currentWorkingPlaceOption = Workplaces::where('office_level_id', $this->currentOfficeLevel)
                //->orderBy('office_name') // if column exists; otherwise remove
                ->get();
            return;
        }

        // For institutions (OLID006) we require both ZEO and category to be present
        if (empty($this->currentZonalEducationOffice) || empty($this->currentInstitutionCategory)) {
            // Do not clear previously-loaded options in this case — keep empty collection
            return;
        }

        // Load filtered institution workplaces
        $this->currentWorkingPlaceOption = Workplaces::where('office_level_id', 'OLID006')
            ->whereHas('institution', function ($q) {
                $q->where('zeo_wp_id', $this->currentZonalEducationOffice)
                  ->where('institution_category_id', $this->currentInstitutionCategory);
            })
            ->with(['institution' => function ($q) {
                $q->orderBy('name', 'asc');
            }])
            ->get()
            ->sortBy(fn($w) => optional($w->institution)->name)
            ->values();
    }

    // public function updatedZonalEducationOffice($value)
    // {
    //     if ($value && $this->institutionCategory) {
    //         $this->institutionOption = Institution::where('zeo_wp_id', $value)
    //             ->where('institution_category_id', $this->institutionCategory)
    //             ->orderBy('name', 'asc')
    //             ->get();
    //         $this->institution = '';
    //     } else {
    //         $this->institutionOption = collect();
    //     }
    // }

    // public function updatedCurrentZonalEducationOffice($value)
    // {
    //     if ($value && $this->currentInstitutionCategory) {
    //         $this->currentInstitutionOption = Institution::where('zeo_wp_id', $value)
    //             ->where('institution_category_id', $this->currentInstitutionCategory)
    //             ->orderBy('name', 'asc')
    //             ->get();
    //         $this->currentInstitution = '';
    //     } else {
    //         $this->currentInstitutionOption = collect();
    //     }
    // }

    // public function updatedInstitutionCategory($value)
    // {
    //     if ($value && $this->zonalEducationOffice) {
    //         $this->institutionOption = Institution::where('institution_category_id', $value)
    //             ->where('zeo_wp_id', $this->zonalEducationOffice)
    //             ->orderBy('name', 'asc')
    //             ->get();
    //         $this->institution = '';
    //     } else {
    //         $this->institutionOption = collect();
    //     }
    // }

    // public function updatedCurrentInstitutionCategory($value)
    // {
    //     if ($value && $this->currentZonalEducationOffice) {
    //         $this->currentInstitutionOption = Institution::where('institution_category_id', $value)
    //             ->where('zeo_wp_id', $this->currentZonalEducationOffice)
    //             ->orderBy('name', 'asc')
    //             ->get();
    //         $this->currentInstitution = '';
    //     } else {
    //         $this->currentInstitutionOption = collect();
    //     }
    // }

    public function updatedHealthCondition()
    {
        if ($this->healthCondition == true) {
            $this->healthProblem = null;
        }
    }

    // -------------------------
    // Dynamic Dropdown Behaviors
    // -------------------------
    public function updatedDOSRegType($value)
    {
        if ($value === 'new') {
            // Copy values from first appointment to current appointment
            $this->currentAppointmentDate = $this->firstAppointmentDate;
            $this->currentAppointmentLetterNo = $this->appointmentLetterNo;
            $this->currentService = $this->service;
            $this->currentServiceRank = $this->serviceRank;
            $this->currentZonalEducationOffice = $this->zonalEducationOffice;
            $this->currentInstitutionCategory = $this->institutionCategory;
            $this->currentInstitution = $this->institution;

            // Update dropdown options
            $this->currentRanksOption = $this->ranksOption;
            $this->currentInstitutionOption = $this->institutionOption;
        } else {
            // Reset current appointment fields for existing development officer
            $this->reset([
                'currentAppointmentDate',
                'currentAppointmentLetterNo',
                'currentService',
                'currentServiceRank',
                'currentZonalEducationOffice',
                'currentInstitutionCategory',
                'currentInstitution'
            ]);
            $this->currentRanksOption = collect();
            $this->currentInstitutionOption = collect();
        }
    }

    // -------------------------
    // Save Logic
    // -------------------------
    public function save()
    {
        $this->validate($this->rulesForCurrentStep());

        DB::beginTransaction();

        try {
            // Convert health condition to boolean properly
            $healthCondition = filter_var($this->healthCondition, FILTER_VALIDATE_BOOLEAN);

            // Generate initials
            $initials = People::generateInitials($this->fullName);

            $nic = NicHelper::normalize($this->nic);

            // Save People
            $people = People::updateOrCreate(
                ['nic_hash' => NicHelper::hash($nic)],
                [
                    'nic' => $nic,
                    'title_id' => $this->title,
                    'full_name' => ucwords(strtolower($this->fullName)),
                    'name_with_initials' => $initials,
                    'gender_id' => $this->gender,
                    'date_of_birth' => $this->birthday,
                    'religion_id' => $this->religion,
                    'ethnicity_id' => $this->ethnicity,
                    'civil_status_id' => $this->civilStatus,
                    'health_condition' => $healthCondition,
                    'health_problem' => $healthCondition ? null : $this->healthProblem,
                    'blood_group_id' => $this->bloodGroup,
                    'district_id' => $this->district,
                    //'dso_id' => $this->divisionalDecretaryOffice, // Fixed: Added missing DSO field
                    'gn_division_id' => $this->gnDivision,
                    'email' => strtolower(trim($this->email)),
                    'phone' => $this->contact,
                    'address_line1' => ucwords(strtolower($this->addressLine1)),
                    'address_line2' => ucwords(strtolower($this->addressLine2)),
                    'address_line3' => ucwords(strtolower($this->addressLine3 ?? null)),
                    'postal_code' => $this->postalCode,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    't_address_line1' => ucwords(strtolower($this->tAddressLine1 ?? null)),
                    't_address_line2' => ucwords(strtolower($this->tAddressLine2 ?? null)),
                    't_address_line3' => ucwords(strtolower($this->tAddressLine3 ?? null)),
                    't_postal_code' => $this->tPostalCode,
                    'profile_picture' => 'default.png',
                ]
            );

            // Calculate retirement date (55 years from birth)
            $retirementDate = Carbon::parse($people->date_of_birth)->addYears(55);

            // Generate appointment ID
            $appointmentId = EmployerAppointment::generateAppointmentId($this->firstAppointmentDate);

            // Create Employer Appointment
            $appointment = EmployerAppointment::create([
                'appointment_id' => $appointmentId,
                'employee_id' => $people->people_id,
                'first_appointment_date' => $this->firstAppointmentDate,
                'retirement_date' => $retirementDate,
                'service_id' => $this->service,
                'rank_id' => $this->serviceRank,
                'position_id' => $this->position,
                'office_level_id' => $this->firstOfficeLevel,
                'workplace_id' => $this->firstWorkingPlace,
                'appointment_letter_no' => $this->appointmentLetterNo,
                'appointment_letter' => 'default_letter.pdf',
                'w_op_no' => null,
            ]);

            // Determine current appointment values based on registration type
            $currentOfficeLevelID = $this->dosRegType === 'new' ? $this->firstOfficeLevel : $this->currentOfficeLevel;
            $currentWorkplaceId = $this->dosRegType === 'new' ? $this->firstWorkingPlace : $this->currentWorkingPlace;
            $currentServiceId = $this->dosRegType === 'new' ? $this->service : $this->currentService;
            $currentRankId = $this->dosRegType === 'new' ? $this->serviceRank : $this->currentServiceRank;
            $currentAppointDate = $this->dosRegType === 'new' ? $this->firstAppointmentDate : $this->currentAppointmentDate;
            $currentAppointmentLetterNo = $this->dosRegType === 'new' ? $this->appointmentLetterNo : $this->currentAppointmentLetterNo;

            // Create Current Appointment
            EmployerCurrentAppointment::create([
                'appointment_id' => $appointment->appointment_id,
                'employee_id' => $people->people_id,
                'appoint_date' => $currentAppointDate,
                'appointment_letter_no' => $currentAppointmentLetterNo,
                'service_id' => $currentServiceId,
                'rank_id' => $currentRankId,
                'office_level_id' => $currentOfficeLevelID,
                'position_id' => $this->currentPosition,
                'workplace_id' => $currentWorkplaceId,
            ]);

            $password = Str::random(6);

            // Create or update User account
            $user = User::updateOrCreate(
                ['nic_hash' => $people->nic_hash],
                [
                    'nic' => $people->nic,
                    'people_id' => $people->people_id,
                    'name' => $people->name_with_initials,
                    'email' => $people->email,
                    'contact' => $people->phone,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]
            );

            // Assign development officer role
            $user->assignRole('development Officer');

            DB::commit();

            // Send password to user
            Mail::to($user->email)->send(
                new SendUserPassword($password)
            );

            session()->flash('success', 'Development Officer created successfully! Default password: ' . $password);
            $this->resetForm();
            $this->dispatch('scroll-top');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');
            $this->dispatch('scroll-top');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Development Officer data. Duplicate entry');
            $this->dispatch('scroll-top');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Development Officer creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
            $this->dispatch('scroll-top');
        }
    }

    private function resetForm()
    {
        $this->reset([
            'peopleId',
            'nic',
            'title',
            'fullName',
            'gender',
            'birthday',
            'religion',
            'ethnicity',
            'civilStatus',
            'bloodGroup',
            'healthCondition',
            'healthProblem',
            'contact',
            'email',
            'district',
            'divisionalDecretaryOffice',
            'gnDivision',
            'addressLine1',
            'addressLine2',
            'addressLine3',
            'postalCode',
            'latitude',
            'longitude',
            'firstAppointmentDate',
            'appointmentLetterNo',
            'service',
            'serviceRank',
            'firstOfficeLevel',
            'firstZonalEducationOffice',
            'firstInstitutionCategory',
            'firstWorkingPlace',
            'position',
            'dosRegType',
            'currentAppointmentDate',
            'currentAppointmentLetterNo',
            'currentService',
            'currentServiceRank',
            'currentOfficeLevel',
            'currentZonalEducationOffice',
            'currentInstitutionCategory',
            'currentWorkingPlace',
            'currentPosition',
        ]);

        // Reset to step 1
        $this->step = 1;
        $this->dosRegType = 'existing';
        $this->healthCondition = true;

        // Reload dropdown options
        $this->mount();
    }

    public function render()
    {
        return view('livewire.d-o-s.d-o-s-create');
    }
}
