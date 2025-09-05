<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temperature Chart</title>
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
        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container mb-3 mt-3">
        <h2>Temperature Over Time</h2>
        <form id="temperatureFilterForm" class="row g-3 align-items-end mb-4" style="justify-content:center;">
            <div class="col-auto">
                <label for="startDate" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="startDate" name="startDate">
            </div>
            <div class="col-auto">
                <label for="startTime" class="form-label">Start Time</label>
                <input type="time" class="form-control" id="startTime" name="startTime">
            </div>
            <div class="col-auto">
                <label for="endDate" class="form-label">End Date</label>
                <input type="date" class="form-control" id="endDate" name="endDate">
            </div>
            <div class="col-auto">
                <label for="endTime" class="form-label">End Time</label>
                <input type="time" class="form-control" id="endTime" name="endTime">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    <div id="minMaxInfo" class="container" style="text-align:center; margin-bottom:16px; font-size:1.1em; color:#333; display:none;"></div>

    <div class="container" style="overflow-x:auto;">
        <canvas id="temperatureChart" style="height:350px; min-width:600px;"></canvas>
    </div>
    <script>
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
        let allLabels = [];
        let allData = [];
        let allTimestamps = [];
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
        const temperatureChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Temperature (°C)',
                    data: data,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: getPointRadius(data.length),
                    pointBackgroundColor: pointColors,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
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
                        min: 0,
                        max: 35,
                        title: {
                            display: true,
                            text: 'Temperature (°C)'
                        },
                        ticks: {
                            stepSize: 0.1
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        },
                        ticks: {
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0,
                            maxTicksLimit: 20
                        }
                    }
                }
            },});
        // Function to add a new temperature value
        function addTemperatureValue(value) {
            const now = new Date();
            const timeLabel = formatTime(now);
            allLabels.push(timeLabel);
            allTimestamps.push(now);
            allData.push(value);
            // Only limit to 60 points in live mode (not filtered)
            if (!isFiltered) {
                // Show only last 60 points
                const startIdx = Math.max(0, allLabels.length - maxPoints);
                const liveData = allData.slice(startIdx);
                temperatureChart.data.labels = allLabels.slice(startIdx);
                temperatureChart.data.datasets[0].data = liveData;
                temperatureChart.data.datasets[0].pointBackgroundColor = getPointColors(liveData);
                temperatureChart.data.datasets[0].pointRadius = getPointRadius(liveData.length);
                temperatureChart.update();
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
            if (!startDate || !startTime || !endDate || !endTime) {
                alert('Please select start and end date/time.');
                return;
            }
            const start = new Date(startDate + 'T' + startTime);
            let end = new Date(endDate + 'T' + endTime);
            end.setSeconds(end.getSeconds() + 59);
            if (start > end) {
                alert('Start date/time must be before end date/time.');
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
            // If range > 1 hour, aggregate by hour
            const rangeMs = end - start;
            if (rangeMs > 3600000) {
                // Group by hour
                let hourlyMap = {};
                filteredTimestamps.forEach((ts, idx) => {
                    // Use UTC for consistency
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
                // Format labels: if more than 1 day, show date and time, else show only time
                let showFullDate = false;
                if (filteredTimestamps.length > 0) {
                    const firstDay = filteredTimestamps[0].toISOString().slice(0, 10);
                    const lastDay = filteredTimestamps[filteredTimestamps.length - 1].toISOString().slice(0, 10);
                    showFullDate = firstDay !== lastDay;
                }
                for (let i = 0; i < filteredTimestamps.length; i++) {
                    if (showFullDate) {
                        filteredLabels.push(filteredTimestamps[i].toLocaleString([], { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' }));
                    } else {
                        filteredLabels.push(filteredTimestamps[i].toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
                    }
                }
            }
            temperatureChart.data.labels = filteredLabels;
            temperatureChart.data.datasets[0].data = filteredData;
            temperatureChart.data.datasets[0].pointBackgroundColor = getPointColors(filteredData);
            temperatureChart.data.datasets[0].pointRadius = getPointRadius(filteredData.length);
            temperatureChart.update();
            isFiltered = true;
            // Calculate min, max, and average temperature in filtered range
            const minMaxInfoDiv = document.getElementById('minMaxInfo');
            if (filteredData.length > 0) {
                const minTemp = Math.min(...filteredData).toFixed(2);
                const maxTemp = Math.max(...filteredData).toFixed(2);
                const avgTemp = (filteredData.reduce((sum, val) => sum + val, 0) / filteredData.length).toFixed(2);
                minMaxInfoDiv.textContent = `Lowest temperature: ${minTemp}°C | Highest temperature: ${maxTemp}°C | Average temperature: ${avgTemp}°C`;
                minMaxInfoDiv.style.display = 'block';
            } else {
                minMaxInfoDiv.textContent = 'No data in selected range.';
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
            temperatureChart.update();
            isFiltered = false;
            document.getElementById('minMaxInfo').textContent = '';
            document.getElementById('minMaxInfo').style.display = 'none';
            showLiveStats();
        });

        // Show min, max, avg for last 60 minutes on initial load
        function showLiveStats() {
            const lastData = allData.slice(-maxPoints);
            if (lastData.length > 0) {
                const minTemp = Math.min(...lastData).toFixed(2);
                const maxTemp = Math.max(...lastData).toFixed(2);
                const avgTemp = (lastData.reduce((sum, val) => sum + val, 0) / lastData.length).toFixed(2);
                const minMaxInfoDiv = document.getElementById('minMaxInfo');
                minMaxInfoDiv.textContent = `Lowest temperature: ${minTemp}°C | Highest temperature: ${maxTemp}°C | Average temperature: ${avgTemp}°C`;
                minMaxInfoDiv.style.display = 'block';
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
