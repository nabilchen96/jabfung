<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk · JABFUNG</title>
    <link rel="stylesheet" href="{{ asset('css/backend/login.css') }}?v={{ filemtime(public_path('css/backend/login.css')) }}">
</head>
<body>
    <main class="login-layout">
        <section class="login-story" aria-label="JABFUNG Manajemen Kepegawaian">
            <a href="{{ url('/') }}" class="login-brand"><span class="login-mark">J<span>.</span></span><span>JABFUNG<small>MANAJEMEN KEPEGAWAIAN</small></span></a>
            <div class="login-story-content">
                <span class="login-eyebrow">RUANG KERJA TERPADU</span>
                <h1>Data lebih teratur.<br>Kerja lebih <em>terarah.</em></h1>
                <p>Kelola profil, kualifikasi, absensi, dan evaluasi kinerja dalam satu ruang yang terhubung.</p>
                <div class="login-preview" aria-hidden="true">
                    <div class="preview-header"><span class="preview-dot"></span> Ringkasan kepegawaian <span class="preview-more">···</span></div>
                    <div class="preview-content"><div class="preview-chart"><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div><div class="preview-lines"><span></span><span></span><span></span><span></span></div></div>
                    <div class="preview-footer"><span>Profil &amp; kualifikasi</span><span>Absensi &amp; kinerja</span></div>
                </div>
            </div>
            <div class="login-story-footer"><x-ui-icon name="shield" /> Pengelolaan data sesuai hak akses.</div>
        </section>
        <section class="login-form-panel" aria-labelledby="login-title">
            <div class="login-form-wrap">
                <span class="login-welcome-icon"><x-ui-icon name="briefcase" /></span>
                <span class="login-form-eyebrow">SELAMAT DATANG KEMBALI</span>
                <h2 id="login-title">Masuk ke ruang kerja</h2>
                <p class="login-subtitle">Gunakan akun Anda untuk melanjutkan.</p>
                <div id="loginFeedback" class="login-feedback" role="alert" hidden></div>
                <form id="formLogin" method="POST" action="{{ url('loginProses') }}">
                    @csrf
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="nama@instansi.go.id" autocomplete="username" required>
                    <label for="password">Kata sandi</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                    <button type="submit" id="btnLogin">Masuk <span aria-hidden="true">&rarr;</span></button>
                    <button type="button" id="btnLoginLoading" class="login-loading" style="display:none;" disabled><span class="login-spinner" aria-hidden="true"></span> Memproses...</button>
                </form>
                <div class="login-help">Belum memiliki akun atau lupa kata sandi?<br><strong>Hubungi administrator instansi Anda.</strong></div>
                <p class="login-form-footer">JABFUNG &middot; Manajemen Kepegawaian Terpadu</p>
            </div>
        </section>
    </main>
    <script src="{{ asset('skydash/js/axios/dist/axios.min.js') }}"></script>
    <script>
        const formLogin = document.getElementById('formLogin');
        formLogin.onsubmit = function (event) {
            event.preventDefault();
            const button = document.getElementById('btnLogin');
            const loading = document.getElementById('btnLoginLoading');
            const feedback = document.getElementById('loginFeedback');
            feedback.hidden = true;
            button.disabled = true;
            button.style.display = 'none';
            loading.style.display = 'flex';
            axios.post(formLogin.action, new FormData(formLogin), { headers: { 'Accept': 'application/json' } })
                .then(function (response) {
                    if (response.data.responCode === 1) {
                        window.location.assign({{ \Illuminate\Support\Js::from(url('dashboard')) }});
                    } else {
                        feedback.textContent = response.data.respon || 'Email atau kata sandi tidak sesuai.';
                        feedback.hidden = false;
                    }
                })
                .catch(function (error) {
                    feedback.textContent = error.response && error.response.status === 419
                        ? 'Sesi telah berakhir. Muat ulang halaman lalu coba lagi.'
                        : 'Tidak dapat masuk. Periksa koneksi dan akun Anda, lalu coba lagi.';
                    feedback.hidden = false;
                })
                .finally(function () {
                    button.disabled = false;
                    button.style.display = 'flex';
                    loading.style.display = 'none';
                });
        };
    </script>
</body>
</html>
