@if($groups->isEmpty())
<div class="alert alert-warning">لا توجد منتجات في هذه الشحنة</div>
@else
@foreach($groups as $group)
@if($groups->count() > 1)
<div class="order-ref-header">
    <i class="fa fa-file-text-o"></i>
    طلب رقم #{{ $group['order']->id }}
    <span class="ref-badge">مرجع</span>
</div>
@endif

@foreach($group['details'] as $i => $detail)
@php $state = $group['states'][$i] ?? []; @endphp
@include('shipments.partials.product-cards.' . ($state['has_design'] ? 'custom' : 'ready'), [
    'detail' => $detail,
    'state' => $state,
])
@endforeach

@if(!$loop->last)
<hr style="margin:16px 0;">
@endif
@endforeach
@endif
