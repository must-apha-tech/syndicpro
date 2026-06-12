<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class RegisterController extends BaseController
{
    /**
     * Endpoint: POST /api/auth/register
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|regex:/[A-Z]/|regex:/[0-9]/|regex:/[^\pL\pN\s]/u',
                'phone'    => 'nullable|string',
            ],
            [
                'name.required'     => 'Le nom complet est obligatoire.',
                'name.string'       => 'Le nom complet doit être du texte.',
                'email.required'    => 'L\'adresse e-mail est obligatoire.',
                'email.email'       => 'L\'adresse e-mail n\'est pas valide.',
                'email.unique'      => 'Cette adresse e-mail est déjà utilisée.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.min'      => 'Le mot de passe doit contenir au moins 8 caractères.',
                'password.regex'    => 'Le mot de passe doit contenir au moins une majuscule, un chiffre et un symbole.',
                'phone.string'      => 'Le numéro de téléphone doit être du texte.',
            ]
        );

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 1. Create Tenant (Mock Database Name)
                $tenantName = strtolower(str_replace(' ', '_', $request->name)) . '_' . time();
                $tenant = Tenant::create([
                    'name'          => $request->name . "'s Office",
                    'database_name' => $tenantName,
                    'is_active'     => true,
                ]);

                // 2. Create User
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'phone'     => $request->phone,
                    'tenant_id' => $tenant->id,
                ]);

                // 3. Assign Role (Spatie)
                // Assuming 'syndic' role is created via seeder later
                // $user->assignRole('syndic');

                // 4. Generate Token (Sanctum)
                $token = $user->createToken('SyndicProAuthToken')->plainTextToken;

                return $this->sendResponse([
                    'user'   => $user,
                    'token'  => $token,
                    'tenant' => $tenant,
                ], 'User register successfully.', 201);
            });
        } catch (\Throwable $throwable) {
            report($throwable);

            return $this->sendError('Registration failed.', ['error' => $throwable->getMessage()], 500);
        }
    }
}
