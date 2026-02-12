<header class="main-header">
    <!-- Start ads -->
    <div class="ads-header-wrapper">
        <a href="#" class="ads-header hidden-sm" target="_blank"
            style="background-image: url({{ url('images/banners/big/' . $banners->where('type', 'top_banner')->first()->image) }})"></a>
    </div>
    <!-- End ads -->
    <!-- Start topbar -->
    <div class="container main-container">
        <div class="topbar dt-sl">
            <div class="row">
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="logo-area">
                        <a href="{{ route('home') }}">
                            <img src="{{ url('frontend/img/logo.png') }}" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-5 d-none d-md-block">
                    <div class="search-area dt-sl">
                        <div class="search position-relative">

                            <i class="far fa-search search-icon"></i>

                            <input type="text" class="form-control search-input"
                                placeholder="نام کالا، برند یا دسته مورد نظر خود را جستجو کنید…" id="ajax-search"
                                autocomplete="off">

                            <button type="button" id="close-search-result" style="display:none;">
                                <i class="mdi mdi-close"></i>
                            </button>

                            <div class="search-result" id="search-result" style="display:none;">
                                <ul id="search-result-list"></ul>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-6 topbar-left">
                    <ul class="nav float-left">
                        <li class="nav-item account dropdown">
                            @auth
                                <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span class="label-dropdown">حساب کاربری</span>
                                    <i class="mdi mdi-account-circle-outline"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-left">
                                    @if (auth()->user()->is_admin)
                                        <a class="dropdown-item" href="{{ route('panel') }}">
                                            <i class="mdi mdi-account-card-details-outline"></i>پنل مدیرت
                                        </a>
                                        <a class="dropdown-item" href="{{ route('profile') }}">
                                            <i class="mdi mdi-account-card-details-outline"></i>پنل کاربری
                                        </a>
                                    @elseif(auth()->user()->seller?->status == \App\Enums\CompanyStatus::Active->value)
                                        <a class="dropdown-item" href="{{ route('panel') }}">
                                            <i class="mdi mdi-account-card-details-outline"></i>پنل فروشنده
                                        </a>
                                        <a class="dropdown-item" href="{{ route('profile') }}">
                                            <i class="mdi mdi-account-card-details-outline"></i>پنل کاربری
                                        </a>
                                    @else
                                        <a class="dropdown-item" href="{{ route('profile') }}">
                                            <i class="mdi mdi-account-card-details-outline"></i>پنل کاربری
                                        </a>
                                    @endif

                                    <div class="dropdown-divider" role="presentation"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}">
                                        <i class="mdi mdi-logout-variant"></i>خروج
                                    </a>
                                </div>
                            @endauth
                            @guest
                                <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span class="label-dropdown">ورود یا ثبت نام</span>
                                    <i class="mdi mdi-account-circle-outline"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-left">
                                    <a class="dropdown-item" href="{{ route('login') }}">
                                        <i class="mdi mdi-account-card-details-outline"></i>ورود
                                    </a>
                                    <a class="dropdown-item" href="{{ route('register') }}">
                                        <i class="mdi mdi-account-card-details-outline"></i>ثبت نام
                                    </a>
                                    <div class="dropdown-divider" role="presentation"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}">
                                        <i class="mdi mdi-logout-variant"></i>خروج
                                    </a>
                                </div>
                            @endguest
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End topbar -->

    <!-- Start bottom-header -->
    <div class="bottom-header dt-sl mb-sm-bottom-header">
        <div class="container main-container">
            <!-- Start Main-Menu -->
            <nav class="main-menu d-flex justify-content-md-between justify-content-end dt-sl">
                <ul class="list hidden-sm">
                    <li class="list-item category-list">
                        <a href="#"><i class="fal fa-bars ml-1"></i>دسته بندی کالاها</a>
                        <ul>
                            @foreach ($categories as $category1)
                                <li>
                                    <a
                                        href="{{ route('main.category.product.list', $category1->slug) }}">{{ $category1->name }}</a>
                                    <ul class="row">
                                        @foreach ($category1->childCategory as $category2)
                                            <li class="sublist--title"><a
                                                    href="{{ route('search.category.product.list', $category2->slug) }}">{{ $category2->name }}</a>
                                            </li>
                                            @foreach ($category2->childCategory as $category3)
                                                <li class="sublist--item"><a
                                                        href="{{ route('search.category.product.list', [$category2->slug, $category3->slug]) }}">{{ $category3->name }}</a>
                                                </li>
                                            @endforeach
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach

                        </ul>
                    </li>
                    <!-- mega menu 3 column -->
                    <li class="list-item">
                        <a class="nav-link" href="#">شگفت انگیز ها</a>
                    </li>
                    <li class="list-item">
                        <a class="nav-link" href="#">پرفروش ترین ها</a>
                    </li>
                    <li class="list-item">
                        <a class="nav-link" href="#">سوپر مارکت</a>
                    </li>
                    <li class="list-item">
                        <a class="nav-link" href="#">تخفیف ها</a>
                    </li>
                    <li class="list-item">
                        <a class="nav-link" href="#">پیشنهادات</a>
                    </li>
                </ul>
                <div class="nav mr-auto">
                    <livewire:frontend.carts.header-carts />
                </div>
                <button class="btn-menu">
                    <div class="align align__justify">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <div class="side-menu">
                    <div class="logo-nav-res dt-sl text-center">
                        <a href="#">
                            <img src="assets/img/logo.png" alt="">
                        </a>
                    </div>
                    <div class="search-box-side-menu dt-sl text-center mt-2 mb-3">
                        <form action="">
                            <input type="text" name="s" placeholder="جستجو کنید...">
                            <i class="mdi mdi-magnify"></i>
                        </form>
                    </div>
                    <ul class="navbar-nav dt-sl">
                        <li class="sub-menu">
                            <a href="#">کالای دیجیتال</a>
                            <ul>
                                <li class="sub-menu">
                                    <a href="#">عنوان دسته</a>
                                    <ul>
                                        <li>
                                            <a href="#">زیر منو یک</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو دو</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو سه</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو چهار</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sub-menu">
                                    <a href="#">عنوان دسته</a>
                                    <ul>
                                        <li>
                                            <a href="#">زیر منو یک</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو دو</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو سه</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">عنوان دسته</a>
                                </li>
                                <li>
                                    <a href="#">عنوان دسته</a>
                                </li>
                                <li class="sub-menu">
                                    <a href="#">عنوان دسته</a>
                                    <ul>
                                        <li>
                                            <a href="#">زیر منو یک</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو دو</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو سه</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو چهار</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="#">بهداشت و سلامت</a>
                            <ul>
                                <li class="sub-menu">
                                    <a href="#">عنوان دسته</a>
                                    <ul>
                                        <li>
                                            <a href="#">زیر منو یک</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو دو</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو سه</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو چهار</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sub-menu">
                                    <a href="#">عنوان دسته</a>
                                    <ul>
                                        <li>
                                            <a href="#">زیر منو یک</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو دو</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو سه</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">عنوان دسته</a>
                                </li>
                                <li>
                                    <a href="#">عنوان دسته</a>
                                </li>
                                <li class="sub-menu">
                                    <a href="#">عنوان دسته</a>
                                    <ul>
                                        <li>
                                            <a href="#">زیر منو یک</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو دو</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو سه</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو چهار</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="#">ابزار و اداری</a>
                            <ul>
                                <li class="sub-menu">
                                    <a href="#">عنوان دسته</a>
                                    <ul>
                                        <li>
                                            <a href="#">زیر منو یک</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو دو</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو سه</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو چهار</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sub-menu">
                                    <a href="#">عنوان دسته</a>
                                    <ul>
                                        <li>
                                            <a href="#">زیر منو یک</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو دو</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو سه</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">عنوان دسته</a>
                                </li>
                                <li>
                                    <a href="#">عنوان دسته</a>
                                </li>
                                <li class="sub-menu">
                                    <a href="#">عنوان دسته</a>
                                    <ul>
                                        <li>
                                            <a href="#">زیر منو یک</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو دو</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو سه</a>
                                        </li>
                                        <li>
                                            <a href="#">زیر منو چهار</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">مد و پوشاک</a>
                        </li>
                        <li>
                            <a href="#">خانه و آشپزخانه</a>
                        </li>
                        <li>
                            <a href="#">ورزش و سفر</a>
                        </li>
                    </ul>
                </div>
                <div class="overlay-side-menu">
                </div>
            </nav>
            <!-- End Main-Menu -->
        </div>
    </div>
    <!-- End bottom-header -->
</header>
