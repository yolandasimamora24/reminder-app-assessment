<h2 class="card-title text-center my-4">{{ trans('backpack::base.register') }}</h2>
<form role="form" method="POST" action="{{ route('backpack.auth.register') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="first_name">First Name</label>
        <input autofocus tabindex="1" type="text" class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" name="first_name" id="first_name" value="{{ old('first_name') }}">
        @if ($errors->has('first_name'))
            <div class="invalid-feedback">{{ $errors->first('first_name') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label" for="last_name">Last Name</label>
        <input tabindex="2" type="text" class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" name="last_name" id="last_name" value="{{ old('last_name') }}">
        @if ($errors->has('last_name'))
            <div class="invalid-feedback">{{ $errors->first('last_name') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label" for="{{ backpack_authentication_column() }}">{{ trans(config('backpack.base.authentication_column_name')) }}</label>
        <input tabindex="3" type="{{ backpack_authentication_column()==backpack_email_column()?'email':'text'}}" class="form-control {{ $errors->has(backpack_authentication_column()) ? 'is-invalid' : '' }}" name="{{ backpack_authentication_column() }}" id="{{ backpack_authentication_column() }}" value="{{ old(backpack_authentication_column()) }}">
        @if ($errors->has(backpack_authentication_column()))
            <div class="invalid-feedback">{{ $errors->first(backpack_authentication_column()) }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label" for="user_type">User Type</label>
        <select class="form-control dropdown {{ $errors !== null && $errors->has('user_type') ? ' is-invalid' : '' }}" name="user_type" id="user_type" tabindex="4">
            @foreach( config('user_type') as $key => $type)
                @if ( $key !== "admin")
                    <option value="{{ $key }}">{{ $type }}</option>
                @endif
            @endforeach
        </select>
        
        @if ($errors->has('user_type'))
            <div class="invalid-feedback">{{ $errors->first('user_type') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label" for="password">{{ trans('backpack::base.password') }}</label>
        <input tabindex="5" type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" id="password" value="">
        @if ($errors->has('password'))
            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
        @endif
    </div>

    <div class="mb-4">
        <label class="form-label" for="password_confirmation">{{ trans('backpack::base.confirm_password') }}</label>
        <input tabindex="6" type="password" class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" name="password_confirmation" id="password_confirmation" value="">
        @if ($errors->has('password_confirmation'))
            <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
        @endif
    </div>

    <div class="form-group">
        <div>
            <button tabindex="7" type="submit" class="btn btn-primary w-100">
                {{ trans('backpack::base.register') }}
            </button>
        </div>
    </div>
</form>