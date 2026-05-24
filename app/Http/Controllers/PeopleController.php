<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\Family;
use App\Helpers\NicHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\FamilyMember;
use App\Rules\UniqueHashedNic;
use Illuminate\Validation\ValidationException;
use App\Rules\UniquePhoneAcrossTables;
use App\Rules\UniqueEmailAcrossTables;


class PeopleController extends Controller
{
    /**
     * Create a new People record.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPeople(Request $request)
    {
        try {
            $validated = $request->validate([
                'nic' => [
                    'required',
                    'string',
                    'regex:/^(\d{9}[vVxX]|\d{12})$/',
                    function ($attribute, $value, $fail) {
                        if (!NicHelper::isValid($value)) {
                            $fail('The provided NIC is not a valid format.');
                        }
                    }
                ],
                'title_id' => 'required|string',
                'full_name' => 'required|string|max:255',
                'gender_id' => 'required|string',
                'date_of_birth' => 'required|date',
                'religion_id' => 'required|string',
                'ethnicity_id' => 'required|string',
                'civil_status_id' => 'nullable|string',
                'email' => 'required|email|unique:people,email',
                'phone' => 'required|string',
                'address_line1' => 'nullable|string',
                'address_line2' => 'nullable|string',
                'address_line3' => 'nullable|string',
                'postal_code' => 'nullable|string',
                'latitude' => 'nullable|string',
                'longitude' => 'nullable|string',
                't_address_line1' => 'nullable|string',
                't_address_line2' => 'nullable|string',
                't_address_line3' => 'nullable|string',
                't_postal_code' => 'nullable|string',
                'profile_picture' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $nic = NicHelper::normalize($validated['nic']);
            $nicHash = NicHelper::hash($nic);

            $initials = People::generateInitials($validated['full_name']);

            $people = People::updateOrCreate(
                ['nic_hash' => $nicHash],
                [
                    'nic' => $nic,
                    'title_id' => $validated['title_id'],
                    'full_name' => ucwords(strtolower($validated['full_name'])),
                    'name_with_initials' => $initials,
                    'gender_id' => $validated['gender_id'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'religion_id' => $validated['religion_id'],
                    'ethnicity_id' => $validated['ethnicity_id'],
                    'civil_status_id' => $validated['civil_status_id'] ?? 'C01',
                    'health_condition' => $validated['health_condition'] ?? 1,
                    'health_problem' => $validated['health_problem'] ?? null,
                    'blood_group_id' => $validated['blood_group_id'] ?? null,
                    'email' => strtolower($validated['email'] ?? $validated['nic'] . '@example.com'),
                    'phone' => $validated['phone'],
                    'district_id' => $validated['district_id'] ?? null,
                    'gn_division_id' => $validated['gn_division_id'] ?? null,
                    'address_line1' => $validated['address_line1'] ?? null,
                    'address_line2' => $validated['address_line2'] ?? null,
                    'address_line3' => $validated['address_line3'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,

                    't_address_line1' => $validated['t_address_line1'] ?? null,
                    't_address_line2' => $validated['t_address_line2'] ?? null,
                    't_address_line3' => $validated['t_address_line3'] ?? null,
                    't_postal_code' => $validated['t_postal_code'] ?? null,

                    'profile_picture' => $validated['profile_picture'] ?? 'default.png',
                    'active_status' => 1,
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Person ' . ($people->wasRecentlyCreated ? 'registered' : 'updated') . ' successfully',
                'data' => $people
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('People Registration Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during registration: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePersonal(Request $request)
    {
        try {
            $validated = $request->validate([
                'people_id' => 'required|string|exists:people,people_id',
                'title_id' => 'required|string',
                'nic' => [
                    'required',
                    'string',
                    'regex:/^(\d{9}[vVxX]|\d{12})$/',
                    new UniqueHashedNic($request->people_id)
                ],
                'full_name' => 'required|string|max:255',
                'gender_id' => 'required|string',
                'date_of_birth' => 'required|date',
                'religion_id' => 'required|string',
                'ethnicity_id' => 'required|string',
                'civil_status_id' => 'nullable|string',
            ]);

            $people = People::where('people_id', $validated['people_id'])->firstOrFail();

            // Generate initials automatically
            $initials = People::generateInitials($validated['full_name']);
            $nic = NicHelper::normalize($validated['nic']);
            DB::beginTransaction();
            $people->update([
                'nic' => $nic,
                'title_id' => $validated['title_id'],
                'full_name' => $validated['full_name'],
                'name_with_initials' => $initials,
                'gender_id' => $validated['gender_id'],
                'date_of_birth' => $validated['date_of_birth'],
                'religion_id' => $validated['religion_id'],
                'ethnicity_id' => $validated['ethnicity_id'],
                'civil_status_id' => $validated['civil_status_id'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Personal information updated successfully.',
                'data' => $people
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
            Log::error('Personal Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating personal information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateHealth(Request $request)
    {
        try {
            $validated = $request->validate([
                'people_id' => 'required|string|exists:people,people_id',
                'blood_group_id' => 'required|string',
                'health_condition' => 'required|boolean',
                'health_problem' => 'nullable|string|max:1000',
            ]);

            $people = People::where('people_id', $validated['people_id'])->firstOrFail();

            DB::beginTransaction();

            $people->update([
                'blood_group_id' => $validated['blood_group_id'],
                'health_condition' => $validated['health_condition'],
                'health_problem' => $validated['health_problem'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Health information updated successfully.',
                'data' => $people
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
            Log::error('Health Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating health information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateContactDetails(Request $request)
    {
        try {
            $validated = $request->validate([
                'people_id' => 'required|string|exists:people,people_id',
                'phone' => ['required', 'string', 'size:10', new UniquePhoneAcrossTables($request->people_id)],
                'email' => ['required', 'email', new UniqueEmailAcrossTables($request->people_id)],
            ]);

            $people = People::where('people_id', $validated['people_id'])->firstOrFail();

            DB::beginTransaction();

            $people->update([
                'phone' => $validated['phone'],
                'email' => $validated['email'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Contact information updated successfully.',
                'data' => $people
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
            Log::error('Contact Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating contact information: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updateResidentialAddress(Request $request)
    {
        try {
            $validated = $request->validate([
                'people_id' => 'required|string|exists:people,people_id',
                'district_id' => 'required|string|exists:districts_lists,district_id',
                'gn_division_id' => 'required|string|exists:gn_divisions,gn_division_id',
                'address_line1' => 'required|string|max:255',
                'address_line2' => 'required|string|max:255',
                'address_line3' => 'nullable|string|max:255',
                'postal_code' => 'required|string|max:10',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            $people = People::where('people_id', $validated['people_id'])->firstOrFail();

            DB::beginTransaction();

            $people->update([
                'district_id' => $validated['district_id'],
                'gn_division_id' => $validated['gn_division_id'],
                'address_line1' => $validated['address_line1'],
                'address_line2' => $validated['address_line2'],
                'address_line3' => $validated['address_line3'],
                'postal_code' => $validated['postal_code'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Residential address updated successfully.',
                'data' => $people
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
            Log::error('Residential Address Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating residential address: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateTemperoryAddress(Request $request)
    {
        try {
            $validated = $request->validate([
                'people_id' => 'required|string|exists:people,people_id',
                't_address_line1' => 'nullable|string|max:255',
                't_address_line2' => 'nullable|string|max:255',
                't_address_line3' => 'nullable|string|max:255',
                't_postal_code' => 'nullable|string|max:10',
            ]);

            $people = People::where('people_id', $validated['people_id'])->firstOrFail();

            DB::beginTransaction();

            $people->update([
                't_address_line1' => $validated['t_address_line1'],
                't_address_line2' => $validated['t_address_line2'],
                't_address_line3' => $validated['t_address_line3'],
                't_postal_code' => $validated['t_postal_code'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Temporary address updated successfully.',
                'data' => $people
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
            Log::error('Temporary Address Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating temporary address: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchPeopleByNic(string $nic)
    {
        try {
            if (!NicHelper::isValid($nic)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid NIC format. Please provide a valid NIC.'
                ], 422);
            }

            $nicHash = NicHelper::hash($nic);

            $people = People::with([
                'title',
                'gender',
                'religion',
                'ethnicity',
                'civilStatus',
                'bloodGroup',
                'district',
                'gnDivision'
            ])
                ->where('nic_hash', $nicHash)
                ->first();

            if ($people) {
                return response()->json([
                    'status' => 'success',
                    'found' => true,
                    'message' => 'Person found.',
                    'data' => $people
                ]);
            }

            return response()->json([
                'status' => 'success',
                'found' => false,
                'message' => 'No person found with this NIC.'
            ]);
        } catch (\Throwable $e) {
            Log::error('People Search Error: ' . $e->getMessage(), [
                'nic' => $nic,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during search: ' . $e->getMessage()
            ], 500);
        }
    }


    public function familyCreate(Request $request)
    {
        try {
            $validated = $request->validate([
                'member_a_id' => 'required|string|exists:people,people_id',
                'member_b_id' => 'required|string|exists:people,people_id',
                'married_date' => 'required|date',
                'married_cf_no' => 'required|string|max:50',
                'family_name' => 'nullable|string|max:255',
            ]);

            // 1. Broad Check: Ensure Member A is not in ANOTHER active family
            $aInFamily = Family::active()->where(function ($q) use ($validated) {
                $q->where('member_a_id', $validated['member_a_id'])
                    ->orWhere('member_b_id', $validated['member_a_id']);
            })
                // Exclude the record between these two specific people if it exists
                ->whereNot(function ($q) use ($validated) {
                    $q->where(function ($sq) use ($validated) {
                        $sq->where('member_a_id', $validated['member_a_id'])->where('member_b_id', $validated['member_b_id']);
                    })->orWhere(function ($sq) use ($validated) {
                        $sq->where('member_a_id', $validated['member_b_id'])->where('member_b_id', $validated['member_a_id']);
                    });
                })
                ->first();

            if ($aInFamily) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The first person (Member A) is already registered in an active family.'
                ], 422);
            }

            // 2. Broad Check: Ensure Member B is not in ANOTHER active family
            $bInFamily = Family::active()->where(function ($q) use ($validated) {
                $q->where('member_a_id', $validated['member_b_id'])
                    ->orWhere('member_b_id', $validated['member_b_id']);
            })
                // Exclude the record between these two specific people if it exists
                ->whereNot(function ($q) use ($validated) {
                    $q->where(function ($sq) use ($validated) {
                        $sq->where('member_a_id', $validated['member_a_id'])->where('member_b_id', $validated['member_b_id']);
                    })->orWhere(function ($sq) use ($validated) {
                        $sq->where('member_a_id', $validated['member_b_id'])->where('member_b_id', $validated['member_a_id']);
                    });
                })
                ->first();

            if ($bInFamily) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The second person (Member B) is already registered in an active family.'
                ], 422);
            }

            // 3. Upsert Logic: Try to update existing record (even if inactive/divorced) or create new
            // We search by the member IDs which have a unique constraint in DB
            $family = Family::where(function ($q) use ($validated) {
                $q->where('member_a_id', $validated['member_a_id'])->where('member_b_id', $validated['member_b_id']);
            })->orWhere(function ($q) use ($validated) {
                $q->where('member_a_id', $validated['member_b_id'])->where('member_b_id', $validated['member_a_id']);
            })->first();

            if ($family) {
                $family->update([
                    'married_date' => $validated['married_date'],
                    'married_cf_no' => $validated['married_cf_no'],
                    'family_name' => $validated['family_name'] ?? null,
                    'divorce_date' => null, // Clear divorce date if reactivating
                    'active_status' => 1,
                ]);
            } else {
                $family = Family::create([
                    'member_a_id' => $validated['member_a_id'],
                    'member_b_id' => $validated['member_b_id'],
                    'married_date' => $validated['married_date'],
                    'married_cf_no' => $validated['married_cf_no'],
                    'family_name' => $validated['family_name'] ?? null,
                    'active_status' => 1,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Family record processed successfully.',
                'data' => $family
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Family Creation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while linking the family: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteFamily(Request $request)
    {
        try {
            $validated = $request->validate([
                'family_id' => 'required|string|exists:families,family_id',
            ]);

            DB::beginTransaction();

            $family = Family::where('family_id', $validated['family_id'])->first();
            if (!$family) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Family record not found.'
                ], 404);
            }

            // Cascade delete associated children
            FamilyMember::where('family_id', $family->family_id)->delete();

            // Delete the family record
            $family->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Family and all associated member records deleted successfully.'
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
            Log::error('Family Deletion Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the family: ' . $e->getMessage()
            ], 500);
        }
    }


    public function terminateFamily(Request $request)
    {
        try {
            $validated = $request->validate([
                'family_id' => 'required|string|exists:families,family_id',
            ]);

            $family = Family::where('family_id', $validated['family_id'])->first();
            if (!$family) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Family record not found.'
                ], 404);
            }

            $family->update([
                'active_status' => 0,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Family relationship terminated successfully.'
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Family Termination Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while terminating the family: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reactivateFamily(Request $request)
    {
        try {
            $validated = $request->validate([
                'family_id' => 'required|string|exists:families,family_id',
            ]);

            $family = Family::where('family_id', $validated['family_id'])->first();
            if (!$family) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Family record not found.'
                ], 404);
            }

            // Ensure Member A is free
            $aActive = Family::active()->where(function ($q) use ($family) {
                $q->where('member_a_id', $family->member_a_id)
                    ->orWhere('member_b_id', $family->member_a_id);
            })->first();

            if ($aActive) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The first person is already in another active family relationship.'
                ], 422);
            }

            // Ensure Member B is free
            $bActive = Family::active()->where(function ($q) use ($family) {
                $q->where('member_a_id', $family->member_b_id)
                    ->orWhere('member_b_id', $family->member_b_id);
            })->first();

            if ($bActive) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The second person is already in another active family relationship.'
                ], 422);
            }

            $family->update([
                'active_status' => 1,
                'divorce_date' => null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Family relationship reactivated successfully.'
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Family Reactivation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while reactivating the family: ' . $e->getMessage()
            ], 500);
        }
    }

    public function childReg(Request $request)
    {
        try {
            $validated = $request->validate([
                'family_id' => 'required|string|exists:families,family_id',
                'child_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender_id' => 'required|string|exists:gender_lists,gender_id',
                'birth_fc_no' => 'required|string|max:50',
                'health_condition' => 'required|boolean',
            ]);

            $child = FamilyMember::create([
                'family_id' => $validated['family_id'],
                'child_name' => $validated['child_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender_id' => $validated['gender_id'],
                'birth_fc_no' => $validated['birth_fc_no'],
                'health_condition' => $validated['health_condition'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Child registered successfully.',
                'data' => $child
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Child Registration Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during child registration: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeChild(Request $request)
    {
        try {
            $validated = $request->validate([
                'family_id' => 'required|string|exists:families,family_id',
                'id' => 'required|integer|exists:family_members,id',
            ]);

            $child = FamilyMember::where('family_id', $validated['family_id'])
                ->where('id', $validated['id'])
                ->first();

            if (!$child) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Child not found.'
                ], 404);
            }

            $child->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Child removed successfully.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Child Removal Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during child removal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a person exists by NIC.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNIC(Request $request)
    {
        $nic = $request->query('nic');

        if (!$nic) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIC is required'
            ], 400);
        }

        if (!NicHelper::isValid($nic)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid NIC format'
            ], 422);
        }

        $normalizedNic = NicHelper::normalize($nic);
        $nicHash = NicHelper::hash($normalizedNic);

        $people = People::where('nic_hash', $nicHash)->first();

        if ($people) {
            return response()->json([
                'status' => 'success',
                'found' => true,
                'data' => $people
            ]);
        }

        return response()->json([
            'status' => 'success',
            'found' => false,
            'message' => 'No person found with this NIC'
        ]);
    }
}
