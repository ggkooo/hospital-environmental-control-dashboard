<link rel="stylesheet" href="{{ asset('css/pages/humidity.css') }}">

<body>
    <div class="container mb-3 mt-3">
        <h2>{{ __('humidity.title') }}</h2>
        <form id="humidityFilterForm" class="row g-3 align-items-end mb-4" style="justify-content:center;">
            <div class="col-auto">
                <label for="startDate" class="form-label">{{ __('humidity.start_date') }}</label>
                <input type="date" class="form-control" id="startDate" name="startDate">
            </div>
            <div class="col-auto">
                <label for="startTime" class="form-label">{{ __('humidity.start_time') }}</label>
                <input type="time" class="form-control" id="startTime" name="startTime">
            </div>
            <div class="col-auto">
                <label for="endDate" class="form-label">{{ __('humidity.end_date') }}</label>
                <input type="date" class="form-control" id="endDate" name="endDate">
            </div>
            <div class="col-auto">
                <label for="endTime" class="form-label">{{ __('humidity.end_time') }}</label>
                <input type="time" class="form-control" id="endTime" name="endTime">
            </div>
            <div class="col-auto">
                <label for="aggregation" class="form-label">{{ __('humidity.unity') }}</label>
                <select class="form-control" id="aggregation" name="aggregation">
                    <option value="minute">{{ __('humidity.unity_minute') }}</option>
                    <option value="hour">{{ __('humidity.unity_hour') }}</option>
                    <option value="day">{{ __('humidity.unity_day') }}</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">{{ __('humidity.filter') }}</button>
            </div>
        </form>
    </div>

    <div id="minMaxInfo" class="container" style="text-align:center; margin-bottom:16px; font-size:1.1em; color:#333; display:none;"></div>

    <div class="container content-container" style="overflow-x:auto; max-height: 60vh; overflow-y: auto;">
        <canvas id="humidityChart" style="height:350px; min-width:600px;"></canvas>
    </div>

    <!-- ChartJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ChartJS Zoom/Pan Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script>
        window.humidityTexts = {
            legendHumidity: "{{ __('humidity.humidity') }}",
            legendMovingAvg: "{{ __('humidity.moving_avg') }}",
            legendMovingMin: "{{ __('humidity.moving_min') }}",
            legendMovingMax: "{{ __('humidity.moving_max') }}",
            legendTrend: "{{ __('humidity.trend') }}",
            legendTooltipHumidity: "{{ __('humidity.legend_tooltip_humidity') }}",
            legendTooltipMovingAvg: "{{ __('humidity.legend_tooltip_moving_avg') }}",
            legendTooltipMovingMin: "{{ __('humidity.legend_tooltip_moving_min') }}",
            legendTooltipMovingMax: "{{ __('humidity.legend_tooltip_moving_max') }}",
            legendTooltipTrend: "{{ __('humidity.legend_tooltip_trend') }}",
            timeLabel: "{{ __('humidity.time') }}",
            minLabel: "{{ __('humidity.lowest') }}",
            maxLabel: "{{ __('humidity.highest') }}",
            avgLabel: "{{ __('humidity.average') }}",
            filterError1: "{{ __('humidity.filter_error_1') }}",
            filterError2: "{{ __('humidity.filter_error_2') }}",
            noData: "{{ __('humidity.no_data') }}",
        };
    </script>
    <script src="{{ asset('js/pages/humidity.js') }}"></script>
</body>
</html>
