@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">إضافة موظف جديد</h4>
                <p class="card-description">قم بإضافة بيانات الموظف الجديد</p>

                <form class="forms-sample" method="POST" action="{{ route('admin.employees.store') }}"
                    style="text-align:right" dir="rtl">
                    @csrf

                    <div class="form-group">
                        <span class="text-danger">@error('name'){{ $message }}@enderror</span>
                        <label for="name">الاسم الكامل</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="{{ old('name') }}" placeholder="الاسم الكامل">
                    </div>

                    <div class="form-group">
                        <span class="text-danger">@error('email'){{ $message }}@enderror</span>
                        <label for="email">البريد الإلكتروني</label>
                        <input type="email" class="form-control" name="email" id="email"
                            value="{{ old('email') }}" placeholder="البريد الإلكتروني">
                    </div>

                    <div class="form-group">
                        <span class="text-danger">@error('password'){{ $message }}@enderror</span>
                        <label for="password">كلمة المرور</label>
                        <input type="password" class="form-control" name="password" id="password"
                            placeholder="كلمة المرور">
                    </div>

                    <div class="form-group">
                        <span class="text-danger">@error('password_confirmation'){{ $message }}@enderror</span>
                        <label for="password_confirmation">تأكيد كلمة المرور</label>
                        <input type="password" class="form-control" name="password_confirmation"
                            id="password_confirmation" placeholder="تأكيد كلمة المرور">
                    </div>

                    <div class="form-group">
                        <span class="text-danger">@error('role_id'){{ $message }}@enderror</span>
                        <label for="role_id">الدور</label>
                        <select class="form-control" name="role_id" id="role_id">
                            <option value="">اختر الدور</option>
                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <hr>
                    <h5 class="mb-3">الصلاحيات (اختياري — تجاوز صلاحيات الدور)</h5>
                    <p class="text-muted small">الصلاحيات المحددة أدناه ستتجاوز صلاحيات الدور الافتراضية. إذا لم تختر أي صلاحية، سيتم تطبيق صلاحيات الدور فقط.</p>

                    @foreach ($permissions as $group => $groupPermissions)
                    <div class="card mb-3">
                        <div class="card-header py-2">
                            <strong>{{ __($group) }}</strong>
                        </div>
                        <div class="card-body py-2">
                            @foreach ($groupPermissions as $permission)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    id="perm_{{ $permission->id }}"
                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                    {{ $permission->display_name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary mr-2">حفظ</button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-light">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
