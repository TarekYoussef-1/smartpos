@extends('layouts.master')
@section('title', 'إدارة الأقسام')
@section('content')
<div class="container-fluid p-3">
    <h4 class="mb-3">إدارة الأقسام</h4>

    <!-- زر العودة -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ url('/dashboard') }}" class="btn btn-outline-danger">
            <i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم
        </a>
    </div>

    <!-- نموذج إضافة قسم -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="addDepartmentForm" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <input type="text" id="departmentName" class="form-control" placeholder="اكتب اسم القسم..." required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success w-100">إضافة قسم</button>
                </div>
            </form>
            <div id="formMessage" class="mt-2"></div>
        </div>
    </div>

    <!-- رسائل -->
    <div id="departmentsMessage"></div>

    <!-- جدول الأقسام -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>م</th>
                            <th>اسم القسم</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="departmentsTableBody">
                        {{-- سيتم ملؤه ديناميكيًا --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = '{{ csrf_token() }}';
        // إضافة ?json=true للرابط الرئيسي
        const indexUrl = "{{ route('departments.index') }}?json=true"; 
        const storeUrl = "{{ route('departments.store') }}";

        // مجموعة الهيدرز المشتركة
        const ajaxHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        };

        // ... (باقي الدوال escapeHtml, showMessage, formatDate لا تغيير فيها)

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        function showMessage(msg, type = 'success') {
            const messageDiv = document.getElementById('departmentsMessage');
            messageDiv.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            setTimeout(() => messageDiv.innerHTML = '', 4000);
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            try {
                return new Date(dateString).toISOString().split('T')[0];
            } catch (e) {
                return '';
            }
        }

        // جلب الأقسام (الرابط يحتوي بالفعل على ?json=true)
        function fetchDepartments() {
            console.log('Fetching departments...');
            fetch(indexUrl, { headers: ajaxHeaders })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
                    return res.json();
                })
                .then(depts => {
                    console.log('Departments received:', depts);
                    const tbody = document.getElementById('departmentsTableBody');
                    tbody.innerHTML = '';
                    if (depts.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center">لا توجد أقسام لعرضها</td></tr>';
                        return;
                    }
                    depts.forEach((dept, i) => {
                        const row = document.createElement('tr');
                        row.dataset.id = dept.id;
                        row.innerHTML = `
                            <td>${i + 1}</td>
                            <td class="dept-name view">${escapeHtml(dept.name)}</td>
                            <td>${formatDate(dept.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary edit-row">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                    attachRowEvents();
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    showMessage('فشل تحميل الأقسام: ' + error.message, 'danger');
                });
        }

        // إضافة قسم
        document.getElementById('addDepartmentForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const name = document.getElementById('departmentName').value.trim();
            if (!name) return;

            fetch(storeUrl, {
                method: 'POST',
                headers: ajaxHeaders,
                body: JSON.stringify({ name })
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('departmentName').value = '';
                    showMessage(data.success, 'success');
                    fetchDepartments();
                } else {
                    showMessage(data.error || 'فشل الإضافة', 'danger');
                }
            })
            .catch(error => {
                console.error('Store Error:', error);
                showMessage('خطأ في الاتصال: ' + error.message, 'danger');
            });
        });

        // ربط الأحداث (مُحدّث لإضافة ?json=true للتعديل والحذف)
        function attachRowEvents() {
            document.querySelectorAll('.edit-row').forEach(btn => {
                btn.onclick = function () {
                    const row = this.closest('tr');
                    const id = row.dataset.id;
                    const nameCell = row.querySelector('.dept-name');
                    const oldName = nameCell.textContent.trim();
                    nameCell.innerHTML = `<input type="text" class="form-control form-control-sm" value="${oldName}">`;
                    const actions = row.querySelector('td:last-child');
                    actions.innerHTML = `
                        <button class="btn btn-sm btn-success save-row"><i class="fas fa-check"></i></button>
                        <button class="btn btn-sm btn-secondary cancel-row"><i class="fas fa-times"></i></button>
                    `;

                    actions.querySelector('.save-row').onclick = function () {
                        const newName = nameCell.querySelector('input').value.trim();
                        if (!newName || newName === oldName) { fetchDepartments(); return; }

                        // إضافة ?json=true هنا
                        fetch(`{{ url('departments') }}/${id}?json=true`, {
                            method: 'PUT',
                            headers: ajaxHeaders,
                            body: JSON.stringify({ name: newName })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showMessage(data.success, 'success');
                                fetchDepartments();
                            } else {
                                showMessage(data.error || 'فشل التعديل', 'danger');
                            }
                        });
                    };
                    actions.querySelector('.cancel-row').onclick = fetchDepartments;
                };
            });

            document.querySelectorAll('.delete-row').forEach(btn => {
                btn.onclick = function () {
                    const id = this.closest('tr').dataset.id;
                    if (!confirm('هل أنت متأكد من حذف هذا القسم؟')) return;

                    // إضافة ?json=true هنا
                    fetch(`{{ url('departments') }}/${id}?json=true`, {
                        method: 'DELETE',
                        headers: ajaxHeaders
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showMessage(data.success, 'success');
                            fetchDepartments();
                        } else {
                            showMessage(data.error || 'فشل الحذف', 'danger');
                        }
                    });
                };
            });
        }

        // بدء التحميل
        fetchDepartments();
    });
</script>
@endpush
@endsection