@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="font-weight-bold m-b-10 line-height-30 primary-font">1</h2>
                                <h6 class="mb-2 font-size-13 font-weight-bold primary-font" style="color: rgb(220, 53, 69);">تعداد کاربران</h6>
                            </div>
                            <div>
                                <span class="dashboard-pie-1" style="display: none;">2/5</span>
                                <svg class="peity" height="60" width="60">
                                    <path d="M 30 0 A 30 30 0 0 1 47.63 54.27 L 30 30" fill="rgba(220, 53, 69, 0.3)"></path>
                                    <path d="M 47.63 54.27 A 30 30 0 1 1 30 0 L 30 30" fill="rgb(220, 53, 69)"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="font-weight-bold m-b-10 line-height-30 primary-font">1</h2>
                                <h6 class="mb-2 font-size-13 font-weight-bold primary-font" style="color: rgb(111, 66, 193);">تعداد فروش</h6>
                            </div>
                            <div>
                                <span class="dashboard-pie-2" style="display: none;">4/5</span>
                                <svg class="peity" height="60" width="60">
                                    <path d="M 30 0 A 30 30 0 1 1 1.47 20.73 L 30 30" fill="rgba(111, 66, 193, 0.3)"></path>
                                    <path d="M 1.47 20.73 A 30 30 0 0 1 30 0 L 30 30" fill="rgb(111, 66, 193)"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="font-weight-bold m-b-10 line-height-30 primary-font">1</h2>
                                <h6 class="mb-2 font-size-13 font-weight-bold primary-font" style="color: rgb(255, 123, 0);">مجموع نظرات</h6>
                            </div>
                            <div>
                                <span class="dashboard-pie-3" style="display: none;">1/5</span>
                                <svg class="peity" height="60" width="60">
                                    <path d="M 30 0 A 30 30 0 0 1 58.53 20.73 L 30 30" fill="rgba(255, 123, 0, 0.3)"></path>
                                    <path d="M 58.53 20.73 A 30 30 0 1 1 30 0 L 30 30" fill="rgb(255, 123, 0)"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="font-weight-bold m-b-10 line-height-30 primary-font">1</h2>
                                <h6 class="mb-2 font-size-13 font-weight-bold primary-font" style="color: rgb(40, 167, 69);">تعداد محصولات</h6>
                            </div>
                            <div>
                                <span class="dashboard-pie-4" style="display: none;">2/5</span>
                                <svg class="peity" height="60" width="60">
                                    <path d="M 30 0 A 30 30 0 0 1 47.63 54.27 L 30 30" fill="rgba(40, 167, 69, 0.3)"></path>
                                    <path d="M 47.63 54.27 A 30 30 0 1 1 30 0 L 30 30" fill="rgb(40, 167, 69)"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection
