<?php

namespace App\Actions\Auth;

use App\Enums\AuthEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Action to reset password.
 */
class ResetPassword
{
    /**
     * Reset Password.
     */
    public function handle(Request $request): JsonResponse
    {
        $user = User::whereEmail($request->email)->first();

        if( !$user || !Hash::check($request->password, $user->password) ){
            return response()->json(
                [
                    'status' => false,
                    'error' => AuthEnum::USER_CREDENTIALS_INVALID(),
                ],
                401,
            );
        }

        $user->forceFill(['password' => Hash::make($request->new_password)])->save();

        activity_log(AuthEnum::USER_PASSWORD_UPDATED(), '', 'api/auth/update-password/' . $user->id);

        return response()->json(
            [
                'status' => true,
                'message' => AuthEnum::USER_PASSWORD_UPDATED(),
            ]
        );
    }
}
