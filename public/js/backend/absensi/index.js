(function () {
    'use strict';
    var config = window.absensiConfig;
    if (!config) return;
    var form = document.getElementById('absensiForm');
    var month = document.getElementById('absensi-bulan');
    var totals = ['total_izin', 'total_cuti', 'total_dinas', 'total_kehadiran'];

    function updateLimit() {
        var parts = month.value.split('-');
        if (parts.length !== 2) return;
        var days = new Date(Number(parts[0]), Number(parts[1]), 0).getDate();
        form.querySelectorAll('.absensi-total').forEach(function (input) { input.max = days; });
    }
    month.addEventListener('change', updateLimit);
    $('#absensiModal').on('show.bs.modal', function (event) {
        // Validation redirects preserve the submitted values when reopening without a trigger.
        if (!event.relatedTarget) { updateLimit(); return; }
        var id = event.relatedTarget.getAttribute('data-id');
        var row = id ? config.rows[id] : null;
        form.reset();
        document.getElementById('absensi-id').value = row ? row.id : '';
        document.getElementById('absensiMethod').value = row ? 'PUT' : 'POST';
        form.action = row ? config.updateUrl.replace('__ID__', row.id) : config.storeUrl;
        document.getElementById('absensiModalTitle').textContent = row ? 'Edit Absensi' : 'Tambah Absensi';
        document.getElementById('absensi-user').value = row ? row.user_id : '';
        month.value = row ? row.bulan : config.bulan;
        ['total_terlambat', 'total_pulang_cepat'].forEach(function (key) {
            document.getElementById('absensi-' + key).value = row ? row[key] : '00:00:00';
        });
        var legacy = document.getElementById('absensi-legacy');
        legacy.hidden = !(row && row.perlu_penyesuaian);
        legacy.textContent = row && row.perlu_penyesuaian
            ? 'Data lama: terlambat ' + row.lama_terlambat_hari + ' hari, pulang cepat ' + row.lama_pulang_cepat_hari + ' hari, cuti/izin/dinas gabungan ' + row.lama_cuti_izin_dinas + ' hari. Isi durasi dan pisahkan jumlah hari berdasarkan rekap asli, lalu simpan.'
            : '';
        totals.forEach(function (key) { document.getElementById('absensi-' + key).value = row ? row[key] : 0; });
        document.getElementById('absensiSubmit').disabled = false;
        updateLimit();
    });
    form.addEventListener('submit', function () { document.getElementById('absensiSubmit').disabled = true; });
    document.querySelectorAll('.absensi-delete-form').forEach(function (deleteForm) {
        deleteForm.addEventListener('submit', function (event) {
            if (!window.confirm('Hapus catatan absensi ini?')) event.preventDefault();
        });
    });
    if (config.hasErrors) $('#absensiModal').modal('show');
})();
