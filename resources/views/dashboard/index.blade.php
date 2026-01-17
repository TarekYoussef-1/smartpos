@extends('layouts.master')
@section('title', 'لوحة التحكم')
@push('styles')
<style>
    /* أضف padding-top للمحتوى الرئيسي لتجنب اختفائه خلف الـ navbar */
    .page-wrapper {
        padding-top: 70px;
        /* اضبط هذه القيمة حسب ارتفاع navbar */
    }
</style>
@endpush
@section('content')
<div class="container-fluid">
    <div class="col-lg-3 col-md-6 mb-4"></div>
    {{-- ✅ ملخص سريع (البطاقات الأربع) --}}
    <div class="row">
        <!-- البطاقات الأصلية -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title">الأقسام</h5>
                        <p class="card-text">{{ $departmentsCount ?? 0 }} قسم</p>
                    </div>
                    <i class="fas fa-sitemap fa-3x"></i>
                </div>
                <a href="{{ route('departments.index') }}" class="card-footer text-white text-center">
                    إدارة الأقسام
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title">الأصناف</h5>
                        <p class="card-text">{{ $productsCount ?? 0 }} صنف</p>
                    </div>
                    <i class="fas fa-box fa-3x"></i>
                </div>
                <a href="{{ route('products.index') }}" class="card-footer text-white text-center">
                    إدارة الأصناف
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title">المستخدمين</h5>
                        <p class="card-text">{{ $usersCount ?? 0 }} مستخدم</p>
                    </div>
                    <i class="fas fa-users fa-3x"></i>
                </div>
                <a href="{{ route('users.index') }}" class="card-footer text-white text-center">
                    إدارة المستخدمين
                </a>
            </div>
        </div>

    <div class="col-lg-3 col-md-6 mb-3">
    <div class="card text-white bg-secondary">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title">البرنترات</h5>
                <p class="card-text">{{ $printersCount ?? 0 }} برنتر</p>
            </div>
            <i class="fas fa-print fa-3x"></i>
        </div>
        <a display="hiden" href="{{ route('printers.index') }}" class="card-footer text-white text-center">
            إدارة البرنترات 
        </a>
    </div>
</div>
<div class="col-lg-3 col-md-6 mb-3"></div>
<div class="col-lg-3 col-md-6 mb-3"></div>


       
     <div class="col-lg-3 col-md-6 mb-3">
    <a style="width:100%"  href="{{ route('reports.sales.manager') }}" class="text-decoration-none">
        <div class="card text-white bg-info h-100 dashboard-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title">التقارير والتحليل</h5>
                    <p class="card-text mb-0">
                        مبيعات – جرد – عجز وزيادة
                    </p>
                </div>
                <i class="fas fa-chart-line fa-3x"></i>
            </div>
            <div class="card-footer text-white text-center">
                عرض التقارير
            </div>
        </div>
    </a>
</div>
 <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-danger">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title">كاشير</h5>
                        <p class="card-text">افتح الكاشير لإدارة الطلبات</p>
                    </div>
                    <i class="fas fa-cash-register fa-3x"></i>
                </div>
                <a href="{{ route('pos.index') }}" class="card-footer text-white text-center">
                    فتح الكاشير
                </a>
            </div>
        </div>


    </div>

    {{-- ✅ إحصائيات اليوم --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <h4 class="mb-3">إحصائيات اليوم</h4>
        </div>
        <div class="col-lg-3 mb-2">

    <!-- كارت مبيعات اليوم -->
    <div class="card border-left-primary shadow py-2 mb-2">
        <div style="height: 188px;" class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        إجمالي مبيعات اليوم
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($todaySales, 2) }} ج.م
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- كارت مبيعات الشهر -->
    <div class="card border-left-success shadow py-2">
        <div style="height: 188px;" class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        إجمالي مبيعات الشهر
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($monthlySales, 2) }} ج.م
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

