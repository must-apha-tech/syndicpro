<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class LoginController extends BaseController
{
    /**
     * Endpoint: POST /api/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('SyndicProAuthToken')->plainTextToken;

            return $this->sendResponse([
                'user'   => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role'  => $user->getRoleName(),
                ],
                'token'  => $token,
                'tenant' => [
                    'id' => $user->tenant_id,
                ],
            ], 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Endpoint: POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'User logged out successfully.');
    }
}
