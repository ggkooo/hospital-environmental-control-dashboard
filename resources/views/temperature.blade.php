<link rel="stylesheet" href="{{ asset('css/pages/temperature.css') }}">
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
                    <option value="second">{{ __('temperature.unity_second') }}</option>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script>
        window.tempTexts = {
            legendTemperature: "{{ __('temperature.temperature') }}",
            legendMovingAvg: "{{ __('temperature.moving_avg') }}",
            legendMovingMin: "{{ __('temperature.moving_min') }}",
            legendMovingMax: "{{ __('temperature.moving_max') }}",
            legendTrend: "{{ __('temperature.trend') }}",
            legendTooltipTemperature: "{{ __('temperature.legend_tooltip_temperature') }}",
            legendTooltipMovingAvg: "{{ __('temperature.legend_tooltip_moving_avg') }}",
            legendTooltipMovingMin: "{{ __('temperature.legend_tooltip_moving_min') }}",
            legendTooltipMovingMax: "{{ __('temperature.legend_tooltip_moving_max') }}",
            legendTooltipTrend: "{{ __('temperature.legend_tooltip_trend') }}",
            timeLabel: "{{ __('temperature.time') }}",
            minLabel: "{{ __('temperature.lowest') }}",
            maxLabel: "{{ __('temperature.highest') }}",
            avgLabel: "{{ __('temperature.average') }}",
            filterError1: "{{ __('temperature.filter_error_1') }}",
            filterError2: "{{ __('temperature.filter_error_2') }}",
            noData: "{{ __('temperature.no_data') }}",
        };
    </script>
    <script src="{{ asset('js/pages/temperature.js') }}"></script>
</body>
</html>
