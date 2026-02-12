@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h6 class="card-title">ایجاد لیست تصاویر {{$product->name}}</h6>
                    <form class="dropzone border border-primary" method="post" action="{{route('store.product.gallery',$product->id)}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <div class="fallback">
                                <input type="file" name="file" multiple>
                            </div>
                        </div>
                    </form>

                    <livewire:admin.products.gallery-list :product="$product"/>
                </div>
            </div>
        </div>
    </main>
@endsection

