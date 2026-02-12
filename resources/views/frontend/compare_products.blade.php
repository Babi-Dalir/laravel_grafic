@extends('frontend.layouts.master')
@section('content')
    @include('frontend.layouts.header')
    <main class="main-content dt-sl mb-3">
        <livewire:frontend.products.compare-products :product_id_1="$product_id_1" :product_id_2="$product_id_2"/>
    </main>
@endsection
