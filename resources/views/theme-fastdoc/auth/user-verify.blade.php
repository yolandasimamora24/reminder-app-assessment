
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
                                <h3 class="text-center mb-4">Please verify your email</h3>
                                <img src="/images/mail-sent.svg" height="130" class="d-block mx-auto" alt="Mail Sent">
                                <div class="text-center pt-5">
                                    A verification email has been sent to
                                    <span class="text-primary">{{$data->email}}</span>.
                                    <p class="pt-3">Click on the link to complete the verification process. You might need to check your spam folder.</p>
                                </div>
                                <form class="col-md-12 p-t-10" id="form-generate-verification-email" role="form" method="POST" action="{{ route('user-re-verify', $data->uuid) }}">
                                    {!! csrf_field() !!}
                                </form>
                            </div>
                        </div>
                        <div class="content d-flex justify-content-center align-items-center">
                            <span>Didn't get the email?</span>
                            <a href="#" id="re-generate-verification-email" uuid="{{ $data->uuid }}" class="text-decoration-none ms-1">Resend</a> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
<script>
    $(document).on('click', '#re-generate-verification-email', function (e) {
        e.preventDefault();
        $('#form-generate-verification-email').submit();
    })
</script>
@endpush
