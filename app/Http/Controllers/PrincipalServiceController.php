<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Principal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EmployerCadreSubject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PrincipalServiceController extends Controller
{
    /**
     * Create or update principal's service details.
     * 
     * @param Request|array $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPrincipalService($request)
    {
        try {
            $data = $request instanceof Request ? $request->all() : $request;

            $validated = Validator::make($data, [
                'appointment_id'        => 'required|exists:employer_appointments,appointment_id',
                'employee_id'           => 'required|exists:people,people_id',
                'principal_category_id' => 'required|string',
                'medium_id'             => 'required|string',
                'subject_id'            => 'required|string',
            ])->validate();

            DB::beginTransaction();

            // 1. Create or Update Principal record
            $principal = Principal::updateOrCreate(
                ['appointment_id' => $validated['appointment_id']],
                [
                    'employee_id'          => $validated['employee_id'],
                    'recruitment_category' => $validated['principal_category_id'],
                ]
            );

            // 2. Sync with Employer Cadre Subject
            EmployerCadreSubject::updateOrCreate(
                ['appointment_id' => $validated['appointment_id']],
                [
                    'employee_id'        => $validated['employee_id'],
                    'appointment_medium' => $validated['medium_id'],
                    'main_subject'       => $validated['subject_id'],
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Principal service details synchronized successfully.',
                'data' => $principal
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'validation_error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save principal service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing principal's service details.
     * 
     * @param Request|array $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePrincipalService($request)
    {
        try {
            $data = $request instanceof Request ? $request->all() : $request;

            $validated = Validator::make($data, [
                'appointment_id'        => 'required|exists:employer_appointments,appointment_id',
                'employee_id'           => 'required|exists:people,people_id',
                'principal_category_id' => 'required|string',
                'medium_id'             => 'required|string',
                'subject_id'            => 'required|string',
            ])->validate();

            DB::beginTransaction();

            // 1. Sync Principal record
            $principal = Principal::updateOrCreate(
                ['appointment_id' => $validated['appointment_id']],
                [
                    'employee_id'          => $validated['employee_id'],
                    'recruitment_category' => $validated['principal_category_id'],
                ]
            );

            // 2. Sync Employer Cadre Subject record
            EmployerCadreSubject::updateOrCreate(
                ['appointment_id' => $validated['appointment_id']],
                [
                    'employee_id'        => $validated['employee_id'],
                    'appointment_medium' => $validated['medium_id'],
                    'main_subject'       => $validated['subject_id'],
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Principal service details updated successfully.',
                'data' => $principal
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'validation_error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update principal service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve principal-specific service data for a given appointment.
     * 
     * @param string|int $appointmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrincipalServiceData($appointmentId)
    {
        try {
            $principal = Principal::with([
                'recruitmentCategory',
                'employee',
                'appointment'
            ])
            ->where('appointment_id', $appointmentId)
            ->first();

            if (!$principal) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => "Principal service data not found for appointment: {$appointmentId}"
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $principal
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve principal service data: ' . $e->getMessage()
            ], 500);
        }
    }
}
