@extends('layouts.master')

@section('content')
<div class="container-fluid">
   <!-- زر العودة -->
<div style="margin-top: 40px;" class="d-flex justify-content-between align-items-center mb-3">
    @php
        $user = session('user');
        $backUrl = url('/');

        if ($user) {
            if ($user->role === 'admin') {
                $backUrl = route('dashboard.index');
            } elseif ($user->role === 'manager') {
                $backUrl = route('dashboard.manager');
            }
        }
    @endphp

    <a href="{{ $backUrl }}" class="btn btn-outline-danger">
        <i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم
    </a>
</div>

    <h4 class="mb-4">تقرير مبيعات الأصناف</h4>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>من</label>
            <input type="date" name="from" class="form-control" required value="{{ $from }}">
        </div>
        <div class="col-md-3">
            <label>إلى</label>
            <input type="date" name="to" class="form-control" required value="{{ $to }}">
        </div>
        <div class="col-md-2 d-flex flex-column gap-2 align-self-end">
            <button class="btn btn-primary w-100">
                عرض التقرير
            </button>
        </div>

        <!-- زر الطباعة يظهر فقط عند وجود بيانات -->
    @if($report->isNotEmpty())
<div class="col-md-2 d-flex flex-column gap-2 align-self-end">

    <!-- زر الطباعة -->
    <a href="{{ route('reports.sales.print', ['from' => $from, 'to' => $to]) }}"
       class="btn btn-outline-primary btn-lg w-100"
       target="_blank"
       title="طباعة التقرير">
        <i class="fas fa-print me-2"></i>
        طباعة التقرير
    </a>
</div>
<div class="col-md-2 d-flex flex-column gap-2 align-self-end">  
    <!-- زر تحميل Excel -->
    <a href="{{ route('sales.export') }}"
       class="btn btn-success btn-lg w-100"
       title="تحميل تقرير المبيعات Excel">
        <i class="fas fa-file-excel me-2"></i>
        تحميل Excel
    </a>

</div>
@endif


    </form>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>الصنف</th>
                    <th>عدد مرات البيع</th>
                    <th>إجمالي الكمية</th>
                    <th>إجمالي المبيعات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $row)
                <tr>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->orders_count }}</td>
                    <td>{{ $row->total_qty }}</td>
                    <td class="fw-bold text-success">
                        {{ number_format($row->total_amount, 2) }} ج.م
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">لا توجد بيانات لعرضها في الفترة المحددة.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection