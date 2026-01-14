<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <style>
        * {
            box-sizing: border-box;
            /*خط عربي */
            font-family: 'Tahoma', sans-serif;
        }
        body {
            width: 87mm;
            margin: 0;
            padding: 5px;
            font-size: 25px;
            font-weight: bold;
            align-items: center;
        }
        .center {
            text-align: center;
        }
        .order-no {
            font-size: 26px;
            margin-bottom: 5px;
        }
        .separator {
            border-top: 2px dashed #000;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            /* إطار خارجي للجدول */
            text-align: center;
        }
        th,
        td {
            border: 1.5px solid #000;
            /* حدود الصفوف والأعمدة */
            padding: 6px 4px;
            text-align: center;
        }
        th {
            font-size: 18px;
            text-align: center;
        }
        td {
            font-size: 22px;
            text-align: center;
        }
        td.qty {
            width: 20%;
            text-align: center;
        }
        td.name {
            width: 80%;
            text-align: center;
        }
        .footer {
            margin-top: 10px;
            font-size: 16px;
            text-align: center;
        }
        .order-type {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            padding: 6px 0;
            margin-bottom: 6px;
            border: 2px solid #000;
        }
        .order-type.dine_in {
            background: #eee;
        }
        .order-type.take_away {
            background: #fff3cd;
        }
        .order-type.delivery {
            background: #f8d7da;
        }
    </style>

    <title>Kitchen Order #{{ $order->id }}</title>
</head>
@php
$typeText = match($order->type) {
'dine_in' => 'صالة',
'take_away' => 'تيك آوي',
'delivery' => 'دليفري',
default => ''
};
@endphp

<body>
    <br><br><br>
    <div class="center order-no"> رقم الطلب : #{{ $order->daily_serial }}</div>

    <div class="order-type {{ $order->type }}">
        {{ $typeText }}
    </div>
    <div class="separator"></div>
    <table>
        <thead>
            <tr>
                <th class="qty">الكمية</th>
                <th class="name">الــصنف</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td class="qty">{{ $item->quantity }}</td>
                <td class="name">{{ $item->product->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="separator"></div>
    <div class="footer">
        {{ now()->format('Y-m-d H:i') }}
    </div>
</body>

</html>