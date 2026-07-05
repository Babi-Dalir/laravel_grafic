@extends('frontend.layouts.master')

@section('content')
    {{-- ۱. لود کردن هدر جدا شده در بالای صفحه --}}
    @include('frontend.layouts.header')

    {{-- ۲. اعمال مارجین بالا (margin-top) برای جلوگیری از رفتن محتوا زیر هدر --}}
    <main class="main-content dt-sl mb-5" style="margin-top: 120px; min-height: 70vh;">
        <div class="container main-container">
            <div class="row">
                <div class="col-12">

                    <div class="dt-sl pt-4 pb-5" style="background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">

                        <div class="error-page text-center">
                            <h1 style="font-size: 24px; font-weight: 900; color: #1e293b; margin-bottom: 25px;">
                                صفحه‌ای که دنبال آن بودید پیدا نشد!
                            </h1>

                            <a href="{{ route('home') }}" class="babi-btn-back">
                                <i class="mdi mdi-shopping-outline ml-2" style="font-size: 18px;"></i> ادامه خرید در رابی گرافیک
                            </a>

                            <div class="error-img-wrapper mt-2 mb-4">
                                <img src="{{ url('frontend/img/theme/404.png') }}" class="img-fluid" width="45%" alt="صفحه پیدا نشد">
                            </div>
                        </div>

                        <hr style="border-color: #f1f5f9; margin: 40px 20px;">

                        <div class="babi-404-footer-info px-4">
                            <div class="row align-items-center">

                                <div class="site-description col-12 col-lg-8 text-right mb-4 mb-lg-0">
                                    <h2 class="site-title mb-3" style="font-size: 17px; font-weight: 800; color: #0f172a;">
                                        رابی گرافیک مرجع فایل‌های گرافیکی، دیجیتال و لایه‌باز
                                    </h2>
                                    <p style="line-height: 28px; color: #64748b; text-align: justify; font-size: 13.5px; margin: 0;">
                                        این فروشگاه پلتفرم تخصصی دانلود و خرید انواع طرح‌های گرافیکی شامل فایل‌های PSD، قالب‌های آماده، وکتور، موکاپ و فایل‌های لایه‌باز است. تمامی فایل‌ها به صورت دیجیتال ارائه شده و پس از خرید، به صورت آنی و با سیستم پیشرفته آپلود تکه‌ای (Chunk) در دسترس شما قرار می‌گیرند.
                                    </p>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
