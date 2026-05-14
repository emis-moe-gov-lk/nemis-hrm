<?php

namespace App\Livewire\Principal;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Title;
use App\Models\People;
use App\Models\Service;

use Livewire\Component;
use App\Models\Position;
use App\Models\Religion;
use App\Models\Ethnicity;
use App\Models\Principal;
use App\Helpers\NicHelper;
use App\Models\BloodGroup;
use App\Models\GenderList;
use App\Models\GnDivision;
use App\Models\CivilStatus;
use App\Models\Institution;
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
use App\Models\EmployerCadreSubject;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Rules\UniqueEmailAcrossTables;
use App\Rules\UniquePhoneAcrossTables;
use App\Models\EmployerCurrentAppointment;
use App\Models\DivisionalSecretariatOffice;
use App\Models\PrincipalRecruitmentCategory;

class PrincipalCreate extends Component
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
    public $principalCategory, $firstAppointmentDate, $appointmentLetterNo;
    public $service, $serviceRank;
    public $zonalEducationOffice, $institutionCategory, $institution;
    public $position;

    // -------------------------
    // Current Appointment
    // -------------------------
    public $principalRegType = 'existing', $currentAppointmentDate, $currentAppointmentLetterNo, $currentService, $currentServiceRank;
    public $currentZonalEducationOffice, $currentInstitutionCategory, $currentInstitution, $currentTeachingSubject, $currentPosition;
    public $cadreSubject, $cadreMedium;

    // -------------------------
    // Dropdown Options
    // -------------------------
    public $titleOptions = [], $religionOptions = [], $genderOptions = [], $ethnicityOptions = [], $civilStatusOptions = [];
    public $bloodGroupOptions = [], $healthConditionOptions = [];
    public $districtOption = [], $divisionalSecretaryofficeOption = [], $gnDivisionOption = [];
    public $servicesOption = [], $ranksOption = [], $currentRanksOption = [], $institutionCategoryOption = [], $institutionOption = [], $currentInstitutionOption = [];
    public $principalCategoriesOption = [];
    public $zonalEducationOfficeOption = [];
    public $positionOption = [];
    public $currentPositionOption = [];
    public $subjectOption = [];
    public $mediumOption = [];

    public $currentZonalEducationOfficeOption = [];

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
                    'principalCategory' => 'required|string',
                    'firstAppointmentDate' => [
                        'required',
                        'date',
                        'after_or_equal:' . Carbon::now()->subYears(60)->toDateString(),
                        'before_or_equal:today',
                    ],
                    'appointmentLetterNo' => 'required|string|max:255',
                    'service' => 'required|string',
                    'serviceRank' => 'required|string',
                    'zonalEducationOffice' => 'required|string',
                    'institutionCategory' => 'required|string',
                    'institution' => 'required|string',
                    'position' => 'required|string',
                ];

                // ------------------------------------
                // STEP 5 → CURRENT APPOINTMENT
                // ------------------------------------
            case 5:
                return [
                    'principalRegType' => 'required|string|in:new,existing',
                    'currentAppointmentDate' => [
                        'required',
                        'date',
                        'after_or_equal:' . Carbon::now()->subYears(60)->toDateString(),
                        'before_or_equal:today',
                    ],
                    'currentAppointmentLetterNo' => 'nullable|string|max:255',
                    'currentService' => 'required|string',
                    'currentServiceRank' => 'required|string',
                    'currentZonalEducationOffice' => 'required|string',
                    'currentInstitutionCategory' => 'required|string',
                    'currentInstitution' => 'required|string',
                    'currentPosition' => 'required|string',
                    'cadreMedium' => 'required|string',
                    'cadreSubject' => 'required|string',
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
        $this->servicesOption = Service::where('service_id', 'SER004')->active()->get();
        $this->ranksOption = collect();
        $this->currentRanksOption = collect();
        $this->institutionCategoryOption = InstitutionCategory::active()->get();
        $this->institutionOption = collect();
        $this->currentInstitutionOption = collect();
        $this->principalCategoriesOption = PrincipalRecruitmentCategory::active()->get();
        $this->zonalEducationOfficeOption = ZonalEducationOffice::active()->orderBy('short_name', 'asc')->get();

        $this->subjectOption = SubjectList::active()->where('type', '=', '2')->orderBy('name_en', 'asc')->get();
        $this->mediumOption = MediumOfInstruction::active()->orderBy('id', 'asc')->get();

        $user = Auth::user()->load('workplace.officeLevel');

        $workplace = $user->workplace;

        if (!$workplace) {
            abort(403, 'You do not have a registered workplace.');
        }

        // Get hierarchy child workplaces (FAST BFS)
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // Get ONLY zonal education offices (ZEO)
        $zonalEducationOffices = ZonalEducationOffice::whereIn(
            'workplace_id',
            $allowedWorkplaceIds
        )->active()->orderBy('short_name', 'asc')->get();
        
        $this->currentZonalEducationOfficeOption = $zonalEducationOffices;

        $this->healthCondition = true;

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
        if (!$p) {
            return;
        }

        /* ---------- Step 1 ---------- */
        $this->title        = $p->title_id ?? null;
        $this->fullName     = $p->full_name ?? null;
        $this->gender       = $p->gender_id ?? null;
        $this->birthday     = $p->date_of_birth ?? null;
        $this->religion     = $p->religion_id ?? null;
        $this->ethnicity    = $p->ethnicity_id ?? null;
        $this->civilStatus  = $p->civil_status_id ?? null;
        $this->bloodGroup   = $p->blood_group_id ?? null;
        $this->district     = $p->district_id ?? null;
        $this->gnDivision   = $p->gn_division_id ?? null;

        /* ---------- Dependent Dropdowns ---------- */
        $this->divisionalSecretaryofficeOption =
            $p->district_id
            ? DivisionalSecretariatOffice::where('district_id', $p->district_id)->get()
            : collect();

        $findGnDivision = $p->gn_division_id
            ? GnDivision::where('gn_division_id', $p->gn_division_id)->first()
            : null;

        $this->divisionalDecretaryOffice = $findGnDivision?->dso_id;

        $this->gnDivisionOption =
            $this->divisionalDecretaryOffice
            ? GnDivision::where('dso_id', $this->divisionalDecretaryOffice)->get()
            : collect();

        /* ---------- Step 2 ---------- */
        $this->contact        = $p->phone ?? null;
        $this->email          = $p->email ?? null;

        $this->addressLine1   = $p->address_line1 ?? null;
        $this->addressLine2   = $p->address_line2 ?? null;
        $this->addressLine3   = $p->address_line3 ?? null;
        $this->postalCode     = $p->postal_code ?? null;
        $this->latitude       = $p->latitude ?? null;
        $this->longitude      = $p->longitude ?? null;

        $this->tAddressLine1  = $p->t_address_line1 ?? null;
        $this->tAddressLine2  = $p->t_address_line2 ?? null;
        $this->tAddressLine3  = $p->t_address_line3 ?? null;
        $this->tPostalCode    = $p->t_postal_code ?? null;
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

    public function updatedZonalEducationOffice($value)
    {
        if ($value && $this->institutionCategory) {
            $this->institutionOption = Institution::where('zeo_wp_id', $value)
                ->where('institution_category_id', $this->institutionCategory)
                ->orderBy('name', 'asc')
                ->get();
            $this->institution = '';
        } else {
            $this->institutionOption = collect();
        }
    }

    public function updatedCurrentZonalEducationOffice($value)
    {
        if ($value && $this->currentInstitutionCategory) {
            $this->currentInstitutionOption = Institution::where('zeo_wp_id', $value)
                ->where('institution_category_id', $this->currentInstitutionCategory)
                ->orderBy('name', 'asc')
                ->get();
            $this->currentInstitution = '';
        } else {
            $this->currentInstitutionOption = collect();
        }
    }

    public function updatedInstitutionCategory($value)
    {
        if ($value && $this->zonalEducationOffice) {
            $this->institutionOption = Institution::where('institution_category_id', $value)
                ->where('zeo_wp_id', $this->zonalEducationOffice)
                ->orderBy('name', 'asc')
                ->get();
            $this->institution = '';
        } else {
            $this->institutionOption = collect();
        }
    }

    public function updatedCurrentInstitutionCategory($value)
    {
        if ($value && $this->currentZonalEducationOffice) {
            $this->currentInstitutionOption = Institution::where('institution_category_id', $value)
                ->where('zeo_wp_id', $this->currentZonalEducationOffice)
                ->orderBy('name', 'asc')
                ->get();
            $this->currentInstitution = '';
        } else {
            $this->currentInstitutionOption = collect();
        }
    }

    public function updatedHealthCondition()
    {
        if ($this->healthCondition == true) {
            $this->healthProblem = null;
        }
    }

    // -------------------------
    // Dynamic Dropdown Behaviors
    // -------------------------
    public function updatedPrincipalRegType($value)
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
            // Reset current appointment fields for existing principal
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
                'office_level_id' => 'OLID006',
                'workplace_id' => $this->institution,
                'appointment_letter_no' => $this->appointmentLetterNo,
                'appointment_letter' => 'default_letter.pdf',
                'w_op_no' => null,
            ]);

            // Create Employer Cadre Subject
            EmployerCadreSubject::create([
                'appointment_id' => $appointment->appointment_id,
                'employee_id' => $people->people_id,
                'appointment_medium' => $this->cadreMedium,
                'main_subject' => $this->cadreSubject,
            ]);


            // Create Principal record
            Principal::create([
                'appointment_id' => $appointment->appointment_id,
                'employee_id' => $people->people_id,
                'recruitment_category' => $this->principalCategory,
            ]);

            // Determine current appointment values based on registration type
            $currentWorkplaceId = $this->principalRegType === 'new' ? $this->institution : $this->currentInstitution;
            $currentServiceId = $this->principalRegType === 'new' ? $this->service : $this->currentService;
            $currentRankId = $this->principalRegType === 'new' ? $this->serviceRank : $this->currentServiceRank;
            $currentAppointDate = $this->principalRegType === 'new' ? $this->firstAppointmentDate : $this->currentAppointmentDate;
            $currentAppointmentLetterNo = $this->principalRegType === 'new' ? $this->appointmentLetterNo : $this->currentAppointmentLetterNo;

            // Create Current Appointment
            EmployerCurrentAppointment::create([
                'appointment_id' => $appointment->appointment_id,
                'employee_id' => $people->people_id,
                'appoint_date' => $currentAppointDate,
                'appointment_letter_no' => $currentAppointmentLetterNo,
                'service_id' => $currentServiceId,
                'rank_id' => $currentRankId,
                'office_level_id' => 'OLID006',
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

            // Assign principal role
            $user->assignRole('principal');


            DB::commit();

            // Send password to user
            Mail::to($user->email)->send(
                new SendUserPassword($password)
            );

            session()->flash('success', 'principal created successfully! Default password: ' . $password);
            $this->resetForm();
            $this->dispatch('scroll-top');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');
            $this->dispatch('scroll-top');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save principal data. Duplicate entry');
            $this->dispatch('scroll-top');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('principal creation error: ' . $e->getMessage(), [
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
            'principalCategory',
            'firstAppointmentDate',
            'appointmentLetterNo',
            'service',
            'serviceRank',
            'zonalEducationOffice',
            'institutionCategory',
            'institution',
            'principalRegType',
            'currentAppointmentDate',
            'currentAppointmentLetterNo',
            'currentService',
            'currentServiceRank',
            'currentZonalEducationOffice',
            'currentInstitutionCategory',
            'currentInstitution',
        ]);

        // Reset to step 1
        $this->step = 1;
        $this->principalRegType = 'existing';
        $this->healthCondition = true;

        // Reload dropdown options
        $this->mount();
    }

    public function render()
    {
        return view('livewire.principal.principal-create');
    }
}
