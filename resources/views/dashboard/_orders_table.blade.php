<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
        <thead class="table-dark">
            <tr>
                <th>رقم الطلب</th>
                <th>التاريخ</th>
                <th>النوع</th>
                <th>العميل</th>
                <th>الإجمالي</th>
                <th>الحالة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <span class="badge bg-primary">
                        {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $order->type)) }}
                    </span>
                </td>
                <td>{{ $order->customer_name ?? 'عميل نقدي' }}</td>
                <td>{{ number_format($order->total, 2) }}</td>
                <td>
                    @if($order->status === 'paid')
                    <span class="badge bg-success">مدفوع</span>
                    @elseif($order->status === 'cancelled')
                    <span class="badge bg-danger">ملغي</span>
                    @else
                    <span class="badge bg-secondary">{{ $order->status }}</span>
                    @endif
                </td>
                <td class="text-center">
                    <a href="{{ route('pos.order.print', $order->id) }}"
                        class="btn btn-sm btn-info"
                        title="إعادة طباعة">
                        <i class="fas fa-print"></i>
                    </a>

                    <!-- <a href="{{ route('pos.edit', $order->id) }}" class="btn btn-sm btn-warning" title="تعديل في الكاشير">
                            <i class="fas fa-edit"></i>
                        </a> -->
                    @if($order->status !== 'cancelled')
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm btn-danger" title="إلغاء الطلب">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">لا توجد طلبات مطابقة للبحث</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>