<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use App\Actions\Nppes\Nppes;
use App\Actions\Nppes\Client;
use App\Mail\EmailVerificationMail;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Backpack\CRUD\app\Library\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Prologue\Alerts\Facades\Alert;

class ProviderRegisterController extends Controller
{
    protected array $data = []; // the information we send to the view

    protected string $redirectTo = '/';

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

    /**
     * Create a new controller instance.
     */
    function __construct(protected Client $client)
    {
        $guard = backpack_guard_name();

        $this->middleware("guest:$guard");

        // Where to redirect users after login / registration.
        $this->redirectTo = property_exists($this, 'redirectTo') ? $this->redirectTo
            : config('backpack.base.route_prefix', 'dashboard');
    }

    /**
     * Show the application registration form.
     */
    public function showRegistrationForm(): View
    {
        // if registration is closed, deny access
        if (!config('backpack.base.provider_registration_open')) {
            abort(403, trans('backpack::base.provider_registration_closed'));
        }

        return view(backpack_view('auth.provider-register'), [
            'title' => 'Provider Registration'
        ]);
    }

    public function getNppes($npi) 
    {
        $states = [];
        $nppes = new Nppes($this->client, $npi);
        $data = $nppes->getHomeState();
        return $data;
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request): View
    {
        // if registration is closed, deny access
        if (!config('backpack.base.provider_registration_open')) {
            abort(403, trans('backpack::base.provider_registration_closed'));
        }
        
        $data = $request->all();

        $user_data = $request->all(Helper::compositeFields());
        $user = User::where($user_data)->first();

        $state = "";

        if (!$user) {
            $practice_types = config('provider.practice_types');
            $registered_practice_types = config('provider.practice_types_categories.RN');

            $validator = Validator::make($data,
                [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'practice_type' => 'required|in:' . implode(',', $practice_types),
                    'email' => 'required|unique:App\Models\User',
                    'password' => ['required', 'same:password_confirmation', Password::min(8)
                        ->mixedCase()
                        ->letters()
                        ->numbers()
                        ->uncompromised()],
                    'password_confirmation' => 'required'
                ],
                [
                    'email.exists' => 'Email is already in use'
                ]
            );

            if (!in_array($data['practice_type'], $registered_practice_types)) {
                if ($data['npi_number'] == "") {
                    $validator->errors()->add('npi_number', 'The npi field is required.');
                    return back()->withInput()->withErrors($validator);
                } else {
                    $state = $this->getNppes($data['npi_number']);
                    if ($state == "") {
                        $validator->errors()->add('npi_number', 'The inputted NPI is ineligble.');
                        return back()->withInput()->withErrors($validator);
                    }
                }
            }

            $validator->validate();

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'user_type' => 'Provider',
                backpack_authentication_column() => $data[backpack_authentication_column()],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('Provider');

            Provider::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'practice_type' => $data['practice_type'],
                'npi_number' => $data['npi_number'] ?? null,
                'license_state' => $state,
                'states' => '["'.$state.'"]'
            ]);
        } else {
            $user->update(
                $data + [
                    'password' => Hash::make($data['password']),
                    'user_type' => 'Provider',
                ]);
        }

        Mail::to($user->email)->send(new EmailVerificationMail($user));
        Alert::success("A verification email has been sent. Please check your inbox")->flash();

        $user->email = Helper::maskEmail($user->email);
        return view(backpack_view('auth.user-verify'), [
            'title' => 'Email Verification',
            'data' => $user
        ]);
    }

    /**
     * Get the guard to be used during registration.
     */
    protected function guard(): StatefulGuard
    {
        return backpack_auth();
    }
}
