<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * GET all institutions
     */
    public function index(Request $request)
    {
        $query = Institution::with([
            'zonalEducationOffice',
            'divisionalEducationOffice',
            'district',
            'institutionCategory',
            'authority',
            'institutionLanguages',
            'typeByGender',
            'policeStation',
            'mohArea',
            'institutionType',
            'gradeSpan'
        ]);

        /* -------------------------
     | Filters
     |--------------------------*/

        if ($request->active === "1") {
            $query->active();
        }

        if ($request->zeo_wp_id) {
            $query->where('zeo_wp_id', $request->zeo_wp_id);
        }

        if ($request->deo_wp_id) {
            $query->where('deo_wp_id', $request->deo_wp_id);
        }

        if ($request->district_id) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->category_id) {
            $query->where('institution_category_id', $request->category_id);
        }

        if ($request->authority_id) {
            $query->where('authority_id', $request->authority_id);
        }

        if ($request->type_id) {
            $query->where('institution_types_id', $request->type_id);
        }

    /* -------------------------
    | SEARCH
    |--------------------------*/
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('workplace_id', 'like', "%{$search}%")
                    ->orWhere('census_No', 'like', "%{$search}%");
            });
        }

        /* -------------------------
     | Pagination
     |--------------------------*/
        $institutions = $query
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString(); // keeps page & search params

        return response()->json([
            'status' => 'success',
            'data'   => $institutions
        ]);
    }



    /**
     * GET a single institution
     */
    public function show($id)
    {
        $institution = Institution::with([
            'zonalEducationOffice',
            'divisionalEducationOffice',
            'district',
            'institutionCategory',
            'authority',
            'institutionLanguages',
            'typeByGender',
            'policeStation',
            'mohArea',
            'institutionType',
            'gradeSpan'
        ])->find($id);

        if (!$institution) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Institution not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $institution
        ]);
    }

    /**
     * UPDATE institution
     */
    public function update(Request $request, $id)
    {
        $institution = Institution::find($id);
        /** @var \App\Models\Institution|null $institution */

        if (!$institution) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Institution not found'
            ], 404);
        }

        // Validation rules
        $validated = $request->validate([
            'census_no'              => 'nullable|string|max:20',
            'institution_category_id' => 'nullable|string',
            'authority_id'           => 'nullable|string',
            'language_id'            => 'nullable|string',
            'ethnicity_id'           => 'nullable|string',
            'gender_id'              => 'nullable|string',
            'institution_types_id'   => 'nullable|string',
            'grade_span_id'          => 'nullable|string',
            'sport_s'                => 'nullable|string',
            'district_id'            => 'nullable|string',
            'zeo_wp_id'              => 'nullable|string',
            'deo_wp_id'              => 'nullable|string',
            'police_station_id'      => 'nullable|string',
            'moh_area_id'            => 'nullable|string',
            'name'                   => 'required|string|max:255',
            'short_name'             => 'nullable|string|max:100',
            'established_year'       => 'nullable|integer',
            'email'                  => 'nullable|email|max:255',
            'phone'                  => 'nullable|string|max:20',
            'address'                => 'nullable|string',
            'postal_code'            => 'nullable|string|max:10',
            'latitude'               => 'nullable|string|max:20',
            'longitude'              => 'nullable|string|max:20',
            'mission'                => 'nullable|string',
            'vision'                 => 'nullable|string',
            'logo'                   => 'nullable|string',
            'active_status'          => 'required|boolean',
        ]);

        $institution->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Institution updated successfully',
            'data'    => $institution
        ]);
    }
}
