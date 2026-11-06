(function () {
    'use strict';
    var sidebar = document.getElementById('sidebar');
    var backdrop = document.querySelector('.sidebar-backdrop');
    var toggle = document.querySelector('[data-toggle="offcanvas"]');
    if (sidebar && backdrop && toggle) {
        function syncMenu() {
            if (window.innerWidth < 992) document.body.classList.remove('sidebar-icon-only');
            var open = sidebar.classList.contains('active') && window.innerWidth < 992;
            sidebar.inert = window.innerWidth < 992 && !open;
            sidebar.setAttribute('aria-hidden', String(window.innerWidth < 992 && !open));
            backdrop.hidden = !open;
            toggle.setAttribute('aria-expanded', String(open));
            document.body.classList.toggle('menu-is-open', open);
        }
        new MutationObserver(syncMenu).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        function closeMenu() { sidebar.classList.remove('active'); syncMenu(); toggle.focus(); }
        backdrop.addEventListener('click', closeMenu);
        document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && !backdrop.hidden) closeMenu(); });
        window.addEventListener('resize', function () { if (window.innerWidth >= 992) sidebar.classList.remove('active'); syncMenu(); });
        syncMenu();
    }
    // Keep labels on table cards in sync after DataTables sorting, paging and AJAX reloads.
    function labelTables() {
        document.querySelectorAll('.content-wrapper table').forEach(function (table) {
            var headers = Array.from(table.querySelectorAll('thead th')).map(function (cell) { return cell.textContent.trim(); });
            table.querySelectorAll('tbody tr').forEach(function (row) {
                Array.from(row.children).forEach(function (cell, index) {
                    if (cell.colSpan === 1 && !cell.hasAttribute('data-label') && headers[index]) cell.setAttribute('data-label', headers[index]);
                });
            });
        });
    }
    labelTables();
    if (window.jQuery) jQuery(document).on('draw.dt', labelTables);
})();
