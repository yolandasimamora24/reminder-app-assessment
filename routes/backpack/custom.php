<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::post('admin/register', 'App\Http\Controllers\Admin\Auth\RegisterController@create')->name('register.user');

Route::group([
    'middleware' => 'web',
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'namespace' => 'App\Http\Controllers\Admin',
], function () {
    // Provider Registration
    Route::get('provider-register', 'Auth\ProviderRegisterController@showRegistrationForm')->name('backpack.auth.provider-register');
    Route::post('provider-register', 'Auth\ProviderRegisterController@register');

    // Provider Registration Verification
    Route::get('verify-email/{uuid}', 'Auth\UserRegisterController@emailVerification')->name('verify-email');
});

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::get('/', 'DashboardController@redirect')->name('backpack');
    // Route::get('dashboard', 'DashboardController@dashboard')->name('dashboard');
    // Route::get('dashboard/stats', 'DashboardController@getStats');
    // Route::get('dashboard/events', 'DashboardController@getCalendarEvents');
    Route::crud('user', 'UserCrudController');
    Route::get('user/{id}/change-password', 'UserCrudController@setupChangePasswordView');
    Route::post('user/{id}/reset-password', 'UserCrudController@resetPassword')->name('reset.password');
    Route::crud('reminder', 'ReminderCrudController');
    Route::get('/get_users', 'MessageController@getUsers');
    Route::get('/get_messages', 'MessageController@getMessages');
    Route::post('notifications', 'MessageController@sendMail');
});

Route::group([
        'namespace'  => 'App\Http\Controllers\Admin',
        'middleware' => config('backpack.base.web_middleware', 'web'),
        'prefix'     => config('backpack.base.route_prefix'),
    ],

    function () {
        
        Route::get('login', 'Auth\LoginController@showLoginForm')->name('backpack.auth.login');
        Route::post('login', 'Auth\LoginController@login');
        Route::get('logout', 'Auth\LoginController@logout')->name('backpack.auth.logout');
        Route::post('logout', 'Auth\LoginController@logout');

        // if not otherwise configured, setup the auth routes
        if (config('backpack.base.setup_password_recovery_routes', true)) {
            Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('backpack.auth.password.reset');
            Route::post('password/reset', 'Auth\ResetPasswordController@reset');
            Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('backpack.auth.password.reset.token');
            Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('backpack.auth.password.email')->middleware('backpack.throttle.password.recovery:' . config('backpack.base.password_recovery_throttle_access'));
        }

        if (config('backpack.base.setup_my_account_routes')) {
            Route::get('edit-account-info', 'MyAccountController@getAccountInfoForm')->name('backpack.account.info');
            Route::post('edit-account-info', 'MyAccountController@postAccountInfoForm')->name('backpack.account.info.store');
            Route::post('change-password', 'MyAccountController@postChangePasswordForm')->name('backpack.account.password');
        }
    }
);
// this should be the absolute last line of this file

