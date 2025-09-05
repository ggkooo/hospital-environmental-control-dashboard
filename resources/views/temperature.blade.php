<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('temperature.page_title') }}</title>
    <!-- ChartJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ChartJS Zoom/Pan Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1/dist/chartjs-plugin-zoom.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 95%;
            max-width: none;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 32px;
        }
        .content-container {
            max-height: 60vh;
            overflow-y: auto;
            overflow-x: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container mb-3 mt-3">
        <h2>{{ __('temperature.title') }}</h2>
        <form id="temperatureFilterForm" class="row g-3 align-items-end mb-4" style="justify-content:center;">
            <div class="col-auto">
                <label for="startDate" class="form-label">{{ __('temperature.start_date') }}</label>
                <input type="date" class="form-control" id="startDate" name="startDate">
            </div>
            <div class="col-auto">
                <label for="startTime" class="form-label">{{ __('temperature.start_time') }}</label>
                <input type="time" class="form-control" id="startTime" name="startTime">
            </div>
            <div class="col-auto">
                <label for="endDate" class="form-label">{{ __('temperature.end_date') }}</label>
                <input type="date" class="form-control" id="endDate" name="endDate">
            </div>
            <div class="col-auto">
                <label for="endTime" class="form-label">{{ __('temperature.end_time') }}</label>
                <input type="time" class="form-control" id="endTime" name="endTime">
            </div>
            <div class="col-auto">
                <label for="aggregation" class="form-label">{{ __('temperature.unity') }}</label>
                <select class="form-control" id="aggregation" name="aggregation">
                    <option value="minute">{{ __('temperature.unity_minute') }}</option>
                    <option value="hour">{{ __('temperature.unity_hour') }}</option>
                    <option value="day">{{ __('temperature.unity_day') }}</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">{{ __('temperature.filter') }}</button>
            </div>
        </form>
    </div>

    <div id="minMaxInfo" class="container" style="text-align:center; margin-bottom:16px; font-size:1.1em; color:#333; display:none;"></div>

    <div class="container content-container" style="overflow-x:auto; max-height: 60vh; overflow-y: auto;">
        <canvas id="temperatureChart" style="height:350px; min-width:600px;"></canvas>
    </div>
    <script>
        // Textos para tradução
        const tempTexts = {
            filterError1: "{{ __('temperature.filter_error_1') }}",
            filterError2: "{{ __('temperature.filter_error_2') }}",
            noData: "{{ __('temperature.no_data') }}",
            minLabel: "{{ __('temperature.lowest') }}",
            maxLabel: "{{ __('temperature.highest') }}",
            avgLabel: "{{ __('temperature.average') }}",
            legendTemperature: "{{ __('temperature.temperature') }} (°C)",
            legendMovingAvg: "{{ __('temperature.moving_avg') }}",
            legendMovingMin: "{{ __('temperature.moving_min') }}",
            legendMovingMax: "{{ __('temperature.moving_max') }}",
            legendTrend: "{{ __('temperature.trend') }}",
            legendTooltipTemperature: "{{ __('temperature.legend_tooltip_temperature') }}",
            legendTooltipMovingAvg: "{{ __('temperature.legend_tooltip_moving_avg') }}",
            legendTooltipMovingMin: "{{ __('temperature.legend_tooltip_moving_min') }}",
            legendTooltipMovingMax: "{{ __('temperature.legend_tooltip_moving_max') }}",
            legendTooltipTrend: "{{ __('temperature.legend_tooltip_trend') }}",
        };

        // Helper to format time as HH:MM
        function formatTime(date) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        // Helper to format date as YYYY-MM-DD
        function formatDate(date) {
            return date.toISOString().slice(0, 10);
        }

        // Store all simulated data for filtering
        const maxPoints = 60;
        const totalSimulatedMinutes = 31 * 24 * 60; // 31 days
        const now = new Date();
        let allTimestamps = [];
        let allData = [];
        for (let i = totalSimulatedMinutes - 1; i >= 0; i--) {
            const d = new Date(now.getTime() - i * 60000);
            allTimestamps.push(d);
            // More random temperature: base 20-26°C, with random daily and minute variation
            const baseTemp = 20 + Math.random() * 6; // 20 to 26
            const dailyVariation = Math.sin(d.getDate() / 2) * 0.5;
            const minuteVariation = Math.random() * 0.5;
            const temp = baseTemp + dailyVariation + minuteVariation;
            allData.push(Number(temp.toFixed(2)));
        }
        let allLabels = allTimestamps.map(d => formatTime(d));
        // Helper to get point color based on temperature
        function getPointColors(dataArr) {
            return dataArr.map(v => (v < 21.0 || v > 24.0) ? 'red' : '#007bff');
        }
        // Initial chart data (live mode: last 60 points)
        let labels = allTimestamps.slice(-maxPoints).map(d => formatTime(d));
        let data = allData.slice(-maxPoints);
        let pointColors = getPointColors(data);

        const ctx = document.getElementById('temperatureChart').getContext('2d');
        function getPointRadius(numPoints) {
            if (numPoints > 200) return 0;
            if (numPoints > 48) return 3.5; // aumentada de 2 para 3.5
            return 7; // aumentada de 4 para 7
        }
        // Função para calcular média móvel simples
        function getMovingAverageArray(dataArr, windowSize = 2) {
            if (!dataArr.length) return [];
            let result = [];
            for (let i = 0; i < dataArr.length; i++) {
                let start = Math.max(0, i - windowSize + 1);
                let window = dataArr.slice(start, i + 1);
                let avg = window.reduce((sum, v) => sum + v, 0) / window.length;
                result.push(Number(avg.toFixed(2)));
            }
            return result;
        }
        // Mínima móvel
        function getMovingMinArray(dataArr, windowSize = 2) {
            if (!dataArr.length) return [];
            let result = [];
            for (let i = 0; i < dataArr.length; i++) {
                let start = Math.max(0, i - windowSize + 1);
                let window = dataArr.slice(start, i + 1);
                result.push(Math.min(...window));
            }
            return result;
        }
        // Máxima móvel
        function getMovingMaxArray(dataArr, windowSize = 2) {
            if (!dataArr.length) return [];
            let result = [];
            for (let i = 0; i < dataArr.length; i++) {
                let start = Math.max(0, i - windowSize + 1);
                let window = dataArr.slice(start, i + 1);
                result.push(Math.max(...window));
            }
            return result;
        }
        // Regressão linear simples
        function getLinearRegressionArray(dataArr) {
            if (!dataArr.length) return [];
            let n = dataArr.length;
            let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;
            for (let i = 0; i < n; i++) {
                sumX += i;
                sumY += dataArr[i];
                sumXY += i * dataArr[i];
                sumXX += i * i;
            }
            let slope = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
            let intercept = (sumY - slope * sumX) / n;
            let result = [];
            for (let i = 0; i < n; i++) {
                result.push(Number((slope * i + intercept).toFixed(2)));
            }
            return result;
        }
        // Detecta outliers (fora de 2 desvios padrão da média móvel)
        function getOutlierIndices(dataArr, windowSize = 2) {
            let avgArr = getMovingAverageArray(dataArr, windowSize);
            let minArr = getMovingMinArray(dataArr, windowSize);
            let maxArr = getMovingMaxArray(dataArr, windowSize);
            let outliers = [];
            for (let i = 0; i < dataArr.length; i++) {
                if (dataArr[i] < minArr[i] || dataArr[i] > maxArr[i]) {
                    outliers.push(i);
                }
            }
            return outliers;
        }
        const temperatureChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: tempTexts.legendTemperature,
                        data: data,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0,123,255,0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: getPointRadius(data.length),
                        pointBackgroundColor: (ctx) => {
                            let arr = data;
                            let outliers = getOutlierIndices(arr, 5);
                            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
                        },
                    },
                    {
                        label: tempTexts.legendMovingAvg,
                        data: getMovingAverageArray(data, 5),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.1)',
                        fill: false,
                        borderDash: [8, 4],
                        pointRadius: 0,
                        pointHoverRadius: 0,
                        tension: 0.3,
                        order: 0,
                    },
                    {
                        label: tempTexts.legendMovingMin,
                        data: getMovingMinArray(data, 5),
                        borderColor: '#17a2b8',
                        fill: false,
                        borderDash: [4,2],
                        pointRadius: 0,
                        pointHoverRadius: 0,
                        tension: 0.3,
                        order: 0,
                    },
                    {
                        label: tempTexts.legendMovingMax,
                        data: getMovingMaxArray(data, 5),
                        borderColor: '#e83e8c',
                        fill: false,
                        borderDash: [4,2],
                        pointRadius: 0,
                        pointHoverRadius: 0,
                        tension: 0.3,
                        order: 0,
                    },
                    {
                        label: tempTexts.legendTrend,
                        data: getLinearRegressionArray(data),
                        borderColor: '#6f42c1',
                        fill: false,
                        borderDash: [1,0],
                        pointRadius: 0,
                        pointHoverRadius: 0,
                        tension: 0,
                        order: 0,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            generateLabels: function(chart) {
                                const original = Chart.defaults.plugins.legend.labels.generateLabels;
                                const labels = original(chart);
                                // Tooltips customizados para cada legenda
                                const tooltips = {};
                                tooltips[tempTexts.legendTemperature] = tempTexts.legendTooltipTemperature;
                                tooltips[tempTexts.legendMovingAvg] = tempTexts.legendTooltipMovingAvg;
                                tooltips[tempTexts.legendMovingMin] = tempTexts.legendTooltipMovingMin;
                                tooltips[tempTexts.legendMovingMax] = tempTexts.legendTooltipMovingMax;
                                tooltips[tempTexts.legendTrend] = tempTexts.legendTooltipTrend;
                                return labels.map(label => ({
                                    ...label,
                                    title: tooltips[label.text] || '',
                                }));
                            }
                        },
                        onHover: function(e, legendItem, legend) {
                            if (legendItem && legendItem.title) {
                                e.native.target.style.cursor = 'pointer';
                                e.native.target.setAttribute('title', legendItem.title);
                            }
                        },
                        onLeave: function(e) {
                            e.native.target.style.cursor = 'default';
                            e.native.target.removeAttribute('title');
                        }
                    },
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: 'x',
                        },
                        zoom: {
                            enabled: true,
                            drag: false,
                            mode: 'x',
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 15,
                        max: 30,
                        title: {
                            display: true,
                            text: tempTexts.legendTemperature
                        },
                        ticks: {
                            stepSize: 0.1
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: '{{ __('temperature.time') }}'
                        },
                        ticks: {
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0,
                            maxTicksLimit: 20
                        }
                    }
                }
            },
        });
        // Function to add a new temperature value
        function addTemperatureValue(value) {
            const now = new Date();
            const timeLabel = formatTime(now);
            allLabels.push(timeLabel);
            allTimestamps.push(now);
            allData.push(value);
            // Se não estiver filtrado, mostrar só os últimos 60 pontos
            if (!isFiltered) {
                const startIdx = Math.max(0, allLabels.length - maxPoints);
                const liveLabels = allLabels.slice(startIdx);
                const liveData = allData.slice(startIdx);
                temperatureChart.data.labels = liveLabels;
                temperatureChart.data.datasets[0].data = liveData;
                temperatureChart.data.datasets[0].pointBackgroundColor = getPointColors(liveData);
                temperatureChart.data.datasets[0].pointRadius = getPointRadius(liveData.length);
                temperatureChart.data.datasets[1].data = getMovingAverageArray(liveData, 5);
                temperatureChart.data.datasets[2].data = getMovingMinArray(liveData, 5);
                temperatureChart.data.datasets[3].data = getMovingMaxArray(liveData, 5);
                temperatureChart.data.datasets[4].data = getLinearRegressionArray(liveData);
                temperatureChart.data.datasets[0].pointBackgroundColor = (ctx) => {
                    let arr = liveData;
                    let outliers = getOutlierIndices(arr, 5);
                    return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
                };
                temperatureChart.update();
                showLiveStats(); // Atualiza as estatísticas ao vivo
            }
        }

        // Filtering logic
        let isFiltered = false;
        document.getElementById('temperatureFilterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const startDate = document.getElementById('startDate').value;
            const startTime = document.getElementById('startTime').value;
            const endDate = document.getElementById('endDate').value;
            const endTime = document.getElementById('endTime').value;
            const aggregation = document.getElementById('aggregation').value;
            if (!startDate || !startTime || !endDate || !endTime) {
                alert(tempTexts.filterError1);
                return;
            }
            const start = new Date(startDate + 'T' + startTime);
            let end = new Date(endDate + 'T' + endTime);
            end.setSeconds(end.getSeconds() + 59);
            if (start > end) {
                alert(tempTexts.filterError2);
                return;
            }
            // Filter data
            let filteredTimestamps = [];
            let filteredData = [];
            for (let i = 0; i < allTimestamps.length; i++) {
                if (allTimestamps[i] >= start && allTimestamps[i] <= end) {
                    filteredTimestamps.push(allTimestamps[i]);
                    filteredData.push(allData[i]);
                }
            }
            let filteredLabels = [];
            // Agrupamento conforme unidade selecionada
            if (aggregation === 'day') {
                // Agrupar por dia
                let dayMap = {};
                filteredTimestamps.forEach((ts, idx) => {
                    const dayKey = ts.getUTCFullYear() + '-' + (ts.getUTCMonth()+1).toString().padStart(2,'0') + '-' + ts.getUTCDate().toString().padStart(2,'0');
                    if (!dayMap[dayKey]) dayMap[dayKey] = [];
                    dayMap[dayKey].push(filteredData[idx]);
                });
                filteredLabels = [];
                filteredData = [];
                Object.keys(dayMap).sort().forEach(dayKey => {
                    filteredLabels.push(dayKey);
                    const arr = dayMap[dayKey];
                    const avg = arr.reduce((sum, v) => sum + v, 0) / arr.length;
                    filteredData.push(Number(avg.toFixed(2)));
                });
            } else if (aggregation === 'hour') {
                // Agrupar por hora
                let hourlyMap = {};
                filteredTimestamps.forEach((ts, idx) => {
                    const hourKey = ts.getUTCFullYear() + '-' + (ts.getUTCMonth()+1).toString().padStart(2,'0') + '-' + ts.getUTCDate().toString().padStart(2,'0') + ' ' + ts.getUTCHours().toString().padStart(2,'0');
                    if (!hourlyMap[hourKey]) hourlyMap[hourKey] = [];
                    hourlyMap[hourKey].push(filteredData[idx]);
                });
                filteredLabels = [];
                filteredData = [];
                Object.keys(hourlyMap).sort().forEach(hourKey => {
                    filteredLabels.push(hourKey + ':00');
                    const arr = hourlyMap[hourKey];
                    const avg = arr.reduce((sum, v) => sum + v, 0) / arr.length;
                    filteredData.push(Number(avg.toFixed(2)));
                });
            } else {
                // Minuto: cada ponto é um minuto
                let minuteMap = {};
                filteredTimestamps.forEach((ts, idx) => {
                    const minKey = ts.getUTCFullYear() + '-' + (ts.getUTCMonth()+1).toString().padStart(2,'0') + '-' + ts.getUTCDate().toString().padStart(2,'0') + ' ' + ts.getUTCHours().toString().padStart(2,'0') + ':' + ts.getUTCMinutes().toString().padStart(2,'0');
                    if (!minuteMap[minKey]) minuteMap[minKey] = [];
                    minuteMap[minKey].push(filteredData[idx]);
                });
                filteredLabels = [];
                filteredData = [];
                Object.keys(minuteMap).sort().forEach(minKey => {
                    filteredLabels.push(minKey);
                    const arr = minuteMap[minKey];
                    const avg = arr.reduce((sum, v) => sum + v, 0) / arr.length;
                    filteredData.push(Number(avg.toFixed(2)));
                });
            }
            temperatureChart.data.labels = filteredLabels;
            temperatureChart.data.datasets[0].data = filteredData;
            temperatureChart.data.datasets[0].pointBackgroundColor = getPointColors(filteredData);
            temperatureChart.data.datasets[0].pointRadius = getPointRadius(filteredData.length);
            temperatureChart.data.datasets[1].data = getMovingAverageArray(filteredData, 5);
            temperatureChart.data.datasets[2].data = getMovingMinArray(filteredData, 5);
            temperatureChart.data.datasets[3].data = getMovingMaxArray(filteredData, 5);
            temperatureChart.data.datasets[4].data = getLinearRegressionArray(filteredData);
            temperatureChart.data.datasets[0].pointBackgroundColor = (ctx) => {
                let arr = filteredData;
                let outliers = getOutlierIndices(arr, 5);
                return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
            };
            temperatureChart.update();
            isFiltered = true;
            // Calculate min, max, and average temperature in filtered range
            const minMaxInfoDiv = document.getElementById('minMaxInfo');
            if (filteredData.length > 0) {
                const minTemp = Math.min(...filteredData).toFixed(2);
                const maxTemp = Math.max(...filteredData).toFixed(2);
                const avgTemp = (filteredData.reduce((sum, val) => sum + val, 0) / filteredData.length).toFixed(2);
                minMaxInfoDiv.innerHTML = `<span style="color:#007bff;font-weight:bold;">${tempTexts.minLabel}:</span> ${minTemp}°C |
                    <span style="color:#dc3545;font-weight:bold;">${tempTexts.maxLabel}:</span> ${maxTemp}°C |
                    <span style="color:#28a745;font-weight:bold;">${tempTexts.avgLabel}:</span> ${avgTemp}°C`;
                minMaxInfoDiv.style.display = 'block';
            } else {
                minMaxInfoDiv.textContent = tempTexts.noData;
                minMaxInfoDiv.style.display = 'block';
            }
        });

        // Optional: Reset filter when any filter field is cleared
        document.getElementById('temperatureFilterForm').addEventListener('reset', function() {
            // Show only last 60 points in live mode
            const startIdx = Math.max(0, allLabels.length - maxPoints);
            const liveData = allData.slice(startIdx);
            temperatureChart.data.labels = allLabels.slice(startIdx);
            temperatureChart.data.datasets[0].data = liveData;
            temperatureChart.data.datasets[0].pointBackgroundColor = getPointColors(liveData);
            temperatureChart.data.datasets[0].pointRadius = getPointRadius(liveData.length);
            temperatureChart.data.datasets[1].data = getMovingAverageArray(liveData, 5);
            temperatureChart.data.datasets[2].data = getMovingMinArray(liveData, 5);
            temperatureChart.data.datasets[3].data = getMovingMaxArray(liveData, 5);
            temperatureChart.data.datasets[4].data = getLinearRegressionArray(liveData);
            temperatureChart.data.datasets[0].pointBackgroundColor = (ctx) => {
                let arr = liveData;
                let outliers = getOutlierIndices(arr, 5);
                return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
            };
            temperatureChart.update();
            isFiltered = false;
            document.getElementById('minMaxInfo').textContent = '';
            document.getElementById('minMaxInfo').style.display = 'none';
            showLiveStats();
        });

        // Show min, max, avg for last 60 minutes on initial load
        function showLiveStats() {
            // Considera apenas a última hora (últimos 60 pontos)
            const lastData = allData.slice(-maxPoints);
            const minMaxInfoDiv = document.getElementById('minMaxInfo');
            if (lastData.length > 0) {
                const minTemp = Math.min(...lastData).toFixed(2);
                const maxTemp = Math.max(...lastData).toFixed(2);
                const avgTemp = (lastData.reduce((sum, val) => sum + val, 0) / lastData.length).toFixed(2);
                minMaxInfoDiv.innerHTML = `<span style=\"color:#007bff;font-weight:bold;\">${tempTexts.minLabel}:</span> ${minTemp}°C |
                    <span style=\"color:#dc3545;font-weight:bold;\">${tempTexts.maxLabel}:</span> ${maxTemp}°C |
                    <span style=\"color:#28a745;font-weight:bold;\">${tempTexts.avgLabel}:</span> ${avgTemp}°C`;
                minMaxInfoDiv.style.display = 'block';
            } else {
                minMaxInfoDiv.textContent = '';
                minMaxInfoDiv.style.display = 'none';
            }
        }
        showLiveStats();

        // Simulate receiving a new value every minute (for demo)
        setInterval(() => {
            // Simulate a new temperature value
            const newValue = 22 + Math.sin(Math.random() * 6) + Math.random() * 0.2;
            addTemperatureValue(newValue);
        }, 60000); // 60000 ms = 1 minute
    </script>
</body>
</html>
