<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Library\Auth\PasswordBrokerManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    protected $data = []; // the information we send to the view

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $request = Request::createFromGlobals();
        if (!$request->input('change_password')) {
            $guard = backpack_guard_name();
            $this->middleware("guest:$guard");
        }
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showLinkRequestForm()
    {

        $this->data['title'] = trans('backpack::base.reset_password'); // set the page title

        return view(backpack_view('auth.passwords.email'), $this->data);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);
        $user = User::where('email', $request->email)->first();
        $is_valid_user =    $user && !in_array($user->user_type, ['parent', 'child']);
        $message =  "Thank you! If the provided email address is associated with an account in our system, you will receive a secure link to reset your password shortly. If you don't see the email, please check your spam or junk folder.";
        if ($is_valid_user) {
            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.

            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            session()->flash('success', $message);
            $obj =  $response == Password::RESET_LINK_SENT
                ? $this->sendResetLinkResponse($request, $response)
                : $this->sendResetLinkFailedResponse($request, $response);

            return ($obj->statusText() == 'Found' && $request->input('change_password'))
            ? Redirect::to('admin/logout')
            : $obj;
        } else {
            return  $response = $this->sendResetLinkResponse($request, $message);
        }
    }

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email');
    }



    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $request->wantsJson()
            ? new JsonResponse(['message' => trans($response)], 200)
            : back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {

        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        $passwords = config('backpack.base.passwords', config('auth.defaults.passwords'));
        $manager = new PasswordBrokerManager(app());

        return $manager->broker($passwords);
    }
}
