@extends('admin.layout')
@section('content')

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">سجل النشاطات</h4>
                <p class="card-description text-center">
                    جميع العمليات والإجراءات التي تمت في النظام
                </p>

                <div class="table-responsive">
                    <table class="table table-striped text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>المستخدم</th>
                                <th>الإجراء</th>
                                <th>الوصف</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td class="fw-bold">{{ $log->user->name ?? 'النظام' }}</td>
                                <td>
                                    <span class="badge badge-info p-2">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td>{{ $log->description ?? '—' }}</td>
                                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    لا توجد نشاطات مسجلة بعد. سجل النشاطات يبدأ بالتسجيل عند تفعيل النظام.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
