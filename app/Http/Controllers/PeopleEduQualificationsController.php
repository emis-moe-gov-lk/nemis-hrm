<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PeopleEducationQualification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PeopleEduQualificationsController extends Controller
{
    public function createQualification(Request $request)
    {
        try {
            $validated = $request->validate([
                'people_id' => 'required|string|exists:people,people_id',
                'qualifications_id' => 'required|string|exists:education_qualifications,qualifications_id',
                'grade' => 'required|string|exists:educational_qualification_grades,grade_id',
                'institution' => 'required|string|max:255',
                'effective_date' => 'required|date',
                'description' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $qualification = PeopleEducationQualification::create([
                'people_id' => $validated['people_id'],
                'qualifications_id' => $validated['qualifications_id'],
                'institution' => $validated['institution'],
                'effective_date' => $validated['effective_date'],
                'grade' => $validated['grade'],
                'description' => $validated['description'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Educational qualification added successfully.',
                'data' => $qualification
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
            Log::error('Educational Qualification Create Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while adding educational qualification: ' . $e->getMessage()
            ], 500);
        }
    }


    public function deleteQualification(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|string|exists:people_education_qualifications,id',
            ]);

            DB::beginTransaction();

            $qualification = PeopleEducationQualification::find($validated['id']);
            /** @var \App\Models\PeopleEducationQualification|null $qualification */

            if ($qualification) {
                $qualification->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Educational qualification deleted successfully.',
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
            Log::error('Educational Qualification Delete Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting educational qualification: ' . $e->getMessage()
            ], 500);
        }
    }
}
