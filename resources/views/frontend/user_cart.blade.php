@extends('frontend.layouts.master')
@section('content')
    @include('frontend.layouts.header')

        <main class="main-content dt-sl mb-3">
            <div class="container main-container">
                <livewire:frontend.carts.carts-detail/>
            </div>
        </main>

@endsection
