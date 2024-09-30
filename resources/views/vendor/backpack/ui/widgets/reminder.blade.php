

                    @foreach($widget['stats'] as $type => $data)
                        <div class="col-sm-6 col-lg-3">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">{{ $type }}</h5>
                                        </div>
                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="nav-icon la {{ $data['icon'] }} d-block d-lg-none d-xl-block display-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3 total-count">{{ $data['count'] }}</h1>
                                    <div class="mb-0">
                                        <span class="text-{{ $data['count'] > 0 ? 'success' : 'danger' }} percentage">
                                            <i class="mdi mdi-arrow-bottom-right"></i>
                                            {{ round( $data['count_percentage'], 2) }}%
                                        </span>
                                        <span class="text-muted">Since last week</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
