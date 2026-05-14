@php
    // API catalog
    $khutCatalog = app(\App\Services\KhutCatalogService::class)->all();

    // Normalize barcode keys
    $khutCatalogNorm = [];
    foreach($khutCatalog as $barcode => $item) {
        $khutCatalogNorm[ltrim((string)$barcode,'0')] = $item;
    }

    // Filter products: only keep those with a barcode or at least one thumbnail barcode
    $productsWithBarcode = $products->getCollection()->filter(function($product) {
        return $product->product_barcode || $product->thumbnails->first()?->thumb_barcode;
    });

    // Sort: in-stock first
    $sorted = $productsWithBarcode->sortBy(function ($product) use ($khutCatalogNorm) {
        $primaryBarcode = $product->thumbnails->first()?->thumb_barcode 
                        ?: $product->product_barcode 
                        ?: null;
        $primaryBarcodeNorm = $primaryBarcode ? ltrim($primaryBarcode,'0') : null;
        $apiStock = $primaryBarcodeNorm ? (int)($khutCatalogNorm[$primaryBarcodeNorm]['stock'] ?? 0) : 0;
        return $apiStock > 0 ? 0 : 1;
    });

    $products->setCollection($sorted);
@endphp

<style>
.padding-custom-mobile {
    padding-left: 12px !important;
    padding-right: 0px !important;
}


.image-wrapper {
    position: relative;
}

.zoom-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.65);
    color: #fff;
    padding: 7px 9px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 9999;
    display: block; /* mobile fix */
    -webkit-tap-highlight-color: transparent;
    display: none!important;
}

.zoom-icon .fas {
    color: #FFF!important;
}
.image-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    z-index: 999999;
    justify-content: center;
    align-items: center;
}

.image-modal img {
    max-width: 90%;
    max-height: 90%;
}

.close-btn {
    position: absolute;
    top: 20px;
    right: 25px;
    font-size: 35px;
    color: #fff;
    cursor: pointer;
}

.image-wrapper:hover .zoom-icon {
    display: block;
}


</style>

<div class="row padding-custom-mobile">
    
    
@foreach($products as $product)
    @php
        $allBarcodes = [];

        // Main product barcode
        if (!empty($product->product_barcode)) {
            $allBarcodes[] = $product->product_barcode;
        }

        // Thumbnail barcodes
        foreach ($product->thumbnails as $thumb) {
            if (!empty($thumb->thumb_barcode)) {
                $allBarcodes[] = $thumb->thumb_barcode;
            }
        }

        // Option barcodes
        foreach ($product->options as $option) {
            if (!empty($option->barcode)) {
                $allBarcodes[] = $option->barcode;
            }
        }

        // Duplicate remove
        $allBarcodes = array_unique($allBarcodes);

        // Default sold out
        $inStock = false;

        foreach ($allBarcodes as $barcode) {

            $barcodeNorm = ltrim($barcode, '0');

            $stock = (int)($khutCatalogNorm[$barcodeNorm]['stock'] ?? 0);

            if ($stock > 0) {
                $inStock = true;
                break;
            }
        }
    @endphp
  @if(!$inStock && request()->get('page', 1) == 1)
        @continue
    @endif

    <div class="col-lg-3 col-md-6 col-6 hozoboro-text productListName productportraitListDiv  product-card"
         data-price="{{ $product->price }}"
         data-sku="{{ $product->product_barcode }}">

       {{-- IMAGE --}}
        <div class="image-wrapper position-relative">

            {{-- IMAGE (details link only here) --}}
            <a href="{{ route('product.details', $product->slug) }}" class="img-link">
                <img src="{{ $baseImagePath . $product->main_image }}"
                     class="product-image img-fluid img-alllist-resize">
            </a>
        
             {{-- ZOOM ICON (separate click target) --}}
            <div class="zoom-icon" data-img="{{ $baseImagePath . $product->main_image }}">
                <i class="fas fa-search-plus"></i>
            </div>
                
            @if(!$inStock)
                <div class="sold-out">Sold Out</div>
            @endif
        
        </div>

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

                {{-- যদি OUT OF STOCK হয় --}}
                @if(!$inStock)
                    <a href="{{ route('product.details', $product->slug) }}"
                       style="padding:4px 18px">
                       View
                    </a>
            
                {{-- যদি IN STOCK হয় --}}
                @else
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

                    @elseif($product->link_status == 'Explore')
                        <a href="{{ route('product.details', $product->slug) }}"
                           style="padding:4px 18px">
                           Explore
                        </a>    
                    @endif
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