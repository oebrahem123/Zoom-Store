@extends('admin.layout')

@section('content')
<style>
    .editor-layout {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }
    .canvas-wrap {
        flex: 1;
        max-width: 560px;
    }
    .canvas-wrap canvas {
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    .sidebar {
        width: 300px;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 16px;
        border: 1px solid #ddd;
    }
    .view-btn {
        display: block;
        width: 100%;
        padding: 10px 14px;
        margin-bottom: 6px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #fff;
        text-align: right;
        cursor: pointer;
        font-size: 14px;
        transition: all .15s;
    }
    .view-btn:hover {
        background: #e9ecef;
    }
    .view-btn.active {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }
    .view-btn .count {
        float: left;
        background: #dee2e6;
        color: #333;
        border-radius: 10px;
        padding: 0 8px;
        font-size: 12px;
    }
    .view-btn.active .count {
        background: rgba(255,255,255,.3);
        color: #fff;
    }
    .toolbar {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }
    .toolbar button {
        padding: 6px 14px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background: #fff;
        cursor: pointer;
        font-size: 13px;
    }
    .toolbar button:hover {
        background: #e9ecef;
    }
    .toolbar button.primary {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }
    .toolbar button.danger {
        color: #dc3545;
        border-color: #dc3545;
    }
    .toolbar button.danger:hover {
        background: #dc3545;
        color: #fff;
    }
    .toolbar button.success {
        background: #28a745;
        color: #fff;
        border-color: #28a745;
    }
    h4 {
        margin-top: 0;
        font-size: 18px;
    }
    .legend {
        margin-top: 12px;
        font-size: 13px;
        color: #666;
    }
    .instructions {
        font-size: 13px;
        color: #666;
        margin-bottom: 12px;
        line-height: 1.6;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>🎨 تحرير مناطق الطباعة — {{ $product->name }}</h3>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-secondary">← رجوع للمنتج</a>
    </div>

    <div class="editor-layout">

        {{-- Canvas --}}
        <div class="canvas-wrap">
            <div class="toolbar">
                <button type="button" id="drawModeBtn" class="primary">✏️ رسم منطقة</button>
                <button type="button" id="deleteSelectedBtn" class="danger">🗑️ حذف المحدد</button>
                <button type="button" id="clearAllBtn" class="danger">مسح الكل</button>
                <button type="button" id="saveBtn" class="success">💾 حفظ</button>
            </div>
            <canvas id="fabricCanvas" width="500" height="500"></canvas>
            <div class="legend">
                🟧 <strong>برتقالي</strong> = المنطقة المحددة حالياً |
                🟦 <strong>أزرق</strong> = مناطق أخرى في نفس العرض
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="sidebar">
            <h4>عرض المنتج</h4>
            <p class="instructions">
                اختر عرض المنتج (أمامي، خلفي، كم أيسر، كم أيمن) لإدارة مناطق الطباعة الخاصة به.
                <br><br>
                <strong>الرسم:</strong> اختر "رسم منطقة" ثم اسحب على الصورة لرسم مستطيل.
                <br>
                <strong>التعديل:</strong> انقر على مستطيل لاختياره، ثم اسحب الحواف لتغيير الحجم.
                <br>
                <strong>البيانات:</strong> انقر نقراً مزدوجاً على مستطيل لفتح نافذة الإعدادات.
            </p>

            <div id="viewButtons">
                @foreach($views as $key => $label)
                <button type="button" class="view-btn" data-view="{{ $key }}">
                    {{ $label }}
                    <span class="count" id="count-{{ $key }}">0</span>
                </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Area config modal --}}
