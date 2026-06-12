<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseController;
use App\Models\Residence;
use App\Models\Lot;
use App\Models\AppelDeFonds;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    /**
     * GET /api/dashboard/summary
     */
    public function summary(): JsonResponse
    {
        $residencesCount = Residence::count();
        $lotsCount = Lot::count();
        $totalUnpaid = AppelDeFonds::whereIn('statut', ['emis', 'partiel'])->sum('reliquat');
        
        $totalCharged = AppelDeFonds::sum('amount');
        $totalPaid = Paiement::sum('amount');
        $recoveryRate = $totalCharged > 0 ? ($totalPaid / $totalCharged) * 100 : 0;

        return $this->sendResponse([
            'residences_count'      => $residencesCount,
            'lots_count'            => $lotsCount,
            'total_unpaid'          => round($totalUnpaid, 2),
            'recovery_rate_percent' => round($recoveryRate, 1),
            'next_assembly'         => null, // Placeholder
            'pending_actions'       => 0,    // Placeholder
        ], 'Dashboard summary retrieved successfully.');
    }

    /**
     * GET /api/dashboard/taux-recouvrement
     */
    public function recoveryRate(): JsonResponse
    {
        $residences = Residence::all()->map(function ($residence) {
            $charged = AppelDeFonds::whereHas('lot', function ($q) use ($residence) {
                $q->where('residence_id', $residence->id);
            })->sum('amount');

            $paid = Paiement::whereHas('appel.lot', function ($q) use ($residence) {
                $q->where('residence_id', $residence->id);
            })->sum('amount');

            return [
                'residence' => $residence->name,
                'rate'      => $charged > 0 ? round(($paid / $charged) * 100, 1) : 0,
            ];
        });

        return $this->sendResponse([
            'current_year'  => 0, // Mock
            'previous_year' => 0, // Mock
            'trend'         => 'up',
            'by_residence'  => $residences,
        ], 'Recovery rate retrieved successfully.');
    }

    /**
     * GET /api/dashboard/impayés
     */
    public function unpaid(): JsonResponse
    {
        $unpaid = AppelDeFonds::whereIn('statut', ['emis', 'partiel'])
            ->with(['lot.proprietaire', 'lot.residence'])
            ->orderBy('date_echeance', 'asc')
            ->get();

        return $this->sendResponse($unpaid, 'Unpaid charges retrieved successfully.');
    }
}
