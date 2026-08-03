@forelse ($designs as $design)
    @php
        $isOrdered = isset($orderedIds) ? in_array($design->id, $orderedIds) : \App\Models\orderdetails::where('design_id', $design->id)->exists();
        $preview = $design->preview_image ? asset($design->preview_image) : asset('assets/frontend/images/placeholder-design.png');
        $productName = $design->product ? $design->product->name : 'منتج';
        $variantLabel = '';
        if ($design->variant) {
            $parts = array_filter([$design->variant->size, $design->variant->color]);
            $variantLabel = implode(' - ', $parts);
        }
    @endphp
    <div class="design-card-wrapper" data-design-id="{{ $design->id }}">
        <div class="design-card {{ request('highlight') == $design->id ? 'highlighted' : '' }}">
            <div class="design-card-image">
                <a href="{{ route('design.edit', $design->id) }}">
                    <img src="{{ $preview }}" alt="{{ $productName }}" loading="lazy">
                </a>
                <div class="design-card-overlay">
                    <a href="{{ route('design.edit', $design->id) }}" class="overlay-btn" title="تعديل التصميم">
                        <span class="icon">✏️</span>
                        <span>تعديل</span>
                    </a>
                    <button type="button" class="overlay-btn duplicate-btn" data-design-id="{{ $design->id }}" title="إنشاء نسخة">
                        <span class="icon">📋</span>
                        <span>نسخة</span>
                    </button>
                    <button type="button" class="overlay-btn delete-btn" data-design-id="{{ $design->id }}" title="حذف التصميم">
                        <span class="icon">🗑️</span>
                        <span>حذف</span>
                    </button>
                </div>
                <span class="design-status {{ $isOrdered ? 'ordered' : 'saved' }}">
                    {{ $isOrdered ? 'تم طلبه' : '✔ محفوظ' }}
                </span>
            </div>
            <div class="design-card-info">
                <h3 class="design-product-name">{{ $productName }}</h3>
                @if ($variantLabel)
                    <p class="design-variant-label">{{ $variantLabel }}</p>
                @endif
                <p class="design-date">{{ $design->created_at->diffForHumans() }}</p>
                <div class="design-actions-row">
                    <form action="{{ route('designs.duplicate', $design->id) }}" method="POST" class="inline-form duplicate-form">
                        @csrf
                    </form>
                    <form action="{{ route('designs.destroy', $design->id) }}" method="POST" class="inline-form delete-form">
                        @csrf @method('DELETE')
                    </form>
                    @php
                        $orderUrl = route('product.details', $design->product_id) . '?design_id=' . $design->id;
                        if ($design->variant) {
                            $orderUrl .= '&variant_id=' . $design->variant_id . '&size=' . urlencode($design->variant->size) . '&color=' . urlencode($design->variant->color);
                        }
                    @endphp
                    <a href="{{ $orderUrl }}" class="order-now-btn {{ !$design->product ? 'disabled' : '' }}" {{ !$design->product ? 'aria-disabled=true' : '' }}>
                        🛒 اطلبه الآن
                    </a>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="empty-designs">
        <div class="empty-icon">🎨</div>
        <h3>لا توجد تصاميم بعد</h3>
        <p>صمم منتجك الأول الآن!</p>
        <a href="{{ route('design.start') }}" class="empty-cta">ابدأ التصميم</a>
    </div>
@endforelse
