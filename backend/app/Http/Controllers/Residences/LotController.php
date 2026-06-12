<?php

namespace App\Http\Controllers\Residences;

use App\Http\Controllers\BaseController;
use App\Models\Lot;
use App\Models\Residence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class LotController extends BaseController
{
    /**
     * GET /api/residences/{residenceId}/lots
     */
    public function index($residenceId): JsonResponse
    {
        $lots = Lot::where('residence_id', $residenceId)->with('proprietaire')->get();
        
        return $this->sendResponse($lots, 'Lots retrieved successfully.');
    }

    /**
     * POST /api/residences/{residenceId}/lots
     */
    public function store(Request $request, $residenceId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'numero'          => 'required|string',
            'type'            => 'required|in:appartement,local,parking',
            'surface'         => 'required|numeric',
            'quote_part'      => 'required|integer|min:1|max:1000',
            'proprietaire_id' => 'nullable|exists:users,id',
            'floor'           => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $residence = Residence::find($residenceId);
        if (!$residence) return $this->sendError('Residence not found.');

        // Quote parts sum validation
        $currentSum = $residence->lots()->sum('quote_part');
        if (($currentSum + $request->quote_part) > 1000) {
            return $this->sendError('Validation Error.', ['quote_part' => ['Total quote parts would exceed 1000‰']], 422);
        }

        $lot = Lot::create(array_merge($request->all(), ['residence_id' => $residenceId]));

        return $this->sendResponse($lot, 'Lot created successfully.', 201);
    }

    /**
     * GET /api/lots/{id}
     */
    public function show($id): JsonResponse
    {
        $lot = Lot::with(['residence', 'proprietaire'])->find($id);

        if (!$lot) return $this->sendError('Lot not found.');

        return $this->sendResponse($lot, 'Lot retrieved successfully.');
    }

    /**
     * PUT /api/lots/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $lot = Lot::find($id);
        if (!$lot) return $this->sendError('Lot not found.');

        $validator = Validator::make($request->all(), [
            'quote_part' => 'nullable|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        if ($request->has('quote_part')) {
            $otherLotsSum = Lot::where('residence_id', $lot->residence_id)->where('id', '!=', $id)->sum('quote_part');
            if (($otherLotsSum + $request->quote_part) > 1000) {
                return $this->sendError('Validation Error.', ['quote_part' => ['Total quote parts would exceed 1000‰']], 422);
            }
        }

        $lot->update($request->all());

        return $this->sendResponse($lot, 'Lot updated successfully.');
    }

    /**
     * DELETE /api/lots/{id}
     */
    public function destroy($id): JsonResponse
    {
        $lot = Lot::find($id);
        if (!$lot) return $this->sendError('Lot not found.');

        $lot->delete();

        return $this->sendResponse([], 'Lot deleted successfully.', 204);
    }
}
