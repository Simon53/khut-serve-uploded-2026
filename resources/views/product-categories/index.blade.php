@extends('layout.app')
@section('title', $category->name ?? 'Products')
@section('content')

<style>
    .innerBanner h1 {
        display: none;
    }
    .bradcum-category {
        margin-top: -10px;
    }
</style>

@php

    $baseImagePath = env('ADMIN_BASE_URL') . '/storage/';
@endphp

{{-- ========================================================= --}}
{{-- BANNER --}}
{{-- Search Result হলে Banner দেখাবে না --}}
{{-- ========================================================= --}}

@if(!request()->is('search-results'))

    <div class="innerBanner">

        @php

            $adminBaseUrl = env('ADMIN_BASE_URL');

            if (!empty($category->banner)) {

                $bannerUrl = $adminBaseUrl . $category->banner;

            } elseif (!empty($banner->banner_image)) {

                $bannerUrl = $adminBaseUrl . $banner->banner_image;

            } else {

                $bannerUrl = asset(
                    'assets/images/product_banner.jpg'
                );

            }

            $bannerTitle =
                $category->title
                ?? $banner->title
                ?? $category->name
                ?? 'Products';

        @endphp


        <img
            src="{{ $bannerUrl }}"
            class="img-fluid img-resize-banner banner-animate"
            alt="{{ $bannerTitle }}"
        >


        <div class="banner-overlay"></div>


        <div class="d-flex justify-content-center position-absolute w-100 top-50 start-50 translate-middle">

            <h1 class="text-white">
                {{ $bannerTitle }}
            </h1>

        </div>


        <div class="ribon"></div>


        <div class="container mt-2">

            <div class="bradcum-category">

                <a href="{{ url('/') }}">
                    Home
                </a>


                @if(!empty($mainMenu))

                    /

                    <a href="{{ url('category/'.$mainMenu->name) }}">
                        {{ $mainMenu->name }}
                    </a>

                @endif


                @if(!empty($subMenu))

                    /

                    <a href="{{ url('subcategory/'.$subMenu->id) }}">
                        {{ $subMenu->name }}
                    </a>

                @endif


                @if(!empty($childMenu))

                    /

                    <a href="{{ url('childcategory/'.$childMenu->id) }}">
                        {{ $childMenu->name }}
                    </a>

                @elseif(!empty($category))

                    /

                    <span>
                        {{ $category->name }}
                    </span>

                @endif

            </div>

        </div>

    </div>

@endif

{{-- ========================================================= --}}
{{-- END BANNER --}}
{{-- ========================================================= --}}






