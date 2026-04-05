@php
    $khutCatalog = app(\App\Services\KhutCatalogService::class)->all();

    // Sort ONLY collection: In-stock products first
    $sorted = $products->getCollection()->sortBy(function ($product) use ($khutCatalog) {
        $primaryBarcode = $product->thumbnails->first()?->thumb_barcode ?: $product->product_barcode ?: null;
        $apiStock = $primaryBarcode ? (int)($khutCatalog[$primaryBarcode]['stock'] ?? 0) : 0;
        return $apiStock > 0 ? 0 : 1;
    });

    $products->setCollection($sorted);
@endphp

<div class="row">
@foreach($products as $product)
    @php
        $primaryBarcode = $product->thumbnails->first()?->thumb_barcode ?: $product->product_barcode ?: null;
        $apiStock = $primaryBarcode ? (int)($khutCatalog[$primaryBarcode]['stock'] ?? 0) : 0;
        $inStock = $apiStock > 0;
    @endphp

    <div class="col-lg-3 col-md-6 col-6 hozoboro-text productListName productportraitListDiv padding-custom-mobile product-card"
         data-price="{{ $product->price }}"
         data-sku="{{ $product->product_barcode }}">

        {{-- IMAGE --}}
        <a href="{{ route('product.details', $product->slug) }}">
            <img src="{{ $baseImagePath . $product->main_image }}"
                 alt="{{ $product->name_en }}"
                 class="img-fluid img-alllist-resize">

            @if(!$inStock)
                <div class="sold-out">Sold Out</div>
            @endif
        </a>

        {{-- NAME --}}
        <div class="nameProduct">
            <a href="{{ route('product.details', $product->slug) }}">
                <p>{{ $product->name_en }}</p>
            </a>

            <h4>
                BDT {{ $product->price }} .VAT &nbsp;&nbsp;
                <span>{{ $product->sale_price }}</span>
            </h4>
        </div>

        {{-- BUTTON --}}
        <div class="custom-link d-flex align-items-center justify-content-between">

            @if($product->link_status == 'Add to Cart')
                <a class="addToCart"
                   data-id="{{ $product->id }}"
                   data-name="{{ $product->name_en }}"
                   data-price="{{ $product->price }}"
                   data-img="{{ $baseImagePath . $product->main_image }}"
                   data-product-barcode="{{ $product->product_barcode }}"
                   style="padding:4px 18px">
                   Add to Cart
                </a>

            @elseif($product->link_status == 'Read More')
                <a href="{{ route('product.details', $product->slug) }}"
                   style="padding:4px 18px">
                   Select Option
                </a>
            @endif

            <button class="wish-btn"
                data-id="{{ $product->id }}"
                data-name="{{ $product->name_en }}"
                data-price="{{ $product->price }}"
                data-img="{{ $baseImagePath . $product->main_image }}"
                data-slug="{{ $product->slug }}">
                <i class="far fa-heart"></i>
            </button>

        </div>
    </div>
@endforeach
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-center mt-4">
    {{ $products->links('pagination::bootstrap-4') }}
</div>