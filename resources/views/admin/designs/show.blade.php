<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طلب #{{ $order->id }} — عرض التصميم | لوحة الإدارة</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('assets/frontend/images/logo/icon.png') }}">
<style>
    body {
        background: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, sans-serif;
        min-height: 100vh;
    }
    .admin-header {
        background: #1f2937;
        color: #fff;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .admin-header h5 {
        margin: 0;
        font-weight: 600;
    }
    .admin-header .back-link {
        color: #9ca3af;
        text-decoration: none;
        font-size: 14px;
    }
    .admin-header .back-link:hover {
        color: #fff;
    }
    .page-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }
    .design-meta-card {
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .design-meta-card .label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 600;
    }
    .design-meta-card .value {
        font-size: 15px;
        font-weight: 700;
        color: #212529;
    }
    .viewer-canvas-wrap {
        background: #f0f0f0;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        border: 2px dashed #ddd;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .viewer-canvas-wrap canvas {
        max-width: 100%;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    }
    .view-tabs .nav-link {
        font-weight: 600;
        color: #495057;
        border-radius: 8px 8px 0 0;
        padding: 10px 24px;
    }
    .view-tabs .nav-link.active {
        background: #ff6e26;
        color: #fff;
        border-color: #ff6e26;
    }
    .action-btn {
        border-radius: 10px;
        padding: 10px 24px;
        font-weight: 600;
        font-size: 14px;
        min-width: 120px;
        transition: all 0.2s;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .action-placeholder {
        border: 2px dashed #ccc;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        color: #999;
        font-size: 13px;
        background: #fafafa;
    }
    .print-area-chip {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 16px;
        background: #e8f4fd;
        color: #0c63e4;
        font-size: 12px;
        font-weight: 600;
        margin: 2px 4px;
    }
    .status-badge-lg {
        padding: 6px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 700;
    }
    .badge-approved {
        background: #28a745;
        color: #fff;
    }
    .badge-cancelled {
        background: #dc3545;
        color: #fff;
    }
    .badge-pending {
        background: #ffc107;
        color: #333;
    }
    .rejection-modal textarea {
        min-height: 100px;
        resize: vertical;
    }
    .admin-toolbar {
        background: #fff;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .admin-toolbar .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
</style>
</head>
<body>

<div class="admin-header">
    <div>
        <a href="{{ route('admin.orders.previousorder') }}" class="back-link">
            <i class="mdi mdi-arrow-right"></i> رجوع إلى الطلبات
        </a>
        <span style="margin:0 16px;color:#6b7280;">|</span>
        <span style="font-weight:600;font-size:15px;">طلب #{{ $order->id }} — عرض التصميم</span>
    </div>
    <div id="designStatusBadge">
        @if($order->isRejected())
        <span class="status-badge-lg badge-cancelled"><i class="mdi mdi-close-circle"></i> مرفوض</span>
        @elseif($order->isApproved())
        <span class="status-badge-lg badge-approved"><i class="mdi mdi-check-circle"></i> مقبول</span>
        @else
        <span class="status-badge-lg badge-pending"><i class="mdi mdi-clock"></i> قيد المراجعة</span>
        @endif
    </div>
</div>

<div class="page-wrapper">

    <div class="admin-toolbar">
        <a href="{{ route('admin.orders.design.edit', [$order->id, $detail->id]) }}" class="btn btn-warning">
            <i class="mdi mdi-pencil"></i> تعديل التصميم
        </a>
        <button class="btn btn-success" onclick="downloadAllPNG()">
            <i class="mdi mdi-file-image"></i> تحميل كل PNG
        </button>
        <button class="btn btn-danger" onclick="downloadPDF()">
            <i class="mdi mdi-file-pdf"></i> تحميل PDF
        </button>
    </div>

    <div class="design-meta-card">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="label">العميل</div>
                <div class="value">{{ $order->name }}</div>
            </div>
            <div class="col-md-2">
                <div class="label">البريد</div>
                <div class="value">{{ $order->email }}</div>
            </div>
            <div class="col-md-3">
                <div class="label">الهاتف</div>
                <div class="value">{{ $order->phone }}</div>
            </div>
            <div class="col-md-4">
                <div class="label">العنوان</div>
                <div class="value">{{ $order->address }}</div>
            </div>
        </div>
        <hr>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="label">المنتج</div>
                <div class="value">{{ $detail->product_name ?? $product->name }}</div>
            </div>
            <div class="col-md-1">
                <div class="label">المقاس</div>
                <div class="value">{{ $detail->size }}</div>
            </div>
            <div class="col-md-2">
                <div class="label">اللون</div>
                <div class="value">{{ $detail->color }}</div>
            </div>
            <div class="col-md-2">
                <div class="label">السعر</div>
                <div class="value">{{ number_format($detail->price, 2) }} جنيه</div>
            </div>
            <div class="col-md-2">
                <div class="label">الكمية</div>
                <div class="value">{{ $detail->quantity }}</div>
            </div>
            <div class="col-md-2">
                <div class="label">تاريخ الطلب</div>
                <div class="value">{{ $order->created_at->format('Y-m-d H:i') }}</div>
            </div>
        </div>
        @if($order->note)
        <div class="row g-3 mt-2">
            <div class="col-md-12">
                <div class="label">ملاحظات الطلب</div>
                <div class="value">{{ $order->note }}</div>
            </div>
        </div>
        @endif
        <div class="row g-3 mt-2">
            <div class="col-md-12">
                <div class="label">مناطق الطباعة</div>
                <div class="value">
                    @forelse ($product->printAreas as $area)
                    <span class="print-area-chip">{{ $area->name }} ({{ $area->width }}×{{ $area->height }})</span>
                    @empty
                    <span class="text-muted">لا توجد مناطق طباعة محددة</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @php
    $viewKeys = $elementsByView->keys()->sort()->values();
    @endphp

    <ul class="nav nav-tabs view-tabs mb-3" id="viewTabs" role="tablist">
        @foreach ($viewKeys as $vk)
        <li class="nav-item">
            <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="view-{{ $vk }}-tab" data-bs-toggle="tab"
                href="#view-{{ $vk }}-content" role="tab" onclick="switchView({{ $vk }})">
                {{ $viewPrintAreas[$vk] ?? ('مشهد ' . ($vk + 1)) }}
            </a>
        </li>
        @endforeach
    </ul>

    <div class="tab-content" id="viewTabsContent">
        @foreach ($viewKeys as $vk)
        <div class="tab-pane {{ $loop->first ? 'show active' : '' }}" id="view-{{ $vk }}-content" role="tabpanel">
            <div class="viewer-canvas-wrap">
                <canvas id="viewerCanvas-{{ $vk }}" width="500" height="500"></canvas>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row mt-4 g-3">
        <div class="col-md-4">
            <a href="{{ route('admin.orders.design.edit', [$order->id, $detail->id]) }}" class="btn btn-warning action-btn d-block w-100">
                <i class="mdi mdi-pencil"></i> تعديل التصميم
            </a>
        </div>
        <div class="col-md-4">
            @if(!$order->isApproved() && !$order->isRejected())
            <form method="POST" action="{{ route('admin.orders.design.approve', [$order->id, $detail->id]) }}" class="d-inline w-100">
                @csrf
                <button type="submit" class="btn btn-success action-btn d-block w-100" onclick="return confirm('تأكيد اعتماد التصميم؟')">
                    <i class="mdi mdi-check-circle"></i> اعتماد التصميم
                </button>
            </form>
            @elseif($order->isApproved())
            <div class="action-placeholder">
                <i class="mdi mdi-check-circle" style="font-size:24px;display:block;margin-bottom:8px;color:#28a745;"></i>
                <strong>✓ تم الاعتماد</strong>
            </div>
            @else
            <div class="action-placeholder">
                <i class="mdi mdi-close-circle" style="font-size:24px;display:block;margin-bottom:8px;color:#dc3545;"></i>
                <strong>✗ تم الرفض</strong>
            </div>
            @endif
        </div>
        <div class="col-md-4">
            @if(!$order->isApproved() && !$order->isRejected())
            <button type="button" class="btn btn-danger action-btn d-block w-100" data-bs-toggle="modal" data-bs-target="#rejectionModal">
                <i class="mdi mdi-close-circle"></i> رفض التصميم
            </button>
            @elseif($order->isRejected())
            <button type="button" class="btn btn-outline-danger action-btn d-block w-100" data-bs-toggle="modal" data-bs-target="#rejectionModal">
                <i class="mdi mdi-information"></i> عرض سبب الرفض
            </button>
            @else
            <div class="action-placeholder">
                <i class="mdi mdi-check-circle" style="font-size:24px;display:block;margin-bottom:8px;color:#28a745;"></i>
                <strong>✓ تم الاعتماد</strong>
            </div>
            @endif
        </div>
    </div>

    @if($order->isRejected())
    <div class="alert alert-danger mt-3">
        <strong><i class="mdi mdi-alert-circle"></i> سبب الرفض:</strong>
        {{ $order->rejection_reason }}
        <br>
        <small class="text-muted">التصنيف: {{ $order->rejectionCategoryLabel() }} — {{ $order->rejected_at->format('Y-m-d H:i') }}</small>
    </div>
    @endif
</div>

{{-- Rejection Modal --}}
<div class="modal fade rejection-modal" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('admin.orders.design.reject', [$order->id, $detail->id]) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectionModalLabel"><i class="mdi mdi-close-circle"></i> رفض الطلب</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_category">سبب الرفض</label>
                        <select name="rejection_category" id="rejection_category" class="form-control" required>
                            <option value="">-- اختر سبب الرفض --</option>
                            <option value="religious">محتوى ديني مخالف</option>
                            <option value="political">محتوى سياسي مخالف</option>
                            <option value="adult_content">محتوى إباحي</option>
                            <option value="copyright">انتهاك حقوق ملكية</option>
                            <option value="hate_speech">خطاب كراهية</option>
                            <option value="illegal_content">محتوى غير قانوني</option>
                            <option value="low_quality">جودة غير مناسبة للطباعة</option>
                            <option value="other">سبب آخر</option>
                        </select>
                    </div>
                    <div class="form-group" id="otherReasonGroup" style="display:none;">
                        <label for="rejection_reason">اكتب سبب الرفض...</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" placeholder="اكتب سبب الرفض..." maxlength="1000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('تأكيد رفض الطلب؟')">
                        <i class="mdi mdi-close-circle"></i> تأكيد الرفض
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script src="{{ asset('assets/js/design-art-icons.js') }}"></script>
<script>window.ZoomStore = window.ZoomStore || {}; ZoomStore.baseUrl = "{{ asset('') }}";</script>
<script src="{{ asset('assets/js/font-catalog.js') }}"></script>
<script src="{{ asset('assets/js/font-manager.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
    const productImages = @json($productImages);
    const elementsByView = @json($elementsByView);
    const viewPrintAreas = @json($viewPrintAreas);
    const orderId = {{ $order->id }};
    const customerName = @json($order->name);
    const productName = @json($detail->product_name ?? $product->name);
    const variantSize = @json($detail->size);
    const variantColor = @json($detail->color);
    const quantity = {{ $detail->quantity }};
    const productIndex = {{ $productIndex }};
    const allViewKeys = @json($viewKeys);

    let canvasInstances = {};
    let currentView = {{ $viewKeys->first() ?? 0 }};
    var _renderPromises = {};

    function applyCustomViewerControls(obj) {
        obj.set({
            hasControls: false, hasBorders: false, selectable: false, evented: false,
            lockMovementX: true, lockMovementY: true, lockRotation: true,
            lockScalingX: true, lockScalingY: true,
        });
    }

    function loadViewerCanvas(viewIndex) {
        const canvasId = 'viewerCanvas-' + viewIndex;
        const existing = canvasInstances[viewIndex];
        if (existing) {
            existing.dispose();
            delete canvasInstances[viewIndex];
        }

        const canvasEl = document.getElementById(canvasId);
        if (!canvasEl) { console.warn('Canvas element #' + canvasId + ' not found'); return; }

        const c = new fabric.Canvas(canvasId, {
            selection: false, preserveObjectStacking: true, width: 500, height: 500,
            backgroundColor: 'transparent', renderOnAddRemove: true,
            interactive: false, skipTargetFind: true,
        });

        c.selection = false;
        canvasInstances[viewIndex] = c;

        _renderPromises[viewIndex] = new Promise(function(resolve) {
            const bgSrc = productImages[viewIndex] || null;
            if (bgSrc) {
                fabric.Image.fromURL(bgSrc, function(img) {
                    if (img && c) {
                        c.setBackgroundImage(img, c.renderAll.bind(c), {
                            scaleX: c.width / img.width, scaleY: c.height / img.height,
                            crossOrigin: 'anonymous',
                        });
                        renderViewElements(c, viewIndex).then(resolve).catch(function(e) { console.warn(e); resolve(); });
                    } else {
                        renderViewElements(c, viewIndex).then(resolve).catch(function(e) { console.warn(e); resolve(); });
                    }
                }, { crossOrigin: 'anonymous' });
            } else {
                renderViewElements(c, viewIndex).then(resolve).catch(function(e) { console.warn(e); resolve(); });
            }
        });
    }

    function loadBadgeViewer(c, objData) {
        const iconKey = objData.content || objData._artKey || (objData._customSrc || '').replace('art://', '');
        const svgStr = window.DesignArtLib && DesignArtLib.svgIcons[iconKey];
        if (!svgStr) return;

        fabric.loadSVGFromString(svgStr, function(objects, options) {
            const obj = fabric.util.groupSVGElements(objects, options);
            obj.set({
                left: objData.position_x ?? 150, top: objData.position_y ?? 150,
                angle: objData.rotation ?? 0, scaleX: objData.scale_x ?? 1, scaleY: objData.scale_y ?? 1,
                originX: objData.origin_x ?? 'left', originY: objData.origin_y ?? 'top',
                stroke: objData.color ?? '#ffffff', fill: '',
            });
            applyCustomViewerControls(obj);
            c.add(obj);
            c.renderAll();
        });
    }

    async function renderViewElements(c, viewIndex) {
        const elements = elementsByView[viewIndex] || [];
        if (elements.length === 0) { c.renderAll(); return; }

        let loadPromises = [];

        // ---- Pre-load fonts before text creation ----
        if (window.ZoomStore && ZoomStore.FontManager) {
            var fontFamilies = {};
            elements.forEach(function(el) {
                if (el.type === 'text' && el.font_family) {
                    fontFamilies[el.font_family] = true;
                }
            });
            var familyKeys = Object.keys(fontFamilies);
            if (familyKeys.length > 0) {
                try {
                    await Promise.all(familyKeys.map(function(f) {
                        return ZoomStore.FontManager.loadFont(f);
                    }));
                } catch (e) {
                    console.warn('[FONT] Some fonts failed to load, continuing with fallbacks:', e);
                }
            }
        }

        elements.forEach(function(el) {
            if (el.type === 'text') {
                var meta = el.metadata || {};
                var textAlign = meta.text_align || 'center';
                var textboxWidth = meta.textbox_width || 200;
                var scaleX = typeof el.scale_x === 'number' ? el.scale_x : 1;
                var scaleY = typeof el.scale_y === 'number' ? el.scale_y : 1;
                const text = new fabric.Textbox(el.content ?? '', {
                    left: el.position_x ?? 150, top: el.position_y ?? 150,
                    fontSize: el.width ?? 20, fill: el.color ?? '#000',
                    fontFamily: el.font_family ?? 'Cairo',
                    fontWeight: el.height >= 700 ? 'bold' : 'normal',
                    fontStyle: meta.font_style || 'normal',
                    angle: el.rotation ?? 0,
                    originX: el.origin_x ?? 'left', originY: el.origin_y ?? 'top',
                    textAlign: textAlign,
                    width: textboxWidth,
                    scaleX: scaleX,
                    scaleY: scaleY,
                    charSpacing: meta.char_spacing || 0,
                    lineHeight: meta.line_height || 1.2,
                    underline: meta.underline || false,
                    overline: meta.overline || false,
                    linethrough: meta.linethrough || false,
                    stroke: meta.stroke || null,
                    strokeWidth: meta.stroke_width || 0,
                    direction: meta.direction || null,
                });
                applyCustomViewerControls(text);
                c.add(text);
            } else if (el.type === 'badge') {
                loadBadgeViewer(c, el);
            } else if (el.type === 'image') {
                let imgSrc = el.content;
                if (imgSrc && imgSrc.startsWith('data:image')) {
                    var p = new Promise(function(resolve) {
                        fabric.Image.fromURL(imgSrc, function(img) {
                            if (img) {
                                img.set({ left: el.position_x ?? 100, top: el.position_y ?? 100,
                                    angle: el.rotation ?? 0, scaleX: el.scale_x ?? 1, scaleY: el.scale_y ?? 1,
                                    originX: el.origin_x ?? 'left', originY: el.origin_y ?? 'top' });
                                applyCustomViewerControls(img);
                                c.add(img);
                            }
                            resolve();
                        }, { crossOrigin: 'anonymous' });
                    });
                    loadPromises.push(p);
                } else if (imgSrc) {
                    var p = new Promise(function(resolve) {
                        var fullSrc = imgSrc.startsWith('http') ? imgSrc : '{{ asset('') }}' + imgSrc;
                        fabric.Image.fromURL(fullSrc, function(img) {
                            if (img) {
                                img.set({ left: el.position_x ?? 100, top: el.position_y ?? 100,
                                    angle: el.rotation ?? 0, scaleX: el.scale_x ?? 1, scaleY: el.scale_y ?? 1,
                                    originX: el.origin_x ?? 'left', originY: el.origin_y ?? 'top' });
                                applyCustomViewerControls(img);
                                c.add(img);
                            }
                            resolve();
                        }, { crossOrigin: 'anonymous' });
                    });
                    loadPromises.push(p);
                }
            }
        });

        if (loadPromises.length > 0) {
            Promise.all(loadPromises).then(function() { c.renderAll(); });
        } else {
            c.renderAll();
        }
    }

    function ensureCanvasLoaded(viewIndex) {
        return new Promise(function(resolve, reject) {
            if (canvasInstances[viewIndex]) {
                var rp = _renderPromises[viewIndex];
                if (rp) {
                    rp.then(function() { resolve(canvasInstances[viewIndex]); }).catch(function() { resolve(canvasInstances[viewIndex]); });
                } else {
                    resolve(canvasInstances[viewIndex]);
                }
                return;
            }
            loadViewerCanvas(viewIndex);
            var elapsed = 0;
            var check = setInterval(function() {
                elapsed += 100;
                if (canvasInstances[viewIndex]) {
                    clearInterval(check);
                    var rp = _renderPromises[viewIndex];
                    if (rp) {
                        rp.then(function() { resolve(canvasInstances[viewIndex]); }).catch(function() { resolve(canvasInstances[viewIndex]); });
                    } else {
                        setTimeout(function() { resolve(canvasInstances[viewIndex]); }, 300);
                    }
                } else if (elapsed > 15000) {
                    clearInterval(check);
                    reject(new Error('View ' + viewIndex + ' canvas failed to load'));
                }
            }, 100);
        });
    }

    function sanitizeFileName(name) {
        return name.replace(/[^\p{L}\p{N}_\-]/gu, '_').replace(/_+/g, '_').replace(/^_|_$/g, '') || 'view';
    }

    async function downloadAllPNG() {
        if (typeof JSZip === 'undefined') { alert('جاري تحميل مكتبة ZIP، حاول مرة أخرى'); return; }

        var zip = new JSZip();
        var prefix = 'Order_' + orderId + '_Product_' + productIndex + '_';

        for (var i = 0; i < allViewKeys.length; i++) {
            var vk = allViewKeys[i];
            try {
                var c = await ensureCanvasLoaded(vk);
                var dataURL = c.toDataURL({ format: 'png', multiplier: 2 });
                var areaName = sanitizeFileName(viewPrintAreas[vk] || ('view_' + vk));
                var fileName = prefix + areaName + '.png';
                zip.file(fileName, dataURL.split(',')[1], { base64: true });
            } catch (e) {
                console.warn('Skipping view ' + vk + ': ' + e);
            }
        }

        var content = await zip.generateAsync({ type: 'blob' });
        saveAs(content, 'Order_' + orderId + '_Product_' + productIndex + '.zip');
    }

    async function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('portrait', 'px', [500, 720]);

        for (var i = 0; i < allViewKeys.length; i++) {
            var vk = allViewKeys[i];
            if (i > 0) pdf.addPage();

            var c;
            try {
                c = await ensureCanvasLoaded(vk);
            } catch (e) {
                console.warn('Skipping view ' + vk + ' for PDF: ' + e);
                continue;
            }
            var areaName = viewPrintAreas[vk] || ('view-' + vk);

            pdf.setFont('Helvetica', 'bold');
            pdf.setFontSize(14);
            pdf.text('Order #' + orderId, 20, 25);

            pdf.setFont('Helvetica', 'normal');
            pdf.setFontSize(11);
            pdf.text('Customer: ' + customerName, 20, 42);
            pdf.text('Product: ' + productName, 20, 57);
            pdf.text('Size: ' + variantSize + '   Color: ' + variantColor + '   Qty: ' + quantity, 20, 72);
            pdf.text('Print Area: ' + areaName, 20, 87);

            pdf.setDrawColor(200);
            pdf.line(20, 95, 480, 95);

            var dataURL = c.toDataURL({ format: 'png', multiplier: 2 });
            pdf.addImage(dataURL, 'PNG', 40, 105, 420, 420);
        }

        pdf.save('Order_' + orderId + '_Product_' + productIndex + '_Full.pdf');
    }

    function switchView(viewIndex) {
        currentView = viewIndex;
        if (!canvasInstances[viewIndex]) {
            loadViewerCanvas(viewIndex);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // ── Initialize FontManager ──
        if (window.ZoomStore && ZoomStore.FontManager) {
            // Re-render text when a web font finishes loading
            ZoomStore.FontManager.onFontReady = function(family) {
                Object.keys(canvasInstances).forEach(function(vk) {
                    var c = canvasInstances[vk];
                    if (!c) return;
                    var objects = c.getObjects();
                    var needsRender = false;
                    for (var i = 0; i < objects.length; i++) {
                        if (objects[i].fontFamily === family) {
                            if (typeof objects[i].initDimensions === 'function') {
                                objects[i].initDimensions();
                            }
                            if (typeof objects[i].setCoords === 'function') {
                                objects[i].setCoords();
                            }
                            needsRender = true;
                        }
                    }
                    if (needsRender) c.renderAll();
                });
            };
            ZoomStore.FontManager.init(['Josefin Sans', 'Cairo', 'Open Sans']);
        }

        allViewKeys.forEach(function(vk) {
            loadViewerCanvas(vk);
        });

        var catSelect = document.getElementById('rejection_category');
        var otherGroup = document.getElementById('otherReasonGroup');
        var reasonInput = document.getElementById('rejection_reason');
        if (catSelect) {
            catSelect.addEventListener('change', function() {
                if (this.value === 'other') {
                    otherGroup.style.display = 'block';
                    reasonInput.setAttribute('required', 'required');
                } else {
                    otherGroup.style.display = 'none';
                    reasonInput.removeAttribute('required');
                }
            });
        }
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
