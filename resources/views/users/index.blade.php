@extends('layouts.master')
@section('title', 'إدارة المستخدمين')
@section('content')

<div class="container-fluid p-3">
    
    <div class="row">
        <div style="margin-top: 40px;" class="col-12">
         <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ url('/dashboard') }}" class="btn btn-outline-danger">
                    <i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم
                </a>
            </div>
            </div>
        
        <div class="col-12">
            <!-- زر العودة -->
           
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">إدارة المستخدمين</h5>
                </div>
                <div class="card-body">
                    <!-- فرم إضافة مستخدم -->
                    <form id="addUserForm" class="row g-2 align-items-end mb-4">
                        <div class="col-md-2">
                            <label class="form-label small">كود المستخدم</label>
                            <input type="text" id="userCode" class="form-control" placeholder="مثل: 001" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">الاسم</label>
                            <input type="text" id="userName" class="form-control" placeholder="اسم المستخدم" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">المسمى الوظيفي</label>
                            <input type="text" id="userJob" class="form-control" placeholder="مثل: كاشير 1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">الصلاحيات/الدور</label>
                            <select id="userRole" class="form-select" required>
                                <option value="">اختر الدور</option>
                                <option value="admin">مدير</option>
                                <option value="cashier">كاشير</option>
                                <option value="kitchen">مطبخ</option>
                                <option value="waiter">ويتر</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">كلمة المرور</label>
                            <input type="password" id="userPassword" class="form-control" placeholder="4 أحرف على الأقل" required>
                        </div>
                        <div class="col-md-1 mt-2">
                            <label class="form-label small">الحالة</label>
                            <select id="userStatus" class="form-select" required>
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 mt-2 ">
                            <button type="submit" class="btn btn-success w-100">إضافة</button>
                        </div>
                    </form>
                    <hr class="my-3">
                    <!-- رسائل -->
                    <div id="usersMessage"></div>
                    <!-- جدول المستخدمين -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="usersTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>كود المستخدم</th>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>المسمى الوظيفي</th>
                                    <th>الدور</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>آخر تحديث</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                {{-- سيتم ملؤه ديناميكيًا --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = '{{ csrf_token() }}';
        const usersIndexUrl = "{{ route('users.index') }}";
        const usersStoreUrl = "{{ route('users.store') }}";
        // حماية من XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }
        // رسالة
        function showMessage(msg, type = 'success') {
            const messageDiv = document.getElementById('usersMessage');
            messageDiv.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            setTimeout(() => messageDiv.innerHTML = '', 4000);
        }
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toISOString().split('T')[0]; // → 2025-11-07
        }
        // جلب المستخدمين
        function fetchUsers() {
            fetch(usersIndexUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(users => {
                    const tbody = document.getElementById('usersTableBody');
                    tbody.innerHTML = '';
                    users.forEach((user, i) => {
                        const row = document.createElement('tr');
                        row.dataset.id = user.id;
                        row.innerHTML = `
                        <td class="u-code view">${escapeHtml(user.user_code ?? '')}</td>
                        <td>${i + 1}</td>
                        <td class="u-name view">${escapeHtml(user.name)}</td>
                        <td class="u-job view">${escapeHtml(user.job_title ?? '')}</td>
                        <td class="u-role view">${escapeHtml(user.role)}</td>
                        <td class="u-status view">
                            <span class="badge bg-${user.status === 'active' ? 'success' : 'secondary'}">
                                ${user.status === 'active' ? 'نشط' : 'غير نشط'}
                            </span>
                        </td>
                        <td>${formatDate(user.created_at)}</td>
                        <td>${formatDate(user.updated_at)}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-row">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-user">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                        tbody.appendChild(row);
                    });
                    attachRowEvents();
                })
                .catch(() => showMessage('فشل تحميل المستخدمين', 'danger'));
        }
        // إضافة مستخدم
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const payload = {
                user_code: document.getElementById('userCode').value.trim(),
                name: document.getElementById('userName').value.trim(),
                job_title: document.getElementById('userJob').value.trim(),
                role: document.getElementById('userRole').value,
                password: document.getElementById('userPassword').value,
                status: document.getElementById('userStatus').value
            };
            fetch(usersStoreUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => {
                    if (!res.ok) {
                        // إذا كان الرد ليس OK، حاول قراءة رسالة الخطأ
                        return res.json().then(errData => {
                            throw new Error(errData.error || errData.message || `فشل الإضافة: ${res.status} ${res.statusText}`);
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        showMessage(data.success, 'success');
                        this.reset();
                        fetchUsers();
                    } else {
                        showMessage(data.error || 'فشل الإضافة', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Add User Error:', error);
                    showMessage(error.message, 'danger');
                });
        });

        // ربط الأحداث
        function attachRowEvents() {
            // تعديل داخل الصف
            document.querySelectorAll('.edit-row').forEach(btn => {
                btn.onclick = function() {
                    const row = this.closest('tr');
                    const id = row.dataset.id;

                    // تحويل الخلايا إلى حقول تعديل
                    row.querySelectorAll('.view').forEach(cell => {
                        const field = cell.classList[0].replace('u-', '');
                        let value = cell.textContent.trim();

                        if (field === 'status') {
                            value = cell.querySelector('span').textContent.includes('نشط') ? 'active' : 'inactive';
                        }

                        let input = '';
                        if (field === 'role') {
                            input = `<select class="form-select form-select-sm">
                                <option value="admin" ${value === 'admin' ? 'selected' : ''}>مدير</option>
                                <option value="cashier" ${value === 'cashier' ? 'selected' : ''}>كاشير</option>
                                <option value="kitchen" ${value === 'kitchen' ? 'selected' : ''}>مطبخ</option>
                                <option value="waiter" ${value === 'waiter' ? 'selected' : ''}>ويتر</option>
                            </select>`;
                        } else if (field === 'status') {
                            input = `<select class="form-select form-select-sm">
                                <option value="active" ${value === 'active' ? 'selected' : ''}>نشط</option>
                                <option value="inactive" ${value === 'inactive' ? 'selected' : ''}>غير نشط</option>
                            </select>`;
                        } else {
                            input = `<input type="text" class="form-control form-control-sm" value="${value}" 
                                           ${field === 'user_code' ? 'placeholder="مثل: USR001"' : ''}>`;
                        }

                        cell.innerHTML = input;
                    });

                    // تغيير الأزرار
                    const actions = row.querySelector('td:last-child');
                    actions.innerHTML = `
                        <button class="btn btn-sm btn-success save-row"><i class="fas fa-check"></i></button>
                        <button class="btn btn-sm btn-secondary cancel-row"><i class="fas fa-times"></i></button>
                    `;

                    // حفظ التعديل
                    actions.querySelector('.save-row').onclick = function() {
                        const payload = {
                            user_code: row.querySelector('.u-code input').value.trim(),
                            name: row.querySelector('.u-name input').value.trim(),
                            job_title: row.querySelector('.u-job input').value.trim(),
                            role: row.querySelector('.u-role select').value,
                            status: row.querySelector('.u-status select').value
                        };

                        const password = prompt('كلمة المرور الجديدة (اترك فارغًا إذا لا تريد تغييرها):');
                        if (password !== null && password !== '') {
                            payload.password = password;
                        }

                        fetch(`{{ url('users') }}/${id}/update`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify(payload)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    showMessage(data.success, 'success');
                                    fetchUsers();
                                } else {
                                    showMessage(data.error || 'فشل التعديل', 'danger');
                                }
                            })
                            .catch(() => showMessage('خطأ في الاتصال', 'danger'));
                    };

                    // إلغاء التعديل
                    actions.querySelector('.cancel-row').onclick = fetchUsers;
                };
            });

            // حذف
            document.querySelectorAll('.delete-user').forEach(btn => {
                btn.onclick = function() {
                    const id = this.closest('tr').dataset.id;
                    if (!confirm('هل أنت متأكد من حذف هذا المستخدم؟')) return;

                    fetch(`{{ url('users') }}/${id}/delete`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showMessage(data.success, 'success');
                                fetchUsers();
                            } else {
                                showMessage(data.error || 'فشل الحذف', 'danger');
                            }
                        })
                        .catch(() => showMessage('خطأ في الاتصال', 'danger'));
                };
            });
        }
        // بدء التحميل
        fetchUsers();
    });
</script>
@endpush
@endsection