<div class="container my-4">
    <!-- Filter Button (Mobile Only) -->
    <button class="d-lg-none mb-3 filterBtn" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#offcanvasTabs"
            aria-controls="offcanvasTabs" style="color:#FFF">
        ☰ Filter
    </button>

    <div class="row topGap-1">
        <!-- Sidebar Menu -->
        <div class="col-lg-3 d-none d-lg-block">
            <!-- Desktop Price Range -->
            <div class="mb-4">
                <!--h6 class="fw-bold mb-2">Price Range</h6-->
                <input type="range" class="form-range" min="60" max="25000" step="100" id="priceRangeDesktop">
                <div class="d-flex justify-content-between">
                    <small>৳60</small>
                    <small id="priceValueDesktop">৳10,000</small>
                    <small>৳25,000</small>
                </div>
            </div>

            <!--h2 class="product-title mb-3">Product Category</h2-->

            <!-- Vertical Categorxb fy Tree -->
            <ul class="nav flex-column nav-pills">
                @foreach($mainMenus as $main)
                   @php
                    // URL same থাকবে
                    $mainUrlName = $main->name;

                    if (strtolower($main->name) === 'new arrivals') {
                        $mainUrlName = 'New-Arrivals';
                    }

                    // 👉 শুধু display ঠিক করার জন্য
                    $displayName = $main->name;

                    if ($main->name === 'Gifts&Crafts') {
                        $displayName = 'Gifts & Crafts';
                    }

                    if ($main->name === 'Hojoborolo') {
                        $displayName = 'Ho-Jo-Bo-Ro-Lo';
                    }
                @endphp

                    <li class="nav-item" role="presentation">
                        <a class="nav-link main-menu-tab
                        {{ request()->is('category/'.$mainUrlName) ? 'active-menu' : '' }}"
                        href="{{ url('category/'.$mainUrlName) }}">
                            @if($main->name === 'Gifts&Crafts')
                                Gifts & Crafts
                            @elseif($main->name === 'Hojoborolo')
                                Ho-Jo-Bo-Ro-Lo
                            @else
                                {{ $main->name }}
                            @endif
                        </a>

                        @if($main->subMenus->count())
                            <ul class="nav flex-column ms-3 mt-1">
                                @foreach($main->subMenus as $sub)
                                    <li class="nav-item">
                                        <a class="nav-link sub-menu-tab
                                        {{ request()->is('subcategory/'.$sub->id) ? 'active-menu' : '' }}"
                                        href="{{ url('subcategory/'.$sub->id) }}">
                                            {{ $sub->name }}
                                        </a>

                                        @if($sub->childMenus->count())
                                            <ul class="nav flex-column ms-3 mt-1">
                                                @foreach($sub->childMenus as $child)
                                                    <li class="nav-item">
                                                        <a class="nav-link child-menu-tab
                                                        {{ request()->is('childcategory/'.$child->id) ? 'active-menu' : '' }}"
                                                        href="{{ url('childcategory/'.$child->id) }}">
                                                            {{ $child->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach

            </ul> 
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9" style="padding-right: 0;">
            
            <div class="row" id="product-list">
               @include('product-categories.partials.products-grid', [
                    'products' => $products,
                    'baseImagePath' => $baseImagePath,
                    'stocks' => $stocks ?? []
                ])
            </div>
        </div>
    </div>

    <!-- Offcanvas (Mobile Filter) -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasTabs">
        <div class="offcanvas-header">
            <!--h5 id="offcanvasTabsLabel">Filter</h5-->
            <!--h6 class="fw-bold mb-2">Price Range</h6-->
            <!--button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button-->
        </div>
        <div class="offcanvas-body">
            <div class="mb-2">
                <input type="range" class="form-range" min="60" max="25000" step="100" id="priceRangeMobile">
                <div class="d-flex justify-content-between">
                    <small>৳60</small>
                    <small id="priceValueMobile">৳10,000</small>
                    <small>৳25,000</small>
                </div>
            </div>

            <!--h6 class="fw-bold mb-2">Product Category</h6-->
               <ul class="nav flex-column nav-pills">

                <ul class="nav flex-column nav-pills">
                @foreach($mainMenus as $main)
                   @php
                    // URL same থাকবে
                    $mainUrlName = $main->name;

                    if (strtolower($main->name) === 'new arrivals') {
                        $mainUrlName = 'New-Arrivals';
                    }

                    // 👉 শুধু display ঠিক করার জন্য
                    $displayName = $main->name;

                    if ($main->name === 'Gifts&Crafts') {
                        $displayName = 'Gifts & Crafts';
                    }

                    if ($main->name === 'Hojoborolo') {
                        $displayName = 'Ho-Jo-Bo-Ro-Lo';
                    }
                @endphp

                    <li class="nav-item" role="presentation">
                        <a class="nav-link main-menu-tab
                        {{ request()->is('category/'.$mainUrlName) ? 'active-menu' : '' }}"
                        href="{{ url('category/'.$mainUrlName) }}">
                            @if($main->name === 'Gifts&Crafts')
                                Gifts & Crafts
                            @elseif($main->name === 'Hojoborolo')
                                Ho-Jo-Bo-Ro-Lo
                            @else
                                {{ $main->name }}
                            @endif
                        </a>

                        @if($main->subMenus->count())
                            <ul class="nav flex-column ms-3 mt-1">
                                @foreach($main->subMenus as $sub)
                                    <li class="nav-item">
                                        <a class="nav-link sub-menu-tab
                                        {{ request()->is('subcategory/'.$sub->id) ? 'active-menu' : '' }}"
                                        href="{{ url('subcategory/'.$sub->id) }}">
                                            {{ $sub->name }}
                                        </a>

                                        @if($sub->childMenus->count())
                                            <ul class="nav flex-column ms-3 mt-1">
                                                @foreach($sub->childMenus as $child)
                                                    <li class="nav-item">
                                                        <a class="nav-link child-menu-tab
                                                        {{ request()->is('childcategory/'.$child->id) ? 'active-menu' : '' }}"
                                                        href="{{ url('childcategory/'.$child->id) }}">
                                                            {{ $child->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach

            </ul> 

<style>
    /* Default menu style */
.nav-link {
    color: #333;
    font-weight: 400;
    transition: 0.3s;
}

/* Active menu (selected) */
.active-menu {
    font-weight: 700 !important;
    color: #000 !important;
    background: transparent !important;
}

/* Hover effect */
.nav-link:hover {
    color: #000;
    padding-left: 6px;
}

/* Nested menu spacing */
.sub-menu-tab {
    font-size: 14px;
}

.child-menu-tab {
    font-size: 13px;
}

/* Optional: smooth show */
.show-menu {
    display: block;
}
</style>


        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('/js/popper.js') }}"></script>
<script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('/js/custom.js') }}"></script>
<script>
/* ---------- Price Range Filter ---------- */
function priceFilter(rangeId, valueId, defaultValue = 10000) {
    const range = document.getElementById(rangeId);
    const value = document.getElementById(valueId);
    const grid = document.getElementById('product-list');
    if (!range || !value || !grid) return;

    range.value = defaultValue;
    value.textContent = '৳' + defaultValue.toLocaleString();

    range.addEventListener('input', function () {
        const maxPrice = parseInt(this.value);
        value.textContent = '৳' + maxPrice.toLocaleString();

        grid.querySelectorAll('.product-card').forEach(card => {
            const price = parseFloat(card.dataset.price);
            card.style.display = (price <= maxPrice) ? '' : 'none';
        });
    });
}
priceFilter('priceRangeDesktop', 'priceValueDesktop', 10000);
priceFilter('priceRangeMobile', 'priceValueMobile', 10000);

