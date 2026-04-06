@extends('layouts.master')

@section('content')

<style>
    body {
        background-color: #fff;
        font-family: sans-serif;
    }

    .table td {
        vertical-align: middle;
    }

    .right- {
        padding-right: 10px;
    }

    .product-img {
        width: 80px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }

    .table thead th {
        border-top: none;
        font-size: 0.8rem;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .qty-input {
        width: 50px;
        text-align: center;
        border: 1px solid #ddd;
        height: 38px;
    }

    .qty-btn {
        border: 1px solid #ddd;
        width: 35px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #888;
    }

    .qty-btn:first-child {
        border-radius: 50%;
    }

    .qty-btn:last-child {
        border-radius: 50%;
    }

    .summary-section {
        background-color: #fcfcfc;
        min-height: 100vh;
        padding: 40px;
        border-left: 1px solid #eee;
    }

    .btn-checkout {
        background-color: #ff6e26;
        color: white;
        border-radius: 30px;
        padding: 12px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .btn-paypal {
        border: 1px solid #ddd;
        border-radius: 30px;
        padding: 12px;
        background: white;
        font-size: 0.9rem;
    }

    .remove-icon {
        color: #000;
        cursor: pointer;
        border: 1px solid #000;
        border-radius: 50%;
        padding: 2px 5px;
        font-size: 0.7rem;
    }

    .form-check-input:checked {
        background-color: #000;
        border-color: #000;
    }
</style>
</head>


<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 p-5" dir="rtl">
            <div class="mb-4" dir="ltr">
                <a href="/">
                    <i class="fa-solid fa-arrow-left">
                    </i>
                </a>


            </div>

            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col " class="text-right"> Product </th>
                        <th scope="col" class="text-center">QUANTITY</th>
                        <th scope="col" class="text-right">TOTAL</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <tr class="border-bottom">
                        <td>
                            <div class="d-flex align-items-center">

                                <img src="https://via.placeholder.com/80x100" class="product-img me-3">
                                <div class="right-">
                                    <h6 class="mb-0 fw-bold small">Breathable slim sport vest</h6>
                                    <p class="text-muted mb-0 small">Color: Blue / Size: S</p>
                                    <span class="fw-bold small">$ 59.00</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">

                                1

                            </div>
                        </td>
                        <td class="text-end fw-bold small">$ 59.00</td>
                        <td class="text-center"><i class="fa-solid fa-xmark remove-icon"></i></td>
                    </tr>
                    <tr class="border-bottom">
                        <td>
                            <div class="d-flex align-items-center">

                                <img src="https://via.placeholder.com/80x100" class="product-img me-3">
                                <div class="right-">
                                    <h6 class="mb-0 fw-bold small">Breathable slim sport vest</h6>
                                    <p class="text-muted mb-0 small">Color: Pink / Size: S</p>
                                    <span class="fw-bold small">$ 39.00</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                1
                            </div>
                        </td>
                        <td class="text-end fw-bold small">$ 39.00</td>
                        <td class="text-center"><i class="fa-solid fa-xmark remove-icon"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-lg-4 summary-section" dir="rtl">
            <h4 class="fw-bold mb-4">SUMMARY</h4>

            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted small">Do you have a promo code? <i
                        class="fa-regular fa-circle-question"></i></span>
                <i class="fa-solid fa-chevron-down small"></i>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Subtotal</span>
                <span class="fw-bold small">$ 137.00</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <span class="text-muted small">International Shipping</span>
                <span class="fw-bold small">$ 18.00</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h3 class="fw-bold">Total</h3>
                <h3 class="fw-bold">$ 155.00</h3>
            </div>

            <button class="btn btn-checkout w-100 mb-3 text-uppercase">Check Out</button>


            <div class="mt-5">
                <h6 class="fw-bold small text-uppercase mb-1">Free Shipping</h6>
                <p class="text-muted" style="font-size: 0.7rem;">YOUR ORDER QUALIFIES FOR FREE SHIPPING. JOIN US for
                    free shipping on every order, every time.</p>
                <a href="#" class="text-dark fw-bold text-decoration-none small border-bottom border-dark">NEED
                    HELP?</a>
            </div>
        </div>
    </div>
</div>

@endsection