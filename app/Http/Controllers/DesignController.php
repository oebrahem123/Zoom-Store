<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomDesign;
use App\Models\DesignElement;
use App\Models\Order;
use App\Models\orderdetails;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Order\OrderResubmitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    public function store(Request $request)
    {
        $isAdmin = auth('admin')->check();
        if (! auth()->check() && ! $isAdmin) {
            return response()->json(['error' => 'يجب تسجيل الدخول أولًا لحفظ التصميم.'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'view' => 'required|string',
            'preview_image' => 'nullable|string',
            'elements' => 'array',
            'designs' => 'array',
        ]);

        try {
            $design = DB::transaction(function () use ($request, $isAdmin) {
                if ($request->has('design_id') && $request->design_id) {
                    $design = CustomDesign::findOrFail($request->design_id);
                    if (! $request->input('admin_mode') && $design->user_id !== auth()->id() && ! $isAdmin) {
                        abort(403);
                    }

                    $design->update([
                        'product_id' => $request->product_id,
                        'variant_id' => $request->variant_id,
                        'view' => $request->view,
                        'preview_image' => is_string($request->preview_image)
                            ? $this->storeBase64Image($request->preview_image)
                            : $request->preview_image,
                    ]);

                    // Admin mode: archive the customer's original elements (once) plus audit metadata,
                    // then fall through so the admin's edits are applied to the elements table.
                    // Snapshot is an audit/archive object only - never a rendering source.
                    if ($request->input('admin_mode')) {
                        $snapshot = $design->snapshot ?? [];

                        // original_elements is immutable: capture only on the first admin edit.
                        if (! array_key_exists('original_elements', $snapshot)) {
                            $snapshot['original_elements'] = $this->serializeElements($design);
                        }

                        $snapshot['admin_user_id'] = Auth::guard('admin')->id();
                        $snapshot['edited_at'] = now()->toISOString();
                        if ($request->filled('change_summary')) {
                            $snapshot['change_summary'] = $request->input('change_summary');
                        }

                        $design->snapshot = $snapshot;
                        $design->save();

                        \Log::debug('[ADMIN_SNAPSHOT] design_id='.$design->id.' original_elements archived, audit metadata written, elements will be updated');
                    }

                    \Log::debug('[FLOW_TRACE_DB] PRE-DELETE: design_id='.$design->id.' elements count='.$design->elements()->count());
                    \Log::debug('[FLOW_TRACE_DB] PRE-DELETE elements: '.$design->elements()->get()->map(function ($e) {
                        return 'id='.$e->id.' type='.$e->type.' view='.$e->view.' content='.($e->content ? substr($e->content, 0, 20) : 'null');
                    })->join(' | '));

                    $deletedCount = $design->elements()->delete();

                    \Log::debug('[FLOW_TRACE_DB] DELETE: design_id='.$design->id.' deleted='.$deletedCount);
                } else {
                    $design = CustomDesign::create([
                        'user_id' => auth()->id(),
                        'product_id' => $request->product_id,
                        'variant_id' => $request->variant_id,
                        'view' => $request->view,
                        'preview_image' => is_string($request->preview_image)
                            ? $this->storeBase64Image($request->preview_image)
                            : $request->preview_image,
                    ]);
                }

                // حفظ العناصر
                $allElements = [];

                if ($request->has('designs') && is_array($request->designs)) {
                    foreach ($request->designs as $viewDesign) {
                        $viewIndex = $viewDesign['view_index'] ?? 0;
                        $printAreaId = $viewDesign['print_area_id'] ?? null;
                        foreach ($viewDesign['elements'] as $el) {
                            $allElements[] = array_merge($el, ['view_index' => $viewIndex, 'print_area_id' => $printAreaId]);
                        }
                    }
                } elseif ($request->has('elements') && is_array($request->elements)) {
                    foreach ($request->elements as $viewData) {
                        $viewIndex = $viewData['view_index'] ?? 0;
                        $printAreaId = $viewData['print_area_id'] ?? null;
                        foreach ($viewData['elements'] as $el) {
                            $allElements[] = array_merge($el, ['view_index' => $viewIndex, 'print_area_id' => $printAreaId]);
                        }
                    }
                }

                \Log::debug('[FLOW_TRACE_DB] RECEIVED PAYLOAD: design_id='.($design->id ?? 'new').' designs_count='.count($request->designs ?? []).' total_elements='.count($allElements));
                \Log::debug('[FLOW_TRACE_DB] PAYLOAD per view: '.collect($request->designs ?? [])->map(function ($vd) {
                    $elems = collect($vd['elements'] ?? []);

                    return 'view'.($vd['view_index'] ?? '?').':'.$elems->count().'elements ['.$elems->map(function ($e) {
                        return ($e['_debugId'] ?? 'no-id').'/'.($e['type'] ?? '?').(($e['font_family'] ?? null) ? '/'.$e['font_family'] : '').($e['content'] ? '/'.substr($e['content'], 0, 15) : '');
                    })->join(',').']';
                })->join(' | '));
                \Log::debug('[FLOW_TRACE_DB] ALL _debugIds in payload: ['.collect($allElements)->pluck('_debugId')->join(',').']');

                foreach ($allElements as $el) {
                    $content = $el['content'] ?? null;
                    $type = $el['type'] ?? 'unknown';

                    if ($type === 'image' && is_string($content) && Str::startsWith($content, 'data:image')) {
                        $content = $this->storeBase64Image($content);
                    }

                    // تحضير البيانات الأساسية
                    $elementData = [
                        'design_id' => $design->id,
                        'type' => $type,
                        'content' => $content,
                        'position_x' => $el['position_x'] ?? 0,
                        'position_y' => $el['position_y'] ?? 0,
                        'rotation' => $el['rotation'] ?? 0,
                        'z_index' => $el['z_index'] ?? 0,
                        'view' => $el['view_index'] ?? 0,
                        'print_area_id' => $el['print_area_id'] ?? null,
                        'origin_x' => $el['origin_x'] ?? null,
                        'origin_y' => $el['origin_y'] ?? null,
                    ];

                    // إضافة بيانات الصورة إذا وجدت
                    if ($type === 'image' || $type === 'Image') {
                        $elementData['width'] = $el['width'] ?? null;
                        $elementData['height'] = $el['height'] ?? null;
                        $elementData['scale_x'] = $el['scale_x'] ?? null;
                        $elementData['scale_y'] = $el['scale_y'] ?? null;
                        $elementData['original_width'] = $el['original_width'] ?? null;
                        $elementData['original_height'] = $el['original_height'] ?? null;
                    }
                    // إضافة بيانات النص (width=font_size, height=font_weight)
                    elseif ($type === 'text') {
                        $elementData['color'] = $el['color'] ?? null;
                        $elementData['font_family'] = $el['font_family'] ?? null;
                        $elementData['width'] = $el['font_size'] ?? null;
                        $elementData['height'] = isset($el['font_weight']) ? (float) $el['font_weight'] : null;
                        $elementData['scale_x'] = $el['scale_x'] ?? null;
                        $elementData['scale_y'] = $el['scale_y'] ?? null;
                        $elementData['metadata'] = [
                            'font_style' => $el['font_style'] ?? null,
                            'text_align' => $el['text_align'] ?? null,
                            'char_spacing' => $el['char_spacing'] ?? 0,
                            'line_height' => $el['line_height'] ?? null,
                            'underline' => $el['underline'] ?? false,
                            'overline' => $el['overline'] ?? false,
                            'linethrough' => $el['linethrough'] ?? false,
                            'stroke' => $el['stroke'] ?? null,
                            'stroke_width' => $el['stroke_width'] ?? 0,
                            'shadow' => $el['shadow'] ?? null,
                            'direction' => $el['direction'] ?? null,
                            'textbox_width' => $el['width'] ?? null,
                        ];
                        // Remove nulls to keep metadata lean
                        $elementData['metadata'] = array_filter($elementData['metadata'], function ($v) {
                            return $v !== null;
                        });
                    }
                    // إضافة بيانات الرسم (asset / badge)
                    elseif ($type === 'asset' || $type === 'badge') {
                        $elementData['color'] = $el['color'] ?? null;
                        $elementData['width'] = $el['width'] ?? null;
                        $elementData['height'] = $el['height'] ?? null;
                        $elementData['scale_x'] = $el['scale_x'] ?? null;
                        $elementData['scale_y'] = $el['scale_y'] ?? null;
                        if ($type === 'asset' && isset($el['_assetMeta'])) {
                            $elementData['metadata'] = array_merge(
                                $elementData['metadata'] ?? [],
                                ['_assetMeta' => $el['_assetMeta']]
                            );
                        }
                    } else {
                        $elementData['color'] = $el['color'] ?? null;
                        $elementData['font_family'] = $el['font_family'] ?? null;
                    }

                    $created = DesignElement::create($elementData);
                    \Log::debug('[FLOW_TRACE_DB] INSERT: id='.$created->id.' debugId='.(isset($el['_debugId']) ? $el['_debugId'] : 'N/A').' type='.$type.' view='.($el['view_index'] ?? 0).' content='.($content ? substr($content, 0, 20) : 'null').' font_family='.($el['font_family'] ?? 'N/A'));
                }

                \Log::debug('[FLOW_TRACE_DB] POST-INSERT: design_id='.$design->id.' total_inserted='.count($allElements).' db_elements_count='.$design->elements()->count());

                if ($request->input('resubmit') && $design->id) {
                    $detail = orderdetails::where('design_id', $design->id)->first();
                    if ($detail) {
                        $order = Order::find($detail->order_id);
                        if ($order && $order->isRejected()) {
                            \Log::debug('[AUDIT_ORDER_STATUS] DesignController@store (resubmit inline)', [
                                'order_id' => $order->id,
                                'from' => $order->status,
                                'to' => 'pending_review',
                                'controller' => 'DesignController@store',
                                'file' => 'DesignController.php',
                                'has_canTransition_check' => false,
                                'via' => 'resubmit_inline',
                            ]);
                            $order->update([
                                'status' => 'pending_review',
                                'rejection_reason' => null,
                                'rejection_category' => null,
                                'rejected_at' => null,
                            ]);
                        }
                    }
                }

                return $design;
            });

            $orderId = null;
            if ($request->input('resubmit')) {
                $detail = orderdetails::where('design_id', $design->id)->first();
                if ($detail) {
                    $orderId = $detail->order_id;
                }
            }

            return response()->json([
                'message' => 'Design saved successfully',
                'design_id' => $design->id,
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function storeBase64Image(string $dataUrl): string
    {
        if (! preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $matches)) {
            return $dataUrl;
        }

        $extension = strtolower($matches[1]);
        $allowed = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
        if (! in_array($extension, $allowed, true)) {
            $extension = 'png';
        }

        $raw = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $raw = str_replace(' ', '+', $raw);
        $binary = base64_decode($raw);

        if ($binary === false) {
            return $dataUrl;
        }

        $directory = public_path('uploads/design-elements');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = Str::uuid().'.'.$extension;
        $fullPath = $directory.DIRECTORY_SEPARATOR.$filename;
        file_put_contents($fullPath, $binary);

        return 'uploads/design-elements/'.$filename;
    }

    /**
     * Serialize a design's elements into the grouped-by-view payload shape
     * used by the editor (view_index / print_area_id / elements[]).
     * Only used to archive the immutable original_elements snapshot.
     */
    private function serializeElements(CustomDesign $design): array
    {
        $elementsByView = [];
        foreach ($design->elements as $element) {
            $viewIndex = $element->view ?? 0;
            if (! isset($elementsByView[$viewIndex])) {
                $elementsByView[$viewIndex] = [];
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
            } elseif ($element->type === 'asset' || $element->type === 'badge') {
                $elementData['color'] = $element->color;
                $elementData['width'] = $element->width;
                $elementData['height'] = $element->height;
                $elementData['scale_x'] = $element->scale_x;
                $elementData['scale_y'] = $element->scale_y;
                $meta = $element->metadata ?: [];
                $elementData['_assetMeta'] = $meta['_assetMeta'] ?? null;
            } else {
                $elementData['color'] = $element->color;
                $elementData['font_family'] = $element->font_family;
            }

            $elementsByView[$viewIndex][] = $elementData;
        }

        $designs = [];
        foreach ($elementsByView as $viewIndex => $elements) {
            $printAreaId = collect($elements)->pluck('print_area_id')->first();
            $designs[] = [
                'view_index' => (int) $viewIndex,
                'print_area_id' => $printAreaId,
                'elements' => $elements,
            ];
        }

        return $designs;
    }

    public function editor($variantId)
    {
        $variant = ProductVariant::with('product', 'product.variants')->findOrFail($variantId);
        $product = $variant->product;
        $product->load('printAreas');

        $designableProducts = Product::with('variants', 'productphotos')
            ->where('is_designable', true)
            ->where('id', '!=', $product->id)
            ->whereHas('variants', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->get()
            ->map(function ($p) {
                $variant = $p->variants->firstWhere('quantity', '>', 0);

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => $p->price,
                    'image' => $p->imagepath ? asset(str_replace('\\', '/', $p->imagepath)) : null,
                    'editor_url' => $variant ? url('/design/'.$variant->id) : null,
                ];
            })->values();

        return view('design.editor', compact('variant', 'product', 'designableProducts'));
    }

    public function getProductData($productId)
    {
        $product = Product::with('variants', 'productphotos', 'printAreas')->findOrFail($productId);

        $variant = $product->variants->firstWhere('quantity', '>', 0);

        $imageData = $product->getEditorImageData();
        $viewMapping = $product->getEditorViewMapping();

        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'variant_id' => $variant ? $variant->id : null,
            'variants' => $product->variants->values(),
            'variants_data' => $product->getEditorVariantsData(),
            'base_images' => $imageData['base_images'],
            'base_images_urls' => array_map(function ($img) {
                return asset($img);
            }, $imageData['base_images']),
            'color_images' => $imageData['color_images'],
            'design_areas' => $product->design_areas,
            'view_names' => $viewMapping['view_names'],
            'color_view_names' => $viewMapping['color_view_names'],
            'areas_by_view' => $product->getEditorAreasByView(),
        ]);
    }

    public function getDesignableProducts($productId)
    {
        $designableProducts = Product::with('variants', 'productphotos')
            ->where('is_designable', true)
            ->where('id', '!=', $productId)
            ->whereHas('variants', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->get()
            ->map(function ($p) {
                $variant = $p->variants->firstWhere('quantity', '>', 0);

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => $p->price,
                    'image' => $p->imagepath ? asset(str_replace('\\', '/', $p->imagepath)) : null,
                    'editor_url' => $variant ? url('/design/'.$variant->id) : null,
                ];
            })->values();

        return response()->json($designableProducts);
    }

    /**
     * Switch product — single endpoint for the full pipeline.
     *
     * Runs SlotMatchingService, ProductSwitchConfirmationService, and
     * CoordinateTransformationService server-side. The browser never
     * holds business state — only sends source/target IDs and serialized objects.
     *
     * Matching + transformation are always computed (cheap, no DB writes).
     * The client decides whether to apply the result based on confirmation.
     */
    public function switchProduct(Request $request)
    {
        \Log::info('SWITCH REQUEST', [
            'content_type' => $request->header('Content-Type'),
            'raw' => $request->getContent(),
            'all' => $request->all(),
        ]);

        $request->validate([
            'source_product_id' => 'required|integer|exists:products,id',
            'target_product_id' => 'required|integer|exists:products,id',
            // Product switching is allowed even when the editor is empty.
            // "objects" must exist in the request but may legitimately be an empty array.
            'objects' => 'present|array',
            'views' => 'sometimes|array',
            'views.*.objects' => 'present|array',
        ]);

        $source = Product::with('printAreas')->findOrFail($request->source_product_id);
        $target = Product::with('printAreas')->findOrFail($request->target_product_id);

        $matchingService = new \App\Services\SlotMatchingService;
        $matchingResult = $matchingService->matchSlots($source, $target);

        $confirmationService = new \App\Services\ProductSwitchConfirmationService;
        $confirmationData = $confirmationService->analyzeSwitch($matchingResult, $request->objects);

        $transformService = new \App\Services\CoordinateTransformationService;
        $transformed = $transformService->transformObjects(
            $request->objects,
            $source,
            $target,
            $matchingResult
        );

        $transformedViews = null;
        if ($request->filled('views')) {
            $transformedViews = [];

            // Build target slot_key lookup once for all views
            $targetSlotKeys = [];
            foreach ($target->printAreas as $area) {
                if (! empty($area->slot_key)) {
                    $targetSlotKeys[$area->slot_key] = true;
                }
            }

            foreach ($request->views as $viewIndex => $viewData) {
                $transformed = $transformService->transformObjects(
                    $viewData['objects'],
                    $source,
                    $target,
                    $matchingResult
                );

                // Safeguard: verify each transformed object's _slotKey exists in target
                foreach ($transformed as $obj) {
                    $sk = $obj['_slotKey'] ?? null;
                    if ($sk !== null && ! isset($targetSlotKeys[$sk])) {
                        \Log::warning('[SWITCH][MISSING_SLOT]', [
                            '_zoomObjectId' => $obj['_zoomObjectId'] ?? null,
                            '_slotKey' => $sk,
                            'type' => $obj['type'] ?? 'unknown',
                            'source_product_id' => $source->id,
                            'target_product_id' => $target->id,
                        ]);
                    }
                }

                $transformedViews[(string) $viewIndex] = $transformed;
            }
        }

        return response()->json([
            'confirmation' => $confirmationData,
            'transformed' => $transformed,
            'transformed_views' => $transformedViews,
        ]);
    }

    public function start(Request $request)
    {
        $query = Product::with('variants', 'productphotos');

        $cartProductIds = [];
        if (auth()->check()) {
            $cartProductIds = \App\Models\Cart::where('user_id', auth()->id())
                ->pluck('product_id')
                ->toArray();
        }

        $query->where('is_designable', true);

        $query->where(function ($q) use ($cartProductIds) {
            $q->whereHas('variants', function ($q2) {
                $q2->where('quantity', '>', 0);
            })
                ->orDoesntHave('variants');

            if (! empty($cartProductIds)) {
                $q->orWhereIn('id', $cartProductIds);
            }
        });

        if ($request->filled('catid')) {
            $query->where('category_id', $request->catid);
        }

        if ($request->filled('color')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('color', $request->color);
            });
        }

        if ($request->filled('price')) {
            if ($request->price == '500+') {
                $query->where('price', '>=', 500);
            } else {
                [$min, $max] = explode('-', $request->price);
                $query->whereBetween('price', [(int) $min, (int) $max]);
            }
        }

        switch ($request->sort) {
            case 'low-high':
                $query->orderBy('price', 'asc');
                break;
            case 'high-low':
                $query->orderBy('price', 'desc');
                break;
            case 'new':
                $query->latest();
                break;
        }

        $products = $query->paginate(8)->withQueryString();
        $categories = Category::all();

        return view('design.start', compact('products', 'categories'));
    }

    public function search(Request $request)
    {
        $products = Product::with('variants', 'productphotos')
            ->where('is_designable', true)
            ->where('name', 'like', '%'.$request->searchkey.'%')
            ->paginate(8);

        $categories = Category::all();

        return view('design.start', compact('products', 'categories'));
    }

    public function edit($designId)
    {
        \Log::debug('[FLOW_TRACE] DesignController@edit called', [
            'design_id' => $designId,
            'url' => request()->fullUrl(),
            'query' => request()->query->all(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
            'referer' => request()->header('referer'),
        ]);

        $design = CustomDesign::with('elements', 'product', 'variant')
            ->findOrFail($designId);

        \Log::debug('[FLOW_TRACE] Design loaded', [
            'design_id' => $design->id,
            'element_count' => $design->elements->count(),
            'element_types' => $design->elements->pluck('type')->toArray(),
            'element_ids' => $design->elements->pluck('id')->toArray(),
            'variant_id' => $design->variant_id,
            'variant_exists' => $design->relationLoaded('variant') && $design->variant ? true : false,
            'product_id' => $design->product_id,
            'product_exists' => $design->relationLoaded('product') && $design->product ? true : false,
        ]);

        if ($design->user_id !== auth()->id()) {
            abort(403);
        }

        // تنظيم العناصر حسب المنظر (view_index)
        $designsByView = [];
        foreach ($design->elements as $element) {
            $viewIndex = $element->view ?? 0;
            if (! isset($designsByView[$viewIndex])) {
                $designsByView[$viewIndex] = [];
            }

            // تجهيز بيانات العنصر كاملة
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

            // إضافة بيانات الصورة إذا كانت موجودة
            if ($element->type === 'image') {
                $elementData['width'] = $element->width;
                $elementData['height'] = $element->height;
                $elementData['scale_x'] = $element->scale_x;
                $elementData['scale_y'] = $element->scale_y;
                $elementData['original_width'] = $element->original_width;
                $elementData['original_height'] = $element->original_height;
            }
            // إضافة بيانات النص أو الرسم
            elseif ($element->type === 'text') {
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

        // تحويل المصفوفة إلى تنسيق designs
        $designsArray = [];
        foreach ($designsByView as $viewIndex => $elements) {
            $printAreaId = collect($elements)->pluck('print_area_id')->first();
            $designsArray[] = [
                'view_index' => (int) $viewIndex,
                'print_area_id' => $printAreaId,
                'elements' => $elements,
            ];
        }

        // تجهيز بيانات المنتج والصور
        $product = $design->product;

        $existingVariantData = [
            'size' => optional($design->variant)->size,
            'color' => optional($design->variant)->color,
            'variant_id' => $design->variant_id,
        ];

        \Log::debug('[FLOW_TRACE] existingDesign being sent to view', [
            'design_id' => $design->id,
            'designs_count' => count($designsArray),
            'designs' => json_encode($designsArray),
            'existingVariant' => json_encode($existingVariantData),
            'cart_item_id' => request('cart_item_id'),
        ]);

        $resubmit = request()->has('resubmit');

        return view('design.editor', [
            'product' => $product,
            'variant' => $design->variant,
            'existingDesign' => (object) [
                'id' => $design->id,
                'designs' => $designsArray,
            ],
            'existingVariantData' => $existingVariantData,
            'resubmit' => $resubmit,
        ]);
    }

    public function myDesigns(Request $request)
    {
        $userId = auth()->id();

        $orderedIds = orderdetails::whereNotNull('design_id')->pluck('design_id')->unique()->toArray();

        $query = CustomDesign::where('user_id', $userId)
            ->with('product', 'variant');

        // Search
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhereHas('product', fn ($pq) => $pq->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('variant', fn ($vq) => $vq->where('size', 'LIKE', "%{$search}%")
                        ->orWhere('color', 'LIKE', "%{$search}%"));
            });
        }

        // Filter ordered status
        if ($request->ordered === 'ordered') {
            $query->whereIn('id', $orderedIds);
        } elseif ($request->ordered === 'not_ordered') {
            $query->whereNotIn('id', $orderedIds);
        }

        // Filter product type
        if ($type = $request->product_type) {
            $query->whereHas('product', fn ($pq) => $pq->where('name', 'LIKE', "%{$type}%"));
        }

        // Sort
        $sort = $request->sort === 'oldest' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sort);

        $perPage = $request->per_page ?? 12;
        $designs = $query->paginate($perPage);

        // Stats
        $totalDesigns = CustomDesign::where('user_id', $userId)->count();
        $orderedCount = CustomDesign::where('user_id', $userId)->whereIn('id', $orderedIds)->count();
        $savedCount = $totalDesigns - $orderedCount;
        $lastModified = CustomDesign::where('user_id', $userId)->latest('updated_at')->first();

        $viewMode = $request->view ?? 'grid';

        if ($request->ajax()) {
            $html = view('design.partials.design-cards', compact('designs', 'orderedIds'))->render();

            return response()->json([
                'html' => $html,
                'next_page_url' => $designs->nextPageUrl(),
                'has_more' => $designs->hasMorePages(),
            ]);
        }

        return view('design.my-designs', compact(
            'designs', 'totalDesigns', 'orderedCount', 'savedCount', 'lastModified', 'viewMode', 'orderedIds'
        ));
    }

    public function duplicate(CustomDesign $design)
    {
        if ($design->user_id !== auth()->id()) {
            abort(403);
        }

        $duplicate = $design->replicate();
        $duplicate->save();

        foreach ($design->elements as $element) {
            $newElement = $element->replicate();
            $newElement->design_id = $duplicate->id;
            $newElement->save();
        }

        return redirect()->route('designs.my', ['highlight' => $duplicate->id])
            ->with('success', '✅ تم إنشاء نسخة من التصميم');
    }

    public function destroy(CustomDesign $design)
    {
        if ($design->user_id !== auth()->id()) {
            abort(403);
        }

        $design->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('designs.my')->with('success', '✅ تم حذف التصميم');
    }

    public function rename(Request $request, CustomDesign $design)
    {
        if ($design->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['name' => 'required|string|max:255']);
        $design->update(['name' => $request->name]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'name' => $request->name]);
        }

        return redirect()->route('designs.my')->with('success', '✅ تم تحديث اسم التصميم');
    }

    public function resubmit(Request $request, orderdetails $detail)
    {
        $order = \App\Models\Order::findOrFail($detail->order_id);
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            OrderResubmitService::resubmit($detail);

            return redirect()->back()->with('success', 'تم إعادة تقديم التصميم للمراجعة');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
