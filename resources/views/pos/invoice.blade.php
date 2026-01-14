<!DOCTYPE html>
    <html dir="rtl" lang="ar">
    <head>
        <meta charset="UTF-8">
        <title>فاتورة</title>
        <style>
            body {
                font-family: 'Courier New', monospace, 'DejaVu Sans';
                font-size: 11px;
                line-height: 1.3;
                margin: 0;
                padding: 5px;
                width: 100%;
            }
            .text-center { text-align: center; }
            .text-right  { text-align: right; }
            .text-left   { text-align: left; }
            .bold { font-weight: bold; }
            .large { font-size: 13px; }
            .xlarge { font-size: 15px; font-weight: bold; }
            .line { border-top: 1px dashed #000; margin: 8px 0; }
            .double-line { border-top: 3px double #000; margin: 10px 0; }
            .item-row { display: flex; justify-content: space-between; }
            .qty { width: 40px; text-align: center; }
            .price { width: 80px; text-align: center; }
            .total { width: 90px; text-align: right; }
            .name { flex: 1; text-align: right; padding-left: 5px; overflow: hidden; }
            @page { margin: 0; size: 80mm auto; }
            @media print {
                body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            }
        </style>
    </head>
    <body onload="window.print(); setTimeout(() => window.close(), 800)">

    <div class="text-center xlarge bold">مطعم [اسم المطعم]</div>
    <div class="text-center">--------------------------------</div>
    <div class="text-center">فاتورة رقم: <strong>{{ $invoice_number }}</strong></div>
    <div class="text-center">{{ $date }}</div>

    {{-- نوع الطلب --}}
    @if($order['type'] === 'dine_in')
        <div class="text-center large bold" style="color:#000;background:#ddd;padding:3px;margin:5px 0;">
            تناول داخل المطعم
        </div>
    @elseif($order['type'] === 'take_away')
        <div class="text-center large bold" style="color:#000;background:#ddd;padding:3px;margin:5px 0;">
            تيك أواي
        </div>
    @else
        <div class="text-center large bold" style="color:#fff;background:#d32f2f;padding:3px;margin:5px 0;">
            توصيل دليفري
        </div>
    @endif

    <div class="double-line"></div>

    {{-- بيانات العميل فقط في الدليفري --}}
    @if($order['type'] === 'delivery' && $order['customer'])
    <div class="bold">العميل: {{ $order['customer']['name'] }}</div>
    <div class="bold">الهاتف: {{ $order['customer']['phone'] }}</div>
    <div class="bold">العنوان:</div>
    <div>{{ $order['customer']['area'] }} - {{ $order['customer']['street'] }}</div>
    <div>مبنى {{ $order['customer']['building'] }} دور {{ $order['customer']['floor'] }} شقة {{ $order['customer']['apartment'] }}</div>
    @if($order['customer']['landmark'])
    <div>علامة: {{ $order['customer']['landmark'] }}</div>
    @endif
    @if($order['customer']['notes'])
    <div>ملاحظات: {{ $order['customer']['notes'] }}</div>
    @endif
    <div class="line"></div>
    @endif

    {{-- الأصناف --}}
    <div class="item-row bold">
        <div class="qty">الكمية</div>
        <div class="price">السعر</div>
        <div class="total">الإجمالي</div>
    </div>
    <div class="line"></div>

    @foreach($order['items'] as $item)
    <div class="item-row">
        <div class="name bold">{{ $item['name'] }}</div>
    </div>
    <div class="item-row">
        <div class="qty">{{ $item['quantity'] }} ×</div>
        <div class="price">{{ number_format($item['price'], 2) }}</div>
        <div class="total bold">{{ number_format($item['total'], 2) }}</div>
    </div>
    <div class="line" style="border-style:dotted;"></div>
    @endforeach

    <div class="double-line"></div>

    <div class="item-row xlarge bold">
        <div>الإجمالي الكلي</div>
        <div></div>
        <div>{{ number_format($order['total'], 2) }} ج.م</div>
    </div>

    <div class="double-line"></div>

    <div class="text-center">
        الكاشير: {{ $order['cashier_name'] ?? 'غير محدد' }}<br>
        الشيفت: {{ $order['shift_name'] ?? 'غير محدد' }}
    </div>

    <div class="text-center" style="margin-top:15px;">
        <strong>شكراً لزيارتكم</strong><br>
        نتمنى لكم يوماً سعيداً ♥
    </div>

    <div class="text-center" style="margin-top:20px;font-size:10px;">
        تمت الطباعة: {{ now()->format('Y-m-d H:i') }}
    </div>

    </body>
    </html>