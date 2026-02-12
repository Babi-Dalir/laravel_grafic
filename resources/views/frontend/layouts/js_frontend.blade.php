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

    $('#ajax-search').on('keyup', function(){
        let query = $(this).val();

        if(query.length < 2){
            $('#search-result').hide();
            return;
        }

        $.ajax({
            url: "{{ route('ajax.search') }}",
            type: "GET",
            data: { query: query },
            success: function(response){
                let html = '';

                if(response.categories.length > 0){
                    html += '<li class="search-title">دسته‌بندی‌ها</li>';
                    response.categories.forEach(function(cat){
                        html += `<li><a href="/search_category_product_list/${cat.slug}">${cat.name}</a></li>`;
                    });
                }

                if(response.products.length > 0){
                    html += '<li class="search-title">محصولات</li>';
                    response.products.forEach(function(prod){
                        html += `<li><a href="/single_products/${prod.slug}">${prod.name}</a></li>`;
                    });
                }

                if(response.categories.length == 0 && response.products.length == 0){
                    html += `<li class="empty-result">نتیجه‌ای برای «${query}» پیدا نشد</li>`;
                }

                $('#search-result-list').html(html);
                $('#search-result').show();
            }
        });
    });

    $('#close-search-result').on('click', function(){
        $('#ajax-search').val('');
        $('#search-result').hide();
    });

});
</script>
{{-- ایجکس برای سرچ زدن محصول در هدر صفحاتی که سرچ دارند --}}ّ
