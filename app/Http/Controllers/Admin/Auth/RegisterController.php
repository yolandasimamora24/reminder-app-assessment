<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Actions\Fortify\PasswordValidationRules;
use App\Enums\AuthEnum;
use App\Models\User;
use Prologue\Alerts\Facades\Alert;


class RegisterController extends Controller 
{
    use PasswordValidationRules;

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'user_type' => ['required', 'in:' . collect(Helper::userTypes())->join(',')],
            'password' => $this->passwordRules(),
        ]);

         if( $validator->fails() ){
            return redirect('admin/register')->withErrors($validator)->withInput();
         }

        $validated = $validator->validated();

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        Alert::add('success', AuthEnum::USER_CREATED())->flash();
        activity_log(AuthEnum::USER_CREATED(), '', '/admin/register');
        return redirect()->to('admin/login');
    }
}