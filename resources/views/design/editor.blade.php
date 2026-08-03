@php
$layout = (isset($admin_mode) && $admin_mode) ? 'layouts.admin_editor' : 'layouts.master';
@endphp
@extends($layout)

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --sidebar-n-bg: #1f1c1d;
        --main-bg: #f3f3f3;
        --border-color: #e1e1e1;
        --accent-blue: #ff6e26;
    }

    .coloo {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        flex: 1;
        height: 7rem;
        padding: .5rem .25rem .75rem;
        background-color: #f4f4f4;
        background-image: none;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
    }

    .org {
        height: 2rem;
        width: 100%;
        background-repeat: no-repeat;
        background-position: center;
        scale: 2.0;
    }

    .designer-container {
        display: flex;
        min-height: calc(63vh - 54px);
    }

    /* sidebar-n */
    .sidebar-n {
        width: 69px;
        background-color: #222;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 66px;
        z-index: 1000;
        flex-shrink: 0;
    }

    .new-tag {
        background: white;
        color: #ff5b1f;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 10px;
        border-radius: 20px;
        margin-bottom: 15px;
    }

    .nav-item-n {
        width: 100%;
        background: none;
        border: none;
        color: #d8d8d8;
        padding: 18px 5px;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 13px;
        cursor: pointer;
        border-left: 4px solid transparent;
        transition: 0.2s;
    }

    .nav-item-n.active {
        background-color: white;
        color: #222;
        border-right: 4px solid var(--accent-blue);
    }

    .panel-v {
        width: 100%;
        max-width: 500px;
        background-color: white;
        border: 1px solid var(--border-color);
        border-right: none;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .panel-v-header {
        height: 65px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 15px;
        position: relative;
    }

    .panel-v-header #header-title {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        font-weight: bold;
        font-size: 16px;
    }

    .btn-header {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #555;
        padding: 5px;
    }

    .panel-v-body {
        padding: 25px;
        overflow-y: auto;
        flex-grow: 1;
    }

    /* Grid Home */
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .icon-wrapper {
        border: 1px solid #6f7480;
        border-radius: 8px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .icon-wrapper i {
        font-size: 45px;
        color: var(--accent-blue);
    }

    .bor10 {
        width: 100% !important;
        margin-top: 50px !important;
        clear: both !important;
    }

    .feature-card {
        cursor: pointer;
        text-align: center;
    }

    .feature-card:hover .icon-wrapper {
        border-color: var(--accent-blue);
        background: #f8f9ff;
    }

    /* Content Styling */
    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
    }

    .main-title {
        font-size: 26px;
        font-weight: 700;
        text-align: right;
        margin-bottom: 35px;
    }

    /* Upload Area */
    .upload-zone {
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        margin-bottom: 25px;
    }

    .btn-upload-main {
        background: var(--accent-blue);
        color: white;
        font-weight: 700;
        padding: 10px 25px;
    }

    /* Tools Styling (Product & Text) */
    .color-swatch {
        width: 25px;
        height: 25px;
        border-radius: 50px;
        border: 1px solid #ddd;
        cursor: pointer;
    }

    .color-swatch.active {
        outline: 2px solid var(--accent-blue);
        outline-offset: 2px;
    }

    .size-btn {
        border: 1px solid #ddd;
        background: white;
        padding: 3px 8px;
        border-radius: 5px;
        font-weight: 600;
    }

    .tool-label {
        font-weight: 700;
        margin-bottom: 10px;
        display: block;
        font-size: 14px;
    }

    /* Preview Area */
    .preview-pane {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #eee;
    }

    .tshirt-mockup {
        max-width: 80%;
        mix-blend-mode: multiply;
    }

    /* =============================================
FIX #2: Canvas Overlay — يجبر المستخدم يختار مقاس ولون أول
============================================= */
    #canvasOverlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.55);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 999;
        border-radius: 4px;
        cursor: pointer;
        transition: opacity 0.3s;
    }

    #canvasOverlay .overlay-icon {
        font-size: 48px;
        color: #fff;
        margin-bottom: 12px;
    }

    #canvasOverlay .overlay-text {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        font-family: 'Cairo', sans-serif;
        text-align: center;
        padding: 0 20px;
    }

    #canvasOverlay .overlay-btn {
        margin-top: 16px;
        background: var(--accent-blue);
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
    }

    @media (max-width: 767px) {
        .designer-container {
            flex-direction: column-reverse;
        }
    }

    /* Start destion */

    /* =========================================
Artwork Categories
========================================= */



    /* Header */

    .artwork-header {
        height: 60px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        flex-shrink: 0;
    }

    .artwork-header h3 {
        font-size: 16px;
        font-weight: 500;
        margin: 0;
        color: #333;
        letter-spacing: 1px;
    }

    .close-art-popup {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        font-size: 24px;
        color: #333;
        cursor: pointer;
    }

    /* Search */

    .art-search-wrapper {
        position: relative;
        padding: 18px;
        flex-shrink: 0;
    }

    .art-search-wrapper i {
        position: absolute;
        left: 35px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 22px;
    }

    .art-search-wrapper input {
        width: 100%;
        height: 62px;
        border-radius: 10px;
        border: 2px solid #355cff;
        outline: none;
        padding-left: 60px;
        font-size: 17px;
        color: #333;
        background: #fff;
    }

    .art-search-wrapper input::placeholder {
        color: #bbb;
    }

    /* Scroll Area */

    .artwork-scroll {
        overflow-y: auto;
        padding: 0 18px 20px;
        flex-grow: 1;
    }

    /* Scrollbar */

    .artwork-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .artwork-scroll::-webkit-scrollbar-thumb {
        background: #cfcfcf;
        border-radius: 20px;
    }

    /* Grid */

    .artwork-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    /* Fixed height scrollable details panel */
    .panel-v {
        height: calc(81vh - 177px);
        max-height: 680px;
        min-height: 420px;
    }


    @media (min-width: 992px) and (max-width: 1366px) {
        .panel-v {
            min-height: 500px;
        }
    }

    .panel-v-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
        -webkit-overflow-scrolling: touch;
    }

    .panel-v-body::-webkit-scrollbar {
        width: 5px;
    }

    .panel-v-body::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    /* Text & Art controls */
    .btn-add-design {
        background: var(--accent-blue);
        color: #fff;
        font-weight: 700;
        border: none;
        border-radius: 10px;
        padding: 14px;
        width: 100%;
        font-size: 16px;
        cursor: pointer;
    }

    .btn-bold-toggle {
        border: 2px solid #ddd;
        background: #fff;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-bold-toggle.active {
        background: #222;
        color: #fff;
        border-color: #222;
    }

    /* ── Panel tool controls (text / AI) ── */
    .panel-v-body .main-title {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 14px;
    }

    .panel-v-body .tool-label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #555;
    }

    .panel-v-body .tool-hint {
        font-size: 12px;
        color: #888;
        margin-bottom: 10px;
    }

    .nav-item-n i {
        font-size: 19px;
        line-height: 1;
    }

    .nav-item-n span {
        font-size: 10px;
        margin-top: 3px;
    }

    .tool-input {
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
        width: 100%;
        background: #fafafa;
        transition: border-color 0.2s;
    }

    .tool-input:focus {
        outline: none;
        border-color: var(--accent-blue);
        background: #fff;
    }

    .tool-textarea {
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
        width: 100%;
        background: #fafafa;
        resize: vertical;
        min-height: 72px;
        max-height: 120px;
        line-height: 1.5;
        transition: border-color 0.2s;
    }

    .tool-textarea:focus {
        outline: none;
        border-color: var(--accent-blue);
        background: #fff;
    }

    .tool-select {
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 13px;
        width: 100%;
        background: #fafafa;
        cursor: pointer;
        appearance: auto;
    }

    .tool-select:focus {
        outline: none;
        border-color: var(--accent-blue);
        background: #fff;
    }

    .tool-row {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        margin-bottom: 12px;
    }

    .tool-row>.tool-field {
        flex: 1;
        min-width: 0;
    }

    .tool-row>.tool-field-sm {
        flex: 0 0 auto;
    }

    .tool-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 12px;
    }

    .color-picker-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        padding: 8px 12px;
        background: #fafafa;
    }

    .color-picker-wrap input[type="color"] {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 6px;
        padding: 0;
        cursor: pointer;
        background: none;
    }

    .color-picker-wrap span {
        font-size: 13px;
        color: #666;
        font-family: monospace;
    }

    .btn-bold-toggle {
        width: 42px;
        height: 42px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .btn-add-design {
        padding: 11px;
        font-size: 14px;
        border-radius: 8px;
    }

    .range-field {
        margin-bottom: 12px;
    }

    .range-field label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        color: #555;
        margin-bottom: 4px;
    }

    .range-field label span {
        color: var(--accent-blue);
        font-weight: 700;
    }

    .range-field input[type="range"] {
        width: 100%;
        accent-color: var(--accent-blue);
    }

    .ai-selects-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 14px;
    }

    .art-category-card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 16px 8px;
        text-align: center;
        cursor: pointer;
        background: #f8f8f8;
        transition: 0.2s;
        min-height: 90px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .art-category-card:hover,
    .art-category-card:active {
        border-color: var(--accent-blue);
        background: #fff5f0;
    }

    .art-category-card svg {
        width: 36px;
        height: 36px;
        color: var(--accent-blue);
    }

    .art-item-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        background: #fff;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .art-item-card svg {
        width: 48px;
        height: 48px;
        color: #333;
    }

    .art-item-card:hover {
        border-color: var(--accent-blue);
        background: #fff5f0;
    }

    .canvas-controls {
        position: absolute;
        top: 10px;
        left: 10px;
        right: 10px;
        display: flex;
        justify-content: space-between;
        pointer-events: none;
        z-index: 10;
    }

    .canvas-ctrl-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        pointer-events: auto;
        font-size: 18px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .zone-name-badge {
        position: absolute;
        left: 50%;
        top: 0;
        transform: translateX(-50%);
        pointer-events: none;
        font-size: 13px;
        font-weight: 700;
        color: #ff6e26;
        font-family: 'Cairo', sans-serif;
        padding: 4px 14px;
        border: 1px solid #ff6e26;
        border-radius: 6px;
        background: rgba(255, 110, 38, 0.07);
        white-space: nowrap;
        display: none;
    }

    @media (max-width: 767px) {
        .canvas-ctrl-btn {
            display: block;
        }
    }

    .mobile-panel-backdrop {
        display: none;
    }

    /* Mobile / Tablet — App-like experience */
    @media (max-width: 767px) {
        .sec-product-detail {
            padding-top: 0 !important;
            padding-bottom: 80px !important;
        }

        .sec-product-detail>.container>.row {
            flex-direction: column;
        }

        .canvas-col {
            order: 1;
            padding: 0 !important;
        }

        .tools-col {
            order: 2;
            padding: 0 !important;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1200;
        }

        .tools-col .p-l-50,
        .tools-col .p-lr-0-lg {
            padding: 0 !important;
        }

        .designer-container {
            flex-direction: column-reverse;
            min-height: auto;
        }

        .sidebar-n {
            width: 100%;
            flex-direction: row;
            justify-content: space-around;
            padding: 6px 0;
            background: #1a1a1a;
            border-top: 1px solid #333;
            order: 2;
        }

        .nav-item-n {
            padding: 8px 4px;
            font-size: 11px;
            border-left: none;
            border-top: 3px solid transparent;
            flex: 1;
        }

        .nav-item-n.active {
            border-right: none;
            border-top-color: var(--accent-blue);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .nav-item-n i {
            font-size: 20px;
        }

        #fabricCanvas {
            width: 327px !important;
        }

        .upper-canvas {
            width: 330px !important;
        }

        .canvas-container {
            width: 327px !important;
        }

        .panel-v {
            position: fixed;
            /* bottom: 62px; */
            left: 0;
            right: 0;
            max-width: 100%;
            height: 55vh;
            max-height: 55vh;
            min-height: 280px;
            border-radius: 16px 16px 0 0;
            box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.15);
            z-index: 1190;
            transform: translateY(110%);
            transition: transform 0.3s ease;
            order: 1;
        }

        .panel-v.mobile-open {
            transform: translateY(0);
        }

        .mobile-panel-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 1180;
        }

        .mobile-panel-backdrop.active {
            display: block;
        }

        .panel-v-body {
            max-height: calc(55vh - 65px);
        }

        #designArea {
            max-width: 100% !important;
            margin: 0 auto;
        }

        .wrap-slick3-dots {
            display: none;
        }

        .wrap-slick3 {
            justify-content: center;
        }

        .bor10.m-t-50 {
            display: none;
        }
    }

    /* ============================================================
       التصميمات section
       ============================================================ */
    #sec-designs.content-section.active {
        display: flex !important;
        flex-direction: column;
        height: 100%;
    }

    #sec-designs.content-section {
        display: none;
    }

    .designs-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 110px));
        gap: 10px;
        padding: 8px 4px;
        overflow-y: auto;
        flex: 1;
        align-content: flex-start;
        min-height: 0;
        justify-content: center;
    }

    .design-card {
        border: 1px solid #e0e0e0;
        overflow: hidden;
        cursor: pointer;
        transition: box-shadow 0.2s;
        background: #fff;
        width: 100%;
        max-width: 110px;
        height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .design-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .design-card img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }

    .design-card .design-name {
        display: none;
    }

    .designs-loading {
        text-align: center;
        color: #999;
        padding: 40px 10px;
        font-size: 14px;
    }

    .designs-empty {
        text-align: center;
        color: #bbb;
        padding: 40px 10px;
        font-size: 13px;
    }


    /* ============================================================
       Action Bar — scoped under .zoom-action-bar
       ============================================================ */
    @media (max-width: 768px) {
        body {
            margin: 0;
            height: 100dvh;
            /* أفضل من 100vh على الموبايلات الحديثة */
            overflow: hidden;
        }

        .page {
            height: 100%;
        }
    }

    /* ============================================================
   Action Bar — scoped under .zoom-action-bar
   ============================================================ */
    .zoom-action-bar {
        width: 100%;
        border: 1px solid #e1e1e1;
    }

    .zoom-action-bar-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        padding: 8px 16px;

        border-radius: 6px;

        font-size: 14px;

        cursor: pointer;

        white-space: nowrap;

        line-height: 1.3;

        height: 42px;

        box-sizing: border-box;

        transition: background .15s, color .15s;

    }



    .zoom-action-bar-btn i {

        font-size: 18px;

        line-height: 1;

    }



    .zoom-action-bar-btn-outline {

        border: 2px solid #222;

        background: #fff;

        color: #222;

    }



    .zoom-action-bar-btn-outline:hover {

        background-color: #ff6e26;

        color: #fff;

        border-color: #ff6e26;

    }



    .zoom-action-bar-btn-primary {

        border: 2px solid #ff6e26;

        background-color: #ff6e26;

        color: #fff;

    }



    .zoom-action-bar-btn-primary:hover {

        background-color: #e55d14;

        border-color: #e55d14;

    }



    .zoom-action-bar-thumb-container {

        border-bottom: 3px solid #222;

        padding-bottom: 2px;

    }



    .zoom-action-bar-vr {

        width: 1px;

        background-color: #dee2e6;

        height: 40px;

    }



    .zoom-action-bar-link {

        color: #222;

        text-decoration: none;

        font-size: 0.9rem;

    }



    .zoom-action-bar-link:hover {

        text-decoration: underline;

    }



    .zoom-action-bar-color-box {

        width: 16px;

        height: 16px;

        background-color: #737397;

        border-radius: 3px;

        display: inline-block;

    }



    /* Mobile nav items */

    .zoom-action-bar-nav-item {

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        color: #4a5568;

        text-decoration: none;

        font-size: 0.85rem;

        font-weight: 500;

        background: none;

        border: none;

        padding: 0 5px;

    }



    .zoom-action-bar-nav-item i {

        font-size: 1.4rem;

        margin-bottom: 2px;

        color: #4a5568;

    }



    .zoom-action-bar-buy-btn {

        background-color: #222;

        color: #ffffff;

        font-weight: 600;

        font-size: 1.1rem;

        padding: 10px 24px;

        border-radius: 12px;

        border: none;

        box-shadow: 0 2px 5px rgba(29, 78, 216, 0.3);

    }



    .zoom-action-bar .com {

        font-size: 0.75rem;

        color: #222;

    }







    /* ========================================

       Product Switcher (inside Quick View modal)

       ======================================== */

    .zoom-switcher-card {

        border: 1px solid #e8e8e8;

        transition: box-shadow 0.2s, border-color 0.2s;

        background: #fff;

        margin-bottom: 20px;

        cursor: pointer;

    }



    .zoom-switcher-card:hover {

        border-color: #ff6e26;

        box-shadow: 0 4px 12px rgba(255, 110, 38, 0.15);

    }



    .zoom-switcher-card-img {

        width: 100%;

        aspect-ratio: 1;

        overflow: hidden;

        background: #f5f5f5;

    }



    .zoom-switcher-card-img img {

        width: 100%;

        height: 100%;

        object-fit: cover;

        display: block;

    }



    .zoom-switcher-card-info {

        padding: 10px 12px;

        text-align: center;

    }



    .zoom-switcher-card-name {

        font-size: 14px;

        font-weight: 500;

        color: #222;

        margin-bottom: 4px;

        overflow: hidden;

        text-overflow: ellipsis;

        white-space: nowrap;

    }



    .zoom-switcher-card-price {

        font-size: 15px;

        font-weight: 700;

        color: #ff6e26;

    }


    /* ========================================
   Product Switcher (inside Quick View modal)
   ======================================== */
    .zoom-switcher-card {
        border: 1px solid #e8e8e8;
        transition: box-shadow 0.2s, border-color 0.2s;
        background: #fff;
        margin-bottom: 20px;
        cursor: pointer;
    }

    .zoom-switcher-card:hover {
        border-color: #ff6e26;
        box-shadow: 0 4px 12px rgba(255, 110, 38, 0.15);
    }

    .zoom-switcher-card-img {
        width: 100%;
        aspect-ratio: 1;
        overflow: hidden;
        background: #f5f5f5;
    }

    .zoom-switcher-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .zoom-switcher-card-info {
        padding: 10px 12px;
        text-align: center;
    }

    .zoom-switcher-card-name {
        font-size: 14px;
        font-weight: 500;
        color: #222;
        margin-bottom: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .zoom-switcher-card-price {
        font-size: 15px;
        font-weight: 700;
        color: #ff6e26;
    }


    /* ========================================
       Product Switcher (inside Quick View modal)
       ======================================== */
    .zoom-switcher-card {
        border: 1px solid #e8e8e8;
        transition: box-shadow 0.2s, border-color 0.2s;
        background: #fff;
        margin-bottom: 20px;
        cursor: pointer;
    }

    .zoom-switcher-card:hover {
        border-color: #ff6e26;
        box-shadow: 0 4px 12px rgba(255, 110, 38, 0.15);
    }

    .zoom-switcher-card-img {
        width: 100%;
        aspect-ratio: 1;
        overflow: hidden;
        background: #f5f5f5;
    }

    .zoom-switcher-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .zoom-switcher-card-info {
        padding: 10px 12px;
        text-align: center;
    }

    .zoom-switcher-card-name {
        font-size: 14px;
        font-weight: 500;
        color: #222;
        margin-bottom: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .zoom-switcher-card-price {
        font-size: 15px;
        font-weight: 700;
        color: #ff6e26;
    }

    .zoom-action-bar .col-auto.d-flex {
        gap: 12px !important;
    }

    /* ========================================
       Toast Notification
       ======================================== */
    .action-toast {
        position: fixed;
        bottom: 80px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        padding: 12px 28px;
        font-size: 15px;
        font-weight: 500;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s, transform 0.3s;
        pointer-events: none;
    }

    .action-toast-show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .action-toast-success {
        background: #222;
        color: #fff;
    }

    .action-toast-info {
        background: #222;
        color: #fff;
    }
</style>
{{-- fabric --}}
<style>
        .placement-guide { position:absolute;pointer-events:none;z-index:10; }
        .pg-boundary { width:100%;height:100%;border:2px dashed #ff6e26;border-radius:4px;box-sizing:border-box; }
        .pg-label { position:absolute;top:-28px;left:0;background:#ff6e26;color:#fff;font-size:12px;padding:2px 8px;border-radius:4px;white-space:nowrap;direction:rtl; }

    @media (max-width: 767px) {
        #fabricCanvas {
            width: 100% !important;
            object-fit: cover;
        }

        .slick-track {
            width: 100% !important;
        }

        #designArea {
            width: 100% !important;
        }

        .canvas-container {
            width: 100% !important;
        }

        .upper-canvas {
            width: 100% !important;
        }

        .slick3 {
            width: 100% !important;
        }
    }
</style>

<style>
    /* ============================================
   🔹 التابلت فقط (768px - 991px)
   ============================================ */
    @media (min-width: 768px) and (max-width: 991px) {

        .panel-v {
            width: 87% !important;
        }

        .canvas-container {
            width: 337px !important;
        }

        #fabricCanvas {
            width: 337px !important;
        }

        .upper-canvas {
            width: 337px !important;
        }

    }
</style>



<section class="sec-product-detail bg0 p-t-65 p-b-60">
    <div class="container">

        @php
        $editorImages = $product->getEditorImageData();
        $baseImages = $editorImages['base_images'];
        $colorImages = $editorImages['color_images'];

        $editorViewMapping = $product->getEditorViewMapping();
        $viewNames = $editorViewMapping['view_names'];
        $colorViewNames = $editorViewMapping['color_view_names'];

        $areasByView = $product->getEditorAreasByView();
        @endphp

        <div class="row">

            <!-- الصور -->
            <div class="col-md-6 col-lg-7 p-b-10 canvas-col" style="padding-left: 0px">
                <div class="p-l-25 p-r-30 p-lr-0-lg">
                    <div class="wrap-slick3 flex-sb flex-w">

                        <!-- الصور الصغيرة -->


                        <div class="wrap-slick3-dots">
                            <ul class="slick3-dots " role="tablist" style="">
                                <li class="" role="presentation">
            @foreach ($baseImages as $index => $img)
            <img src="{{ asset($img) }}" style="display:block;margin-bottom:10px;border-radius:0"
                onclick="changeImage('{{ asset($img) }}', {{ $index }})">
            @endforeach
                                    <div class="slick3-dot-overlay">
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- الـ Canvas -->
                        <div class="slick3 gallery-lb flex-grow-1">
                            <div id="designArea" style="position:relative;width:100%;max-width:500px;margin:0 auto;">

                                <div class="canvas-controls">
                                    <button type="button" class="canvas-ctrl-btn" onclick="undoCanvas()"
                                        title="تراجع (Ctrl+Z)"><i class="bi bi-arrow-counterclockwise"></i></button>

                                    <button type="button" class="canvas-ctrl-btn" onclick="flipView()"
                                        title="تبديل الوجه"><i class="bi bi-arrow-repeat"></i></button>
                                    <span id="currentZoneName" class="zone-name-badge"></span>
                                </div>

                                <canvas id="fabricCanvas" width="500" height="500"
                                    style="width:100%;display:block;"></canvas>

                                <!-- FIX #2: Overlay يمنع التصميم قبل اختيار المقاس واللون -->
                                <div id="canvasOverlay">
                                    <i class="bi bi-palette overlay-icon"></i>
                                    <div class="overlay-text">اختر المقاس واللون أولاً لبدء التصميم</div>
                                    <button class="overlay-btn" onclick="navigateTo('details')">اختر المقاس
                                        واللون</button>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- التفاصيل -->
            <div class="col-md-6 col-lg-5 p-b-10 tools-col" style="padding-right:0px;">
                <div class="mobile-panel-backdrop" id="mobilePanelBackdrop" onclick="closeMobilePanel()"></div>
                <div class="p-l-50 p-lr-0-lg text-right">

                    <div class="designer-container" dir="rtl">
                        <aside class="sidebar-n">
                            <button class="nav-item-n" onclick="navigateTo('designs')" id="btn-designs">
                                <i class="bi bi-grid"></i>
                                <span>تصميمات</span>
                            </button>
                            <button class="nav-item-n" onclick="navigateTo('upload')" id="btn-upload"><i
                                    class="bi bi-cloud-arrow-up"></i><span>رفع</span></button>
                            <button class="nav-item-n" onclick="navigateTo('text')" id="btn-text"><i
                                    class="bi bi-fonts"></i><span>نص</span></button>
                            <button class="nav-item-n" onclick="navigateTo('art')" id="btn-art"><i
                                    class="bi bi-palette"></i><span>رسومات</span></button>
                            <button class="nav-item-n" onclick="navigateTo('details')" id="btn-details"><i
                                    class="bi bi-info-circle"></i><span>التفاصيل</span></button>
                        </aside>

                        <main class="panel-v">
                            <div class="panel-v-header">
                                <button class="btn-header" id="back-btn" onclick="goBack()"
                                    style="visibility: hidden;">‹</button>
                                <span id="header-title" class="fw-bold">تفاصيل المنتج والتصميم</span>
                                <button class="btn-header" id="closeDesignerBtn" onclick="resetToHome()"
                                    style="display: none;">✕</button>
                            </div>

                            <div class="panel-v-body">

                                <section id="sec-home" class="content-section active">
                                    <h2 class="main-title">معلومات المنتج</h2>

                                    <div class="d-flex justify-content-between align-items-center mb-3">

                                        <p class="mb-0 small">
                                            الكمية المتاحة :
                                            <span id="availableQty">{{ $product->quantity }}</span>
                                        </p>

                                        <a href="#product-tabs" class="fw-bold text-decoration-none"
                                            style="color:#ff6e26">

                                            تفاصيل المنتج ↓

                                        </a>

                                    </div>
                                    <div class="feature-grid">
                                        <div class="feature-card" onclick="navigateTo('upload')">
                                            <div class="icon-wrapper"><i class="bi bi-cloud-arrow-up"></i></div>
                                            <div class="feature-title">رفع تصميم</div>
                                        </div>
                                        <div class="feature-card" onclick="navigateTo('text')">
                                            <div class="icon-wrapper"><i class="bi bi-fonts"></i></div>
                                            <div class="feature-title">إضافة نص</div>
                                        </div>
                                        <div class="feature-card" onclick="navigateTo('art')">
                                            <div class="icon-wrapper"><i class="bi bi-palette"></i></div>
                                            <div class="feature-title">إضافة رسومات</div>
                                        </div>
                                        <div class="feature-card" onclick="navigateTo('details')">
                                            <div class="icon-wrapper"><i class="bi bi-tag"></i></div>
                                            <div class="feature-title">تغيير المنتج</div>
                                        </div>
                                    </div>
                                </section>
                                <section id="sec-designs" class="content-section">
                                    <h2 class="main-title">التصميمات</h2>
                                    <div class="designs-grid" id="designsGrid">
                                        <div class="designs-loading">جاري تحميل التصميمات...</div>
                                    </div>
                                </section>
                                <section id="sec-upload" class="content-section">
                                    <h2 class="main-title">رفع صورة</h2>
                                    <div class="upload-zone">
                                        <label for="uploadImageInput" class="btn btn-upload-main mb-3">تصفح جهاز
                                            الكمبيوتر</label>
                                        <input type="file" id="uploadImageInput" accept="image/*" hidden>
                                        <p class="fw-bold">أو اسحب وأفلت في أي مكان</p>
                                    </div>
                                    <div class="tool-grid-2">
                                        <div class="range-field">
                                            <label>حجم الصورة<span id="imageSizeVal">24</span>px</label>
                                            <input type="range" id="imageSize" min="10" max="120" value="24">
                                        </div>
                                        <div class="range-field">
                                            <label>تدوير <span id="imageRotateVal">0</span>°</label>
                                            <input type="range" id="imageRotate" min="0" max="360" value="0">
                                        </div>
                                    </div>

                                </section>

                                <section id="sec-text" class="content-section">
                                    <h2 class="main-title">إضافة نص</h2>
                                    <input type="text" id="textInput" class="tool-input mb-3"
                                        placeholder="اكتب النص هنا">
                                    <button type="button" id="btnAddText" onclick="addTextFromPanel()"
                                        class="btn-add-design mb-3">
                                        إضافة للتصميم
                                    </button>

                                    <div class="tool-row">
                                        <div class="tool-field">
                                            <label class="tool-label">نوع الخط</label>
                                            <select class="tool-select" id="fontFamily"></select>
                                            <input type="text" id="fontSearch" class="form-control form-control-sm mt-2"
                                                placeholder="بحث عن خط..." style="font-size:13px;">
                                        </div>
                                        <div class="tool-field-sm">
                                            <label class="tool-label">&nbsp;</label>
                                            <button type="button" id="textBoldToggle" class="btn-bold-toggle"
                                                onclick="toggleTextBold()" title="Bold">B</button>
                                            <button type="button" id="fontFavToggle" class="btn-fav-toggle mt-1"
                                                title="Favorite"
                                                style="display:block;width:100%;background:none;border:1px solid #ddd;border-radius:4px;padding:2px 8px;font-size:16px;cursor:pointer;">☆</button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="tool-label">لون الخط</label>
                                        <div class="color-picker-wrap">
                                            <input type="color" id="textColor" value="#ffffff">
                                            <span id="textColorHex">#ffffff</span>
                                        </div>
                                    </div>

                                    <div class="tool-grid-2">
                                        <div class="range-field">
                                            <label>حجم الخط <span id="textSizeVal">24</span>px</label>
                                            <input type="range" id="textSize" min="10" max="120" value="24">
                                        </div>
                                        <div class="range-field">
                                            <label>تدوير <span id="textRotateVal">0</span>°</label>
                                            <input type="range" id="textRotate" min="0" max="360" value="0">
                                        </div>
                                    </div>
                                </section>

                                <section id="sec-art" class="content-section">
                                    <div id="art-controls" class="mt-4" style="display:none;">
                                        <hr>
                                        <p class="fw-bold mb-3">تحكم في الرسمة المحددة</p>
                                        <div class="mb-3">
                                            <label class="tool-label">لون الرسمة</label>
                                            <div class="color-picker-wrap">
                                                <input type="color" id="artColor" value="#ffffff">
                                                <span id="artColorHex">#ffffff</span>
                                            </div>
                                        </div>


                                        <div class="mb-3">
                                            <div class="range-field">
                                                <label> حجم الرسمه: <span id="artSizeVal">80</span>px</label>
                                                <input type="range" id="artSize" min="20" max="200" value="80">
                                            </div>


                                        </div>
                                        <div class="tool-grid-2">
                                            <div class="range-field">
                                                <label>بروز الرسمة:<span id="artEmbossVal">0</span>px</label>
                                                <input type="range" id="artEmboss" min="0" max="20" value="24">
                                            </div>
                                            <div class="range-field">
                                                <label>تدوير <span id="artRotateVal">0</span>°</label>
                                                <input type="range" id="artRotate" min="0" max="360" value="0">
                                            </div>
                                        </div>

                                    </div>
                                    <div id="art-categories-view">
                                        <h2 class="main-title">تصنيفات الرسومات</h2>
                                        <input type="text" id="artSearchInput" class="form-control mb-4"
                                            placeholder="ابحث عن رسومات..." oninput="searchArt()">
                                        <div class="artwork-grid" id="artCategoriesGrid"></div>
                                    </div>
                                    <div id="art-items-view" style="display:none;">
                                        <h2 class="main-title" id="artCategoryTitle">الرسومات</h2>
                                        <div class="artwork-grid" id="artItemsGrid"></div>
                                    </div>

                                </section>

                                <section id="sec-details" class="content-section">
                                    <h2 class="main-title">تفاصيل المنتج</h2>

                                    <!-- المقاسات -->
                                    <div class="mb-4">
                                        <label class="tool-label">المقاسات</label>
                                        <div class="gap-2 flex-wrap" id="sizesContainer">
                                            @php
                                            $sizes = $product->variants
                                            ->where('quantity', '>', 0)
                                            ->pluck('size')
                                            ->unique();
                                            @endphp
                                            @foreach ($sizes as $size)
                                            <button type="button" class="size-btn" data-size="{{ $size }}">{{ $size
                                                }}</button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- الألوان -->
                                    <div class="mb-4">
                                        <label class="tool-label">ألوان المنتج المتوفرة</label>
                                        <div class="d-flex gap-2 flex-wrap" id="colorsContainer">
                                            <p class="text-muted" id="noColorsMsg">اختر مقاس أولاً</p>
                                        </div>
                                    </div>

                                    <!-- الفورم -->
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST"
                                        id="addToCartForm">
                                        @csrf
                                        <input type="hidden" name="cart_item_id" value="{{ request('cart_item_id') }}">
                                        <input type="hidden" name="variant_id" id="variant_id">
                                        <input type="hidden" name="design_id" id="design_id"
                                            value="{{ isset($existingDesign) ? $existingDesign->id : '' }}">
                                        <input type="hidden" name="admin_mode" id="admin_mode"
                                            value="{{ isset($admin_mode) && $admin_mode ? '1' : '' }}">
                                        <input type="hidden" name="admin_return_order" id="admin_return_order"
                                            value="{{ isset($admin_return_order) ? $admin_return_order : '' }}">
                                        <input type="hidden" name="admin_return_detail" id="admin_return_detail"
                                            value="{{ isset($admin_return_detail) ? $admin_return_detail : '' }}">
                                        <input type="hidden" name="resubmit" id="resubmit"
                                            value="{{ isset($resubmit) && $resubmit ? '1' : '' }}">
                                        <input type="hidden" name="return_url" id="return_url"
                                            value="{{ $returnUrl ?? '' }}">
                                    </form>
                                </section>

                            </div>
                        </main>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            {{-- Action Bar --}}
            @include('design.partials.action-bar')

            {{-- Product Switcher Modal (Coza Store Quick View block2) --}}
            <div class="wrap-modal1 js-modal1 p-t-60 p-b-20" id="productSwitcherModal">
                <div class="overlay-modal1 js-hide-modal1"></div>
                <div class="container">

                    <div class="bg0 p-t-10 p-b-30 p-lr-15-lg how-pos3-parent">
                        <h3 class="container text-center p-b-8" style="font-weight: 600;">
                            تبديل المنتج
                        </h3>
                        <button class="how-pos3 hov3 trans-04 js-hide-modal1">
                            <img src="{{ asset('assets/frontend/images/icons/icon-close.png') }}" alt="CLOSE">
                        </button>
                        <div class="container text-right p-b-40 p-t-20">
                            <p>
                                أضف تصميمك إلى المزيد من المنتجات !
                                اختر المنتج الذي ترغب في إضافة التصميم إليه
                            </p>
                        </div>
                        <div class="row" id="switcherProductsGrid">
                            <div class="col-12" style="text-align:center; padding:40px 10px; color:#999;">
                                جاري تحميل المنتجات...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bor10 m-t-50 p-t-43 p-b-40">
                <div class="tab01" id="product-tabs">
                    <ul class="nav nav-tabs" role="tablist" dir="rtl">
                        <li class="nav-item p-b-10">
                            <a class="nav-link active" data-toggle="tab" href="#description" role="tab"
                                aria-expanded="true">وصف المنتج</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#information" role="tab"
                                aria-expanded="false">معلومات إضافية</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#reviews" role="tab"
                                aria-expanded="false">التعليقات</a>
                        </li>
                    </ul>

                    <div class="tab-content p-t-43">
                        <div class="tab-pane fade active show" id="description" role="tabpanel" aria-expanded="true"
                            dir="rtl">
                            <div class="how-pos2 p-lr-15-md">
                                <p class="stext-102 cl6">{{ $product->description }}</p>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="information" role="tabpanel" aria-expanded="false" dir="rtl">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <ul class="p-lr-28 p-lr-15-sm">
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">وزن</span>
                                            <span id="weight">--</span>
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">خامات</span>
                                            <span id="material">--</span>
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">الألوان المتاحة</span>
                                            {{ $product->variants->where('quantity', '>',
                                            0)->pluck('color')->unique()->implode('، ') }}
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">المقاسات</span>
                                            @php
                                            $sizes = $product->variants
                                            ->where('quantity', '>', 0)
                                            ->pluck('size')
                                            ->unique();
                                            @endphp
                                            {{ $sizes->implode(' , ') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-expanded="false">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <div class="p-b-30 m-lr-15-sm">

                                        @forelse($product->reviews as $review)
                                        <div class="flex-w flex-t p-b-68" dir="rtl">
                                            <div class="wrap-pic-s size-109 bor0 of-hidden m-l-18 m-t-6">
                                                <x-user-avatar :user="$review->user" alt="AVATAR" />
                                            </div>
                                            <div class="size-207">
                                                <div class="flex-w flex-sb-m p-b-17">
                                                    <span class="mtext-107 cl2 black">{{ $review->name }}</span>
                                                    <span class="fs-18 cl11">
                                                        @php
                                                        $fullStars = floor($review->rating);
                                                        $halfStar = $review->rating - $fullStars >= 0.5;
                                                        @endphp
                                                        @for ($i = 1; $i <= 5; $i++) @if ($i <=$fullStars) <i
                                                            class="zmdi zmdi-star"></i>
                                                            @elseif($i == $fullStars + 1 && $halfStar)
                                                            <i class="zmdi zmdi-star-half"></i>
                                                            @else
                                                            <i class="zmdi zmdi-star-outline"></i>
                                                            @endif
                                                            @endfor
                                                    </span>
                                                </div>
                                                <p class="stext-102 cl6" dir="rtl">{{ $review->message }}
                                                </p>
                                                <small class="stext-102 cl8" style="font-size: 12px;">{{
                                                    $review->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="alert alert-info text-center" dir="rtl"
                                            style="background: #f8f9fa; border: 1px solid #d1ecf1; color: #0c5460; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                            <i class="zmdi zmdi-comment-outline" style="font-size: 24px;"></i>
                                            <p style="margin-top: 10px; margin-bottom: 0;">لا توجد تعليقات على هذا
                                                المنتج بعد. كن أول من يقيّم!</p>
                                        </div>
                                        @endforelse

                                        <form class="w-full" method="POST" action="{{ route('storeReview') }}"
                                            id="reviewForm">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <h5 class="mtext-108 black cl2 p-b-7" dir="rtl">إضافة مراجعة</h5>
                                            <p class="stext-102 cl6" dir="rtl">لن يتم نشر عنوان بريدك الإلكتروني.
                                                الحقول
                                                المطلوبة مُشار إليها بعلامة *</p>

                                            <div class="flex-w flex-m p-t-50 p-b-23" dir="rtl">
                                                <span class="stext-102 cl3 m-l-16">ما هو تقييمك؟</span>
                                                <span class="wrap-rating fs-18 cl11 pointer" id="ratingStars">
                                                    <i class="item-rating pointer zmdi zmdi-star-outline"
                                                        data-value="1"></i>
                                                    <i class="item-rating pointer zmdi zmdi-star-outline"
                                                        data-value="2"></i>
                                                    <i class="item-rating pointer zmdi zmdi-star-outline"
                                                        data-value="3"></i>
                                                    <i class="item-rating pointer zmdi zmdi-star-outline"
                                                        data-value="4"></i>
                                                    <i class="item-rating pointer zmdi zmdi-star-outline"
                                                        data-value="5"></i>
                                                    <input type="hidden" name="rating" id="ratingValue" value="5">
                                                </span>
                                            </div>

                                            <div class="row p-b-25" dir="rtl">
                                                <div class="col-12 p-b-5">
                                                    <label class="stext-102 cl3" for="message">اكتب تقييمك <span
                                                            class="text-danger">*</span></label>
                                                    <textarea class="size-110 bor8 stext-102 cl2 black p-lr-20 p-tb-10"
                                                        id="message" name="message"
                                                        required>{{ old('message', session('review_data.message')) }}</textarea>
                                                    @error('message')
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-6 p-b-5">
                                                    <label class="stext-102 cl3" for="name">الاسم <span
                                                            class="text-danger">*</span></label>
                                                    <input class="size-111 bor8 stext-102 black cl2 p-lr-20" id="name"
                                                        type="text" name="name"
                                                        value="{{ old('name', auth()->check() ? auth()->user()->name : session('review_data.name')) }}"
                                                        @auth readonly @endauth required>
                                                    @error('name')
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-6 p-b-5">
                                                    <label class="stext-102 cl3" for="email">البريد الإلكتروني
                                                        <span class="text-danger">*</span></label>
                                                    <input class="size-111 bor8 stext-102 cl2 black p-lr-20" id="email"
                                                        type="email" name="email"
                                                        value="{{ old('email', auth()->check() ? auth()->user()->email : session('review_data.email')) }}"
                                                        @auth readonly @endauth required>
                                                    @error('email')
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>

                                            <button type="submit"
                                                class="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-b-10">إرسال</button>
                                        </form>

                                        @if (session('success'))
                                        <div class="alert alert-success text-center" dir="rtl"
                                            style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 10px; margin-top: 20px;">
                                            <i class="zmdi zmdi-check-circle"></i> {{ session('success') }}
                                        </div>
                                        @endif

                                        @if ($errors->any())
                                        <div class="alert alert-danger text-center" dir="rtl"
                                            style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 10px; margin-top: 20px;">
                                            <i class="zmdi zmdi-alert-circle"></i> يرجى التحقق من البيانات المدخلة
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Switch Confirmation Modal — REMOVED: switches immediately without confirmation --}}

        </div>
    </div>
</section>

{{-- Fonts & Fabric.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script src="{{ asset('assets/js/fabric-compat.js') }}"></script>
<script src="{{ asset('assets/js/design-art-icons.js') }}"></script>

{{-- ZoomStore Modules --}}
<script src="{{ asset('assets/js/cache.js') }}"></script>
<script>
    window.ZoomStore = window.ZoomStore || {}; ZoomStore.baseUrl = "{{ asset('') }}";
</script>
<script src="{{ asset('assets/js/font-catalog.js') }}"></script>
<script src="{{ asset('assets/js/font-manager.js') }}"></script>
<script src="{{ asset('assets/js/svg-loader.js') }}"></script>
<script src="{{ asset('assets/js/search-manager.js') }}"></script>
<script src="{{ asset('assets/js/preview-manager.js') }}"></script>
<script src="{{ asset('assets/js/color-manager.js') }}"></script>
<script src="{{ asset('assets/js/asset-manager.js') }}"></script>
<script src="{{ asset('assets/js/svg-adapter.js') }}"></script>

<script>
    var _loadingView = false;
    var _renderPromise = Promise.resolve();
    var _saveTimer = null;
        function debouncedSave() {
            if (_saveTimer) clearTimeout(_saveTimer);
            if (_loadingView) return;
            _saveTimer = setTimeout(function() {
                _saveTimer = null;
                saveCurrentView();
            }, 300);
        }

        // ============================================================
        // ARCHITECTURE OVERVIEW
        // ============================================================
        //
        // Subsystem              Depends On                          Used By
        // ---------------------- ----------------------------------- -------------------------
        // PlacementGuide         self-contained (DOM + canvas ref)   Event handlers, initCanvas
        // Print Zone System      getAreasForView, getObjectCenter    enforcePrintAreaBounds,
        //                                                            showGuides, snapToGuides,
        //                                                            saveCurrentView
        // Fabric.js Setup        (global)                            All subsystems
        // History (Undo/Redo)    canvas.toJSON, loadFromJSON         Text, Art, Upload, Move
        // Text Editor            canvas, Fabric.Textbox             initCanvas, syncPanel
        // Art System             AssetManager, SVGLoader,            searchArt, addArtToCanvas
        //                        PreviewManager
        // Image Upload           FileReader, canvas                 initCanvas
        // Thumbnail System       OffscreenCanvas, Fabric            saveCurrentView,
        //                                                           _executeChangeImage
        // View Switching         changeImage, _executeChangeImage   flipView, loadExistingDesign
        // Size/Color Selection   variants data, sessionStorage      handleSubmit, initSizesAndColors
        // Save/Restore           canvas.toJSON, fetch               handleSubmit, changeImage
        // Action Bar             DOM elements                       product switch, saveDesign
        // Custom Controls        fabric.Control, render*Icon        initCanvas
        //
        // ============================================================

        function isAdminMode() {
            return document.getElementById('admin_mode')?.value === '1';
        }

        // ============================================================
        // Placement Guide — HTML/CSS overlay showing active placement boundary
        // Replaces Fabric-based print zone overlay in normal mode.
        // ============================================================
        function getAreaForPlacementGuide(obj) {
            return resolveObjectArea(obj);
        }

        var PlacementGuide = (function() {
            var _initialized = false;
            var _timer = null;
            var _container = null;
            var _canvasEl = null;
            var _guideEl = null;
            var _labelEl = null;
            var _area = null;
            var _visible = false;
            var _resizeHandler = null;
            var _logicalW = 0;
            var _logicalH = 0;
            const HIDE_DELAY = 2000;

            function _createDOM() {
                var g = document.createElement('div');
                g.className = 'placement-guide';
                g.style.display = 'none';
                g.innerHTML = '<div class="pg-boundary"><div class="pg-label"></div></div>';
                return g;
            }

            function _updatePosition() {
                if (!_initialized || !_visible || !_area || !_canvasEl || !_container) return;
                if (!_logicalW || !_logicalH) return;
                var cr = _canvasEl.getBoundingClientRect();
                var dr = _container.getBoundingClientRect();
                var sx = cr.width / _logicalW;
                var sy = cr.height / _logicalH;
                _guideEl.style.left = (_area.x * sx + cr.left - dr.left) + 'px';
                _guideEl.style.top = (_area.y * sy + cr.top - dr.top) + 'px';
                _guideEl.style.width = (_area.width * sx) + 'px';
                _guideEl.style.height = (_area.height * sy) + 'px';
            }

            function initialize(canvas) {
                if (!canvas) return;
                destroy();
                _container = document.getElementById('designArea');
                _canvasEl = document.getElementById('fabricCanvas');
                if (!_container || !_canvasEl) { _container = null; _canvasEl = null; return; }
                _logicalW = canvas.width;
                _logicalH = canvas.height;
                _guideEl = _createDOM();
                _container.appendChild(_guideEl);
                _labelEl = _guideEl.querySelector('.pg-label');
                _resizeHandler = function() { _updatePosition(); };
                window.addEventListener('resize', _resizeHandler, { passive: true });
                _initialized = true;
            }

            function destroy() {
                if (_timer) { clearTimeout(_timer); _timer = null; }
                if (_guideEl && _guideEl.parentNode) _guideEl.parentNode.removeChild(_guideEl);
                if (_resizeHandler) { window.removeEventListener('resize', _resizeHandler); _resizeHandler = null; }
                _container = null; _canvasEl = null; _guideEl = null; _labelEl = null;
                _area = null; _initialized = false; _visible = false; _logicalW = 0; _logicalH = 0;
            }

            function setArea(area) { _area = area; if (_visible && _initialized) _updatePosition(); }
            function setLabel(label) { if (_labelEl) _labelEl.textContent = label; }

            function show() {
                if (!_initialized || !_area) return;
                _visible = true;
                _updatePosition();
                _guideEl.style.display = '';
                if (_timer) clearTimeout(_timer);
                _timer = setTimeout(function() { hide(); _timer = null; }, HIDE_DELAY);
            }

            function hide() {
                if (!_initialized) return;
                _visible = false;
                if (_timer) { clearTimeout(_timer); _timer = null; }
                if (_guideEl) _guideEl.style.display = 'none';
            }

            function refresh() {
                if (_visible && _initialized) {
                    _updatePosition();
                    if (_timer) clearTimeout(_timer);
                    _timer = setTimeout(function() { hide(); _timer = null; }, HIDE_DELAY);
                }
            }

            function updatePosition() { _updatePosition(); }

            return {
                initialize: initialize, destroy: destroy, show: show, hide: hide,
                refresh: refresh, setArea: setArea, setLabel: setLabel, updatePosition: updatePosition
            };
        })();

        function updatePlacementGuide(obj, action) {
            if (isAdminMode()) return;
            var a = getAreaForPlacementGuide(obj);
            if (!a) return;
            PlacementGuide.setArea(a);
            PlacementGuide.updatePosition();
            PlacementGuide.setLabel(a.name || '');
            action();
        }

        // ============================================================
        // البيانات من Laravel
        // ============================================================
        let variants = @json($product->variants);
        let productImages = @json($baseImages);
        let colorImages = @json($colorImages);
        const existingVariant = @json($existingVariantData ?? null);
        const existingDesign = @json($existingDesign ?? null);
        const assetBase = @json(rtrim(asset(''), '/'));
        const isAuthenticated = @json(auth()->check() || auth('admin')->check());
        let productName = @json($product->name);
        let currentProductId = {{ $product->id }};

        var _zlDesignLoadStarted = false;
        var _zlDesignLoadDone = false;
        var _zlLoadingComplete = false;
        function _zlTryComplete() {
            if (_zlLoadingComplete) return;
            if (_zlDesignLoadStarted && !_zlDesignLoadDone) {
                return;
            }
            _zlLoadingComplete = true;
            if (window.ZoomStore && ZoomStore.ZoomLoading) {
                ZoomStore.ZoomLoading.setProgress(100);
                ZoomStore.ZoomLoading.setMessage('\u062A\u0645 \u0627\u0644\u062A\u062D\u0645\u064A\u0644');
                setTimeout(function() { ZoomStore.ZoomLoading.hide(); }, 400);
            }
        }

        let designAreas = @json($product->design_areas);
        let viewNames = @json($viewNames);
        let colorViewNames = @json($colorViewNames);
        let areasByView = @json($areasByView);

        // ---- Global Print Area Bounds ----
        // slot_key → {x, y, width, height} lookup for enforcePrintAreaBounds()
        window._slotKeyToArea = {};
        function buildSlotKeyToAreaMap() {
            var map = {};
            for (var vn in areasByView) {
                var viewAreas = areasByView[vn];
                for (var i = 0; i < viewAreas.length; i++) {
                    if (viewAreas[i].slot_key) {
                        map[viewAreas[i].slot_key] = viewAreas[i];
                    }
                }
            }
            window._slotKeyToArea = map;
        }
        buildSlotKeyToAreaMap();

        // ============================================================
        // FLOW TRACE LOGGING — Runtime state at page load
        // ============================================================
        console.log('=== FLOW TRACE ===');
        console.log('[FLOW] URL:', window.location.href);
        console.log('[FLOW] Query params:', Object.fromEntries(new URLSearchParams(window.location.search).entries()));
        console.log('[FLOW] Referrer:', document.referrer || '(none)');
        console.log('[FLOW] existingDesign:', JSON.parse(JSON.stringify(existingDesign)));
        console.log('[FLOW] existingDesign.designs length:', existingDesign ? (existingDesign.designs ? existingDesign.designs.length : 'designs is null/undefined') : 'existingDesign is null/undefined');
        console.log('[FLOW] existingVariant:', JSON.parse(JSON.stringify(existingVariant)));
        console.log('[FLOW] sessionStorage keys:', Object.keys(sessionStorage).filter(k => !k.startsWith('_') && !k.includes('token')));
        console.log('[FLOW] sessionStorage.pendingDesign:', sessionStorage.getItem('pendingDesign'));
        console.log('[FLOW] sessionStorage.selectedSize:', sessionStorage.getItem('selectedSize'));
        console.log('[FLOW] sessionStorage.selectedColor:', sessionStorage.getItem('selectedColor'));
        console.log('[FLOW] sessionStorage.selectedVariantId:', sessionStorage.getItem('selectedVariantId'));
        const lsKeys = Object.keys(localStorage).filter(k => k.startsWith('design_') || k.startsWith('uploaded_') || k.startsWith('img_'));
        console.log('[FLOW] localStorage design/upload keys:', lsKeys);
        console.log('[FLOW] productImages:', JSON.parse(JSON.stringify(productImages)));
        console.log('[FLOW] === END FLOW TRACE ===');

        // ============================================================
        // Constraint System — keep objects inside their printable areas
        // A design object must NEVER exist outside its own printable area.
        // Resolves the correct area by _slotKey (canonical) or falls back
        // to the nearest area in the current view.
        // ============================================================
        function getAreaBySlotKey(slotKey) {
            return window._slotKeyToArea && window._slotKeyToArea[slotKey] ? window._slotKeyToArea[slotKey] : null;
        }

        function resolveObjectArea(obj) {
            if (obj && obj._slotKey) {
                var a = getAreaBySlotKey(obj._slotKey);
                if (a) return a;
            }
            var areas = getAreasForView(currentView);
            if (!areas || areas.length === 0) return null;
            if (areas.length === 1) return areas[0];
            var center = getObjectCenter(obj);
            var nearest = null;
            var nearestDist = Infinity;
            for (var i = 0; i < areas.length; i++) {
                var acx = areas[i].x + areas[i].width / 2, acy = areas[i].y + areas[i].height / 2;
                var d = Math.pow(center.x - acx, 2) + Math.pow(center.y - acy, 2);
                if (d < nearestDist) { nearestDist = d; nearest = areas[i]; }
            }
            return nearest;
        }

        function enforcePrintAreaBounds(obj) {
            if (!obj || obj._isPrintZone || obj.excludeFromExport) return;
            if (!canvas) return;

            var area = resolveObjectArea(obj);
            if (!area) return;

            obj.setCoords();
            var bounds = obj.getBoundingRect();
            var aL = area.x, aT = area.y, aR = area.x + area.width, aB = area.y + area.height;

            // Scale down if object is too large for the area
            if (bounds.width > area.width || bounds.height > area.height) {
                var sx = obj.scaleX * Math.min(1, area.width / bounds.width);
                var sy = obj.scaleY * Math.min(1, area.height / bounds.height);
                var scale = Math.min(sx, sy);
                obj.set({ scaleX: scale, scaleY: scale });
                obj.setCoords();
                bounds = obj.getBoundingRect();
            }

            // Clamp full bounding box inside area
            var dx = 0, dy = 0;
            if (bounds.left < aL) dx = aL - bounds.left;
            else if (bounds.left + bounds.width > aR) dx = aR - (bounds.left + bounds.width);
            if (bounds.top < aT) dy = aT - bounds.top;
            else if (bounds.top + bounds.height > aB) dy = aB - (bounds.top + bounds.height);

            if (dx !== 0 || dy !== 0) {
                obj.set({ left: obj.left + dx, top: obj.top + dy });
                obj.setCoords();
            }
        }

        // ============================================================
        // Debug ID system
        // ============================================================
        var _nextDebugId = 1;
        var _saveCount = 0;
        function _newDebugId() { return 'obj_' + (_nextDebugId++) + '_' + Date.now(); }

        // ============================================================
        // Application-owned object identity (_zoomObjectId)
        // Generated once per object. Survives serialization, undo/redo,
        // clone, and product switching. Never depends on Fabric.js internals.
        // ============================================================
        function _generateZoomObjectId() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }
        function _ensureZoomObjectId(obj) {
            if (obj && !obj._zoomObjectId) {
                obj._zoomObjectId = _generateZoomObjectId();
            }
        }
        function _logCreate(obj, source) {
            if (!obj._debugId) obj._debugId = _newDebugId();
            console.log('[LIFECYCLE] CREATED  id=' + obj._debugId + ' type=' + (obj.type || obj._type || '?') + ' source=' + source + ' fontFamily=' + (obj.fontFamily || 'N/A') + ' content=' + (typeof obj.text === 'string' ? obj.text.substring(0,20) : (obj._customSrc || obj._artKey || 'N/A')));
        }
        function _logAdd(obj, target) {
            console.log('[LIFECYCLE] ADDED    id=' + obj._debugId + ' type=' + (obj.type || '?') + ' target=' + target);
        }
        function _logSerialize(obj, stage) {
            console.log('[LIFECYCLE] SERIALIZE id=' + obj._debugId + ' type=' + (obj.type || '?') + ' stage=' + stage + ' font=' + (obj.fontFamily || 'N/A'));
        }
        function _logRemove(obj, reason) {
            console.log('[LIFECYCLE] REMOVED  id=' + obj._debugId + ' type=' + (obj.type || '?') + ' reason=' + reason);
        }
        function _logCanvasViews(phase) {
            console.log('[CANVASVIEWS] ' + phase + ' — keys:', Object.keys(canvasViews).map(function(k) {
                var v = canvasViews[k];
                if (!v) return k + ':null';
                if (!v.objects) return k + ':no-objects';
                return k + ':' + v.objects.length + 'objs [' + v.objects.map(function(o) { return (o._debugId || 'no-id') + '/' + (o.type || o._type || '?'); }).join(',') + ']';
            }).join(' | '));
        }

        // ============================================================
        // Fabric.js Setup
        // ============================================================
        let canvas;
        let canvasViews = {};
        window._canvasViewsRef = canvasViews;
        let currentView = 0;
        let editingTextObj = null;
        let canvasHistory = [];
        let historyIndex = -1;
        let _isRestoring = false;
        let _checkoutGuard = false;
        const imageCache = {};
        const uploadedImagesCache = {};

        // ============================================================
        // Print Zone System — Fabric overlay rects for admin mode
        // ============================================================
        let printZoneRects = [];
        let currentPrintAreaIndex = 0;

        function getViewNamesForCurrentColor() {
            const colorKey = window._selectedColorKey;
            if (colorKey && colorViewNames && colorViewNames[colorKey]) {
                return colorViewNames[colorKey];
            }
            return viewNames;
        }

        function getAreasForView(viewIndex) {
            const names = getViewNamesForCurrentColor();
            const vn = names[viewIndex];
            if (!vn) return [];
            return areasByView[vn] || [];
        }

        function getCurrentPrintArea() {
            const areas = getAreasForView(currentView);
            if (!areas || areas.length === 0) return null;
            if (currentPrintAreaIndex >= areas.length) return null;
            return areas[currentPrintAreaIndex];
        }

        function getObjectCenter(obj) {
            var cx, cy;
            if (obj.originX === 'center') {
                cx = obj.left;
            } else {
                cx = obj.left + (obj.width * obj.scaleX) / 2;
            }
            if (obj.originY === 'center') {
                cy = obj.top;
            } else {
                cy = obj.top + (obj.height * obj.scaleY) / 2;
            }
            return { x: cx, y: cy };
        }

        function findNearestAreaIndex(objCx, objCy) {
            const areas = getAreasForView(currentView);
            if (!areas || areas.length === 0) return null;
            if (areas.length === 1) return 0;
            let nearestIdx = 0;
            let nearestDist = Infinity;
            for (let i = 0; i < areas.length; i++) {
                const a = areas[i];
                const aCx = a.x + a.width / 2;
                const aCy = a.y + a.height / 2;
                const dist = (objCx - aCx) ** 2 + (objCy - aCy) ** 2;
                if (dist < nearestDist) {
                    nearestDist = dist;
                    nearestIdx = i;
                }
            }
            return nearestIdx;
        }

        function findNearestAreaForObjects(objects) {
            const areas = getAreasForView(currentView);
            if (!areas || areas.length === 0) return null;
            if (areas.length === 1) return areas[0];

            // Compute bounding box center of all user objects
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            for (const obj of objects) {
                if (obj._isPrintZone || obj.excludeFromExport) continue;
                var b = null;
                if (typeof obj.getBoundingRect === 'function') {
                    b = obj.getBoundingRect();
                } else if (obj.left !== undefined && obj.width !== undefined) {
                    b = { left: obj.left, top: obj.top, width: obj.width, height: obj.height };
                }
                if (!b) continue;
                minX = Math.min(minX, b.left);
                minY = Math.min(minY, b.top);
                maxX = Math.max(maxX, b.left + b.width);
                maxY = Math.max(maxY, b.top + b.height);
            }

            if (!isFinite(minX)) return areas[0]; // no user objects

            const cx = (minX + maxX) / 2;
            const cy = (minY + maxY) / 2;

            // First try: area that CONTAINS the center
            for (const a of areas) {
                if (cx >= a.x && cx <= a.x + a.width && cy >= a.y && cy <= a.y + a.height) {
                    return a;
                }
            }

            // Fallback: nearest area by center distance
            let nearest = areas[0];
            let nearestDist = Infinity;
            for (const a of areas) {
                const aCx = a.x + a.width / 2;
                const aCy = a.y + a.height / 2;
                const dist = (cx - aCx) ** 2 + (cy - aCy) ** 2;
                if (dist < nearestDist) {
                    nearestDist = dist;
                    nearest = a;
                }
            }
            return nearest;
        }

        // ============================================================
        // Slot Key Assignment — every object belongs to exactly one slot
        // ============================================================
        // ---- Convention 1: slot_key assignment ----
        // Every Fabric object must belong to exactly one slot_key.
        // This is the permanent owner of editor objects and the foundation
        // of intelligent product switching.
        function getNearestSlotKey(obj) {
            if (!obj || obj._isPrintZone || obj.excludeFromExport) return null;
            var areas = getAreasForView(currentView);
            if (!areas || areas.length === 0) return null;
            if (areas.length === 1) return areas[0].slot_key || null;
            var center = getObjectCenter(obj);
            var idx = findNearestAreaIndex(center.x, center.y);
            if (idx === null) return areas[0].slot_key || null;
            return areas[idx] ? (areas[idx].slot_key || null) : null;
        }

        function assignSlotKeyToObject(obj) {
            if (!obj || obj._isPrintZone) return;
            var slotKey = getNearestSlotKey(obj);
            if (slotKey) obj._slotKey = slotKey;
        }

        function drawPrintZones(viewIndex) {
            if (!canvas) return;

            // Remove existing zones
            printZoneRects.forEach(function(r) { if (r.canvas) canvas.remove(r); });
            printZoneRects = [];
            hideGuides();

            const areas = getAreasForView(viewIndex);
            if (!areas || areas.length === 0) return;

            const initialOpacity = isAdminMode() ? 1 : 0;

            areas.forEach(function(area) {
                var rect = new fabric.Rect({
                    left: area.x,
                    top: area.y,
                    width: area.width,
                    height: area.height,
                    fill: 'rgba(255, 110, 38, 0.04)',
                    stroke: '#ff6e26',
                    strokeWidth: 1.5,
                    strokeDashArray: [6, 4],
                    selectable: false,
                    evented: false,
                    excludeFromExport: true,
                    _isPrintZone: true,
                    opacity: initialOpacity,
                    _areaViewName: area.view_name,
                    _areaId: area.id
                });
                canvas.add(rect);
                printZoneRects.push(rect);
            });

            canvas.renderAll();
        }

        function showPrintZones(obj) {
            if (!isAdminMode()) {
                printZoneRects.forEach(function(r) { r.set('opacity', 1); });
                if (canvas) canvas.renderAll();
                return;
            }
            var c = getObjectCenter(obj);
            var objCx = c.x, objCy = c.y;
            var nearestIdx = findNearestAreaIndex(objCx, objCy);
            var areas = getAreasForView(currentView);
            var nearestAreaId = (nearestIdx !== null && areas[nearestIdx]) ? areas[nearestIdx].id : null;
            printZoneRects.forEach(function(r) {
                r.set('opacity', r._areaId === nearestAreaId ? 1 : 0.15);
            });
            if (canvas) canvas.renderAll();
        }

        function hidePrintZones(opts) {
            if (!isAdminMode()) return;
            opts = opts || {};
            if (opts.remove) {
                printZoneRects.forEach(function(r) { if (r.canvas) canvas.remove(r); });
                printZoneRects = [];
            } else {
                printZoneRects.forEach(function(r) { r.set('opacity', 0); });
            }
            hideGuides();
            if (canvas) canvas.renderAll();
        }

        function getPrintZoneBounds(areaIndex) {
            if (areaIndex === undefined) areaIndex = currentPrintAreaIndex;
            const areas = getAreasForView(currentView);
            const area = areas[areaIndex];
            if (!area) return null;
            return {
                left: area.x,
                top: area.y,
                right: area.x + area.width,
                bottom: area.y + area.height,
                width: area.width,
                height: area.height
            };
        }

        function getPrintZoneCenter(areaIndex) {
            const bounds = getPrintZoneBounds(areaIndex);
            if (!bounds) return { left: 150, top: 150 };
            return {
                left: bounds.left + bounds.width / 2,
                top: bounds.top + bounds.height / 2
            };
        }

        // ============================================================
        // Snap-to-Guide System — canvas center + area center snapping
        // ============================================================
        let guideVLine = null;
        let guideHLine = null;
        let areaGuideHLine = null;
        let areaGuideVLine = null;
        const SNAP_THRESHOLD = 5;
        const CANVAS_CENTER = 250;

        function snapToGuides(obj) {
            if (obj._isPrintZone || obj.excludeFromExport) return false;

            var c = getObjectCenter(obj);
            var objCx = c.x, objCy = c.y;
            var halfW = (obj.width * obj.scaleX) / 2;
            var halfH = (obj.height * obj.scaleY) / 2;
            var snapped = false;
            var snapX = null, snapY = null;

            // Canvas center snap
            if (Math.abs(objCx - CANVAS_CENTER) < SNAP_THRESHOLD) {
                snapX = obj.originX === 'center' ? CANVAS_CENTER : CANVAS_CENTER - halfW;
                snapped = true;
            }
            if (Math.abs(objCy - CANVAS_CENTER) < SNAP_THRESHOLD) {
                snapY = obj.originY === 'center' ? CANVAS_CENTER : CANVAS_CENTER - halfH;
                snapped = true;
            }

            // Area center snap — use nearest area dynamically
            var nearestIdx = findNearestAreaIndex(objCx, objCy);
            var areaCenter = nearestIdx !== null ? getPrintZoneCenter(nearestIdx) : null;
            if (areaCenter) {
                if (snapX === null && Math.abs(objCx - areaCenter.left) < SNAP_THRESHOLD) {
                    snapX = obj.originX === 'center' ? areaCenter.left : areaCenter.left - halfW;
                    snapped = true;
                }
                if (snapY === null && Math.abs(objCy - areaCenter.top) < SNAP_THRESHOLD) {
                    snapY = obj.originY === 'center' ? areaCenter.top : areaCenter.top - halfH;
                    snapped = true;
                }
            }

            if (snapped) {
                obj.set({
                    left: snapX !== null ? snapX : obj.left,
                    top: snapY !== null ? snapY : obj.top
                });
                obj.setCoords();
                showGuides(obj);
                return true;
            }
            hideGuides();
            return false;
        }

        function showGuides(obj) {
            if (!canvas) return;
            hideGuides();

            var halfW = obj ? (obj.width * obj.scaleX) / 2 : 0;
            var halfH = obj ? (obj.height * obj.scaleY) / 2 : 0;
            var objCx = obj ? getObjectCenter(obj).x : CANVAS_CENTER;
            var objCy = obj ? getObjectCenter(obj).y : CANVAS_CENTER;

            // Canvas center guides
            var nearCx = Math.abs(objCx - CANVAS_CENTER) < SNAP_THRESHOLD;
            var nearCy = Math.abs(objCy - CANVAS_CENTER) < SNAP_THRESHOLD;

            if (nearCx) {
                guideVLine = new fabric.Line([CANVAS_CENTER, 0, CANVAS_CENTER, 500], {
                    stroke: '#ff6e26', strokeWidth: 1, strokeDashArray: [5, 5],
                    selectable: false, evented: false, excludeFromExport: true
                });
                canvas.add(guideVLine);
            }
            if (nearCy) {
                guideHLine = new fabric.Line([0, CANVAS_CENTER, 500, CANVAS_CENTER], {
                    stroke: '#ff6e26', strokeWidth: 1, strokeDashArray: [5, 5],
                    selectable: false, evented: false, excludeFromExport: true
                });
                canvas.add(guideHLine);
            }

            // Area center guides (different color — teal)
            var nearestIdx = findNearestAreaIndex(objCx, objCy);
            var areaCenter = nearestIdx !== null ? getPrintZoneCenter(nearestIdx) : null;
            if (areaCenter && !nearCx && !nearCy) {
                var nearAreaCx = Math.abs(objCx - areaCenter.left) < SNAP_THRESHOLD;
                var nearAreaCy = Math.abs(objCy - areaCenter.top) < SNAP_THRESHOLD;

                if (nearAreaCx) {
                    areaGuideVLine = new fabric.Line([areaCenter.left, 0, areaCenter.left, 500], {
                        stroke: '#00b894', strokeWidth: 1, strokeDashArray: [3, 4],
                        selectable: false, evented: false, excludeFromExport: true
                    });
                    canvas.add(areaGuideVLine);
                }
                if (nearAreaCy) {
                    areaGuideHLine = new fabric.Line([0, areaCenter.top, 500, areaCenter.top], {
                        stroke: '#00b894', strokeWidth: 1, strokeDashArray: [3, 4],
                        selectable: false, evented: false, excludeFromExport: true
                    });
                    canvas.add(areaGuideHLine);
                    }
                }

                // Ensure all restored objects stay inside their print areas
                canvas.getObjects().forEach(function(o) {
                    if (!o._isPrintZone && !o.excludeFromExport) enforcePrintAreaBounds(o);
                });

                canvas.renderAll();
        }

        function hideGuides() {
            [guideVLine, guideHLine, areaGuideVLine, areaGuideHLine].forEach(function(l) {
                if (l && l.canvas) canvas.remove(l);
            });
            guideVLine = null; guideHLine = null;
            areaGuideVLine = null; areaGuideHLine = null;
            if (canvas) canvas.renderAll();
        }

        function constrainObjectToValidArea(obj) {
            if (obj._isPrintZone || obj.excludeFromExport) return;
            var areas = getAreasForView(currentView);
            if (!areas || areas.length === 0) return;

            var c = getObjectCenter(obj);
            var cx = c.x, cy = c.y;

            // Check if object center is inside any area
            for (var i = 0; i < areas.length; i++) {
                var a = areas[i];
                if (cx >= a.x && cx <= a.x + a.width && cy >= a.y && cy <= a.y + a.height) {
                    return; // free movement — inside a valid printable zone
                }
            }

            // Outside all areas — push back to nearest area boundary
            var bestArea = null;
            var bestDist = Infinity;
            var bestClampX = cx, bestClampY = cy;
            for (var i = 0; i < areas.length; i++) {
                var a = areas[i];
                var cpX = Math.max(a.x, Math.min(cx, a.x + a.width));
                var cpY = Math.max(a.y, Math.min(cy, a.y + a.height));
                var d = (cx - cpX) * (cx - cpX) + (cy - cpY) * (cy - cpY);
                if (d < bestDist) {
                    bestDist = d;
                    bestArea = a;
                    bestClampX = cpX;
                    bestClampY = cpY;
                }
            }

            if (bestArea) {
                var pushLeft = obj.originX === 'center' ? bestClampX : bestClampX - (obj.width * obj.scaleX) / 2;
                var pushTop = obj.originY === 'center' ? bestClampY : bestClampY - (obj.height * obj.scaleY) / 2;
                obj.set({ left: pushLeft, top: pushTop });
                obj.setCoords();
            }
        }

        function constrainObjectScaleToValidArea(obj) {
            if (obj._isPrintZone || obj.excludeFromExport) return;
            var areas = getAreasForView(currentView);
            if (!areas || areas.length === 0) return;

            var c = getObjectCenter(obj);
            var cx = c.x, cy = c.y;

            // Find active area (containing center, or nearest if outside all)
            var activeArea = null;
            var nearestDist = Infinity;
            for (var i = 0; i < areas.length; i++) {
                var a = areas[i];
                if (cx >= a.x && cx <= a.x + a.width && cy >= a.y && cy <= a.y + a.height) {
                    activeArea = a;
                    break;
                }
                var cpX = Math.max(a.x, Math.min(cx, a.x + a.width));
                var cpY = Math.max(a.y, Math.min(cy, a.y + a.height));
                var d = (cx - cpX) * (cx - cpX) + (cy - cpY) * (cy - cpY);
                if (d < nearestDist) {
                    nearestDist = d;
                    activeArea = a;
                }
            }

            if (!activeArea) return;

            var aL = activeArea.x, aT = activeArea.y;
            var aR = activeArea.x + activeArea.width, aB = activeArea.y + activeArea.height;

            obj.setCoords();
            var objBounds = obj.getBoundingRect();

            if (objBounds.left < aL || objBounds.top < aT ||
                objBounds.left + objBounds.width > aR ||
                objBounds.top + objBounds.height > aB) {

                if (objBounds.width > activeArea.width || objBounds.height > activeArea.height) {
                    var sx = obj.scaleX * Math.min(1, activeArea.width / objBounds.width);
                    var sy = obj.scaleY * Math.min(1, activeArea.height / objBounds.height);
                    var scale = Math.min(sx, sy);
                    obj.set({ scaleX: scale, scaleY: scale });
                    obj.setCoords();
                }
                constrainObjectToValidArea(obj);
            }
        }

        function updateZoneNameUI() {
            const el = document.getElementById('currentZoneName');
            if (!el) return;
            if (!isAdminMode()) { el.style.display = 'none'; return; }
            const areas = getAreasForView(currentView);
            if (areas && areas.length > 0) {
                const names = areas.map(function(a) { return a.name; }).join(' • ');
                el.textContent = names;
                el.style.display = 'block';
            } else {
                el.textContent = '';
                el.style.display = 'none';
            }
        }

        // -------------------------------------------------------
        // Helper: تصحيح المسارات
        // -------------------------------------------------------
        function fixImagePath(path) {
            if (!path) return null;
            if (path.startsWith('local://')) return path;
            if (path.startsWith('data:') || path.startsWith('blob:') || path.startsWith('http')) return path;
            if (path.startsWith('art://')) return path;

            let cleanPath = path.replace(/^\/design\/edit\//, '').replace(/\\/g, '/');
            cleanPath = cleanPath.replace(/^\/+/, '');

            if (cleanPath.startsWith('http')) return cleanPath;

            return assetBase + '/' + cleanPath;
        }

        function resolveImageContent(src) {
            if (!src) return null;
            if (src.startsWith('local://')) {
                const imageId = src.replace('local://', '');
                const base64 = localStorage.getItem(imageId);
                return base64 || null;
            }
            if (src.startsWith('data:image')) return src;
            return src;
        }

        // -------------------------------------------------------
        // Helper: تحميل صورة Fabric بشكل Promise
        // -------------------------------------------------------
        function loadImagePromise(src, options = {}) {
            return new Promise((resolve, reject) => {
                if (!src) {
                    reject('No image source');
                    return;
                }
                const defaultOpts = {
                    crossOrigin: 'anonymous',
                    ...options
                };
                fabric.Image.fromURL(
                    src,
                    (img) => {
                        if (img) resolve(img);
                        else reject('Failed to load image: ' + src);
                    },
                    defaultOpts
                );
            });
        }

        // -------------------------------------------------------
        // Initialize canvas
        // -------------------------------------------------------
        function initCanvas() {
            console.log('[J1] initCanvas() ENTER — canvas:', !!canvas, 'currentView:', currentView, 'activeImages:', typeof getActiveProductImages === 'function' ? getActiveProductImages().length : 'N/A');
            console.log('[J1] initCanvas() — canvasViews keys at entry:', Object.keys(canvasViews).join(','));
            try {
                const canvasElement = document.getElementById('fabricCanvas');
                if (!canvasElement) {
                    console.error('Canvas element not found');
                    return false;
                }

                canvas = new fabric.Canvas('fabricCanvas', {
                    selection: true,
                    preserveObjectStacking: true,
                    width: 500,
                    height: 500,
                    backgroundColor: 'transparent',
                    renderOnAddRemove: true
                });

                if (!canvas) {
                    console.error('Failed to create fabric canvas');
                    return false;
                }

                fabric.Object.prototype.transparentCorners = false;
                fabric.Object.prototype.cornerStyle = 'circle';
                fabric.Object.prototype.cornerColor = '#ffffff';
                fabric.Object.prototype.cornerStrokeColor = '#999';
                fabric.Object.prototype.borderColor = '#4A90E2';
                fabric.Object.prototype.cornerSize = 18;

                // Remove ALL default Fabric controls — show only custom floating buttons
                ['tl', 'tr', 'bl', 'br', 'ml', 'mr', 'mt', 'mb', 'mtr'].forEach(function(key) {
                    delete fabric.Object.prototype.controls[key];
                });

                // Custom floating action buttons
                fabric.Object.prototype.controls.deleteControl = new fabric.Control({
                    x: -0.5, y: -0.5,
                    offsetX: -18, offsetY: -18,
                    cursorStyle: 'pointer',
                    mouseUpHandler: deleteObject,
                    render: renderDeleteIcon,
                    cornerSize: 32,
                    touchSizeX: 40, touchSizeY: 40,
                });

                fabric.Object.prototype.controls.rotateControl = new fabric.Control({
                    x: 0.5, y: -0.5,
                    offsetX: 18, offsetY: -18,
                    cursorStyle: 'pointer',
                    actionHandler: fabric.controlsUtils.rotationWithSnapping,
                    render: renderRotateIcon,
                    cornerSize: 32,
                    touchSizeX: 40, touchSizeY: 40,
                    actionName: 'rotate',
                });

                fabric.Object.prototype.controls.duplicateControl = new fabric.Control({
                    x: -0.5, y: 0.5,
                    offsetX: -18, offsetY: 18,
                    cursorStyle: 'pointer',
                    mouseUpHandler: duplicateObject,
                    render: renderDuplicateIcon,
                    cornerSize: 32,
                    touchSizeX: 40, touchSizeY: 40,
                });

                fabric.Object.prototype.controls.resizeControl = new fabric.Control({
                    x: 0.5, y: 0.5,
                    offsetX: 18, offsetY: 18,
                    cursorStyle: 'pointer',
                    actionHandler: fabric.controlsUtils.scalingEqually,
                    render: renderResizeIcon,
                    cornerSize: 32,
                    touchSizeX: 40, touchSizeY: 40,
                });

                canvas.on('object:modified', (e) => { if (_loadingView || _isRestoring) return; if (e.target) enforcePrintAreaBounds(e.target); saveCurrentView(); pushHistory(); hideGuides(); if (isAdminMode()) { hidePrintZones(); } else { PlacementGuide.hide(); } refreshCurrentViewThumbnail(); });
                canvas.on('object:added', function(e) { _ensureZoomObjectId(e.target); if (_loadingView || _isRestoring) return; pushHistory(); refreshCurrentViewThumbnail(); });
                canvas.on('object:removed', () => { if (_loadingView || _isRestoring) return; saveCurrentView(); pushHistory(); refreshCurrentViewThumbnail(); });

                document.addEventListener('keydown', (e) => {
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                    if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) { e.preventDefault(); undoCanvas(); }
                    if ((e.ctrlKey || e.metaKey) && e.key === 'z' && e.shiftKey) { e.preventDefault(); redoCanvas(); }
                    if ((e.ctrlKey || e.metaKey) && e.key === 'y') { e.preventDefault(); redoCanvas(); }
                });

                canvas.on('selection:created', (e) => {
                    syncPanelWithSelection(e.selected[0]);
                });
                canvas.on('selection:updated', (e) => {
                    syncPanelWithSelection(e.selected[0]);
                    if (!isAdminMode() && e.selected[0]) { updatePlacementGuide(e.selected[0], PlacementGuide.refresh); }
                });
                canvas.on('selection:cleared', () => {
                    clearSelectionPanel();
                    hideGuides();
                    if (isAdminMode()) { hidePrintZones(); } else { PlacementGuide.hide(); }
                });

                canvas.on('mouse:down', (opt) => {
                    if (!opt.target) {
                        canvas.discardActiveObject();
                        canvas.renderAll();
                        clearSelectionPanel();
                    }
                });

                canvas.on('object:moving', (e) => {
                    if (e.target) {
                        var obj = e.target;
                        var beforeX = obj.left, beforeY = obj.top;
                        enforcePrintAreaBounds(obj);
                        // Sync the drag transform offset so Fabric's engine continues
                        // from the clamped position, preventing cursor-fighting.
                        var t = canvas._transform;
                        if (t && t.action === 'drag') {
                            t.offsetX += obj.left - beforeX;
                            t.offsetY += obj.top - beforeY;
                        }
                        snapToGuides(obj);
                        if (isAdminMode()) { showPrintZones(obj); } else { updatePlacementGuide(obj, PlacementGuide.show); }
                    }
                });

                canvas.on('object:scaling', (e) => {
                    if (e.target) {
                        hideGuides();
                        if (isAdminMode()) { showPrintZones(e.target); } else { updatePlacementGuide(e.target, PlacementGuide.show); }
                    }
                });

                if (designAreas && designAreas.length > 0) {
                    currentPrintAreaIndex = 0;
                    drawPrintZones(0);
                    updateZoneNameUI();
                }

                PlacementGuide.initialize(canvas);

                console.log('[J1] initCanvas() EXIT — success. canvasViews keys:', Object.keys(canvasViews).join(','));
                return true;
            } catch (error) {
                console.error('Error initializing canvas:', error);
                return false;
            }
        }

        function applyCustomControls(obj) {
            obj.set({
                transparentCorners: false,
                cornerStyle: 'circle',
                cornerColor: '#ffffff',
                cornerStrokeColor: '#999',
                borderColor: '#4A90E2',
                cornerSize: 18,
                padding: 1
            });
            // Ensure no default Fabric controls on this object
            ['tl', 'tr', 'bl', 'br', 'ml', 'mr', 'mt', 'mb', 'mtr'].forEach(function(key) {
                delete obj.controls[key];
            });

            obj.controls.deleteControl = new fabric.Control({
                x: -0.5, y: -0.5,
                offsetX: -8, offsetY: -8,
                cursorStyle: 'pointer',
                mouseUpHandler: deleteObject,
                render: renderDeleteIcon,
                cornerSize: 32,
                touchSizeX: 40, touchSizeY: 40,
            });

            obj.controls.rotateControl = new fabric.Control({
                x: 0.5, y: -0.5,
                offsetX: 8, offsetY: -8,
                cursorStyle: 'pointer',
                actionHandler: fabric.controlsUtils.rotationWithSnapping,
                render: renderRotateIcon,
                cornerSize: 32,
                touchSizeX: 40, touchSizeY: 40,
                actionName: 'rotate',
            });

            obj.controls.duplicateControl = new fabric.Control({
                x: -0.5, y: 0.5,
                offsetX: -8, offsetY: 8,
                cursorStyle: 'pointer',
                mouseUpHandler: duplicateObject,
                render: renderDuplicateIcon,
                cornerSize: 32,
                touchSizeX: 40, touchSizeY: 40,
            });

            obj.controls.resizeControl = new fabric.Control({
                x: 0.5, y: 0.5,
                offsetX: 8, offsetY: 8,
                cursorStyle: 'pointer',
                actionHandler: fabric.controlsUtils.scalingEqually,
                render: renderResizeIcon,
                cornerSize: 32,
                touchSizeX: 40, touchSizeY: 40,
            });
        }

        // ============================================================
        // Product Image — load product photo as canvas background
        // ============================================================
        // -------------------------------------------------------
        // تحميل صورة المنتج كـ background
        // -------------------------------------------------------
        function loadProductImage(src) {
            if (!canvas || !src) return Promise.resolve();
            const cleanSrc = fixImagePath(src);
            console.log('[PRODUCT IMAGE] productId:', currentProductId, 'view:', currentView, 'src:', cleanSrc);

            if (imageCache[cleanSrc]) {
                const cachedImg = imageCache[cleanSrc];
                console.log('[PRODUCT IMAGE] Using CACHED image, dimensions:', cachedImg.width, 'x', cachedImg.height);
                canvas.setBackgroundImage(cachedImg, canvas.renderAll.bind(canvas), {
                    scaleX: canvas.width / cachedImg.width,
                    scaleY: canvas.height / cachedImg.height,
                    crossOrigin: 'anonymous'
                });
                var bgAfter = canvas.backgroundImage ? (typeof canvas.backgroundImage.getSrc === 'function' ? canvas.backgroundImage.getSrc() : 'no-getSrc') : 'NO BG';
                console.log('[CANVAS BACKGROUND] cached path — src:', bgAfter);
                return Promise.resolve();
            }

            return new Promise(function(resolve) {
                fabric.Image.fromURL(cleanSrc, function(img) {
                    if (img && canvas) {
                        imageCache[cleanSrc] = img;
                        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                            scaleX: canvas.width / img.width,
                            scaleY: canvas.height / img.height,
                            crossOrigin: 'anonymous'
                        });
                    }
                    var bgAfter2 = canvas.backgroundImage ? (typeof canvas.backgroundImage.getSrc === 'function' ? canvas.backgroundImage.getSrc() : 'no-getSrc') : 'NO BG';
                    console.log('[CANVAS BACKGROUND] fresh load path — src:', bgAfter2);
                    resolve();
                }, {
                    crossOrigin: 'anonymous'
                });
            });
        }

        // ============================================================
        // View Switching — changeImage + _executeChangeImage
        // ============================================================
        // -------------------------------------------------------
        // تغيير الصورة مع حفظ واسترجاع كامل للتصميم
        // -------------------------------------------------------
        async function changeImage(src, index, skipSave = false) {
            if (!canvas) {
                console.log('[FLOW] changeImage — EARLY RETURN, canvas is null');
                return;
            }
            var prev = _renderPromise;
            var _thisResolve;
            _renderPromise = new Promise(function(r) { _thisResolve = r; });
            await prev;
            try {
                await _executeChangeImage(src, index, skipSave);
            } finally {
                _thisResolve();
            }
        }

        async function _executeChangeImage(src, index, skipSave = false) {
            console.log('[FLOW] _executeChangeImage() called — src:', src, 'index:', index, 'skipSave:', skipSave);
            console.log('[FLOW] _executeChangeImage — canvasViews[' + index + ']:', canvasViews[index] ? (canvasViews[index].objects ? canvasViews[index].objects.length + ' objects' : 'objects null') : 'undefined');

            try {
                if (_saveTimer) { clearTimeout(_saveTimer); _saveTimer = null; }
                _loadingView = true;
                if (!skipSave) await saveCurrentView();
                currentView = index;
                const savedView = canvasViews[index];

                console.log('[FLOW] _executeChangeImage — clearing canvas, current objects:', canvas.getObjects().map(function(o) { return (o._debugId || 'no-id') + '/' + o.type; }));
                canvas.getObjects().forEach(function(o) { _logRemove(o, 'changeImage-clear'); });
                canvas.clear();
                await loadProductImage(src);

                currentPrintAreaIndex = 0;
                drawPrintZones(index);
                updateZoneNameUI();

                if (savedView && savedView.objects && savedView.objects.length > 0) {
                    console.log(`[FLOW] _executeChangeImage — Loading saved design for view ${index} with ${savedView.objects.length} objects`);

                    // ---- Pre-load fonts before text creation ----
                    if (window.ZoomStore && ZoomStore.FontManager) {
                        var textFamilies = {};
                        for (var fi = 0; fi < savedView.objects.length; fi++) {
                            var od = savedView.objects[fi];
                            if ((od.type === 'i-text' || od.type === 'text' || od.type === 'textbox') && od.fontFamily) {
                                textFamilies[od.fontFamily] = true;
                            }
                        }
                        var fontKeys = Object.keys(textFamilies);
                        console.log('[FONT_CHANGE] Pre-load fonts:', JSON.stringify(fontKeys));
                        fontKeys.forEach(function(f) {
                            console.log('[FONT_CHANGE] font ' + f + ' — isLoaded:', ZoomStore.FontManager.isLoaded(f), 'isLoading:', ZoomStore.FontManager.isLoading(f));
                        });
                        var loadPromises = fontKeys.map(function(f) {
                            return ZoomStore.FontManager.loadFont(f);
                        });
                        var loadedCount = fontKeys.filter(function(f) { return ZoomStore.FontManager.isLoaded(f); }).length;
                        console.log('[FONT_CHANGE] Already loaded:', loadedCount, 'of', fontKeys.length);
                        try {
                            var FONT_LOAD_TIMEOUT = 10000;
                            await Promise.race([
                                Promise.all(loadPromises),
                                new Promise(function(resolve) {
                                    setTimeout(function() {
                                        console.warn('[FONT_CHANGE] Font loading timed out after ' + FONT_LOAD_TIMEOUT + 'ms, continuing with fallback');
                                        resolve();
                                    }, FONT_LOAD_TIMEOUT);
                                })
                            ]);
                            console.log('[FONT_CHANGE] All fonts loaded (or timed out) after await');
                        } catch (fontErr) {
                            console.warn('[FONT_CHANGE] Some fonts failed to load, continuing with fallbacks:', fontErr);
                        }
                        fontKeys.forEach(function(f) {
                            console.log('[FONT_CHANGE] font ' + f + ' — isLoaded AFTER:', ZoomStore.FontManager.isLoaded(f));
                        });
                    }

                    // ---- النصوص أولاً ----
                    for (const objData of savedView.objects) {
                        if (objData.type === 'i-text' || objData.type === 'text' || objData.type === 'textbox') {
                            try {
                                var textCtor = objData.type === 'textbox' ? fabric.Textbox : fabric.Text;
                                var opts = {
                                    left: objData.left || 150,
                                    top: objData.top || 150,
                                    fontSize: objData.fontSize || 20,
                                    fill: objData.fill || '#000000',
                                    fontFamily: objData.fontFamily || 'Cairo',
                                    fontWeight: objData.fontWeight || 'normal',
                                    fontStyle: objData.fontStyle || 'normal',
                                    angle: objData.angle || 0,
                                    textAlign: objData.textAlign || 'center',
                                    charSpacing: objData.charSpacing || 0,
                                    lineHeight: objData.lineHeight || 1.2,
                                    underline: objData.underline || false,
                                    overline: objData.overline || false,
                                    linethrough: objData.linethrough || false,
                                    stroke: objData.stroke || null,
                                    strokeWidth: objData.strokeWidth || 0,
                                    direction: objData.direction || null,
                                    width: objData.width || 150,
                                    scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                                    scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                    originX: objData.originX || 'left',
                                    originY: objData.originY || 'top',
                                    hasControls: true,
                                    hasBorders: true
                                };
                                var text = new textCtor(objData.text || 'اكتب هنا', opts);
                                text._debugId = objData._debugId || _newDebugId();
                                text._zoomObjectId = objData._zoomObjectId || _generateZoomObjectId();
                                text._slotKey = objData._slotKey || null;
                                _logCreate(text, 'changeImage-text');
                                if (objData.shadow) {
                                    text.set('shadow', new fabric.Shadow(objData.shadow));
                                }
                                applyCustomControls(text);
                                canvas.add(text);
                                _logAdd(text, 'canvas');
                            } catch (err) {
                                console.warn('Error recreating text object:', err);
                            }
                        }
                    }

                    // ---- الأصول (asset) ----
                    // Ensure catalog is loaded before asset restoration so
                    // lookupCapabilities() returns correct data from catalog
                    if (window.ZoomStore && ZoomStore.SVGLoader) {
                        await ZoomStore.SVGLoader.init();
                    }
                    for (const objData of savedView.objects) {
                        if (objData.type === 'asset' && objData._assetMeta) {
                            try {
                                var assetObj = await ZoomStore.AssetManager.addToCanvas(
                                    objData._assetMeta.adapter + ':' + objData._assetMeta.category,
                                    objData._assetMeta.assetId,
                                    canvas,
                                    {
                                        left: objData.left,
                                        top: objData.top,
                                        originX: objData.originX || 'left',
                                        originY: objData.originY || 'top',
                                        color: objData._artColor != null && objData._artColor !== '' ? objData._artColor : '#ffffff',
                                        emboss: objData._embossLevel || 0
                                    }
                                );
                                if (assetObj) {
                                    assetObj._debugId = objData._debugId || _newDebugId();
                                    assetObj._zoomObjectId = objData._zoomObjectId || _generateZoomObjectId();
                                    assetObj._slotKey = objData._slotKey || null;
                                    _logCreate(assetObj, 'changeImage-asset');
                                    assetObj.set({
                                        angle: objData.angle || 0,
                                        scaleX: objData.scaleX || 1,
                                        scaleY: objData.scaleY || 1
                                    });
                                    if (objData.shadow) {
                                        assetObj.set('shadow', new fabric.Shadow(objData.shadow));
                                    }
                                    if (objData.stroke) assetObj.set('stroke', objData.stroke);
                                    if (objData.fill) assetObj.set('fill', objData.fill);
                                    applyCustomControls(assetObj);
                                    canvas.add(assetObj);
                                    _logAdd(assetObj, 'canvas');
                                }
                            } catch (err) {
                                console.warn('Error loading asset:', err);
                            }
                        }
                    }

                    // ---- الصور ثانياً (بالتسلسل للحفاظ على الترتيب) ----
                    // FIX #4: تحميل الصور بالتسلسل بدل Promise.all لضمان الترتيب
                    for (const objData of savedView.objects) {
                        if (objData.type === 'image' && objData._customSrc) {
                            try {
                                let imageSrc = objData._customSrc;
                                let img = null;

                                if (imageSrc.startsWith('local://')) {
                                    const imageId = imageSrc.replace('local://', '');
                                    const base64Data = localStorage.getItem(imageId);
                                    if (base64Data) {
                                        img = await loadImagePromise(base64Data);
                                    } else {
                                        console.warn('Image not found in localStorage:', imageId);
                                    }
                                } else {
                                    imageSrc = fixImagePath(imageSrc);
                                    if (uploadedImagesCache[imageSrc]) {
                                        img = fabric.util.object.clone(uploadedImagesCache[imageSrc]);
                                    } else {
                                        img = await loadImagePromise(imageSrc);
                                        if (img) uploadedImagesCache[imageSrc] = img;
                                    }
                                }

                                if (img) {
                                    img._debugId = objData._debugId || _newDebugId();
                                    img._zoomObjectId = objData._zoomObjectId || _generateZoomObjectId();
                                    img._slotKey = objData._slotKey || null;
                                    _logCreate(img, 'changeImage-image');
                                    img.set({
                                        left: objData.left || 100,
                                        top: objData.top || 100,
                                        angle: objData.angle || 0,
                                        // FIX #3: استرجاع الـ scaleX/scaleY الحقيقيين للصور أيضاً
                                        scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                                        scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                        originX: objData.originX || 'left',
                                        originY: objData.originY || 'top',
                                        hasControls: true,
                                        hasBorders: true
                                    });
                                    // FIX #4: حفظ المرجع للمسار الأصلي
                                    img._customSrc = objData._customSrc;
                                    applyCustomControls(img);
                                    canvas.add(img);
                                    _logAdd(img, 'canvas');
                                }
                            } catch (err) {
                                console.warn('Error loading image:', objData._customSrc, err);
                            }
                        }
                    }
                } else if (!savedView) {
                    console.log(`[FLOW] _executeChangeImage — savedView is UNDEFINED for view ${index}, rendering new canvas`);
                } else if (!savedView.objects) {
                    console.log(`[FLOW] _executeChangeImage — savedView.objects is NULL for view ${index}`);
                } else {
                    console.log(`[FLOW] _executeChangeImage — savedView has ZERO objects for view ${index}`);
                }

                // One-time bounds enforcement after restoration — objects must be
                // inside their printable areas after any view change or product switch.
                canvas.getObjects().forEach(function(o) {
                    if (!o._isPrintZone && !o.excludeFromExport) enforcePrintAreaBounds(o);
                });
                canvas.renderAll();
                if (typeof loadCurrentViewFonts === 'function') loadCurrentViewFonts();
                refreshCurrentViewThumbnail();
                if (typeof updateActionBar === 'function') updateActionBar();
                console.log('[FLOW] _executeChangeImage — AFTER renderAll. currentView:', currentView, 'canvas objects:', canvas.getObjects().length, 'canvasViews[' + currentView + ']:', canvasViews[currentView] ? (canvasViews[currentView].objects ? canvasViews[currentView].objects.length + ' objects' : 'null') : 'undefined');
            } catch (error) {
                console.error('[FLOW] _executeChangeImage — Error:', error);
            } finally {
                _loadingView = false;
            }
        }

        // ============================================================
        // Text Editor — add, edit, sync text objects
        // ============================================================
        // -------------------------------------------------------
        // إضافة / تعديل نص
        // -------------------------------------------------------
        function addTextFromPanel() {
            if (!canvas) return;

            const textValue = document.getElementById('textInput').value.trim();
            if (!textValue) {
                alert('اكتب النص أولاً');
                return;
            }

            const fontFamily = document.getElementById('fontFamily').value;
            const fill = document.getElementById('textColor').value;
            const fontSize = parseInt(document.getElementById('textSize').value) || 24;
            const angle = parseInt(document.getElementById('textRotate').value) || 0;
            const isBold = document.getElementById('textBoldToggle').classList.contains('active');
            const fontWeight = isBold ? 'bold' : 'normal';

            try {
                if (editingTextObj) {
                    editingTextObj.set({
                        text: textValue,
                        fontFamily,
                        fill,
                        fontSize,
                        angle,
                        fontWeight
                    });
                    canvas.renderAll();
                    saveCurrentView();
                    pushHistory();
                    resetTextPanel();
                } else {
                    const center = getPrintZoneCenter();
                   const text = new fabric.Textbox(textValue, {
    left: center.left,
    top: center.top,

    originX: 'center',
    originY: 'center',

    textAlign: 'center',

    fontSize,
    fill,
    fontFamily,
    fontWeight,
    angle,

    padding: 0,

    width: Math.max(70, textValue.length * fontSize * 0.58),

    hasControls: true,
    hasBorders: true,

    splitByGrapheme: false
});
                    text._debugId = _newDebugId();
                    text._slotKey = getNearestSlotKey(text);
                    _logCreate(text, 'addTextFromPanel');
                    applyCustomControls(text);
                    canvas.add(text);
                    enforcePrintAreaBounds(text);
                    _logAdd(text, 'canvas');
                    canvas.setActiveObject(text);
                    canvas.renderAll();
                    saveCurrentView();
                    pushHistory();
                    document.getElementById('textInput').value = '';
                }
            } catch (error) {
                console.error('Error adding text:', error);
            }
        }

        function toggleTextBold() {
            const btn = document.getElementById('textBoldToggle');
            btn.classList.toggle('active');
            const obj = canvas ? canvas.getActiveObject() : null;
            if (obj && obj.type && obj.type.includes('text')) {
                obj.set('fontWeight', btn.classList.contains('active') ? 'bold' : 'normal');
                canvas.renderAll();
                debouncedSave();
            }
        }

        function resetTextPanel() {
            editingTextObj = null;
            document.getElementById('textInput').value = '';
            document.getElementById('btnAddText').textContent = 'إضافة للتصميم';
            document.getElementById('textBoldToggle').classList.remove('active');
        }

        function syncPanelWithSelection(obj) {
            if (!obj) return;

            if (obj.type && obj.type.includes('text')) {
                navigateTo('text');
                editingTextObj = obj;
                document.getElementById('textInput').value = obj.text || '';
                document.getElementById('fontFamily').value = obj.fontFamily || 'Cairo';
                document.getElementById('textColor').value = rgbToHex(obj.fill) || '#ffffff';
                const hexEl = document.getElementById('textColorHex');
                if (hexEl) hexEl.textContent = rgbToHex(obj.fill) || '#ffffff';
                document.getElementById('textSize').value = obj.fontSize || 24;
                document.getElementById('textSizeVal').textContent = obj.fontSize || 24;
                document.getElementById('textRotate').value = Math.round(obj.angle || 0);
                document.getElementById('textRotateVal').textContent = Math.round(obj.angle || 0);
                const boldBtn = document.getElementById('textBoldToggle');
                if (obj.fontWeight === 'bold' || obj.fontWeight >= 700) {
                    boldBtn.classList.add('active');
                } else {
                    boldBtn.classList.remove('active');
                }
                document.getElementById('btnAddText').textContent = 'حفظ التعديل';
            } else if (obj._isArt || obj._assetMeta) {
                navigateTo('art');
                document.getElementById('art-controls').style.display = 'block';
                if (window.ZoomStore && ZoomStore.ColorManager) {
                    ZoomStore.ColorManager.syncPicker(obj);
                } else {
                    document.getElementById('artColor').value = obj._artColor || '#ffffff';
                    document.getElementById('artColorHex').textContent = obj._artColor || '#ffffff';
                }
                const size = Math.round((obj.scaleX || 1) * 80);
                document.getElementById('artSize').value = size;
                document.getElementById('artSizeVal').textContent = size;
                document.getElementById('artRotate').value = Math.round(obj.angle || 0);
                document.getElementById('artRotateVal').textContent = Math.round(obj.angle || 0);
                document.getElementById('artEmboss').value = obj._embossLevel || 0;
                document.getElementById('artEmbossVal').textContent = obj._embossLevel || 0;
            } else if (obj.type === 'image') {
                navigateTo('upload');
            }
        }

        function clearSelectionPanel() {
            resetTextPanel();
            document.getElementById('art-controls').style.display = 'none';
        }

        function rgbToHex(color) {
            if (!color) return '#ffffff';
            if (color.startsWith('#')) return color;
            const match = color.match(/\d+/g);
            if (!match) return '#ffffff';
            return '#' + match.slice(0, 3).map(x => parseInt(x).toString(16).padStart(2, '0')).join('');
        }

        // ============================================================
        // Controls Setup — wire DOM controls to Fabric objects
        // ============================================================
        // -------------------------------------------------------
        // أدوات التحكم (نص وصورة)
        // -------------------------------------------------------
        function setupControls() {
            const textColor = document.getElementById('textColor');
            const fontFamily = document.getElementById('fontFamily');
            const textSize = document.getElementById('textSize');
            const textRotate = document.getElementById('textRotate');
            const imageSize = document.getElementById('imageSize');
            const imageRotate = document.getElementById('imageRotate');
            const artColor = document.getElementById('artColor');
            const artSize = document.getElementById('artSize');
            const artEmboss = document.getElementById('artEmboss');
            const artRotate = document.getElementById('artRotate');

            if (textColor) {
                textColor.addEventListener('input', function() {
                    const hexEl = document.getElementById('textColorHex');
                    if (hexEl) hexEl.textContent = this.value;
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('fill', this.value);
                        canvas.requestRenderAll();
                    }
                });
                textColor.addEventListener('change', function() {
                    saveCurrentView();
                    pushHistory();
                });
            }
            if (fontFamily) {
                fontFamily.addEventListener('change', async function() {
                    console.log('[FONT_CHANGE] fontFamily dropdown changed to:', this.value);
                    if (window.ZoomStore && ZoomStore.FontManager) {
                        console.log('[FONT_CHANGE] isLoaded:', ZoomStore.FontManager.isLoaded(this.value), 'isLoading:', ZoomStore.FontManager.isLoading(this.value));
                        ZoomStore.FontManager.addRecent(this.value);
                        await ZoomStore.FontManager.loadFont(this.value);
                        console.log('[FONT_CHANGE] isLoaded AFTER loadFont:', ZoomStore.FontManager.isLoaded(this.value));
                    }
                    const obj = canvas.getActiveObject();
                    console.log('[FONT_CHANGE] activeObject:', obj ? obj.type : 'none');
                    if (obj && obj.type.includes('text')) {
                        console.log('[FONT_CHANGE] setting fontFamily on object, current width:', obj.width);
                        obj.set('fontFamily', this.value);
                        if (typeof obj.initDimensions === 'function') {
                            console.log('[FONT_CHANGE] calling initDimensions()');
                            obj.initDimensions();
                        }
                        if (typeof obj.setCoords === 'function') obj.setCoords();
                        canvas.renderAll();
                        saveCurrentView();
                        console.log('[FONT_CHANGE] after save — new width:', obj.width);
                    }
                });
            }

            // ── Font search ──
            var fontSearch = document.getElementById('fontSearch');
            if (fontSearch) {
                fontSearch.addEventListener('input', function() {
                    var q = this.value;
                    var select = document.getElementById('fontFamily');
                    if (!select) return;
                    if (!q || q.trim() === '') {
                        if (window.ZoomStore && ZoomStore.FontManager) {
                            ZoomStore.FontManager.populateSelect(select);
                        }
                        return;
                    }
                    var results = window.ZoomStore ? ZoomStore.FontManager.search(q) : [];
                    select.innerHTML = '';
                    results.forEach(function(f) {
                        var opt = document.createElement('option');
                        opt.value = f.family;
                        opt.textContent = f.family + ' (' + f.category + ')';
                        select.appendChild(opt);
                    });
                });
            }

            // ── Font favorites toggle ──
            var fontFavToggle = document.getElementById('fontFavToggle');
            if (fontFavToggle) {
                fontFavToggle.addEventListener('click', function() {
                    var select = document.getElementById('fontFamily');
                    if (!select || !select.value) return;
                    if (window.ZoomStore && ZoomStore.FontManager) {
                        var isFav = ZoomStore.FontManager.toggleFav(select.value);
                        this.textContent = isFav ? '★' : '☆';
                        ZoomStore.FontManager.populateSelect(select);
                        ZoomStore.FontManager.syncSelect(select, select.value);
                    }
                });
            }
            if (textSize) {
                textSize.addEventListener('input', function() {
                    document.getElementById('textSizeVal').textContent = this.value;
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('fontSize', parseInt(this.value));
                        if (obj.type === 'textbox' && typeof obj.initDimensions === 'function') {
                            obj.initDimensions();
                            var w = obj.calcTextWidth();
                            if (w && w > 0) obj.set('width', w);
                            obj.setCoords();
                        }
                        canvas.requestRenderAll();
                    }
                });
                textSize.addEventListener('change', function() {
                    saveCurrentView();
                    pushHistory();
                });
            }
            if (textRotate) {
                textRotate.addEventListener('input', function() {
                    document.getElementById('textRotateVal').textContent = this.value;
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('angle', parseInt(this.value));
                        canvas.requestRenderAll();
                    }
                });
                textRotate.addEventListener('change', function() {
                    saveCurrentView();
                    pushHistory();
                });
            }
            if (imageSize) {
                imageSize.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type === 'image') {
                        obj.scale(parseInt(this.value) / 100);
                        canvas.requestRenderAll();
                    }
                });
                imageSize.addEventListener('change', function() {
                    saveCurrentView();
                    pushHistory();
                });
            }
            if (imageRotate) {
                imageRotate.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type === 'image') {
                        obj.set('angle', parseInt(this.value));
                        canvas.requestRenderAll();
                    }
                });
                imageRotate.addEventListener('change', function() {
                    saveCurrentView();
                    pushHistory();
                });
            }
            if (artColor) {
                artColor.addEventListener('input', function() {
                    var obj = canvas.getActiveObject();
                    if (obj && (obj._isArt || obj._assetMeta)) {
                        document.getElementById('artColorHex').textContent = this.value;
                        if (window.ZoomStore && ZoomStore.ColorManager) {
                            ZoomStore.ColorManager.applyColor(obj, this.value);
                        } else {
                            applyArtStyle(obj, { color: this.value });
                        }
                        canvas.requestRenderAll();
                    }
                });
                artColor.addEventListener('change', function() {
                    saveCurrentView();
                    pushHistory();
                });
            }
            if (artSize) {
                artSize.addEventListener('input', function() {
                    document.getElementById('artSizeVal').textContent = this.value;
                    const obj = canvas.getActiveObject();
                    if (obj && (obj._isArt || obj._assetMeta)) {
                        const scale = parseInt(this.value) / 80;
                        obj.set({ scaleX: scale, scaleY: scale });
                        canvas.requestRenderAll();
                    }
                });
                artSize.addEventListener('change', function() {
                    saveCurrentView();
                    pushHistory();
                });
            }
            if (artEmboss) {
                artEmboss.addEventListener('input', function() {
                    document.getElementById('artEmbossVal').textContent = this.value;
                    var obj = canvas.getActiveObject();
                    if (obj && (obj._isArt || obj._assetMeta)) {
                        obj._embossLevel = parseInt(this.value);
                        if (obj._embossLevel > 0) {
                            obj.set('shadow', new fabric.Shadow({
                                color: 'rgba(0,0,0,0.45)',
                                blur: obj._embossLevel,
                                offsetX: obj._embossLevel / 3,
                                offsetY: obj._embossLevel / 3
                            }));
                        } else {
                            obj.set('shadow', null);
                        }
                        canvas.requestRenderAll();
                    }
                });
                artEmboss.addEventListener('change', function() {
                    saveCurrentView();
                    pushHistory();
                });
            }
            if (artRotate) {
                artRotate.addEventListener('input', function() {
                    document.getElementById('artRotateVal').textContent = this.value;
                    const obj = canvas.getActiveObject();
                    if (obj && (obj._isArt || obj._assetMeta)) {
                        obj.set('angle', parseInt(this.value));
                        canvas.requestRenderAll();
                    }
                });
                artRotate.addEventListener('change', function() {
                    saveCurrentView();
                    pushHistory();
                });
            }
        }

        // ============================================================
        // Art System — categories, items, SVG loading
        // ============================================================
        // -------------------------------------------------------
        // مكتبة الرسومات
        // -------------------------------------------------------
        function renderArtCategories() {
            if (!window.DesignArtLib) return;
            const grid = document.getElementById('artCategoriesGrid');
            if (!grid) return;
            grid.innerHTML = '';

            // Legacy categories
            DesignArtLib.artCategories.forEach((cat, i) => {
                const card = document.createElement('div');
                card.className = 'art-category-card';
                card.innerHTML = (DesignArtLib.svgIcons[cat.icon] || DesignArtLib.svgIcons.star) +
                    '<span class="fw-bold" style="font-size:13px;">' + cat.name + '</span>';
                card.onclick = () => showArtItems(i);
                grid.appendChild(card);
            });

            // File-based categories via AssetManager
            if (window.ZoomStore && ZoomStore.AssetManager && ZoomStore.SVGAdapter) {
                var fileCats = ZoomStore.AssetManager.getCategories().filter(function(c) {
                    return c.source === 'file' || c.source === 'both';
                });
                fileCats.forEach(function(cat) {
                    var alreadyRendered = false;
                    DesignArtLib.artCategories.forEach(function(lc) {
                        var lcId = lc.id || lc.name;
                        var catOrig = cat.originalId || cat.id.replace('svg:', '');
                        if (lcId === catOrig || lc.name === cat.name) alreadyRendered = true;
                    });
                    if (alreadyRendered) return;

                    const card = document.createElement('div');
                    card.className = 'art-category-card';
                    card.innerHTML = (DesignArtLib.svgIcons[cat.icon] || DesignArtLib.svgIcons.star) +
                        '<span class="fw-bold" style="font-size:13px;">' + cat.name + '</span>';
                    card.onclick = function() { showFileItems(cat.id); };
                    grid.appendChild(card);
                });
            }
        }

        function showArtItems(catIndex) {
            if (!window.DesignArtLib) return;
            const cat = DesignArtLib.artCategories[catIndex];
            document.getElementById('art-categories-view').style.display = 'none';
            document.getElementById('art-items-view').style.display = 'block';
            document.getElementById('artCategoryTitle').textContent = cat.name;
            const grid = document.getElementById('artItemsGrid');
            grid.innerHTML = '';
            cat.items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'art-item-card';
                div.innerHTML = DesignArtLib.svgIcons[item] || DesignArtLib.svgIcons.star;
                div.onclick = () => addArtToCanvas(cat.id || cat.name, item);
                grid.appendChild(div);
            });
        }

        function showFileItems(catId) {
            if (!window.ZoomStore || !ZoomStore.AssetManager) return;
            document.getElementById('art-categories-view').style.display = 'none';
            document.getElementById('art-items-view').style.display = 'block';
            document.getElementById('artCategoryTitle').textContent = 'جار التحميل...';
            const grid = document.getElementById('artItemsGrid');
            grid.innerHTML = '<div class="text-center p-4"><div class="spinner-border"></div></div>';

            // PreviewManager and SVGLoader expect a clean category ID (no adapter prefix)
            var cleanCatId = catId.indexOf(':') !== -1 ? catId.substring(catId.indexOf(':') + 1) : catId;

            ZoomStore.AssetManager.getCategoryItems(catId).then(function(items) {
                if (window.ZoomStore && ZoomStore.PreviewManager) {
                    ZoomStore.PreviewManager.render(grid, items, cleanCatId, function(item) {
                        addArtFromFile(catId, item);
                    });
                } else {
                    grid.innerHTML = '';
                    if (!items || items.length === 0) {
                        grid.innerHTML = '<p class="text-muted text-center p-4">لا توجد رسومات متاحة</p>';
                        return;
                    }
                    items.forEach(function(item) {
                        const div = document.createElement('div');
                        div.className = 'art-item-card';
                        if (item.source === 'legacy' && item.svgContent) {
                            div.innerHTML = item.svgContent;
                        } else {
                            div.innerHTML = '<div class="org" style="background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:32px;color:#999;">🖼</div>';
                        }
                        div.setAttribute('data-thumb-id', item.id || '');
                        div.onclick = function() { addArtFromFile(catId, item); };
                        grid.appendChild(div);
                    });
                }
            }).catch(function(err) {
                console.warn('showFileItems error:', err);
                grid.innerHTML = '<p class="text-danger text-center p-4">فشل تحميل الرسومات</p>';
            });
        }

        function addArtFromFile(catId, item) {
            if (!canvas || !window.ZoomStore || !ZoomStore.AssetManager) return;
            var itemId = (typeof item === 'string') ? item : item.id;
            if (!itemId) return;

            ZoomStore.AssetManager.addToCanvas(catId, itemId, canvas).then(function(obj) {
                if (!obj) return;
                const center = getPrintZoneCenter();
                obj.set({
                    left: center.left,
                    top: center.top,
                    originX: 'center',
                    originY: 'center'
                });
                canvas.add(obj);
                enforcePrintAreaBounds(obj);
                canvas.setActiveObject(obj);
                canvas.renderAll();
                saveCurrentView();
                pushHistory();
                document.getElementById('art-controls').style.display = 'block';
            }).catch(function(err) {
                console.warn('addArtFromFile error:', err);
            });
        }

        function backToArtCategories() {
            document.getElementById('art-categories-view').style.display = 'block';
            document.getElementById('art-items-view').style.display = 'none';
        }

        function legacyItemCategory(itemKey) {
            var result = "general";
            if (!window.DesignArtLib) return result;
            for (var lc = 0; lc < DesignArtLib.artCategories.length; lc++) {
                var cat = DesignArtLib.artCategories[lc];
                if (cat.items && cat.items.indexOf(itemKey) >= 0) {
                    result = cat.id || cat.name;
                    break;
                }
            }
            return result;
        }

        function searchArt() {
            if (!window.DesignArtLib) return;
            const q = document.getElementById('artSearchInput').value.toLowerCase().trim();
            if (!q) {
                backToArtCategories();
                return;
            }
            document.getElementById('art-categories-view').style.display = 'none';
            document.getElementById('art-items-view').style.display = 'block';
            document.getElementById('artCategoryTitle').textContent = 'نتائج البحث';
            const grid = document.getElementById('artItemsGrid');
            grid.innerHTML = '<div class="text-center p-4"><div class="spinner-border"></div></div>';

            // Legacy search (existing)
            const allKeys = Object.keys(DesignArtLib.svgIcons);
            const catMatches = DesignArtLib.artCategories
                .filter(c => c.name.toLowerCase().includes(q))
                .flatMap(c => c.items);
            const legacyMatches = [...new Set([
                ...allKeys.filter(k => k.toLowerCase().includes(q)),
                ...catMatches
            ])].slice(0, 20);

            // File search via SearchManager
            var filePromise = (window.ZoomStore && ZoomStore.SearchManager)
                ? ZoomStore.SearchManager.searchAll(q)
                : Promise.resolve({ results: [] });

            filePromise.then(function(searchResult) {
                grid.innerHTML = '';
                var hasAny = false;

                // Render legacy matches
                legacyMatches.forEach(function(item) {
                    hasAny = true;
                    const div = document.createElement('div');
                    div.className = 'art-item-card';
                    div.innerHTML = DesignArtLib.svgIcons[item] || DesignArtLib.svgIcons.star;
                    var itemCat = legacyItemCategory(item);
                    div.onclick = function() { addArtToCanvas(itemCat, item); };
                    grid.appendChild(div);
                });

                // Render file matches
                var fileResults = searchResult.results || [];
                fileResults.forEach(function(result) {
                    if (result.source !== 'svg' && result.adapter !== 'svg') return;
                    hasAny = true;
                    const div = document.createElement('div');
                    div.className = 'art-item-card';
                    div.innerHTML = '<div class="org" style="background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:24px;color:#999;">🖼</div>';
                    div.onclick = function() {
                        var catId = result._catId || 'svg:' + result.category;
                        addArtFromFile(catId, { id: result.id });
                    };
                    grid.appendChild(div);
                });

                if (!hasAny) {
                    grid.innerHTML = '<p class="text-muted text-center p-4">لا توجد نتائج</p>';
                }
            }).catch(function(err) {
                console.warn('Search error:', err);
            });
        }

        function addArtToCanvas(catId, iconKey) {
            if (!canvas || !window.ZoomStore || !ZoomStore.AssetManager) return;
            var center = getPrintZoneCenter();
            ZoomStore.AssetManager.addToCanvas('svg:' + catId, iconKey, canvas, {
                left: center.left,
                top: center.top,
                originX: 'center',
                originY: 'center'
            }).then(function(obj) {
                if (!obj) return;
                obj._debugId = _newDebugId();
                obj._slotKey = getNearestSlotKey(obj);
                _logCreate(obj, 'addArtToCanvas');
                applyCustomControls(obj);
                canvas.add(obj);
                enforcePrintAreaBounds(obj);
                _logAdd(obj, 'canvas');
                canvas.setActiveObject(obj);
                canvas.renderAll();
                saveCurrentView();
                pushHistory();
                document.getElementById('art-controls').style.display = 'block';
            });
        }

        function applyArtStyle(obj, opts) {
            if (!obj || (!obj._isArt && !obj._assetMeta) || !canvas) return;
            if (opts.color && window.ZoomStore && ZoomStore.ColorManager) {
                ZoomStore.ColorManager.applyColor(obj, opts.color);
            } else if (opts.color) {
                obj._artColor = opts.color;
                obj.set('stroke', opts.color);
                if (obj._objects) {
                    obj._objects.forEach(o => {
                        if (o.set) o.set({ stroke: opts.color, fill: o.fill && o.fill !== 'none' ? opts.color : '' });
                    });
                }
            }
            if (opts.emboss !== undefined) {
                obj._embossLevel = opts.emboss;
                if (opts.emboss > 0) {
                    obj.set('shadow', new fabric.Shadow({
                        color: 'rgba(0,0,0,0.45)',
                        blur: opts.emboss,
                        offsetX: opts.emboss / 3,
                        offsetY: opts.emboss / 3
                    }));
                } else {
                    obj.set('shadow', null);
                }
            }
            if (!_loadingView) canvas.renderAll();
            debouncedSave();
        }

        function pushHistory() {
            if (!canvas) { console.log('[J3] pushHistory() — EARLY RETURN, canvas null'); return; }
            const json = JSON.stringify(canvas.toJSON(['_customSrc', '_isArt', '_artKey', '_artColor', '_embossLevel', '_assetMeta', '_slotKey', '_zoomObjectId']));
            canvasHistory = canvasHistory.slice(0, historyIndex + 1);
            canvasHistory.push(json);
            historyIndex = canvasHistory.length - 1;
            console.log('[J3] pushHistory() — historyIndex:', historyIndex, 'stack length:', canvasHistory.length, 'canvas objects:', canvas.getObjects().filter(function(o) { return o !== canvas.backgroundImage; }).length);
        }

        function restoreAssetCapabilities() {
            if (!canvas) return;
            var catalog = window.ZoomStore && ZoomStore.SVGLoader && ZoomStore.SVGLoader.getCatalog();
            if (!catalog || !catalog.categories) return;
            var objects = canvas.getObjects();
            for (var oi = 0; oi < objects.length; oi++) {
                var obj = objects[oi];
                if (!obj._assetMeta || obj._capabilities) continue;
                var catId = obj._assetMeta.category;
                var assetId = obj._assetMeta.assetId;
                var found = false;
                for (var ci = 0; ci < catalog.categories.length && !found; ci++) {
                    if (catalog.categories[ci].id !== catId) continue;
                    var items = catalog.categories[ci].items || [];
                    for (var ii = 0; ii < items.length && !found; ii++) {
                        var item = items[ii];
                        if (item.id === assetId || item.filename === assetId || item.filename === assetId + '.svg') {
                            obj._capabilities = item.capabilities || { supportsColor: true, supportsRecolor: true, supportsStroke: true, supportsShadow: true };
                            found = true;
                        }
                    }
                }
                if (!obj._capabilities) {
                    obj._capabilities = { supportsColor: true, supportsRecolor: true, supportsStroke: true, supportsShadow: true };
                }
            }
        }

        function undoCanvas() {
            if (historyIndex <= 0 || !canvas) return;
            _thumbnailDirty[currentView] = true;
            _isRestoring = true;
            historyIndex--;
            canvas.loadFromJSON(canvasHistory[historyIndex], () => {
                restoreAssetCapabilities();
                canvas.getObjects().forEach(function(o) { if (!o._isPrintZone && !o.excludeFromExport) enforcePrintAreaBounds(o); });
                canvas.renderAll();
                _isRestoring = false;
            });
        }

        function redoCanvas() {
            if (historyIndex >= canvasHistory.length - 1 || !canvas) return;
            _thumbnailDirty[currentView] = true;
            _isRestoring = true;
            historyIndex++;
            canvas.loadFromJSON(canvasHistory[historyIndex], () => {
                restoreAssetCapabilities();
                canvas.getObjects().forEach(function(o) { if (!o._isPrintZone && !o.excludeFromExport) enforcePrintAreaBounds(o); });
                canvas.renderAll();
                _isRestoring = false;
            });
        }

        function flipView() {
            const thumbs = document.querySelectorAll('.wrap-slick3-dots ul li img');
            if (thumbs.length < 2) return;
            const nextIndex = (currentView + 1) % thumbs.length;
            thumbs[nextIndex].click();
        }

        // ============================================================
        // Image Upload — custom image from device
        // ============================================================
        // -------------------------------------------------------
        // رفع صورة
        // -------------------------------------------------------
        function setupImageUpload() {
            const uploadInput = document.getElementById('uploadImageInput');
            if (!uploadInput) return;

            uploadInput.addEventListener('change', function(e) {
                if (!canvas) return;
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = async function(event) {
                    try {
                        const base64Image = event.target.result;
                        const img = await loadImagePromise(base64Image);

                        if (img && canvas) {
                            const maxWidth = 200;
                            if (img.width > maxWidth) img.scale(maxWidth / img.width);

                            // FIX #4: حفظ المسار في خاصية مخصصة _customSrc
                            const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2,
                                9);
                            let customSrc = base64Image;

                            try {
                                localStorage.setItem(imageId, base64Image);
                                customSrc = 'local://' + imageId;
                                console.log('Image saved to localStorage:', imageId);
                            } catch (e) {
                                console.warn('localStorage full, storing base64 directly');
                            }

                            const center = getPrintZoneCenter();
                            img.set({
                                left: center.left,
                                top: center.top,
                                originX: 'center',
                                originY: 'center',
                                hasControls: true,
                                hasBorders: true
                            });
                            // FIX #4: الخاصية المخصصة بدلاً من img.src الغير موثوقة في Fabric
                            img._customSrc = customSrc;
                            img._debugId = _newDebugId();
                            img._slotKey = getNearestSlotKey(img);
                            _logCreate(img, 'uploadImage');

                            applyCustomControls(img);
                            canvas.add(img);
                            enforcePrintAreaBounds(img);
                            _logAdd(img, 'canvas');
                            canvas.setActiveObject(img);
                            canvas.renderAll();
                            await saveCurrentView();
                        }
                    } catch (err) {
                        console.error('Error loading uploaded image:', err);
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        function getActiveProductImages() {
            if (selectedColor && colorImagesData) {
                const colorKey = selectedColor.toLowerCase().trim();
                if (colorImagesData[colorKey] && colorImagesData[colorKey].length > 0) {
                    return colorImagesData[colorKey];
                }
            }
            return productImages || [];
        }

        // ============================================================
        // Thumbnail System — offscreen Fabric rendering per view
        // ============================================================
        // -------------------------------------------------------
        // تحديث Thumbnails
        // -------------------------------------------------------
        function updateThumbnails(images, switchView = true) {
            const container = document.querySelector('.wrap-slick3-dots ul li');
            if (!container) {
                return;
            }

            container.innerHTML = '';
            images.forEach((img, i) => {
                const imgSrc = fixImagePath(img);
                const el = document.createElement('img');
                el.src = imgSrc;
                el.style.cssText =
                    'width:60px;cursor:pointer;display:block;margin:0 auto 10px;border:2px solid transparent;';
                el.onclick = (function(index, src) {
                    return function() {
                        changeImage(src, index);
                    };
                })(i, imgSrc);
                container.appendChild(el);
            });

            refreshCurrentViewThumbnail();

            if (switchView && images.length > 0) {
                setTimeout(() => changeImage(fixImagePath(images[0]), 0), 100);
            }
        }

        var _thumbRefreshTimer = null;
        var _thumbnailGenId = 0;
        var _thumbnailDirty = {};
        function refreshCurrentViewThumbnail() {
            _thumbnailDirty[currentView] = true;
            if (!canvas) return;
            if (_thumbRefreshTimer) clearTimeout(_thumbRefreshTimer);
            _thumbRefreshTimer = setTimeout(function() {
                var thumbContainer = document.querySelector('.wrap-slick3-dots ul li');
                if (!thumbContainer) return;
                var imgs = thumbContainer.querySelectorAll('img');
                if (imgs.length <= currentView) return;
                if (!canvas.backgroundImage) return;
                try {
                    var dataUrl = canvas.toDataURL({ format: 'png', multiplier: 0.2 });
                    imgs[currentView].src = dataUrl;
                } catch (e) { /* cross-origin or empty canvas */ }
            }, 300);
        }

        function _captureThumbnailForView(viewIndex) {
            var thumbContainer = document.querySelector('.wrap-slick3-dots ul li');
            if (!thumbContainer) return;
            var imgs = thumbContainer.querySelectorAll('img');
            if (imgs.length <= viewIndex) return;
            if (!canvas.backgroundImage) return;
            try {
                var dataUrl = canvas.toDataURL({ format: 'png', multiplier: 0.2 });
                imgs[viewIndex].src = dataUrl;
            } catch (e) { /* cross-origin or empty canvas */ }
        }

        function updateAllViewThumbnails() {
            var myGenId = ++_thumbnailGenId;
            var thumbContainer = document.querySelector('.wrap-slick3-dots ul li');
            if (!thumbContainer) return;
            var imgs = thumbContainer.querySelectorAll('img');
            if (!imgs.length) return;
            var activeImages = getActiveProductImages();
            if (!activeImages) return;

            // Capture current view from live canvas (no offscreen needed)
            if (myGenId === _thumbnailGenId) {
                _captureThumbnailForView(currentView);
            }

            // Gather non-current views that have design data AND are dirty
            var viewIndices = [];
            for (var vi = 0; vi < activeImages.length; vi++) {
                if (vi === currentView) continue;
                if (_thumbnailDirty[vi] === false) continue;
                if (canvasViews[vi] && canvasViews[vi].objects && canvasViews[vi].objects.length > 0) {
                    viewIndices.push(vi);
                }
            }
            if (viewIndices.length === 0) return;

            // Create hidden offscreen canvas for thumbnail rendering
            var tempEl = null;
            try {
                tempEl = document.createElement('canvas');
                tempEl.width = canvas ? canvas.width : 800;
                tempEl.height = canvas ? canvas.height : 800;
                tempEl.style.display = 'none';
                document.body.appendChild(tempEl);
            } catch (e) {
                console.warn('[THUMB] Could not create offscreen canvas element', e);
                return;
            }

            var offscreen = null;
            try {
                offscreen = new fabric.StaticCanvas(tempEl);
            } catch (e) {
                if (tempEl.parentNode) tempEl.parentNode.removeChild(tempEl);
                console.warn('[THUMB] Could not create StaticCanvas', e);
                return;
            }

            // Process views sequentially — each render is async (images, assets, fonts)
            var chain = Promise.resolve();
            viewIndices.forEach(function(vi) {
                chain = chain.then(function() {
                    if (myGenId !== _thumbnailGenId) return;
                    return _renderViewOnCanvas(vi, offscreen, activeImages, imgs);
                });
            });

            chain.then(function() {
                if (tempEl && tempEl.parentNode) {
                    try { offscreen.dispose(); } catch(e) {}
                    try { tempEl.parentNode.removeChild(tempEl); } catch(e) {}
                }
            }).catch(function(err) {
                console.warn('[THUMB] Offscreen rendering error', err);
                if (tempEl && tempEl.parentNode) {
                    try { offscreen.dispose(); } catch(e) {}
                    try { tempEl.parentNode.removeChild(tempEl); } catch(e) {}
                }
            });
        }

        // -------------------------------------------------------
        // Offscreen helpers for thumbnail rendering
        // -------------------------------------------------------
        function _addOffscreenObject(objData, offscreen, asyncPromises) {
            if (objData._isPrintZone || objData.excludeFromExport) return;
            if (objData.type === 'textbox' || objData.type === 'i-text' || objData.type === 'text') {
                _addOffscreenText(objData, offscreen);
            } else if (objData.type === 'image' && objData._customSrc) {
                asyncPromises.push(_createOffscreenImage(objData, offscreen));
            } else if (objData.type === 'asset' && objData._assetMeta && window.ZoomStore && ZoomStore.AssetManager) {
                asyncPromises.push(_createOffscreenAsset(objData, offscreen));
            } else if (objData.type === 'group' && objData.objects) {
                _flattenGroupChildren(objData, offscreen, asyncPromises);
            }
            // Unknown types silently skipped — no crash
        }

        function _flattenGroupChildren(groupData, offscreen, asyncPromises) {
            if (!groupData.objects) return;
            for (var gi = 0; gi < groupData.objects.length; gi++) {
                var child = groupData.objects[gi];
                _addOffscreenObject(child, offscreen, asyncPromises);
            }
        }

        function _renderViewOnCanvas(viewIndex, offscreen, activeImages, imgs) {
            return new Promise(function(resolve) {
                var bgSrc = activeImages[viewIndex] ? fixImagePath(activeImages[viewIndex]) : null;
                if (!bgSrc) { resolve(); return; }
                var viewData = canvasViews[viewIndex];
                if (!viewData || !viewData.objects) { resolve(); return; }

                offscreen.clear();

                // Pre-load fonts before any text rendering
                var fontPromise = _preloadViewFonts(viewData.objects);

                fabric.Image.fromURL(bgSrc, function(bgImg) {
                    if (!bgImg || !offscreen) { resolve(); return; }
                    offscreen.setBackgroundImage(bgImg, function() {
                        var objs = viewData.objects;
                        var asyncPromises = [];
                        // Font loading must complete before toDataURL
                        asyncPromises.push(fontPromise);
                        for (var i = 0; i < objs.length; i++) {
                            _addOffscreenObject(objs[i], offscreen, asyncPromises);
                        }
                        Promise.all(asyncPromises).then(function() {
                            offscreen.renderAll();
                            _captureOffscreenThumbnail(offscreen, imgs, viewIndex);
                            _thumbnailDirty[viewIndex] = false;
                            resolve();
                        }).catch(function() {
                            offscreen.renderAll();
                            _captureOffscreenThumbnail(offscreen, imgs, viewIndex);
                            resolve();
                        });
                    }, {
                        scaleX: offscreen.width / bgImg.width,
                        scaleY: offscreen.height / bgImg.height,
                        crossOrigin: 'anonymous'
                    });
                }, { crossOrigin: 'anonymous' });
            });
        }

        function _preloadViewFonts(objects) {
            if (!window.ZoomStore || !ZoomStore.FontManager) return Promise.resolve();
            var families = {};
            for (var fi = 0; fi < objects.length; fi++) {
                var od = objects[fi];
                if ((od.type === 'i-text' || od.type === 'text' || od.type === 'textbox') && od.fontFamily && !od._isPrintZone && !od.excludeFromExport) {
                    families[od.fontFamily] = true;
                }
                if (od.type === 'group' && od.objects) {
                    _collectGroupFonts(od, families);
                }
            }
            var keys = Object.keys(families);
            if (keys.length === 0) return Promise.resolve();
            var loadPromises = keys.map(function(f) { return ZoomStore.FontManager.loadFont(f); });
            return Promise.race([
                Promise.all(loadPromises),
                new Promise(function(resolve) { setTimeout(resolve, 3000); })
            ]);
        }

        function _collectGroupFonts(groupData, families) {
            if (!groupData.objects) return;
            for (var gi = 0; gi < groupData.objects.length; gi++) {
                var child = groupData.objects[gi];
                if ((child.type === 'i-text' || child.type === 'text' || child.type === 'textbox') && child.fontFamily && !child._isPrintZone && !child.excludeFromExport) {
                    families[child.fontFamily] = true;
                }
                if (child.type === 'group' && child.objects) {
                    _collectGroupFonts(child, families);
                }
            }
        }

        function _addOffscreenText(objData, offscreen) {
            try {
                var textCtor = objData.type === 'textbox' ? fabric.Textbox : fabric.Text;
                var opts = {
                    left: objData.left || 150,
                    top: objData.top || 150,
                    fontSize: objData.fontSize || 20,
                    fill: objData.fill || '#000000',
                    fontFamily: objData.fontFamily || 'Cairo',
                    fontWeight: objData.fontWeight || 'normal',
                    fontStyle: objData.fontStyle || 'normal',
                    angle: objData.angle || 0,
                    textAlign: objData.textAlign || 'center',
                    charSpacing: objData.charSpacing || 0,
                    lineHeight: objData.lineHeight || 1.2,
                    underline: objData.underline || false,
                    overline: objData.overline || false,
                    linethrough: objData.linethrough || false,
                    stroke: objData.stroke || null,
                    strokeWidth: objData.strokeWidth || 0,
                    scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                    scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                    originX: objData.originX || 'left',
                    originY: objData.originY || 'top',
                    width: objData.width || 150,
                    direction: objData.direction || null
                };
                var text = new textCtor(objData.text || '', opts);
                if (objData.shadow) {
                    text.set('shadow', new fabric.Shadow(objData.shadow));
                }
                offscreen.add(text);
            } catch (e) {
                console.warn('[THUMB] Text render error', e);
            }
        }

        function _createOffscreenImage(objData, offscreen) {
            return new Promise(function(resolve) {
                var src = objData._customSrc;
                var loadSrc;
                if (src && src.startsWith('local://')) {
                    var imageId = src.replace('local://', '');
                    loadSrc = localStorage.getItem(imageId);
                    if (!loadSrc) { resolve(); return; }
                } else {
                    loadSrc = src ? fixImagePath(src) : null;
                    if (!loadSrc) { resolve(); return; }
                }
                fabric.Image.fromURL(loadSrc, function(img) {
                    if (img && offscreen) {
                        img.set({
                            left: objData.left || 100,
                            top: objData.top || 100,
                            angle: objData.angle || 0,
                            scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                            scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                            originX: objData.originX || 'left',
                            originY: objData.originY || 'top'
                        });
                        offscreen.add(img);
                    }
                    resolve();
                }, { crossOrigin: 'anonymous' });
            });
        }

        function _createOffscreenAsset(objData, offscreen) {
            if (!window.ZoomStore || !ZoomStore.AssetManager) return Promise.resolve();
            return ZoomStore.AssetManager.addToCanvas(
                objData._assetMeta.adapter + ':' + objData._assetMeta.category,
                objData._assetMeta.assetId,
                offscreen,
                {
                    left: objData.left,
                    top: objData.top,
                    originX: objData.originX || 'left',
                    originY: objData.originY || 'top',
                    color: objData._artColor != null && objData._artColor !== '' ? objData._artColor : '#ffffff',
                    emboss: objData._embossLevel || 0
                }
            ).then(function(assetObj) {
                if (assetObj && offscreen) {
                    assetObj.set({
                        angle: objData.angle || 0,
                        scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                        scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1
                    });
                    if (objData.shadow) {
                        assetObj.set('shadow', new fabric.Shadow(objData.shadow));
                    }
                    offscreen.add(assetObj);
                }
            }).catch(function(err) {
                console.warn('[THUMB] Asset render error', err);
            });
        }

        function _captureOffscreenThumbnail(sourceCanvas, imgs, viewIndex) {
            if (!sourceCanvas || !imgs[viewIndex]) return;
            try {
                var dataUrl = sourceCanvas.toDataURL({ format: 'png', multiplier: 0.15 });
                imgs[viewIndex].src = dataUrl;
            } catch (e) { /* cross-origin or canvas error */ }
        }

        // ============================================================
        // Font Loading — load fonts for current view objects
        // ============================================================
        function loadCurrentViewFonts() {
            if (!canvas || !window.ZoomStore || !ZoomStore.FontManager) return;
            var objects = canvas.getObjects();
            var seen = {};
            var families = [];
            for (var i = 0; i < objects.length; i++) {
                var o = objects[i];
                if ((o.type === 'textbox' || o.type === 'i-text' || o.type === 'text') && o.fontFamily) {
                    if (!seen[o.fontFamily]) {
                        seen[o.fontFamily] = true;
                        families.push(o.fontFamily);
                    }
                }
            }
            console.log('[FONT] loadCurrentViewFonts — found families:', JSON.stringify(families), 'canvas objects:', objects.length);
            // Only fonts that are NOT yet loaded need width preservation
            var skipList = families.filter(function(f) {
                var loaded = ZoomStore.FontManager.isLoaded(f);
                var loading = ZoomStore.FontManager.isLoading(f);
                console.log('[FONT] loadCurrentViewFonts — font:', f, 'isLoaded:', loaded, 'isLoading:', loading);
                return !loaded && !loading;
            });
            console.log('[FONT] loadCurrentViewFonts — skipWidthRecalc set to:', JSON.stringify(skipList));
            ZoomStore.FontManager._skipWidthRecalc = skipList;
            var loadPromises = families.map(function(family) {
                console.log('[FONT] loadCurrentViewFonts — loading font:', family);
                return ZoomStore.FontManager.loadFont(family);
            });
            Promise.all(loadPromises).then(function() {
                console.log('[FONT] loadCurrentViewFonts — all fonts loaded, clearing skipWidthRecalc');
                if (ZoomStore.FontManager) ZoomStore.FontManager._skipWidthRecalc = [];
            });
            if (loadPromises.length === 0 && ZoomStore.FontManager) {
                ZoomStore.FontManager._skipWidthRecalc = [];
            }
        }

        // ============================================================
        // Design Load / Restore — load existing design from cart/order
        // ============================================================
        async function loadExistingDesign() {
            _zlDesignLoadStarted = true;
            try {
            console.log('[FLOW] loadExistingDesign() called');
            console.log('[FLOW] loadExistingDesign — existingDesign:', !!existingDesign, 'existingDesign.designs:', existingDesign ? (existingDesign.designs ? existingDesign.designs.length : 'null') : 'N/A');
            console.log('[FLOW] loadExistingDesign — canvas:', !!canvas);
            console.log('[FLOW] loadExistingDesign — selectedColor:', selectedColor, 'currentView:', currentView, 'hasCanvasViewsKeys:', Object.keys(canvasViews).length);

            if (!existingDesign || !existingDesign.designs || !canvas) {
                console.log('[FLOW] loadExistingDesign — EARLY RETURN. existingDesign:', !!existingDesign, 'designs:', existingDesign ? !!existingDesign.designs : 'N/A', 'canvas:', !!canvas);
                return;
            }

            var debugClone;
            try { debugClone = JSON.parse(JSON.stringify(existingDesign.designs)); } catch(e) { debugClone = '(clone failed)'; }
            console.log('[FLOW] loadExistingDesign — existingDesign.designs raw:', debugClone);

            try {
                canvasViews = {};

                existingDesign.designs.forEach((viewDesign, di) => {
                    const viewIndex = viewDesign.view_index;
                    console.log('[FLOW] loadExistingDesign — processing design[' + di + '] view_index:', viewIndex, 'elements count:', viewDesign.elements ? viewDesign.elements.length : 0);
                    canvasViews[viewIndex] = {
                        objects: [],
                        version: '1.0',
                        print_area_id: viewDesign.print_area_id || null
                    };

                    if (!viewDesign.elements || viewDesign.elements.length === 0) {
                        console.log('[FLOW] loadExistingDesign — view ' + viewIndex + ' has ZERO elements');
                    }

                    viewDesign.elements.forEach(el => {
                        var _objDebugId = _newDebugId();
                        console.log('[LIFECYCLE] loadExistingDesign — creating element type=' + el.type + ' id=' + _objDebugId + ' content=' + (el.content || '').substring(0,20) + ' font=' + (el.font_family || 'N/A'));
                        if (el.type === 'text') {
                            canvasViews[viewIndex].objects.push({
                                _debugId: _objDebugId,
                                type: 'textbox',
                                text: el.content,
                                left: el.position_x,
                                top: el.position_y,
                                fill: el.color,
                                fontFamily: el.font_family,
                                fontWeight: (el.font_weight >= 700) ? 'bold' : 'normal',
                                fontStyle: el.font_style || 'normal',
                                textAlign: el.text_align || 'left',
                                charSpacing: el.char_spacing || 0,
                                lineHeight: el.line_height || 1.2,
                                underline: el.underline || false,
                                overline: el.overline || false,
                                linethrough: el.linethrough || false,
                                stroke: el.stroke || null,
                                strokeWidth: el.stroke_width || 0,
                                shadow: el.shadow || null,
                                direction: el.direction || null,
                                angle: el.rotation,
                                fontSize: el.font_size || 20,
                                width: el.width || 150,
                                scaleX: el.scale_x || 1,
                                scaleY: el.scale_y || 1,
                                originX: el.origin_x || 'left',
                                originY: el.origin_y || 'top'
                            });
                        } else if (el.type === 'asset') {
                            canvasViews[viewIndex].objects.push({
                                _debugId: _objDebugId,
                                type: 'asset',
                                _assetMeta: el._assetMeta || null,
                                _artKey: el.content,
                                _customSrc: el.content,
                                _artColor: el.color || '#ffffff',
                                _embossLevel: el.height || 0,
                                left: el.position_x,
                                top: el.position_y,
                                angle: el.rotation,
                                scaleX: el.scale_x || 1,
                                scaleY: el.scale_y || 1,
                                originX: el.origin_x || 'left',
                                originY: el.origin_y || 'top',
                                stroke: el.stroke || null,
                                fill: el.fill || null,
                                shadow: el.shadow || null
                            });
                        } else if (el.type === 'badge') {
                            // Legacy migration to asset type
                            var assetMeta = {
                                version: 1,
                                adapter: 'svg',
                                category: 'general',
                                assetId: el.content
                            };
                            canvasViews[viewIndex].objects.push({
                                _debugId: _objDebugId,
                                type: 'asset',
                                _assetMeta: assetMeta,
                                _artKey: el.content,
                                _customSrc: 'art://' + el.content,
                                _artColor: el.color || '#ffffff',
                                _embossLevel: el.height || 0,
                                left: el.position_x,
                                top: el.position_y,
                                angle: el.rotation,
                                scaleX: el.scale_x || (el.width ? el.width / 80 : 1),
                                scaleY: el.scale_y || (el.width ? el.width / 80 : 1),
                                originX: el.origin_x || 'left',
                                originY: el.origin_y || 'top'
                            });
                        } else if (el.type === 'image') {
                            const imagePath = fixImagePath(el.content);
                            const imageObj = {
                                _debugId: _objDebugId,
                                type: 'image',
                                _customSrc: imagePath,
                                left: el.position_x,
                                top: el.position_y,
                                angle: el.rotation,
                                scaleX: el.scale_x || 1,
                                scaleY: el.scale_y || 1,
                                width: el.original_width || el.width || null,
                                height: el.original_height || el.height || null,
                                originX: el.origin_x || 'left',
                                originY: el.origin_y || 'top'
                            };
                            canvasViews[viewIndex].objects.push(imageObj);
                        }
                    });
                });

                _logCanvasViews('AFTER loadExistingDesign populate');
                console.log('[FLOW] loadExistingDesign — canvasViews after population:', Object.keys(canvasViews).map(k => k + ':' + (canvasViews[k] ? canvasViews[k].objects.length + ' objects' : 'null')));

                const initialView = canvasViews[0] !== undefined ? 0 : currentView;
                const activeImages = getActiveProductImages();

                console.log('[FLOW] loadExistingDesign — initialView:', initialView, 'currentView:', currentView, 'canvasViews[initialView]:', canvasViews[initialView] !== undefined ? (canvasViews[initialView].objects.length + ' objects') : 'undefined');
                console.log('[FLOW] loadExistingDesign — activeImages:', activeImages ? activeImages.length + ' items' : 'empty/null');

                if (canvasViews[initialView] !== undefined && activeImages.length > 0) {
                    const imgSrc = fixImagePath(activeImages[initialView] || activeImages[0]);
                    console.log('[FLOW] loadExistingDesign — calling changeImage with imgSrc:', imgSrc, 'viewIndex:', initialView);
                    await changeImage(imgSrc, initialView, true);
                    console.log('[FLOW] loadExistingDesign — AFTER changeImage returned. currentView:', currentView, 'canvasViews[' + initialView + ']:', canvasViews[initialView] ? (canvasViews[initialView].objects ? canvasViews[initialView].objects.length + ' objects' : 'null') : 'undefined');
                    _logCanvasViews('AFTER changeImage inside loadExistingDesign');

                    // Render all non-active view thumbnails (loading overlay hides canvas)
                    _thumbnailDirty = {};
                    updateAllViewThumbnails();

                    // Reset history to fully-loaded state: undo cannot go past initial canvas
                    await new Promise(function(resume) { setTimeout(resume, 50); });
                    var initialJson = JSON.stringify(canvas.toJSON(['_customSrc', '_isArt', '_artKey', '_artColor', '_embossLevel', '_assetMeta', '_slotKey', '_zoomObjectId']));
                    canvasHistory = [initialJson];
                    historyIndex = 0;
                } else {
                    console.log('[FLOW] loadExistingDesign — SKIP changeImage. canvasViews[initialView]:', canvasViews[initialView] !== undefined ? 'defined' : 'undefined', 'activeImages.length:', activeImages ? activeImages.length : 0);
                }
                // loadCurrentViewFonts is already called at the end of changeImage()
            } catch (error) {
                console.error('[FLOW] Error loading existing design:', error);
            }
            } catch (e) { console.error('[FLOW] FATAL error in loadExistingDesign wrapper:', e); }
            finally {
            _zlDesignLoadDone = true;
            _zlTryComplete();
            }
        }

        // -------------------------------------------------------
        // FIX #3 + #4: حفظ الـ view الحالي بشكل صحيح كامل
        // -------------------------------------------------------
        async function saveCurrentView() {
            console.log('[FLOW_SAVE] saveCurrentView() — currentView:', currentView);
            console.log('[FLOW_SAVE] BEFORE — canvasViews[' + currentView + ']:', canvasViews[currentView] ? (canvasViews[currentView].objects ? canvasViews[currentView].objects.length + ' objects' : 'objects null') : 'undefined');
            console.log('[FLOW_SAVE] BEFORE — canvasViews keys:', Object.keys(canvasViews).map(k => k + ':' + (canvasViews[k] ? (canvasViews[k].objects ? canvasViews[k].objects.length + ' objects' : 'null') : 'undefined')).join(', '));
            if (!canvas) {
                console.log('[FLOW_SAVE] EARLY RETURN — canvas is null');
                return;
            }
            try {
                const objects = canvas.getObjects();
                const currentObjects = objects.filter(obj => obj !== canvas.backgroundImage);
                console.log('[FLOW_SAVE] Canvas objects total:', objects.length, 'filtered (non-background):', currentObjects.length);
                console.log('[FLOW_SAVE] Object types on canvas:', currentObjects.map(function(o) { return o.type + (o._assetMeta ? '(asset)' : '') + (o.fontFamily ? '/' + o.fontFamily : ''); }));
                const savedObjects = [];
                _logCanvasViews('BEFORE saveCurrentView');

                for (const obj of currentObjects) {
                    if (obj._isPrintZone || obj.excludeFromExport) { continue; }
                    console.log('[FLOW_SAVE] DESIGN OBJECT', obj.type, obj._assetMeta, obj._isArt, obj._zoomObjectId);
                    var _did = obj._debugId || _newDebugId();
                    if (!obj._debugId) obj._debugId = _did;
                    _logSerialize(obj, 'saveCurrentView');
                    if (!obj._isPrintZone) assignSlotKeyToObject(obj);
                    try {
                        if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                            savedObjects.push({
                                _debugId: _did,
                                _zoomObjectId: obj._zoomObjectId || null,
                                _slotKey: obj._slotKey || null,
                                type: obj.type,
                                text: obj.text,
                                left: obj.left,
                                top: obj.top,
                                fontSize: obj.fontSize,
                                fill: obj.fill,
                                fontFamily: obj.fontFamily,
                                fontWeight: obj.fontWeight,
                                fontStyle: obj.fontStyle,
                                textAlign: obj.textAlign,
                                charSpacing: obj.charSpacing,
                                lineHeight: obj.lineHeight,
                                underline: obj.underline,
                                overline: obj.overline,
                                linethrough: obj.linethrough,
                                stroke: obj.stroke,
                                strokeWidth: obj.strokeWidth,
                                shadow: obj.shadow ? JSON.parse(JSON.stringify(obj.shadow)) : null,
                                direction: obj.direction,
                                angle: obj.angle,
                                width: obj.width,
                                scaleX: obj.scaleX,
                                scaleY: obj.scaleY,
                                originX: obj.originX || 'left',
                                originY: obj.originY || 'top',
                                hasControls: true,
                                hasBorders: true
                            });
                        } else if (obj._assetMeta) {
                            savedObjects.push({
                                _debugId: _did,
                                _zoomObjectId: obj._zoomObjectId || null,
                                _slotKey: obj._slotKey || null,
                                type: 'asset',
                                _assetMeta: {
                                    version: 1,
                                    adapter: obj._assetMeta.adapter,
                                    category: obj._assetMeta.category,
                                    assetId: obj._assetMeta.assetId
                                },
                                left: obj.left,
                                top: obj.top,
                                angle: obj.angle,
                                scaleX: obj.scaleX,
                                scaleY: obj.scaleY,
                                originX: obj.originX || 'left',
                                originY: obj.originY || 'top',
                                stroke: obj.stroke,
                                fill: obj.fill,
                                _artColor: obj._artColor,
                                _embossLevel: obj._embossLevel || 0,
                                hasControls: true,
                                hasBorders: true,
                                shadow: obj.shadow ? JSON.parse(JSON.stringify(obj.shadow)) : null
                            });
                        } else if (obj._isArt) {
                            // Legacy migration to _assetMeta
                            var adapter = "svg";
                            var catId = "general";
                            var assetId = obj._artKey || obj._customSrc || "";
                            if (obj._customSrc && obj._customSrc.indexOf("file://") === 0) {
                                var parts = obj._customSrc.replace("file://", "").split("/");
                                catId = parts[0] || "general";
                            }
                            savedObjects.push({
                                _debugId: _did,
                                _zoomObjectId: obj._zoomObjectId || null,
                                type: 'asset',
                                _assetMeta: {
                                    version: 1,
                                    adapter: adapter,
                                    category: catId,
                                    assetId: assetId
                                },
                                _slotKey: obj._slotKey || null,
                                left: obj.left,
                                top: obj.top,
                                angle: obj.angle,
                                scaleX: obj.scaleX,
                                scaleY: obj.scaleY,
                                originX: obj.originX || 'left',
                                originY: obj.originY || 'top',
                                stroke: obj.stroke,
                                fill: obj.fill,
                                _artColor: obj._artColor,
                                _embossLevel: obj._embossLevel || 0,
                                hasControls: true,
                                hasBorders: true,
                                shadow: obj.shadow ? JSON.parse(JSON.stringify(obj.shadow)) : null
                            });
                        } else if (obj.type === 'image') {
                            // FIX #4: استخدم _customSrc المخصصة بدل .src المدمجة
                            let customSrc = obj._customSrc;

                            // fallback: لو مفيش _customSrc جرب getSrc()
                            if (!customSrc) {
                                const fabricSrc = obj.getSrc ? obj.getSrc() : null;
                                if (fabricSrc && fabricSrc.startsWith('data:image')) {
                                    const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                                    try {
                                        localStorage.setItem(imageId, fabricSrc);
                                        customSrc = 'local://' + imageId;
                                    } catch (e) {
                                        customSrc = fabricSrc;
                                    }
                                } else {
                                    customSrc = fabricSrc;
                                }
                                obj._customSrc = customSrc;
                            }

                            savedObjects.push({
                                _debugId: _did,
                                _zoomObjectId: obj._zoomObjectId || null,
                                _slotKey: obj._slotKey || null,
                                type: obj.type,
                                _customSrc: customSrc,
                                left: obj.left,
                                top: obj.top,
                                angle: obj.angle,
                                // FIX #3: حفظ القيم الحقيقية
                                scaleX: obj.scaleX,
                                scaleY: obj.scaleY,
                                width: obj.width,
                                height: obj.height,
                                originX: obj.originX || 'left',
                                originY: obj.originY || 'top',
                                hasControls: true,
                                hasBorders: true
                            });
                        } else {
                            console.warn('[FLOW_SAVE] DROPPED object — type:', obj.type, '_isArt:', obj._isArt, '_assetMeta:', obj._assetMeta, '_customSrc:', obj._customSrc, 'keys:', Object.keys(obj).filter(k => k.startsWith('_')));
                        }
                    } catch (err) {
                        console.warn('Error saving object:', err);
                    }
                }

                const bestArea = findNearestAreaForObjects(currentObjects);
                canvasViews[currentView] = {
                    objects: savedObjects,
                    version: '1.0',
                    timestamp: Date.now(),
                    print_area_id: bestArea ? bestArea.id : null
                };

                console.log('[FLOW_SAVE] AFTER — canvasViews[' + currentView + '] stored with ' + savedObjects.length + ' objects');
                console.log('[FLOW_SAVE] Saved object types:', savedObjects.map(function(o) { return o.type + (o.fontFamily ? '/' + o.fontFamily : '') + ' id=' + o._debugId; }));
                _logCanvasViews('AFTER saveCurrentView');
            } catch (error) {
                console.error('[FLOW_SAVE] Error saving view:', error);
            }
        }

        // ============================================================
        // Pending Design — guest → login handoff
        // ============================================================
        // -------------------------------------------------------
        // Guest → Login: save & restore pending design
        // -------------------------------------------------------
        function restoreSizeFromPending(pending) {
            if (!pending.selectedSize) { navigateTo('details'); return; }
            const sizeBtn = document.querySelector(`.size-btn[data-size="${pending.selectedSize}"]`);
            if (!sizeBtn) { navigateTo('details'); return; }
            selectSize(sizeBtn);
            if (pending.selectedColor && pending.selectedVariantId) {
                setTimeout(() => {
                    const colorBtn = document.querySelector(`.color-btn[data-color="${pending.selectedColor}"]`);
                    if (colorBtn) {
                        selectColor(colorBtn);
                    } else if (pending.selectedVariantId) {
                        document.getElementById('variant_id').value = pending.selectedVariantId;
                        hideCanvasOverlay();
                    }
                }, 200);
            } else if (pending.selectedVariantId) {
                document.getElementById('variant_id').value = pending.selectedVariantId;
                hideCanvasOverlay();
            }
        }

        function restorePendingCanvas(pending) {
            console.log('[CHECKPOINT-8] restorePendingCanvas() — views:', Object.keys(pending.canvasViews || {}).length, 'currentView:', pending.currentView);
            if (!pending.canvasViews || pending.currentView === undefined) {
                console.log('[CHECKPOINT-8] restorePendingCanvas — SKIP (no canvasViews or currentView)');
                return;
            }
            canvasViews = pending.canvasViews;
            currentView = pending.currentView;
            const activeImages = getActiveProductImages();
            if (canvasViews[currentView] !== undefined && activeImages.length > 0) {
                changeImage(fixImagePath(activeImages[currentView] || activeImages[0]), currentView, true);
            }
        }

        // ============================================================
        // Pending Design Restoration — async state machine
        // Called after login when pendingDesign exists in sessionStorage.
        // Phases: product switch → variant/size/color → canvas → UI → checkout
        // ============================================================
        async function _restoreFromPending(pending) {
            console.log('[RESTORE] _restoreFromPending start — productId:', pending.productId, 'currentProductId:', currentProductId);

            // Phase 2: Switch product if the pending design is for a different product
            // switchProductInPlace returns true on success (including same-product no-op), false on failure.
            // The boolean is the canonical completion signal — all 19 steps including variant/color/image/area/placement
            // initialization must finish before true is returned.
            if (pending.productId !== currentProductId) {
                console.log('[RESTORE] Phase 2 — switching to product:', pending.productId);
                var switched = await switchProductInPlace(pending.productId);
                if (switched !== true) {
                    console.error('[RESTORE] Phase 2 — product switch FAILED');
                    showToast('فشل استعادة المنتج، يرجى المحاولة مرة أخرى', 'error');
                    // Keep pendingDesign in sessionStorage — user can retry on next page load
                    // Do NOT silently discard the user's work
                    return;
                }
                console.log('[RESTORE] Phase 2 — product switch complete, currentProductId:', currentProductId);
            }

            // Phase 3: Restore variant, size, color (in-order, await-based)
            console.log('[RESTORE] Phase 3 — restoring variant/size/color');
            document.getElementById('variant_id').value = pending.selectedVariantId || '';
            if (pending.selectedSize) {
                const sizeBtn = document.querySelector('.size-btn[data-size="' + pending.selectedSize + '"]');
                if (sizeBtn) selectSize(sizeBtn);
            }
            if (pending.selectedColor) {
                const colorBtn = document.querySelector('.color-btn[data-color="' + pending.selectedColor + '"]');
                if (colorBtn) await selectColor(colorBtn);
            }

            // Phase 4: Restore canvas views and current view
            console.log('[RESTORE] Phase 4 — restoring canvas views');
            canvasViews = pending.canvasViews;
            currentView = pending.currentView;
            const activeImages = getActiveProductImages();
            if (canvasViews[currentView] !== undefined && activeImages.length > 0) {
                await changeImage(fixImagePath(activeImages[currentView] || activeImages[0]), currentView, true);
            }

            // Phase 5: Refresh PlacementGuide, UI, history
            console.log('[RESTORE] Phase 5 — refreshing UI');
            if (typeof PlacementGuide !== 'undefined') {
                PlacementGuide.refresh();
            }
            pushHistory();
            _zlTryComplete();

            // Restoration end state — NO automatic checkout.
            // The customer stays on the Designer page exactly as they left it.
            // No navigation, no redirect, no form submission, no cart action.
            // The customer manually clicks "Add to Cart" when ready.
            sessionStorage.removeItem('pendingDesign');
            console.log('[RESTORE] _restoreFromPending complete — design restored, staying on designer page. No auto checkout.');
        }

        // ============================================================
        // Authenticated submission — reusable by both Buy button and restoration
        // ============================================================
        async function _submitDesignAndCheckout() {
            if (_checkoutGuard) { console.log('[CHECKOUT] Guard blocked duplicate execution'); return; }
            _checkoutGuard = true;
            try {
                const variantId = document.getElementById('variant_id').value;

            const designsPayload = [];

            for (const viewIndex in canvasViews) {
                const view = canvasViews[viewIndex];
                if (!view || !view.objects || view.objects.length === 0) continue;

                const elements = await Promise.all(view.objects.map(async (obj) => {
                    const elemDebugId = obj._debugId || _newDebugId();
                    if (obj.type === 'image') {
                        let content = resolveImageContent(obj._customSrc) || obj._customSrc;
                        if (content && content.startsWith('local://')) {
                            content = null;
                        }
                        return {
                            _debugId: elemDebugId,
                            type: 'image',
                            content: content,
                            position_x: obj.left ?? 0,
                            position_y: obj.top ?? 0,
                            width: obj.width ? Math.round(obj.width * (obj.scaleX || 1)) : null,
                            height: obj.height ? Math.round(obj.height * (obj.scaleY || 1)) : null,
                            rotation: Math.round(obj.angle || 0),
                            scale_x: obj.scaleX || 1,
                            scale_y: obj.scaleY || 1,
                            original_width: obj.width || null,
                            original_height: obj.height || null,
                            origin_x: obj.originX || 'left',
                            origin_y: obj.originY || 'top',
                            z_index: obj.zIndex || 0
                        };
                    }
                    if (obj.type === 'asset' || obj.type === 'badge') {
                        return {
                            _debugId: elemDebugId,
                            type: 'asset',
                            _assetMeta: obj._assetMeta || null,
                            content: obj._artKey || obj._customSrc || (obj._assetMeta ? obj._assetMeta.assetId : ''),
                            position_x: obj.left ?? 0,
                            position_y: obj.top ?? 0,
                            rotation: Math.round(obj.angle || 0),
                            color: obj._artColor || null,
                            width: Math.round((obj.scaleX || 1) * 80),
                            height: obj._embossLevel || 0,
                            scale_x: obj.scaleX || 1,
                            scale_y: obj.scaleY || 1,
                            origin_x: obj.originX || 'left',
                            origin_y: obj.originY || 'top',
                            stroke: obj.stroke || null,
                            fill: obj.fill || null,
                            shadow: obj.shadow || null,
                            z_index: obj.zIndex || 0
                        };
                    }
                    return {
                        _debugId: elemDebugId,
                        type: 'text',
                        content: obj.text || null,
                        position_x: obj.left ?? 0,
                        position_y: obj.top ?? 0,
                        rotation: Math.round(obj.angle || 0),
                        color: obj.fill || null,
                        font_family: obj.fontFamily || null,
                        font_size: obj.fontSize || null,
                        font_weight: (obj.fontWeight === 'bold' || obj.fontWeight >= 700) ? 700 : 400,
                        font_style: obj.fontStyle || null,
                        text_align: obj.textAlign || null,
                        char_spacing: obj.charSpacing || 0,
                        line_height: obj.lineHeight || 1.2,
                        underline: obj.underline || false,
                        overline: obj.overline || false,
                        linethrough: obj.linethrough || false,
                        stroke: obj.stroke || null,
                        stroke_width: obj.strokeWidth || 0,
                        shadow: obj.shadow || null,
                        direction: obj.direction || null,
                        origin_x: obj.originX || 'left',
                        origin_y: obj.originY || 'top',
                        width: obj.width || null,
                        scale_x: obj.scaleX || 1,
                        scale_y: obj.scaleY || 1,
                        z_index: obj.zIndex || 0
                    };
                }));

                designsPayload.push({
                    view_index: parseInt(viewIndex),
                    print_area_id: view.print_area_id || null,
                    elements
                });
            }

            (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) zl.setProgress(50); })();

            const previewImage = canvas.toDataURL({
                format: 'png',
                quality: 0.8
            });
            const existingDesignId = document.getElementById('design_id').value;

            _saveCount++;
            _logCanvasViews('BEFORE SUBMIT (save #' + _saveCount + ')');
            console.log('[LIFECYCLE] SUBMIT save#' + _saveCount + ' — designsPayload views:',
                designsPayload.map(function(v) { return 'view_' + v.view_index + ': ' + v.elements.length + ' elements [' + v.elements.map(function(e) { return e._debugId + '/' + e.type + (e.font_family ? '/' + e.font_family : '') + (e.content ? '/' + (e.content + '').substring(0, 15) : ''); }).join(',') + ']'; }).join(' | ')
            );
            console.log('[LIFECYCLE] SUBMIT save#' + _saveCount + ' — ALL debugIds in payload per view:',
                JSON.stringify(designsPayload.map(function(v) { return 'view' + v.view_index + ':' + v.elements.map(function(e) { return e._debugId; }).join(','); }))
            );
            console.log('[LIFECYCLE] SUBMIT save#' + _saveCount + ' — FULL payload (no preview):',
                JSON.stringify({
                    product_id: currentProductId,
                    variant_id: variantId,
                    view: currentView.toString(),
                    design_id: document.getElementById('design_id').value || null,
                    designs: designsPayload.map(function(v) { return { view_index: v.view_index, print_area_id: v.print_area_id, elements: v.elements.map(function(e) { return { _debugId: e._debugId, type: e.type, content: e.content ? (e.content + '').substring(0, 30) : null, font_family: e.font_family, text: e.text ? (e.text + '').substring(0, 20) : null, position_x: e.position_x, position_y: e.position_y }; }) }; })
                })
            );

            const payload = {
                product_id: currentProductId,
                variant_id: variantId,
                view: currentView.toString(),
                designs: designsPayload,
                preview_image: previewImage
            };

            if (existingDesignId) payload.design_id = existingDesignId;

            const adminMode = document.getElementById('admin_mode').value;
            if (adminMode) payload.admin_mode = true;

            const resubmitMode = document.getElementById('resubmit').value;
            if (resubmitMode) payload.resubmit = true;

            (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) zl.setProgress(75); zl.setMessage('\u062C\u0627\u0631\u064A \u062D\u0641\u0638 \u0627\u0644\u062A\u0635\u0645\u064A\u0645...'); })();

            const response = await fetch("{{ route('design.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.error || '\u062D\u0635\u0644 \u062E\u0637\u0623 \u0641\u064A \u062D\u0641\u0638 \u0627\u0644\u062A\u0635\u0645\u064A\u0645');
                return;
            }

            const designIdInput = document.getElementById('design_id');
            if (designIdInput) designIdInput.value = data.design_id;

            (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) { zl.setProgress(100); zl.setMessage('\u062A\u0645 \u0627\u0644\u062D\u0641\u0638 \u0628\u0646\u062C\u0627\u062D'); setTimeout(function() { zl.hide(); }, 200); } })();

            const adminModeAfter = document.getElementById('admin_mode').value;
            if (adminModeAfter) {
                const orderId = document.getElementById('admin_return_order').value;
                const detailId = document.getElementById('admin_return_detail').value;
                setTimeout(function() { window.location.href = '{{ url("admin/orders") }}/' + orderId + '/design/' + detailId; }, 300);
                return;
            }

            const resubmitAfter = document.getElementById('resubmit').value;
            if (resubmitAfter && data.order_id) {
                setTimeout(function() { window.location.href = '{{ url("order/confirmation") }}/' + data.order_id; }, 300);
                return;
            }

            (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) { zl.setProgress(100); zl.setMessage('\u062A\u0645 \u0627\u0644\u062D\u0641\u0638 \u0628\u0646\u062C\u0627\u062D\u061F'); setTimeout(function() { zl.hide(); }, 300); } })();
            document.getElementById('addToCartForm').submit();
            } finally {
                _checkoutGuard = false;
            }
        }

        // ============================================================
        // Submit / Save Design — sends to server
        // ============================================================
        // -------------------------------------------------------
        // Submit — إرسال التصميم للسيرفر
        // -------------------------------------------------------


        async function handleSubmit() {
            console.log('[CHECKPOINT-1] handleSubmit() entered — isAuthenticated:', !!isAuthenticated, 'variant_id:', document.getElementById('variant_id').value);
            const variantId = document.getElementById('variant_id').value;
            if (!variantId) {
                alert('اختار المقاس واللون أولاً ❗');
                navigateTo('details');
                return;
            }

            if (!canvas) {
                alert('خطأ في تحميل التصميم ❗');
                return;
            }

            (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) { zl.show({ title: 'Zoom Store', subtitle: productName || '', message: '\u062C\u0627\u0631\u064A \u062A\u062C\u0647\u064A\u0632 \u0627\u0644\u062A\u0635\u0645\u064A\u0645...', allowClose: false }); } })();

            try {
                (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) zl.setProgress(25); })();
                await saveCurrentView();

                if (!isAuthenticated) {
                    const designState = {
                        productId: currentProductId,
                        canvasViews: canvasViews,
                        currentView: currentView,
                        selectedSize: selectedSize,
                        selectedColor: selectedColor,
                        selectedVariantId: document.getElementById('variant_id').value,
                        timestamp: Date.now()
                    };
                    sessionStorage.setItem('pendingDesign', JSON.stringify(designState));
                    console.log('[CHECKPOINT-2] pendingDesign saved to sessionStorage — productId:', designState.productId, 'views:', Object.keys(designState.canvasViews).length, 'currentView:', designState.currentView, 'selectedSize:', designState.selectedSize, 'selectedColor:', designState.selectedColor);
                    const loginUrl = '{{ route("login") }}?redirect_to=' + encodeURIComponent(window.location.href);
                    console.log('[CHECKPOINT-3] redirect_to generated — loginUrl:', loginUrl);
                    window.location.href = loginUrl;
                    return;
                }

                await _submitDesignAndCheckout();
            } catch (err) {
                console.error('Submit error:', err);
                (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) zl.hide(); })();
                alert('حصل خطأ، حاول تاني');
            }
        }

        // ============================================================
        // FIX #1 + #2: إدارة المقاسات والألوان مع sessionStorage
        // ============================================================
        let selectedSize = null;
        let selectedColor = null;
        window._selectedColorKey = null;

        @php
            $variantsData = [];
            foreach ($product->variants as $variant) {
                if ($variant->quantity > 0) {
                    $size = $variant->size;
                    $color = $variant->color;
                    if (!isset($variantsData[$size])) {
                        $variantsData[$size] = [];
                    }
                    $variantsData[$size][$color] = [
                        'id' => $variant->id,
                        'quantity' => $variant->quantity,
                        'weight' => $variant->weight,
                        'material' => $variant->material,
                        'color_code' => $variant->color_code ?? null,
                    ];
                }
            }
        @endphp

        let variantsData = @json($variantsData);
        let colorImagesData = @json($colorImages);

        console.log('Variants Data:', variantsData);

        function getColorCodeFromName(colorName) {
            const colorMap = {
                'أحمر': '#ff0000',
                'احمر': '#ff0000',
                'red': '#ff0000',
                'أزرق': '#0000ff',
                'ازرق': '#0000ff',
                'blue': '#0000ff',
                'أخضر': '#00ff00',
                'اخضر': '#00ff00',
                'green': '#00ff00',
                'أصفر': '#ffff00',
                'اصفر': '#ffff00',
                'yellow': '#ffff00',
                'أسود': '#000000',
                'اسود': '#000000',
                'black': '#000000',
                'أبيض': '#ffffff',
                'ابيض': '#ffffff',
                'white': '#ffffff',
                'رمادي': '#808080',
                'gray': '#808080',
                'grey': '#808080',
                'بني': '#8b4513',
                'brown': '#8b4513',
                'بنفسجي': '#800080',
                'purple': '#800080',
                'برتقالي': '#ffa500',
                'orange': '#ffa500'
            };
            return colorMap[colorName.toLowerCase().trim()] || '#cccccc';
        }

        function displayColorsForSize(size) {
            const colorsContainer = document.getElementById('colorsContainer');
            if (!colorsContainer) return;

            if (!variantsData[size] || Object.keys(variantsData[size]).length === 0) {
                colorsContainer.innerHTML = '<p class="text-muted">لا توجد ألوان متاحة لهذا المقاس</p>';
                return;
            }

            const colors = Object.keys(variantsData[size]);
            let html = '';

            colors.forEach(color => {
                const colorData = variantsData[size][color];
                const colorCode = colorData.color_code || getColorCodeFromName(color);

                html += `
            <button type="button"
                    class="color-btn"
                    data-color="${color}"
                    data-variant-id="${colorData.id}"
                    data-quantity="${colorData.quantity}"
                    data-weight="${colorData.weight || '--'}"
                    data-material="${colorData.material || '--'}"
                    style="
                        width:27px; height:27px;
                        border-radius:50%;
                        background:${colorCode};
                        border:2px solid #ddd;
                        cursor:pointer;
                        transition:all 0.2s;
                        position:relative;
                        box-shadow:0 2px 4px rgba(0,0,0,0.1);
                        margin-left: 3px;
                    "
                    title="${color}">
                <span style="
                    position:absolute; bottom:-22px; left:50%;
                    transform:translateX(-50%);
                    font-size:10px; white-space:nowrap;
                    display:none;
                    background:rgba(0,0,0,0.7); color:white;
                    padding:2px 6px; border-radius:4px; z-index:100;
                " class="color-label">${color}</span>
            </button>
        `;
            });

            colorsContainer.innerHTML = html;

            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectColor(this);
                });
                btn.addEventListener('mouseenter', function() {
                    const label = this.querySelector('.color-label');
                    if (label) label.style.display = 'block';
                });
                btn.addEventListener('mouseleave', function() {
                    const label = this.querySelector('.color-label');
                    if (label) label.style.display = 'none';
                });
            });
        }

        // FIX #1: selectColor يحفظ في sessionStorage
        async function selectColor(button) {
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.style.border = '2px solid #ddd';
                btn.style.transform = 'scale(1)';
            });

            button.style.border = '2px solid #ff6e26';
            button.style.transform = 'scale(1.1)';

            selectedColor = button.dataset.color;
            window._selectedColorKey = selectedColor.toLowerCase().trim();

            const variantId = button.dataset.variantId;
            const quantity = button.dataset.quantity;
            const weight = button.dataset.weight;
            const material = button.dataset.material;

            // FIX #1: حفظ في sessionStorage
            sessionStorage.setItem('selectedColor', selectedColor);
            sessionStorage.setItem('selectedVariantId', variantId);

            document.getElementById('variant_id').value = variantId;

            const availableQtySpan = document.getElementById('availableQty');
            const weightSpan = document.getElementById('weight');
            const materialSpan = document.getElementById('material');

            if (availableQtySpan) availableQtySpan.textContent = quantity;
            if (weightSpan) weightSpan.textContent = weight;
            if (materialSpan) materialSpan.textContent = material;

            // تحديث صور المنتج للون المختار
            const colorKey = selectedColor.toLowerCase().trim();
            if (colorImagesData && colorImagesData[colorKey]) {
                updateThumbnails(colorImagesData[colorKey], false);
                if (typeof loadProductImage === 'function' && colorImagesData[colorKey].length > 0) {
                    await loadProductImage(fixImagePath(colorImagesData[colorKey][0]));
                }
            } else if (productImages && productImages.length > 0) {
                updateThumbnails(productImages, false);
                if (typeof loadProductImage === 'function') {
                    await loadProductImage(fixImagePath(productImages[0]));
                }
            }

            // FIX #2: إخفاء الـ overlay بعد اختيار المقاس واللون
            hideCanvasOverlay();

            if (typeof updateActionBar === 'function') updateActionBar();
        }

        // FIX #1: selectSize يحفظ في sessionStorage
        function selectSize(button) {
            document.querySelectorAll('.size-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.style.background = 'white';
                btn.style.color = '#333';
                btn.style.border = '1px solid #ddd';
            });

            button.classList.add('active');
            button.style.background = '#ff6e26';
            button.style.color = 'white';
            button.style.border = '1px solid #ff6e26';

            selectedSize = button.dataset.size;
            selectedColor = null;

            // FIX #1: حفظ المقاس وإزالة اللون القديم
            sessionStorage.setItem('selectedSize', selectedSize);
            sessionStorage.removeItem('selectedColor');
            sessionStorage.removeItem('selectedVariantId');

            displayColorsForSize(selectedSize);
        }

        function initSizesAndColors() {
            document.querySelectorAll('.size-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectSize(this);
                });
            });
        }

        // ============================================================
        // Canvas Overlay — blocks interaction before size/color selected
        // ============================================================
        // -------------------------------------------------------
        // FIX #2: Overlay functions
        // -------------------------------------------------------
        function hideCanvasOverlay() {
            const overlay = document.getElementById('canvasOverlay');
            if (overlay) {
                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 300);
            }
        }

        function showCanvasOverlay() {
            const overlay = document.getElementById('canvasOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
            }
        }

        // ============================================================
        // Navigation
        // ============================================================
        let navigationHistory = ['home'];

        function navigateTo(sectionId, addToHistory = true) {
            document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
            const section = document.getElementById('sec-' + sectionId);
            if (section) section.classList.add('active');

            document.querySelectorAll('.nav-item-n').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.getElementById('btn-' + sectionId);
            if (activeBtn) activeBtn.classList.add('active');

            if (addToHistory && navigationHistory[navigationHistory.length - 1] !== sectionId) {
                navigationHistory.push(sectionId);
            }

            updateUI(sectionId);

            const panel = document.querySelector('.panel-v');
            const backdrop = document.getElementById('mobilePanelBackdrop');
            if (window.innerWidth <= 991) {
                if (sectionId === 'home') {
                    panel.classList.remove('mobile-open');
                    if (backdrop) backdrop.classList.remove('active');
                } else {
                    panel.classList.add('mobile-open');
                    if (backdrop) backdrop.classList.add('active');
                }
            }

            if (sectionId === 'art') {
                renderArtCategories();
            }
        }

        function closeMobilePanel() {
            resetToHome();
        }

        function goBack() {
            const artItemsVisible = document.getElementById('art-items-view') &&
                document.getElementById('art-items-view').style.display !== 'none';
            const activeSection = navigationHistory[navigationHistory.length - 1];

            if (activeSection === 'art' && artItemsVisible) {
                backToArtCategories();
                return;
            }

            if (navigationHistory.length > 1) {
                navigationHistory.pop();
                navigateTo(navigationHistory[navigationHistory.length - 1], false);
            }
        }

        function resetToHome() {
            navigationHistory = ['home'];
            navigateTo('home', false);
        }

        function updateUI(id) {
            const backBtn = document.getElementById('back-btn');
            const closeBtn = document.getElementById('closeDesignerBtn');

            if (backBtn) backBtn.style.visibility = (id === 'home') ? 'hidden' : 'visible';
            if (closeBtn) closeBtn.style.display = (id === 'home') ? 'none' : 'inline-block';

            const titles = {
    'home': 'تفاصيل المنتج والتصميم',
    'upload': 'رفع تصميم',
    'text': 'إضافة نص',
    'art': 'الرسومات',
    'details': 'تفاصيل المنتج',
    'designs': 'التصميمات'
};

            const headerTitle = document.getElementById('header-title');
            if (headerTitle) headerTitle.innerText = titles[id] || 'المصمم';
        }

        // ============================================================
        // LocalStorage Cleanup — periodic garbage collection
        // ============================================================
        // -------------------------------------------------------
        // تنظيف localStorage القديم
        // -------------------------------------------------------
        function cleanOldLocalStorage() {
            const oneHourAgo = Date.now() - 3600000;
            for (let i = localStorage.length - 1; i >= 0; i--) {
                const key = localStorage.key(i);
                if (key && key.startsWith('img_')) {
                    const parts = key.split('_');
                    const timestamp = parseInt(parts[1]);
                    if (timestamp && timestamp < oneHourAgo) {
                        localStorage.removeItem(key);
                    }
                }
            }
        }

        // ============================================================
        // Custom control helpers
        // ============================================================
        function deleteObject(eventData, transform) {
            const target = transform.target;
            const cnv = target.canvas;
            cnv.remove(target);
            cnv.requestRenderAll();
            return true;
        }

        function duplicateObject(eventData, transform) {
            const target = transform.target;
            const cnv = target.canvas;

            if (target.type === 'activeSelection') {
                let count = 0;
                const objects = target._objects;
                cnv.discardActiveObject();
                objects.forEach((obj) => {
                    obj.clone((cloned) => {
                        cloned.set({ left: cloned.left + 20, top: cloned.top + 20 });
                        cnv.add(cloned);
                        applyCustomControls(cloned);
                        count++;
                        if (count === objects.length) {
                            cnv.renderAll();
                            pushHistory();
                        }
                    });
                });
            } else {
                cnv.discardActiveObject();
                target.clone((cloned) => {
                    cloned.set({ left: cloned.left + 20, top: cloned.top + 20 });
                    cnv.add(cloned);
                    applyCustomControls(cloned);
                    cnv.setActiveObject(cloned);
                    cnv.renderAll();
                    pushHistory();
                });
            }
            return true;
        }

        function renderControlBackground(ctx, left, top, size) {
            const half = size / 2;
            ctx.save();
            ctx.shadowColor = 'rgba(0,0,0,0.14)';
            ctx.shadowBlur = 8;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 2;
            ctx.beginPath();
            ctx.arc(left, top, half, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();
            ctx.restore();
        }

        function renderDeleteIcon(ctx, left, top, styleOverride, fabricObject) {
            const size = this.cornerSize;
            const half = size / 2;
            renderControlBackground(ctx, left, top, size);
            ctx.save();
            ctx.strokeStyle = '#EF4444';
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            const d = half * 0.32;
            ctx.beginPath();
            ctx.moveTo(left - d, top - d);
            ctx.lineTo(left + d, top + d);
            ctx.moveTo(left + d, top - d);
            ctx.lineTo(left - d, top + d);
            ctx.stroke();
            ctx.restore();
        }

        function renderRotateIcon(ctx, left, top, styleOverride, fabricObject) {
            const size = this.cornerSize;
            const half = size / 2;
            renderControlBackground(ctx, left, top, size);
            ctx.save();
            ctx.strokeStyle = '#2563EB';
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            const scale = (size / 24) * 0.55;
            ctx.translate(left, top);
            ctx.scale(scale, scale);
            ctx.translate(-12, -12);
            ctx.lineWidth = 2.5 / scale;
            ctx.stroke(new Path2D('M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8M21 3v5h-5'));
            ctx.restore();
        }

        function renderDuplicateIcon(ctx, left, top, styleOverride, fabricObject) {
            const size = this.cornerSize;
            const half = size / 2;
            renderControlBackground(ctx, left, top, size);
            ctx.save();
            ctx.strokeStyle = '#2563EB';
            ctx.lineWidth = 2.5;
            ctx.lineJoin = 'round';
            const s = half * 0.26;
            const off = 2.5;
            const rr = 1.5;
            ctx.beginPath();
            ctx.moveTo(left - s + off + rr, top - s - off);
            ctx.lineTo(left + s + off - rr, top - s - off);
            ctx.quadraticCurveTo(left + s + off, top - s - off, left + s + off, top - s - off + rr);
            ctx.lineTo(left + s + off, top + s - off - rr);
            ctx.quadraticCurveTo(left + s + off, top + s - off, left + s + off - rr, top + s - off);
            ctx.lineTo(left - s + off + rr, top + s - off);
            ctx.quadraticCurveTo(left - s + off, top + s - off, left - s + off, top + s - off - rr);
            ctx.lineTo(left - s + off, top - s - off + rr);
            ctx.quadraticCurveTo(left - s + off, top - s - off, left - s + off + rr, top - s - off);
            ctx.closePath();
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(left - s - off + rr, top - s + off);
            ctx.lineTo(left + s - off - rr, top - s + off);
            ctx.quadraticCurveTo(left + s - off, top - s + off, left + s - off, top - s + off + rr);
            ctx.lineTo(left + s - off, top + s + off - rr);
            ctx.quadraticCurveTo(left + s - off, top + s + off, left + s - off - rr, top + s + off);
            ctx.lineTo(left - s - off + rr, top + s + off);
            ctx.quadraticCurveTo(left - s - off, top + s + off, left - s - off, top + s + off - rr);
            ctx.lineTo(left - s - off, top - s + off + rr);
            ctx.quadraticCurveTo(left - s - off, top - s + off, left - s - off + rr, top - s + off);
            ctx.closePath();
            ctx.stroke();
            ctx.restore();
        }

        function renderResizeIcon(ctx, left, top, styleOverride, fabricObject) {
            const size = this.cornerSize;
            const half = size / 2;
            renderControlBackground(ctx, left, top, size);
            ctx.save();
            ctx.strokeStyle = '#2563EB';
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            const scale = (size / 24) * 0.55;
            ctx.translate(left, top);
            ctx.scale(scale, scale);
            ctx.translate(-12, -12);
            ctx.lineWidth = 2.5 / scale;
            ctx.stroke(new Path2D('M15 3h6v6M21 3l-7 7M3 21l7-7M9 21H3v-6'));
            ctx.restore();
        }

        // ============================================================
        // Initialize everything
        // ============================================================
        // J9: Browser lifecycle instrumentation
        window.addEventListener('beforeunload', function() {
            try { console.log('[J9] beforeunload — currentView:', currentView, 'canvasViews keys:', Object.keys(canvasViews).join(','), 'history length:', canvasHistory.length); } catch(e) { console.log('[J9] beforeunload — error accessing state:', e.message); }
        });
        window.addEventListener('pagehide', function() {
            try { console.log('[J9] pagehide — currentView:', currentView); } catch(e) { console.log('[J9] pagehide — error:', e.message); }
        });
        document.addEventListener('visibilitychange', function() {
            try { console.log('[J9] visibilitychange — state:', document.visibilityState, 'currentView:', currentView, 'canvasViews keys:', Object.keys(canvasViews).join(',')); } catch(e) { console.log('[J9] visibilitychange — error:', e.message); }
        });
        window.addEventListener('pageshow', function() {
            try { console.log('[J9] pageshow — currentView:', currentView, 'canvasViews keys:', Object.keys(canvasViews).join(',')); } catch(e) { console.log('[J9] pageshow — error:', e.message); }
        });

        document.addEventListener('DOMContentLoaded', async function() {
            console.log('[J9] DOMContentLoaded — firing');

            // --- ZoomLoading: Start ---
            (function() {
                var zl = window.ZoomStore && ZoomStore.ZoomLoading;
                if (!zl) return;
                var img = '';
                var savedColor = (existingVariant && existingVariant.color) ? existingVariant.color.toLowerCase().trim() : '';
                if (savedColor && colorImagesData && colorImagesData[savedColor] && colorImagesData[savedColor].length > 0) {
                    img = assetBase.replace(/\/+$/, '') + '/' + colorImagesData[savedColor][0].replace(/^\/+/, '');
                } else if (productImages && productImages[0]) {
                    img = assetBase.replace(/\/+$/, '') + '/' + productImages[0].replace(/^\/+/, '');
                }
                zl.show({ title: 'Zoom Store', subtitle: productName || '', image: img, message: '\u062C\u0627\u0631\u064A \u062A\u0647\u064A\u0626\u0629 \u0627\u0644\u0645\u062D\u0631\u0631...', allowClose: false });
            })();

            if (!initCanvas()) {
                console.error('Failed to initialize canvas');
                return;
            }
            (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) { zl.setProgress(20); zl.setMessage('\u062C\u0627\u0631\u064A \u062A\u062D\u0636\u064A\u0631 \u0627\u0644\u0623\u062F\u0648\u0627\u062A...'); } })();

            setupControls();
            setupImageUpload();
            initSizesAndColors();
            (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) { zl.setProgress(35); zl.setMessage('\u062C\u0627\u0631\u064A \u062A\u062D\u0645\u064A\u0644 \u0627\u0644\u062E\u0637\u0648\u0637...'); } })();

            // ── Initialize ZoomStore modules ──
            if (window.ZoomStore) {
                // Font system
                if (ZoomStore.FontManager && document.getElementById('fontFamily')) {
                    ZoomStore.FontManager.populateSelect(document.getElementById('fontFamily'));

                    // Re-render canvas when a web font finishes loading
                    ZoomStore.FontManager.onFontReady = function(family) {
                        console.log('[FONT] onFontReady fired for:', family);
                        if (!canvas) {
                            console.log('[FONT] onFontReady — EARLY RETURN, canvas null');
                            return;
                        }
                        var skipFams = ZoomStore.FontManager && ZoomStore.FontManager._skipWidthRecalc;
                        var skipThis = Array.isArray(skipFams) && skipFams.indexOf(family) !== -1;
                        console.log('[FONT] onFontReady — skipThis:', skipThis, '_skipWidthRecalc:', JSON.stringify(skipFams));
                        var objects = canvas.getObjects();
                        console.log('[FONT] onFontReady — canvas objects count:', objects.length);
                        var matchedCount = 0;
                        for (var i = 0; i < objects.length; i++) {
                            if (objects[i].fontFamily === family) {
                                matchedCount++;
                                console.log('[FONT] onFontReady — matching object[' + i + '] type:', objects[i].type, 'fontFamily:', objects[i].fontFamily, 'current width:', objects[i].width);
                                if (objects[i].type === 'textbox') {
                                    var savedWidth = skipThis ? objects[i].width : null;
                                    console.log('[FONT] onFontReady — calling initDimensions() for textbox');
                                    objects[i].initDimensions();
                                    if (skipThis && savedWidth !== null) {
                                        objects[i].set('width', savedWidth);
                                        console.log('[FONT] onFontReady — skipThis, restored width:', savedWidth);
                                    } else if (!skipThis) {
                                        var w = objects[i].calcTextWidth();
                                        if (w && w > 0) objects[i].set('width', w);
                                        console.log('[FONT] onFontReady — calcTextWidth:', w, 'new width:', objects[i].width);
                                    }
                                    if (typeof objects[i].setCoords === 'function') {
                                        objects[i].setCoords();
                                        console.log('[FONT] onFontReady — setCoords() called');
                                    }
                                } else {
                                    if (typeof objects[i].initDimensions === 'function') {
                                        console.log('[FONT] onFontReady — calling initDimensions() for non-textbox');
                                        objects[i].initDimensions();
                                    }
                                    if (typeof objects[i].setCoords === 'function') {
                                        objects[i].setCoords();
                                    }
                                }
                            }
                        }
                        console.log('[FONT] onFontReady — matched objects:', matchedCount, 'rendering all');
                        canvas.requestRenderAll();
                        console.log('[FONT] onFontReady — requestRenderAll() executed');
                    };

                    ZoomStore.FontManager.init(['Josefin Sans', 'Cairo', 'Open Sans']);

                    // Log font state after init
                    console.log('[FONT] FontManager initialized');
                    console.log('[FONT] document.fonts ready:', document.fonts ? document.fonts.size + ' entries' : 'N/A');
                    if (document.fonts) {
                        var fontEntries = [];
                        try {
                            // document.fonts is a Set-like FontFaceSet in modern browsers
                            document.fonts.forEach(function(f) { fontEntries.push(f.family + ' ' + f.style + ' ' + f.weight); });
                        } catch(e) {}
                        console.log('[FONT] document.fonts entries:', fontEntries.length ? JSON.stringify(fontEntries) : '(empty or enumeration failed)');
                    }
                    ['Josefin Sans', 'Cairo', 'Open Sans'].forEach(function(f) {
                        console.log('[FONT] Pre-init check — ' + f + ' loaded:', ZoomStore.FontManager.isLoaded(f), 'loading:', ZoomStore.FontManager.isLoading(f));
                    });
                }

                // Asset system
                if (ZoomStore.AssetManager && ZoomStore.SVGAdapter) {
                    ZoomStore.AssetManager.registerAdapter(ZoomStore.SVGAdapter);
                    ZoomStore.AssetManager.init().catch(function(){}).then(function() {
                        // Re-render categories after async catalog loads
                        if (typeof renderArtCategories === 'function') {
                            renderArtCategories();
                        }
                    });

                    // Wire SVGAdaper search into SearchManager
                    if (ZoomStore.SearchManager) {
                        ZoomStore.SearchManager.registerSource('svg', function(query) {
                            return ZoomStore.AssetManager.searchAll(query);
                        });
                    }
                }

                // Font favorites sync
                if (ZoomStore.FontManager) {
                    var fontFavToggle = document.getElementById('fontFavToggle');
                    if (fontFavToggle) {
                        var curFam = document.getElementById('fontFamily') ? document.getElementById('fontFamily').value : '';
                        fontFavToggle.textContent = (curFam && ZoomStore.FontManager.isFav(curFam)) ? '★' : '☆';
                    }
                }
            }

            renderArtCategories();
            (function(){ var zl = window.ZoomStore && ZoomStore.ZoomLoading; if (zl) { zl.setProgress(50); zl.setMessage('\u062C\u0627\u0631\u064A \u062A\u062C\u0647\u064A\u0632 \u0627\u0644\u062A\u0635\u0645\u064A\u0645...'); } })();

            _logCanvasViews('INIT START');
            console.log('[LIFECYCLE] INIT — canvasViews keys at init start:', Object.keys(canvasViews));

            // ★ Phase 2: Source-of-truth priority — existingDesign > pendingDesign > existingVariant > sessionStorage
            console.log('[CHECKPOINT-6] designer init() Phase 2 — window.location.href:', window.location.href);
            let pendingDesignData = null;
            const pendingDesignRaw = sessionStorage.getItem('pendingDesign');
            console.log('[CHECKPOINT-6] pendingDesign raw from sessionStorage:', pendingDesignRaw ? '(found, length=' + pendingDesignRaw.length + ')' : '(not found)');
            console.log('[FLOW INIT] Phase 2 start — existingVariant:', !!existingVariant, 'existingDesign:', !!existingDesign, 'pendingDesignRaw:', pendingDesignRaw);

            // Phase 1: Read and validate pendingDesign — timestamp only, NOT productId
            // Product mismatch is handled in _restoreFromPending via switchProductInPlace
            if (pendingDesignRaw) {
                if (existingDesign) {
                    console.log('[FLOW INIT] Removing pendingDesign because existingDesign present (server data is authoritative)');
                    sessionStorage.removeItem('pendingDesign');
                } else {
                    try {
                        pendingDesignData = JSON.parse(pendingDesignRaw);
                        const oneHourAgo = Date.now() - 3600000;
                        if (!pendingDesignData.timestamp || pendingDesignData.timestamp < oneHourAgo) {
                            console.warn('Stale pendingDesign (expired), removing');
                            sessionStorage.removeItem('pendingDesign');
                            pendingDesignData = null;
                        } else if (!pendingDesignData.canvasViews || !pendingDesignData.selectedSize) {
                            console.warn('Incomplete pendingDesign, removing');
                            sessionStorage.removeItem('pendingDesign');
                            pendingDesignData = null;
                        } else {
                            console.log('[FLOW INIT] Valid pendingDesign found — productId:', pendingDesignData.productId, 'currentProductId:', currentProductId);
                        }
                    } catch (e) {
                        console.warn('Corrupt pendingDesign in sessionStorage, removing');
                        sessionStorage.removeItem('pendingDesign');
                    }
                }
            }

            if (pendingDesignData) {
                // Full async state machine restoration — product switch, variant/size/color,
                // canvas views, PlacementGuide, history. Stops on the designer page; no auto checkout.
                console.log('[CHECKPOINT-7] restoring pending design — pendingDesignData.productId:', pendingDesignData.productId, 'selectedSize:', pendingDesignData.selectedSize, 'selectedColor:', pendingDesignData.selectedColor, 'selectedVariantId:', pendingDesignData.selectedVariantId, 'views:', Object.keys(pendingDesignData.canvasViews || {}).length, 'currentView:', pendingDesignData.currentView);
                console.log('[FLOW INIT] BRANCH PENDING — starting _restoreFromPending state machine');
                try {
                    await _restoreFromPending(pendingDesignData);
                } catch (e) {
                    console.error('[FLOW INIT] _restoreFromPending failed:', e);
                    sessionStorage.removeItem('pendingDesign');
                }
            } else if (existingVariant && existingVariant.size) {
                console.log('[FLOW INIT] BRANCH A — existingVariant present with size, will call loadExistingDesign via setTimeout 200ms');
                const sizeBtn = document.querySelector(`.size-btn[data-size="${existingVariant.size}"]`);
                if (sizeBtn) {
                    console.log('[FLOW INIT] BRANCH A1 — sizeBtn found, calling selectSize');
                    selectSize(sizeBtn);
                    setTimeout(() => {
                        console.log('[FLOW INIT] BRANCH A1 — timeout fired at ~200ms');
                        const colorBtn = document.querySelector(`.color-btn[data-color="${existingVariant.color}"]`);
                        if (colorBtn) {
                            console.log('[FLOW INIT] BRANCH A1a — colorBtn found, calling selectColor');
                            selectColor(colorBtn);
                        } else if (existingVariant.color) {
                            console.log('[FLOW INIT] BRANCH A1b — colorBtn NOT found, setting selectedColor directly');
                            selectedColor = existingVariant.color;
                            window._selectedColorKey = selectedColor.toLowerCase().trim();
                            sessionStorage.setItem('selectedColor', selectedColor);
                            if (existingVariant.variant_id) {
                                document.getElementById('variant_id').value = existingVariant.variant_id;
                                sessionStorage.setItem('selectedVariantId', existingVariant.variant_id);
                            }
                            const colorKey = existingVariant.color.toLowerCase().trim();
                            const imgs = (colorImagesData && colorImagesData[colorKey])
                                ? colorImagesData[colorKey]
                                : productImages;
                            updateThumbnails(imgs, false);
                            hideCanvasOverlay();
                        } else if (existingVariant.variant_id) {
                            console.log('[FLOW INIT] BRANCH A1c — fallback to variant_id only');
                            document.getElementById('variant_id').value = existingVariant.variant_id;
                        }

                        if (existingDesign) {
                            console.log('[FLOW INIT] BRANCH A — calling loadExistingDesign()');
                            loadExistingDesign();
                            console.log('[FLOW INIT] BRANCH A — AFTER loadExistingDesign()');
                            setTimeout(function() { _logCanvasViews('AFTER loadExistingDesign (100ms later)'); }, 100);
                        } else {
                            console.log('[FLOW INIT] BRANCH A — existingDesign is falsy, skipping loadExistingDesign');
                        }
                    }, 200);
                } else {
                    console.log('[FLOW INIT] BRANCH A — sizeBtn NOT found for size:', existingVariant.size, '— using fallback');
                    if (existingVariant.variant_id) {
                        document.getElementById('variant_id').value = existingVariant.variant_id;
                        sessionStorage.setItem('selectedVariantId', existingVariant.variant_id);
                    }
                    if (existingVariant.color) {
                        selectedColor = existingVariant.color;
                        window._selectedColorKey = selectedColor.toLowerCase().trim();
                        sessionStorage.setItem('selectedColor', selectedColor);
                        const colorKey = existingVariant.color.toLowerCase().trim();
                        const imgs = (colorImagesData && colorImagesData[colorKey])
                            ? colorImagesData[colorKey]
                            : productImages;
                        updateThumbnails(imgs, false);
                        hideCanvasOverlay();
                    } else if (existingVariant.variant_id) {
                        document.getElementById('variant_id').value = existingVariant.variant_id;
                        hideCanvasOverlay();
                    }
                    if (existingDesign) {
                        console.log('[FLOW INIT] BRANCH A fallback — calling loadExistingDesign()');
                        loadExistingDesign();
                        setTimeout(function() { _logCanvasViews('AFTER loadExistingDesign fallback (100ms later)'); }, 100);
                    }
                }
            } else {
                console.log('[FLOW INIT] BRANCH B — existingVariant missing or has no size');
                if (productImages && productImages.length > 0 && productImages[0]) {
                    console.log('[FLOW INIT] BRANCH B — loading first product image');
                    await loadProductImage(fixImagePath(productImages[0]));
                }

                // Note: pendingDesignData is always null here — already consumed by _restoreFromPending above
                const savedSize = sessionStorage.getItem('selectedSize');
                const savedColor = sessionStorage.getItem('selectedColor');
                const savedVariantId = sessionStorage.getItem('selectedVariantId');
                console.log('[FLOW INIT] BRANCH B — sessionStorage:', { savedSize, savedColor, savedVariantId });

                if (savedSize) {
                    const sizeBtn = document.querySelector(`.size-btn[data-size="${savedSize}"]`);
                    if (sizeBtn) {
                        selectSize(sizeBtn);

                        if (savedColor) {
                            setTimeout(() => {
                                const colorBtn = document.querySelector(
                                    `.color-btn[data-color="${savedColor}"]`);
                                if (colorBtn) {
                                    selectColor(colorBtn);
                                    if (savedVariantId) {
                                        document.getElementById('variant_id').value = savedVariantId;
                                    }
                                }
                            }, 150);
                        }
                    }
                } else {
                    navigateTo('details');
                }

                if (existingDesign) {
                    console.log('[FLOW INIT] BRANCH B — scheduling loadExistingDesign via setTimeout 400ms');
                    setTimeout(function() {
                        loadExistingDesign();
                        console.log('[FLOW INIT] BRANCH B — AFTER loadExistingDesign()');
                        setTimeout(function() { _logCanvasViews('AFTER loadExistingDesign B (100ms later)'); }, 100);
                    }, 400);
                } else {
                    console.log('[FLOW INIT] BRANCH B — NOT calling loadExistingDesign. existingDesign:', !!existingDesign);
                }
            }

            // pushHistory and _zlTryComplete for non-pending paths only
            // _restoreFromPending handles these internally for pending designs
            if (!pendingDesignData) {
                setTimeout(() => pushHistory(), 600);

                if (!existingDesign) {
                    _zlTryComplete();
                }
            }

            // تنظيف دوري للـ localStorage
            setInterval(cleanOldLocalStorage, 3600000);

            console.log('Initialization complete');
        });
