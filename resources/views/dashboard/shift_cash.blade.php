@extends('layouts.master')

@section('content')

<div class="container mt-4">
    <!-- زر العودة -->
    <div style="margin-top: 40px;" class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ url('/dashboard') }}" class="btn btn-outline-danger">
            <i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم
        </a>
    </div>

    {{-- =======================
        بيانات الشيفت
    ======================== --}}
    <div class="card p-3 mb-4" style="background:#f6faff;">
        <h4 class="mb-3">مراجعة شيفت رقم: {{ $shift->id }}</h4>
        
        <table class="table table-borderless">
            <tr><th>الكاشير:</th> <td>{{ $shift->cashier_name }}</td></tr>
            <tr><th>من:</th> <td>{{ $shift->opened_at }}</td></tr>
            <tr><th>إلى:</th> <td>{{ $shift->closed_at }}</td></tr>
            <tr><th>عدد الطلبات:</th> <td>{{ $orderCount }}</td></tr>
            <tr>
                <th>إجمالي مبيعات النظام:</th> 
                <td style="color:blue;font-size:18px;">
                    {{ number_format($paidTotal,2) }} جم
                </td>
            </tr>
        </table>
    </div>


    
    {{-- =======================
        جدول الفئات
    ======================== --}}
    <form id="cashForm" method="POST" action="{{ route('shift.cash.save',$shift->id) }}">
        @csrf

        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>الفئة</th>
                    <th>العدد</th>
                    <th>القيمة</th>
                </tr>
            </thead>

            <tbody id="cashTable">

                @foreach([200,100,50,20,10,5,1] as $d)
                <tr>
                    <td style="font-size:18px;">{{ $d }} جم</td>

                    <td style="width:140px;">
                        <input type="number" 
                               name="denom[{{ $d }}]" 
                               value="0" 
                               class="form-control text-center qty_input" 
                               min="0"
                               step="1"
                               style="font-size:18px;">
                    </td>

                    <td class="result_cell" style="font-size:18px;">
                        0.00
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>


        {{-- =======================
            النتائج
        ======================== --}}
        <div class="card mt-4 p-4" style="background:#eef7ff;">
            
            <h3 class="mb-3">ملخص النقدية</h3>

            <h4>
                إجمالي النقدية:
                <span id="cashTotal" style="color:green;font-weight:bold;">
                    0.00
                </span>
                جم
            </h4>

            <h4>
                الفرق (نقدية - نظام):
                <span id="cashDiff" 
                      style="font-weight:bold;font-size:22px;">
                    0.00
                </span>
                جم
            </h4>

            <input type="hidden" name="final_cash" id="final_cash_input">
            <input type="hidden" name="final_diff" id="final_diff_input">

            <button type="submit" 
                    class="btn btn-primary mt-3"
                    style="width:260px;height:55px;font-size:20px;">
                ✔ حفظ بيانات الشيفت
            </button>

            <button type="button" 
        onclick="window.open('{{ route('shift.cash.print',$shift->id) }}','_blank')"
        class="btn btn-dark mt-3 ms-2"
        style="width:200px;height:55px;font-size:19px;">
    🖨 طباعة
</button>
        </div>

    </form>

</div>



{{-- ===========================
    طباعة 80mm
=========================== --}}
<style>
@media print {
    @page { size: 80mm auto; margin:0; }
    body{ width:78mm; margin:0; font-size:14px; }

    table {
        width:100%;
        border-collapse:collapse;
    }

    .btn, .card, input, .container {
        display:none !important;
    }

    #cashForm, h4, h3, table {
        display:block !important;
    }
}
</style>




{{-- =======================
    JavaScript حساب تلقائي
======================= --}}
<script>
document.querySelectorAll(".qty_input").forEach(function (input) {
    input.addEventListener("input", calcCash);
});

function calcCash(){
    let total = 0;

    document.querySelectorAll("#cashTable tr").forEach(function (row) {

        let qty = parseFloat(row.querySelector("input").value);
        let denom = parseFloat(
            row.querySelector("input").name.replace("denom[","").replace("]","")
        );

        let sum = qty * denom;

        if(!isNaN(sum)) total += sum;

        row.querySelector(".result_cell").innerText = sum.toFixed(2);
    });

    document.getElementById("cashTotal").innerText = total.toFixed(2);

    let diff = (total - {{ $paidTotal }}).toFixed(2);

    document.getElementById("final_cash_input").value = total;
    document.getElementById("final_diff_input").value = diff;

    let diffBox = document.getElementById("cashDiff");
    diffBox.innerText = diff;

    // لون الفرق
    if(diff < 0){
        diffBox.style.color = "red"; // عجز
    }else if(diff > 0){
        diffBox.style.color = "green"; // زيادة
    }else{
        diffBox.style.color = "blue"; // متزن
    }
}
</script>

@endsection
