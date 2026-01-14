@extends('layouts.master')

@section('title', 'إدارة الأصناف')

@section('content')
<div class="container-fluid p-3">
    <!-- زر العودة -->
    <div style="margin-top: 40px;" class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ url('/dashboard') }}" class="btn btn-outline-danger">
            <i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم
        </a>
    </div>
    <h4 class="mb-3">إدارة الأصناف</h4>

  <!-- نموذج إضافة صنف -->
<div class="mb-3">
    <form id="addProductForm" class="row g-2" enctype="multipart/form-data">
        <div class="col-md-3">
            <input type="text" name="name" id="productName" class="form-control" placeholder="اسم الصنف" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="price" id="productPrice" class="form-control" placeholder="السعر" min="0" required>
        </div>
        <div class="col-md-2">
            <select style="width:100%" name="department_id" id="productDepartment" class="form-select" required>
                <option value="">اختر القسم</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="file" name="image" id="productImage" class="form-control" accept="image/*">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">إضافة</button>
        </div>
    </form>
</div>

  <!-- صندوق الرسائل -->
<div id="productsMessage"></div>

<!-- بحث -->
<div  class="mb-2 d-flex">
    <input   id="productSearch" class="form-control " placeholder="ابحث عن صنف..." />
    <button id="clearSearch" class="btn btn-outline-secondary">مسح</button>
</div>

