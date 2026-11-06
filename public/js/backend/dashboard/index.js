(function () {
    'use strict';
    var data = window.dashboardCharts;
    if (!data || typeof Chart === 'undefined') return;

    var colors = ['#087f72', '#6b88bd', '#a9c9a5', '#d7ae67', '#94b7c3', '#a595bb', '#c9a694', '#647b90'];
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
                legend: { display: round, position: 'bottom', labels: { boxWidth: 10, padding: 16, fontSize: 11, fontColor: '#718096' } },
                scales: round ? {} : {
                    yAxes: [{ ticks: { beginAtZero: true, precision: id === 'trainingChart' ? 0 : 2 } }],
                    xAxes: [{ gridLines: { display: false } }]
                },
                tooltips: { intersect: round, mode: round ? 'nearest' : 'index' }
            }
        });
    }
    render('educationChart', 'doughnut', data.education.labels, data.education.values, 'Riwayat pendidikan');
    render('trainingChart', 'bar', data.years, data.training, 'Jumlah diklat', '#6b88bd');
    render('creditChart', 'line', data.years, data.credits, 'Total angka kredit', '#087f72');
})();
