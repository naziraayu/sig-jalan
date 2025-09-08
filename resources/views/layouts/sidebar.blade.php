<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">DPUBMSDA JEMBER</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">St</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="dropdown active">
                <a href="#" ><i class="fas fa-fire"></i><span>Dashboard</span></a>
                {{-- <ul class="dropdown-menu">
                    <li><a class="nav-link" href="index-0.html">General Dashboard</a></li>
                    <li class=active><a class="nav-link" href="index.html">Ecommerce Dashboard</a></li>
                </ul> --}}
            </li>
            <li class="menu-header">Menu</li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i> <span>Administrasi</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="layout-default.html">Provinsi</a></li>
                    <li><a class="nav-link" href="layout-transparent.html">Balai</a></li>
                    <li><a class="nav-link" href="layout-top-navigation.html">Pulau</a></li>
                    <li><a class="nav-link" href="layout-transparent.html">Kabupaten</a></li>
                    <li><a class="nav-link" href="layout-top-navigation.html">Kecamatan</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-th"></i> <span>Pengaturan Jaringan</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="bootstrap-alert.html">Ruas Jalan</a></li>
                    <li><a class="nav-link" href="bootstrap-badge.html">DRP</a></li>
                    <li><a class="nav-link" href="bootstrap-breadcrumb.html">Kelas Jalan</a></li>
                    <li><a class="nav-link" href="bootstrap-buttons.html">Koridor</a></li>
                    <li><a class="nav-link" href="bootstrap-card.html">Ruas Jalan/Kabupaten</a></li>
                    <li><a class="nav-link" href="bootstrap-carousel.html">Ruas Jalan/Kecamatan</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-th-large"></i> <span>Jalan</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="components-article.html">Inventarisasi Jalan</a></li>                              
                    <li><a class="nav-link" href="components-chat-box.html">Kondisi Jalan</a></li>                
                    <li><a class="nav-link" href="components-gallery.html">Impor Koordinat GPS</a></li>
                    <li><a class="nav-link" href="components-hero.html">Nilai MCA Ruas</a></li>                                      
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-map"></i> <span>Peta</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="components-article.html">Kabupaten</a></li>                              
                    <li><a class="nav-link" href="components-chat-box.html">Kecamatan</a></li>                                                    
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-cog"></i> <span>Pengaturan</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('roles.index') }}">Hak Akses</a></li>                           
                    <li><a class="nav-link" href="{{ route('users.index') }}">User</a></li>                
                    <li><a class="nav-link" href="components-gallery.html">Profil</a></li>                             
                </ul>
            </li>
        </ul>

        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <a href="https://getstisla.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-rocket"></i> Logout
            </a>
        </div>        
    </aside>
</div>