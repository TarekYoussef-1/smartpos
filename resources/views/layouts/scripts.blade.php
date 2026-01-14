<script>
// ✅ التحكم في عرض أقسام POS
function showPOS(type) {
    const deliveryInfo = document.getElementById('deliveryInfo');
    if (deliveryInfo) deliveryInfo.style.display = (type === 'delivery') ? 'block' : 'none';
}

// ✅ تحميل الصفحة يبدأ بـ Dine In
document.addEventListener('DOMContentLoaded', () => {
    showPOS('dineIn');
    updateClock();
    initCategoryEvents(); // تشغيل وظيفة تحميل الأصناف عند الضغط على القسم
    loadDefaultCategory();
 
});

//✅ تحميل الأصناف للقسم رقم 1 تلقائيًا
function loadDefaultCategory() {
    const categoryBtn1 = document.querySelector('.category-btn[data-id="1"]');
    if (categoryBtn1) {
        // إزالة تمييز من باقي الأزرار
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('btn-primary'));
        // تمييز القسم 1
        categoryBtn1.classList.add('btn-primary');
        // استدعاء fetch للأصناف للقسم 1
        categoryBtn1.click();
    }
}

// ✅ تحديث الساعة والتاريخ
function updateClock() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('posDate').textContent = now.toLocaleDateString('ar-EG', options);
    document.getElementById('posClock').textContent = now.toLocaleTimeString('ar-EG');
}
setInterval(updateClock, 1000);


</script>
    <script src="{{ asset('assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>

<!-- ✅ مكتبات الجافاسكربت -->
<script src="{{ asset('assets/js/vanilla-utils.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap-5.3.8.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chartjs/chart.umd.js-4.5.0.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap5-init.js') }}"></script>
<script src="{{ asset('assets/js/main-vanilla.js') }}"></script>
<script src="{{ asset('assets/js/swiper-bundle-11.2.10.min.js') }}"></script>
<script src="{{ asset('assets/js/aos.js') }}"></script>
<script src="{{ asset('assets/js/modern-plugins.js') }}"></script>
