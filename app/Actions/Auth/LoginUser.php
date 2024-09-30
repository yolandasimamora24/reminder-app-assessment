<?php

namespace App\Actions\Auth;

use App\Enums\AuthEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Action to login user.
 */
class LoginUser
{
    /**
     * Login a user
     */
    public function handle(Request $request): JsonResponse
    {
        $user = User::whereEmail($request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'status' => false,
                    'error' => 'Invalid credentials',
                ],
                401,
            );
        }

        activity_log(AuthEnum::USER_LOGGED_IN(), '', 'api/auth/login/' . $user->id);

        return response()->json(
            [
                'status' => true,
                'message' => AuthEnum::USER_LOGGED_IN(),
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
            ]
        );
    }
}
