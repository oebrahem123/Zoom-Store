<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintArea;
use App\Models\Product;
use Illuminate\Http\Request;

class PrintAreaController extends Controller
{
    public function edit($productId)
    {
        $product = Product::with('printAreas', 'productphotos')->findOrFail($productId);

        $views = [
            'front' => 'أمامي',
            'back' => 'خلفي',
            'left_sleeve' => 'كم أيسر',
            'right_sleeve' => 'كم أيمن',
            'hood' => 'هود',
            'pocket' => 'جيب',
        ];

        // Build per-view image map from product photos
        $imagesByView = [];

        foreach ($views as $key => $label) {
            // Try to find a photo matching this view_name
            $photo = $product->productphotos
                ->where('view_name', $key)
                ->first();

            if ($photo && $photo->imagepath) {
                $imagesByView[$key] = asset($photo->imagepath);
            } else {
                $imagesByView[$key] = null;
            }
        }

        return view('admin.print-areas.editor', compact('product', 'views', 'imagesByView'));
    }

    public function save(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $data = $request->validate([
            'areas' => 'required|array',
            'areas.*.id' => 'nullable|integer|exists:print_areas,id',
            'areas.*.view_name' => 'required|string',
            'areas.*.view_index' => 'nullable|integer',
            'areas.*.name' => 'required|string|max:255',
            'areas.*.area_type' => 'nullable|string|max:255',
            'areas.*.slot_key' => 'nullable|string|max:255',
            'areas.*.slot_type' => 'nullable|string|max:255',
            'areas.*.comment' => 'nullable|string|max:1000',
            'areas.*.x' => 'required|numeric',
            'areas.*.y' => 'required|numeric',
            'areas.*.width' => 'required|numeric|min:1',
            'areas.*.height' => 'required|numeric|min:1',
            'deleted_ids' => 'nullable|array',
            'deleted_ids.*' => 'integer|exists:print_areas,id',
        ]);

        // Delete removed areas
        if (!empty($data['deleted_ids'])) {
            PrintArea::whereIn('id', $data['deleted_ids'])
                ->where('product_id', $productId)
                ->delete();
        }

        // Upsert areas
        $createdIds = [];
        foreach ($data['areas'] as $index => $area) {
            $areaData = [
                'product_id' => $productId,
                'view_name' => $area['view_name'],
                'view_index' => $area['view_index'] ?? null,
                'name' => $area['name'],
                'area_type' => $area['area_type'] ?? null,
                'slot_key' => $area['slot_key'] ?? null,
                'slot_type' => $area['slot_type'] ?? null,
                'comment' => $area['comment'] ?? null,
                'x' => $area['x'],
                'y' => $area['y'],
                'width' => $area['width'],
                'height' => $area['height'],
            ];

            if (!empty($area['id'])) {
                PrintArea::where('id', $area['id'])
                    ->where('product_id', $productId)
                    ->update($areaData);
                $createdIds[$index] = null;
            } else {
                $newArea = PrintArea::create($areaData);
                $createdIds[$index] = $newArea->id;
            }
        }

        return response()->json([
            'success' => true,
            'created_ids' => $createdIds,
        ]);
    }
}
