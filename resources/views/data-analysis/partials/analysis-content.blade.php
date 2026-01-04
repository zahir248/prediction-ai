<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Leaflet.js for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@if($dataAnalysis->status === 'completed')
<style>
    .analysis-container {
        background: #ffffff;
        min-height: 100%;
        padding: 0;
    }
    
    .analysis-content {
        padding: 0;
        max-width: 100%;
    }
    
    .charts-grid {
        display: flex;
        flex-direction: column;
        gap: 40px;
        margin-bottom: 40px;
    }
    
    .chart-item {
        padding: 0;
    }
    
    .chart-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
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
    
    @media (max-width: 768px) {
        .charts-grid {
            gap: 32px;
        }
    }
</style>

<div class="analysis-container">
    <div class="analysis-content">
        <!-- Charts Section -->
        @if($dataAnalysis->chart_configs && count($dataAnalysis->chart_configs) > 0)
            <div class="charts-grid">
                @foreach($dataAnalysis->chart_configs as $index => $chart)
                    @php
                        $chartTitle = $chart['title'] ?? 'Chart ' . ($index + 1);
                        $chartDescription = $chart['description'] ?? '';
                        $chartDataSource = $chart['data_source'] ?? '';
                        $chartType = $chart['type'] ?? '';
                        // Check if map: in title, type, description, or data_source
                        $isMapChart = stripos($chartTitle, 'map') !== false 
                            || stripos($chartType, 'map') !== false
                            || stripos($chartDescription, 'map') !== false
                            || stripos($chartDataSource, 'map') !== false;
                    @endphp
                    <div class="chart-item">
                        <div class="chart-title">{{ $chartTitle }}</div>
                        @if($isMapChart)
                            <div class="map-container" id="map-{{ $index }}-{{ $dataAnalysis->id }}"></div>
                        @else
                            <div class="chart-container">
                                <canvas id="chart-{{ $index }}-{{ $dataAnalysis->id }}"></canvas>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- No charts available -->
            <div class="chart-item">
                <div class="chart-title">No Charts Generated</div>
                <div style="padding: 40px 0; text-align: center; color: #6b7280;">
                    <p>Charts are being generated based on your insights. Please wait or try again.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Initialize Charts -->
