@extends('layout.app')
@section('meta')
    @php
        $baseImagePath = env('ADMIN_BASE_URL') . '/storage/';
        $firstImage = $thumbnails->first()->image_path ?? 'default.png';
        $fullImageUrl = $baseImagePath . $firstImage;
    @endphp
    <meta property="og:title" content="{{ $product->name_en }}">
    <meta property="og:description" content="{{ strip_tags($product->details) }}">
    <meta property="og:image" content="{{ $fullImageUrl }}">
    <meta property="og:image:secure_url" content="{{ $fullImageUrl }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="product">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endsection

@section('title', 'Khut :: ' . $product->name_en)

@section('content')

<style>
    .btn-outline-secondary{
        font-size:11px; 
        padding:2px 6px; 
        background-color:#fff;
    }

    .barcode_hide{
        display:none;
    }
    /* ফেসবুক বাটন স্টাইল */
    #fbShareBtn {
        height: 38px;
        width: 38px;
        background-color: #424242; /* বক্স কালার */
        border: none;
        border-radius: 0px; /* রেডিয়াস ০ */
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        /* এটি নিশ্চিত করে যে ভেতরের এলিমেন্টটি বক্সকে ফুটো করে দেখাবে */
        isolation: isolate; 
    }

    #fbShareBtn i {
        display: block;
        width: 14px; /* আইকন সাইজ সামান্য ছোট করা হয়েছে ব্যালেন্সের জন্য */
        height: 18px;
        background-color: #fff; /* এই রঙটিই কেটে গিয়ে ট্রান্সপারেন্ট হবে */
        
        /* ফেসবুকের 'f' আইকনের পাথ মাস্ক হিসেবে ব্যবহার */
        -webkit-mask-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path d='M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z'/></svg>");
        mask-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path d='M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z'/></svg>");
        
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-size: contain;
        mask-size: contain;
        -webkit-mask-position: center;
        mask-position: center;

        /* জাদুর লাইন: এটি আইকনকে ট্রান্সপারেন্ট করে পেছনের বডি দেখাবে */
        mix-blend-mode: destination-out;
    }

    /* হোভার ইফেক্ট বন্ধ রাখা হয়েছে আপনার রিকোয়েস্ট অনুযায়ী */
    #fbShareBtn:hover {
        background-color: #424242; 
        cursor: pointer;
    }
</style>

@php
    $baseImagePath = env('ADMIN_BASE_URL') . '/storage/';
    $firstImage = $thumbnails->first()->image_path ?? 'default.png';
@endphp

