<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\BaseController;
use App\Models\AppelDeFonds;
use App\Models\ExerciceComptable;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AppelDeFondsController extends BaseController
{
    /**
     * POST /api/appels
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'exercice_id'  => 'required|exists:exercices_comptables,id',
            'lot_id'       => 'nullable|exists:lots,id',
            'amount_total' => 'required|numeric',
            'date_emission'=> 'required|date',
            'date_echeance'=> 'required|date|after:date_emission',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $exercice = ExerciceComptable::find($request->exercice_id);
        $appelsCreated = [];

        if ($request->lot_id) {
            // Single lot
            $appelsCreated[] = $this->createAppel($request->exercice_id, $request->lot_id, $request->amount_total, $request);
        } else {
            // All lots in residence
            $lots = Lot::where('residence_id', $exercice->residence_id)->orderBy('quote_part', 'desc')->get();
            $totalAmount = $request->amount_total;
            $remainingAmount = $totalAmount;

            foreach ($lots as $index => $lot) {
                if ($index === 0) {
                    // Skip the first one (largest quote part) to give it the remainder later
                    continue;
                }
                $lotAmount = round($totalAmount * ($lot->quote_part / 1000), 2);
                $appelsCreated[] = $this->createAppel($request->exercice_id, $lot->id, $lotAmount, $request);
                $remainingAmount -= $lotAmount;
            }

            // Create for the largest quote part (the first one)
            $appelsCreated[] = $this->createAppel($request->exercice_id, $lots[0]->id, round($remainingAmount, 2), $request);
        }

        return $this->sendResponse($appelsCreated, 'Appels created successfully.', 201);
    }

    private function createAppel($exerciceId, $lotId, $amount, $request)
    {
        return AppelDeFonds::create([
            'exercice_id'   => $exerciceId,
            'lot_id'        => $lotId,
            'numero'        => 'APP-' . $exerciceId . '-' . $lotId . '-' . time(),
            'amount'        => $amount,
            'reliquat'      => $amount,
            'date_emission' => $request->date_emission,
            'date_echeance' => $request->date_echeance,
            'statut'        => 'emis',
        ]);
    }

    /**
     * GET /api/exercices/{exerciceId}/appels
     */
    public function index($exerciceId, Request $request): JsonResponse
    {
        $status = $request->query('status');
        $query = AppelDeFonds::where('exercice_id', $exerciceId)->with('lot.proprietaire');

        if ($status) $query->where('statut', $status);

        return $this->sendResponse($query->get(), 'Appels retrieved successfully.');
    }
}
