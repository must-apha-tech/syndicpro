<?php

namespace App\Http\Controllers\Residences;

use App\Http\Controllers\Controller;
use App\Models\Residence;
use App\Models\Lot;
use App\Models\ResidenceAccessCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AccessCodeController extends Controller
{
    /**
     * Generate a unique access code for a specific lot.
     */
    public function generate(Residence $residence, Lot $lot, Request $request): JsonResponse
    {
        // Authorization check (should be handled by middleware or policy)
        if (auth()->id() !== $residence->syndic_id && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);

        $code = $this->generateUniqueCode();
        $expiresIn = $request->input('expires_in_days', 30);

        $accessCode = ResidenceAccessCode::create([
            'tenant_id' => $residence->tenant_id,
            'residence_id' => $residence->id,
            'lot_id' => $lot->id,
            'code' => $code,
            'created_by' => auth()->id(),
            'expires_at' => now()->addDays($expiresIn),
        ]);

        return response()->json([
            'code' => $accessCode->code,
            'residence_id' => $accessCode->residence_id,
            'lot_id' => $accessCode->lot_id,
            'created_at' => $accessCode->created_at->toISOString(),
            'expires_at' => $accessCode->expires_at->toISOString(),
            'created_by' => auth()->user()->name,
        ], 201);
    }

    /**
     * List all access codes for a residence.
     */
    public function list(Residence $residence): JsonResponse
    {
        if (auth()->id() !== $residence->syndic_id && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $codes = ResidenceAccessCode::with(['lot', 'creator', 'usedBy'])
            ->where('residence_id', $residence->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $codes->map(function ($code) {
                return [
                    'code' => $code->code,
                    'lot' => [
                        'id' => $code->lot->id,
                        'numero' => $code->lot->numero,
                    ],
                    'created_by' => $code->creator->name,
                    'created_at' => $code->created_at->toISOString(),
                    'expires_at' => $code->expires_at ? $code->expires_at->toISOString() : null,
                    'used_by' => $code->usedBy ? $code->usedBy->name : null,
                    'used_at' => $code->used_at ? $code->used_at->toISOString() : null,
                    'status' => $this->getCodeStatus($code),
                ];
            }),
            'pagination' => [
                'current_page' => $codes->currentPage(),
                'per_page' => $codes->perPage(),
                'total' => $codes->total(),
            ],
        ]);
    }

    /**
     * Revoke an unused access code.
     */
    public function revoke(ResidenceAccessCode $accessCode): JsonResponse
    {
        $residence = $accessCode->residence;
        if (auth()->id() !== $residence->syndic_id && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($accessCode->isUsed()) {
            return response()->json([
                'error' => 'Ce code a déjà été utilisé et ne peut pas être révoqué',
            ], 400);
        }

        $accessCode->delete();

        return response()->json([
            'message' => 'Code révoqué avec succès',
        ]);
    }

    /**
     * Helper: Generate a unique 6-character alphanumeric code.
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (ResidenceAccessCode::where('code', $code)->exists());

        return $code;
    }

    /**
     * Helper: Get code status.
     */
    private function getCodeStatus(ResidenceAccessCode $code): string
    {
        if ($code->isUsed()) {
            return 'used';
        }
        if ($code->isExpired()) {
            return 'expired';
        }
        return 'pending';
    }
}
