@extends(backpack_view('blank'))

@section('after_styles')
    <style media="screen">
        .backpack-profile-form .required::after {
            content: ' *';
            color: red;
        }
    </style>
@endsection

@php
    $breadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        trans('backpack::base.my_account') => false,
    ];
@endphp

@section('header')
    <section class="content-header">
        <div class="container-fluid mb-3">
            <h1>{{ trans('backpack::base.my_account') }}</h1>
        </div>
    </section>
@endsection

@section('content')
    <div class="row">

        @if (session('success'))
            <div class="col-lg-12">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if ($errors->count())
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <ul class="mb-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- UPDATE INFO FORM --}}
        <div class="col-lg-12 mb-4">
            <form class="form" action="{{ route('backpack.account.info.store') }}" method="post"
                enctype="multipart/form-data">

                {!! csrf_field() !!}

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">{{ trans('backpack::base.update_account_info') }}</h3>
                    </div>

                    <div class="card-body backpack-profile-form bold-labels">
                        <div class="row">


                            <div class="col-md-6 form-group">
                                @php
                                    $label = 'Avatar'; // Set the label for the profile picture field
                                    $field = 'profile'; // Set the field name
                                @endphp
                                <div class="show-image @if (empty($user->avatar)) d-none @endif ">

                                    <img style="width: 200px;height: 200px;background: #0090ba1f;border-radius: 100px;object-fit: contain;"
                                        src="{{ url('/') . '/' . $user->avatar }}?{{ time() }}"">
                                    <div class="btn-group">
                                        <div class="btn btn-light btn-sm btn-file choose_file_image">Choose file</div>
                                    </div>
                                </div>
                                <div class="upload_image  @if ($user->avatar != '') d-none @endif ">
                                    <label>{{ $label }}</label>
                                    <input type="file" class="form-control" value="dd" name="{{ $field }}"
                                        accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-4 form-group">
                                @php
                                    $label = config('backpack.base.first_name_column_name');
                                    $field = 'first_name';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}"
                                    value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                            <div class="col-md-4 form-group">
                                @php
                                    $label = config('backpack.base.middle_name_column_name');
                                    $field = 'middle_name';
                                @endphp
                                <label>{{ $label }}</label>
                                <input class="form-control" type="text" name="{{ $field }}"
                                    value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>

                            <div class="col-md-4 form-group">
                                @php
                                    $label = config('backpack.base.last_name_column_name');
                                    $field = 'last_name';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}"
                                    value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>

                            <div class="col-md-6 form-group">
                                @php
                                    $label = config('backpack.base.mobile_number_column_name');
                                    $field = 'mobile_number';
                                @endphp
                                <label class="required">{{ $label }}</label>

                                <input required placeholder="123-45-678" class="form-control" type="tel"
                                    name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                            <div class="col-md-6 form-group">
                                @php
                                    $label = config('backpack.base.dob_column_name');
                                    $field = 'dob';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="date" name="{{ $field }}"
                                    value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                            <div class="col-md-6 form-group">
                                @php
                                    $label = config('backpack.base.gender_column_name');
                                    $field = 'gender';
                                    $genderOptions = [
                                        'Male (M)' => 'Male (M)',
                                        'Female (F)' => 'Female (F)',
                                        'Unspecified (U)' => 'Unspecified (U)',
                                        'Undisclosed (X)' => 'Undisclosed (X)',
                                        'Prefer Not to Answer' => 'Prefer Not to Answer',
                                    ];
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <select required class="form-control" name="{{ $field }}">
                                    @foreach ($genderOptions as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old($field) ? (old($field) == $value ? 'selected' : '') : ($user->$field == $value ? 'selected' : '') }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group d-none">
                                @php
                                    $label = config('backpack.base.middle_name_column_name');
                                    $field = 'name';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}"
                                    value="testing">
                            </div>
                            <div class="col-md-6 form-group">
                                @php
                                    $label = config('backpack.base.authentication_column_name');
                                    $field = backpack_authentication_column();
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required readonly class="form-control"
                                    type="{{ backpack_authentication_column() == backpack_email_column() ? 'email' : 'text' }}"
                                    name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success"><i class="la la-save"></i>
                            {{ trans('backpack::base.save') }}</button>
                        <a href="{{ backpack_url() }}" class="btn">{{ trans('backpack::base.cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
        {{-- CHANGE PASSWORD FORM --}}

        @include(backpack_view('auth.passwords.change-password'))
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://unpkg.com/sweetalert@2.1.2/dist/sweetalert.min.js?05a5e34cbc42"></script>
<script>
$(document).ready(function() {
    $('.choose_file_image').click(function() {
        $('input[type="file"]').click();
        $('.show-image').hide();
        $('.upload_image').removeClass('d-none');
    });

    $('.btn-primary').click(function(e) {
        e.preventDefault();
        swal({
            dangerMode: true,
            buttons: ["Cancel", "Proceed"],
            dangerMode: true,
            icon: "warning",
            title: "Proceed with this action?",
            text: "You will be logged out of the system, and a password reset link will be sent to your email",

        }).then((result) => {
            if (result) {
                $("form").last().trigger("submit");
            }
        })
    });

});
</script>
