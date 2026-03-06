<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="../dashboard/index.html" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="{{asset('assets\image\logo_klh_tulisan.png')}}" class="img-fluid brand-logo" >
      </a>
    </div>
    <div class="navbar-content">
    <ul class="pc-navbar">
        <li class="pc-item">
            <a href="{{route('dashboard.index')}}" class="pc-link">
                <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                <span class="pc-mtext">Dashboard</span>
            </a>
        </li>

        <li class="pc-item pc-caption">
            <label>Side Panel</label>
            <i class="ti ti-dashboard"></i>
        </li>

        @if (auth()->check() && auth()->user()->isAdmin())

        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-stack-2"></i></span><span class="pc-mtext">Master Data</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('user.index') }}">Data User</a></li>
            <li><hr class=""></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('satker.index') }}">Data Satker</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('bmn.index') }}">Data Jenis BMN</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('unitkerja.index') }}">Data Unit Kerja</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('unitteknis.index') }}">Data Unit Teknis</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('barang.index') }}">Data Kode Barang</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('lokasi.index') }}">Data Lokasi Ruang</a></li>
            <li><hr class=""></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('identitas.index') }}">Data Identitas</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('atribut.index') }}">Data Atribut</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('identitas-kategori.index') }}">Data Kategori Identitas</a></li>
            <li><hr class=""></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('settings.index') }}">Settings</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('backups.index') }}">Backup Dashboard</a></li>

            {{-- <li class="pc-item pc-hasmenu">
              <a href="#!" class="pc-link">User<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu">
                <li class="pc-item"><a class="pc-link" href="{{ route('user.index') }}">Users</a></li>
                <li class="pc-item"><a class="pc-link" href="{{ route('level.index') }}">Levels</a></li>
              </ul>
            </li> --}}
          </ul>
        </li>

        @endif

        <li class="pc-item pc-hasmenu">
            <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-database"></i></span><span class="pc-mtext">Database</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> @if($requestedCount > 0 && auth()->check() && auth()->user()->isAdmin())<span class="badge bg-danger ms-2">{{ $requestedCount }}</span>@endif</a>
            <ul class="pc-submenu">
                <li class="pc-item ">
                <a href="{{ route('siman.index') }}" class="pc-link"><span class="pc-micon"><i class="ti ti-database"></i></span><span class="pc-mtext">Data SIMAN</span></a>
                </li>


                <li class="pc-item ">
                <a href="{{ route('internal.index') }}" class="pc-link"><span class="pc-micon"><i class="ti ti-database"></i></span><span class="pc-mtext">Data Internal</span></a>
                </li>

                @if (auth()->check() && auth()->user()->isAdmin())
                <li class="pc-item ">
                <a href="{{ route('internal.locked') }}" class="pc-link"><span class="pc-micon"><i class="ti ti-lock"></i></span><span class="pc-mtext">Data Internal Terkunci</span> @if($requestedCount > 0)<span class="badge bg-danger ms-2">{{ $requestedCount }}</span>@endif</a>
                </li>
                @endif

                <li class="pc-item ">
                <a href="{{ route('invalid.index') }}" class="pc-link"><span class="pc-micon"><i class=" ti ti-database"></i></span><span class="pc-mtext">Data Invalid</span></a>
                </li>

                {{-- <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link">User<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                <ul class="pc-submenu">
                    <li class="pc-item"><a class="pc-link" href="{{ route('user.index') }}">Users</a></li>
                    <li class="pc-item"><a class="pc-link" href="{{ route('level.index') }}">Levels</a></li>
                </ul>
                </li> --}}
            </ul>
        </li>



        <li class="pc-item ">
          <a href="{{ route('compare.index') }}" class="pc-link"><span class="pc-micon"><i class=" ti ti-table"></i></span><span class="pc-mtext">Tabel Komparasi</span></a>
        </li>

        @if (auth()->check() && auth()->user()->isAdmin())

        <li class="pc-item ">
          <a href="{{ route('activity-logs.index') }}" class="pc-link"><span class="pc-micon"><i class=" ti ti-clipboard-list"></i></span><span class="pc-mtext">Activity Logs</span></a>
        </li>
        @endif


    </ul>
    </div>
  </div>
</nav>
