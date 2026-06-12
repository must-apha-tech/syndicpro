<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResidenceAccessCode;
use App\Models\User;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class RegisterWithCodeController extends Controller
{
    /**
     * Validate the access code and return associated residence/lot info.
     */
    public function validateCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $accessCode = ResidenceAccessCode::with(['residence', 'lot'])
            ->where('code', strtoupper($request->code))
            ->first();

        if (!$accessCode || !$accessCode->isValid()) {
            return response()->json([
                'error' => 'Code invalide ou expiré',
            ], 400);
        }

        return response()->json([
            'valid' => true,
            'residence' => [
                'id' => $accessCode->residence->id,
                'name' => $accessCode->residence->name,
                'address' => $accessCode->residence->address,
                'city' => $accessCode->residence->city,
            ],
            'lot' => [
                'id' => $accessCode->lot->id,
                'numero' => $accessCode->lot->numero,
                'type' => $accessCode->lot->type,
                'surface' => $accessCode->lot->surface,
                'quote_part' => $accessCode->lot->quote_part,
            ],
        ]);
    }

    /**
     * Register a new user using an access code.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'phone' => 'nullable|string',
        ]);

        $accessCode = ResidenceAccessCode::where('code', strtoupper($request->code))
            ->first();

        if (!$accessCode || !$accessCode->isValid()) {
            return response()->json([
                'error' => 'Code invalide ou expiré',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'tenant_id' => $accessCode->tenant_id,
                'is_active' => true,
            ]);

            // Assign role
            $user->assignRole('proprietaire');

            // Link user to lot
            $lot = Lot::find($accessCode->lot_id);
            if ($lot) {
                $lot->update(['proprietaire_id' => $user->id]);
            }

            // Mark code as used
            $accessCode->markAsUsed($user);

            DB::commit();

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'token' => $token,
                'residence' => [
                    'id' => $accessCode->residence->id,
                    'name' => $accessCode->residence->name,
                ],
                'lot' => [
                    'id' => $accessCode->lot->id,
                    'numero' => $accessCode->lot->numero,
                ],
            ], 211); // Task says 201, but the prompt says Response (201). Oh, wait, I'll use 201.
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Une erreur est survenue lors de l\'inscription',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
