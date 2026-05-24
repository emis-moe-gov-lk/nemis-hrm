<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\People;
use App\Models\Teacher;
use App\Models\Principal;
use App\Helpers\NicHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\SendUserPassword;
use App\Support\Auth\PasswordGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\EmployerAppointment;
use App\Models\EmployerCadreSubject;
use App\Models\EmployerCurrentAppointment;
use App\Models\EducationAdministratorService;
use App\Models\EmployerAppointmentRankHistory;
use App\Models\EmployerAppointmentPositionHistory;
use App\Models\EmployerAppointmentWorkplaceHistory;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    /**
     * Centralized employee registration endpoint.
     * Orchestrates People, Employment, Service-specific data, and User account creation.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // 1. Unified Validation
            $validated = $request->validate([
                // Personal Data (People)
                'nic'              => ['required', 'string', 'regex:/^(\d{9}[vVxX]|\d{12})$/'],
                'title_id'         => 'required|string',
                'full_name'        => 'required|string|max:255',
                'gender_id'        => 'required|string',
                'date_of_birth'    => 'required|date',
                'religion_id'      => 'required|string',
                'ethnicity_id'     => 'required|string',
                'civil_status_id'  => 'nullable|string',
                'blood_group_id'   => 'nullable|string',
                'health_condition' => 'nullable|boolean',
                'health_problem'   => 'nullable|string',
                'email'            => 'required|email',
                'phone'            => 'required|string',
                'district_id'      => 'nullable|string',
                'gn_division_id'   => 'nullable|string',
                'address_line1'    => 'nullable|string|max:255',
                'address_line2'    => 'nullable|string|max:255',
                'address_line3'    => 'nullable|string|max:255',
                'postal_code'      => 'nullable|string|max:20',
                'latitude'         => 'nullable|numeric',
                'longitude'        => 'nullable|numeric',
                't_address_line1'  => 'nullable|string|max:255',
                't_address_line2'  => 'nullable|string|max:255',
                't_address_line3'  => 'nullable|string|max:255',
                't_postal_code'    => 'nullable|string|max:20',

                // Employment Data (EmployerAppointment)
                'first_appointment_date' => 'required|date',
                'service_id'             => 'required|string|exists:services,service_id',
                'rank_id'                => 'required|string',
                'position_id'            => 'required|string',
                'office_level_id'        => 'required|string',
                'workplace_id'           => 'required|string',
                'appointment_letter_no'  => 'nullable|string|max:255',
                'is_fresh_appointment'   => 'required|boolean',

                // Current Appointment (If not fresh)
                'current_appointment_date' => 'nullable|date',
                'current_rank_id'          => 'nullable|string',
                'current_position_id'      => 'nullable|string',
                'current_workplace_id'     => 'nullable|string',
                'current_office_level_id'  => 'nullable|string',

                // Service Specific Data (Conditional based on service_id)
                'teacher_category_id'      => 'nullable|required_if:service_id,SER001|string',
                'teacher_type_id'          => 'nullable|required_if:service_id,SER001|string',
                'appointment_subject_id'   => 'nullable|required_if:service_id,SER001|string',
                'main_teaching_subject_id' => 'nullable|required_if:service_id,SER001|string',
                'secondary_subject_id'     => 'nullable|string',
                'principal_category_id'    => 'nullable|required_if:service_id,SER004|string',
                'category_id'              => 'nullable|required_if:service_id,SER005|string',
                'cadre_subject_id'         => 'nullable|string',
                'appointment_medium_id'    => 'nullable|required_if:service_id,SER001,SER004|string',
                'subject_id'               => 'nullable|required_if:service_id,SER004,SER005|string',
            ]);

            DB::beginTransaction();

            // 2. Sync People Record
            $nic = NicHelper::normalize($validated['nic']);
            $nicHash = NicHelper::hash($nic);
            $initials = People::generateInitials($validated['full_name']);
            $healthCondition = filter_var($validated['health_condition'], FILTER_VALIDATE_BOOLEAN);

            $people = People::updateOrCreate(
                ['nic_hash' => $nicHash],
                [
                    'nic'                => $nic,
                    'title_id'           => $validated['title_id'],
                    'full_name'          => ucwords(strtolower($validated['full_name'])),
                    'name_with_initials' => $initials,
                    'gender_id'          => $validated['gender_id'],
                    'date_of_birth'      => $validated['date_of_birth'],
                    'religion_id'        => $validated['religion_id'],
                    'ethnicity_id'       => $validated['ethnicity_id'],
                    'civil_status_id'    => $validated['civil_status_id'] ?? 'C01',
                    'blood_group_id'     => $validated['blood_group_id'] ?? null,
                    'health_condition'   => $healthCondition,
                    'health_problem'     => $validated['health_problem'] ?? null,
                    'email'              => strtolower($validated['email']),
                    'phone'              => $validated['phone'],
                    'address_line1'      => $validated['address_line1'] ?? null,
                    'address_line2'      => $validated['address_line2'] ?? null,
                    'address_line3'      => $validated['address_line3'] ?? null,
                    'postal_code'        => $validated['postal_code'] ?? null,
                    'latitude'           => $validated['latitude'] ?? null,
                    'longitude'          => $validated['longitude'] ?? null,
                    't_address_line1'    => $validated['t_address_line1'] ?? null,
                    't_address_line2'    => $validated['t_address_line2'] ?? null,
                    't_address_line3'    => $validated['t_address_line3'] ?? null,
                    't_postal_code'      => $validated['t_postal_code'] ?? null,
                    'gn_division_id'     => $validated['gn_division_id'] ?? null,
                    'district_id'        => $validated['district_id'] ?? null,
                    'active_status'      => 1,
                ]
            );

            // 3. Sync Employment (Employer Appointment)
            $retirementDate = Carbon::parse($people->date_of_birth)->addYears(60);

            $appointment = EmployerAppointment::updateOrCreate(
                [
                    'employee_id'   => $people->people_id,
                    'active_status' => 1
                ],
                [
                    'service_id'             => $validated['service_id'],
                    'first_appointment_date' => $validated['first_appointment_date'],
                    'retirement_date'        => $retirementDate,
                    'rank_id'                => $validated['rank_id'],
                    'position_id'            => $validated['position_id'],
                    'office_level_id'        => $validated['office_level_id'],
                    'workplace_id'           => $validated['workplace_id'],
                    'appointment_letter_no'  => $validated['appointment_letter_no'] ?? null,
                    'active_status'          => 1,
                ]
            );

            // 4. Sync Current Appointment
            $currentData = [
                'employee_id'     => $people->people_id,
                'appoint_date'    => $validated['is_fresh_appointment'] ? $validated['first_appointment_date'] : ($validated['current_appointment_date'] ?? $validated['first_appointment_date']),
                'rank_id'         => $validated['is_fresh_appointment'] ? $validated['rank_id'] : ($validated['current_rank_id'] ?? $validated['rank_id']),
                'office_level_id' => $validated['is_fresh_appointment'] ? $validated['office_level_id'] : ($validated['current_office_level_id'] ?? $validated['office_level_id']),
                'position_id'     => $validated['is_fresh_appointment'] ? $validated['position_id'] : ($validated['current_position_id'] ?? $validated['position_id']),
                'workplace_id'    => $validated['is_fresh_appointment'] ? $validated['workplace_id'] : ($validated['current_workplace_id'] ?? $validated['workplace_id']),
            ];

            EmployerCurrentAppointment::updateOrCreate(
                ['appointment_id' => $appointment->appointment_id],
                array_merge($currentData, ['active_status' => 1])
            );

            EmployerAppointmentPositionHistory::create([
                'appointment_id' => $appointment->appointment_id,
                'employee_id'    => $people->people_id,
                'position_id'    => $currentData['position_id'],
                'start_date'     => $currentData['appoint_date'],
                'ref_letter_no'  => $validated['appointment_letter_no'] ?? null,
                'is_active'      => 1,
            ]);

            EmployerAppointmentRankHistory::create([
                'appointment_id' => $appointment->appointment_id,
                'employee_id'    => $people->people_id,
                'rank_id'        => $currentData['rank_id'],
                'start_date'     => $currentData['appoint_date'],
                'ref_letter_no'  => $validated['appointment_letter_no'] ?? null,
                'is_active'      => 1,
            ]);

            EmployerAppointmentWorkplaceHistory::create([
                'appointment_id'  => $appointment->appointment_id,
                'employee_id'     => $people->people_id,
                'workplace_id'    => $currentData['workplace_id'],
                'office_level_id' => $currentData['office_level_id'],
                'start_date'      => $currentData['appoint_date'],
                'ref_letter_no'   => $validated['appointment_letter_no'] ?? null,
                'is_active'       => 1,
            ]);

            // 5. Sync Service-Specific Details
            $this->syncServiceDetails($people, $appointment, $validated);

            // 6. Sync User Account
            $password = PasswordGenerator::compliant();
            $user = User::updateOrCreate(
                ['nic_hash' => $people->nic_hash],
                [
                    'nic'               => $people->nic,
                    'people_id'         => $people->people_id,
                    'name'              => $people->name_with_initials,
                    'email'             => $people->email,
                    'contact'           => $people->phone,
                    'password'          => Hash::make($password),
                    'email_verified_at' => now(),
                ]
            );

            // Assign role based on service
            $role = $this->getRoleByService($validated['service_id']);
            if ($role) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                $user->syncRoles([$role]);
            }

            DB::commit();

            // Send password mail
            try {
                Mail::to($user->email)->send(new SendUserPassword($password));
            } catch (Exception $e) {
                Log::warning('Failed to send registration email: ' . $e->getMessage());
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Employee registered successfully.',
                'data'    => [
                    'people_id'      => $people->people_id,
                    'appointment_id' => $appointment->appointment_id,
                    'user_id'        => $user->id,
                    'password'       => $password
                ]
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'validation_error',
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Employee Registration Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace'   => $e->getTraceAsString()
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user role name based on service ID.
     */
    private function getRoleByService(string $serviceId)
    {
        $map = [
            'SER001' => 'teacher',
            'SER004' => 'principal',
            'SER005' => 'sleas officer',
            'SER003' => 'Teacher Advisor',
            'SER002' => 'Teacher Educator',
            'SER006' => 'Administrative Service',
            'SER007' => 'development officer',
            'SER008' => 'management assistant',
        ];

        return $map[$serviceId] ?? null;
    }

    /**
     * Sync service-specific records.
     */
    private function syncServiceDetails(People $people, EmployerAppointment $appointment, array $data)
    {
        switch ($data['service_id']) {
            case 'SER001': // Teacher
                Teacher::updateOrCreate(
                    ['appointment_id' => $appointment->appointment_id],
                    [
                        'employee_id'              => $people->people_id,
                        'teacher_category'         => $data['teacher_category_id'],
                        'teacher_type'             => $data['teacher_type_id'],
                        'appointment_medium'       => $data['appointment_medium_id'],
                        'appointment_subject'      => $data['appointment_subject_id'],
                        'main_subject'             => $data['main_teaching_subject_id'],
                        'secondary_subject'        => $data['secondary_subject_id'] ?? null,
                        'current_teaching_subject' => $data['main_teaching_subject_id'],
                    ]
                );

                EmployerCadreSubject::updateOrCreate(
                    ['appointment_id' => $appointment->appointment_id],
                    [
                        'employee_id'        => $people->people_id,
                        'appointment_medium' => $data['appointment_medium_id'],
                        'main_subject'       => $data['main_teaching_subject_id'],
                    ]
                );
                break;

            case 'SER004': // Principal
                Principal::updateOrCreate(
                    ['appointment_id' => $appointment->appointment_id],
                    [
                        'employee_id'          => $people->people_id,
                        'recruitment_category' => $data['principal_category_id'],
                    ]
                );

                EmployerCadreSubject::updateOrCreate(
                    ['appointment_id' => $appointment->appointment_id],
                    [
                        'employee_id'        => $people->people_id,
                        'appointment_medium' => $data['appointment_medium_id'],
                        'main_subject'       => $data['subject_id'],
                    ]
                );
                break;

            case 'SER005': // SLEAS
                EducationAdministratorService::updateOrCreate(
                    ['appointment_id' => $appointment->appointment_id],
                    [
                        'employee_id' => $people->people_id,
                        'category_id' => $data['category_id'],
                        'subject'     => $data['subject_id'],
                    ]
                );

                if (!empty($data['appointment_medium_id']) && !empty($data['cadre_subject_id'])) {
                    EmployerCadreSubject::updateOrCreate(
                        ['appointment_id' => $appointment->appointment_id],
                        [
                            'employee_id'        => $people->people_id,
                            'appointment_medium' => $data['appointment_medium_id'],
                            'main_subject'       => $data['cadre_subject_id'],
                        ]
                    );
                }
                break;
        }
    }
}
