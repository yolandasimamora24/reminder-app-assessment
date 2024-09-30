<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Auth\UpdateUserPassword;
use App\Helpers\Helper;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     */
    public function setup() : void
    {
        CRUD::setModel(User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
    }

    protected function setupListOperation() : void
    {
        CRUD::column('email');
        $this->crud->addButtonFromView('line', 'change_password', 'change_password', 'end');
    }

    protected function setupCreateOperation(): void
    {
        
        CRUD::setValidation(UserRequest::class);

        CRUD::addField([
            'name' => 'email',
            'type' => 'email',
            'label' => 'Email',
            'wrapper' => ['class' => 'form-group col-md-12'],
        ]);

        CRUD::addField([
            'name' => 'password',
            'type' => 'password',
            'label' => 'Password',
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

        CRUD::addField([
            'name' => 'confirm_password',
            'type' => 'password',
            'label' => 'Confirm Password',
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

    }


    protected function setupUpdateOperation(): void
    {
        CRUD::addField([
            'name' => 'email',
            'type' => 'email',
            'label' => 'Email',
            'wrapper' => ['class' => 'form-group col-md-12'],
        ]);

        CRUD::setValidation(UserRequest::class);
    }

    /**
     * This function stores a new user in the database after checking if the password and confirm
     * password fields match.
     */
    public function store(UserRequest $request) : Redirector|RedirectResponse
    {
        $model = new User( $request->input() );
        $confirm_password = data_get($request, 'confirm_password');

        if( $model->password === $confirm_password ){
            $model->password = Hash::make($request->input('password'));
            $model->save();
            return redirect( $this->crud->route);
        } else {
            Alert::error("Password don't match. Please try again")->flash();
            return back()->withInput();
        }
    }

    public function setupChangePasswordView() 
    {
        $this->crud->allowAccess(['update']);

        $this->data['id'] = Route::current()->parameter('id');
        $this->data['pageTitle'] = "Change Password";

        return view('vendor.user.change_password', $this->data);
    }

    /**
     * Reset a user's password
     */
    public function resetPassword(UpdateUserPassword $resetPassword, Request $request, string $id) : Redirector|RedirectResponse
    {
        $user = User::find($id);
        $request->merge(['email' => $user->email]);
        $response = json_decode($resetPassword->handle($request, $user->email)->getContent());

        if( !$response->status ){
            Alert::error($response->error)->flash();
            return back()->withInput();
        }

        Alert::success($response->message)->flash();
        return redirect( $this->crud->route);
    }
}