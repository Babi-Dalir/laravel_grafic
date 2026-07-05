<footer class="main-footer dt-sl">
    <div class="back-to-top">
        <a href="#">
            <span class="icon"><i class="mdi mdi-chevron-up"></i></span>
            <span>بازگشت به بالا</span>
        </a>
    </div>

    <div class="container main-container">

        <!-- Services -->
        <div class="footer-services">
            <div class="row">

                <div class="service-item col">
                    <img src="{{url('frontend/img/svg/download.svg')}}">
                    <p>دانلود آنی فایل‌ها</p>
                </div>

                <div class="service-item col">
                    <img src="{{url('frontend/img/svg/support.svg')}}">
                    <p>پشتیبانی 24 ساعته</p>
                </div>

                <div class="service-item col">
                    <img src="{{url('frontend/img/svg/license.svg')}}">
                    <p>لایسنس و استفاده قانونی</p>
                </div>

                <div class="service-item col">
                    <img src="{{url('frontend/img/svg/update.svg')}}">
                    <p>آپدیت رایگان محصولات</p>
                </div>

                <div class="service-item col">
                    <img src="{{url('frontend/img/svg/original_file.svg')}}">
                    <p>فایل اورجینال و قابل ویرایش</p>
                </div>

            </div>
        </div>

        <!-- Widgets -->
        <div class="footer-widgets">
            <div class="row">

                <!-- guide -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="widget-menu widget card">
                        <header class="card-header">
                            <h3 class="card-title">راهنمای دانلود و خرید</h3>
                        </header>
                        <ul class="footer-menu">
                            <li><a href="#">نحوه خرید و دانلود فایل</a></li>
                            <li><a href="#">فرمت فایل‌ها (PSD, AI, PNG)</a></li>
                            <li><a href="#">شرایط استفاده از طرح‌ها</a></li>
                        </ul>
                    </div>
                </div>

                <!-- support -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="widget-menu widget card">
                        <header class="card-header">
                            <h3 class="card-title">پشتیبانی مشتریان</h3>
                        </header>
                        <ul class="footer-menu">
                            <li><a href="#">سوالات متداول</a></li>
                            <li><a href="#">مشکل در دانلود فایل</a></li>
                            <li><a href="#">بازگشت وجه</a></li>
                            <li><a href="#">حریم خصوصی</a></li>
                        </ul>
                    </div>
                </div>

                <!-- about -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="widget-menu widget card">
                        <header class="card-header">
                            <h3 class="card-title">درباره ما</h3>
                        </header>
                        <ul class="footer-menu">
                            <li><a href="#">درباره فروشگاه</a></li>
                            <li><a href="#">فروش طرح‌های گرافیکی</a></li>
                            <li><a href="#">همکاری با طراحان</a></li>
                            <li><a href="#">تماس با ما</a></li>
                        </ul>
                    </div>
                </div>

                <!-- newsletter -->
                <div class="col-12 col-md-6 col-lg-3">

                    <div class="widget-menu widget card footer-contact-card">

                        <p class="footer-title mb-3 text-center">ما را دنبال کنید</p>

                        <!-- Social (بدون تغییر) -->
                        <ul class="social-list">

                            <!-- Instagram -->
                            <li><a href="{{ config('social.instagram') }}" class="social-icon instagram"><i
                                        class="mdi mdi-instagram"></i></a></li>

                            <!-- Telegram -->
                            <li><a href="{{ config('social.telegram') }}" class="social-icon telegram"><i class="mdi mdi-telegram"></i></a></li>

                            <!-- Rubika -->
                            <li>
                                <a href="{{ config('social.rubika') }}"
                                   class="social-icon rubika"
                                   target="_blank">

                                    <img src="{{ url('frontend/img/rubika.png') }}"
                                         alt="Rubika"
                                         style="width:30px;height:30px;">

                                </a>
                            </li>
                        </ul>

                        <!-- SUPPORT (فقط کنترل layout) -->
                        <div class="footer-support-wrap">

                            <div class="footer-support">

                                <i class="mdi mdi-headset support-icon"></i>

                                <span class="support-text">پشتیبانی :</span>

                                <a href="tel:{{ config('social.phone') }}" class="support-phone">
                                    <i class="mdi mdi-phone"></i>
                                    {{ config('social.phone') }}
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- description -->
    <div class="description">
        <div class="container main-container">
            <div class="row">

                <div class="site-description col-12 col-lg-8">
                    <h1 class="site-title">
                        فروشگاه فایل‌های گرافیکی، دیجیتال و لایه‌باز
                    </h1>
                    <p>
                        این فروشگاه مرجع دانلود و خرید انواع طرح‌های گرافیکی شامل فایل‌های PSD،
                        قالب‌های آماده، وکتور، موکاپ و فایل‌های لایه‌باز است.
                        تمامی فایل‌ها به صورت دیجیتال ارائه شده و پس از خرید، به صورت آنی قابل دانلود هستند.
                        هدف ما ارائه فایل‌های باکیفیت برای طراحان، کسب‌وکارها و تولیدکنندگان محتوا است.
                    </p>
                </div>

                <div class="symbol col-12 col-lg-4">
                    <a href="#"><img src="{{url('frontend/img/symbol-01.png')}}"></a>
                    <a href="#"><img src="{{url('frontend/img/symbol-02.png')}}"></a>
                </div>

            </div>
        </div>
    </div>

    <div class="copyright">
        <div class="container main-container">
        </div>
    </div>

</footer>
