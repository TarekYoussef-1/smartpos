<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير نقدية الشيفت</title>

    <style>
        body {
            font-family: Tahoma, Arial;
            font-size: 12px;
            width: 80mm;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
        }

        .success { color: green; font-weight: bold; }
        .danger  { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
    </style>
</head>

<body>

@php
    // إجمالي النقدية من التقفيل
    $cashTotal   = $shift->closing_balance;

    // إجمالي النظام
    $systemTotal = $paidTotal;

    // الفرق
    $diff = $cashTotal - $systemTotal;

    if ($diff == 0) {
        $diffClass = 'success';
    } elseif ($diff < 0) {
        $diffClass = 'danger';   // عجز
    } else {
        $diffClass = 'warning';  // زيادة
    }
@endphp

<div class="center bold">
    تقرير نقدية الشيفت
</div>

<hr>

<table>
    <tr>
        <td>شيفت رقم:</td>
        <td class="right">{{ $shift->id }}</td>
    </tr>
    <tr>
        <td>الكاشير:</td>
        <td class="right">{{ $shift->cashier_name }}</td>
    </tr>
    <tr>
        <td>وقت الفتح:</td>
        <td class="right">{{ $shift->opened_at }}</td>
    </tr>
    <tr>
        <td>وقت الإغلاق:</td>
        <td class="right">{{ $shift->closed_at }}</td>
    </tr>
    <tr>
        <td>عدد الطلبات:</td>
        <td class="right">{{ $orderCount }}</td>
    </tr>
</table>

<hr>

<div class="bold center">تفاصيل الفئات</div>

<table>
    <tr class="bold">
        <td>الفئة</td>
        <td class="center">العدد</td>
        <td class="right">القيمة</td>
    </tr>

    @foreach($denoms as $value => $count)
        <tr>
            <td>{{ $value }} ج </td>
            <td class="center">{{ $count }}</td>
            <td class="right">{{ number_format($value * $count, 2) }}</td>
        </tr>
    @endforeach
</table>

<hr>

<div class="bold center">ملخص النقدية</div>

<table>
    <tr>
        <td>إجمالي النقدية:</td>
        <td class="right bold">{{ number_format($cashTotal, 2) }} جم</td>
    </tr>
    <tr>
        <td>إجمالي مبيعات النظام:</td>
        <td class="right bold">{{ number_format($systemTotal, 2) }} جم</td>
    </tr>
    <tr>
        <td>الفرق (نقدية - نظام):</td>
        <td class="right {{ $diffClass }}">
            {{ number_format($diff, 2) }} جم
        </td>
    </tr>
</table>

<hr>

<div class="center">
    {{ now()->format('Y-m-d H:i') }}
</div>

<script>
window.onload = function() {
    window.print();
    window.onafterprint = function() {
        window.close(); // تغلق النافذة بعد الطباعة
    }
};
</script>

</body>
</html>
