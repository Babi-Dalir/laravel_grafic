@extends('frontend.layouts.master')
@section('content')
    @include('frontend.layouts.header')
    <main class="main-content dt-sl mb-3">
        <div class="container main-container">
            <div class="row">
                <!-- Start Sidebar -->
                <livewire:frontend.products.filter-product :main_slug="$main_slug" :sub_slug="$sub_slug" :child_slug="$child_slug"/>
                <!-- End Sidebar -->
                <!-- Start Content -->
               <livewire:frontend.products.category-product :main_slug="$main_slug" :sub_slug="$sub_slug" :child_slug="$child_slug"/>
                <!-- End Content -->
            </div>
        </div>
    </main>
@endsection
