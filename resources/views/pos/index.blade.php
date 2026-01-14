<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    @isset($order)
    <meta name="order-data" content="{{ $order->toJson() }}">
    <meta name="editing-order-id" content="{{ $order->id }}">
    @endisset
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title Page-->
    <title>Smart POS</title>

    <!-- Fontfaces CSS-->
    <link href="{{ asset('assets/css/font-face.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('assets/vendor/fontawesome-7.0.1/css/all.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('assets/vendor/mdi-font/css/material-design-iconic-font.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('assets/css/pos.css') }}" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="{{ asset('assets/vendor/bootstrap-5.3.8.min.css') }}" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="{{ asset('assets/css/aos.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('assets/vendor/css-hamburgers/hamburgers.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('assets/css/swiper-bundle-11.2.10.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.css') }}" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet" media="all">
</head>
<style>
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
        font-family: 'Cairo', sans-serif;
    }
    .container-fluid {
        position: relative;
        left: 20px ;
        width: 107%;
    }
    .page-wrapper {
        height: 100vh;
        width: 100vw;
        max-width: 1920px;
        max-height: 1080px;
        margin: 0 auto;
        background: #111;
        color: white;
    }

    /* الأزرار الثلاثة في الهيدر */
    .pos-mode-btn {
        padding: 12px 24px;
        transition: all 0.3s ease;
        color: white !important;
        font-weight: bold;
        background: transparent;
        border: 2px solid transparent;
        /* عشان ما يتحركش لما نضيف بوردر */
    }

    /* لما يكون الزر مفعّل */
    .pos-mode-btn.active {
        background: #ff8c00 !important;
        /* برتقالي */
        color: black !important;
        border: 2px solid #ff8c00 !important;
        box-shadow: 0 4px 12px rgba(255, 140, 0, 0.5);
        position: relative;
        top: -9px
    }

    /* تأثير عند المرور بالماوس (اختياري بس حلو) */
    .pos-mode-btn:hover:not(.active) {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        /* لو عايز حواف خفيفة جدًا بس */
    }

    .col-md-6 {
        width: 135px;
    }

    .invoice-table tr.table-active td {
        background-color: #393939 !important;
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


<body class="animsition body_pos">

    <!-- ✅ Header Desktop (Navbar) -->
    <header class="header-desktop3 d-none d-lg-block">
        <div class="section__content section__content--p35">
            <div class="header3-wrap">

                <div class="header__logo">
                    <a href="#">
                        <img width="80px" src="{{ asset('assets/images/icon/DAGAGOO_logo.webp') }}" alt="DAGAGOO_logo" />
                    </a>
                </div>

                <div class="header__navbar d-flex align-items-center">
                    <ul class="list-unstyled d-flex align-items-center m-0">

                        <!-- Dine In -->
                        <li  style="left: 10%;">
                            <a href="#" onclick="showPOS('dineIn')" class="pos-mode-btn text-white fw-bold" id="dineInBtn">
                                <i class="fas fa-chair fa-lg"></i> Dine In
                            </a>
                        </li>

                        <!-- Take Away -->
                        <li style="left: 10%;">
                            <a href="#" onclick="showPOS('takeAway')" class="pos-mode-btn text-white fw-bold" id="takeAwayBtn">
                                <i class="fas fa-shopping-bag fa-lg"></i> Take Away
                            </a>
                        </li>

                        <!-- Delivery -->
                        <li style="left: 10%;">
                            <a href="#" onclick="showPOS('delivery')" class="pos-mode-btn text-white fw-bold" id="deliveryBtn">
                                <i class="fas fa-motorcycle fa-lg"></i> Delivery
                            </a>
                        </li>

                        <!-- Spacer -->




                        <li class="text-white fw-bold" style="position: relative;left: 168px; text-align: center;">
                            <!-- التاريخ -->
                            <div id="posDate" style="font-size:0.85rem;"></div>
                            <div id="posClock" style="font-size:1rem; font-weight:bold;"></div>
                        </li>

                        <li class="ms-auto text-white fw-bold position-relative"
                            style="direction: rtl; left: 20%; text-align: start; width: 200px;">

                            <div class="account-item clearfix js-item-menu">

                                <!-- اسم المستخدم (يفتح الدروب داون) -->
                                <div class="content">
                                    <a style="width:200px;" class="js-acc-btn text-white fw-bold" href="#">
                                        {{ session('user')->name ?? '' }}

                                    </a>
                                </div>

                                <!-- الدروب داون -->
                                <div class="account-dropdown js-dropdown" style="min-width: 200px;top: 74px;">

                                    <div class="info clearfix text-center p-2">
                                        <h5 class="name mb-1">
                                            {{ session('user')->name ?? 'ضيف' }}
                                        </h5>
                                        <h5 class="email fw-bold">
                                            شيفت: {{ session('shift_type')->name ?? '' }}
                                        </h5>
                                    </div>

                                    <div class="account-dropdown__footer p-0">
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button class="btn btn-danger w-100 fw-bold rounded-0">
                                                <i class="zmdi zmdi-power"></i> تسجيل الخروج
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>

                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div class="page-wrapper">

        <!-- ✅ Page Container -->
        <div class="page-container">

            <!-- ✅ Main Content -->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">


                        <div class="container-fluid p-3">

                            {{-- ✅ بيانات العميل  (دليفري فقط) --}}
                            <div id="deliveryInfo" class="card border-0 shadow-sm p-4 mb-4" style="display:none; border-radius:12px;width:95%;height: 100%; ">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0"><i class="fas fa-truck text-primary"></i> بيانات التوصيل</h5>
                                    <button type="button" id="editCustomerBtn" class="btn btn-warning btn-sm d-none">
                                        <i class="fas fa-edit"></i> تعديل البيانات
                                    </button>
                                </div>

                                <form id="customerForm">
                                    <div class="row g-3">
                                        <!-- رقم الهاتف + أيقونة البحث -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">رقم الهاتف <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="customerPhone" placeholder="01xxxxxxxxx" required>
                                                <button class="btn btn-outline-secondary" type="button" id="searchPhoneBtn">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label fw-bold">اسم العميل</label>
                                            <input type="text" class="form-control" id="customerName" placeholder="اسم العميل">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">المنطقة</label>
                                            <input type="text" class="form-control" id="customerArea" placeholder=" اسم المنطقة">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">الشارع</label>
                                            <input type="text" class="form-control" id="customerStreet" placeholder="اسم الشارع">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">رقم العقار / المبنى</label>
                                            <input type="text" class="form-control" id="customerBuilding" placeholder="مثل: 15">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">الدور</label>
                                            <input type="text" class="form-control" id="customerFloor" placeholder="مثل: الدور الثالث">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">رقم الشقة</label>
                                            <input type="text" class="form-control" id="customerApartment" placeholder="مثل: 12">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">علامة مميزة</label>
                                            <input type="text" class="form-control" id="customerLandmark" placeholder="مثل: بجوار الصيدلية">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-bold">ملاحظات إضافية</label>
                                            <textarea class="form-control" id="customerNotes" rows="2" placeholder="تعليمات خاصة بالتوصيل..."></textarea>
                                        </div>

                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" id="saveCustomerBtn" class="btn btn-success btn-lg px-5">
                                                <i class="fas fa-save"></i> حفظ بيانات العميل
                                            </button>
                                            <button type="button" id="placeOrderBtn" class="btn btn-primary btn-lg px-5 d-none">
                                                <i class="fas fa-shopping-cart"></i> اطلب الآن
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>


                            {{-- ✅ منطقة الكاشير --}}
                            <div class="row">
                                <!-- يمين: جدول الفاتورة + لوحة التحكم -->
                                <div class="col-md-5 col-xl-5 col-12 mb-3">

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
                                        <div class="d-flex flex-column" style="width: 135px;">
                                            <button class="btn btn-primary mb-2 py-3 fw-bold" id="printInvoiceBtn">
                                                <i class="fas fa-print"></i> طباعة الفاتورة
                                            </button>
                                            <button class="btn btn-success mb-2 py-3 fw-bold" id="newInvoiceBtn">
                                                <i class="fas fa-file"></i> فاتورة جديدة
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
                                <div class="col-md-7 col-12">
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Copyright © {{ date('Y') }}
                                        <a href="https://wa.me/+201127535355" target="_blank" rel="noopener">Tarek Youssef</a>. All rights reserved.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

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
            row.dataset.productId = id;
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

                minusBtn.innerHTML = qty <= 1 ?
                    `<i class="fas fa-trash"></i>` :
                    `<i class="fas fa-minus"></i>`;

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

        function updateSerialNumbers() {
            const rows = document.querySelectorAll('#invoiceBody tr');
            let index = 1;

            rows.forEach(row => {
                row.cells[0].textContent = index++;
            });
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
                    fetch("{{ url('pos/items') }}/" + categoryId)
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
                                // تعديل مسار الصورة هنا
                                const img = item.image ? `assets/images/products/${item.image}` : 'assets/images/no-image.png';
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

        // تعديل الكود التالي:
        document.getElementById('placeOrderBtn').addEventListener('click', function() {
            // إخفاء نموذج العميل بالكامل
            const customerSection = document.getElementById('deliveryInfo');
            if (customerSection) {
                customerSection.style.display = 'none';
            }

           // alert("تم تسجيل العميل، يمكنك الآن اختيار الأصناف");
        });


        // ✅ التحكم في عرض أقسام POS
        function showPOS(type) {
            const deliveryInfo = document.getElementById('deliveryInfo');

            // إزالة الـ active من كل الأزرار
            document.querySelectorAll('.pos-mode-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // تفعيل الزر المناسب
            const activeBtn = document.getElementById(type + 'Btn');
            if (activeBtn) {
                activeBtn.classList.add('active');
            }

            // التحكم في عرض بيانات العميل
            if (deliveryInfo) {
                if (type === 'delivery') {
                    deliveryInfo.style.display = 'block';
                    // لا نعمل reset للعميل
                } else {
                    deliveryInfo.style.display = 'none';
                    // عند الخروج من الدليفري، لا نمسح البيانات
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            showPOS('dineIn');
            updateClock();
            initCategoryEvents();
            loadDefaultCategory();

            // قراءة البيانات من وسوم meta (الطريقة الجديدة)
            const orderDataMeta = document.querySelector('meta[name="order-data"]');
            const editingIdMeta = document.querySelector('meta[name="editing-order-id"]');

            if (orderDataMeta && editingIdMeta) {
                window.orderData = JSON.parse(orderDataMeta.getAttribute('content'));
                window.editingOrderId = parseInt(editingIdMeta.getAttribute('content'));
            } else {
                window.orderData = null;
                window.editingOrderId = null;
            }

            // التحقق مما إذا كنا في وضع التعديل
            if (window.orderData) {
                editingOrderId = window.editingOrderId;
                const orderData = window.orderData;

                // ✅ حل مشكلة الـ Navbar: تحويل نوع الطلب من قاعدة البيانات إلى معرف الزر
                let buttonType = orderData.type;
                if (buttonType === 'dine_in') buttonType = 'dineIn';
                if (buttonType === 'take_away') buttonType = 'takeAway';

                // تعيين نوع الطلب مع الزر الصحيح
                showPOS(buttonType);

                // إذا كان دليفري، قم بملء بيانات العميل
                if (orderData.type === 'delivery' && orderData.customer) {
                    const c = orderData.customer;
                    document.getElementById('customerPhone').value = c.phone || '';
                    document.getElementById('customerName').value = c.name || '';
                    document.getElementById('customerArea').value = c.region || '';
                    document.getElementById('customerStreet').value = c.street || '';
                    document.getElementById('customerBuilding').value = c.building_number || '';
                    document.getElementById('customerFloor').value = c.floor || '';
                    document.getElementById('customerApartment').value = c.apartment || '';
                    document.getElementById('customerLandmark').value = c.landmark || '';
                    document.getElementById('customerNotes').value = c.notes || '';
                    lockCustomerForm(true);
                }

                // تحميل أصناف الطلب في الفاتورة
                if (orderData.items && orderData.items.length > 0) {
                    orderData.items.forEach(item => {
                        if (item.product) {
                            // ✅ حل مشكلة السعر والكمية: التعامل مع القيم كأرقام
                            const price = parseFloat(item.product.price);
                            const quantity = parseInt(item.quantity);

                            if (!isNaN(price) && !isNaN(quantity)) {
                                const addedRow = addToInvoice(item.product_id, item.product.name, price);

                                if (addedRow) {
                                    const qtyInput = addedRow.querySelector('.qty-input');
                                    if (qtyInput) {
                                        // تعيين الكمية مباشرة
                                        qtyInput.value = quantity;

                                        // ✅ التحديث المباشر للإجماليات بدلاً من الاعتماد على الأحداث
                                        const total = quantity * price;
                                        addedRow.cells[4].textContent = total.toFixed(2); // تحديث إجمالي الصنف

                                        // تحديث أيقونة زر الحذف/النقصان
                                        const minusBtn = addedRow.querySelector('.qty-minus');
                                        if (minusBtn) {
                                            minusBtn.innerHTML = quantity <= 1 ? `<i class="fas fa-trash"></i>` : `<i class="fas fa-minus"></i>`;
                                        }

                                        // تحديث الإجمالي الكلي للفاتورة
                                        updateInvoiceTotal();
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });


        //✅ تحميل الأصناف للقسم رقم 1 تلقائيًا
        function loadDefaultCategory() {
            const categoryBtn1 = document.querySelector('.category-btn[data-id="1"]');
            if (categoryBtn1) {
                // إزالة تمييز من باقي الأزرار
                document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('btn-primary'));
                // تمييز القسم 1
                categoryBtn1.classList.add('btn-primary');
                // استدعاء fetch للأصناف للقسم 1
                categoryBtn1.click();
            } else {
                // إذا لم يوجد قسم بالمعرف 1، اختر أول قسم متاح
                const firstCategory = document.querySelector('.category-btn');
                if (firstCategory) {
                    firstCategory.click();
                }
            }
        }

        // ✅ تحديث الساعة والتاريخ
        function updateClock() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('posDate').textContent = now.toLocaleDateString('ar-EG', options);
            document.getElementById('posClock').textContent = now.toLocaleTimeString('ar-EG');
        }
        setInterval(updateClock, 1000);

        // ✅ نظام العملاء المتكامل للدليفري
        let currentCustomerId = null;

        function lockCustomerForm(locked = true) {
            const inputs = document.querySelectorAll('#customerForm input, #customerForm textarea');
            inputs.forEach(input => input.disabled = locked);
            document.getElementById('searchPhoneBtn').disabled = locked;
            document.getElementById('saveCustomerBtn').classList.toggle('d-none', locked);
            document.getElementById('placeOrderBtn').classList.toggle('d-none', !locked);
            document.getElementById('editCustomerBtn').classList.toggle('d-none', !locked);
        }

        function clearCustomerForm() {
            document.getElementById('customerForm').reset();
            currentCustomerId = null;
            lockCustomerForm(false);
        }

        // البحث عن العميل - النسخة اللي شغالة 100000% مع الروت بتاعك
        document.getElementById('searchPhoneBtn').addEventListener('click', function() {
            const phone = document.getElementById('customerPhone').value.trim().replace(/[^0-9]/g, '');
            if (phone.length < 10) {
                alert('ادخل رقم هاتف صحيح (10 أرقام على الأقل)');
                return;
            }

            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            // الحل السحري: استخدام route() مع Blade + replace
            const url = "{{ route('pos.customer.search', ':phone') }}".replace(':phone', phone);

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    console.log('تم جلب العميل:', data);

                    if (data.success && data.customer) {
                        const c = data.customer;
                        currentCustomerId = c.id;

                        document.getElementById('customerName').value = c.name || '';
                        document.getElementById('customerStreet').value = c.street || '';
                        document.getElementById('customerFloor').value = c.floor || '';
                        document.getElementById('customerLandmark').value = c.landmark || '';
                        document.getElementById('customerNotes').value = c.notes || '';
                        document.getElementById('customerArea').value = c.region || '';
                        document.getElementById('customerBuilding').value = c.building_number || '';
                        document.getElementById('customerFloor').value = c.floor || '';
                        document.getElementById('customerApartment').value = c.apartment || '';
                        document.getElementById('customerLandmark').value = c.landmark || '';
                        document.getElementById('customerNotes').value = c.notes || '';

                        lockCustomerForm(true);
                       // alert(`مرحبًا بعودتك يا ${c.name || 'العميل'}! جاهز للطلب`);
                    } else {
                        alert('عميل جديد - املأ البيانات وحفظها');
                        lockCustomerForm(false);
                    }
                })
                .catch(err => {
                    console.error('خطأ في البحث:', err);
                    alert('فشل البحث - تأكد من الرقم أو اتصل بالمبرمج');
                })
                .finally(() => {
                    this.innerHTML = '<i class="fas fa-search"></i>';
                    this.disabled = false;
                });
        });

        // تعديل بيانات العميل القديم
        document.getElementById('editCustomerBtn').addEventListener('click', function() {
            lockCustomerForm(false);
            document.getElementById('saveCustomerBtn').textContent = 'تحديث بيانات العميل';
        });

        // حفظ أو تحديث العميل
        // حفظ أو تحديث بيانات العميل (النسخة النهائية الشغالة 100%)
        document.getElementById('customerForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('name', document.getElementById('customerName').value.trim());
            formData.append('phone', document.getElementById('customerPhone').value.trim());
            formData.append('region', document.getElementById('customerArea').value.trim()); // ← region
            formData.append('street', document.getElementById('customerStreet').value.trim());
            formData.append('building_number', document.getElementById('customerBuilding').value.trim()); // ← نفس الاسم
            formData.append('floor', document.getElementById('customerFloor').value.trim());
            formData.append('apartment', document.getElementById('customerApartment').value.trim()); // ← نفس الاسم
            formData.append('landmark', document.getElementById('customerLandmark').value.trim());
            formData.append('notes', document.getElementById('customerNotes').value.trim());

            fetch("{{ route('pos.customer.store') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error('Server Error');
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        currentCustomerId = data.customer_id;
                        alert('تم حفظ بيانات العميل بنجاح');
                        lockCustomerForm(true);
                        document.getElementById('saveCustomerBtn').innerHTML = '<i class="fas fa-save"></i> حفظ بيانات العميل';
                    } else {
                        alert(data.message || 'حدث خطأ أثناء الحفظ');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('فشل في الاتصال بالسيرفر');
                });
        });

        // عند التبديل للدليفري → إعادة تهيئة النموذج
        function showPOS(type) {
            const deliveryInfo = document.getElementById('deliveryInfo');
            if (deliveryInfo) {
                deliveryInfo.style.display = (type === 'delivery') ? 'block' : 'none';
                if (type === 'delivery') {
                    // لا نعمل reset للعميل
                    deliveryInfo.style.display = 'block';
                } else {
                    deliveryInfo.style.display = 'none';
                }

            }

            // باقي كود تفعيل الأزرار البرتقالية (اللي عملناه قبل كده)
            document.querySelectorAll('.pos-mode-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(type + 'Btn')?.classList.add('active');
        }

        //==============

        document.getElementById("printInvoiceBtn").addEventListener("click", async function(e) {
            const btn = e.target.closest('button');
            const originalHTML = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ والطباعة...';
            btn.disabled = true;

            try {
                // جمع بيانات الفاتورة من الجدول
                const rows = document.querySelectorAll("#invoiceBody tr");
                let items = [];

                rows.forEach(r => {
                    const name = r.cells[1].textContent.trim();
                    const qtyInput = r.querySelector(".qty-input");
                    const priceCell = r.cells[3].textContent.trim();
                    const productId = r.dataset.productId ? parseInt(r.dataset.productId) : null;

                    if (name && qtyInput && priceCell) {
                        const qty = parseInt(qtyInput.value) || 1;
                        const price = parseFloat(priceCell);
                        items.push({
                            name,
                            product_id: productId,
                            quantity: qty,
                            price,
                            total: qty * price
                        });
                    }
                });

                if (!items.length) {
                    alert("لا توجد أصناف في الفاتورة!");
                    return;
                }

                let orderType = "dine_in";
                if (document.getElementById("takeAwayBtn").classList.contains("active")) orderType = "take_away";
                if (document.getElementById("deliveryBtn").classList.contains("active")) orderType = "delivery";

                const invoiceTotal = items.reduce((sum, item) => sum + item.total, 0).toFixed(2);

                const formData = new FormData();
                formData.append('type', orderType);
                formData.append('customer_id', currentCustomerId || '');
                formData.append('total', invoiceTotal);

                if (window.editingOrderId) formData.append('order_id', window.editingOrderId);

                items.forEach((item, index) => {
                    formData.append(`items[${index}][name]`, item.name);
                    formData.append(`items[${index}][product_id]`, item.product_id || '');
                    formData.append(`items[${index}][quantity]`, item.quantity);
                    formData.append(`items[${index}][price]`, item.price.toFixed(2));
                    formData.append(`items[${index}][total]`, item.total.toFixed(2));
                });

                const response = await fetch("{{ route('pos.order.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.logout) {
                    alert(data.message);
                    window.location.href = data.redirect;
                    return;
                }

                if (data.success) {
                    // ✅ هنا نرسل للطابعة مباشرة بدون فتح نافذة
                    // server-side بيعمل shell_exec لطباعة PDF
                    // alert("تتم حفظ الطلب وإرساله للطباعة بنجاح ✓");

                    // إعادة تهيئة الفاتورة
                    resetInvoice(orderType);
                    showPOS('dineIn');
                } else {
                    alert(data.message || "حدث خطأ أثناء حفظ الطلب أو الطباعة");
                }

            } catch (error) {
                console.error("خطأ أثناء عملية الطباعة:", error);
                alert("حدث خطأ في الاتصال بالخادم");
            } finally {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        });

        // ===== دالة Reset آمنة ترجع شكل الجدول زي البداية (9 صفوف فارغة) =====
        function resetInvoice(orderType = '') {
            const invoiceBody = document.getElementById('invoiceBody');
            if (!invoiceBody) return;

            // أعد بناء الـ tbody بنفس شكل الـ Blade — 9 صفوف فارغة جاهزة للاستخدام
            invoiceBody.innerHTML = '';
            for (let i = 1; i <= 9; i++) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
            <td>${i}</td>
            <td></td>
            <td class="text-center quantity-cell"></td>
            <td></td>
            <td class="item-total"></td>
        `;
                invoiceBody.appendChild(tr);
            }

            // إعادة ضبط الإجمالي
            const invoiceTotal = document.getElementById('invoiceTotal');
            if (invoiceTotal) invoiceTotal.textContent = '0.00';

            // إلغاء التحديد
            activeQtyInput = null;
            activeRow = null;
            setActiveRow(null);

            // إلغاء وضع التعديل
            editingOrderId = null;

            // لو الطلب كان دليفري يبقى نمسح بيانات العميل
            if (orderType === 'delivery') {
                clearCustomerForm();
            }

            // إعادة تحميل القسم الافتراضي (عشان الأزرار ترجع جاهزة)
            try {
                loadDefaultCategory();
            } catch (e) {
                /* إن لم تكن موجودة لا تفرق */
            }

            // لو فيه أي UI إضافي يحتاج إعادة ضبط ضيفه هنا
            console.log('Invoice reset safe done.');
        }

        // ===== ربط زر "فاتورة جديدة" بالدالة لضمان وجود عمل Reset حقيقي =====
        const newBtn = document.getElementById('newInvoiceBtn');
        if (newBtn) {
            newBtn.addEventListener('click', function() {
                // ما تمررش نوع الطلب هنا عشان ما تمسحش بيانات العميل لو مش مطلوب
                resetInvoice();
            });
        }
    </script>

    <!-- ✅ مكتبات الجافاسكربت -->
    <script src="{{ asset('assets/js/vanilla-utils.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-5.3.8.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chartjs/chart.umd.js-4.5.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap5-init.js') }}"></script>
    <script src="{{ asset('assets/js/main-vanilla.js') }}"></script>
    <script src="{{ asset('assets/js/swiper-bundle-11.2.10.min.js') }}"></script>
    <script src="{{ asset('assets/js/aos.js') }}"></script>
    <script src="{{ asset('assets/js/modern-plugins.js') }}"></script>
    </script>
</body>

</html>