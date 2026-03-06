<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>@yield('title')</title>

  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
  <meta name="keywords" content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
  <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('dependencies')
  <!-- [Favicon] icon -->
<link rel="icon" href="{{asset('assets/favicon.png')}}" type="image/x-icon"> <!-- [Google Font] Family -->
<link rel="stylesheet" href="{{asset('https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap')}}" id="main-font-link">
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{asset('assets/dist/assets/fonts/tabler-icons.min.css')}}" >
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{asset('assets/dist/assets/fonts/feather.css')}}" >
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{asset('assets/dist/assets/fonts/fontawesome.css')}}" >
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{asset('assets/dist/assets/fonts/material.css')}}" >
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{asset('assets/dist/assets/css/style.css')}}" id="main-style-link" >
<link rel="stylesheet" href="{{asset('assets/dist/assets/css/style-preset.css')}}" >
<link rel="stylesheet" href="{{ asset('bootstrap-icons/font/bootstrap-icons.min.css') }}">

<style>
/* Loading spinner for buttons */
.fa-spinner {
    margin-right: 5px;
}

/* Disabled button styles */
button:disabled,
input[type="submit"]:disabled {
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    pointer-events: none !important;
}
</style>



    <!-- In your main layout file -->


</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
  <!-- [ Pre-loader ] start -->
<div class="loader-bg">
  <div class="loader-track">
    <div class="loader-fill"></div>
  </div>
</div>
<!-- [ Pre-loader ] End -->
 <!-- [ Sidebar Menu ] start -->
    @include('inc.sidebar')
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
    @include('inc.header')
<!-- [ Header ] end -->



  <!-- [ Main Content ] start -->
  <div class="pc-container">
    @yield('content')
  </div>
  <!-- [ Main Content ] end -->
  @include('inc.footer')

  {{-- @include('sweetalert::alert') --}}
@include('sweetalert::alert')

  <!-- [Page Specific JS] start -->
  <script src="{{asset('assets/dist/assets/js/plugins/apexcharts.min.js')}}"></script>
  <script src="{{asset('assets/dist/assets/js/pages/dashboard-default.js')}}"></script>
  <!-- [Page Specific JS] end -->
  <!-- Required Js -->
  <script src="{{asset('assets/dist/assets/js/plugins/popper.min.js')}}"></script>
  <script src="{{asset('assets/dist/assets/js/plugins/simplebar.min.js')}}"></script>
  <script src="{{asset('assets/dist/assets/js/plugins/bootstrap.min.js')}}"></script>
  <script src="{{asset('assets/dist/assets/js/fonts/custom-font.js')}}"></script>
  <script src="{{asset('assets/dist/assets/js/pcoded.js')}}"></script>
  <script src="{{asset('assets/dist/assets/js/plugins/feather.min.js')}}"></script>




  <script>layout_change('light');</script>




  <script>change_box_container('false');</script>



  <script>layout_rtl_change('false');</script>


  <script>preset_change("preset-1");</script>


  <script>font_change("Public-Sans");</script>

  <script src="{{asset('assets/dist/assets/js/plugins/sweetalert2.all.min.js')}}"></script>
@include('sweetalert::alert')

  <script>
  // Global AJAX error handler for session expiration
  document.addEventListener('DOMContentLoaded', function() {
      // Simple visual feedback for submit buttons
      const buttons = document.querySelectorAll('button[type="submit"], input[type="submit"]');

      buttons.forEach(button => {
          let isProcessing = false;

          button.addEventListener('click', function(e) {
              // Prevent multiple clicks
              if (isProcessing) {
                  e.preventDefault();
                  e.stopPropagation();
                  return false;
              }

              // Mark as processing
              isProcessing = true;

              // Store original text
              const originalText = button.textContent || button.value;

              // Show processing state immediately
              button.style.opacity = '0.6';
              button.style.cursor = 'not-allowed';

              if (button.tagName === 'BUTTON') {
                  button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
              } else {
                  button.value = 'Processing...';
              }

              // Actually disable after a short delay to allow form submission
              setTimeout(() => {
                  button.disabled = true;
              }, 50);

              // Re-enable after 3 seconds
              setTimeout(() => {
                  isProcessing = false;
                  button.disabled = false;
                  button.style.opacity = '';
                  button.style.cursor = '';
                  if (button.tagName === 'BUTTON') {
                      button.innerHTML = originalText;
                  } else {
                      button.value = originalText;
                  }
              }, 3000);
          });
      });

      // Override fetch to check for 401 and 419
      // Override fetch to check for 401 and 419
      const originalFetch = window.fetch;
      window.fetch = function(...args) {
          return originalFetch.apply(this, args).then(response => {
              if (response.status === 401) {
                  Swal.fire({
                      icon: 'warning',
                      title: 'Session Expired',
                      text: 'Your session has expired. Please log in again.',
                      confirmButtonText: 'OK'
                  }).then(() => {
                      window.location.href = '/login';
                  });
              } else if (response.status === 419) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Page Expired',
                      text: 'The page has expired due to inactivity. Redirecting to login...',
                      confirmButtonText: 'OK',
                      timer: 3000,
                      timerProgressBar: true
                  }).then(() => {
                      window.location.href = '/login';
                  });
              }
              return response;
          });
      };

      // Proactive session expiration warning for AFK users
      // Assuming session lifetime is 120 minutes (7200000 ms), warn 5 minutes before (300000 ms)
      const sessionLifetime = 7200000; // 120 minutes in ms
      const warningTime = 300000; // 5 minutes before expiration
      const warningDelay = sessionLifetime - warningTime;

      setTimeout(() => {
          Swal.fire({
              icon: 'warning',
              title: 'Session Expiring Soon',
              text: 'Your session will expire in 5 minutes due to inactivity. Please save your work or continue using the app.',
              confirmButtonText: 'OK',
              timer: 10000, // Auto-close after 10 seconds
              timerProgressBar: true
          });
      }, warningDelay);
  });
  </script>

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}"
        });
    </script>
    @endif

@yield('script')

</body>
<!-- [Body] end -->

</html>
