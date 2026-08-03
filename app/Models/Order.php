<?php

namespace App\Models;
use App\Models\orderdetails;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'name', 'email', 'address', 'phone', 'note', 'user_id',
        'status', 'rejected_at', 'rejection_reason', 'rejection_category',
        'shipment_group_id', 'shipping_cost', 'shipping_saved',
    ];

    protected $casts = [
        'rejected_at' => 'datetime',
        'shipping_cost' => 'decimal:2',
        'shipping_saved' => 'decimal:2',
        'return_requested_at' => 'datetime',
        'returned_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function orderdetails()
    {
        return $this->hasMany(orderdetails::class, 'order_id', 'id');
    }

    public function shipmentGroup()
    {
        return $this->belongsTo(ShipmentGroup::class, 'shipment_group_id');
    }

    public function timeline()
    {
        return $this->hasMany(OrderTimeline::class, 'order_id');
    }

    public static function getMergeableStatuses(): array
    {
        return [
            'pending',
            'pending_review',
            'approved',
            'processing',
            'printing',
        ];
    }

    public function isRejected(): bool
    {
        return $this->status === 'cancelled' && $this->rejected_at !== null;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function rejectionCategoryLabel(): string
    {
        $labels = [
            'religious' => 'محتوى ديني مخالف',
            'political' => 'محتوى سياسي مخالف',
            'adult_content' => 'محتوى إباحي',
            'copyright' => 'انتهاك حقوق ملكية',
            'hate_speech' => 'خطاب كراهية',
            'illegal_content' => 'محتوى غير قانوني',
            'low_quality' => 'جودة غير مناسبة للطباعة',
            'other' => 'سبب آخر',
        ];
        return $labels[$this->rejection_category] ?? $this->rejection_reason ?? '—';
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'approved' => '<span class="badge-status badge-complete">✓ مقبول</span>',
            'cancelled' => '<span class="badge-status badge-cancelled">✗ مرفوض</span>',
            default => '<span class="badge-status badge-pending">⏳ قيد التنفيذ</span>',
        };
    }
}