</script>


{{-- Designs Tab JS --}}
<script>
    (function() {
    'use strict';
    var designsLoaded = false;
    var designsGrid = document.getElementById('designsGrid');

    function addDesignToCanvas(imageUrl) {
        if (typeof canvas === 'undefined' || !canvas) return;
        var center = typeof getPrintZoneCenter === 'function' ? getPrintZoneCenter() : { left: 100, top: 100 };
        fabric.Image.fromURL(imageUrl, function(img) {
            img._debugId = typeof _newDebugId === 'function' ? _newDebugId() : Date.now();
            var scale = 0.4;
            var bounds = typeof getPrintZoneBounds === 'function' ? getPrintZoneBounds() : null;
            if (bounds && img.width > 0 && img.height > 0) {
                var targetW = bounds.width * 0.65;
                var targetH = bounds.height * 0.65;
                scale = Math.min(targetW / img.width, targetH / img.height, 1);
            }
            img.set({
                left: center.left,
                top: center.top,
                originX: 'center',
                originY: 'center',
                scaleX: scale,
                scaleY: scale
            });
            img._customSrc = imageUrl;
            if (typeof applyCustomControls === 'function') applyCustomControls(img);
            canvas.add(img);
            if (typeof _logAdd === 'function') _logAdd(img, 'canvas');
            canvas.setActiveObject(img);
            canvas.renderAll();
            if (typeof saveCurrentView === 'function') saveCurrentView();
        });
    }
    window.addDesignToCanvas = addDesignToCanvas;

    function loadDesigns() {
        if (designsLoaded) return;
        if (!designsGrid) return;
        designsGrid.innerHTML = '<div class="designs-loading">جاري تحميل التصميمات...</div>';
        fetch('{{ route('api.designs') }}')
            .then(function(r) { return r.json(); })
            .then(function(designs) {
                if (!designs || designs.length === 0) {
                    designsGrid.innerHTML = '<div class="designs-empty">لا توجد تصاميم متاحة</div>';
                    return;
                }
                var html = '';
                designs.forEach(function(d) {
                    html += '<div class="design-card" onclick="addDesignToCanvas(\'' + d.image.replace(/'/g, "\\'") + '\')">';
                    html += '<img src="' + d.image + '" alt="' + d.name.replace(/"/g, '&quot;') + '" loading="lazy">';
                    html += '<div class="design-name">' + d.name + '</div>';
                    html += '</div>';
                });
                designsGrid.innerHTML = html;
                designsLoaded = true;
            })
            .catch(function() {
                designsGrid.innerHTML = '<div class="designs-empty">فشل تحميل التصميمات</div>';
            });
    }

    var _origNavDesigns = window.navigateTo;
    if (typeof _origNavDesigns === 'function') {
        window.navigateTo = function() {
            var sectionId = arguments[0];
            var r = _origNavDesigns.apply(this, arguments);
            if (sectionId === 'designs') {
                setTimeout(loadDesigns, 50);
            }
            return r;
        };
    }
})();
</script>

{{-- Action Bar Functions --}}
<script>
    // ============================================================
    // Action Bar Functions — Save, Share, Add-to-Cart, Switcher
    // ============================================================

    var designableProducts = @json($designableProducts ?? []);

    /**
     * Save design without redirecting
     */
    async function saveDesign() {
        if (typeof handleSubmit !== 'function') return;
        var form = document.getElementById('addToCartForm');
        var origSubmit = form.submit;
        form.submit = function() {};
        try {
            await handleSubmit();
            showToast('\u062A\u0645 \u062D\u0641\u0638 \u0627\u0644\u062A\u0635\u0645\u064A\u0645 \u0628\u0646\u062C\u0627\u062D', 'success');
        } finally {
            form.submit = origSubmit;
        }
    }

    /**
     * Copy shareable link to clipboard
     */
    function shareDesign() {
        var url = window.location.href;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function() {
                showToast('\u062A\u0645 \u0646\u0633\u062E \u0631\u0627\u0628\u0637 \u0627\u0644\u062A\u0635\u0645\u064A\u0645', 'success');
            }).catch(function() {
                fallbackCopy(url);
            });
        } else {
            fallbackCopy(url);
        }
    }

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            showToast('\u062A\u0645 \u0646\u0633\u062E \u0631\u0627\u0628\u0637 \u0627\u0644\u062A\u0635\u0645\u064A\u0645', 'success');
        } catch (e) {
            alert('\u0627\u0644\u0631\u0627\u0628\u0637: ' + text);
        }
        document.body.removeChild(ta);
    }

    /**
     * Save then add to cart
     */
    async function addToCartDesign() {
        if (typeof handleSubmit === 'function') {
            await handleSubmit();
        }
        // handleSubmit will submit addToCartForm at the end
    }

    /**
     * Navigate to size/color selection
     */
    function switchSize() {
        navigateTo('details');
    }

    /**
     * Show product switcher (uses Coza Store Quick View modal)
     */
    async function openProductSwitcher() {
        var modal = document.getElementById('productSwitcherModal');
        if (!modal) return;
        var grid = document.getElementById('switcherProductsGrid');
        if (grid) grid.innerHTML = '<div style="text-align:center; padding:40px 10px; color:#999; width:100%;">\u062C\u0627\u0631\u064A \u062A\u062D\u0645\u064A\u0644 \u0627\u0644\u0645\u0646\u062A\u062AC\u0627\u062A...</div>';
        modal.classList.add('show-modal1');
        try {
            var resp = await fetch('/api/editor/designable-products/' + currentProductId);
            if (resp.ok) {
                designableProducts = await resp.json();
            }
        } catch (e) { /* fall through to embedded data */ }
        renderSwitcherProducts();
    }

    function closeProductSwitcher() {
        var modal = document.getElementById('productSwitcherModal');
        if (modal) modal.classList.remove('show-modal1');
    }

    function renderSwitcherProducts() {
        var grid = document.getElementById('switcherProductsGrid');
        if (!grid) return;

        var products = designableProducts || [];

        if (!products || products.length === 0) {
            grid.innerHTML = '<div class="col-12" style="text-align:center; padding:40px 10px; color:#999;">لا توجد منتجات أخرى متاحة</div>';
            return;
        }

        var html = '';
        products.forEach(function(p) {
            if (!p.id) return;
            var imgTag = p.image
                ? '<img src="' + p.image + '" alt="' + p.name + '">'
                : '<img src="' + (typeof assetBase !== 'undefined' ? assetBase : '') + '/assets/frontend/images/product-placeholder.png" alt="' + p.name + '">';
            html += '<div class="col-sm-6 col-md-4 col-lg-3 p-b-35">';
            html += '  <div class="block2">';
            html += '    <div class="block2-pic hov-img0">';
            html += imgTag;
            html += '      <a href="javascript:void(0)" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-hide-modal1" onclick="switchProductInPlace(' + p.id + ')">\u062A\u0628\u062F\u064A\u0644</a>';
            html += '    </div>';
            html += '    <div class="block2-txt flex-w p-t-14">';
            html += '      <div class="block2-txt-child1 flex-col-l">';
            html += '        <a href="javascript:void(0)" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">' + p.name + '</a>';
            html += '        <span class="stext-105 cl3">' + p.price + ' \u0631.\u0633</span>';
            html += '      </div>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
        });
        grid.innerHTML = html;
    }

    /**
     * Switch product in-place without page reload.
     * Uses SlotMatchingService, CoordinateTransformationService, and
     * ProductSwitchConfirmationService via backend API.
     * Atomic operation — entire switch is one history checkpoint.
     */
    async function switchProductInPlace(productId) {
        console.log('[SWITCH] Called — productId:', productId, 'currentProductId:', currentProductId);
        closeProductSwitcher();

        if (!productId || productId === currentProductId) { console.log('[SWITCH] EARLY RETURN — same product or no id'); return true; }

        // ---- Step 1: Capture rollback state ----
        var rollbackJson = JSON.stringify(canvas.toJSON(['_customSrc', '_isArt', '_artKey', '_artColor', '_embossLevel', '_assetMeta', '_slotKey', '_zoomObjectId']));

        // ---- Step 2: Fetch target product data ----
        try {
            console.log('[SWITCH-REQ1] GET /api/editor/product-data/' + productId);
            var response = await fetch('/api/editor/product-data/' + productId);
            var r1Text = await response.text();
            console.log('[SWITCH-REQ1] Status:', response.status);
            console.log('[SWITCH-REQ1] Content-Type:', response.headers.get('Content-Type'));
            console.log('[SWITCH-REQ1] Body (first 300):', r1Text.substring(0, 300));
            if (!response.ok) throw new Error('Failed to fetch product data');
            var data = JSON.parse(r1Text);
            console.log('[SWITCH-REQ1] OK — parsed data keys:', Object.keys(data));
        } catch (err) {
            console.error('[SWITCH-REQ1] FAILED:', err);
            showToast('\u0641\u0634\u0644 \u062A\u062D\u0645\u064A\u0644 \u0628\u064A\u0627\u0646\u0627\u062A \u0627\u0644\u0645\u0646\u062A\u062AC', 'error');
            return false;
        }

        // ---- Step 1b: Save current canvas to canvasViews before serializing ----
        await saveCurrentView();

        // ---- Step 3: Serialize ALL views for backend ----
        var serializedObjects = [];
        canvas.getObjects().forEach(function(obj) {
            if (obj._isPrintZone || obj.excludeFromExport) return;
            serializedObjects.push({
                _zoomObjectId: obj._zoomObjectId || null,
                _slotKey: obj._slotKey || null,
                type: obj.type,
                left: obj.left,
                top: obj.top,
                width: obj.width,
                height: obj.height,
                scaleX: obj.scaleX,
                scaleY: obj.scaleY,
                angle: obj.angle || 0,
                opacity: obj.opacity || 1,
                originX: obj.originX || 'left',
                originY: obj.originY || 'top',
                _isPrintZone: false,
                excludeFromExport: false
            });
        });

        // Second: build views payload from ALL canvasViews
        var viewsPayload = {};
        for (var viewIdx in canvasViews) {
            if (!canvasViews[viewIdx] || !canvasViews[viewIdx].objects) continue;
            viewsPayload[viewIdx] = {
                objects: canvasViews[viewIdx].objects.filter(function(obj) {
                    return !obj._isPrintZone && !obj.excludeFromExport;
                }).map(function(obj) {
                    return {
                        _zoomObjectId: obj._zoomObjectId || null,
                        _slotKey: obj._slotKey || null,
                        type: obj.type,
                        left: obj.left,
                        top: obj.top,
                        width: obj.width || 0,
                        height: obj.height || 0,
                        scaleX: obj.scaleX || 1,
                        scaleY: obj.scaleY || 1,
                        angle: obj.angle || 0,
                        opacity: obj.opacity || 1,
                        originX: obj.originX || 'left',
                        originY: obj.originY || 'top'
                    };
                })
            };
        }

        // ---- Step 4: Backend call with ALL views for multi-view transformation ----
        var switchData;
        var postBody = JSON.stringify({
            source_product_id: currentProductId,
            target_product_id: data.product_id,
            objects: serializedObjects,
            views: viewsPayload
        });
        console.log("========== REQUEST PAYLOAD ==========");
        console.log(JSON.stringify({source_product_id: currentProductId, target_product_id: data.product_id, objects_count: serializedObjects.length, views: viewsPayload}, null, 2));
        console.log("========== END PAYLOAD ==========");
        var switchResp = await fetch('/api/editor/switch-product', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '' },
            body: postBody
        });
        var r2Text = await switchResp.text();
        console.log("========== RESPONSE ==========");
        console.log("Status:", switchResp.status);
        console.log("Content-Type:", switchResp.headers.get("content-type"));
        console.log(r2Text.substring(0, 1000));
        console.log("========== END RESPONSE ==========");
        if (!switchResp.ok) { console.error('[SWITCH] Backend error:', switchResp.status); showToast('\u0641\u0634\u0644 \u062A\u062D\u0645\u064A\u0644 \u0627\u0644\u0645\u0646\u062A\u062AC', 'error'); return false; }
        switchData = JSON.parse(r2Text);

        // ---- Steps 6-14: Atomic mutation — canvas + state + history checkpoint ----
        try {
            // Step 6: Merge transformed geometry into canvasViews and live canvas
            var allTransformedById = {};
            if (switchData.transformed_views) {
                for (var viewIdx in switchData.transformed_views) {
                    if (!canvasViews[viewIdx]) continue;
                    var to = switchData.transformed_views[viewIdx];
                    var viewById = {};
                    to.forEach(function(t) {
                        if (t._zoomObjectId) {
                            viewById[t._zoomObjectId] = t;
                            allTransformedById[t._zoomObjectId] = t;
                        }
                    });
                    // Merge into canvasViews: only left/top change
                    canvasViews[viewIdx].objects.forEach(function(obj) {
                        var t = obj._zoomObjectId ? viewById[obj._zoomObjectId] : null;
                        if (t) {
                            obj.left = t.left;
                            obj.top = t.top;
                        }
                    });
                }
            } else if (switchData.transformed) {
                // Fallback: single-view transformation
                switchData.transformed.forEach(function(t) {
                    if (t._zoomObjectId) allTransformedById[t._zoomObjectId] = t;
                });
            }
            // Update live Fabric objects on the current canvas
            canvas.getObjects().forEach(function(obj) {
                if (obj._isPrintZone || obj.excludeFromExport) return;
                var t = obj._zoomObjectId ? allTransformedById[obj._zoomObjectId] : null;
                if (t) {
                    obj.set({ left: t.left, top: t.top });
                    obj.setCoords();
                }
            });
            canvas.requestRenderAll();

            // Step 7: Update all product state
            console.log('[SWITCH-STEP7] currentProductId BEFORE:', currentProductId, '→ AFTER:', data.product_id);
            console.log('[SWITCH-STEP7] base_images:', data.base_images ? data.base_images.length + ' entries' : 'null');
            console.log('[SWITCH-STEP7] color_images keys:', data.color_images ? Object.keys(data.color_images) : 'null');
            console.log('[SWITCH-STEP7] view_names:', data.view_names);
            window.productName = data.product_name;
            currentProductId = data.product_id;
            variants = data.variants;
            productImages = data.base_images;
            colorImages = data.color_images;
            designAreas = data.design_areas;
            viewNames = data.view_names;
            colorViewNames = data.color_view_names;
            areasByView = data.areas_by_view;
            buildSlotKeyToAreaMap();
            variantsData = data.variants_data;
            colorImagesData = data.color_images;

            // Step 8: Update hidden form fields
            var variantIdField = document.getElementById('variant_id');
            if (variantIdField) variantIdField.value = data.variant_id || '';
            var productIdField = document.getElementById('product_id');
            if (productIdField) productIdField.value = data.product_id;
            var productNameField = document.getElementById('product_name');
            if (productNameField) productNameField.value = data.product_name;
            var productPriceField = document.getElementById('product_price');
            if (productPriceField) productPriceField.value = data.product_price;

            // Step 8b: Update form action URL for cart submission
            var addToCartForm = document.getElementById('addToCartForm');
            if (addToCartForm) {
                addToCartForm.action = '/addproducttocart/' + data.product_id;
            }

            // Step 9: Update action bar display
            var nameEl = document.getElementById('actionBarProductName');
            if (nameEl) nameEl.textContent = data.product_name;
            var priceEl = document.getElementById('actionBarProductPrice');
            if (priceEl) priceEl.textContent = data.product_price + ' \u0631.\u0633';

            // Step 10: Reset selection state
            selectedSize = null;
            selectedColor = null;
            window._selectedColorKey = null;
            sessionStorage.removeItem('selectedSize');
            sessionStorage.removeItem('selectedColor');
            sessionStorage.removeItem('selectedVariantId');

            currentView = 0;
            currentPrintAreaIndex = 0;

            // Step 10b: Clear image cache entries belonging to previous product
            if (typeof imageCache !== 'undefined') {
                Object.keys(imageCache).forEach(function(key) {
                    if (key.indexOf('/products/') !== -1) {
                        delete imageCache[key];
                    }
                });
            }

            // Step 11: Load first available color
            var firstColorKey = null;
            if (data.color_images && Object.keys(data.color_images).length > 0) {
                firstColorKey = Object.keys(data.color_images)[0];
            }

            if (firstColorKey) {
                selectedColor = firstColorKey;
                window._selectedColorKey = firstColorKey.toLowerCase().trim();
                sessionStorage.setItem('selectedColor', firstColorKey);

                if (data.variants_data) {
                    for (var sz in data.variants_data) {
                        if (data.variants_data[sz][firstColorKey]) {
                            var vid = data.variants_data[sz][firstColorKey].id;
                            if (variantIdField) variantIdField.value = vid;
                            sessionStorage.setItem('selectedVariantId', vid);
                            selectedSize = sz;
                            sessionStorage.setItem('selectedSize', sz);
                            break;
                        }
                    }
                }

                var qtyEl = document.getElementById('availableQty');
                var weightEl = document.getElementById('weight');
                var materialEl = document.getElementById('material');
                if (selectedSize && data.variants_data[selectedSize] && data.variants_data[selectedSize][firstColorKey]) {
                    var vData = data.variants_data[selectedSize][firstColorKey];
                    if (qtyEl) qtyEl.textContent = vData.quantity || '--';
                    if (weightEl) weightEl.textContent = vData.weight || '--';
                    if (materialEl) materialEl.textContent = vData.material || '--';
                }
            }

            // Step 12: Load product background image (await actual load)
            var oldBgSrc = canvas.backgroundImage ? (typeof canvas.backgroundImage.getSrc === 'function' ? canvas.backgroundImage.getSrc() : 'no-getSrc') : 'NO BACKGROUND';
            var colorImagesForBg = (firstColorKey && data.color_images[firstColorKey])
                ? data.color_images[firstColorKey]
                : data.base_images_urls;
            var imgSrc = null;
            if (colorImagesForBg && colorImagesForBg.length > 0) {
                imgSrc = colorImagesForBg[0];
            } else if (data.base_images_urls && data.base_images_urls.length > 0) {
                imgSrc = data.base_images_urls[0];
            }
            console.log('[SWITCH-STEP12] oldBgSrc:', oldBgSrc);
            console.log('[SWITCH-STEP12] imgSrc BEFORE:', imgSrc);
            if (imgSrc) {
                await loadProductImage(imgSrc);
            }
            var newBgSrc = canvas.backgroundImage ? (typeof canvas.backgroundImage.getSrc === 'function' ? canvas.backgroundImage.getSrc() : 'no-getSrc') : 'NO BACKGROUND';
            console.log('[SWITCH-STEP12] bgSrc AFTER loadProductImage:', newBgSrc);
            console.log('[SWITCH-STEP12] changed?', oldBgSrc !== newBgSrc);

            // Step 13: (moved into _executeChangeImage at Step 14)

            // Step 14: Load Front view from transformed data before saving
            if (imgSrc) {
                await _executeChangeImage(imgSrc, 0, true);
            }
            saveCurrentView();
            // Step 15: ONE atomic history checkpoint — entire switch is one undo step
            pushHistory();

            // Capture all thumbnails after product switch
            _thumbnailDirty = {};
            if (typeof updateAllViewThumbnails === 'function') updateAllViewThumbnails();

        } catch (err) {
            console.error('[SWITCH] Mutation error:', err);
            canvas.loadFromJSON(rollbackJson, function() {
                canvas.getObjects().forEach(function(o) { if (!o._isPrintZone && !o.excludeFromExport) enforcePrintAreaBounds(o); });
                canvas.renderAll();
            });
            showToast('\u0641\u0634\u0644 \u062A\u0637\u0628\u064A\u0642 \u0627\u0644\u0623\u0645\u0627\u0643\u0646', 'error');
            return false;
        }

        // ---- Step 16: Re-render sizes/colors UI ----
        if (typeof displayColorsForSize === 'function' && selectedSize) {
            displayColorsForSize(selectedSize);
            var firstColorBtn = document.querySelector('.color-btn[data-color="' + firstColorKey + '"]');
            if (firstColorBtn) {
                firstColorBtn.style.border = '3px solid #ff6e26';
                firstColorBtn.style.transform = 'scale(1.1)';
            }
        } else if (typeof initSizesAndColors === 'function') {
            initSizesAndColors();
        }

        // ---- Step 17: Update thumbnails ----
        if (typeof updateThumbnails === 'function') {
            var thumbImages = (firstColorKey && data.color_images[firstColorKey])
                ? data.color_images[firstColorKey]
                : data.base_images;
            updateThumbnails(thumbImages, false);
            if (typeof refreshCurrentViewThumbnail === 'function') {
                refreshCurrentViewThumbnail();
            }
        }

        // ---- Step 18: Update action bar ----
        if (typeof updateActionBar === 'function') updateActionBar();

        // ---- Step 19: Refresh designable products list ----
        try {
            var dpResp = await fetch('/api/editor/designable-products/' + currentProductId);
            if (dpResp.ok) {
                designableProducts = await dpResp.json();
            }
        } catch (e) { /* ignore */ }

        showToast('\u062A\u0645 \u062A\u0628\u062F\u064A\u0644 \u0627\u0644\u0645\u0646\u062A\u062C \u0628\u0646\u062C\u0627\u062D \u2014 \u0627\u0644\u0635\u0645\u064A\u0645 \u0645\u062D\u0641\u0648\u0638', 'success');
        return true;
    }

    /**
     * Simple toast notification
     */
    function showToast(msg, type) {
        var existing = document.getElementById('actionToast');
        if (existing) existing.remove();
        var toast = document.createElement('div');
        toast.id = 'actionToast';
        toast.className = 'action-toast action-toast-' + (type || 'info');
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.classList.add('action-toast-show');
        }, 10);
        setTimeout(function() {
            toast.classList.remove('action-toast-show');
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    }
</script>

{{-- Action Bar Integration JS --}}
<script>
    /* ===========================================
   Action Bar Integration
   =========================================== */
(function() {
    'use strict';
    function getColorCode(colorName) {
        if (!colorName || colorName === '—') return '#737397';
        var map = {
            'أحمر': '#ff0000', 'احمر': '#ff0000', 'red': '#ff0000',
            'أزرق': '#0000ff', 'ازرق': '#0000ff', 'blue': '#0000ff',
            'أخضر': '#00ff00', 'اخضر': '#00ff00', 'green': '#00ff00',
            'أصفر': '#ffff00', 'اصفر': '#ffff00', 'yellow': '#ffff00',
            'أسود': '#000000', 'اسود': '#000000', 'black': '#000000',
            'أبيض': '#ffffff', 'ابيض': '#ffffff', 'white': '#ffffff',
            'رمادي': '#808080', 'gray': '#808080', 'grey': '#808080',
            'بني': '#8b4513', 'brown': '#8b4513',
            'بنفسجي': '#800080', 'purple': '#800080',
            'برتقالي': '#ffa500', 'orange': '#ffa500'
        };
        return map[colorName.toLowerCase().trim()] || '#737397';
    }

    function updateActionBar() {
        var nameEl = document.getElementById('actionBarProductName');
        if (nameEl && typeof productName !== 'undefined') nameEl.textContent = productName;
        var colorBox = document.getElementById('actionBarColorBox');
        var colorText = document.getElementById('actionBarColorText');
        var c = window._selectedColorKey || sessionStorage.getItem('selectedColor') || '—';
        if (colorText) {
            colorText.textContent = c.charAt(0).toUpperCase() + c.slice(1);
        }
        if (colorBox) {
            colorBox.style.backgroundColor = getColorCode(c);
        }
        var imgEl = document.getElementById('actionBarImage');
        if (imgEl) {
            var colorKey = (c || '').toLowerCase().trim();
            var imgs = (typeof colorImagesData !== 'undefined' && colorImagesData[colorKey]) ? colorImagesData[colorKey] : null;
            if (imgs && imgs.length > 0) {
                imgEl.src = typeof fixImagePath === 'function' ? fixImagePath(imgs[0]) : imgs[0];
            } else if (typeof productImages !== 'undefined' && productImages && productImages.length > 0) {
                imgEl.src = typeof fixImagePath === 'function' ? fixImagePath(productImages[0]) : productImages[0];
            }
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateActionBar);
    } else {
        updateActionBar();
    }
    document.addEventListener('click', function(e) {
        if (e.target.closest('.color-btn, .size-btn')) setTimeout(updateActionBar, 50);
    });
    var _origLE = window.loadExistingDesign;
    if (typeof _origLE === 'function') {
        window.loadExistingDesign = function() {
            var r = _origLE.apply(this, arguments);
            setTimeout(updateActionBar, 100);
            return r;
        };
    }
    var _origNav = window.navigateTo;
    if (typeof _origNav === 'function') {
        window.navigateTo = function() {
            var r = _origNav.apply(this, arguments);
            setTimeout(updateActionBar, 100);
            return r;
        };
    }
    console.log('[ActionBar] Initialized');
})();
</script>

@endsection