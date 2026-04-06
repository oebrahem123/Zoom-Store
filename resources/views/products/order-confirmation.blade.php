@extends('layouts.master')

@section('content')

<style>
   /* حدد لونك البرتقالي المميز (يمكنك تغييره) */
:root {
    --orange-color: #f28123; /* اللون البرتقالي الأساسي */
}


.btn-outline-custom-orange {
    color: var(--orange-color);
    border: 1px solid var(--orange-color);
    background-color: transparent;
    border-radius: 7px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    display: inline-flex;
    align-items: center;
}

.btn-outline-custom-orange .fas {
    margin-left: 8px;
    font-size: 1rem;
}
.btn-outline-custom-orange:hover,
.btn-outline-custom-orange:focus {
    box-shadow: 0 2px 6px rgba(73, 59, 55, 0.6);
    transform: translateY(-3px);
    color: var(--orange-color);
    border-color: var(--orange-color);
    background-color: transparent;
}
.order-status-page {
    direction: rtl;
    text-align: right;
    font-family: "Tajawal", sans-serif;
}

.order-number-title {
    font-weight: 700;
    font-size: 26px;
    margin-bottom: 10px;
}

/* Timeline */
.order-timeline-wrap {
    position: relative;
    padding-right: 30px;
    margin-top: 10px;
}

.order-timeline-wrap::before {
content: '';
    position: absolute;
    top: 0;
    right: 39px;
    width: 2px;
    height: 90%;
    background-color: #e0e0e0;
}

.timeline-item {
    position: relative;
    padding-right: 40px;
    margin-bottom: 45px;
}

.timeline-dot {
    position: absolute;
    top: 0;
    right: -1px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background-color: #cfcfcf;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 11px;
    font-weight: bold;
    border: 2px solid #fff;
    box-shadow: 0 0 3px #888;
}

.timeline-item.completed .timeline-dot { background-color: #28a745; }
.timeline-item.active .timeline-dot { background-color: #ffc107; color: #333; }

.timeline-content h5 { font-size: 1.1rem; font-weight: bold; margin-bottom: 4px; }
.timeline-content p { font-size: 0.9rem; color: #666; margin: 0; }
.timeline-date { color: #777; font-size: 0.85rem; margin-top: 5px; }

/* Map */
.map-container {
    height: 400px;
    border: 1px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
</style>

<div class="container order-status-page py-5">

    {{-- Header --}}
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h2 class="order-number-title">حالة الطلب رقم :{{ $order->id }}</h2>
            <small class="text-muted">
                وسيلة الدفع: <strong>الدفع عند الاستلام</strong> —
                بتاريخ {{ $order->created_at->format('d-m-Y h:i A') }}
            </small>
        </div>
       <div class="col-md-6 text-left">
    <a href="tel:000000000" class="btn btn-outline-custom-orange px-4 py-2">
        <i class="fas fa-phone-alt"></i>
        خدمة العملاء
    </a>
</div>
    </div>

    <div class="row g-5">

        {{-- Timeline --}}
        <div class="col-lg-6">
            <div class="order-timeline-wrap">
                <div class="timeline-item completed">
                    <div class="timeline-dot">&#10003;</div>
                    <div class="timeline-content">
                        <h5>تم استلام الطلب</h5>
                        <p>استلمنا طلبك وجاري مراجعته.</p>
                        <div class="timeline-date">{{ $order->created_at->format('d-m-Y h:i A') }}</div>
                    </div>
                </div>

                <div class="timeline-item active">
                    <div class="timeline-dot">&#x23F3;</div>
                    <div class="timeline-content">
                        <h5>جاري التجهيز</h5>
                        <p>يتم تجهيز المنتجات الآن.</p>
                    </div>
                </div>

                <div class="timeline-item pending">
                    <div class="timeline-dot">&#128666;</div>
                    <div class="timeline-content">
                        <h5>تم الشحن</h5>
                        <p>طلبك في الطريق إليك.</p>
                    </div>
                </div>

                <div class="timeline-item pending">
                    <div class="timeline-dot">&#127968;</div>
                    <div class="timeline-content">
                        <h5>تم التوصيل</h5>
                        <p>وصل الطلب إلى عنوانك.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Map --}}
        <div class="col-lg-6">
            <div id="map" class="map-container"></div>
        </div>
    </div>
</div>

{{-- Leaflet JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<!-- رابط أيقونات FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let address = @json($order->address);

    // خريطة ابتدائية (مركز مؤقت) بدون zoom controls و attribution
    const map = L.map('map', {
        center: [30.0444, 31.2357], // القاهرة كمركز افتراضي
        zoom: 12,
        zoomControl: false,          // يشيل أزرار + و -
        attributionControl: false    // يشيل النص في الأسفل
    });

    // إضافة الطبقة
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);

    // تحويل العنوان إلى إحداثيات باستخدام Nominatim
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const lat = data[0].lat;
                const lon = data[0].lon;
                map.setView([lat, lon], 15);
                L.marker([lat, lon]).addTo(map)
                    .bindPopup("موقع التوصيل")
                    .openPopup();
            } else {
                alert("تعذر تحديد موقع العنوان على الخريطة.");
            }
        })
        .catch(err => console.error(err));
});
</script>



@endsection
