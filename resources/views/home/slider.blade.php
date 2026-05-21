


  <!-- ===== Slider Start ===== -->
      <div class="slider-container">
        <!-- Carousel -->
        <div id="customCarousel" class="carousel slide" data-bs-ride="carousel">
             <div class="carousel-inner">
                  <!-- @foreach($sliders as $key => $slider)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                           <img src="{{ $slider->full_image }}" class="sliderImgSize" alt="Slide {{ $key + 1 }}">
                        </div>
                    @endforeach -->

                    @foreach($sliders as $key => $slider)

                      <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                             {{-- ===== Embed Video (YouTube / Facebook) ===== --}}
                        @if($slider->video_url)

                            <iframe class="sliderImgSize" src="{{ $slider->video_url }}" frameborder="0" allow="autoplay; encrypted-media"
                                allowfullscreen>
                            </iframe>

                           @elseif(Str::endsWith($slider->full_image, ['.mp4', '.webm', '.mov', '.avi']))
                              <video class="sliderImgSize" autoplay muted loop playsinline>
                                  <source src="{{ $slider->full_image }}">
                              </video>
                          @else
                            <img src="{{ $slider->full_image }}" class="sliderImgSize" alt="Slide {{ $key + 1 }}">
                          @endif

                      </div>

                  @endforeach
             </div>

             <!-- Controls -->
             <button class="carousel-control-prev" type="button" data-bs-target="#customCarousel" data-bs-slide="prev">
               <span class="carousel-control-prev-icon" aria-hidden="true"></span>
               <span class="visually-hidden">Previous</span>
             </button>
             <button class="carousel-control-next" type="button" data-bs-target="#customCarousel" data-bs-slide="next">
               <span class="carousel-control-next-icon" aria-hidden="true"></span>
               <span class="visually-hidden">Next</span>
             </button>
            <div class="ribon"></div>
        </div>

        <!-- Left overlay text -->
        <div class="slider-overlay">
            <p >
               আমাদের প্রত্যেকের আছে<br/>
               কিছু নিজস্বতা,<br/>
               যা পূর্ণতা চায়না।<br/>
               কিছু কমতি, একটু খুঁত-ই<br/>
               যেন একক সত্ত্বার<br/>
               সৌন্দর্য ধারণ করে।<br/>
            <p>
            <p >   
               ঠিক এমন বিশ্বাসের জায়গা<br/>
               থেকেই প্রতিটি পণ্য গড়েছে<br/>
               স্বাতন্ত্র্য, তবু আপন<br/>
               একটু খুঁত নিয়ে-<br/>
               ঠিক আপনার-আমার মতো…
            </p>
            <p class="custom-link"><a href="{{ route('shop.index') }}">All Products</a></p>
        </div>
      </div>


      <!-- ===== Slider End ===== -->
      
      <!-- ===== Under Construction Modal ===== -->
<!--div class="modal fade" id="underConstructionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-body">
        <h5>🚧 Under Construction 🚧</h5>
        <p>Coming back very soon...</p>
        <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div-->

<!-- ===== Script to Show Modal on Page Load ===== -->
<!--script>
  document.addEventListener('DOMContentLoaded', function() {
    var underModal = new bootstrap.Modal(document.getElementById('underConstructionModal'));
    underModal.show();
  });
</script-->


<!-- ===== Popup Modal Start ===== -->
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
                >
                    Processed
                </button>
            </div>

        </div>
    </div>
</div>
<!-- ===== Popup Modal End ===== -->


<!-- ===== Auto Show Modal ===== -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let popupModal = new bootstrap.Modal(
            document.getElementById('homePopupModal')
        );

        popupModal.show();
    });
</script>


<!-- ===== Popup Style ===== -->
<style>
    .popup-image{
        width: 100%;
        border-radius: 20px;
    }

    .popup-close-btn{
        position: absolute;
        bottom: 120px;
        left: 50%;
        transform: translateX(-50%);
        background: #000;
        color: #fff;
        padding: 12px 35px;
        font-size: 20px;
        font-weight: 600;
        border-radius: 50px;
        border: none;
        transition: 0.3s;
        z-index: 10;
    }

    .popup-close-btn:hover{
        background: #222;
        color: #fff;
    }

    .modal-content{
        background: transparent !important;
    }

    @media(max-width: 576px){
        .popup-close-btn{
            font-size: 16px;
            padding: 10px 28px;
            bottom: 80px;
        }
    }
</style>
      
