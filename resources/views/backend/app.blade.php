<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PANDU Pengelolaan Kepegawaian Terpadu</title>
    <link rel="stylesheet" href="{{ asset('skydash/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('skydash/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('skydash/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('skydash/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('skydash/js/select.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('skydash/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="{{ url('ilanding/assets/img/pandu3.png') }}" rel="icon">
    <link href="{{ url('ilanding/assets/img/pandu3.png') }}" rel="apple-touch-icon">
    <link rel="stylesheet" href="{{ asset('css/backend/app.css') }}?v={{ filemtime(public_path('css/backend/app.css')) }}">
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    @stack('style')
    <script language="javascript">
        function getkey(e) {
            if (window.event)
                return window.event.keyCode;
            else if (e)
                return e.which;
            else
                return null;
        }

        function goodchars(e, goods, field) {
            var key, keychar;
            key = getkey(e);
            if (key == null) return true;

            keychar = String.fromCharCode(key);
            keychar = keychar.toLowerCase();
            goods = goods.toLowerCase();

            // check goodkeys
            if (goods.indexOf(keychar) != -1)
                return true;
            // control keys
            if (key == null || key == 0 || key == 8 || key == 9 || key == 27)
                return true;

            if (key == 13) {
                var i;
                for (i = 0; i < field.form.elements.length; i++)
                    if (field == field.form.elements[i])
                        break;
                i = (i + 1) % field.form.elements.length;
                field.form.elements[i].focus();
                return false;
            };
            // else return false
            return false;
        }
    </script>
</head>

<body class="app-shell">
    <div class="container-scroller">
        <a class="skip-link" href="#main-content">Langsung ke konten</a>
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row" aria-label="Bilah aplikasi">
            <div class="navbar-brand-wrapper d-flex align-items-center">
                <a class="navbar-brand brand-logo" href="{{ url('dashboard') }}" aria-label="JABFUNG Dashboard">
                    <span class="brand-mark">J<span>.</span></span><span class="brand-name">JABFUNG<span>Manajemen Kepegawaian</span></span>
                </a>
                <a class="navbar-brand brand-logo-mini" href="{{ url('dashboard') }}" aria-label="Dashboard"><span class="brand-mark">J<span>.</span></span></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center">
                <button class="navbar-toggler d-none d-lg-inline-flex" type="button" data-toggle="minimize" aria-label="Perkecil atau perluas sidebar"><x-ui-icon name="menu" /></button>
                <button class="navbar-toggler d-lg-none" type="button" data-toggle="offcanvas" aria-controls="sidebar" aria-expanded="false" aria-label="Buka menu navigasi"><x-ui-icon name="menu" /></button>
                <div class="topbar-context"><span>RUANG KERJA</span><strong>Pengelolaan Kepegawaian</strong></div>
                <div class="topbar-right">
                    <span class="topbar-date">{{ now()->locale('id')->translatedFormat('d F Y') }}</span>
                    <div class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle topbar-profile" href="#" data-toggle="dropdown" id="profileDropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="user-avatar">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                            <span class="topbar-user"><strong>{{ Auth::user()->name }}</strong><small>{{ Auth::user()->role }}</small></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                            <div class="dropdown-user-info"><strong>{{ Auth::user()->name }}</strong><span>{{ Auth::user()->email }}</span></div>
                            <a class="dropdown-item" href="{{ url('logout') }}"><x-ui-icon name="logout" /> Keluar</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <button class="sidebar-backdrop" type="button" aria-label="Tutup menu navigasi" hidden></button>
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_settings-panel.html -->
            <div class="theme-setting-wrapper">
                <!-- <div id="settings-trigger"><i class="ti-settings"></i></div> -->
            </div>

            <!-- partial -->
            <!-- partial:partials/_sidebar.html -->
            @include('backend.components.navbar')

            <!-- partial -->
            <div class="main-panel">

                <main class="content-wrapper" id="main-content" tabindex="-1">
                    @yield('content')
                </main>
                <footer class="app-footer"><span>JABFUNG <span class="footer-dot">&middot;</span> Manajemen Kepegawaian</span><span>Ruang kerja yang lebih teratur.</span></footer>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->

                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <script src="{{ asset('skydash/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('skydash/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('skydash/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('skydash/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('skydash/js/off-canvas.js') }}"></script>
    <script src="{{ asset('skydash/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('skydash/js/template.js') }}"></script>
    <script src="{{ asset('skydash/js/settings.js') }}"></script>
    <script src="{{ asset('skydash/js/todolist.js') }}"></script>
    <script src="{{ asset('skydash/js/dashboard.js') }}"></script>
    <script src="{{ asset('skydash/js/Chart.roundedBarCharts.js') }}"></script>
    <script src="{{ asset('skydash/js/axios/dist/axios.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="{{ asset('js/backend/app.js') }}?v={{ filemtime(public_path('js/backend/app.js')) }}"></script>
    @stack('script')
    <script>
        $('#btnSync').click(function() {

            $.ajax({
                url: '/sync-users',
                type: 'GET',
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 3000
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memulai sinkronisasi'
                    });
                }
            });

        });
    </script>
</body>

</html>
