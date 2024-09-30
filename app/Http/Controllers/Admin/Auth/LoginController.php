<?php

namespace App\Http\Controllers\Admin\Auth;

use Backpack\CRUD\app\Library\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Provider;

class LoginController extends Controller
{
    protected $data = []; // the information we send to the view

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers {
        logout as defaultLogout;
    }

    protected function authenticated(Request $request, $user){
        
        if ( $user->user_type == "provider" ) {
            $provider = Provider::where('user_id',$user->id)->first();
            $provider?->update(['online' => 1]);
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $guard = backpack_guard_name();

        $this->middleware("guest:$guard", ['except' => 'logout']);

        // ----------------------------------
        // Use the admin prefix in all routes
        // ----------------------------------

        // If not logged in redirect here.
        $this->loginPath = property_exists($this, 'loginPath') ? $this->loginPath
            : backpack_url('login');

        // Redirect here after successful login.
        $this->redirectTo = property_exists($this, 'redirectTo') ? $this->redirectTo
            : backpack_url('dashboard');

        // Redirect here after logout.
        $this->redirectAfterLogout = property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout
            : backpack_url('login');
    }

    /**
     * Return custom username for authentication.
     *
     * @return string
     */
    public function username()
    {
        return backpack_authentication_column();
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    
    public function logout(Request $request)
    {
        $user = backpack_user();
        if ( $user && $user?->user_type == "provider" ) {
            $provider = Provider::where('user_id',$user->id)->first();
            $provider?->update(['online' => 0]);
        }
        // Do the default logout procedure
        $this->guard()->logout();

        // And redirect to custom location
        return redirect($this->redirectAfterLogout);
    }

    /**
     * Get the guard to be used during logout.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }
}
