@extends(backpack_view('layouts.auth'))

@section('content')
    <div class="page page-center">
        <div class="container container-normal py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg">
                    <div class="container-tight">
                        <div class="text-center mb-4 display-6 auth-logo-container">
                            {!! backpack_theme_config('project_logo') !!}
                        </div>
                        <div class="card card-md mb-5">
                            <div class="card-body pt-10">
                                <h3 class="text-center mb-4">{{ trans('backpack::base.user-register') }}</h3>
                                <!-- Registration Form -->
                                <form class="col-md-12 p-t-10" role="form" method="POST" action="{{ route('user-register') }}">
                                    {!! csrf_field() !!}

                                    <div class="form-group required">
                                        <label class="control-label" for="first_name">{{ trans('backpack::base.first_name') }}</label>

                                        <div>
                                            <input placeholder="Ex. John" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" id="first_name" value="{{ old('first_name') }}">

                                            @if ($errors->has('first_name'))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first('first_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label" for="last_name">{{ trans('backpack::base.last_name') }}</label>

                                        <div>
                                            <input placeholder="Ex. Smith" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" id="last_name" value="{{ old('last_name') }}">

                                            @if ($errors->has('last_name'))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first('last_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label" for="{{ backpack_authentication_column() }}">{{ config('backpack.base.authentication_column_name') }}</label>

                                        <div>
                                            <input placeholder="Ex. john@test.com" type="{{ backpack_authentication_column()==backpack_email_column()?'email':'text'}}" class="form-control{{ $errors->has(backpack_authentication_column()) ? ' is-invalid' : '' }}" name="{{ backpack_authentication_column() }}" id="{{ backpack_authentication_column() }}" value="{{ old(backpack_authentication_column()) }}">

                                            @if ($errors->has(backpack_authentication_column()))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first(backpack_authentication_column()) }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label" for="department">{{ trans('backpack::base.department') }}</label>

                                        <div>
                                            <select class="form-control{{ $errors->has('department') ? ' is-invalid' : '' }}" name="department" id="department" value="{{ old('department') }}">
                                                <option value="" >Please choose your department</option>
                                                @foreach (config('department') as $key => $value)
                                                    <option value="{{ $key }}" > {{ $value['label'] }} </option>
                                                @endforeach
                                            </select>

                                            @if ($errors->has('department'))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first('department') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label" for="password">{{ trans('backpack::base.password') }}</label>

                                        <div>
                                            <input placeholder="Please enter your password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="password">

                                            @if ($errors->has('password'))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label" for="password_confirmation">{{ trans('backpack::base.confirm_password') }}</label>

                                        <div>
                                            <input placeholder="Please re-enter your password" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" id="password_confirmation">

                                            @if ($errors->has('password_confirmation'))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div>
                                            <button type="submit" class="btn btn-block btn-primary w-100">
                                                {{ trans('backpack::base.register') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @if (backpack_users_have_email() && backpack_email_column() == 'email' && config('backpack.base.setup_password_recovery_routes', true))
                            <div class="text-center"><a href="{{ route('backpack.auth.password.reset') }}">{{ trans('backpack::base.forgot_your_password') }}</a></div>
                        @endif
                        <div class="text-center"><a href="{{ route('backpack.auth.login') }}">{{ trans('backpack::base.login') }}</a></div>
                    </div>
                </div>
                <div class="col-lg d-none d-lg-block">
                    <img src="/images/user-register.svg" height="300" class="d-block mx-auto" alt="User Register">
                </div>
            </div>
        </div>
    </div>
@endsection