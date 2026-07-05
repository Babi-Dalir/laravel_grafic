@extends('frontend.layouts.master')

@section('content')
    @include('frontend.layouts.header')

    <main class="main-content dt-sl mb-5" style="margin-top: 120px; min-height: 70vh;">
        <div class="container main-container">
            <div class="row">
                <div class="col-12">

                    <div class="dt-sl pt-4 pb-5" style="background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">

                        <div class="error-page text-center">

                            <div style="width: 70px; height: 70px; background: rgba(239, 68, 68, 0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="mdi mdi-lock-alert" style="color: #ef4444; font-size: 35px;"></i>
                            </div>

                            <h1 style="font-size: 24px; font-weight: 900; color: #1e293b; margin-bottom: 15px;">
                                دسترسی شما به این بخش امکان‌پذیر نیست!
                            </h1>

                            <p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">
                                شما مجوزهای لازم برای مشاهده این صفحه را ندارید، یا سشن کاربری شما منقضی شده است.
                            </p>

                            <div class="d-flex align-items-center justify-content-center" style="gap: 15px; flex-wrap: wrap;">
                                <a href="{{ route('home') }}" class="btn-primary-cm" style="display: inline-flex; align-items: center; background: #f1f5f9; color: #334155; padding: 12px 25px; font-size: 13.5px; font-weight: 700; border-radius: 14px; text-decoration: none; border: 1px solid #cbd5e1;">
                                    <i class="mdi mdi-home-outline ml-2" style="font-size: 18px;"></i> بازگشت به خانه
                                </a>

                                <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: #ffffff; padding: 12px 25px; font-size: 13.5px; font-weight: 700; border-radius: 14px; text-decoration: none; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.25);">
                                    <i class="mdi mdi-account-key-outline ml-2" style="font-size: 18px;"></i> ورود با حساب کاربری
                                </a>
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
