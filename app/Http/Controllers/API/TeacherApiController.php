<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Title;
use App\Models\People;
use App\Models\Service;

use App\Models\Teacher;
use App\Models\Position;
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
use Illuminate\Http\Request;
use App\Models\DistrictsList;
use App\Models\ApointedSubject;
use App\Models\TeacherCategory;
use Illuminate\Support\Facades\DB;
use App\Models\EmployerAppointment;
use App\Models\InstitutionCategory;
use App\Models\MediumOfInstruction;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\In;
use App\Http\Controllers\Controller;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\Hash;
use App\Models\EmployerCurrentAppointment;
use App\Support\Auth\PasswordGenerator;
use App\Models\DivisionalSecretariatOffice;

use Illuminate\Validation\ValidationException;

class TeacherApiController extends Controller
{
    public function index()
    {
        return response()->json([
            "status" => "success",

            "titles" => Title::active()->get(),
            "genders" => GenderList::active()->get(),
            "religions" => Religion::active()->get(),
            "ethnicities" => Ethnicity::active()->get(),
            "civil_statuses" => CivilStatus::active()->get(),
            "blood_groups" => BloodGroup::all(),
            "districts" => DistrictsList::active()->get(),

            // Services
            "services" => Service::active()->get(),
            "service_ranks" => ServiceRank::active()->get(),

            // Teacher settings
            "teacher_categories" => TeacherCategory::active()->get(),
            "teacher_types" => TeacherType::active()->get(),

            // Subjects
            "subjects" => SubjectList::active()->orderBy('name_en')->get(),
            "appointed_subjects" => ApointedSubject::active()->orderBy('name_en')->get(),

            // Medium
            "mediums" => MediumOfInstruction::active()->get(),
        ]);
    }

