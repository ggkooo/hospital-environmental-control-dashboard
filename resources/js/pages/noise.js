document.addEventListener('DOMContentLoaded', function() {
    // Texts for translation
    const noiseTexts = window.noiseTexts;
    // Utilities
    const formatTime = date => date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const formatDate = date => date.toISOString().slice(0, 10);
    // Simulated noise data (replace with real logic)
    const maxPoints = 60;
    const totalSimulatedMinutes = 31 * 24 * 60;
    const now = new Date();
    let allTimestamps = [];
    let allData = [];
    for (let i = totalSimulatedMinutes - 1; i >= 0; i--) {
        const d = new Date(now.getTime() - i * 60000);
        allTimestamps.push(d);
        // Generate noise values (replace with real API)
        const baseNoise = 35 + Math.random() * 40; // 35 to 75 dB
        const dailyVariation = Math.sin(d.getDate() / 2) * 2;
        const minuteVariation = Math.random() * 2;
        const noise = baseNoise + dailyVariation + minuteVariation;
        allData.push(Number(noise.toFixed(2)));
    }
    let allLabels = allTimestamps.map(formatTime);
    // Data processing functions
    const getPointColors = dataArr => dataArr.map(v => {
        if (v < 40.0 || v > 70.0) return '#dc3545'; // red
        if ((v >= 40.0 && v < 45.0) || (v > 65.0 && v <= 70.0)) return '#ffc107'; // yellow
        return '#003366'; // dark blue
    });
    const getPointRadius = numPoints => numPoints > 200 ? 0 : numPoints > 48 ? 3.5 : 7;
    const getMovingAverageArray = (dataArr, windowSize = 2) => dataArr.map((_, i) => {
        const start = Math.max(0, i - windowSize + 1);
        const window = dataArr.slice(start, i + 1);
        return Number((window.reduce((sum, v) => sum + v, 0) / window.length).toFixed(2));
    });
    const getMovingMinArray = (dataArr, windowSize = 2) => dataArr.map((_, i) => {
        const start = Math.max(0, i - windowSize + 1);
        return Math.min(...dataArr.slice(start, i + 1));
    });
    const getMovingMaxArray = (dataArr, windowSize = 2) => dataArr.map((_, i) => {
        const start = Math.max(0, i - windowSize + 1);
        return Math.max(...dataArr.slice(start, i + 1));
    });
    const getLinearRegressionArray = dataArr => {
        if (!dataArr.length) return [];
        const n = dataArr.length;
        let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;
        for (let i = 0; i < n; i++) {
            sumX += i;
            sumY += dataArr[i];
            sumXY += i * dataArr[i];
            sumXX += i * i;
        }
        const slope = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
        const intercept = (sumY - slope * sumX) / n;
        return Array.from({ length: n }, (_, i) => Number((slope * i + intercept).toFixed(2)));
    };
    const getOutlierIndices = (dataArr, windowSize = 2) => {
        const minArr = getMovingMinArray(dataArr, windowSize);
        const maxArr = getMovingMaxArray(dataArr, windowSize);
        return dataArr.map((v, i) => (v < minArr[i] || v > maxArr[i]) ? i : -1).filter(i => i !== -1);
    };
    const ctx = document.getElementById('noiseChart').getContext('2d');
    const noiseChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: allLabels.slice(-maxPoints),
            datasets: [
                {
                    label: noiseTexts.legendNoise,
                    data: allData.slice(-maxPoints),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: (ctx) => {
                        let arr = allData.slice(-maxPoints);
                        return getPointColors(arr);
                    },
                    pointBorderColor: (ctx) => {
                        let arr = allData.slice(-maxPoints);
                        return getPointColors(arr);
                    },
                },
                {
                    label: noiseTexts.legendMovingAvg,
                    data: getMovingAverageArray(allData.slice(-maxPoints), 5),
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
                    label: noiseTexts.legendMovingMin,
                    data: getMovingMinArray(allData.slice(-maxPoints), 5),
                    borderColor: '#17a2b8',
                    fill: false,
                    borderDash: [4,2],
                    pointRadius: 0,
                    pointHoverRadius: 0,
                    tension: 0.3,
                    order: 0,
                },
                {
                    label: noiseTexts.legendMovingMax,
                    data: getMovingMaxArray(allData.slice(-maxPoints), 5),
                    borderColor: '#e83e8c',
                    fill: false,
                    borderDash: [4,2],
                    pointRadius: 0,
                    pointHoverRadius: 0,
                    tension: 0.3,
                    order: 0,
                },
                {
                    label: noiseTexts.legendTrend,
                    data: getLinearRegressionArray(allData.slice(-maxPoints)),
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
                            const tooltips = {};
                            tooltips[noiseTexts.legendNoise] = noiseTexts.legendTooltipNoise;
                            tooltips[noiseTexts.legendMovingAvg] = noiseTexts.legendTooltipMovingAvg;
                            tooltips[noiseTexts.legendMovingMin] = noiseTexts.legendTooltipMovingMin;
                            tooltips[noiseTexts.legendMovingMax] = noiseTexts.legendTooltipMovingMax;
                            tooltips[noiseTexts.legendTrend] = noiseTexts.legendTooltipTrend;
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
                },
                tooltip: {
                    callbacks: {
                        labelColor: function(context) {
                            const value = context.parsed.y;
                            if (value < 40.0 || value > 70.0) {
                                return {
                                    borderColor: '#dc3545',
                                    backgroundColor: '#dc3545'
                                };
                            } else if ((value >= 40.0 && value < 45.0) || (value > 65.0 && value <= 70.0)) {
                                return {
                                    borderColor: '#ffc107',
                                    backgroundColor: '#ffc107'
                                };
                            } else {
                                return {
                                    borderColor: '#003366',
                                    backgroundColor: '#003366'
                                };
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 30,
                    max: 80,
                    title: {
                        display: true,
                        text: noiseTexts.legendNoise
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: noiseTexts.timeLabel
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
    // Add new noise value
    function addNoiseValue(value) {
        const now = new Date();
        const timeLabel = formatTime(now);
        allLabels.push(timeLabel);
        allTimestamps.push(now);
        allData.push(value);
        if (!isFiltered) {
            const startIdx = Math.max(0, allLabels.length - maxPoints);
            const liveLabels = allLabels.slice(startIdx);
            const liveData = allData.slice(startIdx);
            noiseChart.data.labels = liveLabels;
            noiseChart.data.datasets[0].data = liveData;
            noiseChart.data.datasets[0].pointBackgroundColor = getPointColors(liveData);
            noiseChart.data.datasets[0].pointRadius = getPointRadius(liveData.length);
            noiseChart.data.datasets[1].data = getMovingAverageArray(liveData, 5);
            noiseChart.data.datasets[2].data = getMovingMinArray(liveData, 5);
            noiseChart.data.datasets[3].data = getMovingMaxArray(liveData, 5);
            noiseChart.data.datasets[4].data = getLinearRegressionArray(liveData);
            noiseChart.data.datasets[0].pointBackgroundColor = (ctx) => {
                let arr = liveData;
                let outliers = getOutlierIndices(arr, 5);
                return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
            };
            noiseChart.data.datasets[0].pointBorderColor = (ctx) => {
                let arr = liveData;
                let outliers = getOutlierIndices(arr, 5);
                return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : '#003366');
            };
            noiseChart.update();
            showLiveStats();
        }
    }
    // Filter logic
    let isFiltered = false;
    document.getElementById('noiseFilterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const startDate = document.getElementById('startDate').value;
        const startTime = document.getElementById('startTime').value;
        const endDate = document.getElementById('endDate').value;
        const endTime = document.getElementById('endTime').value;
        const aggregation = document.getElementById('aggregation').value;
        if (!startDate || !startTime || !endDate || !endTime) {
            alert(noiseTexts.filterError1);
            return;
        }
        const start = new Date(startDate + 'T' + startTime);
        let end = new Date(endDate + 'T' + endTime);
        end.setSeconds(end.getSeconds() + 59);
        if (start > end) {
            alert(noiseTexts.filterError2);
            return;
        }
        let filteredTimestamps = [];
        let filteredData = [];
        for (let i = 0; i < allTimestamps.length; i++) {
            if (allTimestamps[i] >= start && allTimestamps[i] <= end) {
                filteredTimestamps.push(allTimestamps[i]);
                filteredData.push(allData[i]);
            }
        }
        let filteredLabels = [];
        if (aggregation === 'day') {
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
        noiseChart.data.labels = filteredLabels;
        noiseChart.data.datasets[0].data = filteredData;
        noiseChart.data.datasets[0].pointBackgroundColor = getPointColors(filteredData);
        noiseChart.data.datasets[0].pointRadius = getPointRadius(filteredData.length);
        noiseChart.data.datasets[1].data = getMovingAverageArray(filteredData, 5);
        noiseChart.data.datasets[2].data = getMovingMinArray(filteredData, 5);
        noiseChart.data.datasets[3].data = getMovingMaxArray(filteredData, 5);
        noiseChart.data.datasets[4].data = getLinearRegressionArray(filteredData);
        noiseChart.data.datasets[0].pointBackgroundColor = (ctx) => {
            let arr = filteredData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
        };
        noiseChart.data.datasets[0].pointBorderColor = (ctx) => {
            let arr = filteredData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : '#003366');
        };
        noiseChart.update();
        isFiltered = true;
        // Calculate min, max, avg
        const minMaxInfoDiv = document.getElementById('minMaxInfo');
        if (filteredData.length > 0) {
            const minNoise = Math.min(...filteredData).toFixed(2);
            const maxNoise = Math.max(...filteredData).toFixed(2);
            const avgNoise = (filteredData.reduce((sum, val) => sum + val, 0) / filteredData.length).toFixed(2);
            minMaxInfoDiv.innerHTML = `<span style="color:#007bff;font-weight:bold;">${noiseTexts.minLabel}:</span> ${minNoise} dB |
                <span style="color:#dc3545;font-weight:bold;">${noiseTexts.maxLabel}:</span> ${maxNoise} dB |
                <span style="color:#28a745;font-weight:bold;">${noiseTexts.avgLabel}:</span> ${avgNoise} dB`;
            minMaxInfoDiv.style.display = 'block';
        } else {
            minMaxInfoDiv.textContent = noiseTexts.noData;
            minMaxInfoDiv.style.display = 'block';
        }
    });
    // Reset filter
    document.getElementById('noiseFilterForm').addEventListener('reset', function() {
        const startIdx = Math.max(0, allLabels.length - maxPoints);
        const liveData = allData.slice(startIdx);
        noiseChart.data.labels = allLabels.slice(startIdx);
        noiseChart.data.datasets[0].data = liveData;
        noiseChart.data.datasets[0].pointBackgroundColor = getPointColors(liveData);
        noiseChart.data.datasets[0].pointRadius = getPointRadius(liveData.length);
        noiseChart.data.datasets[1].data = getMovingAverageArray(liveData, 5);
        noiseChart.data.datasets[2].data = getMovingMinArray(liveData, 5);
        noiseChart.data.datasets[3].data = getMovingMaxArray(liveData, 5);
        noiseChart.data.datasets[4].data = getLinearRegressionArray(liveData);
        noiseChart.data.datasets[0].pointBackgroundColor = (ctx) => {
            let arr = liveData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
        };
        noiseChart.data.datasets[0].pointBorderColor = (ctx) => {
            let arr = liveData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : '#003366');
        };
        noiseChart.update();
        isFiltered = false;
        document.getElementById('minMaxInfo').textContent = '';
        document.getElementById('minMaxInfo').style.display = 'none';
        showLiveStats();
    });
    // Initial statistics
    function showLiveStats() {
        const lastData = allData.slice(-maxPoints);
        const minMaxInfoDiv = document.getElementById('minMaxInfo');
        if (lastData.length > 0) {
            const minNoise = Math.min(...lastData).toFixed(2);
            const maxNoise = Math.max(...lastData).toFixed(2);
            const avgNoise = (lastData.reduce((sum, val) => sum + val, 0) / lastData.length).toFixed(2);
            minMaxInfoDiv.innerHTML = `<span style=\"color:#007bff;font-weight:bold;\">${noiseTexts.minLabel}:</span> ${minNoise} dB |
                <span style=\"color:#dc3545;font-weight:bold;\">${noiseTexts.maxLabel}:</span> ${maxNoise} dB |
                <span style=\"color:#28a745;font-weight:bold;\">${noiseTexts.avgLabel}:</span> ${avgNoise} dB`;
            minMaxInfoDiv.style.display = 'block';
        } else {
            minMaxInfoDiv.textContent = '';
            minMaxInfoDiv.style.display = 'none';
        }
    }
    showLiveStats();
    // Simulate receiving new value every minute
    setInterval(() => {
        const newValue = 55 + Math.sin(Math.random() * 6) + Math.random() * 2;
        addNoiseValue(newValue);
    }, 60000);
});

