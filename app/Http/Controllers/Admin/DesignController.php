<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomDesign;
use App\Models\Order;
use App\Models\orderdetails;
use App\Models\product;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    public function show($orderId, $detailId)
    {
        $order = Order::with('orderdetails')->findOrFail($orderId);
        $detail = orderdetails::with('design')->findOrFail($detailId);
        $product = product::with('printAreas')->findOrFail($detail->product_id);

        $design = $detail->design;
        if (!$design) {
            return redirect()->back()->with('error', 'لا يوجد تصميم لهذا المنتج');
        }

        $customDesign = $design;
        $elements = $customDesign->elements ?? [];
        $productImages = $customDesign->productImages ?? [];
        $viewPrintAreas = $customDesign->viewPrintAreas ?? [];

        if (empty($productImages)) {
            $imageData = $product->getEditorImageData();
            $productImages = $imageData['base_images'];
        }
        if (empty($viewPrintAreas)) {
            $viewMapping = $product->getEditorViewMapping();
            $viewPrintAreas = $viewMapping['view_names'];
        }

        $elementsByView = collect();
        foreach ($elements as $el) {
            $viewIndex = $el['view'] ?? 0;
            if (!$elementsByView->has($viewIndex)) {
                $elementsByView[$viewIndex] = collect();
            }
            $elementsByView[$viewIndex]->push($el);
        }

        $viewKeys = $elementsByView->keys();
        $productIndex = $order->orderdetails->search(function ($d) use ($detailId) {
            return $d->id == $detailId;
        });

        return view('admin.designs.show', compact(
            'order', 'detail', 'product', 'design',
            'customDesign', 'elements', 'productImages',
            'viewPrintAreas', 'elementsByView', 'viewKeys', 'productIndex'
        ));
    }

    public function edit($orderId, $detailId)
    {
        $order = Order::with('orderdetails')->findOrFail($orderId);
        $detail = orderdetails::with('design.product.variant', 'design.elements', 'design.product.productphotos', 'design.product.printAreas')->findOrFail($detailId);
        $product = $detail->product;

        $design = $detail->design;
        if (!$design) {
            return redirect()->back()->with('error', 'لا يوجد تصميم لهذا المنتج');
        }

        $designsByView = [];
        foreach ($design->elements as $element) {
            $viewIndex = $element->view ?? 0;
            if (! isset($designsByView[$viewIndex])) {
                $designsByView[$viewIndex] = [];
            }

            $elementData = [
                'type' => $element->type,
                'content' => $element->content,
                'position_x' => $element->position_x,
                'position_y' => $element->position_y,
                'rotation' => $element->rotation,
                'z_index' => $element->z_index,
                'print_area_id' => $element->print_area_id,
                'origin_x' => $element->origin_x,
                'origin_y' => $element->origin_y,
            ];

            if ($element->type === 'image') {
                $elementData['width'] = $element->width;
                $elementData['height'] = $element->height;
                $elementData['scale_x'] = $element->scale_x;
                $elementData['scale_y'] = $element->scale_y;
                $elementData['original_width'] = $element->original_width;
                $elementData['original_height'] = $element->original_height;
            } elseif ($element->type === 'text') {
                $elementData['color'] = $element->color;
                $elementData['font_family'] = $element->font_family;
                $elementData['font_size'] = $element->width;
                $elementData['font_weight'] = $element->height;
                $elementData['scale_x'] = $element->scale_x;
                $elementData['scale_y'] = $element->scale_y;
                $meta = $element->metadata ?: [];
                $elementData['font_style'] = $meta['font_style'] ?? null;
                $elementData['text_align'] = $meta['text_align'] ?? null;
                $elementData['char_spacing'] = $meta['char_spacing'] ?? 0;
                $elementData['line_height'] = $meta['line_height'] ?? null;
                $elementData['underline'] = $meta['underline'] ?? false;
                $elementData['overline'] = $meta['overline'] ?? false;
                $elementData['linethrough'] = $meta['linethrough'] ?? false;
                $elementData['stroke'] = $meta['stroke'] ?? null;
                $elementData['stroke_width'] = $meta['stroke_width'] ?? 0;
                $elementData['shadow'] = $meta['shadow'] ?? null;
                $elementData['direction'] = $meta['direction'] ?? null;
                $elementData['width'] = $meta['textbox_width'] ?? null;
            } elseif ($element->type === 'asset') {
                $elementData['color'] = $element->color;
                $elementData['width'] = $element->width;
                $elementData['height'] = $element->height;
                $elementData['scale_x'] = $element->scale_x;
                $elementData['scale_y'] = $element->scale_y;
                $meta = $element->metadata ?: [];
                $elementData['_assetMeta'] = $meta['_assetMeta'] ?? null;
            } elseif ($element->type === 'badge') {
                $elementData['color'] = $element->color;
                $elementData['width'] = $element->width;
                $elementData['height'] = $element->height;
                $elementData['scale_x'] = $element->scale_x;
                $elementData['scale_y'] = $element->scale_y;
            } else {
                $elementData['color'] = $element->color;
                $elementData['font_family'] = $element->font_family;
            }

            $designsByView[$viewIndex][] = $elementData;
        }

        $designsArray = [];
        foreach ($designsByView as $viewIndex => $elements) {
            $printAreaId = collect($elements)->pluck('print_area_id')->first();
            $designsArray[] = [
                'view_index' => (int) $viewIndex,
                'print_area_id' => $printAreaId,
                'elements' => $elements,
            ];
        }

        $existingVariantData = [
            'size' => optional($design->variant)->size,
            'color' => optional($design->variant)->color,
            'variant_id' => $design->variant_id,
        ];

        return view('design.editor', [
            'product' => $product,
            'variant' => $design->variant,
            'existingDesign' => (object) [
                'id' => $design->id,
                'designs' => $designsArray,
            ],
            'existingVariantData' => $existingVariantData,
            'resubmit' => false,
            'admin_mode' => true,
            'admin_return_order' => $orderId,
            'admin_return_detail' => $detailId,
        ]);
    }

    public function reject(Request $request, $orderId, $detailId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'rejection_category' => 'required|string',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $order->status = 'cancelled';
        $order->rejection_category = $request->rejection_category;
        $order->rejection_reason = $request->rejection_reason ?? $request->rejection_category;
        $order->rejected_at = now();
        $order->save();

        return redirect()->route('admin.orders.design.show', [$orderId, $detailId])
            ->with('success', 'تم رفض التصميم بنجاح');
    }

    public function approve($orderId, $detailId)
    {
        $order = Order::findOrFail($orderId);

        $order->status = 'approved';
        $order->rejected_at = null;
        $order->rejection_reason = null;
        $order->rejection_category = null;
        $order->save();

        return redirect()->route('admin.orders.design.show', [$orderId, $detailId])
            ->with('success', 'تم اعتماد التصميم بنجاح');
    }
}
