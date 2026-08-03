@extends('layouts.master')
@section('title', 'مكتبة تصاميمي')
@section('content')

<section class="my-designs-page section">
    <div class="container">

        {{-- Header --}}
        <div class="designs-header">
            <h1 class="designs-title"> مكتبة تصاميمي</h1>
            <a href="{{ route('design.start') }}" class="new-design-btn">+ تصميم جديد</a>
        </div>

        {{-- Stats --}}
        <div class="designs-stats">
            <div class="stat-card">
                <span class="stat-number">{{ $totalDesigns }}</span>
                <span class="stat-label">إجمالي التصاميم</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">{{ $savedCount }}</span>
                <span class="stat-label">✔ محفوظة</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">{{ $orderedCount }}</span>
                <span class="stat-label">تم طلبها</span>
            </div>
            @if ($lastModified)
            <div class="stat-card">
                <span class="stat-number">{{ $lastModified->updated_at->diffForHumans() }}</span>
                <span class="stat-label">آخر تعديل</span>
            </div>
            @endif
        </div>

        {{-- Filters & Controls --}}
        <div class="designs-controls">
            <form method="GET" action="{{ route('designs.my') }}" class="filters-form" id="filters-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="ابحث عن تصميم..." value="{{ request('search') }}"
                        class="filter-input">
                </div>
                <div class="filter-group">
                    <select name="ordered" class="filter-select">
                        <option value="">جميع التصاميم</option>
                        <option value="ordered" {{ request('ordered')==='ordered' ? 'selected' : '' }}>تم طلبها</option>
                        <option value="not_ordered" {{ request('ordered')==='not_ordered' ? 'selected' : '' }}>محفوظة
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="product_type" class="filter-select">
                        <option value="">جميع المنتجات</option>
                        @php
                        $productTypes = \App\Models\Product::where('is_designable', true)->distinct()->pluck('name');
                        @endphp
                        @foreach ($productTypes as $pt)
                        <option value="{{ $pt }}" {{ request('product_type')===$pt ? 'selected' : '' }}>{{ $pt }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <select name="sort" class="filter-select">
                        <option value="newest" {{ request('sort')==='oldest' ? '' : 'selected' }}>الأحدث</option>
                        <option value="oldest" {{ request('sort')==='oldest' ? 'selected' : '' }}>الأقدم</option>
                    </select>
                </div>
                <div class="filter-group view-toggle">
                    <button type="button" class="view-btn {{ $viewMode === 'grid' ? 'active' : '' }}" data-view="grid"
                        title="عرض شبكي">
                        ▦
                    </button>
                    <button type="button" class="view-btn {{ $viewMode === 'list' ? 'active' : '' }}" data-view="list"
                        title="عرض قائمة">
                        ☰
                    </button>
                </div>
                <input type="hidden" name="view" id="view-input" value="{{ $viewMode }}">
                <button type="submit" class="filter-submit">تصفية</button>
            </form>
        </div>

        {{-- Designs Grid --}}
        <div class="designs-grid {{ $viewMode === 'list' ? 'list-view' : '' }}" id="designs-container">
            @include('design.partials.design-cards', ['designs' => $designs, 'orderedIds' => $orderedIds])
        </div>

        {{-- Load More / Pagination --}}
        @if ($designs->hasMorePages())
        <div class="load-more-wrapper">
            <button type="button" class="load-more-btn" id="load-more" data-next-url="{{ $designs->nextPageUrl() }}"
                data-current-page="1">
                عرض المزيد
            </button>
        </div>
        @endif

    </div>
</section>

{{-- Delete Confirmation Modal --}}
<div class="modal-overlay" id="delete-modal" style="display:none;">
    <div class="modal-box">
        <h3>حذف التصميم</h3>
        <p>هل أنت متأكد من حذف هذا التصميم؟ لا يمكن التراجع عن هذا الإجراء.</p>
        <div class="modal-actions">
            <button type="button" class="modal-cancel-btn" id="modal-cancel">إلغاء</button>
            <form method="POST" id="delete-form-modal" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="modal-confirm-btn">حذف</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
    // ZoomLoading: show on edit/new design link clicks
    $(document).on('click', 'a[href*="design.edit"], a[href*="design.start"], .new-design-btn', function() {
        if (window.ZoomStore && ZoomStore.ZoomLoading) {
            ZoomStore.ZoomLoading.show({ message: '\u062C\u0627\u0631\u064A \u062A\u062D\u0645\u064A\u0644 \u0627\u0644\u0645\u062D\u0631\u0631...', allowClose: false });
        }
    });
    $(document).on('click', '#load-more', function() {
        if (window.ZoomStore && ZoomStore.ZoomLoading) {
            ZoomStore.ZoomLoading.show({ message: '\u062C\u0627\u0631\u064A \u062A\u062D\u0645\u064A\u0644 \u0627\u0644\u0645\u0632\u064A\u062F...', allowClose: false });
        }
    }).on('load-more-complete', function() {
        if (window.ZoomStore && ZoomStore.ZoomLoading) ZoomStore.ZoomLoading.hide();
    });

    // View toggle
    $('.view-btn').on('click', function() {
        $('.view-btn').removeClass('active');
        $(this).addClass('active');
        $('#view-input').val($(this).data('view'));
        $('#filters-form').submit();
    });

    // Auto-submit on filter change
    $('.filter-select').on('change', function() {
        $('#filters-form').submit();
    });

    // Delete confirmation
    $(document).on('click', '.delete-btn', function() {
        var designId = $(this).data('design-id');
        $('#delete-form-modal').attr('action', '/design/' + designId);
        $('#delete-modal').fadeIn(200);
    });

    $('#modal-cancel').on('click', function() {
        $('#delete-modal').fadeOut(200);
    });

    $(document).on('click', '.modal-overlay', function(e) {
        if ($(e.target).closest('.modal-box').length === 0) {
            $(this).fadeOut(200);
        }
    });

    // Duplicate via form submit
    $(document).on('click', '.duplicate-btn', function() {
        $(this).closest('.design-card-wrapper').find('.duplicate-form').submit();
    });

    // Load more
    $('#load-more').on('click', function() {
        var btn = $(this);
        var nextUrl = btn.data('next-url');
        var page = btn.data('current-page') + 1;

        $.get(nextUrl + '&page=' + page + '&view=' + $('#view-input').val(), function(data) {
            $('#designs-container').append(data.html);
            btn.data('current-page', page);
            if (window.ZoomStore && ZoomStore.ZoomLoading) ZoomStore.ZoomLoading.hide();
            if (data.next_page_url) {
                btn.data('next-url', data.next_page_url);
            } else {
                btn.remove();
            }
        }).fail(function() {
            if (window.ZoomStore && ZoomStore.ZoomLoading) ZoomStore.ZoomLoading.hide();
        });
    });

    // Highlight animation
    @if (request('highlight'))
        setTimeout(function() {
            var $card = $('.design-card-wrapper[data-design-id="{{ request('highlight') }}"]');
            if ($card.length) {
                $('html, body').animate({ scrollTop: $card.offset().top - 100 }, 500);
                $card.find('.design-card').addClass('highlighted-anim');
                setTimeout(function() {
                    $card.find('.design-card').removeClass('highlighted-anim');
                }, 3000);
            }
        }, 300);
    @endif
});
</script>
@endpush

