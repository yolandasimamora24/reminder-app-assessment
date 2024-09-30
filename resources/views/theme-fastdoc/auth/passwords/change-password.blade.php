<div class="col-lg-12 mb-4">
    <div class="card padding-10">
        <div class="card-header">
            <h3 class="card-title">{{ trans('backpack::base.change_password') }}</h3>
        </div>
        <form class="col-md-12 p-t-10" role="form" method="POST"
            action="{{ route('backpack.auth.password.email') }}">
            <div class="card-body backpack-profile-form bold-labels">
                <div class="row">
                    <div class="col-md-8 form-group"> @csrf <div class="mb-3">
                            <label class="form-label" for="email">{{ trans('backpack::base.email_address') }}</label>
                            <input required readonly autofocus type="email" va
                                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                name="email" id="email" value="{{ backpack_user()->email }}">
                            <input required type="hidden"
                                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                name="change_password" id="email" value="change_password">
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <div class="mb-3">
                            <div class="form-footer" style="margin-top: 2.3rem;">
                                <button type="submit" class="btn btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z">
                                        </path>
                                        <path d="M3 7l9 6l9 -6"></path>
                                    </svg> Send Change Password link </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>