<div class="container mt-5">
    <div class="row">
        <!-- Right Side: Details (Show first in mobile) -->
        <div class="col-lg-5 order-1 order-lg-2">
            <div class="gallery-container productDetailBradcome">
              

                <!-- Main Image -->
                <div class="img-zoom-container">
                    <img id="mainImage"
                         src="{{ $baseImagePath . $firstImage }}"
                         alt="{{ $product->name_en }}">
                
                    <button class="mobile-zoom-btn" id="openZoom">
                        <i class="fa fa-search-plus"></i>
                    </button>
                </div>
                
                <div id="mobileZoomModal">
                    <span class="closeZoom">&times;</span>
                    <img src="{{ $baseImagePath . $firstImage }}">
                </div>
                                    
                <!-- Thumbnails -->
                <div class="thumbnails">
                    @foreach($thumbnails as $thumb)
                        <img class="thumb {{ $loop->first ? 'active' : '' }}"
                             src="{{ $baseImagePath . $thumb->image_path }}"
                             data-full="{{ $baseImagePath . $thumb->image_path }}"
                             data-color="{{ $thumb->color->color_name ?? '' }}"
                             data-barcode="{{ $thumb->thumb_barcode ?? 'NO_BARCODE' }}"
                             alt="Thumb">
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Left Side: Text, Info, Add to Cart (Show after in mobile) -->
        <div class="col-lg-7 order-2 order-lg-1 productDetailBradcome">
            
              
                <a href="{{ url('/') }}">Home</a>

                    @if($product->mainMenu)
                        /
                        <a href="{{ url('category/' . $product->mainMenu->name) }}">
                            {{ $product->mainMenu->name }}
                        </a>
                    @endif

                    @if($product->subMenu)
                        /
                        <a href="{{ url('subcategory/' . $product->subMenu->id) }}">
                            {{ $product->subMenu->name }}
                        </a>
                    @endif

                    @if($product->childMenu)
                        /
                        <a href="{{ url('childcategory/' . $product->childMenu->id) }}">
                            {{ $product->childMenu->name }}
                        </a>
                    @endif

             / <span>{{ $product->name_en }}</span>
                
            <div class="left-content padding-r">
                <h1 class="mt-4">{{ $product->name_bn }}</h1>
                <h2 class="mt-4">{{ $product->name_en }}</h2>

                <div>
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="text-danger h4">৳{{ number_format($product->sale_price, 0) }}</span>
                        <del class="text-muted">৳{{ number_format($product->price, 0) }}</del>
                    @else
                        <span class="h4">৳{{ number_format($product->price, 0) }}</span>
                    @endif
                    <small>inc. VAT</small>
                </div>

                <div class="col-lg-8">
                    <p class="mt-4">{!! $product->details !!}</p>
                </div>

                @php
                    $isExplore = $product->link_status === 'Explore';
                @endphp

                @if(!$isExplore)
                    <div class="deliveryInfo">
                        <b>Delivery Charge</b>
                        <p>Inside Dhaka: <span>BDT 80.</span></p>
                        <p>Outside Dhaka: <span>BDT 150.</span></p>
                    </div>

                    <div class="deliveryInfo mt-1">
                        <b>Delivery Time</b>
                        <p>Inside Dhaka: <span>3-5 working days.</span></p>
                        <p>Outside Dhaka: <span>5-7 working days.</span></p>
                    </div>
                @endif

                <div class="deliveryInfo mt-4 aboutProduct">
                    <b>Care</b>
                    <p><span>
                    
                    @php
                        $washTexts = [];
                    
                        if($product->statuses->count() > 0) {
                            foreach($product->statuses as $status) {
                                $washTexts[] = $status->name ?? $status->status ?? 'Normal';
                            }
                        }
                    
                        if($product->dryWashes->count() > 0) {
                            foreach($product->dryWashes as $wash) {
                                $washTexts[] = $wash->name ?? $wash->drywash_name ?? 'Dry Wash';
                            }
                        }
                    
                        if($product->irons->count() > 0) {
                            foreach($product->irons as $iron) {
                                $washTexts[] = $iron->name ?? $iron->iron_type ?? 'Iron Medium Temp';
                            }
                        }
                    @endphp
                    
                    @if(count($washTexts))
                        <div class="wash-tags">
                            @foreach($washTexts as $text)
                                <span class="wash-tag">{{ $text }}</span>
                            @endforeach
                        </div>
                    @endif
     
                   
                      
                      
                      
                    </span></p>
                </div>
            </div>

            <div class="product-card mt-3">
                <div class="row">
                    <div class="col-md-6 mt-2">
                        {{-- Color & Size --}}
                        @if($product->thumbnails->count() > 0)
                        <div id="colorContainer">
                            @foreach($product->thumbnails->groupBy('thumb_color') as $colorId => $thumbGroup)
                                @php
                                    $firstThumb = $thumbGroup->first();
                                    $sizes = $firstThumb->options->map(function($opt) use ($firstThumb) {
                                        $sizeValue = $opt->commonSize?->common_size ?? $opt->bodySize?->body_size;
                                        $barcodeValue = $opt->barcode ?: $firstThumb->thumb_barcode ?: 'NO_BARCODE';
                                        return $sizeValue ? ['size'=>$sizeValue,'barcode'=>$barcodeValue] : null;
                                    })->filter()->values();
                                @endphp
                                @if($firstThumb->color)
                                <button class="colorBtn btn btn-outline-secondary m-1"
                                        data-color="{{ $firstThumb->color->color_name }}"
                                        data-img="{{ $baseImagePath . $firstThumb->image_path }}"
                                        data-thumb-barcode="{{ $firstThumb->thumb_barcode ?? 'NO_BARCODE' }}"
                                        data-sizes='@json($sizes)'>
                                    {{ $firstThumb->color->color_name }}
                                </button>
                                @endif
                            @endforeach
                        </div>
                        @endif

                        <div id="sizeContainer" class="mt-2"></div>

                        {{-- Barcode --}}
                        <div class="form-group mt-2 col-lg-12 barcode_hide">
                            <label><strong>Barcode:</strong></label>
                            <span id="barcodeDisplay" class="ml-2 text-primary"
                                data-thumb-barcode="{{ $thumbnails->first()->thumb_barcode ?? 'NO_BARCODE' }}">
                                {{ $thumbnails->first()->thumb_barcode ?? 'NO_BARCODE' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>


               <div class="form-group mt-2 col-lg-12 ">
                    <label><strong>Stock:</strong></label>
                    <span id="stockDisplay" class="ml-2" style="color:#790101!important; font-weight:bold;">
                        Loading...
                    </span>
                </div>

               @php
                    $isExplore = $product->link_status === 'Explore';
                @endphp
                            
                {{-- Add to Cart --}}
                <div class="custom-btn d-flex bd-highlight mt-3 flex-wrap" style="width:73%">
                     @if($isExplore)
                    <div class="bd-highlight flex-grow-1 custom-link mb-2 mb-sm-0 mobdetailbtn">
                        <a class="addToCart btn-block btn-primary addToCartDetail">
                            Available in Show Room
                        </a>
                    </div>
                @else
                    <div class="bd-highlight flex-grow-1 custom-link mb-2 mb-sm-0 mobdetailbtn">
                        <a class="addToCart btn-block btn-primary addToCartDetail" 
                            id="addToCartBtn"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name_en }}"
                            data-price="{{ $product->sale_price ?? $product->price }}"
                            data-tax-status="{{ $product->tax_status }}">
                            Add to Cart
                        </a>
                    </div>
                @endif

                   @if(!$isExplore)
                        <div class="bd-highlight mx-2 mb-2 mb-sm-0">
                            <input type="number" id="quantityInput" value="1" min="1" class="form-control qty-input"/>
                        </div>
                    @endif                     

                    <div class="bd-highlight mb-2 mb-sm-0">
                        <button id="wishBtn" class="wish-btn" 
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name_en }}"
                            data-price="{{ $product->price }}"
                            data-img="{{ $baseImagePath . $product->main_image }}"
                            data-slug="{{ $product->slug }}">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>

                    <div class="bd-highlight mb-2 mb-sm-0 ml-2">
                        <button id="fbShareBtn" title="Share on Facebook">
                            <i></i> </button>
                    </div>
                </div>
               
               <style>
                  
                   .qty-input {
                    width: 70px;          /* Input ছোট হবে */
                    height: 38px;         /* Button height এর সাথে match */
                    text-align: center;
                }
                
                .wish-btn {
                    height: 38px;
                    width: 38px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 1px solid #ddd;
                    background: #fff;
                    border-radius: 6px;
                }
                
                .addToCartDetail {
                    height: 38px;         /* Same height for alignment */
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

               </style>

        </div>
    </div>
</div>




 <!-- ===== Related Products Section Start ===== -->

@php
    // Load catalog once
    $khutCatalog = app(\App\Services\KhutCatalogService::class)->all();
@endphp

<div class="container topGap-1 mt-5">
  <div class="row align-items-center">
    <div class="col-lg-7">
      <h1 class="mb-2 product-title">Related Products</h1>
    </div>
    <div class="col-lg-5 d-flex justify-content-end custom-link">
      <a class="m-1" href="{{ route('category.list', str_replace(' ', '-', $product->mainMenu->name)) }}">
        All Products
      </a>
    </div>
  </div>

  <div class="tab-content mt-4">
    <div class="tab-pane fade show active">
      <div class="swiper mySwiperShoes">
        <div class="swiper-wrapper">

          @forelse($relatedProducts as $related)

           @php
                    $primaryBarcode = null;

                    $firstThumb = $related->thumbnails->first();
                    if ($firstThumb && $firstThumb->thumb_barcode) {
                        $primaryBarcode = trim((string) $firstThumb->thumb_barcode);
                    }

                    if (!$primaryBarcode) {
                        $mainSku = trim((string) ($related->product_barcode ?? ''));
                        if ($mainSku !== '') {
                            $primaryBarcode = $mainSku;
                        }
                    }

                    $apiStock = $primaryBarcode ? (int)($khutCatalog[$primaryBarcode]['stock'] ?? 0) : 0;
                    $inStock = $apiStock > 0;
                @endphp

                {{-- 🔥 এই লাইনটাই main fix --}}
                @if(!$inStock)
                    @continue
                @endif
            

            <!-- Each Product Card -->
            <div class="swiper-slide p-2">
              <div class="product-card hozoboro-text productListName productportraitListDiv padding-custom-mobile">

                <!-- Product Image -->
                <a href="{{ route('product.details', $related->slug) }}">
                  <img 
                    src="{{ $baseImagePath . $related->main_image }}" 
                    class="img-fluid img-list-resize" 
                    alt="{{ $related->name_en }}">

                  @if(!$inStock)
                    <div class="sold-out">Sold Out</div>
                  @endif
                </a>

                <!-- Product Name -->
                <div class="nameProduct">
                  <p>{{ $related->name_en }}</p>

                  @if($inStock)
                    <h4>
                      BDT {{ number_format($related->price, 0) }} VAT &nbsp;&nbsp;
                      <span>
                        {{ $related->sale_price ? 'BDT '.number_format($related->sale_price,0) : '' }}
                      </span>
                    </h4>
                  @endif
                </div>

                <!-- Buttons -->
                <div class="custom-link d-flex align-items-center justify-content-between">
                    @if($inStock)
                        {{-- স্টক থাকলে আগের লজিক --}}
                        @if($related->link_status == 'Add to Cart')
                            <a class="addToCart"
                                data-id="{{ $related->id }}"
                                data-name="{{ $related->name_en }}"
                                data-price="{{ $related->price }}"
                                data-img="{{ $baseImagePath . $related->main_image }}"
                                data-product-barcode="{{ $related->product_barcode }}">
                                Add to Cart
                            </a>
                        @elseif($related->link_status == 'Read More')
                            <a href="{{ route('product.details', $related->slug) }}">
                                Select Option
                            </a>
                         @elseif($related->link_status == 'Explore')
                            <a href="{{ route('product.details', $related->slug) }}">
                                Explore
                            </a>    
                        @endif
                    @else
                        {{-- স্টক না থাকলে (Sold Out হলে) শুধু View বাটন দেখাবে --}}
                        <a href="{{ route('product.details', $related->slug) }}" class="btn-view">
                            View
                        </a>
                    @endif
                
                    <button class="wish-btn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>

              </div>
            </div>

          @empty
            <div class="swiper-slide text-center p-5">
              <p>No related products found.</p>
            </div>
          @endforelse

        </div>

        <!-- Swiper navigation -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
      </div>
    </div>
  </div>
</div>

<!-- ===== Related Products Section End ===== -->



@endsection

@section('script')

<script src="{{asset('/js/jquery-3.6.0.min.js')}}" ></script>
<script src="{{asset('/js/popper.js')}}"></script>
<script src="{{asset('/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('/js/swiper-bundle.min.js')}}"></script>
<script src="{{asset('/js/owl-carousel.js')}}"></script>
<script src="{{asset('/js/custom.js')}}"></script>




<script>
document.addEventListener("DOMContentLoaded", () => {

    const mainImg = document.getElementById("mainImage");
    const colorContainer = document.getElementById("colorContainer");
    const sizeContainer = document.getElementById("sizeContainer");
    const addToCartBtn = document.getElementById("addToCartBtn");
    const barcodeDisplay = document.getElementById("barcodeDisplay");
    const stockDisplay = document.getElementById("stockDisplay");
    const quantityInput = document.getElementById("quantityInput");

    let selectedColor = null;
    let selectedSize = null;
    let selectedBarcode = barcodeDisplay?.dataset.thumbBarcode || null;

    // ===================== STOCK =====================
    function showNotAvailable() {
        stockDisplay.innerText = 'Not available';
        stockDisplay.classList.remove('text-success');
        stockDisplay.classList.add('text-danger');
        addToCartBtn?.setAttribute('disabled', true);
    }

    function showStock(qty) {
        stockDisplay.innerText = qty;
        stockDisplay.classList.remove('text-danger');
        stockDisplay.classList.add('text-success');
        addToCartBtn?.removeAttribute('disabled');
    }

    function fetchStock(barcode) {
        if (!barcode || barcode === 'NO_BARCODE') {
            showNotAvailable();
            return;
        }

        const stockUrl = "{{ url('/stock') }}/" + barcode;

        fetch(stockUrl)
            .then(res => res.json())
            .then(res => {
                const data = res?.data ?? {};
                const inCatalog = res?.success && data.in_catalog === true;
                const qty = inCatalog ? (Number(data.store_stock) || 0) : 0;
                if (!inCatalog || qty <= 0) {
                    showNotAvailable();
                } else {
                    showStock(qty);
                }
            })
            .catch(() => showNotAvailable());
    }

    // ===================== BARCODE =====================
    function updateBarcodeDisplay() {
        if (barcodeDisplay) {
            barcodeDisplay.textContent = selectedBarcode || 'NO_BARCODE';
        }
    }

    // ===================== VALIDATION =====================
    function validateInputs() {
        const hasColors = colorContainer && colorContainer.querySelectorAll(".colorBtn").length > 0;
        const hasSizes = sizeContainer && sizeContainer.querySelectorAll(".sizeBtn").length > 0;

        const colorOk = !hasColors || selectedColor !== null;
        const sizeOk = !hasSizes || selectedSize !== null;

        if (addToCartBtn) {
            if (colorOk && sizeOk && stockDisplay.innerText !== 'Not available') {
                addToCartBtn.removeAttribute("disabled");
            } else {
                addToCartBtn.setAttribute("disabled", true);
            }
        }
    }

    // ===================== COLOR CLICK =====================
    colorContainer?.querySelectorAll(".colorBtn").forEach(btn => {
        btn.addEventListener("click", () => {
            colorContainer.querySelectorAll(".colorBtn").forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            selectedColor = btn.dataset.color;
            mainImg.src = btn.dataset.img || mainImg.src;

            const sizes = JSON.parse(btn.dataset.sizes || "[]");
            const thumbBarcode = btn.dataset.thumbBarcode || 'NO_BARCODE';

            sizeContainer.innerHTML = "";
            selectedSize = null;

            if (sizes.length === 0) {
                selectedBarcode = thumbBarcode;
                updateBarcodeDisplay();
                fetchStock(selectedBarcode);
            } else {
                selectedBarcode = null;
                stockDisplay.innerText = '--';
                stockDisplay.classList.remove('text-success', 'text-danger');
                stockDisplay.classList.add('text-primary');
            }

            sizes.forEach(opt => {
                const sizeBtn = document.createElement("button");
                sizeBtn.textContent = opt.size;
                sizeBtn.className = "sizeBtn btn btn-outline-secondary m-1";
                sizeBtn.dataset.barcode = opt.barcode;

                sizeBtn.addEventListener("click", () => {
                    sizeContainer.querySelectorAll(".sizeBtn").forEach(b => b.classList.remove("active"));
                    sizeBtn.classList.add("active");

                    selectedSize = opt.size;
                    selectedBarcode = opt.barcode || thumbBarcode;

                    updateBarcodeDisplay();
                    fetchStock(selectedBarcode);
                    validateInputs();
                });

                sizeContainer.appendChild(sizeBtn);
            });

            updateBarcodeDisplay();
            validateInputs();
        });
    });

    // ===================== THUMBNAILS =====================
    document.querySelectorAll(".thumb").forEach(thumb => {
        thumb.addEventListener("click", () => {
            mainImg.src = thumb.dataset.full;
            document.querySelectorAll(".thumb").forEach(t => t.classList.remove("active"));
            thumb.classList.add("active");
        });
    });

    // ===================== AUTO PICK FIRST COLOR =====================
    const firstColorBtn = colorContainer?.querySelector(".colorBtn");
    if (firstColorBtn) firstColorBtn.click();
    else {
        // Simple product → fetch stock from API if barcode exists, else show unavailable
        if (selectedBarcode && selectedBarcode !== 'NO_BARCODE') {
            fetchStock(selectedBarcode);
        } else {
            showNotAvailable();
        }
    }

    // ===================== ADD TO CART =====================
    addToCartBtn?.addEventListener("click", (e) => {
        e.stopPropagation();

        const qty = parseInt(quantityInput?.value) || 1;
        const productData = {
            id: addToCartBtn.dataset.id,
            name: addToCartBtn.dataset.name,
            img: mainImg?.src || '/images/no-image.png',
            price: Number(addToCartBtn.dataset.price) || 0,
            qty,
            size: selectedSize || null,
            color: selectedColor || null,
            barcode: selectedBarcode || 'NO_BARCODE',
            tax_status: addToCartBtn.dataset.taxStatus || 'Standard'
        };

        let cart = JSON.parse(localStorage.getItem("cart")) || [];

        const existing = cart.find(item =>
            item.id == productData.id &&
            item.size == productData.size &&
            item.color == productData.color
        );

        if (existing) {
            existing.qty += qty;
            existing.barcode = productData.barcode;
        } else {
            cart.push(productData);
        }

        localStorage.setItem("cart", JSON.stringify(cart));

        if (typeof renderCart === "function") renderCart();
        if (typeof updateCartCounts === "function") updateCartCounts();

        const cartSidebar = document.getElementById("cartSidebar");
        const cartOverlay = document.getElementById("cartOverlay");
        if (cartSidebar && cartOverlay) {
            cartSidebar.classList.add("active");
            cartOverlay.classList.add("active");
        }

        showCartToast("Added to cart successfully!");
    });

    function showCartToast(message) {
        let toastEl = document.getElementById("cartToast");
        if (!toastEl) {
            toastEl = document.createElement("div");
            toastEl.id = "cartToast";
            toastEl.style.position = "fixed";
            toastEl.style.bottom = "20px";
            toastEl.style.right = "20px";
            toastEl.style.background = "#333";
            toastEl.style.color = "#fff";
            toastEl.style.padding = "10px 20px";
            toastEl.style.borderRadius = "5px";
            toastEl.style.zIndex = 9999;
            document.body.appendChild(toastEl);
        }
        toastEl.textContent = message;
        toastEl.style.display = "block";
        setTimeout(() => { toastEl.style.display = "none"; }, 2000);
    }

    // ===================== IMAGE ZOOM =====================
   $(document).ready(function () {

    const zoomScale = 2.5;
    const imgContainer = $(".img-zoom-container");
    const img = $(".img-zoom-container img");

    let mobileZoomed = false;

    // ===================== DESKTOP ZOOM (UNCHANGED) =====================
    if (window.innerWidth > 768) {

        imgContainer.mousemove(function (e) {
            let offset = $(this).offset();
            let x = e.pageX - offset.left;
            let y = e.pageY - offset.top;

            let width = $(this).width();
            let height = $(this).height();

            let xPercent = (x / width) * 100;
            let yPercent = (y / height) * 100;

            img.css({
                "transform": `scale(${zoomScale})`,
                "transform-origin": `${xPercent}% ${yPercent}%`
            });
        });

        imgContainer.mouseleave(function () {
            img.css({
                "transform": "scale(1)",
                "transform-origin": "center center"
            });
        });
    }

    // ===================== MOBILE ZOOM + DRAG =====================
if (window.innerWidth <= 768) {

    let zoomed = false;
    let startX = 0;
    let startY = 0;
    let moveX = 0;
    let moveY = 0;

    const img = $(".img-zoom-container img");
    const container = $(".img-zoom-container");

    const ZOOM = 2.5;

    function setTransform() {
        img.css({
            "transform": `scale(${ZOOM}) translate(${moveX}px, ${moveY}px)`,
            "transform-origin": "center center",
            "transition": zoomed ? "none" : "transform 0.25s ease"
        });
    }

    // ===================== TAP TOGGLE ZOOM =====================
    container.on("click", function () {

        // prevent accidental click after drag
        if (!zoomed && (Math.abs(moveX) > 10 || Math.abs(moveY) > 10)) return;

        if (!zoomed) {
            zoomed = true;
            moveX = 0;
            moveY = 0;
            setTransform();
        } else {
            zoomed = false;
            moveX = 0;
            moveY = 0;

            img.css({
                "transform": "scale(1) translate(0px, 0px)",
                "transition": "transform 0.3s ease"
            });
        }
    });

    // ===================== TOUCH START =====================
    container.on("touchstart", function (e) {
        if (!zoomed) return;

        const touch = e.originalEvent.touches[0];
        startX = touch.pageX - moveX;
        startY = touch.pageY - moveY;
    });

    // ===================== TOUCH MOVE =====================
    container.on("touchmove", function (e) {
        if (!zoomed) return;

        e.preventDefault(); // stop page scroll

        const touch = e.originalEvent.touches[0];

        moveX = touch.pageX - startX;
        moveY = touch.pageY - startY;

        // ===================== BOUNDARY FIX =====================
        const maxMove = 150; // limit so image never goes outside too far

        if (moveX > maxMove) moveX = maxMove;
        if (moveX < -maxMove) moveX = -maxMove;
        if (moveY > maxMove) moveY = maxMove;
        if (moveY < -maxMove) moveY = -maxMove;

        setTransform();
    });

    // ===================== TOUCH END =====================
    container.on("touchend", function () {
        if (!zoomed) return;

        // small snap back effect (optional)
        img.css({
            "transition": "transform 0.2s ease"
        });
    });
}

});
    
    
    
    document.getElementById("fbShareBtn")?.addEventListener("click", function() {
        const productUrl = window.location.href;
        const fbUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(productUrl)}`;
        window.open(fbUrl, '_blank', 'width=600,height=400');
    });

});
</script>


<script>
document.addEventListener("DOMContentLoaded", () => {
    if (window.innerWidth > 768) return;

    const btn = document.getElementById("openZoom");
    const modal = document.getElementById("mobileZoomModal");
    const close = document.querySelector(".closeZoom");

    btn.addEventListener("click", () => {
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";
    });

    close.addEventListener("click", () => {
        modal.style.display = "none";
        document.body.style.overflow = "";
    });

    modal.addEventListener("click", e => {
        if(e.target === modal){
            modal.style.display = "none";
            document.body.style.overflow = "";
        }
    });
});
</script>




@endsection