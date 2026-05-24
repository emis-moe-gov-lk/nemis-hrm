<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EmployerCadreSubject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TeacherServiceController extends Controller
{
    /**
     * Update existing teacher-specific service details.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTeacherService(Request $request)
    {
        try {
            $validated = $request->validate([
                'appointment_id'           => 'required|exists:employer_appointments,appointment_id',
                'employee_id'              => 'required|exists:people,people_id',
                'teacher_category_id'      => 'required|string',
                'teacher_type_id'          => 'required|string',
                'appointment_medium_id'    => 'required|string',
                'appointment_subject_id'   => 'required|string',
                'main_teaching_subject_id' => 'required|string',
                'secondary_subject_id'     => 'nullable|string',
            ]);

            DB::beginTransaction();

            // 1. Sync Teacher record
            $teacher = Teacher::updateOrCreate(
                ['appointment_id' => $validated['appointment_id']],
                [
                    'employee_id'              => $validated['employee_id'],
                    'teacher_category'         => $validated['teacher_category_id'],
                    'teacher_type'             => $validated['teacher_type_id'],
                    'appointment_medium'       => $validated['appointment_medium_id'],
                    'appointment_subject'      => $validated['appointment_subject_id'],
                    'main_subject'             => $validated['main_teaching_subject_id'],
                    'secondary_subject'        => $validated['secondary_subject_id'],
                    'current_teaching_subject' => $validated['main_teaching_subject_id'],
                ]
            );

            // 2. Sync Employer Cadre Subject record
            EmployerCadreSubject::updateOrCreate(
                ['appointment_id' => $validated['appointment_id']],
                [
                    'employee_id'        => $validated['employee_id'],
                    'appointment_medium' => $validated['appointment_medium_id'],
                    'main_subject'       => $validated['main_teaching_subject_id'],
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Teacher service details updated successfully.',
                'data' => $teacher
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update teacher service: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Retrieve teacher-specific service data for a given appointment.
     * 
     * @param string|int $appointmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTeacherServiceData($appointmentId)
    {
        try {
            $teacher = Teacher::with([
                'category',
                'type',
                'medium',
                'appointmentSubject',
                'mainSubject',
                'secondarySubject',
                'currentTeachingSubject'
            ])
                ->where('appointment_id', $appointmentId)
                ->first();

            if (!$teacher) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => "Teacher service data not found for appointment: {$appointmentId}"
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $teacher
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve teacher service data: ' . $e->getMessage()
            ], 500);
        }
    }
}
