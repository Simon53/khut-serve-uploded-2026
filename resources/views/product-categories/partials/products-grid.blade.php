@php

    $khutCatalog = app(\App\Services\KhutCatalogService::class)->all();

    /*
    |--------------------------------------------------------------------------
    | Normalize Catalog
    |--------------------------------------------------------------------------
    */
    $khutCatalogNorm = [];

    foreach($khutCatalog as $barcode => $item) {

        $khutCatalogNorm[ltrim((string)$barcode,'0')] = $item;

    }


    /*
    |--------------------------------------------------------------------------
    | Get All Products Collection
    |--------------------------------------------------------------------------
    */
    //$allProducts = $products->getCollection();
    $allProducts = $products;


    /*
    |--------------------------------------------------------------------------
    | Remove Product Without Barcode
    |--------------------------------------------------------------------------
    */
    $allProducts = $allProducts->filter(function($product){

        return !empty($product->product_barcode);

    });


    /*
    |--------------------------------------------------------------------------
    | Sort Products
    | 1. In Stock First
    | 2. Sold Out Last
    | 3. Latest Product First
    |--------------------------------------------------------------------------
    */
    $sortedProducts = $allProducts->sortByDesc(function ($product) use ($khutCatalogNorm) {

        $allBarcodes = [];

        // Main Product Barcode
        if (!empty($product->product_barcode)) {

            $allBarcodes[] = $product->product_barcode;

        }

        // Thumbnail Barcode
        foreach ($product->thumbnails as $thumb) {

            if (!empty($thumb->thumb_barcode)) {

                $allBarcodes[] = $thumb->thumb_barcode;

            }
        }

        // Option Barcode
        foreach ($product->options as $option) {

            if (!empty($option->barcode)) {

                $allBarcodes[] = $option->barcode;

            }
        }

        // Unique Barcode
        $allBarcodes = array_unique($allBarcodes);

        // Default
        $inStock = false;

        foreach ($allBarcodes as $barcode) {

            $barcodeNorm = ltrim($barcode, '0');

            $stock = (int)($khutCatalogNorm[$barcodeNorm]['stock'] ?? 0);

            if ($stock > 0) {

                $inStock = true;
                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Priority
        |--------------------------------------------------------------------------
        |
        | In Stock Product => Big Number
        | Sold Out Product => Small Number
        |
        */

        return ($inStock ? 1000000000 : 0) + $product->id;

    });


    /*
    |--------------------------------------------------------------------------
    | Manual Pagination After Sorting
    |--------------------------------------------------------------------------
    */
    $currentPage = request()->get('page', 1);

    $perPage = 40;

    $pagedProducts = new \Illuminate\Pagination\LengthAwarePaginator(

        $sortedProducts->forPage($currentPage, $perPage)->values(),

        $sortedProducts->count(),

        $perPage,

        $currentPage,

        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]

    );

@endphp




<div class="row padding-custom-mobile">


@foreach($pagedProducts as $product)

   @php

        $allBarcodes = [];

        // Main Product Barcode
        if (!empty($product->product_barcode)) {

            $allBarcodes[] = $product->product_barcode;

        }

        // Thumbnail Barcode
        foreach ($product->thumbnails as $thumb) {

            if (!empty($thumb->thumb_barcode)) {

                $allBarcodes[] = $thumb->thumb_barcode;

            }
        }

        // Option Barcode
        foreach ($product->options as $option) {

            if (!empty($option->barcode)) {

                $allBarcodes[] = $option->barcode;

            }
        }

        // Remove Duplicate
        $allBarcodes = array_unique($allBarcodes);

        // Default
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


    <div class="col-lg-3 col-md-6 col-6 hozoboro-text productListName productportraitListDiv product-card"
         data-price="{{ $product->price }}"
         data-sku="{{ $product->product_barcode }}">


        {{-- IMAGE --}}
        <div class="image-wrapper position-relative">


            {{-- IMAGE --}}
            <a href="{{ route('product.details', $product->slug) }}" class="img-link">

                <img src="{{ $baseImagePath . $product->main_image }}"
                     class="product-image img-fluid img-alllist-resize">

            </a>


            {{-- SOLD OUT --}}
            @if(!$inStock)

                <div class="sold-out">
                    Sold Out
                </div>

            @endif

        </div>


        {{-- PRODUCT NAME --}}
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


            {{-- OUT OF STOCK --}}
            @if(!$inStock)

                <a href="{{ route('product.details', $product->slug) }}"
                   style="padding:4px 18px">

                    View

                </a>


            {{-- IN STOCK --}}
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


            {{-- WISHLIST --}}
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

    {{ $pagedProducts->links('pagination::bootstrap-4') }}

</div>