document.addEventListener('DOMContentLoaded', function() {

    const tempTexts = window.tempTexts;

    const formatTime = date => date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const formatDate = date => date.toISOString().slice(0, 10);

    async function fetchTemperatureData(params = {}) {
        const url = new URL('/api/temperature', window.location.origin);
        Object.entries(params).forEach(([key, value]) => {
            if (value) url.searchParams.append(key, value);
        });
        const res = await fetch(url);
        if (!res.ok) throw new Error('Erro ao buscar dados de temperatura');
        return res.json();
    }

    function updateChart(chart, data) {
        if (!data.length) {
            chart.data.labels = [];
            chart.data.datasets.forEach(ds => ds.data = []);
            chart.update();
            document.getElementById('minMaxInfo').style.display = 'block';
            document.getElementById('minMaxInfo').innerText = tempTexts.noData;
            return;
        }
        const labels = data.map(d => d.period);
        const values = data.map(d => Number(d.value));
        chart.data.labels = labels;
        chart.data.datasets[0].data = values;
        chart.data.datasets[1].data = getMovingAverageArray(values, 5);
        chart.data.datasets[2].data = getMovingMinArray(values, 5);
        chart.data.datasets[3].data = getMovingMaxArray(values, 5);
        chart.data.datasets[4].data = getLinearRegressionArray(values);
        chart.update();
        const min = Math.min(...values).toFixed(3);
        const max = Math.max(...values).toFixed(3);
        const avg = (values.reduce((a,b) => a+b,0)/values.length).toFixed(3);
        document.getElementById('minMaxInfo').style.display = 'block';
        document.getElementById('minMaxInfo').innerText = `${tempTexts.minLabel}: ${min}°C | ${tempTexts.maxLabel}: ${max}°C | ${tempTexts.avgLabel}: ${avg}°C`;
    }

    const ctx = document.getElementById('temperatureChart').getContext('2d');
    const temperatureChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: tempTexts.legendTemperature,
                    data: [],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: (ctx) => getPointColors(ctx.chart.data.datasets[0].data),
                    pointBorderColor: (ctx) => getPointColors(ctx.chart.data.datasets[0].data),
                },
                {
                    label: tempTexts.legendMovingAvg,
                    data: [],
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
                    data: [],
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
                    data: [],
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
                    data: [],
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
                    min: 21.5,
                    max: 24.5,
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

    const filterForm = document.getElementById('temperatureFilterForm');
    if (filterForm) filterForm.style.display = '';

    let pollingInterval = null;
    let isFiltered = false;

    async function fetchAndUpdateWithFilter(params) {
        try {
            const data = await fetchTemperatureData(params);
            updateChart(temperatureChart, data);
        } catch (err) {
            document.getElementById('minMaxInfo').style.display = 'block';
            document.getElementById('minMaxInfo').innerText = tempTexts.noData;
        }
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            isFiltered = true;
            if (pollingInterval) clearInterval(pollingInterval);
            const form = e.target;
            const params = {
                startDate: form.startDate.value,
                startTime: form.startTime.value,
                endDate: form.endDate.value,
                endTime: form.endTime.value,
                aggregation: form.aggregation.value
            };
            fetchAndUpdateWithFilter(params);
        });
    }

    async function fetchAndUpdateLastHour() {
        if (isFiltered) return;
        try {
            const now = new Date();
            const startDate = formatDate(new Date(now.getTime() - 60*60000));
            const endDate = formatDate(now);
            const startTime = new Date(now.getTime() - 60*60000).toTimeString().slice(0,5);
            const endTime = now.toTimeString().slice(0,5);
            const data = await fetchTemperatureData({
                startDate,
                endDate,
                startTime,
                endTime,
                aggregation: 'minute'
            });
            updateChart(temperatureChart, data.slice(-60));
        } catch (err) {
            document.getElementById('minMaxInfo').style.display = 'block';
            document.getElementById('minMaxInfo').innerText = tempTexts.noData;
        }
    }

    pollingInterval = setInterval(fetchAndUpdateLastHour, 10000);

    fetchAndUpdateLastHour();

    function getMovingAverageArray(arr, windowSize) {
        if (!Array.isArray(arr) || arr.length === 0) return [];
        const result = [];
        for (let i = 0; i < arr.length; i++) {
            const start = Math.max(0, i - windowSize + 1);
            const window = arr.slice(start, i + 1);
            const avg = window.reduce((a, b) => a + b, 0) / window.length;
            result.push(Number(avg.toFixed(2)));
        }
        return result;
    }

    function getMovingMinArray(arr, windowSize) {
        if (!Array.isArray(arr) || arr.length === 0) return [];
        const result = [];
        for (let i = 0; i < arr.length; i++) {
            const start = Math.max(0, i - windowSize + 1);
            const window = arr.slice(start, i + 1);
            result.push(Math.min(...window));
        }
        return result;
    }

    function getMovingMaxArray(arr, windowSize) {
        if (!Array.isArray(arr) || arr.length === 0) return [];
        const result = [];
        for (let i = 0; i < arr.length; i++) {
            const start = Math.max(0, i - windowSize + 1);
            const window = arr.slice(start, i + 1);
            result.push(Math.max(...window));
        }
        return result;
    }

    function getLinearRegressionArray(arr) {
        if (!Array.isArray(arr) || arr.length === 0) return [];
        const n = arr.length;
        const xSum = (n * (n - 1)) / 2;
        const ySum = arr.reduce((a, b) => a + b, 0);
        const xxSum = (n * (n - 1) * (2 * n - 1)) / 6;
        let xySum = 0;
        for (let i = 0; i < n; i++) {
            xySum += i * arr[i];
        }
        const slope = (n * xySum - xSum * ySum) / (n * xxSum - xSum * xSum);
        const intercept = (ySum - slope * xSum) / n;
        return Array.from({ length: n }, (_, i) => Number((slope * i + intercept).toFixed(2)));
    }

    function getPointColors(arr) {
        return arr.map(value => {
            if (value < 20.0 || value > 24.0) return '#dc3545';
            if ((value >= 20.0 && value < 21.0) || (value > 23.0 && value <= 24.0)) return '#ffc107';
            return '#003366';
        });
    }

    async function disableUnavailableDates() {
        try {
            const res = await fetch('/api/temperature/available-dates');
            if (!res.ok) return;
            const availableDates = await res.json();
            const availableSet = new Set(availableDates);
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            [startDateInput, endDateInput].forEach(input => {
                if (!input) return;
                input.addEventListener('input', function() {
                    // Força o valor para uma data disponível
                    if (input.value && !availableSet.has(input.value)) {
                        input.setCustomValidity('Data sem leitura disponível');
                    } else {
                        input.setCustomValidity('');
                    }
                });
                input.setAttribute('min', availableDates[0] || '');
                input.setAttribute('max', availableDates[availableDates.length-1] || '');
                input.addEventListener('keydown', function(e) {
                    setTimeout(() => {
                        if (input.value && !availableSet.has(input.value)) {
                            input.value = '';
                        }
                    }, 10);
                });
            });
        } catch (e) {}
    }
    disableUnavailableDates();

    function updateAggregationOptions() {
        const startDate = document.getElementById('startDate').value;
        const startTime = document.getElementById('startTime').value || '00:00';
        const endDate = document.getElementById('endDate').value;
        const endTime = document.getElementById('endTime').value || '23:59';
        const aggregation = document.getElementById('aggregation');
        if (!startDate || !endDate) return;
        const start = new Date(`${startDate}T${startTime}`);
        const end = new Date(`${endDate}T${endTime}`);
        const diffMs = end - start;
        if (isNaN(diffMs) || diffMs <= 0) return;
        const diffMinutes = diffMs / 60000;
        const diffHours = diffMs / 3600000;
        const diffDays = diffMs / 86400000;
        Array.from(aggregation.options).forEach(opt => opt.disabled = false);
        if (diffDays < 2) aggregation.querySelector('option[value="day"]').disabled = true;
        if (diffHours < 2) aggregation.querySelector('option[value="hour"]').disabled = true;
        if (diffHours > 12) aggregation.querySelector('option[value="minute"]').disabled = true;
        if (diffMinutes > 10) aggregation.querySelector('option[value="second"]').disabled = true;
    }
    ['startDate','startTime','endDate','endTime'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', updateAggregationOptions);
    });
    updateAggregationOptions();
});
