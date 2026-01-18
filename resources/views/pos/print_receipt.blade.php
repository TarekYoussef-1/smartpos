<!doctype html>
<html lang="ar">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=320">
<title>فاتورة #{{ $order->id }}</title>
<style>
/* إعدادات رول الطباعة 80mm */
@page { size: 88mm; margin: 0; }
body { width: 80mm;  margin-left: 20px;padding: 5px; font-family: "Tahoma", sans-serif; font-size: 18px; direction: rtl; color: #000;; }
.center { text-align: center; }
.bold { font-weight: bold; }
.title {  font-weight: bold; margin-bottom: 4px; font-size: 40px; }
.sub { font-size: 20px; }
.divider { border-top: 2px dashed #000; margin: 6px 0; }
table { width: 100%; border-collapse: collapse; font-size: 18px; }
th, td { padding: 2px 0; }
th { font-weight: bold; }
.items td { vertical-align: top; }
.qty { width: 20%; text-align: center; }
.price { width: 20%; text-align: center; }
.total-row td { font-weight: bold; }
.footer { text-align: center; margin-top: 8px;  }
</style>
</head>
<body>
<div id="receipt">
    <!-- هنا نفس محتوى الفاتورة HTML -->
    <div class="center">
        <img src="{{ asset('assets/images/icon/dagago.png') }}" alt="Logo" style="max-width: 150px; height: auto;left: 20px;">
        <div class="title">DAGAGOO</div>
        <!-- <div class="sub">فاتورة مبيعات</div> -->
        
        {{-- === التعديل الجديد يبدأ من هنا === --}}
        @if(($isCancelled ?? false) || $order->status === 'cancelled')
            <div style="color: red; font-weight: bold; font-size: 20px; margin-top: 5px; margin-bottom: 5px;">
                *** ملغي ***
            </div>
        @endif
        @if($isReprint ?? false)
            <div style="color: red; font-weight: bold; font-size: 20px; margin-top: 5px; margin-bottom: 5px;">
                *** إعادة طباعة ***
            </div>
        @endif
        {{-- === التعديل الجديد ينتهي هنا === --}}
        
    </div>
    <div class="divider"></div>
    <table>
        <tr>
            <td>رقم الفاتورة:</td>
            <td class="bold">#{{ $order->daily_serial }}</td>
        </tr>
        <tr>
            <td>الفترة :</td>
            <td class="bold">{{ $order->shift_type_id == 2 ? "صباحي" : "مسائي" }}</td>
        </tr>
        <tr>
            <td>التاريخ:</td>
            <td class="bold">{{ $order->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <td>نوع الطلب:</td>
            <td class="bold">@if($order->type == 'dine_in') صالة @elseif($order->type == 'take_away') تيك أواي @else دليفري @endif</td>
        </tr>
        <tr>
            <td>الكاشير:</td>
            <td class="bold">{{ $order->cashier->name ?? 'غير معرف' }}</td>
        </tr>
    </table>
@if($order->type === 'delivery' && $order->customer)
    <div class="divider"></div>

    <table>
        <tr>
            <td>العميل:</td>
            <td class="bold">{{ $order->customer->name ?? '-' }}</td>
        </tr>

        <tr>
            <td>هاتف:</td>
            <td class="bold">{{ $order->customer->phone ?? '-' }}</td>
        </tr>

        @if(!empty($order->customer->region))
        <tr>
            <td>المنطقة:</td>
            <td class="bold">{{ $order->customer->region }}</td>
        </tr>
        @endif

        <tr>
            <td>العنوان:</td>
            <td class="bold">
                {{ $order->customer->street ?? '' }}
                {{ $order->customer->building_number ? ' - عمارة ' . $order->customer->building_number : '' }}
                {{ $order->customer->floor ? ' - دور ' . $order->customer->floor : '' }}
                {{ $order->customer->apartment ? ' - شقة ' . $order->customer->apartment : '' }}
            </td>
        </tr>

        @if(!empty($order->customer->landmark))
        <tr>
            <td>علامة مميزة:</td>
            <td class="bold">{{ $order->customer->landmark }}</td>
        </tr>
        @endif

        @if(!empty($order->customer->notes))
        <tr>
            <td>ملاحظات:</td>
            <td class="bold">{{ $order->customer->notes }}</td>
        </tr>
        @endif
    </table>
@endif

    <div class="divider"></div>
    <table class="items">
        <thead>
            <tr>
                <th style="width:55%;text-align:right;">الصنف</th>
                <th class="qty">الكمية</th>
                <th class="price">السعر</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $it)
            <tr>
                <td>{{ $it->product->name ?? $it->name ?? 'صنف' }}</td>
                <td class="qty">{{ $it->quantity }}</td>
                <td class="price">{{ number_format($it->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="divider"></div>
    <table>
        <tr class="total-row">
            <td>الإجمالي</td>
            <td>{{ number_format($order->total, 2) }} ج.م</td>
        </tr>
        @if($order->type == 'delivery')
        <tr class="total-row">
            <td>خدمة التوصيل</td>
            <td>15.00 ج.م</td>
        </tr>
        <tr class="total-row">
            <td>الإجمالي النهائي</td>
            <td>{{ number_format($order->total + 15, 2) }} ج.م</td>
        </tr>
        @else
        <tr class="total-row">
            <td>الصافي</td>
            <td>{{ number_format($order->total, 2) }} ج.م</td>
        </tr>
        @endif
    </table>
    <div class="divider"></div>
    <div class="center">
    <img  src="{{ asset('assets/images/icon/qr-code-FB.jpeg') }}" alt="QR-code" style="width:150px; height: auto;">
    </div>
    <div style="font-size: 18px;" class="footer">رضائكم يسرنا، ورأيك يطورنا ...❤<br><span style="font-size: 15px;"> Smart POS • 0111-5959773 </span></div>
</div>
</body>
</html>
