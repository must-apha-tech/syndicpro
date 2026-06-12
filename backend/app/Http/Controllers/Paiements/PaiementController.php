<?php

namespace App\Http\Controllers\Paiements;

use App\Http\Controllers\BaseController;
use App\Models\Paiement;
use App\Models\AppelDeFonds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PaiementController extends BaseController
{
    /**
     * POST /api/paiements
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'appel_id'      => 'required|exists:appels_de_fonds,id',
            'amount'        => 'required|numeric|min:0.01',
            'date_paiement' => 'required|date',
            'mode'          => 'required|in:virement,cheque,especes,en_ligne',
            'reference'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $appel = AppelDeFonds::find($request->appel_id);

        if ($request->amount > $appel->reliquat) {
            return $this->sendError('Payment amount exceeds remaining balance.', [], 400);
        }

        try {
            return DB::transaction(function () use ($request, $appel) {
                $paiement = Paiement::create(array_merge($request->all(), [
                    'user_id' => auth()->id(),
                ]));

                // Update Appel status
                $newReliquat = $appel->reliquat - $request->amount;
                $status = $newReliquat <= 0 ? 'paye' : 'partiel';

                $appel->update([
                    'reliquat' => $newReliquat,
                    'statut'   => $status,
                ]);

                return $this->sendResponse([
                    'paiement'      => $paiement,
                    'appel_updated' => $appel,
                    'receipt_url'   => $paiement->generateReceipt(),
                ], 'Payment recorded successfully.', 201);
            });
        } catch (\Exception $e) {
            return $this->sendError('Payment recording failed.', [$e->getMessage()], 500);
        }
    }

    /**
     * GET /api/paiements/{id}/receipt
     */
    public function getReceipt($id): JsonResponse
    {
        // Binary PDF logic placeholder
        return $this->sendResponse(['url' => "receipts/payment_{$id}.pdf"], 'Receipt link generated.');
    }
}
