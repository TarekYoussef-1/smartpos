<!doctype html>
<html lang="ar">
<head>
<meta charset="utf-8">
<title>تقرير شيفت #{{ $shift->id }}</title>

<style>
    body {
            margin: 0px !important;
            width: 100mm;
            font-family: auto;
            text-align: center;
            font-size: 25px;
            font-weight: 600;
    }
    .line{
        border-bottom:2px solid #000;
        margin:5px 0;
    }
    table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5px;
    font-size: 22px;
}

th, td {
    border-bottom: 1px dashed #000;
    padding: 4px 0;
    text-align: center;
}

th {
    font-weight: bold;
}

.order-type {
    font-size: 20px;
}

</style>

</head>
<body>

<h3>تقرير الشيفت</h3>

<div class="line"></div>

تاريخ الفتح:
{{ $shift->opened_at }}<br>

تاريخ الإغلاق:
{{ $shift->closed_at ?? 'مفتوح' }}<br>

الكاشير:
{{ $shift->cashier_name }}<br>

<div class="line"></div>

عدد الطلبات: {{ $totalOrders }}

<div class="line"></div>

{{ number_format($paidTotal,2) }} 
    :اجمالي المبيعات    

<div style="direction: lrt;" class="line"></div>

{{ number_format($cancelledTotal,2) }} 
    :طلبات ملغاة   

<div class="line"></div>
<h4>تفاصيل الطلبات</h4>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>النوع</th>
            <th>القيمة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $o)
            <tr>
                <td>{{ $o->daily_serial }}</td>
                <td class="order-type">
                    @if($o->type === 'dine_in')
                        صالة
                    @elseif($o->type === 'take_away')
                        تيك آوي
                    @elseif($o->type === 'delivery')
                        دليفري
                    @endif
                </td>
                <td>{{ number_format($o->total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="line"></div>

صفحة: 1
<script>
window.onload = function() {
    window.print( "_blank");
    window.onafterprint = function() {
        window.close(); // تغلق النافذة بعد الطباعة
    }
};
</script>

</body>
</html>
