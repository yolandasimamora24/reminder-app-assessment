
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
                                <img src="/images/user-verified.svg" height="150" class="d-block mx-auto mb-4" alt="Email Verified">
                                <h2 class="text-center mb-4">Email Verified</h2>
                                <div class="text-center">
                                    <p>Thank you for verifying your email address. </p>
                                    @if ($awaiting_approval)
                                        Your account is now under review for approval. 
                                        We will notify you once the process is complete.
                                    @else
                                        You may now access the portal. To login, click <a href='/admin/login'>here</a>.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
