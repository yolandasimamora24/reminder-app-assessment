<?php

namespace App\Actions\Auth;

use App\Enums\AuthEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Action to logout user.
 */
class LogoutUser
{
    /**
     * Logout a user
     */
    public function handle(Request $request): JsonResponse
    {
        $user = User::whereId(auth('sanctum')->user()->id)->first();

        if (!$user) {
            return response()->json(
                [
                    'status' => false,
                    'error' => AuthEnum::USER_NOT_FOUND,
                ],
                401,
            );
        }

        $user->tokens()->delete();

        activity_log(AuthEnum::USER_LOGGED_OUT(), '', 'api/auth/logout/' . $user->id);

        return response()->json(
            [
                'status' => true,
                'message' => AuthEnum::USER_LOGGED_OUT(),
            ]
        );
    }
}
