<?php

namespace App\Livewire\Employees;

use App\Models\Title;
use App\Models\People;
use App\Models\Service;
use Livewire\Component;
use App\Models\Position;
use App\Models\PrincipalRecruitmentCategory;
use App\Models\EducationAdministratorServiceSubject;
use App\Models\EducationAdministratorServiceCategory;
use App\Models\Religion;
use App\Models\Ethnicity;
use App\Helpers\NicHelper;
use App\Models\BloodGroup;
use App\Models\GenderList;
use App\Models\GnDivision;
use App\Models\CivilStatus;
use App\Models\Institution;
use App\Models\ServiceRank;
use App\Models\SubjectList;
use App\Models\TeacherType;
use App\Models\DistrictsList;
use App\Models\ApointedSubject;
use App\Models\TeacherCategory;
use App\Models\OfficeLevel;
use App\Models\Workplaces;
use App\Http\Controllers\EmployeeController;
use App\Models\InstitutionCategory;
use App\Models\MediumOfInstruction;
use Illuminate\Support\Facades\Log;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\Auth;
use App\Rules\UniqueEmailAcrossTables;
use App\Rules\UniquePhoneAcrossTables;
use App\Models\EmployerCurrentAppointment;
use App\Models\DivisionalSecretariatOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class EmployeeCreate extends Component
{
    public ?string $employeeServiceType = null;
    public ?string $typeOfEmployee = null;
    public bool $nicCheck = false;
    public int $step = 1;
    public int $maxStep = 5;
    public ?string $peopleId = null;
    public ?string $myNewNic = null;

    // -------------------------
    // Personal Details
    // -------------------------
    public ?string $nic = null, $title = null, $fullName = null, $gender = null, $birthday = null, $religion = null;
    public ?string $ethnicity = null, $civilStatus = null, $bloodGroup = null, $healthProblem = null;
    public ?string $healthCondition = '1';
    public ?string $district = null, $divisionalDecretaryOffice = null, $gnDivision = null;

    // -------------------------
    // Contact Details
    // -------------------------
    public ?string $contact = null, $email = null;
    public ?string $addressLine1 = null, $addressLine2 = null, $addressLine3 = null, $postalCode = null, $latitude = null, $longitude = null;
    public ?string $tAddressLine1 = null, $tAddressLine2 = null, $tAddressLine3 = null, $tPostalCode = null;

    // -------------------------
    // Appointment Details (Common)
    // -------------------------
    public ?string $firstAppointmentDate = null, $appointmentLetterNo = null;
    public ?string $service = null, $serviceRank = null, $position = null;
    public ?string $zonalEducationOffice = null, $institutionCategory = null, $institution = null;
    public ?string $officeLevel = 'OLID006', $workingPlace = null;
    public array|Collection $officeLevelOption = [], $workingPlaceOption = [];

    // -------------------------
    // Service Specific Details (Teachers)
    // -------------------------
    public ?string $teacherCategory = null, $teacherType = null, $appointmentSubject = null, $appointmentMedium = null;
    public ?string $mainTeachingSubject = null, $secondaryTeachingSubject = null;
    public ?string $principalCategory = null, $cadreSubject = null, $cadreMedium = null;
    public ?string $recruitmentCategory = null, $recruitmentSubject = null;

    // -------------------------
    // Current Appointment
    // -------------------------
    public string $regType = 'new';
    public ?string $currentAppointmentDate = null, $currentAppointmentLetterNo = null, $currentService = null, $currentServiceRank = null;
    public ?string $currentZonalEducationOffice = null, $currentInstitutionCategory = null, $currentInstitution = null, $currentTeachingSubject = null, $currentPosition = null;
    public ?string $currentOfficeLevel = 'OLID006', $currentWorkingPlace = null;
    public array|Collection $currentWorkingPlaceOption = [];

    // -------------------------
    // Dropdown Options
    // -------------------------
    public array|Collection $titleOptions = [], $religionOptions = [], $genderOptions = [], $ethnicityOptions = [], $civilStatusOptions = [];
    public array|Collection $bloodGroupOptions = [], $healthConditionOptions = [];
    public array|Collection $districtOption = [], $divisionalSecretaryofficeOption = [], $gnDivisionOption = [];
    public array|Collection $servicesOption = [], $ranksOption = [], $currentRanksOption = [], $institutionCategoryOption = [], $institutionOption = [], $currentInstitutionOption = [];
    public array|Collection $teacherCategoriesOption = [], $appointmentSubjectOption = [], $subjectOption = [], $appointmentMediumOptions = [];
    public array|Collection $teacherTypeOptions = [], $zonalEducationOfficeOption = [], $positionOption = [], $currentPositionOption = [];
    public array|Collection $currentZonalEducationOfficeOption = [];
    public array|Collection $principalCategoriesOption = [], $principalSubjectOption = [];
    public array|Collection $recruitmentCategoryOption = [], $recruitmentSubjectOption = [];

    protected function rulesForCurrentStep(): array
    {
        switch ($this->step) {
            case 1:
                return [
                    'nic' => ['required', 'string', 'min:10', 'max:12', 'regex:/^(?:[0-9]{9}[vVxX]|[0-9]{12})$/'],
                ];
            case 2:
                return [
                    'title' => 'required|string',
                    'fullName' => 'required|string|max:255',
                    'gender' => 'required|string',
                    'birthday' => ['required', 'date', 'before:today', 'after:' . now()->subYears(60)->toDateString()],
                    'religion' => 'required|string',
                    'ethnicity' => 'required|string',
                    'civilStatus' => 'required|string',
                    'bloodGroup' => 'required|string',
                    'healthCondition' => 'required|in:1,0',
                    'healthProblem' => 'nullable|required_if:healthCondition,0|string|max:1000',
                    'district' => 'required|string',
                    'divisionalDecretaryOffice' => 'required|string',
                    'gnDivision' => 'required|string',
                ];
            case 3:
                return [
                    'contact' => ['required', 'string', 'min:10', 'max:10', new UniquePhoneAcrossTables($this->peopleId), 'regex:/^0\d{9}$/'],
                    'email' => ['required', 'email', new UniqueEmailAcrossTables($this->peopleId)],
                    'addressLine1' => 'required|string|max:255',
                    'addressLine2' => 'required|string|max:255',
                    'postalCode' => 'required|string|max:10',
                ];
            case 4:
                $rules = [
                    'service' => 'required|string',
                    'firstAppointmentDate' => ['required', 'date', 'before_or_equal:today'],
                    'appointmentLetterNo' => 'required|string|max:255',
                    'serviceRank' => 'required|string',
                    'position' => 'required|string',
                    'officeLevel' => 'required|string',
                    'workingPlace' => 'required|string',
                ];

                // Additional rules for School level
                if ($this->officeLevel === 'OLID006') {
                    $rules['zonalEducationOffice'] = 'required|string';
                    $rules['institutionCategory'] = 'required|string';
                }

                // Teacher Specific Rules
                if ($this->service === 'SER001') {
                    $rules = array_merge($rules, [
                        'teacherCategory' => 'required|string',
                        'teacherType' => 'required|string',
                        'appointmentSubject' => 'required|string',
                        'appointmentMedium' => 'required|string',
                        'mainTeachingSubject' => 'required|string',
                    ]);
                }

                // Principal Specific Rules
                if ($this->service === 'SER004') {
                    $rules = array_merge($rules, [
                        'principalCategory' => 'required|string',
                        'cadreSubject' => 'required|string',
                        'cadreMedium' => 'required|string',
                    ]);
                }

                // SLEAS Specific Rules
                if ($this->service === 'SER005') {
                    $rules = array_merge($rules, [
                        'recruitmentCategory' => 'required|string',
                        'recruitmentSubject' => 'required|string',
                        'cadreMedium' => 'required|string',
                    ]);
                }
                return $rules;

            case 5:
                // For fresh appointments, current fields are auto-synced — no need to validate separately
                if ($this->regType === 'new') {
                    $rules = [
                        'regType' => 'required|string|in:new,existing',
                    ];
                    // Cadre still required for Principal/SLEAS at school level
                    if (in_array($this->service, ['SER004', 'SER005']) && $this->officeLevel === 'OLID006') {
                        $rules['cadreSubject'] = 'required|string';
                        $rules['cadreMedium'] = 'required|string';
                    }
                    if ($this->service === 'SER001') {
                        $rules['currentTeachingSubject'] = 'required|string';
                    }
                    return $rules;
                }

                // For existing registrations, current appointment fields are required
                $rules = [
                    'regType'                    => 'required|string|in:new,existing',
                    'currentAppointmentDate'     => ['required', 'date', 'before_or_equal:today'],
                    'currentAppointmentLetterNo' => 'nullable|string',
                    'currentServiceRank'         => 'required|string',
                    'currentPosition'            => 'required|string',
                    'currentOfficeLevel'         => 'required|string',
                    'currentWorkingPlace'        => 'required|string',
                ];

                if ($this->currentOfficeLevel === 'OLID006') {
                    $rules['currentZonalEducationOffice'] = 'required|string';
                    $rules['currentInstitutionCategory']  = 'required|string';
                }

                if ($this->service === 'SER001') {
                    $rules['currentTeachingSubject'] = 'required|string';
                }

                if (in_array($this->service, ['SER004', 'SER005']) && $this->currentOfficeLevel === 'OLID006') {
                    $rules['cadreSubject'] = 'required|string';
                    $rules['cadreMedium']  = 'required|string';
                }
                return $rules;

            default:
                return [];
        }
    }

    protected array $messages = [
        'nic.required' => 'NIC is required',
        'nic.regex' => 'Please enter a valid NIC number',
        'fullName.required' => 'Full Name is required',
        'email.required' => 'Email is required',
        'contact.required' => 'Contact number is required',
        'healthProblem.required_if' => 'Please provide health problem details',
    ];

    public function mount($typeOfEmployee = null)
    {
        $this->typeOfEmployee = $typeOfEmployee;
        $this->employeeServiceType = $typeOfEmployee;

        $this->initializeForm();
    }

    private function initializeForm()
    {
        $this->loadInitialOptions();
        $this->loadServicesByType();
        $this->loadUserZeoOptions();

        $this->healthCondition = '1';
    }

    private function loadInitialOptions()
    {
        $this->titleOptions = Title::active()->get();
        $this->genderOptions = GenderList::active()->get();
        $this->religionOptions = Religion::active()->get();
        $this->ethnicityOptions = Ethnicity::active()->get();
        $this->civilStatusOptions = CivilStatus::active()->get();
        $this->bloodGroupOptions = BloodGroup::all();
        $this->districtOption = DistrictsList::active()->orderBy('district_name')->get();

        $this->institutionCategoryOption = InstitutionCategory::active()->get();
        $this->teacherCategoriesOption = TeacherCategory::active()->get();
        $this->subjectOption = SubjectList::active()->where('type', '!=', '2')->orderBy('name_en')->get();
        $this->appointmentSubjectOption = ApointedSubject::active()->orderBy('name_en')->get();
        $this->appointmentMediumOptions = MediumOfInstruction::active()->get();
        $this->teacherTypeOptions = TeacherType::active()->get();

        $this->zonalEducationOfficeOption = ZonalEducationOffice::active()->orderBy('short_name')->get();
        $this->officeLevelOption = OfficeLevel::active()->get();

        $this->principalCategoriesOption = PrincipalRecruitmentCategory::active()->get();
        $this->principalSubjectOption = SubjectList::active()->where('type', '2')->orderBy('name_en')->get();

        $this->recruitmentCategoryOption = EducationAdministratorServiceCategory::active()->get();
        $this->recruitmentSubjectOption = EducationAdministratorServiceSubject::active()->get();

        $this->healthConditionOptions = ['1' => 'Yes', '0' => 'No'];

        $this->divisionalSecretaryofficeOption = [];
        $this->gnDivisionOption = [];
    }

    private function loadServicesByType()
    {
        if ($this->typeOfEmployee === 'ALL') {

            $this->servicesOption = Service::active()->get();
        } elseif ($this->typeOfEmployee) {

            $service = Service::where('service_name', $this->typeOfEmployee)->first();

            if ($service) {
                $this->servicesOption = collect([$service]);
                $this->service = $service->service_id;

                $this->ranksOption = ServiceRank::where('service_id', $this->service)->active()->get();
                $this->positionOption = Position::where('service_id', $this->service)->active()->get();
                $this->currentRanksOption = ServiceRank::where('service_id', $this->service)->active()->get();
                $this->currentPositionOption = Position::where('service_id', $this->service)->active()->get();
            } else {
                $this->servicesOption = collect();
            }
        } else {
            $this->servicesOption = collect();
        }
    }

    private function loadUserZeoOptions()
    {
        $user = Auth::user()?->load('workplace');

        if ($user && $user->workplace) {
            $allowedIds = $user->workplace->getAllChildWorkplaces();

            $this->currentZonalEducationOfficeOption = ZonalEducationOffice::whereIn('workplace_id', $allowedIds)
                ->active()
                ->orderBy('short_name')
                ->get();
        }
    }

    public function searchNic()
    {
        $this->validateOnly('nic', [
            'nic' => ['required', 'regex:/^(?:[0-9]{9}[VvXx]|[0-9]{12})$/']
        ]);

        $nic = NicHelper::normalize($this->nic);
        $nicHash = NicHelper::hash($nic);

        $existingPerson = People::where('nic_hash', $nicHash)->first();

        if ($existingPerson) {

            $currentApp = EmployerCurrentAppointment::where('employee_id', $existingPerson->people_id)->first();

            if ($currentApp) {
                $this->addError('nic', 'This person already has active employment.');
                return;
            }

            $this->peopleId = $existingPerson->people_id;
            $this->fillExistingPerson($existingPerson);

            session()->flash('info', 'Existing record found and filled.');
        } else {

            // preserve type
            $type = $this->typeOfEmployee;

            // reset only form fields
            $this->reset([
                'peopleId',
                'fullName',
                'gender',
                'birthday',
                'religion',
                'ethnicity',
                'civilStatus',
                'bloodGroup',
                'healthProblem',
                'district',
                'divisionalDecretaryOffice',
                'gnDivision',
                'contact',
                'email',
                'addressLine1',
                'addressLine2',
                'postalCode'
            ]);

            // restore type
            $this->typeOfEmployee = $type;
            $this->employeeServiceType = $type;

            // reload dependent data
            $this->loadServicesByType();

            session()->flash('success', 'New employee registration.');
        }

        $this->nicCheck = true;
    }

    private function fillExistingPerson(People $p)
    {
        $this->title = $p->title_id;
        $this->fullName = $p->full_name;
        $this->gender = $p->gender_id;
        $this->birthday = $p->date_of_birth;
        $this->religion = $p->religion_id;
        $this->ethnicity = $p->ethnicity_id;
        $this->civilStatus = $p->civil_status_id;
        $this->bloodGroup = $p->blood_group_id;
        $this->district = $p->district_id;
        if ($this->district) {
            $this->divisionalSecretaryofficeOption = DivisionalSecretariatOffice::where('district_id', $this->district)->active()->get();
        }
        $this->contact = $p->phone;
        $this->email = $p->email;
        $this->addressLine1 = $p->address_line1;
        $this->addressLine2 = $p->address_line2;
        $this->postalCode = $p->postal_code;
    }

    public function updated(string $propertyName)
    {
        $rules = $this->rulesForCurrentStep();
        if (array_key_exists($propertyName, $rules)) {
            $this->validateOnly($propertyName, $rules);
        }

        if ($propertyName === 'district') {
            $this->divisionalSecretaryofficeOption = DivisionalSecretariatOffice::where('district_id', $this->district)->active()->get();
            $this->divisionalDecretaryOffice = null;
            $this->gnDivision = null;
            $this->gnDivisionOption = [];
        }

        if ($propertyName === 'divisionalDecretaryOffice') {
            $this->gnDivisionOption = GnDivision::where('dso_id', $this->divisionalDecretaryOffice)->active()->get();
            $this->gnDivision = null;
        }

        if ($propertyName === 'service') {
            $this->ranksOption = ServiceRank::where('service_id', $this->service)->active()->get();
            $this->positionOption = Position::where('service_id', $this->service)->active()->get();
            $this->currentRanksOption = ServiceRank::where('service_id', $this->service)->active()->get();
            $this->currentPositionOption = Position::where('service_id', $this->service)->active()->get();
            $this->teacherTypeOptions = TeacherType::active()->get();
        }

        if ($propertyName === 'officeLevel') {
            $this->zonalEducationOffice = null;
            $this->institutionCategory = null;
            $this->workingPlace = null;
            $this->workingPlaceOption = [];

            // If it's not a school level, we might need to load other types of workplaces
            if ($this->officeLevel && $this->officeLevel !== 'OLID006') {
                $this->workingPlaceOption = Workplaces::where('office_level_id', $this->officeLevel)
                    ->active()
                    ->get();
            }
        }

        if ($propertyName === 'zonalEducationOffice' || $propertyName === 'institutionCategory') {
            if ($this->officeLevel === 'OLID006' && $this->zonalEducationOffice && $this->institutionCategory) {
                $this->workingPlaceOption = Institution::where('zeo_wp_id', $this->zonalEducationOffice)
                    ->where('institution_category_id', $this->institutionCategory)
                    ->active()
                    ->orderBy('name', 'asc')
                    ->get()
                    ->map(function ($item) {
                        return (object) [
                            'workplace_id' => $item->workplace_id,
                            'office_name' => $item->census_no . ' - ' . $item->name
                        ];
                    });
            } else {
                $this->workingPlaceOption = [];
            }
            $this->workingPlace = null;
        }

        if ($propertyName === 'currentOfficeLevel') {
            $this->currentZonalEducationOffice = null;
            $this->currentInstitutionCategory = null;
            $this->currentWorkingPlace = null;
            $this->currentWorkingPlaceOption = [];

            if ($this->currentOfficeLevel && $this->currentOfficeLevel !== 'OLID006') {
                $this->currentWorkingPlaceOption = Workplaces::where('office_level_id', $this->currentOfficeLevel)
                    ->active()
                    ->get();
            }
        }

        if ($propertyName === 'currentZonalEducationOffice' || $propertyName === 'currentInstitutionCategory') {
            if ($this->currentOfficeLevel === 'OLID006' && $this->currentZonalEducationOffice && $this->currentInstitutionCategory) {
                $this->currentWorkingPlaceOption = Institution::where('zeo_wp_id', $this->currentZonalEducationOffice)
                    ->where('institution_category_id', $this->currentInstitutionCategory)
                    ->active()
                    ->orderBy('name', 'asc')
                    ->get()
                    ->map(function ($item) {
                        return (object) [
                            'workplace_id' => $item->workplace_id,
                            'office_name' => $item->census_no . ' - ' . $item->name
                        ];
                    });
            } else {
                $this->currentWorkingPlaceOption = [];
            }
            $this->currentWorkingPlace = null;
        }

        if ($propertyName === 'currentService') {
            $this->currentRanksOption = ServiceRank::where('service_id', $this->currentService)->active()->get();
            $this->currentPositionOption = Position::where('service_id', $this->currentService)->active()->get();
        }

        if ($propertyName === 'healthCondition') {
            if ($this->healthCondition === '0') {
                $this->healthProblem = null;
            }
        }

        if ($propertyName === 'regType' && $this->regType === 'new') {
            $this->syncFirstToCurrent();
        }
    }

    public function nextStep()
    {
        $this->validate($this->rulesForCurrentStep());
        if ($this->step < $this->maxStep) {
            $this->step++;

            if ($this->step === 5 && $this->regType === 'new') {
                $this->syncFirstToCurrent();
            }

            $this->resetValidation();
            $this->dispatch('scroll-top');
        }
    }

    private function syncFirstToCurrent()
    {
        $this->currentAppointmentDate = $this->firstAppointmentDate;
        $this->currentAppointmentLetterNo = $this->appointmentLetterNo;
        $this->currentServiceRank = $this->serviceRank;
        $this->currentPosition = $this->position;
        $this->currentOfficeLevel = $this->officeLevel;
        $this->currentZonalEducationOffice = $this->zonalEducationOffice;
        $this->currentInstitutionCategory = $this->institutionCategory;
        $this->currentWorkingPlace = $this->workingPlace;
        $this->currentWorkingPlaceOption = $this->workingPlaceOption;

        // Sync teaching subject for teachers
        if ($this->service === 'SER001') {
            $this->currentTeachingSubject = $this->mainTeachingSubject;
        }

        // Cadre Sync
        if (in_array($this->service, ['SER004', 'SER005'])) {
            // These will be editable in step 5 if needed, but defaulted here
            // Note: In individual components they are only in step 5
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

    private function resetForm()
    {
        $type = $this->typeOfEmployee;

        $this->reset();

        // restore important values
        $this->typeOfEmployee = $type;
        $this->employeeServiceType = $type;

        $this->initializeForm();

        $this->step = 1;
        $this->nicCheck = false;

        $this->dispatch('scroll-top');
    }

    public function save()
    {
        Log::info('save() called', ['step' => $this->step, 'regType' => $this->regType, 'service' => $this->service]);

        // For fresh appointments, ensure current data is synced from first appointment
        if ($this->regType === 'new') {
            $this->syncFirstToCurrent();
        }

        $this->validate($this->rulesForCurrentStep());
        Log::info('save() passed validation');

        try {
            $requestData = [
                'nic' => $this->nic,
                'title_id' => $this->title,
                'full_name' => $this->fullName,
                'gender_id' => $this->gender,
                'date_of_birth' => $this->birthday,
                'religion_id' => $this->religion,
                'ethnicity_id' => $this->ethnicity,
                'civil_status_id' => $this->civilStatus,
                'blood_group_id' => $this->bloodGroup,
                'health_condition' => $this->healthCondition,
                'health_problem' => $this->healthProblem,
                'email' => $this->email,
                'phone' => $this->contact,
                'district_id' => $this->district,
                'gn_division_id' => $this->gnDivision,
                'address_line1' => $this->addressLine1,
                'address_line2' => $this->addressLine2,
                'postal_code' => $this->postalCode,
                'first_appointment_date' => $this->firstAppointmentDate,
                'service_id' => $this->service,
                'rank_id' => $this->serviceRank,
                'position_id' => $this->position,
                'office_level_id' => $this->officeLevel,
                'workplace_id' => $this->workingPlace,
                'zeo_id' => $this->zonalEducationOffice,
                'institution_category_id' => $this->institutionCategory,
                'appointment_letter_no' => $this->appointmentLetterNo,
                'is_fresh_appointment' => $this->regType === 'new',
                'current_appointment_date' => $this->currentAppointmentDate,
                'current_rank_id' => $this->currentServiceRank,
                'current_position_id' => $this->currentPosition,
                'current_workplace_id' => $this->currentWorkingPlace,
                'current_office_level_id' => $this->currentOfficeLevel,
                'current_zeo_id' => $this->currentZonalEducationOffice,
                'current_institution_category_id' => $this->currentInstitutionCategory,
                'current_teaching_subject_id' => $this->currentTeachingSubject,

                // Teacher Specifics
                'teacher_category_id' => $this->teacherCategory,
                'teacher_type_id' => $this->teacherType,
                'appointment_subject_id' => $this->appointmentSubject,
                'main_teaching_subject_id' => $this->mainTeachingSubject,
                'secondary_subject_id' => $this->secondaryTeachingSubject,

                // Principal & Service Specifics
                'principal_category_id' => $this->principalCategory,
                'subject_id' => ($this->service === 'SER004') ? $this->cadreSubject : (($this->service === 'SER005') ? $this->recruitmentSubject : null),
                'appointment_medium_id' => ($this->service === 'SER001') ? $this->appointmentMedium : $this->cadreMedium,

                // SLEAS Specifics
                'category_id' => $this->recruitmentCategory,
                'cadre_subject_id' => $this->cadreSubject,
                'cadre_medium_id' => $this->cadreMedium,
            ];

            Log::info('About to call controller register', ['requestData' => $requestData]);

            $request = Request::create('/api/employees/register', 'POST', $requestData);
            $response = (new EmployeeController())->register($request);
            $result = json_decode($response->getContent(), true);

            Log::info('Controller returned', ['status' => $result['status'] ?? 'unknown']);

            if ($result['status'] === 'success') {
                session()->flash('success', 'Employee registered successfully! Employee ID: ' . ($result['data']['people_id'] ?? 'N/A'));

                // Reset form for new registration
                $this->resetForm();

                return;
            }

            if ($result['status'] === 'validation_error') {
                $errorMsg = 'Validation failed: ' . implode(', ', array_map(fn($e) => implode(' ', $e), $result['errors']));
                Log::warning('Validation errors', ['errors' => $result['errors']]);
                session()->flash('error', $errorMsg);
            } else {
                session()->flash('error', $result['message'] ?? 'Registration failed.');
            }
        } catch (\Throwable $e) {
            Log::error('Unified Registration Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error occurred: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employees.employee-create');
    }
}
