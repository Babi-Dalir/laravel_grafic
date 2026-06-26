@extends('frontend.layouts.master')

@section('content')
    @include('frontend.layouts.header')

    <main class="main-content dt-sl mb-3 bg-light-subtle">
        {{-- فراخوانی جزء لایووایر محصول --}}
        <livewire:frontend.products.single-product :product="$product" />

        <div class="container mx-auto mt-2">
            <div class="card border-0 shadow-sm rounded-20 bg-white overflow-hidden mb-5">
                <section class="tabs-product-info dt-sl">

                    <div class="ah-tab-custom-wrapper dt-sl">
                        <div class="ah-tab dt-sl">
                            <a class="ah-tab-item-custom" href="javascript:void(0)">
                                <i class="mdi mdi-format-list-checks me-1 text-danger"></i>مشخصات فنی فایل منبع
                            </a>
                        </div>
                    </div>

                    <div class="product-info px-4 py-4 dt-sl">
                        <div class="params dt-sl">

                            <div class="mb-4">
                                <h3 class="h5 text-dark font-weight-black mb-1">{{ $product->name }}</h3>
                                @if($product->e_name)
                                    <span class="text-muted font-numeric font-13">{{ $product->e_name }}</span>
                                @endif
                            </div>

                            <section class="mt-2">
                                <h4 class="font-14 text-secondary font-weight-bold mb-3 d-flex align-items-center">
                                    <i class="mdi mdi-text-box-search-outline text-danger me-2 fs-5"></i>
                                    بررسی پکیج و ویژگی‌های لایه‌ها
                                </h4>

                                <ul class="list-unstyled p-0 m-0">
                                    @foreach($product->propertyGroups as $propertyGroup)
                                        @php
                                            $productProperties = $propertyGroup->properties->where('product_id', $product->id);
                                        @endphp

                                        @if($productProperties->count() > 0)
                                            <li class="params-list-item">
                                                <div class="params-key-box">
                                                    <span>{{ $propertyGroup->name }}</span>
                                                </div>

                                                <div class="params-value-box">
                                                    @foreach($productProperties as $property)
                                                        <span class="property-badge-item font-numeric">
                                                            {{ $property->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </section>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    {{-- ⚡ اسکریپت فوق پیشرفته و منعطف تایمر معکوس شگفت‌انگیز متصل به هوک‌های لایووایر --}}
    <script>
        let babiTimerInterval = null;

        function startBabiCountdown() {
            const timerEl = document.getElementById('babi-product-countdown');
            if (!timerEl) return;

            const expirationAttr = timerEl.getAttribute('data-expiration');
            let targetTimestamp = NaN;

            // تحلیل ساختاری امن نوع تاریخ دریافتی دیتابیس
            if (!isNaN(expirationAttr)) {
                targetTimestamp = parseInt(expirationAttr) * 1000;
            } else {
                targetTimestamp = Date.parse(expirationAttr.replace(/-/g, '/'));
            }

            // در صورتی که ساختار فرمت تاریخ با مرورگر کلاینت همخوانی نداشت (فال‌بک امن برای جلوگیری از خرابی فرانت)
            if (isNaN(targetTimestamp)) {
                targetTimestamp = new Date().getTime() + (12 * 60 * 60 * 1000);
            }

            if (babiTimerInterval) clearInterval(babiTimerInterval);

            babiTimerInterval = setInterval(() => {
                const now = new Date().getTime();
                const distance = targetTimestamp - now;

                if (distance < 0) {
                    clearInterval(babiTimerInterval);
                    document.getElementById('babi-days').innerText = "00";
                    document.getElementById('babi-hours').innerText = "00";
                    document.getElementById('babi-minutes').innerText = "00";
                    document.getElementById('babi-seconds').innerText = "00";
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const dEl = document.getElementById('babi-days');
                const hEl = document.getElementById('babi-hours');
                const mEl = document.getElementById('babi-minutes');
                const sEl = document.getElementById('babi-seconds');

                if(dEl) dEl.innerText = String(days).padStart(2, '0');
                if(hEl) hEl.innerText = String(hours).padStart(2, '0');
                if(mEl) mEl.innerText = String(minutes).padStart(2, '0');
                if(sEl) sEl.innerText = String(seconds).padStart(2, '0');
            }, 1000);
        }

        // اجرای اولیه پس از لود دام
        document.addEventListener('DOMContentLoaded', startBabiCountdown);

        // همگام‌سازی و راه‌اندازی مجدد پس از تغییرات الگو توسط Livewire
        document.addEventListener('livewire:load', function () {
            window.Livewire.hook('message.processed', () => {
                startBabiCountdown();
            });
        });
    </script>
@endsection