/* ---------- Category Filter (AJAX) ---------- */
$(document).on('click', '.main-menu-tab, .sub-menu-tab, .child-menu-tab', function () {
    let slug = $(this).data('slug');
    if (!slug) return;

    if ($(this).hasClass('main-menu-tab')) {
        window.location.href = '/category/' + slug;
    } else if ($(this).hasClass('sub-menu-tab')) {
        window.location.href = '/subcategory/' + slug;
    } else if ($(this).hasClass('child-menu-tab')) {
        window.location.href = '/childcategory/' + slug;
    }
});


</script>



<script>
document.addEventListener("DOMContentLoaded", function () {

    const modal = document.createElement("div");
    modal.className = "image-modal";

    modal.innerHTML = `
        <span class="close-btn">&times;</span>
        <img src="" />
    `;

    document.body.appendChild(modal);

    const modalImg = modal.querySelector("img");
    const closeBtn = modal.querySelector(".close-btn");

    // 🔥 EVENT DELEGATION (mobile safe fix)
    document.addEventListener("click", function (e) {

        // OPEN ZOOM
        const zoom = e.target.closest(".zoom-icon");
        if (zoom) {
            e.preventDefault();
            e.stopPropagation();

            modalImg.src = zoom.dataset.img;
            modal.style.display = "flex";
            return;
        }

        // CLOSE BUTTON
        if (e.target.classList.contains("close-btn")) {
            modal.style.display = "none";
            modalImg.src = "";
            return;
        }

        // OUTSIDE CLICK CLOSE
        if (e.target === modal) {
            modal.style.display = "none";
            modalImg.src = "";
        }
    });

});
</script>



@endsection
