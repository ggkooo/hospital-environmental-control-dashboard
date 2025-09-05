document.addEventListener('DOMContentLoaded', function() {

    // Textos para tradução
    const tempTexts = window.tempTexts;

    // --- Funções utilitárias ---
    const formatTime = date => date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const formatDate = date => date.toISOString().slice(0, 10);

    // --- Geração de dados simulados (substitua por lógica real no futuro) ---
    const maxPoints = 60;
    const totalSimulatedMinutes = 31 * 24 * 60; // 31 dias
    const now = new Date();
    let allTimestamps = [];
    let allData = [];
    for (let i = totalSimulatedMinutes - 1; i >= 0; i--) {
        const d = new Date(now.getTime() - i * 60000);
        allTimestamps.push(d);
        // --- INÍCIO: GERAÇÃO DE VALORES DE TEMPERATURA (SUBSTITUA POR LÓGICA REAL) ---
        // Aqui é gerado um valor de temperatura aleatório. Substitua por chamada à API ou lógica real.
        const baseTemp = 20 + Math.random() * 6; // 20 a 26
        const dailyVariation = Math.sin(d.getDate() / 2) * 0.5;
        const minuteVariation = Math.random() * 0.5;
        const temp = baseTemp + dailyVariation + minuteVariation;
        allData.push(Number(temp.toFixed(2)));
        // --- FIM: GERAÇÃO DE VALORES DE TEMPERATURA ---
    }
    let allLabels = allTimestamps.map(formatTime);

    // --- Funções de processamento de dados ---
    // Azul escuro: normal, amarelo: atenção, vermelho: alerta
    const getPointColors = dataArr => dataArr.map(v => {
        if (v < 20.0 || v > 24.0) return '#dc3545'; // vermelho
        if ((v >= 20.0 && v < 21.0) || (v > 23.0 && v <= 24.0)) return '#ffc107'; // amarelo
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

    const ctx = document.getElementById('temperatureChart').getContext('2d');
    const temperatureChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: allLabels.slice(-maxPoints),
            datasets: [
                {
                    label: tempTexts.legendTemperature,
                    data: allData.slice(-maxPoints),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3, // bolinhas menores
                    pointHoverRadius: 5, // área de hover menor
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
                    label: tempTexts.legendMovingAvg,
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
                    label: tempTexts.legendMovingMin,
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
                    label: tempTexts.legendMovingMax,
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
                    label: tempTexts.legendTrend,
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
                },
                tooltip: {
                    callbacks: {
                        labelColor: function(context) {
                            const value = context.parsed.y;
                            if (value < 20.0 || value > 24.0) {
                                return {
                                    borderColor: '#dc3545',
                                    backgroundColor: '#dc3545'
                                };
                            } else if ((value >= 20.0 && value < 21.0) || (value > 23.0 && value <= 24.0)) {
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
                        text: tempTexts.timeLabel
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
            temperatureChart.data.datasets[0].pointBorderColor = (ctx) => {
                let arr = liveData;
                let outliers = getOutlierIndices(arr, 5);
                return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : '#003366');
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
        temperatureChart.data.datasets[0].pointBorderColor = (ctx) => {
            let arr = filteredData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : '#003366');
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
        temperatureChart.data.datasets[0].pointBorderColor = (ctx) => {
            let arr = liveData;
            let outliers = getOutlierIndices(arr, 5);
            return arr.map((v, i) => outliers.includes(i) ? '#dc3545' : '#003366');
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
});