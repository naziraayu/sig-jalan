<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>{{ config('app.name') }}</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('assets/modules/jqvmap/dist/jqvmap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css') }}">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css"/>
  
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

  <!-- ✅ TAMBAH: iziToast CSS -->
  <link rel="stylesheet" href="{{ asset('assets/modules/izitoast/css/iziToast.min.css') }}">

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    /* Year Filter Styling */
    .year-filter-wrapper {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 5px 15px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 6px;
      backdrop-filter: blur(10px);
    }

    .year-filter-label {
      color: #fff;
      font-size: 13px;
      font-weight: 500;
      white-space: nowrap;
      margin: 0;
    }

    #yearFilter {
      min-width: 120px;
      background-color: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: #333;
      font-weight: 600;
      font-size: 14px;
      padding: 4px 10px;
      height: 32px;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    #yearFilter:hover:not(:disabled) {
      background-color: #fff;
      border-color: #6777ef;
    }

    #yearFilter:focus {
      background-color: #fff;
      border-color: #6777ef;
      box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.25);
      outline: none;
    }

    #yearFilter:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      background-color: rgba(255, 255, 255, 0.7);
    }

    /* Loading spinner */
    .year-filter-loading {
      display: none;
      width: 16px;
      height: 16px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .year-filter-loading.active {
      display: block;
    }

    @media (max-width: 768px) {
      .year-filter-wrapper {
        padding: 3px 10px;
        gap: 6px;
      }
      
      #yearFilter {
        min-width: 90px;
        font-size: 13px;
        padding: 3px 8px;
      }
      
      .year-filter-label {
        font-size: 12px;
      }
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>

          <!-- Year Filter Component -->
          <div class="year-filter-wrapper ml-3">
            <label class="year-filter-label" for="yearFilter">
              <i class="fas fa-calendar-alt"></i> Tahun:
            </label>
            <select id="yearFilter" class="form-control form-control-sm" aria-label="Pilih Tahun">
              <option value="">Memuat...</option>
            </select>
            <div class="year-filter-loading" id="yearFilterLoading" title="Loading..."></div>
          </div>
        </form>

        <ul class="navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" 
                src="{{ Auth::check() && Auth::user()->photo 
                    ? asset('storage/' . Auth::user()->photo) 
                    : asset('assets/img/avatar/avatar-1.png') }}" 
                class="rounded-circle mr-1" 
                style="width: 35px; height: 35px; object-fit: cover;">
              <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title">{{ Auth::check() ? 'Logged in as ' . Auth::user()->email : 'Not logged in' }}</div>
              <a href="{{ route('profile.edit') }}" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profile
              </a>
              <div class="dropdown-divider"></div>
              <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
              </form>
              <a href="#" class="dropdown-item has-icon text-danger"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      
      @include('layouts.sidebar')

      @yield('content')
      
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2025 <div class="bullet"></div>
        </div>
        <div class="footer-right">
        </div>
      </footer>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    window.jQuery || document.write('<script src="{{ asset("assets/modules/jquery.min.js") }}"><\/script>');
  </script>

  <script src="{{ asset('assets/modules/popper.js') }}"></script>
  <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
  <script src="{{ asset('assets/js/stisla.js') }}"></script>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  
  <!-- JS Libraries -->
  <script src="{{ asset('assets/modules/jquery.sparkline.min.js') }}"></script>
  <script src="{{ asset('assets/modules/chart.min.js') }}"></script>
  <script src="{{ asset('assets/modules/owlcarousel2/dist/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('assets/modules/summernote/summernote-bs4.js') }}"></script>
  <script src="{{ asset('assets/modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

  <!-- ✅ TAMBAH: iziToast JS -->
  <script src="{{ asset('assets/modules/izitoast/js/iziToast.min.js') }}"></script>

  <!-- Page Specific JS File -->
  <script src="{{ asset('assets/js/page/index.js') }}"></script>
  
  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

  <!-- ✅ PERBAIKAN: Year Filter Script -->
  <script>
    $(function() {
      const yearFilter = $('#yearFilter');
      const yearFilterLoading = $('#yearFilterLoading');
      let isChanging = false; // Prevent double submit

      // ✅ PERBAIKI: Cek CSRF token dulu
      const csrfToken = $('meta[name="csrf-token"]').attr('content');
      if (!csrfToken) {
        console.error('CSRF token not found!');
      }

      function showLoading() {
        yearFilter.prop('disabled', true);
        yearFilterLoading.addClass('active');
      }

      function hideLoading() {
        yearFilter.prop('disabled', false);
        yearFilterLoading.removeClass('active');
      }

      // ✅ PERBAIKI: Toast notification dengan fallback
      function showToast(message, type = 'success') {
        if (typeof iziToast !== 'undefined') {
          const config = {
            message: message,
            position: 'topRight',
            timeout: 3000,
            transitionIn: 'fadeInDown',
            transitionOut: 'fadeOutUp',
          };

          switch(type) {
            case 'success':
              iziToast.success({
                ...config,
                title: 'Berhasil',
                backgroundColor: '#47c363',
                icon: 'fas fa-check-circle',
              });
              break;
            case 'error':
              iziToast.error({
                ...config,
                title: 'Error',
                backgroundColor: '#fc544b',
                icon: 'fas fa-exclamation-circle',
              });
              break;
            case 'warning':
              iziToast.warning({
                ...config,
                title: 'Peringatan',
                backgroundColor: '#ffa426',
                icon: 'fas fa-exclamation-triangle',
              });
              break;
            case 'info':
              iziToast.info({
                ...config,
                title: 'Info',
                backgroundColor: '#3abaf4',
                icon: 'fas fa-info-circle',
              });
              break;
          }
        } else {
          // ✅ Fallback ke Bootstrap alert atau console
          console.log(`[${type.toUpperCase()}] ${message}`);
          
          // Optional: Gunakan alert native
          if (type === 'error') {
            alert('Error: ' + message);
          }
        }
      }

      function parseYearsResponse(resp) {
        // Handle berbagai format response
        if (Array.isArray(resp)) return resp;
        if (resp && Array.isArray(resp.years)) return resp.years;
        if (resp && resp.data && Array.isArray(resp.data)) return resp.data;
        return [];
      }

      function loadAvailableYears() {
        showLoading();

        $.ajax({
          url: '{{ route("year.filter.available") }}',
          method: 'GET',
          dataType: 'json',
          timeout: 10000, // ✅ TAMBAH: timeout 10 detik
          success: function(response) {
            const years = parseYearsResponse(response);

            yearFilter.empty();

            if (years.length === 0) {
              yearFilter.append('<option value="">Tidak ada data tahun</option>');
              hideLoading();
              return;
            }

            // ✅ PERBAIKI: Tambah option untuk setiap tahun
            years.forEach(function(year) {
              yearFilter.append(`<option value="${year}">${year}</option>`);
            });

            // ✅ Load current year dari server
            loadCurrentYear();
          },
          error: function(xhr, status, error) {
            console.error('Error loading years:', {
              status: status,
              error: error,
              response: xhr.responseText
            });
            
            yearFilter.empty();
            yearFilter.append('<option value="">Error memuat tahun</option>');
            
            showToast('Gagal memuat data tahun. Silakan refresh halaman.', 'error');
            hideLoading();
          }
        });
      }

      // ✅ Load tahun terpilih dari server
      function loadCurrentYear() {
        $.ajax({
          url: '{{ route("year.filter.current") }}',
          method: 'GET',
          dataType: 'json',
          timeout: 5000,
          success: function(response) {
            if (response && response.success && response.year) {
              // ✅ Set selected year
              yearFilter.val(response.year);
              
              // ✅ Jika tahun tidak ada di options, tambahkan
              if (!yearFilter.find(`option[value="${response.year}"]`).length) {
                yearFilter.append(`<option value="${response.year}" selected>${response.year}</option>`);
              }
            } else {
              // ✅ Jika gagal, pilih tahun pertama
              yearFilter.find('option:first').prop('selected', true);
            }
            hideLoading();
          },
          error: function(xhr, status, error) {
            console.error('Error loading current year:', error);
            
            // ✅ Fallback: pilih tahun pertama
            yearFilter.find('option:first').prop('selected', true);
            hideLoading();
          }
        });
      }

      // ✅ PERBAIKI: Event handler untuk change year
      yearFilter.on('change', function() {
        const selectedYear = $(this).val();
        
        // ✅ Validasi
        if (!selectedYear || selectedYear === '') {
          showToast('Silakan pilih tahun terlebih dahulu', 'warning');
          return;
        }

        // ✅ Prevent double submit
        if (isChanging) {
          console.log('Already changing year, please wait...');
          return;
        }

        // ✅ Cek CSRF token
        if (!csrfToken) {
          showToast('CSRF token tidak ditemukan. Silakan refresh halaman.', 'error');
          return;
        }

        isChanging = true;
        showLoading();

        $.ajax({
          url: '{{ route("year.filter.set") }}',
          method: 'POST',
          data: {
            year: selectedYear,
            _token: csrfToken
          },
          dataType: 'json',
          timeout: 10000,
          success: function(response) {
            if (response && response.success) {
              showToast(`Filter tahun berhasil diubah ke ${selectedYear}`, 'success');
              
              // ✅ Reload dengan delay untuk lihat toast
              setTimeout(function() {
                window.location.reload();
              }, 800);
            } else {
              const msg = response && response.message 
                ? response.message 
                : 'Gagal mengubah filter tahun';
              
              showToast(msg, 'error');
              hideLoading();
              isChanging = false;
            }
          },
          error: function(xhr, status, error) {
            console.error('Error setting year filter:', {
              status: status,
              error: error,
              response: xhr.responseText
            });

            let errorMsg = 'Gagal mengubah filter tahun';
            
            // ✅ Parse error message dari server
            try {
              const errorResponse = JSON.parse(xhr.responseText);
              if (errorResponse.message) {
                errorMsg = errorResponse.message;
              }
            } catch (e) {
              // Use default error message
            }

            showToast(errorMsg, 'error');
            hideLoading();
            isChanging = false;
          }
        });
      });

      // ✅ Init: Load years on page load
      loadAvailableYears();
    });
  </script>

  @stack('scripts')

</body>
</html>