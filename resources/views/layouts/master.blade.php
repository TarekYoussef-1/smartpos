<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    @include('layouts.header')
</head>
<body class="animsition pos-mode" >
    @include('layouts.navbar')
    <div class="page-wrapper">
        <div class="page-container">
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        @yield('content')
                        @include('layouts.footer')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ✅ تحديث الساعة والتاريخ
            function updateClock() {
                const now = new Date();
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('posDate').textContent = now.toLocaleDateString('ar-EG', options);
                document.getElementById('posClock').textContent = now.toLocaleTimeString('ar-EG');
            }

            // قم بتشغيل الدالة مرة واحدة عند تحميل الصفحة
            updateClock(); 
            // ثم قم بتشغيلها كل ثانية
            setInterval(updateClock, 1000);
        });
    </script>

  <!--  مكتبات الجافاسكربت -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="{{ asset('assets/js/vanilla-utils.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap-5.3.8.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chartjs/chart.umd.js-4.5.0.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap5-init.js') }}"></script>
<script src="{{ asset('assets/js/main-vanilla.js') }}"></script>
<script src="{{ asset('assets/js/swiper-bundle-11.2.10.min.js') }}"></script>
<script src="{{ asset('assets/js/aos.js') }}"></script>
<script src="{{ asset('assets/js/modern-plugins.js') }}"></script>
@stack('scripts')
</body>
</html>