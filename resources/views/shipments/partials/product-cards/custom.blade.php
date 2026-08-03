@php
$img = $detail->displayImagePath();

$dsLabel = match($state['workflow'] ?? '') {
    'approved' => 'مقبول',
    'cancelled' => 'مرفوض',
    'pending_review' => 'قيد المراجعة',
    default => 'قيد المراجعة',
};
$dsClass = match($dsLabel) {
    'مقبول' => 'design-approved',
    'مرفوض' => 'design-rejected',
    default => 'design-pending',
};
$dsIcon = match($dsLabel) {
    'مقبول' => '🟢',
    'مرفوض' => '🔴',
    default => '🟡',
};

$canSubmit = $state['design_updated_at'] && $state['rejection']['rejected_at']
    && $state['design_updated_at'] > $state['rejection']['rejected_at'];
@endphp
<div class="product-card product-card-custom">
    <div class="product-card-image">
        <img src="{{ $img ? asset($img) : asset('images/default.png') }}" alt="{{ $detail->displayName() }}">
        @if($state['design_preview'])
        <div class="design-preview-overlay">
            <img src="{{ asset($state['design_preview']) }}" alt="معاينة التصميم">
        </div>
        @endif
    </div>
    <div class="product-card-body">
        <div class="product-type-badge badge-custom">🎨 تصميم مخصص</div>
        <h4>{{ $detail->displayName() }}</h4>
        @if($detail->catalogStatusMessage())
        <div class="product-availability unavailable">{{ $detail->catalogStatusMessage() }}</div>
        @endif
        <div class="product-details">
            <span><strong>المقاس:</strong> {{ $detail->size ?? '—' }}</span>
            <span><strong>اللون:</strong> {{ $detail->color ?? '—' }}</span>
        </div>
        <div class="product-qty-row">
            <span>{{ $detail->quantity }} × {{ number_format($detail->price, 2) }} ج.م</span>
            <span class="product-subtotal">{{ number_format($detail->lineTotal(), 2) }} ج.م</span>
        </div>

        <div class="design-status">
            <span class="design-status-badge {{ $dsClass }}">{{ $dsIcon }} {{ $dsLabel }}</span>
        </div>

        @if($state['is_rejected'])
        <div class="rejected-block">
            <div class="rejected-header">❌ تم رفض التصميم</div>
            <div class="rejected-reason">{{ $state['rejection']['category_label'] }}</div>
            @if($state['rejection']['reason'] && $state['rejection']['reason'] !== $state['rejection']['category_label'])
            <div class="rejected-notes">{{ $state['rejection']['reason'] }}</div>
            @endif
            <div class="rejected-actions">
                @if($detail->design_id)
                <a href="{{ route('design.edit', $detail->design_id) }}" class="btn btn-sm btn-primary">تعديل التصميم</a>
                @endif
                @if($detail->design_id)
                <form method="POST" action="{{ route('design.resubmit', $detail->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success" {{ $canSubmit ? '' : 'disabled' }}>إرسال مرة أخرى</button>
                </form>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
