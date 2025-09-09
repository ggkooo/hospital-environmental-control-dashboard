document.addEventListener('DOMContentLoaded', function() {
    // Textos para tradução
    const humidityTexts = window.humidityTexts;
    // Utilitários
    const formatTime = date => date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const formatDate = date => date.toISOString().slice(0, 10);
    // Simulação de dados de umidade (substitua por lógica real)
    const maxPoints = 60;
    const totalSimulatedMinutes = 31 * 24 * 60;
    const now = new Date();
    let allTimestamps = [];
    let allData = [];
    for (let i = totalSimulatedMinutes - 1; i >= 0; i--) {
        const d = new Date(now.getTime() - i * 60000);
        allTimestamps.push(d);
        // Geração de valores de umidade (substitua por API real)
        const baseHumidity = 40 + Math.random() * 30; // 40 a 70%
        const dailyVariation = Math.sin(d.getDate() / 2) * 2;
        const minuteVariation = Math.random() * 2;
        const humidity = baseHumidity + dailyVariation + minuteVariation;
        allData.push(Number(humidity.toFixed(2)));
    }
    let allLabels = allTimestamps.map(formatTime);
    // Funções de processamento de dados
    const getPointColors = dataArr => dataArr.map(v => {
        if (v < 45.0 || v > 65.0) return '#dc3545'; // vermelho
        if ((v >= 45.0 && v < 50.0) || (v > 60.0 && v <= 65.0)) return '#ffc107'; // amarelo
        return '#003366'; // azul escuro
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
    const ctx = document.getElementById('humidityChart').getContext('2d');
    const humidityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: allLabels.slice(-maxPoints),
            datasets: [
                {
                    label: humidityTexts.legendHumidity,
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
                    label: humidityTexts.legendMovingAvg,
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
                    label: humidityTexts.legendMovingMin,
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
                    label: humidityTexts.legendMovingMax,
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
                    label: humidityTexts.legendTrend,
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
                            tooltips[humidityTexts.legendHumidity] = humidityTexts.legendTooltipHumidity;
                            tooltips[humidityTexts.legendMovingAvg] = humidityTexts.legendTooltipMovingAvg;
                            tooltips[humidityTexts.legendMovingMin] = humidityTexts.legendTooltipMovingMin;
                            tooltips[humidityTexts.legendMovingMax] = humidityTexts.legendTooltipMovingMax;
                            tooltips[humidityTexts.legendTrend] = humidityTexts.legendTooltipTrend;
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
                            if (value < 45.0 || value > 65.0) {
                                return {
                                    borderColor: '#dc3545',
                                    backgroundColor: '#dc3545'
                                };
                            } else if ((value >= 45.0 && value < 50.0) || (value > 60.0 && value <= 65.0)) {
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
                        text: humidityTexts.legendHumidity
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: humidityTexts.timeLabel
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
    // Adiciona novo valor de umidade
    function addHumidityValue(value) {
        const now = new Date();
        const timeLabel = formatTime(now);
        allLabels.push(timeLabel);
        allTimestamps.push(now);
        allData.push(value);
        if (!isFiltered) {
            const startIdx = Math.max(0, allLabels.length - maxPoints);
            const liveLabels = allLabels.slice(startIdx);
            const liveData = allData.slice(startIdx);
            humidityChart.data.labels = liveLabels;
            humidityChart.data.datasets[0].data = liveData;
            humidityChart.data.datasets[0].pointBackgroundColor = getPointColors(liveData);
            humidityChart.data.datasets[0].pointRadius = getPointRadius(liveData.length);
            humidityChart.data.datasets[1].data = getMovingAverageArray(liveData, 5);
            humidityChart.data.datasets[2].data = getMovingMinArray(liveData, 5);
            humidityChart.data.datasets[3].data = getMovingMaxArray(liveData, 5);
            humidityChart.data.datasets[4].data = getLinearRegressionArray(liveData);
            humidityChart.data.datasets[0].pointBackgroundColor = (ctx) => {
                let arr = liveData;
                let outliers = getOutlierIndices(arr, 5);
                return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
            };
            humidityChart.data.datasets[0].pointBorderColor = (ctx) => {
                let arr = liveData;
                let outliers = getOutlierIndices(arr, 5);
                return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : '#003366');
            };
            humidityChart.update();
            showLiveStats();
        }
    }
    // Lógica de filtro
    let isFiltered = false;
    document.getElementById('humidityFilterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const startDate = document.getElementById('startDate').value;
        const startTime = document.getElementById('startTime').value;
        const endDate = document.getElementById('endDate').value;
        const endTime = document.getElementById('endTime').value;
        const aggregation = document.getElementById('aggregation').value;
        if (!startDate || !startTime || !endDate || !endTime) {
            alert(humidityTexts.filterError1);
            return;
        }
        const start = new Date(startDate + 'T' + startTime);
        let end = new Date(endDate + 'T' + endTime);
        end.setSeconds(end.getSeconds() + 59);
        if (start > end) {
            alert(humidityTexts.filterError2);
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
        humidityChart.data.labels = filteredLabels;
        humidityChart.data.datasets[0].data = filteredData;
        humidityChart.data.datasets[0].pointBackgroundColor = getPointColors(filteredData);
        humidityChart.data.datasets[0].pointRadius = getPointRadius(filteredData.length);
        humidityChart.data.datasets[1].data = getMovingAverageArray(filteredData, 5);
        humidityChart.data.datasets[2].data = getMovingMinArray(filteredData, 5);
        humidityChart.data.datasets[3].data = getMovingMaxArray(filteredData, 5);
        humidityChart.data.datasets[4].data = getLinearRegressionArray(filteredData);
        humidityChart.data.datasets[0].pointBackgroundColor = (ctx) => {
            let arr = filteredData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
        };
        humidityChart.data.datasets[0].pointBorderColor = (ctx) => {
            let arr = filteredData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : '#003366');
        };
        humidityChart.update();
        isFiltered = true;
        // Calcula min, max, média
        const minMaxInfoDiv = document.getElementById('minMaxInfo');
        if (filteredData.length > 0) {
            const minHumidity = Math.min(...filteredData).toFixed(2);
            const maxHumidity = Math.max(...filteredData).toFixed(2);
            const avgHumidity = (filteredData.reduce((sum, val) => sum + val, 0) / filteredData.length).toFixed(2);
            minMaxInfoDiv.innerHTML = `<span style="color:#007bff;font-weight:bold;">${humidityTexts.minLabel}:</span> ${minHumidity}% |
                <span style="color:#dc3545;font-weight:bold;">${humidityTexts.maxLabel}:</span> ${maxHumidity}% |
                <span style="color:#28a745;font-weight:bold;">${humidityTexts.avgLabel}:</span> ${avgHumidity}%`;
            minMaxInfoDiv.style.display = 'block';
        } else {
            minMaxInfoDiv.textContent = humidityTexts.noData;
            minMaxInfoDiv.style.display = 'block';
        }
    });
    // Reset filtro
    document.getElementById('humidityFilterForm').addEventListener('reset', function() {
        const startIdx = Math.max(0, allLabels.length - maxPoints);
        const liveData = allData.slice(startIdx);
        humidityChart.data.labels = allLabels.slice(startIdx);
        humidityChart.data.datasets[0].data = liveData;
        humidityChart.data.datasets[0].pointBackgroundColor = getPointColors(liveData);
        humidityChart.data.datasets[0].pointRadius = getPointRadius(liveData.length);
        humidityChart.data.datasets[1].data = getMovingAverageArray(liveData, 5);
        humidityChart.data.datasets[2].data = getMovingMinArray(liveData, 5);
        humidityChart.data.datasets[3].data = getMovingMaxArray(liveData, 5);
        humidityChart.data.datasets[4].data = getLinearRegressionArray(liveData);
        humidityChart.data.datasets[0].pointBackgroundColor = (ctx) => {
            let arr = liveData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : getPointColors([v])[0]);
        };
        humidityChart.data.datasets[0].pointBorderColor = (ctx) => {
            let arr = liveData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : '#003366');
        };
        humidityChart.update();
        isFiltered = false;
        document.getElementById('minMaxInfo').textContent = '';
        document.getElementById('minMaxInfo').style.display = 'none';
        showLiveStats();
    });
    // Estatísticas iniciais
    function showLiveStats() {
        const lastData = allData.slice(-maxPoints);
        const minMaxInfoDiv = document.getElementById('minMaxInfo');
        if (lastData.length > 0) {
            const minHumidity = Math.min(...lastData).toFixed(2);
            const maxHumidity = Math.max(...lastData).toFixed(2);
            const avgHumidity = (lastData.reduce((sum, val) => sum + val, 0) / lastData.length).toFixed(2);
            minMaxInfoDiv.innerHTML = `<span style=\"color:#007bff;font-weight:bold;\">${humidityTexts.minLabel}:</span> ${minHumidity}% |
                <span style=\"color:#dc3545;font-weight:bold;\">${humidityTexts.maxLabel}:</span> ${maxHumidity}% |
                <span style=\"color:#28a745;font-weight:bold;\">${humidityTexts.avgLabel}:</span> ${avgHumidity}%`;
            minMaxInfoDiv.style.display = 'block';
        } else {
            minMaxInfoDiv.textContent = '';
            minMaxInfoDiv.style.display = 'none';
        }
    }
    showLiveStats();
    // Simula recebimento de novo valor a cada minuto
    setInterval(() => {
        const newValue = 55 + Math.sin(Math.random() * 6) + Math.random() * 2;
        addHumidityValue(newValue);
    }, 60000);
});