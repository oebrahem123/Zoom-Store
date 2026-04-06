@extends('admin.layout')
@section('content')

            {{-- عنوان الصفحة --}}
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="card-title text-center mb-4">سجل عمليات الحذف</h4>
                            <h5 class="card-description text-center">
                                قائمة بجميع عمليات الحذف التي تمت في النظام
                            </h5>

                            {{-- إشعار نجاح --}}
                            @if (session('success'))
                                <div class="alert alert-success text-center">{{ session('success') }}</div>
                            @endif

                            <div class="table-responsive" style="border-radius: 10px">
                                <table class="table table-striped text-center align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>المستخدم</th>
                                            <th>العملية</th>
                                            <th>العنصر المحذوف</th>
                                            <th>الوقت</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($logs as $log)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="fw-bold">{{ $log->user->name ?? 'غير معروف' }}</td>
                                                <td>
                                                    <span class="badge badge-danger p-2">
                                                        {{ $log->action }}
                                                    </span>
                                                </td>
                                                <td>{{ $log->target }}</td>
                                                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">لا توجد عمليات حذف مسجلة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div> {{-- end table-responsive --}}
                        </div> {{-- end card-body --}}
                    </div> {{-- end card --}}
                </div> {{-- end col --}}
            </div>

@endsection
