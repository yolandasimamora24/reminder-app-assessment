@extends(backpack_view('blank'))

@php
    $breadcrumbs = [
        'Admin' => backpack_url('dashboard'),
        'Users' => backpack_url('user'),
        'Change Password' => false,
    ];
    
@endphp

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
        bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">users</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">Change password.</p>
        <p class="mb-0 ms-2 ml-2" bp-section="page-subheading-back-button">
            <small><a href="{{ url('/') }}/admin/user" class="d-print-none font-sm"><i
                        class="la la-angle-double-left"></i> Back to all <span>users</span></a></small>
        </p>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 bold-labels">
            <form method="post" action="{{ route('reset.password', ['id' => $id]) }}" id="changePasswordForm">
                @csrf

                <input type="hidden" name="_http_referrer"
                    value={{ session('referrer_url_override') ?? (old('_http_referrer') ?? (\URL::previous() ?? url($crud->route))) }}>

                <div class="card">
                    <div class="card-body row">
                        <div class="form-group col-md-12 required" element="div" bp-field-wrapper="true"
                            bp-field-name="password" bp-field-type="password">
                            <label>Old Password</label>
                            <input type="password" name="password" autocomplete="off" class="form-control">
                        </div>
                        <div class="form-group col-md-12 required" element="div" bp-field-wrapper="true"
                            bp-field-name="new_password" bp-field-type="password">
                            <label>New Password</label>
                            <input type="password" name="new_password" autocomplete="off" class="form-control">
                        </div>
                        <div class="form-group col-md-12 required" element="div" bp-field-wrapper="true"
                            bp-field-name="confirm_new_password" bp-field-type="password">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_new_password" autocomplete="off" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="d-none" id="parentLoadedAssets">[]</div>

                <div id="saveActions" class="form-group my-3">
                    <input type="hidden" name="_save_action" value="save_and_back">
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-success" id="btnSavePassword">
                            <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
                            <span data-value="save_and_back">Save</span>
                        </button>
                    </div>
                    <a href="/admin/user" class="btn btn-default"><span class="la la-ban"></span> &nbsp;Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
