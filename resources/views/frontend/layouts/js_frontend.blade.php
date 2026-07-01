@livewireScripts
<script src="{{url('frontend/js/vendor/jquery-3.4.1.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/popper.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/bootstrap.min.js')}}"></script>
<!-- Plugins -->
<script src="{{url('frontend/js/vendor/bootstrap-slider.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/owl.carousel.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/owl.carousel2.thumbs.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/jquery.nicescroll.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/jquery.nice-select.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/nouislider.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/jquery.horizontalmenu.js')}}"></script>
<script src="{{url('frontend/js/vendor/jquery-stack-menu.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/jquery.fancybox.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/countdown.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/wNumb.js')}}"></script>
<script src="{{url('frontend/js/vendor/ResizeSensor.min.js')}}"></script>
<script src="{{url('frontend/js/vendor/theia-sticky-sidebar.min.js')}}"></script>
<!-- Main JS File -->
<script src="{{url('frontend/js/main.js')}}"></script>
@stack('scripts')





{{-- ایجکس برای سرچ زدن محصول در هدر صفحاتی که سرچ دارند --}}
<script>
    $(document).ready(function(){

        let searchInput  = $('#ajax-search');
        let searchResult = $('#search-result');
        let resultList   = $('#search-result-list');
        let closeBtn     = $('#close-search-result');

        searchInput.on('input', function(){

            let query = $(this).val().trim();

            // اگر خالی شد → ریست کامل
            if(query.length === 0){
                resultList.empty();
                searchResult.hide();
                closeBtn.hide();
                return;
            }

            // اگر کمتر از 2 کاراکتر بود → بسته شود
            if(query.length < 2){
                resultList.empty();
                searchResult.hide();
                closeBtn.hide();
                return;
            }

            // نمایش دکمه پاکسازی (ضربدر)
            closeBtn.show();

            $.ajax({
                url: "{{ route('ajax.search') }}",
                type: "GET",
                data: { query: query },
                success: function(response){

                    let html = '';

                    // 🟢 منطق جدید: فقط و فقط رندر کردن محصولات
                    if(response.products && response.products.length > 0){
                        html += '<li class="search-title"><i class="mdi mdi-vector-square me-1"></i>محصولات یافت شده</li>';
                        response.products.forEach(function(prod){
                            // از مسیر صحیح تک‌محصول پلتفرمت استفاده کن
                            html += `<li><a href="/single_products/${prod.slug}"><i class="mdi mdi-link-variant me-2 text-muted"></i>${prod.name}</a></li>`;
                        });
                    } else {
                        // در صورتی که هیچ محصولی با این نام پیدا نشد
                        html += `<li class="empty-result">نتیجه‌ای برای «${query}» پیدا نشد</li>`;
                    }

                    resultList.html(html);
                    searchResult.show();
                },
                error: function() {
                    // مدیریت خطاهای احتمالی شبکه جهت جلوگیری از خرابی فرانت
                    resultList.html('<li class="empty-result text-danger">خطا در برقراری ارتباط با سرور</li>');
                    searchResult.show();
                }
            });

        });

        // دکمه ضربدر
        closeBtn.on('click', function(){
            searchInput.val('');
            resultList.empty();
            searchResult.hide();
            closeBtn.hide();
            searchInput.focus();
        });

        // بستن باکس سرچ اگر کاربر جایی خارج از باکس کلیک کرد
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-area').length) {
                searchResult.hide();
            }
        });

    });
</script>
{{-- ایجکس برای سرچ زدن محصول در هدر صفحاتی که سرچ دارند --}}ّ

{{--حذف لودر لوگو--}}
<script>

    window.addEventListener('DOMContentLoaded', () => {

        const bar = document.getElementById('progress-bar');
        const loader = document.getElementById('robi-loader-wrapper');

        if (!bar || !loader) return;

        let percent = 0;

        const interval = setInterval(() => {

            // پیشرفت نرم و سریع بدون گیر
            percent += 2 + Math.random() * 5;

            if (percent >= 100) {
                percent = 100;

                // فول شدن نوار
                bar.style.width = '100%';

                clearInterval(interval);

                // خروج نرم لودر
                setTimeout(() => {

                    loader.classList.add('fade-out');

                    // حذف کامل از DOM
                    setTimeout(() => {
                        if (loader && loader.parentNode) {
                            loader.parentNode.removeChild(loader);
                        }
                    }, 700);

                }, 250);
            }

            // آپدیت نوار
            bar.style.width = percent + '%';

        }, 60);

    });
</script>

{{--برای منو همبرگری--}}
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const btn = document.querySelector(".btn-menu");
        const menu = document.querySelector(".side-menu");
        const overlay = document.querySelector(".overlay-side-menu");

        function openMenu(){
            menu.classList.add("active");
            overlay.classList.add("active");
            document.body.classList.add("menu-open");
        }

        function closeMenu(){
            menu.classList.remove("active");
            overlay.classList.remove("active");
            document.body.classList.remove("menu-open");
        }

        btn.addEventListener("click", function () {
            if(menu.classList.contains("active")){
                closeMenu();
            } else {
                openMenu();
            }
        });

        document.getElementById("closeMenu").addEventListener("click", closeMenu);

        overlay.addEventListener("click", closeMenu);

    });
</script>