<style>
    .my-designs-page {
        padding: 40px 0;
    }

    .designs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .designs-title {
        font-size: 28px;
        font-weight: 700;
        color: #222;
        margin: 0;
    }

    .new-design-btn {
        display: inline-block;
        padding: 12px 28px;
        background: #1e1e1e;
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }

    .new-design-btn:hover {
        background: #333;
        color: #fff;
    }

    .designs-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .stat-number {
        display: block;
        font-size: 28px;
        font-weight: 700;
        color: #1e1e1e;
    }

    .stat-label {
        display: block;
        font-size: 13px;
        color: #888;
        margin-top: 4px;
    }

    .designs-controls {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .filters-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 150px;
    }

    .filter-input,
    .filter-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        background: #fafafa;
    }

    .filter-input:focus,
    .filter-select:focus {
        border-color: #999;
        outline: none;
    }

    .filter-submit {
        padding: 10px 24px;
        background: #1e1e1e;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }

    .filter-submit:hover {
        background: #333;
    }

    .view-toggle {
        display: flex;
        gap: 4px;
        min-width: auto;
    }

    .view-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fafafa;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .view-btn.active {
        background: #1e1e1e;
        color: #fff;
        border-color: #1e1e1e;
    }

    .designs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }

    .designs-grid.list-view {
        grid-template-columns: 1fr;
    }

    .designs-grid.list-view .design-card {
        display: flex;
        flex-direction: row;
    }

    .designs-grid.list-view .design-card-image {
        width: 200px;
        min-height: 200px;
        flex-shrink: 0;
    }

    .designs-grid.list-view .design-card-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .design-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .design-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .design-card-image {
        position: relative;
        overflow: hidden;
        background: #f5f5f5;
    }

    .design-card-image img {
        width: 100%;
        height: 260px;
        object-fit: cover;
        display: block;
        transition: transform 0.4s;
    }

    .design-card:hover .design-card-image img {
        transform: scale(1.05);
    }

    .design-card-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .design-card:hover .design-card-overlay {
        opacity: 1;
    }

    .overlay-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        padding: 10px 14px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        color: #333;
        text-decoration: none;
        transition: all 0.2s;
    }

    .overlay-btn:hover {
        background: #fff;
        transform: scale(1.05);
        color: #000;
        text-decoration: none;
    }

    .overlay-btn .icon {
        font-size: 18px;
    }

    .design-status {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .design-status.ordered {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .design-status.saved {
        background: #e3f2fd;
        color: #1565c0;
    }

    .design-card-info {
        padding: 14px;
    }

    .design-product-name {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 600;
        color: #222;
    }

    .design-variant-label {
        margin: 0 0 4px;
        font-size: 13px;
        color: #666;
    }

    .design-date {
        margin: 0 0 10px;
        font-size: 12px;
        color: #999;
    }

    .design-actions-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .inline-form {
        display: inline;
    }

    .order-now-btn {
        display: inline-block;
        padding: 7px 16px;
        background: #1e1e1e;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .order-now-btn:hover {
        background: #333;
        color: #fff;
        text-decoration: none;
    }

    .order-now-btn.disabled,
    .order-now-btn[aria-disabled="true"] {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .empty-designs {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        font-size: 64px;
        margin-bottom: 16px;
    }

    .empty-designs h3 {
        font-size: 22px;
        color: #333;
        margin-bottom: 8px;
    }

    .empty-designs p {
        color: #888;
        margin-bottom: 20px;
    }

    .empty-cta {
        display: inline-block;
        padding: 12px 32px;
        background: #1e1e1e;
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
    }

    .empty-cta:hover {
        color: #fff;
        background: #333;
    }

    .load-more-wrapper {
        text-align: center;
        margin-top: 30px;
    }

    .load-more-btn {
        padding: 12px 40px;
        background: #fff;
        border: 2px solid #1e1e1e;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .load-more-btn:hover {
        background: #1e1e1e;
        color: #fff;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-box {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        max-width: 420px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .modal-box h3 {
        margin-bottom: 12px;
        font-size: 20px;
    }

    .modal-box p {
        color: #666;
        margin-bottom: 24px;
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .modal-cancel-btn,
    .modal-confirm-btn {
        padding: 10px 28px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
    }

    .modal-cancel-btn {
        background: #eee;
        color: #333;
    }

    .modal-confirm-btn {
        background: #d32f2f;
        color: #fff;
    }

    .highlighted {
        border-color: #1e1e1e;
        box-shadow: 0 0 0 2px #1e1e1e;
    }

    .highlighted-anim {
        animation: highlightPulse 3s ease;
    }

    @keyframes highlightPulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 transparent;
        }

        20% {
            border-color: #1e1e1e;
            box-shadow: 0 0 0 2px #1e1e1e;
        }
    }

    @media (max-width: 768px) {
        .designs-header {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .designs-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .filters-form {
            flex-direction: column;
        }

        .filter-group {
            min-width: 100%;
        }

        .designs-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .designs-grid.list-view .design-card {
            flex-direction: column;
        }

        .designs-grid.list-view .design-card-image {
            width: 100%;
        }
    }
</style>

@endsection