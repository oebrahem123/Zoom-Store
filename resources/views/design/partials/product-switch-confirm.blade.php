{{-- Product Switch Confirmation Modal --}}
{{-- Receives confirmationData from ProductSwitchConfirmationService --}}
{{-- Never displays internal slot_key — only Display Names --}}
<div class="modal fade" id="productSwitchConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered p-t-75" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden;" dir="rtl">
            <div class="modal-header" style="background:#fff3cd; border-bottom:1px solid #ffc107;">
                <h5 class="modal-title d-flex align-items-center gap-2" style="color:#856404;">

                    تأكيد التبديل
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"
                    id="switchConfirmClose"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3" style="color:#333; font-size:15px; line-height:1.8;">
                    المنتج المحدد لا يدعم بعض المناطق المستخدمة في تصميمك الحالي.
                </p>

                <div id="switchConfirmMissingSlots" class="mb-3"></div>

                <div class="d-flex gap-3 mb-3" id="switchConfirmCounts">
                    <div class="flex-fill p-3 rounded" style="background:#f8f9fa;">
                        <div class="fw-bold" style="color:#dc3545; font-size:20px;" id="switchConfirmAffected">0</div>
                        <div style="color:#666; font-size:13px;">عنصر متأثر</div>
                    </div>
                    <div class="flex-fill p-3 rounded" style="background:#f8f9fa;">
                        <div class="fw-bold" style="color:#28a745; font-size:20px;" id="switchConfirmUnaffected">0</div>
                        <div style="color:#666; font-size:13px;">عنصر غير متأثر</div>
                    </div>
                </div>

                <div class="p-3 rounded" style="background:#e7f3ff; border:1px solid #b8daff;">
                    <p class="mb-0" style="color:#004085; font-size:14px; line-height:1.7;">
                        سيتم إزالة العناصر التابعة للمناطق غير المدعومة فقط. جميع العناصر الأخرى ستبقى كما هي.
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e9ecef;" dir="ltr">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" id="switchConfirmCancel">
                    إلغاء
                </button>
                <button type="button" class="btn px-4" style="background:#f28123; color:#fff; border:none;"
                    id="switchConfirmContinue">
                    متابعة
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #productSwitchConfirmModal .missing-slot-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        margin-bottom: 6px;
        background: #fff5f5;
        border-radius: 8px;
        border: 1px solid #fecaca;
    }

    #productSwitchConfirmModal .missing-slot-item:last-child {
        margin-bottom: 0;
    }

    #productSwitchConfirmModal .missing-slot-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fecaca;
        color: #dc3545;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }

    #productSwitchConfirmModal .missing-slot-name {
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
</style>

<script>
    (function() {
    'use strict';

    var confirmCallback = null;

    /**
     * Show the product switch confirmation dialog.
     *
     * @param {Object} data - From ProductSwitchConfirmationService::analyzeSwitch()
     * @param {Function} callback - Called with true (continue) or false (cancel)
     */
    window.showProductSwitchConfirm = function(data, callback) {
        confirmCallback = callback || null;

        if (!data.needs_confirmation) {
            if (confirmCallback) confirmCallback(true);
            return;
        }

        var slotsHtml = '';
        if (data.missing_slot_names && data.missing_slot_names.length > 0) {
            slotsHtml = '<div style="margin-bottom:8px; font-weight:600; color:#856404; font-size:14px;">المناطق غير المدعومة:</div>';
            for (var i = 0; i < data.missing_slot_names.length; i++) {
                slotsHtml += '<div class="missing-slot-item">';
                slotsHtml += '  <div class="missing-slot-icon">✕</div>';
                slotsHtml += '  <div class="missing-slot-name">' + escapeHtml(data.missing_slot_names[i]) + '</div>';
                slotsHtml += '</div>';
            }
        }

        document.getElementById('switchConfirmMissingSlots').innerHTML = slotsHtml;
        document.getElementById('switchConfirmAffected').textContent = data.affected_count || 0;
        document.getElementById('switchConfirmUnaffected').textContent = data.unaffected_count || 0;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = new bootstrap.Modal(document.getElementById('productSwitchConfirmModal'));
            modal.show();
        } else if (typeof jQuery !== 'undefined' || typeof $ !== 'undefined') {
            $('#productSwitchConfirmModal').modal('show');
        } else {
            document.getElementById('productSwitchConfirmModal').style.display = 'block';
            document.getElementById('productSwitchConfirmModal').classList.add('in');
            document.body.classList.add('modal-open');
        }
    };

    document.getElementById('switchConfirmCancel').addEventListener('click', function() {
        closeConfirmModal();
        if (confirmCallback) confirmCallback(false);
    });

    document.getElementById('switchConfirmContinue').addEventListener('click', function() {
        closeConfirmModal();
        if (confirmCallback) confirmCallback(true);
    });

    document.getElementById('switchConfirmClose').addEventListener('click', function() {
        closeConfirmModal();
        if (confirmCallback) confirmCallback(false);
    });

    function closeConfirmModal() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(document.getElementById('productSwitchConfirmModal'));
            if (modal) modal.hide();
        } else if (typeof jQuery !== 'undefined' || typeof $ !== 'undefined') {
            $('#productSwitchConfirmModal').modal('hide');
        } else {
            document.getElementById('productSwitchConfirmModal').style.display = '';
            document.getElementById('productSwitchConfirmModal').classList.remove('in');
            document.body.classList.remove('modal-open');
        }
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
})();
</script>
