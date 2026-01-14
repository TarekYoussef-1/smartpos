@extends('layouts.master')

@section('content')
<style>
    .invoice-table tr.table-active td {
    background-color:#393939 !important;
    font-weight: bold;
    color: #fff;
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }
    .invoice-table-container {
        max-height: 400px;
        /* ارتفاع ثابت */
        overflow-y: auto;
        /* Scroll رأسي */
    }

    .invoice-table-container table {
        width: 100%;
    }

    .invoice-table-container {
        max-height: 400px;
        /* ارتفاع ثابت للعرض 10 صفوف تقريبًا */
        overflow-y: auto;
        /* Scroll رأسي */
        position: relative;
    }

    .invoice-table-container table tfoot tr {
        position: sticky;
        bottom: 0;
        background: #343a40;
        /* لون الخلفية مثل table-dark */
        color: #fff;
        z-index: 1;
        /* للتأكد أنه فوق الصفوف */
    }
</style>

<div class="container-fluid p-3">
    
    {{-- ✅ بيانات العميل (تظهر فقط في حالة الديلفري) --}}
    <div id="deliveryInfo" class="card p-3 mb-3" style="display:none;">
        <h5 class="mb-3"><i class="fas fa-user"></i> بيانات العميل</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>اسم العميل</label>
                <input type="text" class="form-control" id="customerName" placeholder="الاسم">
            </div>
            <div class="col-md-4 mb-2">
                <label>رقم الهاتف</label>
                <input type="text" class="form-control" id="customerPhone" placeholder="0123...">
            </div>
            <div class="col-md-4 mb-2">
                <label>العنوان</label>
                <input type="text" class="form-control" id="customerAddress" placeholder="العنوان">
            </div>
        </div>
    </div>

    {{-- ✅ منطقة الكاشير --}}
    <div class="row">
        <!-- يمين: جدول الفاتورة + لوحة التحكم -->
        <div class="col-md-4 col-12 mb-3">

            {{-- ✅ جدول الفاتورة --}}
            <div class="invoice-table-container card p-2 shadow-sm">
                <table class="table table-bordered table-sm table-striped invoice-table mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:40px;">م</th>
                            <th style="width:35%;">الصنف</th>
                            <th style="width:150px;">الكمية</th>
                            <th style="width:80px;">السعر</th>
                            <th style="width:90px;">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceBody" style="height:400px; overflow-y:auto;">
                        @for ($i = 1; $i <= 9; $i++)
                            <tr>
                            <td>{{ $i }}</td>
                            <td></td> <!-- اسم الصنف -->
                            <td class="text-center quantity-cell">
                            </td>
                            <td></td> <!-- السعر -->
                            <td class="item-total"></td>

                            </tr>
                            @endfor
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="4" class="text-end">إجمالي الفاتورة</th>
                            <th id="invoiceTotal">0.00</th>
                            
                        </tr>
                    </tfoot>
                </table>

            </div>


            {{-- ✅ لوحة الأزرار ولوحة الأرقام --}}
            <div class="d-flex flex-row justify-content-between align-items-start mt-3">

                <!-- العمود الأول: الأزرار الأربعة -->
                <div class="d-flex flex-column" style="width: 180px;">
                    <button class="btn btn-success mb-2 py-3 fw-bold" id="newInvoiceBtn">
                        <i class="fas fa-file"></i> فاتورة جديدة
                    </button>
                    <button class="btn btn-primary mb-2 py-3 fw-bold" id="printInvoiceBtn">
                        <i class="fas fa-print"></i> طباعة الفاتورة
                    </button>
                    <button class="btn btn-danger mb-2 py-3 fw-bold" id="deleteInvoiceBtn">
                        <i class="fas fa-trash-alt"></i> حذف الفاتورة
                    </button>
                    <button class="btn btn-secondary py-3 fw-bold" disabled>فراغ</button>
                </div>

                <!-- العمود الثاني: لوحة الأرقام -->
                <div class="keypad-grid ms-3"
                    style="display: grid; grid-template-columns: repeat(3, 80px); gap: 8px;">

                    <!-- أزرار الأرقام 1 إلى 9 -->
                    @for ($i = 1; $i <= 9; $i++)
                        <button class="btn btn-outline-dark py-3 fw-bold fs-4"
                        onclick="handleKeypad('{{ $i }}')">{{ $i }}</button>
                        @endfor

                        <!-- زر المسح (Backspace) -->
                        <button class="btn btn-dark py-3 fw-bold fs-4 d-flex justify-content-center align-items-center"
                            onclick="handleBackspace()">
                            <i class="fas fa-backspace"></i>
                        </button>

                        <!-- زر 0 -->
                        <button class="btn btn-outline-dark py-3 fw-bold fs-4"
                            onclick="handleKeypad('0')">0</button>

                        <!-- الزر الإضافي للنقطة -->
                        <button class="btn btn-outline-dark py-3 fw-bold fs-4"
                            onclick="handleKeypad('.')">.</button>
                </div>
            </div>
        </div>

        <!-- يسار: الفئات والأصناف -->
        <div class="col-md-8 col-12">
            <div class="row">
                <!-- ✅ الفئات -->
                <div class="col-md-3 col-12 mb-3">
                    <div id="categoryButtons" class="d-flex flex-column">
                        @foreach ($categories as $category)
                        <button class="btn btn-dark mb-2 category-btn"
                            data-id="{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- ✅ الأصناف -->
                <div class="col-md-9 col-12">
                    <div id="itemsArea" class="d-flex flex-wrap">
                        <div class="alert alert-info text-center w-100">
                            اختر قسم لعرض الأصناف التابعة له
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ✅ لوحة الأرقام
    let keypadInput = '';

   function handleKeypad(value) {
    if (!activeQtyInput) return;

    let current = activeQtyInput.value || '1';

    // منع أي شيء غير الأرقام
    if (!/[0-9]/.test(value)) return;

    if (current === '1' && value !== '0') {
        current = value;
    } else {
        current += value;
    }

    activeQtyInput.value = current;
    activeQtyInput.dispatchEvent(new Event('input'));
}

