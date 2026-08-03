@extends('admin.layout')
@section('content')
<style>
    .artwork-gallery-wrapper {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -6px;
    }
    .artwork-gallery-wrapper > [class*="col-"] {
        padding-left: 6px;
        padding-right: 6px;
    }
    .artwork-card {
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        transition: box-shadow 0.2s ease;
        background: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .artwork-card:hover {
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .artwork-card .artwork-image-wrap {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f6f8fa;
        padding: 10px;
        min-height: 120px;
    }
    .artwork-card .artwork-image-wrap img {
        max-width: 100%;
        max-height: 120px;
        object-fit: contain;
        display: block;
    }
    .artwork-card .artwork-actions {
        padding: 8px;
        display: flex;
        gap: 6px;
        justify-content: center;
        border-top: 1px solid #edf0f5;
    }
    .artwork-card .artwork-actions .btn {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 12px;
        border: none;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    .empty-state i {
        font-size: 56px;
        display: block;
        margin-bottom: 16px;
        color: #ccc;
    }
</style>

<div class="contener">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                @if (session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div>
                @endif

                <div class="artwork-gallery-wrapper">
                    @forelse ($designs as $design)
                    <div class="col-lg-2 col-md-3 col-4 mb-2">
                        <div class="artwork-card">
                            <div class="artwork-image-wrap">
                                <img src="{{ asset($design->image) }}" alt="{{ $design->name }}" loading="lazy">
                            </div>
                            <div class="artwork-actions">
                                @permission(\App\Permissions\Permission::DESIGNS_EDIT)
                                <a href="{{ route('admin.designs.edit', $design->id) }}" class="btn btn-warning btn-sm">
                                    <i class="mdi mdi-pencil"></i> تعديل
                                </a>
                                @endpermission
                                @permission(\App\Permissions\Permission::DESIGNS_DELETE)
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="confirmDelete({{ $design->id }}, '{{ addslashes($design->name) }}')">
                                    <i class="mdi mdi-delete"></i> حذف
                                </button>
                                @endpermission
                                <form id="delete-form-{{ $design->id }}"
                                    action="{{ route('admin.designs.destroy', $design->id) }}"
                                    method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="mdi mdi-image-multiple"></i>
                        <p>لا توجد تصاميم بعد</p>
                        @permission(\App\Permissions\Permission::DESIGNS_CREATE)
                        <a href="{{ route('admin.designs.create') }}" class="btn btn-primary">إضافة أول تصميم</a>
                        @endpermission
                    </div>
                    @endforelse
                </div>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $designs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        if (confirm("هل أنت متأكد من حذف هذا التصميم؟")) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection
