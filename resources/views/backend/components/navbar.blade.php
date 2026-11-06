@php
    $profilOpen = request()->is('user', 'detail-user', 'jabatan-fungsional', 'kepangkatan', 'angka-kredit');
    $kualifikasiOpen = request()->is('pendidikan', 'diklat');
@endphp
<nav class="sidebar sidebar-offcanvas" id="sidebar" aria-label="Navigasi utama">
    <div class="sidebar-workspace">
        <span class="workspace-symbol"><x-ui-icon name="briefcase" /></span>
        <div><strong>Kepegawaian</strong><span>Ruang kerja terpadu</span></div>
    </div>
    <div class="sidebar-section-label">MENU UTAMA</div>
    <ul class="nav">
        <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('dashboard') }}" @if(request()->is('dashboard')) aria-current="page" @endif>
                <x-ui-icon name="grid" /><span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item {{ $profilOpen ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#menu-profil" aria-expanded="{{ $profilOpen ? 'true' : 'false' }}" aria-controls="menu-profil">
                <x-ui-icon name="users" /><span class="menu-title">Profil</span><x-ui-icon name="chevron" class="nav-chevron" />
            </a>
            <div class="collapse {{ $profilOpen ? 'show' : '' }}" id="menu-profil">
                <ul class="nav flex-column sub-menu">
                    @foreach (['user' => 'Data Pribadi', 'jabatan-fungsional' => 'Jabatan Fungsional', 'kepangkatan' => 'Kepangkatan', 'angka-kredit' => 'Angka Kredit'] as $path => $label)
                        <li class="nav-item"><a class="nav-link {{ request()->is($path) || ($path === 'user' && request()->is('detail-user')) ? 'active' : '' }}" href="{{ url($path) }}" @if(request()->is($path) || ($path === 'user' && request()->is('detail-user'))) aria-current="page" @endif>{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
        </li>
        <li class="nav-item {{ $kualifikasiOpen ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#menu-kualifikasi" aria-expanded="{{ $kualifikasiOpen ? 'true' : 'false' }}" aria-controls="menu-kualifikasi">
                <x-ui-icon name="book" /><span class="menu-title">Kualifikasi</span><x-ui-icon name="chevron" class="nav-chevron" />
            </a>
            <div class="collapse {{ $kualifikasiOpen ? 'show' : '' }}" id="menu-kualifikasi">
                <ul class="nav flex-column sub-menu">
                    @foreach (['pendidikan' => 'Pendidikan', 'diklat' => 'Diklat'] as $path => $label)
                        <li class="nav-item"><a class="nav-link {{ request()->is($path) ? 'active' : '' }}" href="{{ url($path) }}" @if(request()->is($path)) aria-current="page" @endif>{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
        </li>
        <li class="nav-item {{ request()->is('absensi') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('absensi.index') }}" @if(request()->is('absensi')) aria-current="page" @endif>
                <x-ui-icon name="calendar" /><span class="menu-title">Absensi</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('evaluasi-kinerja') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('evaluasi-kinerja.index') }}" @if(request()->is('evaluasi-kinerja')) aria-current="page" @endif>
                <x-ui-icon name="clipboard" /><span class="menu-title">Evaluasi Kinerja</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-account">
        <span class="user-avatar">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
        <div class="sidebar-account-info"><strong>{{ Auth::user()->name }}</strong><span>{{ Auth::user()->role }}</span></div>
        <a href="{{ url('logout') }}" class="sidebar-signout" aria-label="Keluar dari aplikasi" title="Keluar"><x-ui-icon name="logout" /></a>
    </div>
</nav>
