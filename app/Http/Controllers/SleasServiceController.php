<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EmployerCadreSubject;
use Illuminate\Support\Facades\Validator;
use App\Models\EducationAdministratorService;
use Illuminate\Validation\ValidationException;

class SleasServiceController extends Controller
{
    /**
     * Create or update SLEAS (Education Administrator) service details.
     * 
     * @param Request|array $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSleasService($request)
    {
        try {
            $data = $request instanceof Request ? $request->all() : $request;

            $validated = Validator::make($data, [
                'appointment_id'   => 'required|exists:employer_appointments,appointment_id',
                'employee_id'      => 'required|exists:people,people_id',
                'category_id'      => 'required|string',
                'subject_id'       => 'required|string',
                'medium_id'        => 'nullable|string',
                'cadre_subject_id' => 'nullable|string',
            ])->validate();

            DB::beginTransaction();

            // 1. Create or Update SLEAS record
            $sleas = EducationAdministratorService::updateOrCreate(
                ['appointment_id' => $validated['appointment_id']],
                [
                    'employee_id' => $validated['employee_id'],
                    'category_id' => $validated['category_id'],
                    'subject'     => $validated['subject_id'],
                ]
            );

            // 2. Sync with Employer Cadre Subject (Conditional)
            if (!empty($validated['medium_id']) && !empty($validated['cadre_subject_id'])) {
                EmployerCadreSubject::updateOrCreate(
                    ['appointment_id' => $validated['appointment_id']],
                    [
                        'employee_id'        => $validated['employee_id'],
                        'appointment_medium' => $validated['medium_id'],
                        'main_subject'       => $validated['cadre_subject_id'],
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'SLEAS service details synchronized successfully.',
                'data' => $sleas
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
                'message' => 'Failed to save SLEAS service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing SLEAS service details.
     * 
     * @param Request|array $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSleasService($request)
    {
        try {
            $data = $request instanceof Request ? $request->all() : $request;

            $validated = Validator::make($data, [
                'appointment_id'   => 'required|exists:employer_appointments,appointment_id',
                'employee_id'      => 'required|exists:people,people_id',
                'category_id'      => 'required|string',
                'subject_id'       => 'required|string',
                'medium_id'        => 'nullable|string',
                'cadre_subject_id' => 'nullable|string',
            ])->validate();

            DB::beginTransaction();

            // 1. Sync SLEAS record
            $sleas = EducationAdministratorService::updateOrCreate(
                ['appointment_id' => $validated['appointment_id']],
                [
                    'employee_id' => $validated['employee_id'],
                    'category_id' => $validated['category_id'],
                    'subject'     => $validated['subject_id'],
                ]
            );

            // 2. Sync Employer Cadre Subject record (Conditional)
            if (!empty($validated['medium_id']) && !empty($validated['cadre_subject_id'])) {
                EmployerCadreSubject::updateOrCreate(
                    ['appointment_id' => $validated['appointment_id']],
                    [
                        'employee_id'        => $validated['employee_id'],
                        'appointment_medium' => $validated['medium_id'],
                        'main_subject'       => $validated['cadre_subject_id'],
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'SLEAS service details updated successfully.',
                'data' => $sleas
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
                'message' => 'Failed to update SLEAS service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve SLEAS service data for a given appointment.
     * 
     * @param string|int $appointmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSleasServiceData($appointmentId)
    {
        try {
            $sleas = EducationAdministratorService::with([
                'recruitmentCategory',
                'serviceSubject',
                'employee',
                'appointment'
            ])
            ->where('appointment_id', $appointmentId)
            ->first();

            if (!$sleas) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => "SLEAS service data not found for appointment: {$appointmentId}"
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $sleas
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve SLEAS service data: ' . $e->getMessage()
            ], 500);
        }
    }
}
