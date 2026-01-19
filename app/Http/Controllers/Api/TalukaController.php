<?php

namespace App\Http\Controllers\Api;

use App\Models\Taluka;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TalukaController extends BaseApiController
{
    /**
     * Get all talukas.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Taluka::with(['state', 'district'])->where('fld_isdeleted', 0);

        // Filter by district if provided (highest priority)
        if ($request->has('district_id')) {
            $query->where('fld_disc_id', $request->district_id);
        }
        // Filter by state if provided
        elseif ($request->has('state_id')) {
            $query->where('fld_state_id', $request->state_id);
        }

        // Filter by country if provided
        if ($request->has('country_id')) {
            $query->where('fld_country_id', $request->country_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('fld_name', 'like', '%' . $request->search . '%');
        }

        $talukas = $query->orderBy('fld_taluka_id', 'asc')->get();

        return $this->successResponse($talukas, 'TALUKAS RETRIEVED SUCCESSFULLY');
    }

    /**
     * Get a specific taluka.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $taluka = Taluka::where('fld_taluka_id', $id)->where('fld_isdeleted', 0)->with(['state', 'district'])->first();

        if (!$taluka) {
            return $this->notFoundResponse('TALUKA NOT FOUND');
        }

        return $this->successResponse($taluka, 'TALUKA RETRIEVED SUCCESSFULLY');
    }
}
