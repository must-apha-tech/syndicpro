<?php

namespace App\Http\Controllers\Incidents;

use App\Http\Controllers\BaseController;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class IncidentController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $incidents = Incident::with(['residence', 'lot', 'assignee'])->orderBy('created_at', 'desc')->get();
        return $this->sendResponse($incidents, 'Incidents retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'residence_id' => 'required|exists:residences,id',
            'lot_id'       => 'nullable|exists:lots,id',
            'titre'        => 'required|string|max:255',
            'description'  => 'required|string',
            'priorite'     => 'required|in:basse,moyenne,haute,critique',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $incident = Incident::create($request->all());

        return $this->sendResponse($incident, 'Incident reported successfully.', 201);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $incident = Incident::find($id);
        if (!$incident) return $this->sendError('Incident not found.');

        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:nouveau,en_cours,en_attente_prestataire,resolu,clos',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $incident->update(['statut' => $request->statut]);

        return $this->sendResponse($incident, 'Incident status updated.');
    }
}
