@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <livewire:admin.products.product-property-list :product="$product"/>
            </div>
        </div>
    </main>
@endsection
