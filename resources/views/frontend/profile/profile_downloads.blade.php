@extends('frontend.layouts.master')

@section('content')
    @include('frontend.layouts.header')

    <main class="main-content dt-sl mb-3">
        <div class="container main-container">
            <div class="row">

                @include('frontend.profile.sidebar')

                <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="section-title text-sm-title title-wide mb-2 no-after-title-wide">
                                <h2>دانلودهای من</h2>
                            </div>

                            <livewire:frontend.profiles.download-list/>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </main>
@endsection