<div class="modal fade" id="areaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header">
                <h5 class="modal-title">⚙️ إعدادات المنطقة</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="areaForm">
                    <input type="hidden" id="areaId">
                    <div class="form-group mb-3">
                        <label>اسم المنطقة</label>
                        <input type="text" id="areaName" class="form-control" placeholder="مثال: الصدر الأيسر" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>نوع المنطقة</label>
                        <select id="areaType" class="form-control">
                            <option value="">— اختر —</option>
                            <option value="logo">شعار (Logo Area)</option>
                            <option value="artwork">تصميم فني (Artwork Area)</option>
                            <option value="text">نص (Text Area)</option>
                            <option value="sleeve">كم (Sleeve Area)</option>
                            <option value="pocket">جيب (Pocket Area)</option>
                            <option value="full_print">طباعة كاملة (Full Print Area)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>ملاحظات</label>
                        <textarea id="areaComment" class="form-control" rows="3" placeholder="ملاحظات اختيارية"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="saveAreaConfig">حفظ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================================
        // Data
        // ============================================================
        var defaultView = 'front';
        var currentView = defaultView;

        // Per-view images
        var imagesByView = {};
        @foreach($views as $key => $label)
        imagesByView['{{ $key }}'] = '{{ $imagesByView[$key] ?? '' }}';
        @endforeach

        // All areas grouped by view_name
        var areasByView = {};

        @foreach($views as $key => $label)
        areasByView['{{ $key }}'] = [];
        @endforeach

        // Load existing areas
        @foreach($product->printAreas as $area)
        if (!areasByView['{{ $area->view_name ?? "front" }}']) {
            areasByView['{{ $area->view_name ?? "front" }}'] = [];
        }
        areasByView['{{ $area->view_name ?? "front" }}'].push({
            id: {{ $area->id ?? 'null' }},
            view_name: '{{ $area->view_name ?? "front" }}',
            name: '{{ $area->name }}',
            area_type: '{{ $area->area_type ?? "" }}',
            comment: '{{ addslashes($area->comment ?? "") }}',
            x: {{ $area->x }},
            y: {{ $area->y }},
            width: {{ $area->width }},
            height: {{ $area->height }}
        });
        @endforeach

        function updateCounts() {
            for (var v in areasByView) {
                var el = document.getElementById('count-' + v);
                if (el) el.textContent = areasByView[v].length;
            }
        }

        var placeholderTexts = [];

        function showNoImagePlaceholder() {
            if (placeholderTexts.length) return;
            var lines = [
                {text: 'لم يتم رفع صورة', y: 230},
                {text: 'لهذا العرض', y: 260}
            ];
            lines.forEach(function(l) {
                var t = new fabric.Text(l.text, {
                    left: 250, top: l.y,
                    fontSize: 18, fill: '#bbbbbb',
                    fontFamily: 'Tahoma, Arial, sans-serif',
                    textAlign: 'center', originX: 'center', originY: 'top',
                    selectable: false, evented: false, excludeFromExport: true
                });
                canvas.add(t);
                placeholderTexts.push(t);
            });
        }

        // ============================================================
        // Fabric.js Canvas
        // ============================================================
        var canvas = new fabric.Canvas('fabricCanvas', {
            selection: false,
            preserveObjectStacking: true,
            width: 500,
            height: 500,
            backgroundColor: '#f0f0f0'
        });

        var bgImage = null;
        var drawMode = false;
        var isDrawing = false;
        var startX, startY;
        var tempRect = null;
        var areaLabel = null;

        // Load background image for a given view
        function loadBackground(src, callback) {
            if (!src) { if (callback) callback(); return; }
            fabric.Image.fromURL(src, function(img) {
                bgImage = img;
                var scale = Math.min(500 / img.width, 500 / img.height);
                img.set({
                    left: (500 - img.width * scale) / 2,
                    top: (500 - img.height * scale) / 2,
                    scaleX: scale,
                    scaleY: scale,
                    selectable: false,
                    evented: false,
                    excludeFromExport: true
                });
                canvas.add(img);
                canvas.sendToBack(img);
                canvas.renderAll();
                if (callback) callback();
            }, {crossOrigin: 'anonymous'});
        }
        // Background is loaded by showView() during init below

        // ============================================================
        // Area label: show name on selection
        // ============================================================
        function updateAreaLabel(selected) {
            if (!areaLabel || !areaLabel.canvas) {
                areaLabel = null;
                areaLabel = new fabric.Text('', {
                    fontSize: 13,
                    fill: '#ffffff',
                    fontFamily: 'Tahoma, Arial, sans-serif',
                    backgroundColor: 'rgba(0,0,0,0.6)',
                    padding: 4,
                    selectable: false,
                    evented: false,
                    excludeFromExport: true
                });
                canvas.add(areaLabel);
            }
            if (selected && selected._areaData && selected._areaData.name) {
                var name = selected._areaData.name;
                var bounds = selected.getBoundingRect();
                areaLabel.set({
                    text: name,
                    left: bounds.left,
                    top: bounds.top - 28,
                    visible: true
                });
                areaLabel.bringToFront();
            } else {
                areaLabel.set({ visible: false });
            }
            canvas.renderAll();
        }

        canvas.on('selection:created', function(e) {
            updateAreaLabel(e.selected ? e.selected[0] : null);
        });
        canvas.on('selection:updated', function(e) {
            updateAreaLabel(e.selected ? e.selected[0] : null);
        });
        canvas.on('selection:cleared', function() {
            updateAreaLabel(null);
        });
        // Also fire on object modification (drag/resize)
        canvas.on('object:modified', function(e) {
            if (e.target && e.target._areaData) {
                updateAreaLabel(e.target);
            }
        });
        canvas.on('object:removed', function(e) {
            if (e.target && e.target._areaData) {
                updateAreaLabel(null);
            }
        });

        // ============================================================
        // Drawing
        // ============================================================
        var drawModeBtn = document.getElementById('drawModeBtn');

        drawModeBtn.addEventListener('click', function() {
            drawMode = !drawMode;
            drawModeBtn.textContent = drawMode ? '🔴 إيقاف الرسم' : '✏️ رسم منطقة';
            drawModeBtn.classList.toggle('primary', !drawMode);
            drawModeBtn.classList.toggle('btn-danger', drawMode);
            canvas.selection = !drawMode;
            if (drawMode) {
                canvas.defaultCursor = 'crosshair';
            } else {
                canvas.defaultCursor = 'default';
            }
        });

        canvas.on('mouse:down', function(opt) {
            if (!drawMode) return;
            var pointer = canvas.getPointer(opt.e);
            isDrawing = true;
            startX = pointer.x;
            startY = pointer.y;

            tempRect = new fabric.Rect({
                left: startX,
                top: startY,
                width: 0,
                height: 0,
                fill: 'rgba(0, 123, 255, 0.15)',
                stroke: '#007bff',
                strokeWidth: 2,
                selectable: false,
                evented: false
            });
            canvas.add(tempRect);
        });

        canvas.on('mouse:move', function(opt) {
            if (!isDrawing || !tempRect) return;
            var pointer = canvas.getPointer(opt.e);
            var w = pointer.x - startX;
            var h = pointer.y - startY;

            if (w > 0) {
                tempRect.set('left', startX);
                tempRect.set('width', w);
            } else {
                tempRect.set('left', pointer.x);
                tempRect.set('width', Math.abs(w));
            }
            if (h > 0) {
                tempRect.set('top', startY);
                tempRect.set('height', h);
            } else {
                tempRect.set('top', pointer.y);
                tempRect.set('height', Math.abs(h));
            }
            canvas.renderAll();
        });

        canvas.on('mouse:up', function(opt) {
            if (!isDrawing || !tempRect) return;
            isDrawing = false;
            drawMode = false;
            drawModeBtn.textContent = '✏️ رسم منطقة';
            drawModeBtn.classList.add('primary');
            drawModeBtn.classList.remove('btn-danger');
            canvas.selection = true;
            canvas.defaultCursor = 'default';

            var rect = tempRect;
            tempRect = null;

            if (rect.width < 5 || rect.height < 5) {
                canvas.remove(rect);
                canvas.renderAll();
                return;
            }

            // Convert to a permanent zone rect
            var zone = makeZoneRect(rect.left, rect.top, rect.width, rect.height, null);
            canvas.remove(rect);
            canvas.add(zone);

            // Add to data
            var areaData = {
                id: null,
                view_name: currentView,
                name: 'منطقة جديدة',
                area_type: '',
                comment: '',
                x: zone.left,
                y: zone.top,
                width: zone.width * zone.scaleX,
                height: zone.height * zone.scaleY
            };
            zone._areaData = areaData;
            areasByView[currentView].push(areaData);
            updateCounts();

            canvas.renderAll();

            // Open config modal
            openAreaConfig(zone);
        });

        // ============================================================
        // Zone rectangle factory
        // ============================================================
        function makeZoneRect(left, top, width, height, areaData) {
            var fillColor = 'rgba(0, 123, 255, 0.1)';
            var strokeColor = '#007bff';

            var rect = new fabric.Rect({
                left: left,
                top: top,
                width: width,
                height: height,
                fill: fillColor,
                stroke: strokeColor,
                strokeWidth: 2,
                strokeUniform: true,
                cornerColor: '#007bff',
                cornerSize: 8,
                transparentCorners: false,
                cornerStyle: 'circle',
                borderColor: '#ff6e26',
                borderScaleFactor: 2,
                padding: 4,
                hasRotatingPoint: false,
                lockRotation: true
            });

            if (areaData) {
                rect._areaData = areaData;
            }

            // Double-click to open config
            rect.on('mousedblclick', function() {
                openAreaConfig(this);
            });

            // Track changes on modification
            rect.on('modified', function() {
                syncAreaData(this);
            });

            return rect;
        }

        function syncAreaData(rect) {
            if (!rect._areaData) return;
            rect._areaData.x = rect.left;
            rect._areaData.y = rect.top;
            rect._areaData.width = rect.width * rect.scaleX;
            rect._areaData.height = rect.height * rect.scaleY;
        }

        // ============================================================
        // View switching
        // ============================================================
        var viewSwitchCallId = 0;

        function showView(viewName) {
            currentView = viewName;
            viewSwitchCallId++;
            var myCallId = viewSwitchCallId;

            // Update button states
            document.querySelectorAll('.view-btn').forEach(function(btn) {
                btn.classList.toggle('active', btn.dataset.view === viewName);
            });

            // Remove all objects (zones + background)
            canvas.clear();
            areaLabel = null;
            placeholderTexts = [];
            canvas.backgroundColor = '#f0f0f0';

            // Load background for this view, then draw zones
            var viewImage = imagesByView[viewName];
            loadBackground(viewImage, function() {
                // Guard: discard if a newer showView call already ran
                if (viewSwitchCallId !== myCallId) return;

                // Draw zones for this view on top of new background
                var areas = areasByView[viewName] || [];
                areas.forEach(function(area) {
                    var rect = makeZoneRect(area.x, area.y, area.width, area.height, area);
                    canvas.add(rect);
                });
                // Show placeholder when no view-specific image
                if (!viewImage) {
                    showNoImagePlaceholder();
                }
                canvas.renderAll();
            });
        }

        // View button clicks
        document.querySelectorAll('.view-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                showView(this.dataset.view);
            });
        });

        // ============================================================
        // Area config modal
        // ============================================================
        var selectedZone = null;

        function openAreaConfig(zone) {
            selectedZone = zone;
            var data = zone._areaData || {};

            document.getElementById('areaId').value = data.id || '';
            document.getElementById('areaName').value = data.name || '';
            document.getElementById('areaType').value = data.area_type || '';
            document.getElementById('areaComment').value = data.comment || '';

            $('#areaModal').modal('show');
        }

        document.getElementById('saveAreaConfig').addEventListener('click', function() {
            if (!selectedZone) return;

            var name = document.getElementById('areaName').value.trim();
            if (!name) {
                alert('يرجى إدخال اسم المنطقة');
                return;
            }

            var data = selectedZone._areaData;
            data.name = name;
            data.area_type = document.getElementById('areaType').value;
            data.comment = document.getElementById('areaComment').value;

            // Update label immediately if this zone is currently selected
            var active = canvas.getActiveObject();
            if (active && active._areaData === data) {
                updateAreaLabel(active);
            }

            $('#areaModal').modal('hide');
            selectedZone = null;
        });

        // ============================================================
        // Delete selected zone
        // ============================================================
        document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
            var active = canvas.getActiveObject();
            if (!active || !active._areaData) {
                alert('الرجاء اختيار منطقة لحذفها');
                return;
            }

            if (!confirm('حذف هذه المنطقة؟')) return;

            var view = active._areaData.view_name;
            var idx = areasByView[view].indexOf(active._areaData);
            if (idx > -1) areasByView[view].splice(idx, 1);

            canvas.remove(active);
            canvas.discardActiveObject();

            if (areaLabel && areaLabel.canvas) {
                areaLabel.set({ visible: false });
            }

            canvas.renderAll();
            updateCounts();
        });

        // ============================================================
        // Clear all zones for current view
        // ============================================================
        document.getElementById('clearAllBtn').addEventListener('click', function() {
            if (!confirm('مسح جميع مناطق الطباعة في عرض "' + currentView + '"؟')) return;

            areasByView[currentView] = [];

            canvas.discardActiveObject();
            var objects = canvas.getObjects();
            for (var i = objects.length - 1; i >= 0; i--) {
                if (objects[i]._areaData && objects[i]._areaData.view_name === currentView) {
                    canvas.remove(objects[i]);
                }
            }
            if (areaLabel) {
                canvas.remove(areaLabel);
                areaLabel = null;
            }
            canvas.renderAll();
            updateCounts();
        });

        // ============================================================
        // Save
        // ============================================================
        document.getElementById('saveBtn').addEventListener('click', function() {
            var allAreas = [];
            var deletedIds = [];

            for (var view in areasByView) {
                areasByView[view].forEach(function(area) {
                    allAreas.push({
                        id: area.id || null,
                        view_name: view,
                        name: area.name || 'منطقة',
                        area_type: area.area_type || '',
                        comment: area.comment || '',
                        x: area.x,
                        y: area.y,
                        width: area.width,
                        height: area.height
                    });
                });
            }

            // Collect IDs not in the new data (for deletion)
            @foreach($product->printAreas as $area)
            var found = allAreas.some(function(a) { return a.id === {{ $area->id }}; });
            if (!found) {
                deletedIds.push({{ $area->id }});
            }
            @endforeach

            var saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.textContent = '⏳ جاري الحفظ...';

            fetch('{{ route("admin.products.print-areas.save", $product->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    areas: allAreas,
                    deleted_ids: deletedIds
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    if (data.created_ids) {
                        var flatIdx = 0;
                        for (var view in areasByView) {
                            areasByView[view].forEach(function(area) {
                                if (!area.id && data.created_ids[flatIdx]) {
                                    area.id = data.created_ids[flatIdx];
                                }
                                flatIdx++;
                            });
                        }
                    }
                    saveBtn.textContent = '✅ تم الحفظ';
                    setTimeout(function() {
                        saveBtn.textContent = '💾 حفظ';
                        saveBtn.disabled = false;
                    }, 2000);
                } else {
                    alert('خطأ في الحفظ');
                    saveBtn.textContent = '💾 حفظ';
                    saveBtn.disabled = false;
                }
            })
            .catch(function() {
                alert('خطأ في الاتصال');
                saveBtn.textContent = '💾 حفظ';
                saveBtn.disabled = false;
            });
        });

        // ============================================================
        // Init
        // ============================================================
        updateCounts();
        showView(defaultView);
    });
</script>
@endsection