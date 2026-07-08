


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



      
