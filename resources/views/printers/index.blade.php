@extends('layouts.master')

@section('title', 'إدارة الطابعات')

@section('content')
<div class="container-fluid p-3">
    <!-- زر العودة -->
    <div style="margin-top: 40px;" class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ url('/dashboard') }}" class="btn btn-outline-danger">
            <i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم
        </a>
    </div>

    <h4 class="mb-3">إدارة الطابعات</h4>

    <!-- صندوق الرسائل -->
    <div id="printersMessage"></div>

    <!-- ✅ نموذج إضافة طابعة جديد (بدلاً من المودال) -->
    <div class="card mb-4">
        <div class="card-header bg-dark ">
            <h5 class="mb-0 text-white"><i class="fas fa-plus"></i> إضافة طابعة جديدة</h5>
        </div>
        <div class="card-body">
            <form id="addPrinterForm" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label for="printer_name" class="form-label">اسم الطابعة</label>
                    <input placeholder="اسم البرنتر كما هو في السيرفر" type="text" class="form-control" id="printer_name" name="printer_name" required>
                </div>
                <div class="col-md-3">
                    <label for="printer_ip" class="form-label">عنوان IP</label>
                    <input type="text" class="form-control" id="printer_ip" name="printer_ip" placeholder="" required>
                </div>
                
                <!-- ✅ تعديل: إضافة حاوية لحقل القسم للتحكم في ظهوره -->
                <div class="col-md-2" id="departmentFieldContainer">
                    <label for="department_id" class="form-label">القسم</label><br> 
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">اختر القسم</option>
                        @foreach (\App\Models\Department::orderBy('name')->get() as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="type" class="form-label">النوع</label><br>  
                    <select class="form-select" id="type" name="type" required>
                        <option value="kitchen">مطبخ</option>
                        <option value="cashier">كاشير</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="active" name="active" checked>
                        <label class="form-check-label" for="active">نشطة</label>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-save"></i> حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول الطابعات -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>اسم الطابعة</th>
                    <th>القسم</th>
                    <th>النوع</th> <!-- ✅ تعديل: إضافة عمود النوع في الجدول -->
                    <th>عنوان IP</th>
                    <th>الحالة</th>
                    <th width="180px">الإجراءات</th>
                </tr>
            </thead>
            <tbody id="printersTableBody">
                <!-- سيتم ملؤها بواسطة JavaScript -->
            </tbody>
        </table>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = '{{ csrf_token() }}';
    const printersIndexUrl = "{{ route('printers.index') }}";
    const printersStoreUrl = "{{ route('printers.store') }}";
    const printersUpdateUrl = "{{ route('printers.update', ':id') }}";
    const printersDeleteUrl = "{{ route('printers.destroy', ':id') }}";

    // دالة عرض الرسائل
    function showMessage(msg, type = 'success') {
        const box = document.getElementById('printersMessage');
        box.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        setTimeout(() => { if (box) box.innerHTML = ''; }, 4000);
    }

    // ✅ دالة جديدة للتحكم في ظهور حقل القسم
    function toggleDepartmentField() {
        const typeSelect = document.getElementById('type');
        const departmentContainer = document.getElementById('departmentFieldContainer');
        const departmentSelect = document.getElementById('department_id');

        if (typeSelect.value === 'kitchen') {
            departmentContainer.style.display = 'block';
            departmentSelect.setAttribute('required', 'required');
        } else {
            departmentContainer.style.display = 'none';
            departmentSelect.removeAttribute('required');
            departmentSelect.value = ''; // تفريغ القيمة عند الاخفاء
        }
    }

    // تحميل الطابعات
    function fetchPrinters() {
        fetch(printersIndexUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(printers => {
                const tbody = document.getElementById('printersTableBody');
                tbody.innerHTML = '';
                if (printers.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center">لا توجد طابعات مضافة.</td></tr>';
                    return;
                }

                printers.forEach((printer, i) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${i + 1}</td>
                        <td>${printer.printer_name}</td>
                        <td>${printer.department ? printer.department.name : '-'}</td>
                        <td>${printer.type === 'kitchen' ? '<span class="badge bg-info">مطبخ</span>' : '<span class="badge bg-warning text-dark">كاشير</span>'}</td>
                        <td>${printer.printer_ip}</td>
                        <td>${printer.active ? '<span class="badge bg-success">نشطة</span>' : '<span class="badge bg-secondary">غير نشطة</span>'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info test-connection" data-ip="${printer.printer_ip}" title="اختبار الاتصال">
                                <i class="fas fa-plug"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary edit-printer" data-id="${printer.id}" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-printer" data-id="${printer.id}" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                attachEvents();
            })
            .catch(error => {
                console.error('Fetch Printers Error:', error);
                showMessage('فشل تحميل الطابعات', 'danger');
            });
    }

    // ربط الأحداث
    function attachEvents() {
        // تعديل
        document.querySelectorAll('.edit-printer').forEach(btn => {
            btn.onclick = function () {
                const id = this.dataset.id;
                fetch(`${printersIndexUrl}/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(printer => {
                        document.getElementById('addPrinterForm').scrollIntoView({ behavior: 'smooth' });
                        document.querySelector('#addPrinterForm button[type="submit"]').innerHTML = '<i class="fas fa-edit"></i> تعديل';
                        document.getElementById('printer_name').value = printer.printer_name;
                        document.getElementById('printer_ip').value = printer.printer_ip;
                        document.getElementById('type').value = printer.type;
                        document.getElementById('active').checked = printer.active;
                        
                        // ✅ استدعاء دالة التحكم بعد تحديد النوع
                        toggleDepartmentField();

                        // تعيين قيمة القسم إذا كان النوع 'kitchen'
                        if (printer.type === 'kitchen') {
                            document.getElementById('department_id').value = printer.department_id;
                        }
                        
                        let hiddenId = document.getElementById('printerId');
                        if (!hiddenId) {
                            hiddenId = document.createElement('input');
                            hiddenId.type = 'hidden';
                            hiddenId.id = 'printerId';
                            hiddenId.name = 'id';
                            document.getElementById('addPrinterForm').appendChild(hiddenId);
                        }
                        hiddenId.value = printer.id;
                    });
            };
        });

        // حذف
        document.querySelectorAll('.delete-printer').forEach(btn => {
            btn.onclick = function () {
                if (!confirm('هل أنت متأكد من الحذف؟')) return;
                const id = this.dataset.id;
                fetch(printersDeleteUrl.replace(':id', id), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    showMessage(data.success, 'success');
                    fetchPrinters();
                })
                .catch(error => {
                    console.error('Delete Error:', error);
                    showMessage('فشل الحذف', 'danger');
                });
            };
        });

        // اختبار الاتصال
        document.querySelectorAll('.test-connection').forEach(btn => {
            btn.onclick = function () {
                const ip = this.dataset.ip;
                const icon = this.querySelector('i');
                icon.className = 'fas fa-spinner fa-spin';
                
                fetch(`/api/test-printer-connection/${ip}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            icon.className = 'fas fa-check-circle text-success';
                            showMessage('الاتصال بالطابعة ناجح', 'success');
                        } else {
                            icon.className = 'fas fa-times-circle text-danger';
                            showMessage('فشل الاتصال بالطابعة', 'danger');
                        }
                    })
                    .catch(() => {
                        icon.className = 'fas fa-times-circle text-danger';
                        showMessage('فشل الاتصال بالطابعة', 'danger');
                    });
            };
        });
    }

    // حفظ الطابعة (إضافة أو تعديل)
    document.getElementById('addPrinterForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const printerId = document.getElementById('printerId')?.value;
        const isEdit = !!printerId;
        const url = isEdit ? printersUpdateUrl.replace(':id', printerId) : printersStoreUrl;

        const formData = new FormData(this);
        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showMessage(data.success, 'success');
                this.reset(); // إعادة تعيين النموذج
                document.querySelector('#addPrinterForm button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> حفظ';
                const hiddenId = document.getElementById('printerId');
                if (hiddenId) hiddenId.remove();
                fetchPrinters();
            } else {
                showMessage(data.message || 'حدث خطأ', 'danger');
            }
        })
        .catch(error => {
            console.error('Save Error:', error);
            showMessage('فشل الحفظ', 'danger');
        });
    });

    // ✅ إضافة حدث change لحقل النوع
    document.getElementById('type').addEventListener('change', toggleDepartmentField);

    // تحميل الطابعات عند فتح الصفحة
    fetchPrinters();
    
    // ✅ استدعاء الدالة عند تحميل الصفحة لضبط الحالة الأولية
    toggleDepartmentField();
});
</script>
@endpush
@endsection