(function () {
    'use strict';
    var config = window.evaluasiConfig;
    if (!config) return;
    var form = document.getElementById('evaluasiForm');
    $('#evaluasiModal').on('show.bs.modal', function (event) {
        if (!event.relatedTarget) return;
        var id = event.relatedTarget.getAttribute('data-id');
        var row = id ? config.rows[id] : null;
        form.reset();
        form.action = row ? config.updateUrl.replace('__ID__', row.id) : config.storeUrl;
        document.getElementById('evaluasi-id').value = row ? row.id : '';
        document.getElementById('evaluasiMethod').value = row ? 'PUT' : 'POST';
        document.getElementById('evaluasiModalTitle').textContent = row ? 'Edit Evaluasi Kinerja' : 'Tambah Evaluasi Kinerja';
        document.getElementById('evaluasi-user').value = row ? row.user_id : '';
        document.getElementById('evaluasi-nilai').value = row ? row.nilai : '';
        document.getElementById('evaluasi-keterangan').value = row ? row.keterangan || '' : '';
        document.getElementById('evaluasiSubmit').disabled = false;
    });
    form.addEventListener('submit', function () { document.getElementById('evaluasiSubmit').disabled = true; });
    document.querySelectorAll('.evaluasi-delete-form').forEach(function (deleteForm) {
        deleteForm.addEventListener('submit', function (event) {
            if (!window.confirm('Hapus evaluasi kinerja ini?')) event.preventDefault();
        });
    });
    if (config.hasErrors) $('#evaluasiModal').modal('show');
})();
