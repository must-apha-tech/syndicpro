<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\BaseController;
use App\Models\ExerciceComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ExerciceController extends BaseController
{
    /**
     * GET /api/residences/{residenceId}/exercices
     */
    public function index($residenceId): JsonResponse
    {
        $exercices = ExerciceComptable::where('residence_id', $residenceId)->orderBy('annee', 'desc')->get();
        return $this->sendResponse($exercices, 'Exercises retrieved successfully.');
    }

    /**
     * POST /api/exercices
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'residence_id' => 'required|exists:residences,id',
            'annee'        => 'required|integer',
            'budget_total' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $exercice = ExerciceComptable::create($request->all());

        return $this->sendResponse($exercice, 'Fiscal year created successfully.', 201);
    }

    /**
     * PATCH /api/exercices/{id}/close
     */
    public function close($id): JsonResponse
    {
        $exercice = ExerciceComptable::with('appels')->find($id);

        if (!$exercice) return $this->sendError('Exercise not found.');

        $hasUnpaid = $exercice->appels()->whereIn('statut', ['emis', 'partiel'])->exists();

        if ($hasUnpaid) {
            return $this->sendError('Cannot close exercise with unpaid charges.', [], 400);
        }

        $exercice->update(['statut' => 'clôturé']);

        return $this->sendResponse($exercice, 'Fiscal year closed successfully.');
    }
}
