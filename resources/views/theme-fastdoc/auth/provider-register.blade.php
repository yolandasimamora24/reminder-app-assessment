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
                                <h3 class="text-center mb-4">{{ trans('backpack::base.provider-register') }}</h3>
                                <!-- Registration Form -->
                                <form class="col-md-12 p-t-10" role="form" method="POST" action="{{ route('backpack.auth.provider-register') }}">
                                    {!! csrf_field() !!}
                                    <div class="form-group required">
                                        <label class="control-label" for="first_name">{{ trans('backpack::base.first_name') }}</label>
                                        <div>
                                            <input type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
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
                                            <input type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
                                            @if ($errors->has('last_name'))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first('last_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group required">
                                        <label class="control-label" for="practice_type">{{ trans('backpack::base.practice_type') }}</label>
                                        <div>
                                            <select class="form-control{{ $errors->has('practice_type') ? ' is-invalid' : '' }}" name="practice_type" id="practice_type" required>
                                            @foreach ( config('provider.practice_types') as $practice_type )
                                                <option {{ old('practice_type') == $practice_type ? "selected" : "" }} value="{{ $practice_type }}" > {{ $practice_type }} </option>
                                            @endforeach
                                            </select>
                                            @if ($errors->has('practice_type'))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first('practice_type') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group npi-area">
                                        <label class="control-label" for="npi_number">{{ trans('backpack::base.npi') }}</label>
                                        <div>
                                            <input type="text" class="form-control{{ $errors->has('npi_number') ? ' is-invalid' : '' }}" name="npi_number" id="npi_number" value="{{ old('npi_number') }}">
                                            @if ($errors->has('npi_number'))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first('npi_number') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group required">
                                        <label class="control-label" for="{{ backpack_authentication_column() }}">{{ config('backpack.base.authentication_column_name') }}</label>

                                        <div>
                                            <input type="{{ backpack_authentication_column()==backpack_email_column()?'email':'text'}}" class="form-control{{ $errors->has(backpack_authentication_column()) ? ' is-invalid' : '' }}" name="{{ backpack_authentication_column() }}" id="{{ backpack_authentication_column() }}" value="{{ old(backpack_authentication_column()) }}">

                                            @if ($errors->has(backpack_authentication_column()))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first(backpack_authentication_column()) }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group required">
                                        <label class="control-label" for="password">{{ trans('backpack::base.password') }}</label>
                                        <div>
                                            <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="password">
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
                                            <input type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" id="password_confirmation">
                                            @if ($errors->has('password_confirmation'))
                                                <span class="invalid-feedback">
                                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div>
                                            <button type="submit" class="btn btn-primary w-100">
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
                    <img src="/images/provider-register.svg" height="300" class="d-block mx-auto" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_styles')
    @basset('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css')
    @basset('https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css')
@endpush

@push('after_scripts')
    @basset('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js')
<script>

let registered_nurses = {!! json_encode(config('provider.practice_types_categories.RN')) !!};

$(document).on('change', '#practice_type', function (e) {
    e.preventDefault();
    let practice_type = $(this).val()
    if(jQuery.inArray(practice_type, registered_nurses) != -1) {
        $('#npi').text('')
        $('.npi-area').hide()
    } else {
        $('.npi-area').show()
    } 
})
$('#practice_type').select2({
    theme: "bootstrap",
});
$('#practice_type').trigger('change')
</script>
@endpush