<!-- جدول داخل حاوية scroll -->
<div style="max-height: 420px; overflow-y: auto;">
    <table class="table table-bordered table-striped mb-0">
        <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
            <tr>
                <th style="width:40px">#</th>
                <th style="width:80px">الصورة</th>
                <th>الاسم</th>
                <th style="width:180px">القسم</th>
                <th style="width:100px">السعر</th>
                <th style="width:120px">تاريخ الإضافة</th>
                <th style="width:120px">الإجراءات</th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            <!-- الصفوف server-side يمكن تبقى هنا كـ fallback -->
            @foreach ($products as $index => $p)
                <tr data-id="{{ $p->id }}">
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if($p->image)
                            <img src="{{ asset('storage/products/' . $p->image) }}" alt="{{ $p->name }}" class="img-thumbnail" style="width: 50px; height: 50px;">
                        @else
                            <span class="text-muted">لا توجد صورة</span>
                        @endif
                    </td>
                    <td class="p-name">{{ $p->name }}</td>
                    <td class="p-dept" data-id="{{ $p->department_id }}">{{ $p->department->name ?? '-' }}</td>
                    <td class="p-price">{{ $p->price }}</td>
                    <td>{{ $p->created_at->format('Y-m-d') }}</td>
                    <td>
                        <!-- وحدنا الكلاسات هنا -->
                        <button class="btn btn-sm btn-outline-primary edit-row"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger delete-row"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const csrfToken = '{{ csrf_token() }}';
    const productsIndexUrl = "{{ route('products.index') }}";
    const productsStoreUrl = "{{ route('products.store') }}";
    const productsUpdateUrl = "{{ route('products.update', ':id') }}"; // مسار التعديل
    const productsDeleteUrl = "{{ route('products.destroy', ':id') }}"; // مسار الحذف

    // حماية XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    // رسائل
    function showMessage(msg, type = 'success') {
        const box = document.getElementById('productsMessage');
        box.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${escapeHtml(msg)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
        setTimeout(() => { if (box) box.innerHTML = ''; }, 4000);
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        let d = new Date(dateStr);
        return d.toISOString().split('T')[0];
    }

    // ====================================
    //   تحميل الأصناف مع دعم البحث q
    // ====================================
    let lastQuery = '';
    function fetchProducts(q = '') {
        lastQuery = q;
        const url = q ? `${productsIndexUrl}?q=${encodeURIComponent(q)}` : productsIndexUrl;

        fetch(url, {
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(res => {
            if (!res.ok) throw new Error(`فشل التحميل: ${res.statusText}`);
            return res.json();
        })
        .then(products => {
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = "";

            products.forEach((p, i) => {
                let tr = document.createElement('tr');
                tr.dataset.id = p.id;

               tr.innerHTML = `
    <td>${i + 1}</td>
    <td>
        ${p.image ? 
            `<img src="assets/images/products/${p.image}" alt="${escapeHtml(p.name)}" class="img-thumbnail" style="width: 50px; height: 50px;">` : 
            '<img src="assets/images/no-image.png" alt="No Image" class="img-thumbnail" style="width: 50px; height: 50px;">'
        }
    </td>
    <td class="p-name view">${escapeHtml(p.name)}</td>
    <td class="p-dept view" data-id="${p.department_id}">${escapeHtml(p.department?.name ?? "-")}</td>
    <td class="p-price view">${escapeHtml(String(p.price))}</td>
    <td>${formatDate(p.created_at)}</td>
    <td>
        <button class="btn btn-sm btn-outline-primary edit-row"><i class="fas fa-edit"></i></button>
        <button class="btn btn-sm btn-outline-danger delete-row"><i class="fas fa-trash"></i></button>
    </td>
`;

                tbody.appendChild(tr);
            });

            attachEvents();
        })
        .catch(error => {
            console.error('Fetch Products Error:', error);
            showMessage(error.message, "danger");
        });
    }

    fetchProducts();

    // ================
    //  إضافة صنف
    // ================
    document.getElementById('addProductForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('name', document.getElementById('productName').value.trim());
        formData.append('price', document.getElementById('productPrice').value.trim());
        formData.append('department_id', document.getElementById('productDepartment').value);
        
        const imageInput = document.getElementById('productImage');
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }

        fetch(productsStoreUrl, {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest"
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(errData => {
                    throw new Error(errData.errors ? Object.values(errData.errors)[0][0] : (errData.error || `فشل الإضافة: ${res.statusText}`));
                });
            }
            return res.json();
        })
        .then(data => {
            showMessage(data.success, 'success');
            this.reset();
            fetchProducts(lastQuery);
        })
        .catch(error => {
            console.error('Add Product Error:', error);
            showMessage(error.message, "danger");
        });
    });

    // ================
    //  ربط الأحداث بعد رسم الجدول
    // ================
    function attachEvents() {

        // تعديل صف
        document.querySelectorAll('.edit-row').forEach(btn => {
            btn.onclick = function () {
                const row = this.closest('tr');
                const id = row.dataset.id;

                const nameCell = row.querySelector('.p-name');
                const priceCell = row.querySelector('.p-price');
                const deptCell = row.querySelector('.p-dept');
                const imageCell = row.querySelector('td:nth-child(2)');

                const oldName = nameCell.textContent.trim();
                const oldPrice = priceCell.textContent.trim();
                const oldDeptId = deptCell.dataset.id ?? '';

                nameCell.innerHTML = `<input class="form-control form-control-sm" value="${escapeHtml(oldName)}">`;
                priceCell.innerHTML = `<input class="form-control form-control-sm" type="number" min="0" value="${escapeHtml(oldPrice)}">`;

                deptCell.innerHTML = `
                    <select class="form-select form-select-sm">
                        <option value="">اختر القسم</option>
                        @foreach ($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                `;
                const select = deptCell.querySelector('select');
                select.value = oldDeptId;

                imageCell.innerHTML = `
                    <input type="file" class="form-control form-control-sm" accept="image/*">
                    <small class="text-muted d-block mt-1">اتركه فارغاً إذا كنت لا تريد تغيير الصورة</small>
                `;

                const actions = row.querySelector('td:last-child');
                actions.innerHTML = `
                    <button class="btn btn-sm btn-success save-row"><i class="fas fa-check"></i></button>
                    <button class="btn btn-sm btn-secondary cancel-row"><i class="fas fa-times"></i></button>
                `;

                // حفظ
                actions.querySelector('.save-row').onclick = function () {
                    const formData = new FormData();
                    formData.append('name', nameCell.querySelector('input').value.trim());
                    formData.append('price', priceCell.querySelector('input').value.trim());
                    formData.append('department_id', select.value);
                    
                    const imageInput = imageCell.querySelector('input[type="file"]');
                    if (imageInput.files.length > 0) {
                        formData.append('image', imageInput.files[0]);
                    }
                    
                    const updateUrl = productsUpdateUrl.replace(':id', id);

                    fetch(updateUrl, {
                        method: "POST", // Using POST with _method=PUT for file upload
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        body: formData
                    })
                    .then(res => {
                        if (!res.ok) {
                            return res.json().then(errData => {
                                throw new Error(errData.errors ? Object.values(errData.errors)[0][0] : (errData.error || `فشل التحديث: ${res.statusText}`));
                            });
                        }
                        return res.json();
                    })
                    .then(data => {
                        showMessage(data.success, 'success');
                        fetchProducts(lastQuery);
                    })
                    .catch(error => {
                        console.error('Update Product Error:', error);
                        showMessage(error.message, "danger");
                    });
                };

                // إلغاء
                actions.querySelector('.cancel-row').onclick = () => fetchProducts(lastQuery);
            };
        });

        // حذف صف
        document.querySelectorAll('.delete-row').forEach(btn => {
            btn.onclick = function () {
                const row = this.closest('tr');
                const id = row.dataset.id;
                if (!confirm("هل تريد حذف هذا الصنف؟")) return;

                const deleteUrl = productsDeleteUrl.replace(':id', id);

                fetch(deleteUrl, {
                    method: "DELETE",
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(errData => {
                            throw new Error(errData.error || `فشل الحذف: ${res.statusText}`);
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    showMessage(data.success, 'success');
                    fetchProducts(lastQuery);
                })
                .catch(error => {
                    console.error('Delete Product Error:', error);
                    showMessage(error.message, "danger");
                });
            };
        });
    }

    // ============================
    //   بحث مع debounce (server-side)
    // ============================
    const searchInput = document.getElementById('productSearch');
    const clearBtn = document.getElementById('clearSearch');
    let debounceTimer = null;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchProducts(this.value.trim());
            }, 400);
        });
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            fetchProducts('');
        });
    }

});
</script>
@endpush
@endsection