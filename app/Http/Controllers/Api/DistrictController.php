<?php

namespace App\Http\Controllers\Api;

use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends BaseApiController
{
    /**
     * Get all districts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = District::with('state')->where('fld_isdeleted', 0);

        // Filter by state if provided
        if ($request->has('state_id')) {
            $query->where('fld_state_id', $request->state_id);
        }

        // Filter by country if provided
        if ($request->has('country_id')) {
            $query->where('fld_country_id', $request->country_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('fld_dist_name', 'like', '%' . $request->search . '%');
        }

        $districts = $query->orderBy('fld_dist_id', 'asc')->get();

        return $this->successResponse($districts, 'DISTRICTS RETRIEVED SUCCESSFULLY');
    }

    /**
     * Get a specific district.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $district = District::where('fld_dist_id', $id)->where('fld_isdeleted', 0)->with(['state', 'talukas'])->first();

        if (!$district) {
            return $this->notFoundResponse('DISTRICT NOT FOUND');
        }

        return $this->successResponse($district, 'DISTRICT RETRIEVED SUCCESSFULLY');
    }
}
