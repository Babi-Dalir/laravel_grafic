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
                            <h3 class="card-title">مزایای خرید از ما</h3>
                        </header>
                        <ul class="footer-menu">
                            <li><a href="#">کیفیت تضمین شده</a></li>
                            <li><a href="#">قیمت مناسب</a></li>
                            <li><a href="#">تنوع در انواع فایل</a></li>
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
                            <li><a href="{{route('about')}}">درباره رابی گرافیک</a></li>
                            <li><a href="#">فروش طرح‌های گرافیکی</a></li>
                            <li><a href="tel:{{ config('social.phone') }}#contact-info">تماس با ما</a></li>
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
                    <h2 class="site-title">
                        رابی گرافیک | جایی که طراحان حرفه‌ای خرید می‌کنند و می‌فروشند
                    </h2>

                    <p>
                        <strong>رابی گرافیک</strong> از سال <strong>۱۴۰۱</strong> با هدف حمایت از جامعه
                        طراحان ایرانی فعالیت خود را آغاز کرد و امروز به عنوان
                        <strong>اولین مارکت‌پلیس چندفروشندگی فایل‌های گرافیکی</strong>،
                        بستری امن و حرفه‌ای برای خرید و فروش محصولات گرافیکی محسوب می‌شود.
                    </p>

                    <p>
                        با بیش از
                        <strong class="text-primary">۱۵٬۰۰۰ محصول تخصصی</strong>،
                        <strong class="text-success">۵۰۰+ فروشنده فعال</strong>
                        و هزاران مشتری راضی، هر آنچه برای طراحی، چاپ، برندینگ و تولید محتوا نیاز دارید،
                        در رابی گرافیک آماده دانلود است.
                    </p>

                    <div class="d-flex flex-wrap gap-3 small mt-3 text-muted">
                        <span><i class="mdi mdi-shield-check text-success"></i> تضمین کیفیت محصولات</span>
                        <span><i class="mdi mdi-update text-primary"></i> بروزرسانی مادام‌العمر</span>
                        <span><i class="mdi mdi-license text-warning"></i> لایسنس معتبر تجاری</span>
                        <span><i class="mdi mdi-cloud-download text-info"></i> دانلود سریع و دائمی</span>
                    </div>
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
