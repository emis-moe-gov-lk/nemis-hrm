<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\EmployerAppointment;
use App\Models\EmployerCurrentAppointment;
use App\Models\Teacher;
use App\Models\EmployerCadreSubject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class PeopleEmploymentController extends Controller
{
    /**
     * Create a new employment record for a person.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNewOrExistingEmployment($request)
    {
        try {
            $data = $request instanceof Request ? $request->all() : $request;
            $validated = Validator::make($data, [
                'people_id' => 'required|string|exists:people,people_id',
                'first_appointment_date' => 'required|date',
                'appointment_letter_no' => 'nullable|string|max:255',
                'service_id' => 'required|string',
                'rank_id' => 'required|string',
                'position_id' => 'required|string',
                'office_level_id' => 'required|string',
                'workplace_id' => 'required|string',
                'is_fresh_appointment' => 'required|boolean',
            ])->validate();

            DB::beginTransaction();

            $people = People::where('people_id', $validated['people_id'])->firstOrFail();

            // 1. Create Employer Appointment (First Appointment)
            // Default retirement date: 60 years from birth (adjustable based on policy)
            $retirementDate = Carbon::parse($people->date_of_birth)->addYears(60);

            $appointment = EmployerAppointment::create([
                'employee_id' => $people->people_id,
                'first_appointment_date' => $validated['first_appointment_date'],
                'retirement_date' => $retirementDate,
                'service_id' => $validated['service_id'],
                'rank_id' => $validated['rank_id'],
                'position_id' => $validated['position_id'],
                'office_level_id' => $validated['office_level_id'],
                'workplace_id' => $validated['workplace_id'],
                'appointment_letter_no' => $validated['appointment_letter_no'] ?: null,
                'appointment_letter' => 'default_letter.pdf',
                'active_status' => $validated['is_fresh_appointment'],
            ]);

            // 2. Create Current Appointment (Initial state)
            if ($validated['is_fresh_appointment']) {
                EmployerCurrentAppointment::create([
                    'appointment_id' => $appointment->appointment_id,
                    'employee_id' => $people->people_id,
                    'appoint_date' => $validated['first_appointment_date'],
                    'appointment_letter_no' => $validated['appointment_letter_no'] ?: null,
                    'rank_id' => $validated['rank_id'],
                    'office_level_id' => $validated['office_level_id'],
                    'position_id' => $validated['position_id'],
                    'workplace_id' => $validated['workplace_id'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Employment record created successfully.',
                'data' => [
                    'appointment_id' => $appointment->appointment_id,
                ]
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('createNewOrExistingEmployment Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating employment record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing employment record.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAppointment($request)
    {
        try {
            $data = $request instanceof Request ? $request->all() : $request;
            $validated = Validator::make($data, [
                'appointment_id' => 'required|string|exists:employer_appointments,appointment_id',
                'first_appointment_date' => 'required|date',
                'appointment_letter_no' => 'nullable|string|max:255',
                'service_id' => 'required|string',
                'rank_id' => 'required|string',
                'position_id' => 'required|string',
                'office_level_id' => 'required|string',
                'workplace_id' => 'required|string',
            ])->validate();

            DB::beginTransaction();

            $appointment = EmployerAppointment::where('appointment_id', $validated['appointment_id'])->firstOrFail();

            $appointment->update([
                'first_appointment_date' => $validated['first_appointment_date'],
                'appointment_letter_no' => $validated['appointment_letter_no'] ?: null,
                'service_id' => $validated['service_id'],
                'rank_id' => $validated['rank_id'],
                'position_id' => $validated['position_id'],
                'office_level_id' => $validated['office_level_id'],
                'workplace_id' => $validated['workplace_id'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Employment record updated successfully.',
                'data' => $appointment
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('updateExistingEmployment Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating employment record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set a specific appointment as the "Current" active appointment for an employee.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCurrentStatusOfAppointment($request)
    {
        try {
            $data = $request instanceof Request ? $request->all() : $request;
            $validated = Validator::make($data, [
                'appointment_id' => 'required|string|exists:employer_appointments,appointment_id',
                'appointment_date' => 'required|date',
                'appointment_letter_no' => 'nullable|string|max:255',
                'rank_id' => 'required|string',
                'office_level_id' => 'required|string',
                'position_id' => 'required|string',
                'workplace_id' => 'required|string',
            ])->validate();

            DB::beginTransaction();

            // 3. Sync these details with the EmployerCurrentAppointment model
            $appointment = EmployerCurrentAppointment::updateOrCreate(
                ['appointment_id' => $validated['appointment_id']],
                [
                    'appointment_id' => $validated['appointment_id'],
                    'appoint_date' => $validated['appointment_date'],
                    'appointment_letter_no' => $validated['appointment_letter_no'] ?: null,
                    'rank_id' => $validated['rank_id'],
                    'office_level_id' => $validated['office_level_id'],
                    'position_id' => $validated['position_id'],
                    'workplace_id' => $validated['workplace_id'],
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Current appointment status updated and synchronized successfully.',
                'data' => [
                    'appointment_id' => $appointment->appointment_id,
                    'employee_id' => $appointment->employee_id
                ]
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('updateCurrentStatusOfAppointment Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating current status: ' . $e->getMessage()
            ], 500);
        }
    }
}
