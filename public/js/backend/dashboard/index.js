(function () {
    'use strict';
    var data = window.dashboardCharts;
    if (!data || typeof Chart === 'undefined') return;

    var colors = ['#4b49ac', '#248afd', '#57b657', '#ffc100', '#f3797e', '#6c5ce7', '#17a2b8', '#855f42'];
    function render(id, type, labels, values, label, color) {
        var canvas = document.getElementById(id);
        if (!canvas) return;
        var round = type === 'doughnut';
        new Chart(canvas.getContext('2d'), {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    backgroundColor: round ? labels.map(function (_, i) { return colors[i % colors.length]; }) : color,
                    borderColor: round ? '#ffffff' : color,
                    borderWidth: round ? 2 : 3,
                    fill: false,
                    lineTension: 0,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: round, position: 'bottom' },
                scales: round ? {} : {
                    yAxes: [{ ticks: { beginAtZero: true, precision: id === 'trainingChart' ? 0 : 2 } }],
                    xAxes: [{ gridLines: { display: false } }]
                },
                tooltips: { intersect: round, mode: round ? 'nearest' : 'index' }
            }
        });
    }
    render('educationChart', 'doughnut', data.education.labels, data.education.values, 'Riwayat pendidikan');
    render('trainingChart', 'bar', data.years, data.training, 'Jumlah diklat', '#248afd');
    render('creditChart', 'line', data.years, data.credits, 'Total angka kredit', '#4b49ac');
})();
