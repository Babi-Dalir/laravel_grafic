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

            {{-- <div class="product-sellers shadow-around mb-5" id="product-seller-all">
                @php
                    $pro_count = $product->productPrices()->select('price')->distinct()->get()
                @endphp
                @if ($pro_count->count() > 1)
                 <div class="category-section-title dt-sl">
                        <h3> <mark>تنوع قیمت محصول {{ $product->name }}</mark></h3>
                </div>
                @endif

                @foreach($product->productPrices()->where('price','!=',$product->price)->get() as $productPrice)
                    <div class="product-seller">
                        <div class="product-seller-col">
                            <div class="product-seller-title">
                                <div class="icon">
                                    <i class="fas fa-store-alt"></i>
                                </div>
                                <div class="detail">
                                    <div class="name"> {{ $productPrice->product->name }} </div>
                                </div>
                            </div>
                        </div>
                        <div class="product-seller-col">
                            <div class="product-seller-conditions">
                               <div class="product-seller-info">
                                      <span class="product-color">
                                        <span class="product-color-circle" style="background-color: {{ $productPrice->color->code }}"
                                       ></span>
                                       {{ $productPrice->color->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="product-seller-col">
                            <div class="product-seller-guarantee">
                                <div class="product-seller-info">
                                    <i class="fad fa-shield-check"></i>
                                    <span>{{$productPrice->guaranty->name}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="product-seller-col">
                            <div class="product-seller-price-action">
                                <div class="product-seller-price">{{number_format($productPrice->price)}}<span class="currency">تومان</span></div>
                                <div class="product-seller-action"><a href="" class="btn btn-outline-danger">افزودن به
                                        سبد</a></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div> --}}
            
            <div class="dt-sn mb-5 px-0 dt-sl pt-0">
                <!-- Start tabs -->
                <section class="tabs-product-info mb-3 dt-sl">
                    <div class="ah-tab-wrapper border-bottom dt-sl">
                        <div class="ah-tab dt-sl">
                            <a class="ah-tab-item" data-ah-tab-active="true" href=""><i
                                    class="mdi mdi-glasses"></i>نقد و بررسی</a>
                            <a class="ah-tab-item" href=""><i class="mdi mdi-format-list-checks"></i>مشخصات</a>
                            <a class="ah-tab-item" href=""><i
                                    class="mdi mdi-comment-text-multiple-outline"></i>نظرات کاربران</a>
                            <a class="ah-tab-item" href=""><i class="mdi mdi-comment-question-outline"></i>پرسش و
                                پاسخ</a>
                        </div>
                    </div>
                    <div class="ah-tab-content-wrapper product-info px-4 dt-sl">
                        <div class="ah-tab-content dt-sl" data-ah-tab-active="true">
                            <div class="section-title text-sm-title title-wide no-after-title-wide mb-0 dt-sl">
                                <h2>نقد و بررسی</h2>
                            </div>
                            <div class="product-title dt-sl">
                                <h1>{{$product->name}}</h1>
                                <h3>{{$product->e_name}}</h3>
                            </div>
                            <div class="accordion dt-sl accordion-product" id="accordionExample">
                                @foreach($product->reviews as $review)
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn collapsed" type="button" data-toggle="collapse"
                                                        data-target="#collapseOne" aria-expanded="false"
                                                        aria-controls="collapseOne">
                                                    {{$review->name}}
                                                </button>
                                            </h5>
                                        </div>

                                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                             data-parent="#accordionExample">
                                            <div class="card-body">
                                               {!! $review->description !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
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
                        <div class="ah-tab-content comments-tab dt-sl">
                            <div class="section-title title-wide no-after-title-wide mb-0 dt-sl">
                                <h2>امتیاز کاربران به:</h2>
                            </div>
                            <div class="product-title dt-sl mb-3">
                                <h1>{{$product->name}}</h1>
                                @if($product->productStars()->first())
                                    <h3>{{$product->e_name}}<span
                                            class="rate-product">({{$product->productStars()->first()->count}} نفر)</span></h3>
                                @endif

                            </div>
                            <div class="dt-sl">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <ul class="content-expert-rating">
                                            @foreach($product->productStars as $star)
                                                <li>
                                                    <div class="cell">{{$star->name}}</div>
                                                    <div class="cell">
                                                        @php
                                                            if ($star->count > 0){
                                                                $percent = (($star->score*100) / ($star->count*5));
                                                                if ($percent <20){
                                                                    $text = "ضعیف";
                                                                }elseif (20 < $percent && $percent <=40){
                                                                    $text = "متوسط";
                                                                }elseif (40 < $percent && $percent <=60){
                                                                    $text = "خوب";
                                                                }elseif (60 < $percent && $percent <=80){
                                                                    $text = "خیلی خوب";
                                                                }elseif (80 < $percent){
                                                                    $text = "عالی";
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="rating rating--general" data-rate-digit="{{$text ?? ""}}">
                                                            <div class="rating-rate" data-rate-value="100%"
                                                                 style="width: {{$percent ?? 0}}%;"></div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach

                                        </ul>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="comments-summary-note">
                                                <span>شما هم می‌توانید در مورد این کالا نظر
                                                    بدهید.</span>
                                            <p>برای ثبت نظر، لازم است ابتدا وارد حساب کاربری خود
                                                شوید. اگر این محصول را قبلا از دیجی‌کالا خریده
                                                باشید، نظر
                                                شما به عنوان مالک محصول ثبت خواهد شد.
                                            </p>
                                            <div class="dt-sl mt-2">
                                                <a href="{{route('product.comment',$product->id)}}" class="btn-primary-cm btn-with-icon">
                                                    <i class="mdi mdi-comment-text-outline"></i>
                                                    افزودن نظر جدید
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="comments-area dt-sl">
                                    <div
                                        class="section-title text-sm-title title-wide no-after-title-wide mb-0 dt-sl">
                                        <h2>نظرات کاربران</h2>
                                        <p class="count-comment">{{$product->comments()->where('status',\App\Enums\CommentStatus::Approved->value)->count()}} نظر</p>
                                    </div>
                                    <ol class="comment-list">
                                        @foreach($product->approvedComments as $comment)
                                            <!-- #comment-## -->
                                           <livewire:frontend.comments.comment-reaction :comment="$comment" :product="$product"/>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="ah-tab-content dt-sl">
                            <div class="section-title title-wide no-after-title-wide dt-sl">
                                <h2>پرسش و پاسخ</h2>
                                <p class="count-comment">پرسش خود را در مورد محصول مطرح نمایید</p>
                            </div>
                            <livewire:frontend.questions.add-question :product="$product"/>
                            <livewire:frontend.questions.question-list :product="$product"/>

                        </div>
                    </div>
                </section>
                <!-- End tabs -->
            </div>
            <!-- End Product -->
        </div>
    </main>
@endsection
