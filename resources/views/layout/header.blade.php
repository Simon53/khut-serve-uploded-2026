 <!--<head>-->
 <!--     <meta charset="utf-8">-->
 <!--     <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">-->
 <!--     <meta name="description" content="">-->
 <!--     <meta name="author" content="">-->
 <!--     <title>@yield('title')</title>-->
 <!--      @yield('meta')-->
 <!--     <link rel="icon" type="image/x-icon" href="{{asset('/images/favicon.ico')}}">-->
      <!-- Additional CSS Files -->
    
 <!--     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>-->
      
 <!--     <link rel="stylesheet" href="{{asset('/css/swiper-bundle.min.css')}}" />-->
 <!--     <link rel="stylesheet" type="text/css" href="{{asset('/css/bootstrap.css')}}">-->
 <!--      <link rel="stylesheet" href="{{asset('/css/aos.css')}}">-->
 <!--     <link rel="stylesheet" href="{{asset('/css/style.css')}}">-->
 <!--     <link rel="stylesheet" href="{{asset('/css/resposive.css')}}">-->
 <!--  </head>-->
   
   
   <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title', 'Khut Shop')</title>

    <meta name="description" content="@yield('description', 'বাংলাদেশের বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম')">
    <meta name="author" content="Khut Shop">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Khut Shop')">
    <meta property="og:description" content="@yield('description', 'বাংলাদেশের বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Khut Shop">
    <meta property="og:image" content="{{ asset('images/share-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Khut Shop')">
    <meta name="twitter:description" content="@yield('description', 'বাংলাদেশের বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম')">
    <meta name="twitter:image" content="{{ asset('images/share-image.jpg') }}">

    @yield('meta')

    <link rel="icon" type="image/x-icon" href="{{ asset('/images/favicon.ico') }}">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/resposive.css') }}">
</head>
   
<!-- ===== Popup Modal Start ===== -->
<div 
    class="modal fade" 
    id="homePopupModal" 
    tabindex="-1" 
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent shadow-none">

            <!-- Image -->
            <div class="position-relative text-center">
                <img 
                    src="{{ asset('images/popup-image.jpg') }}" 
                    alt="Popup Image"
                    class="img-fluid rounded-3 popup-image"
                >

                <!-- Processed Button -->
                <button 
                    type="button"
                    class="btn popup-close-btn"
                    data-bs-dismiss="modal"
                    disabled
                >
                    Eid Mubarak 
                </button>
            </div>

        </div>
    </div>
</div>
<!-- ===== Popup Modal End ===== -->


<!-- ===== Auto Show Modal ===== -->
<!--script>
    document.addEventListener('DOMContentLoaded', function () {
        let popupModal = new bootstrap.Modal(
            document.getElementById('homePopupModal')
        );

        popupModal.show();
    });
</script-->


<!-- ===== Popup Style ===== -->
<style>
    /*.popup-image{*/
    /*    width: 100%;*/
    /*    border-radius: 20px;*/
    /*}*/

    /*.popup-close-btn{*/
    /*    position: absolute;*/
    /*    bottom: 120px;*/
    /*    left: 50%;*/
    /*    transform: translateX(-50%);*/
    /*    background: #000;*/
    /*    color: #fff;*/
    /*    padding: 12px 35px;*/
    /*    font-size: 20px;*/
    /*    font-weight: 600;*/
    /*    border-radius: 50px;*/
    /*    border: none;*/
    /*    transition: 0.3s;*/
    /*    z-index: 10;*/
    /*}*/

    /*.popup-close-btn:hover{*/
    /*    background: #222;*/
    /*    color: #fff;*/
    /*}*/

    /*.modal-content{*/
    /*    background: transparent !important;*/
    /*}*/

    /*@media(max-width: 576px){*/
    /*    .popup-close-btn{*/
    /*        font-size: 16px;*/
    /*        padding: 10px 28px;*/
    /*        bottom: 80px;*/
    /*    }*/
    /*}*/
</style>