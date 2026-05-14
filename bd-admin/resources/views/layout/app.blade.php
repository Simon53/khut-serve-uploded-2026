<!DOCTYPE html>
<html lang="en">
  @include('layout.header')
  <body>
    <div class="container-scroller" style="background: url('{{ asset('images/auth/Login_bg.jpg') }}'); background-size: cover;">
      <!-- partial:partials/_sidebar.html -->
     @include('layout.sidebar')
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        @include('layout.navbar')
       
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            @yield('content')
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
            @include('layout.footer') 
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
     @include('layout.bottomlink') 
     @yield('script')
  </body>
</html>