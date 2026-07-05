@extends('frontend.auth.layouts.master')

@section('content')
    <main class="main-content dt-sl mt-5 mb-5">
        <div class="container main-container">
            <div class="row">
                <div class="col-xl-8 col-lg-10 col-md-12 col-12 mx-auto">

                    <div class="babi-about-card babi-fx-card">

                        <div class="babi-about-header text-center">
                            <h2 class="babi-about-title">درباره لاراول گرافیک</h2>
                            <p class="babi-about-subtitle">خلاقیت، کیفیت و دسترسی سریع به ابزارهای طراحان</p>
                        </div>

                        <div class="babi-about-body">
                            <p class="babi-about-lead">
                                <strong class="babi-highlight">لاراول گرافیک</strong> به عنوان یک پلتفرم پیشرو و تخصصی، بستری هوشمند برای ارائه‌دهنده ابزارها، قالب‌ها و فایل‌های لایه‌باز گرافیکی (PSD) و محصولات دیجیتال است که با هدف ساده‌سازی فرآیند خلق اثر برای طراحان و توسعه‌دهندگان راه‌اندازی شده است.
                            </p>
                            <p class="babi-about-text">
                                ما در این مجموعه زیرساختی مجهز، امن و پرسرعت فراهم کرده‌ایم تا هنرمندان خلاق بتوانند آثار خلاقانه خود را به فروش برسانند و از سوی دیگر، طراحان در کوتاه‌ترین زمان ممکن به باکیفیت‌ترین منابع دسترسی داشته باشند. سیستم آپلود پیشرفته تکه‌ای (Chunk) و اسکن دقیق لایه‌ها، ضامن امنیت و سرعت صددرصدی در مبادلات دیجیتال شماست.
                            </p>
                        </div>

                        <div class="row justify-content-center g-4 babi-stats-grid">
                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                <div class="babi-stat-box box-purple">
                                    <div class="babi-stat-icon-wrapper">
                                        <i class="fad fa-images"></i>
                                    </div>
                                    <h3 class="babi-stat-number">+۵,۰۰۰</h3>
                                    <span class="babi-stat-label">طرح و فایل لایه‌باز</span>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                <div class="babi-stat-box box-emerald">
                                    <div class="babi-stat-icon-wrapper">
                                        <i class="fad fa-users"></i>
                                    </div>
                                    <h3 class="babi-stat-number">+۱۰,۰۰۰</h3>
                                    <span class="babi-stat-label">کاربر و طراح خلاق</span>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                <div class="babi-stat-box box-amber">
                                    <div class="babi-stat-icon-wrapper">
                                        <i class="fad fa-wallet"></i>
                                    </div>
                                    <h3 class="babi-stat-number">۱۰۰٪ امن</h3>
                                    <span class="babi-stat-label">سیستم کیف پول و تسویه</span>
                                </div>
                            </div>
                        </div>

                        <div class="babi-about-footer text-center mt-4">
                            <a href="{{ route('home') }}" class="babi-btn-back">
                                <i class="fad fa-arrow-right ml-2"></i> بازگشت به صفحه اصلی فروشگاه
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    {{-- اسکریپت حرکت سه‌بعدی کارت با حرکت ماوس --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const card = document.querySelector('.babi-fx-card');
            if (card) {
                document.addEventListener('mousemove', function (e) {
                    const xAxis = (window.innerWidth / 2 - e.pageX) / 85;
                    const yAxis = (window.innerHeight / 2 - e.pageY) / 85;
                    card.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
                });
                document.addEventListener('mouseleave', function () {
                    card.style.transform = 'rotateY(0deg) rotateX(0deg)';
                });
            }
        });
    </script>
@endpush
