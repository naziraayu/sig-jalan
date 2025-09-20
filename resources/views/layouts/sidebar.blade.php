@php
    $user = auth()->user();
@endphp

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">DPUBMSDA JEMBER</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('dashboard') }}">St</a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="dropdown active">
                <a href="{{ route('dashboard') }}"><i class="fas fa-fire"></i><span>Dashboard</span></a>
            </li>

            <li class="menu-header">Menu</li>

            {{-- Administrasi --}}
            @if($user->features()->intersect(['provinsi','balai','pulau','kabupaten','kecamatan'])->isNotEmpty())
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-columns"></i> <span>Administrasi</span></a>
                    <ul class="dropdown-menu">
                        @if($user->hasPermission('detail', 'provinsi'))
                            <li><a class="nav-link" href="{{ route('provinces.index') }}">Provinsi</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'balai'))
                            <li><a class="nav-link" href="{{ route(name: 'balai.index') }}">Balai</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'pulau'))
                            <li><a class="nav-link" href="{{ route(name: 'island.index') }}">Pulau</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'kabupaten'))
                            <li><a class="nav-link" href="{{ route(name: 'kabupaten.index') }}">Kabupaten</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'kecamatan'))
                            <li><a class="nav-link" href="{{ route(name: 'kecamatan.index') }}">Kecamatan</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Pengaturan Jaringan --}}
            @if($user->features()->intersect(['ruas_jalan','drp','kelas_jalan','koridor','ruas_jalan_kabupaten','ruas_jalan_kecamatan'])->isNotEmpty())
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-th"></i> <span>Pengaturan Jaringan</span></a>
                    <ul class="dropdown-menu">
                        @if($user->hasPermission('detail', 'ruas_jalan'))
                            <li><a class="nav-link" href="{{ route(name: 'ruas-jalan.index') }}">Ruas Jalan</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'drp'))
                            <li><a class="nav-link" href="{{ route(name: 'drp.index') }}">DRP</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'kelas_jalan'))
                            <li><a class="nav-link" href="#">Kelas Jalan</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'koridor'))
                            <li><a class="nav-link" href="#">Koridor</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'ruas_jalan_kabupaten'))
                            <li><a class="nav-link" href="#">Ruas Jalan/Kabupaten</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'ruas_jalan_kecamatan'))
                            <li><a class="nav-link" href="#">Ruas Jalan/Kecamatan</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Jalan --}}
            @if($user->features()->intersect(['inventarisasi_jalan','kondisi_jalan','koordinat_gps','nilai_mca_ruas'])->isNotEmpty())
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-th-large"></i> <span>Jalan</span></a>
                    <ul class="dropdown-menu">
                        @if($user->hasPermission('detail', 'inventarisasi_jalan'))
                            <li><a class="nav-link" href="{{ route(name: 'inventarisasi-jalan.index') }}">Inventarisasi Jalan</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'kondisi_jalan'))
                            <li><a class="nav-link" href="#">Kondisi Jalan</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'koordinat_gps'))
                            <li><a class="nav-link" href="#">Impor Koordinat GPS</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'nilai_mca_ruas'))
                            <li><a class="nav-link" href="#">Nilai MCA Ruas</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Peta --}}
            @if($user->features()->intersect(['kabupaten','kecamatan'])->isNotEmpty())
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-map"></i> <span>Peta</span></a>
                    <ul class="dropdown-menu">
                        @if($user->hasPermission('detail', 'kabupaten'))
                            <li><a class="nav-link" href="#">Kabupaten</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'kecamatan'))
                            <li><a class="nav-link" href="#">Kecamatan</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Pengaturan --}}
            @if($user->features()->intersect(['hak_akses','user','profile'])->isNotEmpty())
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-cog"></i> <span>Pengaturan</span></a>
                    <ul class="dropdown-menu">
                        @if($user->hasPermission('detail', 'hak_akses'))
                            <li><a class="nav-link" href="{{ route('roles.index') }}">Hak Akses</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'user'))
                            <li><a class="nav-link" href="{{ route('users.index') }}">User</a></li>
                        @endif
                        @if($user->hasPermission('detail', 'profile'))
                            <li><a class="nav-link" href="{{ route('profile.edit') }}">Profil</a></li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>

        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
            </form>
            <a href="#" class="btn btn-primary btn-lg btn-block btn-icon-split"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>
</div>
