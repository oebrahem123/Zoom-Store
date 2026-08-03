<?php

namespace App\Services\Workflow;

use Illuminate\Support\Collection;

class WorkflowAggregator
{
    public static function evaluate(Collection $statuses): array
    {
        if ($statuses->contains(fn($s) => $s === 'rejected')) {
            return [
                'status' => 'action_required',
                'label' => 'يتطلب إجراء',
                'badge' => 'badge-cancelled',
                'action_required' => true,
            ];
        }

        if ($statuses->every(fn($s) => $s === 'delivered')) {
            return [
                'status' => 'delivered',
                'label' => 'تم التوصيل',
                'badge' => 'badge-delivered',
                'action_required' => false,
            ];
        }

        if ($statuses->every(fn($s) => in_array($s, ['shipped', 'delivered'], true))) {
            return [
                'status' => 'shipped',
                'label' => 'تم الشحن',
                'badge' => 'badge-shipped',
                'action_required' => false,
            ];
        }

        if ($statuses->contains(fn($s) => in_array($s, ['printing', 'processing', 'approved'], true))) {
            return [
                'status' => 'processing',
                'label' => 'قيد التجهيز',
                'badge' => 'badge-processing',
                'action_required' => false,
            ];
        }

        return [
            'status' => 'pending',
            'label' => 'قيد التنفيذ',
            'badge' => 'badge-pending',
            'action_required' => false,
        ];
    }
}