function handleBackspace() {
    if (!activeQtyInput) return;

    let current = activeQtyInput.value || '1';
    current = current.slice(0, -1);
    if (!current || current === '0') current = '1';

    activeQtyInput.value = current;
    activeQtyInput.dispatchEvent(new Event('input'));
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.qty-input') && !e.target.closest('.keypad-grid')) {
        activeQtyInput = null;
        updateDisplay();
    }
});

    function updateDisplay() {
        const inputField = document.getElementById('posInput');
        if (inputField) inputField.value = keypadInput;
    }

    // ✅ دالة مؤقتة لإضافة صنف للفاتورة
function addToInvoice(id, name, price) {
    const invoiceBody = document.getElementById('invoiceBody');
    if (!invoiceBody) return;

    let row = null;
    for (let r of invoiceBody.rows) {
        if (!r.cells[1].textContent.trim()) {
            row = r;
            break;
        }
    }

    if (!row) {
        const serial = invoiceBody.rows.length + 1;
        row = invoiceBody.insertRow();
        row.innerHTML = `
            <td class="text-center">${serial}</td>
            <td class="text-end"></td>
            <td class="text-center quantity-cell"></td>
            <td class="text-center"></td>
            <td class="item-total text-center">0.00</td>
        `;
    }

    row.cells[1].textContent = name;
    row.cells[3].textContent = price.toFixed(2);

    row.cells[2].innerHTML = `
        <div class="d-flex align-items-center justify-content-center gap-1">
            <button class="btn btn-sm btn-success qty-plus">+</button>
            <input type="number" class="form-control form-control-sm text-center qty-input" 
                   value="1" min="1" step="1" style="width:60px;">
            <button class="btn btn-sm btn-danger qty-minus">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    `;

    const qtyInput = row.querySelector('.qty-input');
    const plusBtn = row.querySelector('.qty-plus');
    const minusBtn = row.querySelector('.qty-minus');

    // تحديد الصف عند النقر على الـ input
    qtyInput.onclick = (e) => {
        e.stopPropagation();
        activeQtyInput = qtyInput;
        setActiveRow(row);
        updateDisplay();
    };

    const updateItemTotal = () => {
        let qty = parseInt(qtyInput.value) || 1;
        if (qty < 1) qty = 1;
        qtyInput.value = qty;

        const total = qty * price;
        row.cells[4].textContent = total.toFixed(2);

        minusBtn.innerHTML = qty <= 1 
            ? `<i class="fas fa-trash"></i>` 
            : `<i class="fas fa-minus"></i>`;

        updateInvoiceTotal();
        if (activeQtyInput === qtyInput) updateDisplay();
    };

    plusBtn.onclick = () => {
        let qty = parseInt(qtyInput.value) || 1;
        qty += 1;
        qtyInput.value = qty;
        updateItemTotal();
    };

    minusBtn.onclick = () => {
        let qty = parseInt(qtyInput.value) || 1;
        if (qty > 1) {
            qty -= 1;
        } else {
            row.remove();
            updateSerialNumbers();
            if (activeQtyInput === qtyInput) {
                activeQtyInput = null;
                setActiveRow(null);
            }
            updateInvoiceTotal();
            return;
        }
        qtyInput.value = qty;
        updateItemTotal();
    };

    qtyInput.oninput = () => {
        let val = qtyInput.value.replace(/[^0-9]/g, '');
        if (val === '' || parseInt(val) < 1) val = '1';
        qtyInput.value = val;
        updateItemTotal();
    };

    qtyInput.onblur = () => {
        if (!qtyInput.value || parseInt(qtyInput.value) < 1) {
            qtyInput.value = '1';
        }
        updateItemTotal();
    };

    updateItemTotal();
    updateSerialNumbers();
}


    function updateInvoiceTotal() {
        const invoiceBody = document.getElementById('invoiceBody');
        const invoiceTotal = document.getElementById('invoiceTotal');
        let total = 0;

        invoiceBody.querySelectorAll('.item-total').forEach(cell => {
            total += parseFloat(cell.textContent) || 0;
        });

        invoiceTotal.textContent = total.toFixed(2);
    }


    // ✅ تحميل الأصناف حسب القسم
    function initCategoryEvents() {
        const buttons = document.querySelectorAll(".category-btn");
        const itemsArea = document.getElementById("itemsArea");

        if (!buttons.length || !itemsArea) return;

        buttons.forEach(btn => {
            btn.addEventListener("click", function() {
                const categoryId = this.getAttribute("data-id");

                // تمييز القسم المختار
                buttons.forEach(b => b.classList.remove("btn-primary"));
                this.classList.add("btn-primary");

                // طلب الأصناف من السيرفر
                fetch(`{{ url('pos/items') }}/${categoryId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.length) {
                            itemsArea.innerHTML = `
                            <div class="alert alert-warning text-center w-100">
                                لا توجد أصناف في هذا القسم
                            </div>`;
                            return;
                        }

                        let html = `<div class="row g-2">`;
                        data.forEach(item => {
                            const img = item.image ? `/storage/${item.image}` : '/assets/images/no-image.png';
                            html += `
                        <div class="col-md-6 col-6">
                            <div class="card text-center p-2 shadow-sm" style="cursor:pointer;">
                                <img src="${img}" class="img-fluid rounded mb-2"
                                    alt="${item.name}" style="height:80px;object-fit:cover;">
                                <h6 class="fw-bold mb-1">${item.name}</h6>
                                <p class="text-muted mb-1 small">${item.price} ج</p>
                                <button class="btn btn-sm btn-success fw-bold"
                                        onclick="addToInvoice(${item.id}, '${item.name}', ${item.price})">
                                    <i class="fas fa-plus"></i> إضافة
                                </button>
                            </div>
                        </div>`;
                        });
                        html += `</div>`;
                        itemsArea.innerHTML = html;
                    })
                    .catch(() => {
                        itemsArea.innerHTML = `
                        <div class="alert alert-danger text-center w-100">
                            حدث خطأ أثناء تحميل الأصناف
                        </div>`;
                    });
            });
        });
    }

    let activeQtyInput = null;
let activeRow = null;

function setActiveRow(row) {
    document.querySelectorAll('#invoiceBody tr').forEach(r => {
        r.classList.remove('table-active');
    });
    if (row) {
        row.classList.add('table-active');
        activeRow = row;
    } else {
        activeRow = null;
    }
}

function updateDisplay() {
    const posInput = document.getElementById('posInput');
    if (posInput) {
        posInput.value = activeQtyInput ? activeQtyInput.value : '';
    }
}

// إلغاء التحديد عند النقر خارج
document.addEventListener('click', (e) => {
    if (!e.target.closest('.qty-input') && !e.target.closest('.keypad-grid')) {
        activeQtyInput = null;
        setActiveRow(null);
        updateDisplay();
    }
});
</script>
@endsection