<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PendingUserApproversMail;
use App\Mail\UserRegistrationMail;
use App\Models\User;
use App\Models\PendingUser;
use Backpack\CRUD\app\Library\Auth\RegistersUsers;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
class PendingUserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default, this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use RegistersUsers;

    protected array $departments = [];

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->departments = config('department');
    }


    /**
     * Verify Pendind User via OTP.
     */
    public function verifyUser($id, Request $request): RedirectResponse
    {
        $pendingUser = PendingUser::findOrFail($id);
        $inputOtp = implode("", $request->otp);


        if ($pendingUser->email_verified_at) {
            return redirect()->back()->withErrors(['message' => 'User already verified']);
        }

        if (Carbon::parse($pendingUser->otp_expires_at)->isPast()) {
            return redirect()->back()->withErrors(['message' => 'OTP already expired']);
        }

        if ($pendingUser->otp != $inputOtp) {
            return redirect()->back()->withErrors(['message' => 'Invalid OTP']);
        }

        $this->sendEmailNotificationForApprovers($pendingUser);

        $pendingUser->email_verified_at = Carbon::now();
        $pendingUser->otp = null;
        $pendingUser->otp_expires_at = null;
        $pendingUser->save();

        return redirect('admin/user-register')->with('success', 'Your account details have been saved.');
    }

    /**
     * Send Email Noticications For Approvers.
     */
    public function sendEmailNotificationForApprovers ($pendingUser): void
    {
        $approverUserTypes = $this->departments[$pendingUser->department]['approvers'];
        $users = User::whereIn('user_type', $approverUserTypes)->get();
        foreach ($users as $user) {
            Mail::to($user->email)->send(new PendingUserApproversMail($pendingUser, $user));
        }
    }

    public function promoteToUserViaUrl($apiToken): void
    {
        $pendingUser = PendingUser::where('api_token', $apiToken)->firstOrFail();

        if (Carbon::parse($pendingUser->api_token_expires_at)->isPast()) {
             abort(401, "Token already expired");
        }

        $promoteToUser = $this->promoteToUser($pendingUser->id);

        if (isset($promoteToUser->getData()->error)) {
            abort(404, $promoteToUser->getData()->error);
        }

        redirect()->to('/admin/user-approval')->send();
    }

    public function deletePendingUser($id): JsonResponse
    {
        $pendingUser = PendingUser::findOrFail($id);
        $pendingUser->delete();
        return response()->json(
            [
                'status' => true,
                'error' => 'success!',
            ],
            200,
        );
    }

    public function promoteToUser($id): JsonResponse
    {
        $pendingUser = PendingUser::findOrFail($id);
        $user = User::select('email')->whereNotIn('user_type', ['parent', 'child'])->where('email', $pendingUser->email)->first();

        $error = 'Something went wrong';

        if ($pendingUser && $user == null) {

            $user = User::create([
                'first_name' => $pendingUser->first_name,
                'last_name' => $pendingUser->last_name,
                'user_type' => $pendingUser->user_type,
                'email' => $pendingUser->email,
                'password' => $pendingUser->password
            ]);

            $user->assignRole($pendingUser->user_type);

            Mail::to($pendingUser->email)->send(new UserRegistrationMail($pendingUser));

            $pendingUser->api_token_expires_at = null;
            $pendingUser->api_token = null;
            $pendingUser->approved_at = carbon::now();
            $pendingUser->save();
            $pendingUser->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Success!',
                ],
                200,
            );

        } elseif (isset($user->email)) {
            $error = "Email is already taken.";
        }

        return response()->json(
            [
                'status' => false,
                'error' => $error,
            ],
            400,
        );
    }

    /**
     * Get the guard to be used during registration.
     */
    protected function guard(): StatefulGuard
    {
        return backpack_auth();
    }
}
