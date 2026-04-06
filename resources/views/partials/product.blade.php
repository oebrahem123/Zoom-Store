{{-- <style>
    .slick-slide {
        height: auto;
    }
</style>
<section class="sec-product bg0 p-t-100 p-b-50">
    <div class="container">
        <div class="p-b-32">
            <h3 class="ltext-105 cl5 txt-center respon1">
                Store Overview
            </h3>
        </div>

        <!-- Tab01 -->
        <div class="tab01">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item p-b-10">
                    <a class="nav-link active" data-toggle="tab" href="#best-seller" role="tab" aria-expanded="true">
                        الأكثر مبيعاً
                    </a>
                </li>
                <li class="nav-item p-b-10">
                    <a class="nav-link" data-toggle="tab" href="#featured" role="tab" aria-expanded="false">
                        مميزة
                    </a>
                </li>
                <li class="nav-item p-b-10">
                    <a class="nav-link" data-toggle="tab" href="#sale" role="tab" aria-expanded="false">
                        عروض وتخفيضات
                    </a>
                </li>
                <li class="nav-item p-b-10">
                    <a class="nav-link" data-toggle="tab" href="#top-rate" role="tab" aria-expanded="false">
                        أعلى تقييم
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content p-t-50">
                <!-- Best Seller Tab -->
                <div class="tab-pane fade active show" id="best-seller" role="tabpanel">
                    <div class="wrap-slick2">
                        <div class="slick2">
                            @foreach($bestSeller->chunk(4) as $chunk)
                            <div class="item-slick2">
                                @foreach($chunk as $product)
                                <div class="p-l-15 p-r-15 p-t-15 p-b-15" style="display: inline-block; width: 25%;">
                                    @include('partials.product-card', ['product' => $product])
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Featured Tab -->
                <div class="tab-pane fade" id="featured" role="tabpanel">
                    <div class="wrap-slick2">
                        <div class="slick2">
                            @foreach($featured->chunk(4) as $chunk)
                            <div class="item-slick2">
                                @foreach($chunk as $product)
                                <div class="p-l-15 p-r-15 p-t-15 p-b-15" style="display: inline-block; width: 25%;">
                                    @include('partials.product-card', ['product' => $product])
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sale Tab -->
                <div class="tab-pane fade" id="sale" role="tabpanel">
                    <div class="wrap-slick2">
                        <div class="slick2">
                            @foreach($sale->chunk(4) as $chunk)
                            <div class="item-slick2">
                                @foreach($chunk as $product)
                                <div class="p-l-15 p-r-15 p-t-15 p-b-15" style="display: inline-block; width: 25%;">
                                    @include('partials.product-card', ['product' => $product])
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Top Rated Tab -->
                <div class="tab-pane fade" id="top-rate" role="tabpanel">
                    <div class="wrap-slick2">
                        <div class="slick2">
                            @foreach($topRated->chunk(4) as $chunk)
                            <div class="item-slick2">
                                @foreach($chunk as $product)
                                <div class="p-l-15 p-r-15 p-t-15 p-b-15" style="display: inline-block; width: 25%;">
                                    @include('partials.product-card', ['product' => $product])
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}