</div>

        <!-- بطاقة عدد الطلبات اليوم -->
        <div class="col-lg-4 mb-2">
            <div style="height: 90%;" class="card text-white bg-success shadow ">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-utensils fa-2x me-2"></i>
                            <h5 class="card-title mb-0">عدد الطلبات اليوم</h5>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="text-center">
                                    <i class="fas fa-chair fa-lg text-light mb-1"></i>
                                    <div class="fw-bold fs-5">{{ $dineInCount ?? 0 }}</div>
                                    <small class="text-light opacity-75">Dine In</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <i class="fas fa-shopping-bag fa-lg text-light mb-1"></i>
                                    <div class="fw-bold fs-5">{{ $takeAwayCount ?? 0 }}</div>
                                    <small class="text-light opacity-75">Take Away</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <i class="fas fa-motorcycle fa-lg text-light mb-1"></i>
                                    <div class="fw-bold fs-5">{{ $deliveryCount ?? 0 }}</div>
                                    <small class="text-light opacity-75">Delivery</small>
                                </div>
                            </div>
                        </div>
                        <hr class="my-2 border-light opacity-30">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-6">الإجمالي:</span>
                            <span class="badge bg-light text-success fs-5 px-3 py-2">
                                <i class="fas fa-chart-line me-1"></i>
                                {{ $todayOrdersCount }}
                            </span>
                        </div>
                    </div>
                </div>
                 <div class="card-footer bg-transparent border-0 p-2 text-center">
            <button style="background-color: whitesmoke; padding: 10px; border-radius: 8px;" class="scroll-to-orders-btn" onclick="scrollToOrders()" title="الانتقال إلى إدارة الطلبات">
                <i class="fas fa-list"></i>
                <span>عرض الطلبات</span>
            </button>
        </div>
            </div>
        </div>


        <!-- المخطط الدائري لتوزيع الطلبات -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع الطلبات حسب النوع</h6>
                    <select class="form-select form-select-sm" id="orderTypePeriodSelect">
                        <option value="today">اليوم</option>
                        <option value="last7days" selected>آخر 7 أيام</option>
                        <option value="thismonth">هذا الشهر</option>
                        <option value="lastmonth">الشهر الماضي</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="orderTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


    </div>
     <!-- المخطط البياني للمبيعات -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">المبيعات</h6>
                    <div class="d-flex align-items-center">
                        <select class="form-select form-select-sm" id="salesPeriodSelect">
                            <option value="today">اليوم</option>
                            <option value="last7days" selected>آخر 7 أيام</option>
                            <option value="thismonth">هذا الشهر</option>
                            <option value="lastmonth">الشهر الماضي</option>
                        </select>
                        <div id="chartLoader" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    {{-- ===================== --}}
{{-- إدارة الشيفتات (للمدير فقط) --}}
{{-- ===================== --}}
@if(session('user') && session('user')->role === 'admin')
<div class="row mt-5">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    إدارة الشيفتات
                </h6>
                <span class="badge bg-dark fs-6">
                    إجمالي المبيعات: {{ number_format($totalShiftsSales ?? 0, 2) }} ج.م
                </span>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>الكاشير</th>
                            <th>بداية الشيفت</th>
                            <th>نهاية الشيفت</th>
                            <th>إجمالي المبيعات</th>
                            <th>الحالة</th>
                            <th>إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                            <tr>
                                <td>{{ $shift->id }}</td>
                                <td>{{ $shift->user_name ?? 'غير معروف' }}</td>
                                <td>{{ $shift->opened_at }}</td>
                                <td>
                                    {{ $shift->closed_at ?? '-' }}
                                </td>
                                <td class="fw-bold text-success">
                                    {{ number_format($shift->total_sales ?? 0, 2) }} ج.م
                                </td>
                                <td>
                                    @if($shift->closed_at)
                                        <span class="badge bg-secondary">مقفول</span>
                                    @else
                                        <span class="badge bg-success">مفتوح</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$shift->closed_at)
                                        <button 
                                            class="btn btn-danger btn-sm"
                                            onclick="confirmCloseShift({{$shift->id}})">
                                            <i class="fas fa-lock"></i>
                                            إغلاق
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted">
                                    لا توجد شيفتات
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif




    {{-- ✅ المخططات البيانية --}}
    <div class="row mt-4">


       
    </div>
    {{-- ✅ قسم فلترة وعرض الطلبات --}}
    <div id="orders-section" class="row mt-4">
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إدارة الطلبات</h6>
                </div>
                <div class="card-body">
                    {{-- ✅ نموذج الفلترة --}}
                    <form action="{{ route('dashboard.index') }}" method="GET" class="row g-3 mb-4 align-items-end" id="orderFiltersForm">
                        <div class="col-md-3">
                            <label for="order_id" class="form-label">بحث برقم الطلب</label>
                            <input type="number" name="order_id" id="order_id" class="form-control" placeholder="أدخل رقم الطلب" value="{{ request('order_id') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">من تاريخ</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">إلى تاريخ</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <button type="button" class="btn btn-secondary" id="resetFilters">
                                    <i class="fas fa-redo"></i> إعادة تعيين
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- ✅ جدول الطلبات --}}
                    <div id="ordersTableWrapper">
                        @include('dashboard._orders_table')
                    </div>

                    {{-- ✅ روابط التقسيم الصفحي --}}
                    <div class="d-flex justify-content-center mt-4" id="paginationWrapper">
                        {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>

  function confirmCloseShift(shiftId) {

    if (!confirm('هل أنت متأكد من إغلاق الشيفت؟')) return;

    fetch("{{ url('/dashboard/shifts') }}/" + shiftId + "/close", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            alert(data.message || 'حدث خطأ في إغلاق الشيفت');
            return;
        }

        alert('تم إغلاق الشيفت بنجاح ✔');

        // طباعة التقرير الحراري
        window.open("{{ url('/shift/report') }}/" + shiftId, "_blank");

        // 👈 التوجيه لصفحة مراجعة الكاش
        window.location.href = "/dashboard/shift-cash/" + shiftId;

    })
    .catch(() => alert('خطأ في الاتصال بالسيرفر'));
}



    
    let orderTypeChart;
    let salesChart;

    // --- دالة المخطط الدائري ---
    function renderOrderTypeChart(labels, data) {
        const ctx = document.getElementById('orderTypeChart');
        if (!ctx) return;

        if (orderTypeChart) {
            orderTypeChart.data.labels = labels;
            orderTypeChart.data.datasets[0].data = data;
            orderTypeChart.update();
        } else {
            orderTypeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    // --- دالة مخطط المبيعات ---
    function renderSalesChart(labels, data) {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        if (salesChart) {
            salesChart.data.labels = labels;
            salesChart.data.datasets[0].data = data;
            salesChart.update();
        } else {
            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: "إجمالي المبيعات (ج.م)",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' ج.م';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('ar-EG', {
                                            style: 'currency',
                                            currency: 'EGP'
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // --- دالة جلب البيانات وتحديث المخططات ---
    function updateCharts(orderTypePeriod, salesPeriod) {
        const loader = document.getElementById('chartLoader');
        loader.classList.remove('d-none');

        fetch(`{{ route('dashboard.order-type.chart.data', ':period') }}`.replace(':period', orderTypePeriod))
            .then(res => res.json())
            .then(response => renderOrderTypeChart(response.labels, response.data))
            .catch(error => console.error('Error fetching order type data:', error));

        fetch(`{{ route('dashboard.sales.chart.data', ':period') }}`.replace(':period', salesPeriod))
            .then(res => res.json())
            .then(response => renderSalesChart(response.labels, response.data))
            .catch(error => console.error('Error fetching sales data:', error))
            .finally(() => loader.classList.add('d-none'));
    }

    // --- دالة مساعدة لمسح الفلاتر ---
    function clearFilters() {
        document.getElementById('orderFiltersForm').reset();
        window.location.href = "{{ route('dashboard.index') }}";
    }

    document.addEventListener('DOMContentLoaded', () => {
        const initialOrderTypePeriod = document.getElementById('orderTypePeriodSelect').value;
        const initialSalesPeriod = document.getElementById('salesPeriodSelect').value;
        updateCharts(initialOrderTypePeriod, initialSalesPeriod);

        document.getElementById('orderTypePeriodSelect').addEventListener('change', (e) => {
            const salesPeriod = document.getElementById('salesPeriodSelect').value;
            updateCharts(e.target.value, salesPeriod);
        });

        document.getElementById('salesPeriodSelect').addEventListener('change', (e) => {
            const orderTypePeriod = document.getElementById('orderTypePeriodSelect').value;
            updateCharts(orderTypePeriod, e.target.value);
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // دالة لتحميل البيانات عبر AJAX
        function loadOrders(url = "{{ route('dashboard.index') }}", isReset = false) {
            let formData = new FormData(document.getElementById('orderFiltersForm'));
            let queryParams = new URLSearchParams();

            if (!isReset) {
                // جمع الفلاتر من الفورم
                for (let [key, value] of formData.entries()) {
                    if (value) {
                        queryParams.append(key, value);
                    }
                }
            }

            // إضافة الـ page إذا كان موجود في الـ URL
            let pageMatch = url.match(/page=(\d+)/);
            if (pageMatch) {
                queryParams.append('page', pageMatch[1]);
            }

            // طلب AJAX
            fetch(`${url.split('?')[0]}?${queryParams.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // ✅ تحديث الـ wrapper بالجدول الكامل (لأن الـ partial يحتوي على table كاملة)
                    document.getElementById('ordersTableWrapper').innerHTML = data.table;

                    // تحديث الـ pagination
                    document.getElementById('paginationWrapper').innerHTML = data.pagination;

                    // إعادة ربط الأحداث على الروابط الجديدة
                    attachPaginationListeners();
                })
                .catch(error => console.error('Error:', error));
        }

        // ربط الأحداث على روابط الـ pagination
        function attachPaginationListeners() {
            document.querySelectorAll('#paginationWrapper .pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadOrders(this.href);
                });
            });
        }

        // ربط submit الفورم عبر AJAX
        document.getElementById('orderFiltersForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loadOrders();
        });

        // ربط زر إعادة التعيين عبر AJAX
        document.getElementById('resetFilters').addEventListener('click', function(e) {
            e.preventDefault();
            // مسح حقول الفورم
            document.getElementById('order_id').value = '';
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            loadOrders("{{ route('dashboard.index') }}", true); // ✅ تحميل بدون فلاتر، واستخدم الـ route مباشرة (لأنه button وليس a)
        });

        // ربط الأحداث الأولي
        attachPaginationListeners();
    });

    // ✅ دالة التمرير السلس إلى قسم الطلبات
    function scrollToOrders() {
        const targetSection = document.getElementById('orders-section');
        if (targetSection) {
            targetSection.scrollIntoView({
                behavior: 'smooth', // تمرير سلس
                block: 'start', // يبدأ من أعلى القسم
                inline: 'nearest' // محاذاة أفقية تلقائية
            });
        } else {
            console.warn('القسم المستهدف غير موجود!'); // تحذير إذا مش لاقي الـ ID
        }
    }

   

  
</script>
@endpush
@endsection