<?php

namespace App\Actions\Auth;

use App\Enums\UserEnum;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Http\JsonResponse;
use App\Mail\UserForgotPasswordMail;
use Illuminate\Support\Facades\Mail;
use Exception;

/**
 * Action to update user.
 */
class ForgotPassword
{
    public function __construct(private UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    /**
     * Update user
     */
    public function handle(ForgotPasswordRequest $request): JsonResponse
    {
        $user = $this->userInterface->find($request->email);

        try {
            $response = Mail::to($user->email)->send(new UserForgotPasswordMail($user));
        } catch (Exception $ex) {
            $response = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
        }

        activity_log(UserEnum::USER_UPDATED(), '', 'api/user/' . $user->id);
        return response()->json(
            [
                'message' => 'Reset password link has been sent',
            ],
        );
    }
}
