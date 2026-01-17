@extends('layouts.master')

@section('title', 'لوحة تحكم المدير')

@push('styles')
<style>
    .page-wrapper {
        padding-top: 70px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ========================= --}}
    {{-- كروت سريعة --}}
    {{-- ========================= --}}
    <div class="row">

        {{-- التقارير --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5>التقارير</h5>
                        <small>مبيعات وتحليل</small>
                    </div>
                    <i class="fas fa-chart-line fa-3x"></i>
                </div>
                <a href="{{ route('reports.sales.manager') }}" class="card-footer text-white text-center">  
                        عرض التقارير
                    </a>
            </div>
        </div>

        {{-- الكاشير --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-danger h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5>الكاشير</h5>
                        <small>إدارة الطلبات</small>
                    </div>
                    <i class="fas fa-cash-register fa-3x"></i>
                </div>
                <a href="{{ route('pos.index') }}" class="card-footer text-white text-center">
                    فتح الكاشير
                </a>
            </div>
        </div>

    </div>

    {{-- ========================= --}}
    {{-- إحصائيات اليوم --}}
    {{-- ========================= --}}
    <div class="row mt-4">

        {{-- مبيعات اليوم --}}
        <div class="col-lg-3 mb-3">
            <div class="card shadow border-left-primary">
                <div class="card-body">
                    <div class="text-primary text-uppercase small">مبيعات اليوم</div>
                    <div class="h5 fw-bold">
                        {{ number_format($todaySales ?? 0, 2) }} ج.م
                    </div>
                </div>
            </div>
        </div>

        {{-- مبيعات الشهر --}}
        <div class="col-lg-3 mb-3">
            <div class="card shadow border-left-success">
                <div class="card-body">
                    <div class="text-success text-uppercase small">مبيعات الشهر</div>
                    <div class="h5 fw-bold">
                        {{ number_format($monthlySales ?? 0, 2) }} ج.م
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ========================= --}}
    {{-- الشيفت (للمدير فقط) --}}
    {{-- ========================= --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="text-primary">الشيفت الحالي</h6>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
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
                                <td>{{ $shift->opened_at }}</td>
                                <td>{{ $shift->closed_at ?? '-' }}</td>
                                <td class="fw-bold text-success">
                                    {{ number_format($shift->total_sales ?? 0,2) }} ج.م
                                </td>
                                <td>
                                    @if($shift->closed_at)
                                        <span class="badge bg-secondary">مغلق</span>
                                    @else
                                        <span class="badge bg-success">مفتوح</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$shift->closed_at)
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmCloseShift({{ $shift->id }})">
                                            <i class="fas fa-lock"></i> إغلاق
                                        </button>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-muted">لا يوجد شيفت</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- الطلبات --}}
    {{-- ========================= --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="text-primary">الطلبات</h6>
                </div>
                <div class="card-body">
                    @include('dashboard._orders_table')
                    <div class="d-flex justify-content-center mt-3">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


@push('scripts')
<script>
function confirmCloseShift(id) {
    if (!confirm('هل أنت متأكد من إغلاق الشيفت؟')) return;

    fetch(`{{ url('/dashboard/shifts') }}/${id}/close`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'حدث خطأ');
            return;
        }
        alert('تم إغلاق الشيفت بنجاح');
        location.reload();
    })
    .catch(() => alert('خطأ في الاتصال بالسيرفر'));
}
</script>
@endpush
@endsection
