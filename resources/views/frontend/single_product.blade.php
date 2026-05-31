@extends('frontend.layouts.master')
@section('content')
    @include('frontend.layouts.header')
    <main class="main-content dt-sl mb-3">
        <div class="container main-container">
            <!-- Start title - breadcrumb -->
            <div class="title-breadcrumb-special dt-sl mb-3">
                <div class="breadcrumb dt-sl">
                    <nav>
                        <a href="#">{{$product->category->parentCategory->name}}</a>
                        <a href="#">{{$product->category->name}}</a>
                        <a href="#">{{$product->name , $product->e_name}}</a>
                    </nav>
                </div>
            </div>
            <!-- End title - breadcrumb -->
            <!-- Start Product -->
           <livewire:frontend.products.single-product :product="$product"/>
            <!-- sellers -->

            <div class="dt-sn mb-5 px-0 dt-sl pt-0">
                <!-- Start tabs -->
                <section class="tabs-product-info mb-3 dt-sl">
                    <div class="ah-tab-wrapper border-bottom dt-sl">
                        <div class="ah-tab dt-sl">
                            <a class="ah-tab-item" href=""><i class="mdi mdi-format-list-checks"></i>مشخصات</a>
                        </div>
                    </div>
                    <div class="ah-tab-content-wrapper product-info px-4 dt-sl">
                        <div class="ah-tab-content params dt-sl">
                            <div class="section-title text-sm-title title-wide no-after-title-wide mb-0 dt-sl">
                                <h2>مشخصات فنی</h2>
                            </div>
                            <div class="product-title dt-sl mb-3">
                                <h1>{{$product->name}}</h1>
                                <h3>{{$product->e_name}}</h3>
                            </div>
                            <section>
                                <h3 class="params-title">مشخصات کلی</h3>
                                <ul class="params-list">
                                    @foreach($product->propertyGroups as $propertyGroup)
                                        <li>
                                            <div class="params-list-key">
                                                <span class="d-block">{{$propertyGroup->name}}</span>
                                            </div>
                                            @foreach($propertyGroup->properties->where('product_id',$product->id) as $property)
                                                <div class="params-list-value">
                                                <span class="d-block">
                                                    {{$property->name}}
                                                </span>
                                                </div>
                                            @endforeach
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        </div>
                    </div>
                </section>
                <!-- End tabs -->
            </div>
            <!-- End Product -->
        </div>
    </main>
@endsection
