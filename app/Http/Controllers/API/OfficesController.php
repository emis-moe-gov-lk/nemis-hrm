<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MinistryOfEducationOffice;
use App\Models\ProvincialMinistryOfEducationOffice;
use App\Models\ProvincialEducationOffice;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;

class OfficesController extends Controller
{
    /**
     * Shared pagination helper
     */
    private function paginatedResponse($query, Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $page    = $request->input('page', 1);

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status'       => 'success',
            'current_page' => $data->currentPage(),
            'per_page'     => $data->perPage(),
            'total'        => $data->total(),
            'last_page'    => $data->lastPage(),
            'data'         => $data->items(),
        ]);
    }

    /* ---------------------------------------------------
     * OFFICE LISTS
     * ---------------------------------------------------*/

    public function moeList(Request $request)
    {
        $query = MinistryOfEducationOffice::active();
        return $this->paginatedResponse($query, $request);
    }

    public function pmoeList(Request $request)
    {
        $query = ProvincialMinistryOfEducationOffice::active();
        return $this->paginatedResponse($query, $request);
    }

    public function peoList(Request $request)
    {
        $query = ProvincialEducationOffice::active();
        return $this->paginatedResponse($query, $request);
    }

    /**
     * ZEO list filtered by PEO
     * ?peo_wp_id=PEO0001
     */
    public function zeoList(Request $request)
    {
        $query = ZonalEducationOffice::active();

        if ($request->filled('peo_wp_id')) {
            $query->where('peo_wp_id', $request->peo_wp_id);
        }

        return $this->paginatedResponse($query, $request);
    }

    /**
     * DEO list filtered by ZEO
     * ?zeo_wp_id=ZEO0001
     */
    public function deoList(Request $request)
    {
        $query = DivisionalEducationOffice::active();

        if ($request->filled('zeo_wp_id')) {
            $query->where('zeo_wp_id', $request->zeo_wp_id);
        }

        return $this->paginatedResponse($query, $request);
    }

    public function singleOffice($type, $workplace_id)
    {
        $models = [
            'moe'  => MinistryOfEducationOffice::class,
            'pmoe' => ProvincialMinistryOfEducationOffice::class,
            'peo'  => ProvincialEducationOffice::class,
            'zeo'  => ZonalEducationOffice::class,
            'deo'  => DivisionalEducationOffice::class,
        ];

        if (!isset($models[$type])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid office type'
            ], 400);
        }

        $office = $models[$type]::where('workplace_id', $workplace_id)->first();

        if (!$office) {
            return response()->json([
                'status' => 'error',
                'message' => 'Office not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $office
        ], 200);
    }

}
