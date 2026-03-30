@php
    $khutCatalog = app(\App\Services\KhutCatalogService::class)->all();

    // Sort ONLY collection, not whole paginator
    $sorted = $products->getCollection()->sortBy(function ($product) use ($khutCatalog) {

        $primaryBarcode = null;

        $firstThumb = $product->thumbnails->first();
        if ($firstThumb && $firstThumb->thumb_barcode) {
            $primaryBarcode = trim((string) $firstThumb->thumb_barcode);
        }

        if (!$primaryBarcode) {
            $mainSku = trim((string) ($product->product_barcode ?? ''));
            if ($mainSku !== '') {
                $primaryBarcode = $mainSku;
            }
        }

        $apiStock = $primaryBarcode ? (int)($khutCatalog[$primaryBarcode]['stock'] ?? 0) : 0;

        return $apiStock > 0 ? 0 : 1; // ইনস্টক আগে
    });

    // Replace collection inside paginator
    $products->setCollection($sorted);
@endphp

<style>
    .disabled-link {
        pointer-events: none;
        cursor: not-allowed;
    }

    .img-disabled {
        opacity: 0.5;
    }

    .disabled-btn {
        pointer-events: none;
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<div class="row">
@foreach($products as $product)
    @php
        // Primary barcode logic
        $primaryBarcode = null;

        $firstThumb = $product->thumbnails->first();
        if ($firstThumb && $firstThumb->thumb_barcode) {
            $primaryBarcode = trim((string) $firstThumb->thumb_barcode);
        }

        if (!$primaryBarcode) {
            $mainSku = trim((string) ($product->product_barcode ?? ''));
            if ($mainSku !== '') {
                $primaryBarcode = $mainSku;
            }
        }

        $apiStock = $primaryBarcode ? (int)($khutCatalog[$primaryBarcode]['stock'] ?? 0) : 0;
        $inStock = $apiStock > 0;
    @endphp

    <div class="col-lg-3 col-md-6 col-6 hozoboro-text productListName productportraitListDiv padding-custom-mobile product-card"
         data-price="{{ $product->price }}"
         data-sku="{{ $product->product_barcode }}">

        {{-- IMAGE --}}
        <a href="{{ $inStock ? route('product.details', $product->slug) : 'javascript:void(0)' }}"
           class="{{ !$inStock ? 'disabled-link' : '' }}">

            <img src="{{ $baseImagePath . $product->main_image }}"
                 alt="{{ $product->name_en }}"
                 class="img-fluid img-alllist-resize {{ !$inStock ? 'img-disabled' : '' }}">

            @if(!$inStock)
                <div class="sold-out">Sold Out</div>
            @endif
        </a>

        {{-- NAME --}}
        <div class="nameProduct">
            <a href="{{ $inStock ? route('product.details', $product->slug) : 'javascript:void(0)' }}"
               class="{{ !$inStock ? 'disabled-link' : '' }}">
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
                <a class="addToCart {{ !$inStock ? 'disabled-btn' : '' }}"
                   data-id="{{ $product->id }}"
                   data-name="{{ $product->name_en }}"
                   data-price="{{ $product->price }}"
                   data-img="{{ $baseImagePath . $product->main_image }}"
                   data-product-barcode="{{ $product->product_barcode }}"
                   style="padding:4px 18px"
                   {{ !$inStock ? 'onclick=return false;' : '' }}>
                   Add to Cart
                </a>

            @elseif($product->link_status == 'Read More')
                <a href="{{ $inStock ? route('product.details', $product->slug) : 'javascript:void(0)' }}"
                   class="{{ !$inStock ? 'disabled-btn' : '' }}"
                   style="padding:4px 18px">
                   Select Option
                </a>
            @endif

            <button class="wish-btn {{ !$inStock ? 'disabled-btn' : '' }}"
                data-id="{{ $product->id }}"
                data-name="{{ $product->name_en }}"
                data-price="{{ $product->price }}"
                data-img="{{ $baseImagePath . $product->main_image }}"
                data-slug="{{ $product->slug }}"
                {{ !$inStock ? 'disabled' : '' }}>
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