@extends('admin.layout')
@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            <h4 class="card-title text-center mb-4">الموظفين</h4>
            <p class="card-description text-center">إدارة حسابات الموظفين في لوحة التحكم</p>

            @if (session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger text-center">{{ $errors->first() }}</div>
            @endif

            @permission(\App\Permissions\Permission::EMPLOYEES_CREATE)
            <div class="mb-3 text-end">
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">إضافة موظف جديد</a>
            </div>
            @endpermission

            <form method="GET" class="row mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو البريد الإلكتروني"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role_id" class="form-control">
                        <option value="">جميع الأدوار</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="is_active" class="form-control">
                        <option value="">جميع الحالات</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100">بحث</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الدور</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td class="fw-bold">{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td><span class="badge badge-info p-2">{{ $employee->role->display_name }}</span></td>
                            <td>
                                @if ($employee->is_active)
                                <span class="badge badge-success p-2">نشط</span>
                                @else
                                <span class="badge badge-secondary p-2">غير نشط</span>
                                @endif
                            </td>
                            <td>{{ $employee->created_at->format('Y-m-d') }}</td>
                            <td>
                                @permission(\App\Permissions\Permission::EMPLOYEES_EDIT)
                                <a href="{{ route('admin.employees.edit', $employee->id) }}"
                                    class="btn btn-warning btn-sm">تعديل</a>
                                @endpermission

                                @permission(\App\Permissions\Permission::EMPLOYEES_TOGGLE_STATUS)
                                <form action="{{ route('admin.employees.toggle-status', $employee->id) }}"
                                    method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ $employee->is_active ? 'btn-secondary' : 'btn-success' }}">
                                        {{ $employee->is_active ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                </form>
                                @endpermission
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">لا يوجد موظفون</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $employees->links() }}
            </div>

        </div>
    </div>
</div>

@endsection