<script>
    @if($dataAnalysis->chart_configs && count($dataAnalysis->chart_configs) > 0)
        // Function to geocode a location using Nominatim API
        async function geocodeLocation(locationName) {
            try {
                // Use Nominatim (OpenStreetMap geocoding service)
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
                    return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                }
            } catch (error) {
                console.warn('Geocoding failed for:', locationName, error);
            }
            return null;
        }
        
        // Function to initialize map for a chart
        async function initializeMapForChart(config, index, mapContainer) {
            if (typeof L === 'undefined') {
                console.error('Leaflet.js is not loaded');
                mapContainer.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;">Map library not loaded. Please refresh the page.</div>';
                return;
            }
            
            // Initialize map centered on world view
            const map = L.map(mapContainer).setView([20, 0], 2);
            
            // Add a loading indicator
            const loadingControl = L.control({position: 'topright'});
            loadingControl.onAdd = function() {
                const div = L.DomUtil.create('div', 'loading-indicator');
                div.innerHTML = '<div style="background: white; padding: 10px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">Loading locations...</div>';
                return div;
            };
            loadingControl.addTo(map);
            
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
            
            // Check if coordinates are provided in the chart config
            const coordinates = config.coordinates || null;
            
            // Get Excel data to check for lat/long columns
            const excelData = @json($dataAnalysis->excel_data ?? []);
            
            // Try to geocode locations and add markers
            if (labels.length > 0) {
                const markers = [];
                const locationData = labels.map((label, i) => ({
                    name: label,
                    value: dataValues[i] || 0,
                    coordinates: null
                }));
                
                // First, try to get coordinates from chart config
                if (coordinates && Array.isArray(coordinates) && coordinates.length === labels.length) {
                    locationData.forEach((location, i) => {
                        if (coordinates[i] && Array.isArray(coordinates[i]) && coordinates[i].length === 2) {
                            location.coordinates = [parseFloat(coordinates[i][0]), parseFloat(coordinates[i][1])];
                        }
                    });
                }
                
                // If coordinates not in config, try to find lat/long in Excel data
                if (locationData.some(loc => !loc.coordinates) && excelData && excelData.sheets) {
                    // Look for latitude/longitude columns in Excel data
                    excelData.sheets.forEach(sheet => {
                        if (!sheet.headers || !sheet.rows) return;
                        
                        // Find lat/long column indices
                        const latIndex = sheet.headers.findIndex(h => 
                            ['latitude', 'lat', 'y', 'coord_y'].includes(h.toLowerCase().trim())
                        );
                        const lngIndex = sheet.headers.findIndex(h => 
                            ['longitude', 'lng', 'lon', 'long', 'x', 'coord_x'].includes(h.toLowerCase().trim())
                        );
                        
                        if (latIndex !== -1 && lngIndex !== -1) {
                            // Find label column index
                            const labelIndex = sheet.headers.findIndex(h => 
                                locationData.some(loc => 
                                    loc.name.toLowerCase().trim() === String(h).toLowerCase().trim()
                                )
                            );
                            
                            // Try to match by row data
                            sheet.rows.forEach(row => {
                                const rowArray = Array.isArray(row) ? row : Object.values(row);
                                if (rowArray[latIndex] && rowArray[lngIndex]) {
                                    const lat = parseFloat(rowArray[latIndex]);
                                    const lng = parseFloat(rowArray[lngIndex]);
                                    
                                    if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                                        // Try to match this row with a location
                                        locationData.forEach(location => {
                                            if (!location.coordinates) {
                                                // Check if any column value matches the location name
                                                const matches = rowArray.some((cell, idx) => {
                                                    if (idx === latIndex || idx === lngIndex) return false;
                                                    return String(cell).toLowerCase().trim() === location.name.toLowerCase().trim();
                                                });
                                                
                                                if (matches) {
                                                    location.coordinates = [lat, lng];
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
                
                // Process each location - use coordinates if available, otherwise geocode
                for (let i = 0; i < locationData.length; i++) {
                    const location = locationData[i];
                    let finalCoordinates = location.coordinates;
                    
                    // If no coordinates found, geocode the location name
                    if (!finalCoordinates) {
                        finalCoordinates = await geocodeLocation(location.name);
                        
                        // Add a small delay between requests to respect rate limits (1 request per second)
                        if (i < locationData.length - 1) {
                            await new Promise(resolve => setTimeout(resolve, 1000));
                        }
                    }
                    
                    if (finalCoordinates && Array.isArray(finalCoordinates) && finalCoordinates.length === 2) {
                        // Validate coordinates
                        const lat = parseFloat(finalCoordinates[0]);
                        const lng = parseFloat(finalCoordinates[1]);
                        
                        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                            // Create marker with popup
                            const marker = L.marker([lat, lng], {
                                draggable: false
                            }).addTo(map);
                            
                            marker.bindPopup(`
                                <div style="text-align: center;">
                                    <strong>${location.name}</strong><br>
                                    <span style="color: #667eea; font-weight: 600;">Value: ${location.value}</span>
                                </div>
                            `);
                            
                            markers.push(marker);
                        } else {
                            console.warn('Invalid coordinates for location:', location.name, finalCoordinates);
                        }
                    } else {
                        console.warn('Could not find coordinates for location:', location.name);
                    }
                }
                
                // Remove loading indicator
                map.removeControl(loadingControl);
                
                // Fit map bounds to show all markers
                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                } else {
                    // No markers found, show default world view
                    map.setView([20, 0], 2);
                    const errorControl = L.control({position: 'topright'});
                    errorControl.onAdd = function() {
                        const div = L.DomUtil.create('div', 'error-indicator');
                        div.innerHTML = '<div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">Could not find locations</div>';
                        return div;
                    };
                    errorControl.addTo(map);
                }
            } else {
                // No location data, show default world view
                map.setView([20, 0], 2);
            }
        }
        
        // Wait for Chart.js to be available
        function initializeCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(initializeCharts, 100);
                return;
            }
            
            const chartConfigs_{{ $dataAnalysis->id }} = @json($dataAnalysis->chart_configs);
            
            chartConfigs_{{ $dataAnalysis->id }}.forEach((config, index) => {
                const chartTitle = config.title || 'Chart ' + (index + 1);
                const chartType = config.type || 'bar';
                const chartDescription = config.description || '';
                const chartDataSource = config.data_source || '';
                // Check if map: in title, type, description, or data_source (matching input insights check)
                const isMapChart = chartTitle.toLowerCase().includes('map') 
                    || chartType.toLowerCase() === 'map'
                    || chartDescription.toLowerCase().includes('map')
                    || chartDataSource.toLowerCase().includes('map');
                
                // Handle map visualization
                if (isMapChart) {
                    const mapContainer = document.getElementById('map-' + index + '-{{ $dataAnalysis->id }}');
                    if (!mapContainer) {
                        console.warn('Map container not found for index', index);
                        return;
                    }
                    
                    // Wait for Leaflet to be available, then initialize map
                    if (typeof L === 'undefined') {
                        setTimeout(() => {
                            initializeMapForChart(config, index, mapContainer).catch(err => {
                                console.error('Error initializing map:', err);
                                mapContainer.innerHTML = '<div style="padding: 40px; text-align: center; color: #ef4444;">Error loading map. Please refresh the page.</div>';
                            });
                        }, 100);
                        return;
                    }
                    
                    initializeMapForChart(config, index, mapContainer).catch(err => {
                        console.error('Error initializing map:', err);
                        mapContainer.innerHTML = '<div style="padding: 40px; text-align: center; color: #ef4444;">Error loading map. Please refresh the page.</div>';
                    });
                    return; // Skip chart initialization for map
                }
                
                // Handle regular charts
                const ctx = document.getElementById('chart-' + index + '-{{ $dataAnalysis->id }}');
                if (!ctx) {
                    console.warn('Chart canvas not found for index', index);
                    return;
                }

                // Ensure chart type is valid (not 'map')
                const validChartType = (chartType && chartType.toLowerCase() !== 'map') ? chartType : 'bar';
                const chartData = config.data || {};
                const labels = chartData.labels || [];
                const datasets = chartData.datasets || [];
                
                // Validate data - check if we have labels and at least one dataset with data
                const hasLabels = labels.length > 0;
                const hasDatasets = datasets.length > 0;
                const hasDataInDatasets = datasets.some(ds => {
                    const data = ds.data || [];
                    return Array.isArray(data) && data.length > 0 && data.some(val => val !== null && val !== undefined && val !== '');
                });
                
                if (!hasLabels || !hasDatasets || !hasDataInDatasets) {
                    console.warn('No data for chart', index, config.title, {
                        hasLabels,
                        hasDatasets,
                        hasDataInDatasets,
                        labelsCount: labels.length,
                        datasetsCount: datasets.length,
                        datasets: datasets.map(ds => ({
                            label: ds.label,
                            dataLength: (ds.data || []).length,
                            data: ds.data
                        }))
                    });
                    ctx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;">No data available for this chart</div>';
                    return;
                }
                
                // Enhanced color palette for better visualization
                const colorPalettes = {
                    pie: ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'],
                    bar: ['rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)'],
                    line: ['rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)', 'rgba(16, 185, 129, 0.8)']
                };
                
                const backgroundColor = validChartType === 'pie' 
                    ? colorPalettes.pie
                    : (colorPalettes[validChartType] || colorPalettes.bar);
                
                try {
                    new Chart(ctx, {
                        type: validChartType,
                        data: {
                            labels: labels,
                            datasets: datasets.map((dataset, i) => ({
                                label: dataset.label || 'Data',
                                data: dataset.data || [],
                                backgroundColor: validChartType === 'pie'
                                    ? backgroundColor
                                    : (Array.isArray(backgroundColor) 
                                        ? backgroundColor[i % backgroundColor.length]
                                        : backgroundColor),
                                borderColor: validChartType === 'pie' ? '#fff' : (backgroundColor[i % backgroundColor.length] || '#667eea'),
                                borderWidth: validChartType === 'pie' ? 2 : 2,
                            })),
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: validChartType === 'pie' ? 'right' : 'top',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                title: {
                                    display: false,
                                },
                            },
                            scales: validChartType !== 'pie' ? {
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
                } catch (error) {
                    console.error('Error creating chart', index, error);
                    ctx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #ef4444;">Error rendering chart: ' + error.message + '</div>';
                }
            });
        }
        
        // Initialize charts when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeCharts);
        } else {
            initializeCharts();
        }
    @endif
</script>
@elseif($dataAnalysis->status === 'failed')
    <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
        <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">Analysis Failed</h3>
        <p style="margin: 0;">{{ $dataAnalysis->error_message ?? 'An error occurred during analysis.' }}</p>
    </div>
@endif
