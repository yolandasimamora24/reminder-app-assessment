<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Mail\PendingUserApproversMail;
use App\Mail\SendPendingUserVerificationMail;
use App\Models\User;
use App\Models\PendingUser;
use Backpack\CRUD\app\Library\Auth\RegistersUsers;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Prologue\Alerts\Facades\Alert;

class UserRegisterController extends Controller
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
        $guard = backpack_guard_name();
        $this->middleware("guest:$guard");
        $this->departments = config('department');
    }

    /**
     * Show the application Verification form.
     */
    public function showVerifyUser($uuid): View
    {
        $pendingUser = PendingUser::where('uuid', $uuid)->firstOrFail();
        $pendingUser->email = $this->maskEmail($pendingUser->email);

        // if registration is closed, deny access
        if (!config('_init.provider_registration_open')) {
            abort(403, trans('backpack::base.provider_registration_closed'));
        }

        return view(backpack_view('auth.user-verify'), [
            'title' => 'User Verification',
            'data' => $pendingUser
        ]);
    }

    protected function maskEmail(string $email): string
    {
        list($localPart, $domain) = explode('@', $email);
        $localPartLength = strlen($localPart);

        if ($localPartLength > 1) {
            $maskedLocalPart = $localPart[0] . str_repeat('*', max($localPartLength - 2, 0)) . $localPart[$localPartLength - 1];
        } else {
            // Handle case where local part is only one character or empty
            $maskedLocalPart = $localPart . str_repeat('*', max($localPartLength - 1, 0));
        }
        return $maskedLocalPart . '@' . $domain;
    }


    /**
     * Verify Pendind User via OTP.
     */
    public function verifyUser($token): View|RedirectResponse
    {
        $pendingUser = PendingUser::where('verification_token', $token)->firstOrFail();

        if ($pendingUser->email_verified_at) {
            return redirect()->back()->withErrors(['message' => 'User already verified']);
        }

        if (Carbon::parse($pendingUser->verification_token_expires_at)->isPast()) {
            return redirect()->back()->withErrors(['message' => 'Verification already expired']);
        }

        if ($pendingUser->verification_token != $token) {
            return redirect()->back()->withErrors(['message' => 'Invalid OTP']);
        }

        $this->sendEmailNotificationForApprovers($pendingUser);

        $pendingUser->email_verified_at = Carbon::now();
        $pendingUser->verification_token = null;
        $pendingUser->verification_token_expires_at = null;
        $pendingUser->save();

        return view(backpack_view('auth.user-verified'), [
            'title' => 'User Verified',
            'data' => $pendingUser,
            'awaiting_approval' => true,
        ])->with('success', 'Your account has been successfully submitted for approval.');
    }

    /**
     * Send Email Noticications For Approvers.
     */
    public function sendEmailNotificationForApprovers ($pendingUser): void
    {
        //$approverUserTypes = $this->departments[$pendingUser->department]['notify'];
        // $users = User::whereIn('user_type', 'Admin')
        //     ->where('approval_notify', '=', true)->get();

        $users = User::where('approval_notify', '=', true)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new PendingUserApproversMail($pendingUser, $user));
        }
    }

    /**
     * Show the application registration form.
     */
    public function showRegistrationForm(): View
    {
        // if registration is closed, deny access
        if (!config('_init.provider_registration_open')) {
            abort(403, trans('backpack::base.provider_registration_closed'));
        }

        return view(backpack_view('auth.user-register'), [
            'title' => 'User Registration'
        ]);
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request): RedirectResponse
    {
        // if registration is closed, deny access
        if (!config('_init.provider_registration_open')) {
            abort(403, trans('backpack::base.user_registration_closed'));
        }

        $validated = $request->validate(
            [
            'first_name' => 'required',
            'last_name' => 'required',
            'department' => 'required|in:' . implode(',', array_keys($this->departments)),
            'email' => [
                'required',
                Rule::unique('App\Models\PendingUser', 'email'),
            ],
            'password' => ['required', 'same:password_confirmation', Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->uncompromised()],
            'password_confirmation' => 'required',
        ],
            [
            'email.exists' => 'Email is already in use',
            'department.in' => 'Invalid Department'
        ]
        );

        $data = $request->all();
        $userType = $this->departments[$data['department']]['user_type'];
        $verificationToken = Str::random(60);
        $verificationTokenExpiresAt = Carbon::now()->addDays(2);
        $apiToken = Str::random(60);
        $apiTokenExpiresAt = Carbon::now()->addDays(5);

        $pendingUser = PendingUser::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'department' => $data['department'],
            'user_type' => $userType,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'verification_token' => $verificationToken,
            'verification_token_expires_at' => $verificationTokenExpiresAt,
            'api_token' => $apiToken,
            'api_token_expires_at' => $apiTokenExpiresAt
        ]);

        Mail::to($pendingUser->email)->send(new SendPendingUserVerificationMail($pendingUser));

        return redirect('admin/user-verify/' . $pendingUser->uuid);
    }

    public function reGenerateVerificationEmail($uuid): RedirectResponse
    {
        $user = null;
        $type = '';

        $user = PendingUser::where('uuid', $uuid)->first();

        if (!$user) {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $type = 'registered-user';
        } else {
            $type = 'pending-user';
        }

        if ($type === 'registered-user') {
            if (!is_null($user?->email_verified_at)) {
                abort(401, 'User already approved!');
            }

            $user->save();
            Mail::to($user->email)->send(new EmailVerificationMail($user));
        } else {
            $verificationToken = Str::random(60);
            $verificationTokenExpiresAt = Carbon::now()->addDays(2);

            $user->verification_token = $verificationToken;
            $user->verification_token_expires_at = $verificationTokenExpiresAt;

            if (!is_null($user->approved_at)) {
                abort(401, 'User already approved!');
            }

            $user->save();
            Mail::to($user->email)->send(new SendPendingUserVerificationMail($user));
        }

        Alert::success("A verification email has been sent. Please check your inbox")->flash();
        return redirect()->back();
    }

    public function emailVerification(string $uuid): View|RedirectResponse
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        if ($user->email_verified_at) {
            return redirect()->back()->withErrors(['message' => 'User already verified']);
        }

        $user->email_verified_at = Carbon::now();
        $user->save();

        return view(backpack_view('auth.user-verified'), [
            'title' => 'User Verified',
            'data' => $user,
            'awaiting_approval' => false,
        ])->with('success', 'Your account has been successfully verified.');
    }

    /**
     * Get the guard to be used during registration.
     */
    protected function guard(): StatefulGuard
    {
        return backpack_auth();
    }
}