    public function teacherList(Request $request)
    {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $nic     = trim($request->get('nic'));

            $teachers = People::query()
                ->whereHas('teacher')
                ->when($nic, function ($query) use ($nic) {

                    // Validate NIC first
                    if (! NicHelper::checkNicValid($nic)) {
                        return;
                    }

                    // Normalize + hash NIC
                    $normalizedNic = NicHelper::normalize($nic);
                    $nicHash       = NicHelper::hash($normalizedNic);

                    $query->where('nic_hash', $nicHash);
                })
                ->with([
                    'teacher.teacherCategory',
                    'teacher.teacherType',
                    'appointment',
                    'currentAppointment.workplace.institution',
                ])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data'   => $teachers,
            ], 200);
        } catch (\Throwable $e) {

            Log::error('Teacher List Fetch Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch teacher list',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // ==============================
            // BASIC VALIDATION
            // ==============================
            $validated = $request->validate([
                // PERSONAL
                'nic' => 'required|string',
                'is_new_registration' => 'required|boolean',

                'titleId' => 'required|string',
                'fullName' => 'required|string',
                'dateOfBirth' => 'required|date',
                'genderId' => 'required|string',
                'religionId' => 'required|string',
                'ethnicityId' => 'required|string',
                'civilStatusId' => 'required|string',
                'bloodGroupId' => 'required|string',

                'healthCondition' => 'required',
                'healthConditionDescription' => 'nullable|string',

                'districtId' => 'required|string',
                'gnDivisionId' => 'required|string',
                'dsOfficeId' => 'required|string',

                // CONTACT
                'email' => 'required|email',
                'contact' => 'required|string',

                'addressLine1' => 'required|string',
                'addressLine2' => 'required|string',
                'addressLine3' => 'nullable|string',
                'postalCode' => 'required|string',

                // FIRST APPOINTMENT
                'firstAppointmentCategory' => 'required|string',
                'firstAppointmentDate' => 'required|date',
                'firstAppointmentLetter' => 'required|string',

                'firstAppointmentService' => 'required|string',
                'firstAppointmentRank' => 'required|string',
                'firstAppointmentType' => 'required|string',
                'firstAppointmentSubject' => 'required|string',
                'firstAppointmentMedium' => 'required|string',
                'firstAppointmentTeachingSubject' => 'required|string',

                'firstAppointmentZone' => 'required|string',
                'firstAppointmentInstCategory' => 'required|string',
                'firstAppointmentInstitution' => 'required|string',
                'firstAppointmentPosition' => 'required|string',

                // CURRENT APPOINTMENT
                'currentAppointmentRegType' => 'required|string',
                'currentAppointmentDate' => 'required|date',
                'currentAppointmentLetter' => 'required|string',

                'currentAppointmentService' => 'required|string',
                'currentAppointmentRank' => 'required|string',
                'currentAppointmentSubject' => 'required|string',
                'currentAppointmentZone' => 'required|string',
                'currentAppointmentInstCategory' => 'required|string',
                'currentAppointmentInstitution' => 'required|string',
                'currentAppointmentPosition' => 'required|string',
            ]);

            DB::beginTransaction();

            // ==============================
            // NORMALIZE DATA
            // ==============================

            // Convert health condition to boolean properly
            $healthCondition = filter_var($validated['healthCondition'], FILTER_VALIDATE_BOOLEAN);

            // Generate initials
            $initials = People::generateInitials($validated['fullName']);

            $nic = NicHelper::normalize($validated['nic']);

            // ==============================
            // PEOPLE
            // ==============================
            $people = People::updateOrCreate(
                ['nic_hash' => NicHelper::hash($nic)],
                [
                    'nic' => $nic,
                    'title_id' => $validated['titleId'],
                    'full_name' => ucwords(strtolower($validated['fullName'])),
                    'name_with_initials' => $initials,
                    'gender_id' => $validated['genderId'],
                    'date_of_birth' => $validated['dateOfBirth'],
                    'religion_id' => $validated['religionId'],
                    'ethnicity_id' => $validated['ethnicityId'],
                    'civil_status_id' => $validated['civilStatusId'],
                    'blood_group_id' => $validated['bloodGroupId'],
                    'health_condition' => $validated['healthCondition'],
                    'health_problem' => $validated['healthConditionDescription'],
                    'district_id' => $validated['districtId'],
                    'gn_division_id' => $validated['gnDivisionId'],
                    'ds_office_id' => $validated['dsOfficeId'],
                    'email' => strtolower($validated['email']),
                    'phone' => $validated['contact'],
                    'address_line1' => $validated['addressLine1'],
                    'address_line2' => $validated['addressLine2'],
                    'address_line3' => $validated['addressLine3'],
                    'postal_code' => $validated['postalCode'],
                    'profile_picture' => 'default.png',
                ]
            );

            // ==============================
            // FIRST APPOINTMENT
            // ==============================

            // Calculate retirement date (55 years from birth)
            $retirementDate = Carbon::parse($people->date_of_birth)->addYears(55);

            // Generate appointment ID
            $appointmentId = EmployerAppointment::generateAppointmentId($validated['firstAppointmentDate']);

            EmployerAppointment::create([
                'appointment_id' => $appointmentId,
                'employee_id' => $people->people_id,
                'first_appointment_date' => $validated['firstAppointmentDate'],
                'retirement_date' => $retirementDate->toDateString(),
                'service_id' => $validated['firstAppointmentService'],
                'rank_id' => $validated['firstAppointmentRank'],
                'position_id' => $validated['firstAppointmentPosition'],
                'office_level_id' => 'OLID006',
                'workplace_id' => $validated['firstAppointmentInstitution'],
                'appointment_letter_no' => $validated['firstAppointmentLetter'],
                'appointment_letter' => 'none.pdf',
            ]);

            // ==============================
            // TEACHER
            // ==============================
            Teacher::create([
                'appointment_id' => $appointmentId,
                'employee_id' => $people->people_id,
                'teacher_category' => $validated['firstAppointmentCategory'],
                'teacher_type' => $validated['firstAppointmentType'],
                'appointment_medium' => $validated['firstAppointmentMedium'],
                'appointment_subject' => $validated['firstAppointmentSubject'],
                'main_subject' => $validated['firstAppointmentTeachingSubject'],
                'current_teaching_subject' => $validated['currentAppointmentSubject'],
            ]);

            // ==============================
            // CURRENT APPOINTMENT
            // ==============================
            EmployerCurrentAppointment::create([
                'appointment_id' => $appointmentId,
                'employee_id' => $people->people_id,
                'appoint_date' => $validated['currentAppointmentDate'],
                'rank_id' => $validated['currentAppointmentRank'],
                'office_level_id' => 'OLID006',
                'position_id' => $validated['currentAppointmentPosition'],
                'workplace_id' => $validated['currentAppointmentInstitution'],
            ]);

            // ==============================
            // SYSTEM USER
            // ==============================
            $password = PasswordGenerator::compliant();

            $user = User::create([
                'nic' => $nic,
                'nic_hash' => NicHelper::hash($nic),
                'people_id' => $people->people_id,
                'name' => $people->name_with_initials,
                'email' => strtolower($validated['email']),
                'contact' => $validated['contact'],
                'password' => Hash::make($password),
            ]);

            $user->assignRole('teacher');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Teacher created successfully',
                'people_id' => $people->people_id,
                'default_password' => $password,
            ], 201);
        }
        // ==============================
        // VALIDATION ERROR
        // ==============================
        catch (ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
        // ==============================
        // SERVER ERROR
        // ==============================
        catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Teacher Store Error', ['error' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error' . $e->getMessage(),
            ], 500);
        }
    }



    public function getTeacher($people_id)
    {
        $teacher = People::with([

            'title',
            'gender',
            'religion',
            'ethnicity',
            'civilStatus',
            'bloodGroup',
            'district',
            'gnDivision',

            // FIXED NAMES
            'myAppointments',                  // all appointments
            'appointment',                     // active first appointment
            'currentAppointment',              // current active appointment
            'appointmentHistory',              // full appointment history

            // Appointment → workplace
            'currentAppointment.workplace',
            'currentAppointment.workplace.ministry',
            'currentAppointment.workplace.provincial',
            'currentAppointment.workplace.zonal',
            'currentAppointment.workplace.divisional',
            'currentAppointment.workplace.institution',

            // Teacher relationships (if exist)
            'teacher',
            'teacher.appointmentSubject',
            'teacher.mainSubject',
            'teacher.secondarySubject',
            'teacher.currentTeachingSubject',

        ])
            ->where('people_id', $people_id)
            ->first();

        if (!$teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Teacher not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $teacher,
        ], 200);
    }

    public function getTeacherWithNIC(string $nic)
    {
        try {
            // 1️⃣ Validate directly (no need to normalize manually)
            if (! NicHelper::isValid($nic)) {
                return response()->json([
                    'status'        => 'error',
                    'message'       => 'NIC verification failed. Please provide a valid NIC number.',
                    'nic_verified'  => false,
                ], 422);
            }

            // 2️⃣ Generate secure hash (internally normalizes)
            $nicHash = NicHelper::hash($nic);

            // 3️⃣ Fetch Teacher Only
            $people = People::query()
                //->whereHas('teacher') // ensures it's a teacher
                ->with([
                    'title',
                    'gender',
                    'religion',
                    'ethnicity',
                    'civilStatus',
                    'bloodGroup',
                    'district',
                    'gnDivision.divisionalSecretariatOffice',
                    'currentAppointment.workplace.institution',
                    'appointment',
                ])
                ->where('nic_hash', $nicHash)
                ->whereHas('teacher')
                ->with([
                    'title:id,name',
                    'gender:id,name',
                    'religion:id,name',
                    'ethnicity:id,name',
                    'civilStatus:id,name',
                    'bloodGroup:id,name',
                    'district:id,name',
                    'gnDivision:id,name,divisional_secretariat_office_id',
                    'gnDivision.divisionalSecretariatOffice:id,name',
                    'currentAppointment:id,people_id,workplace_id',
                    'currentAppointment.workplace:id,institution_id',
                    'currentAppointment.workplace.institution:id,name',
                    'appointment:id,people_id',
                ])
                ->first();

            // 4️⃣ If not found
            if (! $people) {
                return response()->json([
                    'status'             => 'success',
                    'message'            => 'No teacher found for this NIC.',
                    'nic_verified'       => true,
                    'teacher_found'      => false,
                    'active_appointment' => false,
                ]);
            }

            // 5️⃣ Active appointment check (more accurate)
            $hasActiveAppointment = $people->currentAppointment !== null;

            return response()->json([
                'status'             => 'success',
                'message'            => 'Teacher record found for this NIC.',
                'nic_verified'       => true,
                'teacher_found'      => true,
                'active_appointment' => $hasActiveAppointment,
                'data'               => $people,
            ]);
        } catch (\Throwable $e) {

            Log::error('Get Teacher By NIC Error', [
                'nic'     => $nic,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch teacher data',
            ], 500);
        }
    }


    public function getPersonalFromData(Request $request)
    {
        $districtId = $request->query('district');
        $dsOfficeId = $request->query('ds_office');

        return response()->json([
            'status' => 'success',

            'titles' => Title::active()->get(),
            'genders' => GenderList::active()->get(),
            'religions' => Religion::active()->get(),
            'ethnicities' => Ethnicity::active()->get(),
            'civilStatuses' => CivilStatus::active()->get(),
            'bloodGroups' => BloodGroup::all(),

            'districts' => DistrictsList::active()->get(),

            'divisionalSecretariats' => $districtId
                ? DivisionalSecretariatOffice::where('district_id', $districtId)
                ->active()
                ->get()
                : [],

            'gnDivisions' => $dsOfficeId
                ? GnDivision::where('dso_id', $dsOfficeId)
                ->active()
                ->get()
                : [],
        ]);
    }

    public function getAppoinmentFromData(Request $request)
    {
        $service = $request->query('service');
        $institutionCategory = $request->query('ins_cat');
        $zone = $request->query('zone');

        return response()->json([
            'status' => 'success',

            'teacherCategorys' => TeacherCategory::active()->get(),
            'teacherTypes' => TeacherType::active()->get(),
            'apointmentSubjects' => ApointedSubject::active()->orderBy('name_en')->get(),
            'appointmentMedium' => MediumOfInstruction::active()->get(),
            'service' => Service::active()->get(),
            'serviceRanks' => $service ? ServiceRank::where('service_id', $service)->active()->get() : [],
            'positions' => $service ? Position::where('service_id', $service)->active()->get() : [],
            'mainTeachingSubjects' => SubjectList::active()->orderBy('name_en')->get(),
            'aapointedSubjects' => ApointedSubject::active()->orderBy('name_en')->get(),
            'institutionCategory' => InstitutionCategory::active()->get(),
            'zonalEducationOffices' => ZonalEducationOffice::active()->get(),
            'institutions' => $zone && $institutionCategory ? Institution::where('zeo_wp_id', $zone)->where('institution_category_id', $institutionCategory)->get() : [],
        ]);
    }
}
