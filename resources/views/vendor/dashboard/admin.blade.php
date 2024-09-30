@extends(backpack_view('blank'))

@section('content')
    <main class="main dashboard">
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div class="d-flex mb-3">
                    <div>
                        <h1>
                            <a href="{{ url('/') }}">Dashboard</a>
                        </h1>
                    </div>
                    <div class="ms-auto p-2">
                        <input type="text" id="dashboard-date" class="form-control" name="date">
                    </div>
                </div>
                <!-- Count Stats -->
                <div class="row">
                    @foreach($stats_count as $key => $stat)
                        <div class="col-sm-6 col-lg-3">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">{{ $stat['label']}}</h5>
                                        </div>
                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="nav-icon la {{ $stat['icon'] }} d-block d-lg-none d-xl-block display-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3 {{$key}} total-count"><i class="las la-spinner spinner"></i></h1>
                                    <div class="mb-0">
                                        <span class="text-{{ $stat['percentage'] > 0 ? 'success' : 'danger' }} {{$key}} percentage">
                                            <i class="mdi mdi-arrow-bottom-right"></i>
                                            {{ round($stat['percentage'], 2) }}%
                                        </span>
                                        <!-- <span class="text-muted">Since last week</span> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Patient Graphs -->
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Patient Growth</h5>
                            </div>
                            <div class="card-body py-3">
                                <div class="chart chart-lg">
                                    <canvas id="patientGrowth"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Patients by Specialization</h5>
                            </div>
                            <div class="card-body py-3">
                                <div class="pt-specialization chart chart-lg d-none">
                                    <canvas id="patientsBySpecialization"></canvas>
                                </div>
                                <div class="empty-state text-center chart chart-lg d-flex justify-content-center align-items-center opacity-75">
                                    <div class="row">
                                        <img src="{{ url('/') }}/images/chart-empty-state.svg" height= "100px" alt="No data available">
                                        <p class="p-2 fw-semibold">No data available</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Provider Graphs -->
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Consultations</h5>
                            </div>
                            <div class="card-body py-3">
                                <div class="chart chart-lg">
                                    <canvas id="consultationsByStatus"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Appointments</h5>
                            </div>
                            <div class="card-body py-3">
                                <div class="chart chart-lg">
                                <canvas id="appointmentsByStatus"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('after_scripts')
    @basset('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js')
    @basset('https://cdnjs.cloudflare.com/ajax/libs/chroma-js/2.4.2/chroma.min.js')
    @basset('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js')
    @basset('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css')
    @basset('js/admin/dashboard/index.js')
    @basset('css/admin/dashboard/index.css')
@endpush
