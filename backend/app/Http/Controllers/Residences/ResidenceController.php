<?php

namespace App\Http\Controllers\Residences;

use App\Http\Controllers\BaseController;
use App\Models\Residence;
use App\Models\ExerciceComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ResidenceController extends BaseController
{
    /**
     * GET /api/residences
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $perPage = $request->query('per_page', 15);

        $query = Residence::query();

        if ($search) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%");
        }

        $residences = $query->orderBy($sortBy)->paginate($perPage);

        return $this->sendResponse($residences, 'Residences retrieved successfully.');
    }

    /**
     * POST /api/residences
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'address'  => 'required|string',
            'city'     => 'required|string',
            'zip_code' => 'nullable|string',
            'nb_lots'  => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $residence = Residence::create(array_merge($request->all(), [
            'syndic_id' => auth()->id(),
        ]));

        // Side Effect: Create first fiscal year
        ExerciceComptable::create([
            'residence_id' => $residence->id,
            'annee'        => date('Y'),
            'budget_total' => 0,
            'statut'       => 'ouvert',
        ]);

        return $this->sendResponse($residence, 'Residence created successfully.', 201);
    }

    /**
     * GET /api/residences/{id}
     */
    public function show($id): JsonResponse
    {
        $residence = Residence::with('lots.proprietaire')->find($id);

        if (!$residence) {
            return $this->sendError('Residence not found.');
        }

        $data = $residence->toArray();
        $data['unpaid_charges'] = $residence->unpaid_charges;
        $data['total_lots'] = $residence->lots->count();

        return $this->sendResponse($data, 'Residence retrieved successfully.');
    }

    /**
     * PUT /api/residences/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $residence = Residence::find($id);

        if (!$residence) {
            return $this->sendError('Residence not found.');
        }

        $residence->update($request->all());

        return $this->sendResponse($residence, 'Residence updated successfully.');
    }

    /**
     * DELETE /api/residences/{id}
     */
    public function destroy($id): JsonResponse
    {
        $residence = Residence::find($id);

        if (!$residence) {
            return $this->sendError('Residence not found.');
        }

        $residence->delete();

        return $this->sendResponse([], 'Residence deleted successfully.', 204);
    }
}
