<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <li class="nav-item mb-2">
            <div style="
                border-radius: 8px; 
                height: fit-content; 
                width: 100%;
                background-image: url('https://cdn.pixabay.com/photo/2022/10/03/23/41/house-7497002_640.png');
                background-position: center;
                "
                class="text-white py-1 px-3">
                @php
                    $user = DB::table('users')->where('users.id', Auth::id())->first();
                @endphp

                <b>Name:</b><br>
                {{ $user->email }}

            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="icon-layout menu-icon"></i>
                <span class="menu-title">Master</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('user') }}">User</a>
                    </li>
                </ul>
            </div>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="bi bi-person-fill menu-icon"></i>
                <span class="menu-title">Profil</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('user') }}">Data Pribadi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('jabatan-fungsional') }}">Jab. Fungsional</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('kepangkatan') }}">Kepangkatan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('angka-kredit') }}">Angka Kredit</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="bi bi-mortarboard-fill menu-icon"></i>
                <span class="menu-title">Kualifikasi</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('pendidikan') }}">Pendidikan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('diklat') }}">Diklat</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
