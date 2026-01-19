<?php

namespace App\Http\Controllers\Api;

use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StateController extends BaseApiController
{
    /**
     * Get all states.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = State::where('fld_isdeleted', 0);

        // Filter by country if provided
        if ($request->has('country_id')) {
            $query->where('fld_country_id', $request->country_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('fld_name', 'like', '%' . $request->search . '%');
        }

        $states = $query->orderBy('fld_state_id', 'asc')->get();

        return $this->successResponse($states, 'STATES RETRIEVED SUCCESSFULLY');
    }

    /**
     * Get a specific state.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $state = State::where('fld_state_id', $id)->where('fld_isdeleted', 0)->with('districts')->first();

        if (!$state) {
            return $this->notFoundResponse('STATE NOT FOUND');
        }

        return $this->successResponse($state, 'STATE RETRIEVED SUCCESSFULLY');
    }
}
