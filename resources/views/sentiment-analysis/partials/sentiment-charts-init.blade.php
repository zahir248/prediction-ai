<script>
(function () {
    window.initSentimentReportChartsFromDom = function (root) {
        if (!root) return;
        function destroyOnCanvas(canvas) {
            if (typeof Chart === 'undefined') return;
            var existing = Chart.getChart(canvas);
            if (existing) existing.destroy();
        }
        function buildChart(canvas, cfg) {
            if (!cfg || !cfg.type) return;
            destroyOnCanvas(canvas);
            var ctx = canvas.getContext('2d');
            var common = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } };
            if (cfg.type === 'barPolarity' || cfg.type === 'barVolume') {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: cfg.labels || [],
                        datasets: [{
                            label: cfg.type === 'barVolume' ? 'Volume (est.)' : 'Polarity',
                            data: cfg.values || [],
                            backgroundColor: ['#667eea', '#764ba2'],
                            borderRadius: 6
                        }]
                    },
                    options: Object.assign({}, common, { scales: { y: { beginAtZero: true, max: 100 } } })
                });
                return;
            }
            if (cfg.type === 'groupedValence') {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: cfg.labels || [],
                        datasets: [
                            { label: cfg.labelPos || 'Positive', data: cfg.positive || [], backgroundColor: '#22c55e', borderRadius: 4 },
                            { label: cfg.labelNeu || 'Neutral', data: cfg.neutral || [], backgroundColor: '#94a3b8', borderRadius: 4 },
                            { label: cfg.labelNeg || 'Negative', data: cfg.negative || [], backgroundColor: '#ef4444', borderRadius: 4 }
                        ]
                    },
                    options: Object.assign({}, common, { scales: { y: { beginAtZero: true, max: 100 } } })
                });
                return;
            }
            if (cfg.type === 'barTrend' && cfg.rows && cfg.rows.length) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: cfg.rows.map(function (r) { return r.label; }),
                        datasets: [
                            { label: cfg.labelA || 'A', data: cfg.rows.map(function (r) { return r.a; }), backgroundColor: '#667eea', borderRadius: 4 },
                            { label: cfg.labelB || 'B', data: cfg.rows.map(function (r) { return r.b; }), backgroundColor: '#764ba2', borderRadius: 4 }
                        ]
                    },
                    options: Object.assign({}, common, { scales: { y: { beginAtZero: true, max: 100 } } })
                });
                return;
            }
            if (cfg.type === 'horizontalTopics' && cfg.rows && cfg.rows.length) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: cfg.rows.map(function (r) { return r.topic; }),
                        datasets: [
                            { label: cfg.labelA || 'A', data: cfg.rows.map(function (r) { return r.a; }), backgroundColor: '#667eea', borderRadius: 4 },
                            { label: cfg.labelB || 'B', data: cfg.rows.map(function (r) { return r.b; }), backgroundColor: '#764ba2', borderRadius: 4 }
                        ]
                    },
                    options: Object.assign({}, common, { indexAxis: 'y', scales: { x: { beginAtZero: true, max: 100 } } })
                });
                return;
            }
            if (cfg.type === 'horizontalDrivers' && cfg.rows && cfg.rows.length) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: cfg.rows.map(function (r) { return r.driver; }),
                        datasets: [
                            { label: cfg.labelA || 'A', data: cfg.rows.map(function (r) { return r.a; }), backgroundColor: '#0ea5e9', borderRadius: 4 },
                            { label: cfg.labelB || 'B', data: cfg.rows.map(function (r) { return r.b; }), backgroundColor: '#a855f7', borderRadius: 4 }
                        ]
                    },
                    options: Object.assign({}, common, { indexAxis: 'y', scales: { x: { beginAtZero: true, max: 100 } } })
                });
            }
        }
        function run() {
            root.querySelectorAll('.sentiment-chart-canvas').forEach(function (canvas) {
                var raw = canvas.getAttribute('data-config');
                if (!raw) return;
                try {
                    var cfg = JSON.parse(raw);
                    buildChart(canvas, cfg);
                } catch (e) { /* ignore */ }
            });
        }
        if (typeof Chart !== 'undefined') {
            run();
            return;
        }
        var existing = document.querySelector('script[data-sentiment-chartjs]');
        if (existing) {
            existing.addEventListener('load', run);
            return;
        }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
        s.async = true;
        s.setAttribute('data-sentiment-chartjs', '1');
        s.onload = run;
        document.head.appendChild(s);
    };
})();
</script>
