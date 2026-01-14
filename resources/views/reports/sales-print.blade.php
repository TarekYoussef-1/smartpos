<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>تقرير المبيعات</title>
    <style>
        * {
            box-sizing: border-box;
            /* خط عربي واضح ومتوافق */
            font-family: 'Tahoma', sans-serif;
        }
        body {
            width: 70mm; /* نفس مقاس الطلب الخاص بك */
            margin: 0;
            padding: 2px;
            font-size: 16px;
            align-items: center;
        }
        .center {
            text-align: center;
        }
        .report-title {
            font-size: 26px;
            margin-bottom: 5px;
        }
        .date-range {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .separator {
            border-top: 2px dashed #000;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000; /* إطار خارجي للجدول */
            text-align: center;
        }
        th,
        td {
            border: 1.5px solid #000; /* حدود الصفوف والأعمدة */
            padding: 6px 4px;
            text-align: center;
        }
        th {
            font-size: 17px;
        }
        td {
            font-size: 16px;
        }
        /* تحديد عرض الأعمدة لتناسب البيانات */
        td.name {
            width: 50%;
        }
        td.qty {
            width: 25%;
        }
        td.total {
            width: 25%;
        }
        .footer {
            margin-top: 10px;
            font-size: 16px;
            text-align: center;
        }
        /* إخفاء عناصر التحكم عند الطباعة */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="center report-title">تقرير المبيعات</div>
    <div class="center date-range">من {{ $from }} إلى {{ $to }}</div>

    <div class="separator"></div>

    <table>
        <thead>
            <tr>
                <th class="name">الصنف</th>
                <th class="qty">الكمية</th>
                
            </tr>
        </thead>
        <tbody>
            @forelse($report as $row)
            <tr>
                <td class="name">{{ $row->product_name }}</td>
                <td class="qty">{{ $row->total_qty }}</td>
               
            </tr>
            @empty
            <tr>
                <td colspan="3" class="center">لا توجد بيانات</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="separator"></div>

   

    <div class="footer">
        {{ now()->format('Y-m-d H:i') }} <br> SmartPOS
    </div>

</body>

</html>