<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Leaflet.js for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .dashboard-container {
        background: #ffffff;
        min-height: 100%;
        padding: 0;
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    
    .dashboard-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .dashboard-title {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 8px 0;
    }
    
    .dashboard-subtitle {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }
    
    .insights-section {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .insights-summary {
        color: #374151;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 16px;
    }
    
    .key-findings {
        margin-top: 16px;
    }
    
    .key-findings-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 12px;
    }
    
    .key-findings-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .key-findings-list li {
        padding: 8px 0;
        padding-left: 24px;
        position: relative;
        color: #4b5563;
        font-size: 13px;
    }
    
    .key-findings-list li:before {
        content: "•";
        position: absolute;
        left: 8px;
        color: #667eea;
        font-weight: bold;
        font-size: 16px;
    }
    
    .dashboard-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
        padding: 16px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
    }
    
    .filter-label {
        font-size: 11px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .filter-select {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        background: #ffffff;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .filter-select:hover {
        border-color: #667eea;
    }
    
    .filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .filter-clear-btn {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        background: #f3f4f6;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        width: 100%;
        font-weight: 500;
        box-sizing: border-box;
        line-height: 1.5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    
    .filter-clear-btn i {
        display: inline-flex;
        align-items: center;
        line-height: 1;
    }
    
    .filter-clear-btn:hover {
        background: #e5e7eb;
        border-color: #d1d5db;
    }
    
    .metrics-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .metric-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
    }
    
    .metric-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .metric-value {
        font-size: 32px;
        font-weight: 700;
        color: #111827;
    }
    
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .chart-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    .chart-item.hidden {
        display: none;
    }
    
    .chart-item.filtered-out {
        opacity: 0.3;
        transform: scale(0.95);
        pointer-events: none;
    }
    
    .chart-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .chart-description {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 16px;
        font-style: italic;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .map-container {
        position: relative;
        height: 500px;
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    
    .map-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        grid-column: 1 / -1;
    }
    
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 16px;
        }
        
        .charts-grid {
            grid-template-columns: 1fr;
        }
        
        .dashboard-filters {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-container">
    <!-- AI Insights Section -->
    @if(!empty($dashboardData['summary']) || !empty($dashboardData['key_findings']))
    <div class="insights-section">
        @if(!empty($dashboardData['summary']))
        <div class="insights-summary">
            <strong>Summary:</strong> {{ $dashboardData['summary'] }}
        </div>
        @endif
        
        @if(!empty($dashboardData['key_findings']) && is_array($dashboardData['key_findings']))
        <div class="key-findings">
            <div class="key-findings-title">Key Findings:</div>
            <ul class="key-findings-list">
                @foreach($dashboardData['key_findings'] as $finding)
                    <li>{{ is_array($finding) ? json_encode($finding) : $finding }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif

    <!-- Filters Section -->
    @if(!empty($dashboardData['filter_options']))
    <div class="dashboard-filters">
        @if(!empty($dashboardData['filter_options']['labels']))
        <div class="filter-group">
            <label class="filter-label">Category</label>
             <select class="filter-select" id="filterCategory">
                <option value="">All</option>
                @foreach($dashboardData['filter_options']['labels'] as $label)
                    <option value="{{ $label }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        @endif
        
        @if(!empty($dashboardData['filter_options']['datasets']))
        <div class="filter-group">
            <label class="filter-label">Dataset</label>
             <select class="filter-select" id="filterDataset">
                <option value="">All</option>
                @foreach($dashboardData['filter_options']['datasets'] as $dataset)
                    <option value="{{ $dataset }}">{{ $dataset }}</option>
                @endforeach
            </select>
        </div>
        @endif
        
        @if(!empty($dashboardData['filter_options']['categories']))
        <div class="filter-group">
            <label class="filter-label">Chart Type</label>
             <select class="filter-select" id="filterChartType">
                <option value="">All</option>
                @foreach($dashboardData['filter_options']['categories'] as $category)
                    <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                @endforeach
            </select>
        </div>
        @endif
        
        <div class="filter-group">
            <label class="filter-label" style="opacity: 0;">Clear</label>
            <button id="clearFiltersBtn" class="filter-clear-btn">
                <i class="bi bi-x-circle" style="font-size: 13px; vertical-align: middle;"></i>Clear
            </button>
        </div>
    </div>
    @endif

    <!-- Metrics Section -->
    @if(!empty($dashboardData['metrics']))
    <div class="metrics-section">
        @foreach($dashboardData['metrics'] as $metricName => $metricValue)
        <div class="metric-card">
            <div class="metric-label">{{ $metricName }}</div>
            <div class="metric-value">{{ number_format($metricValue) }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Charts Section -->
    @if(!empty($dashboardData['chart_configs']) && count($dashboardData['chart_configs']) > 0)
    <div class="charts-grid">
        @foreach($dashboardData['chart_configs'] as $index => $chart)
            @php
                $chartTitle = $chart['title'] ?? 'Chart ' . ($index + 1);
                $chartDescription = $chart['description'] ?? '';
                $chartType = $chart['type'] ?? 'bar';
                $isMapChart = stripos($chartTitle, 'map') !== false 
                    || stripos($chartType, 'map') !== false
                    || stripos($chartDescription, 'map') !== false;
            @endphp
            
            @if($isMapChart)
            <div class="map-card chart-item" 
                 data-chart-index="{{ $index }}"
                 data-chart-type="{{ strtolower($chartType) }}"
                 data-chart-title="{{ strtolower($chartTitle) }}"
                 data-chart-labels="{{ json_encode($chart['data']['labels'] ?? []) }}"
                 data-chart-datasets="{{ json_encode(array_column($chart['data']['datasets'] ?? [], 'label')) }}">
                <div class="chart-title">{{ $chartTitle }}</div>
                @if(!empty($chartDescription))
                <div class="chart-description">{{ $chartDescription }}</div>
                @endif
                <div class="map-container" id="map-{{ $index }}-{{ $dataAnalysis->id }}"></div>
            </div>
            @else
            <div class="chart-card chart-item" 
                 data-chart-index="{{ $index }}"
                 data-chart-type="{{ strtolower($chartType) }}"
                 data-chart-title="{{ strtolower($chartTitle) }}"
                 data-chart-labels="{{ json_encode($chart['data']['labels'] ?? []) }}"
                 data-chart-datasets="{{ json_encode(array_column($chart['data']['datasets'] ?? [], 'label')) }}">
                <div class="chart-title">{{ $chartTitle }}</div>
                @if(!empty($chartDescription))
                <div class="chart-description">{{ $chartDescription }}</div>
                @endif
                <div class="chart-container">
                    <canvas id="chart-{{ $index }}-{{ $dataAnalysis->id }}"></canvas>
                </div>
            </div>
            @endif
        @endforeach
    </div>
    @else
    <div class="chart-card">
        <div class="chart-title">No Charts Available</div>
        <div style="padding: 40px; text-align: center; color: #64748b;">
            <p>No charts were generated for this analysis.</p>
        </div>
    </div>
    @endif
</div>

<script>
    // Dashboard data
    const dashboardData = @json($dashboardData);
    const chartConfigs = @json($dashboardData['chart_configs'] ?? []);
    const analysisId = {{ $dataAnalysis->id }};
    
    // Store chart instances globally to prevent duplicates
    if (!window.dashboardChartInstances) {
        window.dashboardChartInstances = {};
    }
    if (!window.dashboardMapInstances) {
        window.dashboardMapInstances = {};
    }

    // Destroy all existing charts and maps
    function destroyAllCharts() {
        // Destroy chart instances
        Object.keys(window.dashboardChartInstances).forEach(key => {
            const chart = window.dashboardChartInstances[key];
            if (chart && typeof chart.destroy === 'function') {
                try {
                    chart.destroy();
                } catch (e) {
                    console.warn('Error destroying chart:', e);
                }
            }
        });
        window.dashboardChartInstances = {};
        
        // Destroy map instances
        Object.keys(window.dashboardMapInstances).forEach(key => {
            const map = window.dashboardMapInstances[key];
            if (map && typeof map.remove === 'function') {
                try {
                    map.remove();
                } catch (e) {
                    console.warn('Error destroying map:', e);
                }
            }
        });
        window.dashboardMapInstances = {};
    }

    // Initialize dashboard charts - make it global
    window.initializeDashboardCharts = function() {
        if (typeof Chart === 'undefined') {
            setTimeout(window.initializeDashboardCharts, 100);
            return;
        }

        // Destroy existing charts first
        destroyAllCharts();

        // Initialize all charts from chart configs
        chartConfigs.forEach((config, index) => {
            const chartTitle = config.title || 'Chart ' + (index + 1);
            const chartType = config.type || 'bar';
            const chartDescription = config.description || '';
            const isMapChart = chartTitle.toLowerCase().includes('map') 
                || chartType.toLowerCase() === 'map'
                || chartDescription.toLowerCase().includes('map');

            if (isMapChart) {
                initializeMapForChart(config, index);
            } else {
                initializeChart(config, index);
            }
        });
    }

    // Initialize a chart
    function initializeChart(config, index) {
        const chartId = 'chart-' + index + '-' + analysisId;
        const ctx = document.getElementById(chartId);
        if (!ctx) {
            console.warn('Chart canvas not found for index', index);
            return;
        }

        // Destroy existing chart if it exists
        if (window.dashboardChartInstances[chartId]) {
            try {
                window.dashboardChartInstances[chartId].destroy();
            } catch (e) {
                console.warn('Error destroying existing chart:', e);
            }
            delete window.dashboardChartInstances[chartId];
        }

        // Check if Chart.js has an existing instance on this canvas
        if (typeof Chart !== 'undefined' && Chart.getChart && Chart.getChart(ctx)) {
            try {
                Chart.getChart(ctx).destroy();
            } catch (e) {
                console.warn('Error destroying Chart.js instance:', e);
            }
        }

        const chartData = config.data || {};
        const labels = chartData.labels || [];
        const datasets = chartData.datasets || [];
        const chartType = config.type || 'bar';
        const validChartType = (chartType && chartType.toLowerCase() !== 'map') ? chartType : 'bar';

        // Validate data
        const hasLabels = labels.length > 0;
        const hasDatasets = datasets.length > 0;
        const hasDataInDatasets = datasets.some(ds => {
            const data = ds.data || [];
            return Array.isArray(data) && data.length > 0 && data.some(val => val !== null && val !== undefined && val !== '');
        });

        if (!hasLabels || !hasDatasets || !hasDataInDatasets) {
            ctx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;">No data available for this chart</div>';
            return;
        }

        // Enhanced color palette
        const colorPalettes = {
            pie: ['#8b5cf6', '#3b82f6', '#1e40af', '#f59e0b', '#ec4899', '#10b981', '#ef4444', '#06b6d4'],
            doughnut: ['#8b5cf6', '#3b82f6', '#1e40af', '#f59e0b', '#ec4899', '#10b981', '#ef4444', '#06b6d4'],
            bar: ['rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)'],
            line: ['rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)', 'rgba(16, 185, 129, 0.8)']
        };

        const backgroundColor = (validChartType === 'pie' || validChartType === 'doughnut')
            ? colorPalettes[validChartType] || colorPalettes.pie
            : (colorPalettes[validChartType] || colorPalettes.bar);

        try {
            const chartInstance = new Chart(ctx, {
                type: validChartType,
                data: {
                    labels: labels,
                    datasets: datasets.map((dataset, i) => ({
                        label: dataset.label || 'Data',
                        data: dataset.data || [],
                        backgroundColor: (validChartType === 'pie' || validChartType === 'doughnut')
                            ? backgroundColor
                            : (Array.isArray(backgroundColor) 
                                ? backgroundColor[i % backgroundColor.length]
                                : backgroundColor),
                        borderColor: (validChartType === 'pie' || validChartType === 'doughnut') 
                            ? '#fff' 
                            : (backgroundColor[i % backgroundColor.length] || '#667eea'),
                        borderWidth: (validChartType === 'pie' || validChartType === 'doughnut') ? 2 : 2,
                    })),
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: (validChartType === 'pie' || validChartType === 'doughnut') ? 'right' : 'top',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    scales: (validChartType !== 'pie' && validChartType !== 'doughnut') ? {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    } : undefined,
                },
            });
            
            // Store chart instance
            window.dashboardChartInstances[chartId] = chartInstance;
        } catch (error) {
            console.error('Error creating chart', index, error);
            ctx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #ef4444;">Error rendering chart: ' + error.message + '</div>';
        }
    }

    // Initialize map for chart
    async function initializeMapForChart(config, index) {
        if (typeof L === 'undefined') {
            setTimeout(() => initializeMapForChart(config, index), 100);
            return;
        }

        const mapId = 'map-' + index + '-' + analysisId;
        const mapContainer = document.getElementById(mapId);
        if (!mapContainer) {
            console.warn('Map container not found for index', index);
            return;
        }

        // Destroy existing map if it exists
        if (window.dashboardMapInstances[mapId]) {
            try {
                window.dashboardMapInstances[mapId].remove();
            } catch (e) {
                console.warn('Error destroying existing map:', e);
            }
            delete window.dashboardMapInstances[mapId];
        }

        // Clear the map container
        mapContainer.innerHTML = '';

        // Initialize map
        const map = L.map(mapContainer).setView([20, 0], 2);
        
        // Store map instance
        window.dashboardMapInstances[mapId] = map;

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Extract location data from chart config
        const chartData = config.data || {};
        const labels = chartData.labels || [];
        const datasets = chartData.datasets || [];
        const dataValues = datasets.length > 0 ? datasets[0].data || [] : [];
        const coordinates = config.coordinates || null;

        if (labels.length > 0) {
            const markers = [];
            
            for (let i = 0; i < labels.length; i++) {
                const locationName = labels[i];
                const value = dataValues[i] || 0;
                let finalCoordinates = null;

                // Use coordinates from config if available
                if (coordinates && Array.isArray(coordinates) && coordinates[i]) {
                    finalCoordinates = coordinates[i];
                } else {
                    // Geocode location name
                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(locationName)}&limit=1`,
                            {
                                headers: {
                                    'User-Agent': 'DataAnalysisApp/1.0'
                                }
                            }
                        );
                        const data = await response.json();
                        if (data && data.length > 0) {
                            finalCoordinates = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                        }
                    } catch (error) {
                        console.warn('Geocoding failed for:', locationName, error);
                    }
                    
                    // Rate limiting
                    if (i < labels.length - 1) {
                        await new Promise(resolve => setTimeout(resolve, 1000));
                    }
                }

                if (finalCoordinates && Array.isArray(finalCoordinates) && finalCoordinates.length === 2) {
                    const lat = parseFloat(finalCoordinates[0]);
                    const lng = parseFloat(finalCoordinates[1]);
                    
                    if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                        const marker = L.marker([lat, lng]).addTo(map);
                        marker.bindPopup(`
                            <div style="text-align: center;">
                                <strong>${locationName}</strong><br>
                                <span style="color: #667eea; font-weight: 600;">Value: ${value}</span>
                            </div>
                        `);
                        markers.push(marker);
                    }
                }
            }

            // Fit map bounds to show all markers
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
    }

    // Apply filters to show/hide charts - make it global
    window.applyFilters = function() {
        const categoryFilter = document.getElementById('filterCategory')?.value || '';
        const datasetFilter = document.getElementById('filterDataset')?.value || '';
        const chartTypeFilter = document.getElementById('filterChartType')?.value || '';

        // Get all chart items
        const chartItems = document.querySelectorAll('.chart-item');
        let visibleCount = 0;

        chartItems.forEach((item) => {
            let shouldShow = true;

            // Filter by category (labels)
            if (categoryFilter) {
                try {
                    const chartLabels = JSON.parse(item.getAttribute('data-chart-labels') || '[]');
                    const categoryLower = categoryFilter.toLowerCase();
                    const hasMatchingLabel = chartLabels.some(label => 
                        String(label).toLowerCase().includes(categoryLower) ||
                        categoryLower.includes(String(label).toLowerCase())
                    );
                    if (!hasMatchingLabel) {
                        shouldShow = false;
                    }
                } catch (e) {
                    console.warn('Error parsing chart labels:', e);
                }
            }

            // Filter by dataset
            if (shouldShow && datasetFilter) {
                try {
                    const chartDatasets = JSON.parse(item.getAttribute('data-chart-datasets') || '[]');
                    const datasetLower = datasetFilter.toLowerCase();
                    const hasMatchingDataset = chartDatasets.some(ds => 
                        String(ds).toLowerCase().includes(datasetLower) ||
                        datasetLower.includes(String(ds).toLowerCase())
                    );
                    if (!hasMatchingDataset) {
                        shouldShow = false;
                    }
                } catch (e) {
                    console.warn('Error parsing chart datasets:', e);
                }
            }

            // Filter by chart type
            if (shouldShow && chartTypeFilter) {
                const chartType = item.getAttribute('data-chart-type') || '';
                const typeFilterLower = chartTypeFilter.toLowerCase();
                if (chartType !== typeFilterLower) {
                    shouldShow = false;
                }
            }

            // Show or hide the chart
            if (shouldShow) {
                item.classList.remove('hidden', 'filtered-out');
                item.style.display = '';
                visibleCount++;
            } else {
                item.classList.add('hidden');
                item.style.display = 'none';
            }
        });

        // Show message if no charts are visible
        const chartsGrid = document.querySelector('.charts-grid');
        if (chartsGrid) {
            let noResultsMsg = chartsGrid.querySelector('.no-results-message');
            
            if (visibleCount === 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'no-results-message';
                    noResultsMsg.style.cssText = 'grid-column: 1 / -1; padding: 40px; text-align: center; color: #64748b; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;';
                    noResultsMsg.innerHTML = '<p style="font-size: 16px; margin: 0;">No charts match the selected filters.</p><p style="font-size: 13px; margin-top: 8px; color: #9ca3af;">Try adjusting your filter selections or click "Clear Filters" to show all charts.</p>';
                    chartsGrid.appendChild(noResultsMsg);
                }
            } else {
                if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }
        }
    }

    // Clear all filters - make it global
    window.clearFilters = function() {
        const categorySelect = document.getElementById('filterCategory');
        const datasetSelect = document.getElementById('filterDataset');
        const chartTypeSelect = document.getElementById('filterChartType');
        
        if (categorySelect) categorySelect.value = '';
        if (datasetSelect) datasetSelect.value = '';
        if (chartTypeSelect) chartTypeSelect.value = '';
        
        // Show all charts
        const chartItems = document.querySelectorAll('.chart-item');
        chartItems.forEach((item) => {
            item.classList.remove('hidden', 'filtered-out');
            item.style.display = '';
        });
        
        // Remove no results message
        const noResultsMsg = document.querySelector('.no-results-message');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }

    // Attach event listeners to filter elements
    function attachFilterListeners() {
        const categorySelect = document.getElementById('filterCategory');
        const datasetSelect = document.getElementById('filterDataset');
        const chartTypeSelect = document.getElementById('filterChartType');
        const clearBtn = document.getElementById('clearFiltersBtn');
        
        if (categorySelect) {
            categorySelect.addEventListener('change', window.applyFilters);
        }
        if (datasetSelect) {
            datasetSelect.addEventListener('change', window.applyFilters);
        }
        if (chartTypeSelect) {
            chartTypeSelect.addEventListener('change', window.applyFilters);
        }
        if (clearBtn) {
            clearBtn.addEventListener('click', window.clearFilters);
        }
    }

    // Initialize charts when DOM is ready
    function initDashboard() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initializeDashboardCharts();
                attachFilterListeners();
            });
        } else {
            initializeDashboardCharts();
            attachFilterListeners();
        }
    }
    
    // Call initDashboard immediately (for AJAX loaded content)
    initDashboard();
</script>
