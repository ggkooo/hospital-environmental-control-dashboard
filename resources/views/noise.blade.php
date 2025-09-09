<link rel="stylesheet" href="{{ asset('css/pages/noise.css') }}">
<body>
    <div class="container mb-3 mt-3">
        <h2>{{ __('noise.title') }}</h2>
        <form id="noiseFilterForm" class="row g-3 align-items-end mb-4" style="justify-content:center;">
            <div class="col-auto">
                <label for="startDate" class="form-label">{{ __('noise.start_date') }}</label>
                <input type="date" class="form-control" id="startDate" name="startDate">
            </div>
            <div class="col-auto">
                <label for="startTime" class="form-label">{{ __('noise.start_time') }}</label>
                <input type="time" class="form-control" id="startTime" name="startTime">
            </div>
            <div class="col-auto">
                <label for="endDate" class="form-label">{{ __('noise.end_date') }}</label>
                <input type="date" class="form-control" id="endDate" name="endDate">
            </div>
            <div class="col-auto">
                <label for="endTime" class="form-label">{{ __('noise.end_time') }}</label>
                <input type="time" class="form-control" id="endTime" name="endTime">
            </div>
            <div class="col-auto">
                <label for="aggregation" class="form-label">{{ __('noise.aggregation') }}</label>
                <select class="form-control" id="aggregation" name="aggregation">
                    <option value="minute">{{ __('noise.aggregation_minute') }}</option>
                    <option value="hour">{{ __('noise.aggregation_hour') }}</option>
                    <option value="day">{{ __('noise.aggregation_day') }}</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">{{ __('noise.filter') }}</button>
                <button type="reset" class="btn btn-secondary">{{ __('noise.reset') }}</button>
            </div>
        </form>
    </div>

    <div id="minMaxInfo" class="container" style="text-align:center; margin-bottom:16px; font-size:1.1em; color:#333; display:none;"></div>

    <div class="container content-container" style="overflow-x:auto; max-height: 60vh; overflow-y: auto;">
        <canvas id="noiseChart" style="height:350px; min-width:600px;"></canvas>
    </div>

    <!-- ChartJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ChartJS Zoom/Pan Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script>
        window.noiseTexts = {
            legendNoise: "{{ __('noise.noise') }}",
            legendMovingAvg: "{{ __('noise.moving_avg') }}",
            legendMovingMin: "{{ __('noise.moving_min') }}",
            legendMovingMax: "{{ __('noise.moving_max') }}",
            legendTrend: "{{ __('noise.trend') }}",
            legendTooltipNoise: "{{ __('noise.legend_tooltip_noise') }}",
            legendTooltipMovingAvg: "{{ __('noise.legend_tooltip_moving_avg') }}",
            legendTooltipMovingMin: "{{ __('noise.legend_tooltip_moving_min') }}",
            legendTooltipMovingMax: "{{ __('noise.legend_tooltip_moving_max') }}",
            legendTooltipTrend: "{{ __('noise.legend_tooltip_trend') }}",
            timeLabel: "{{ __('noise.time') }}",
            minLabel: "{{ __('noise.min') }}",
            maxLabel: "{{ __('noise.max') }}",
            avgLabel: "{{ __('noise.avg') }}",
            filterError1: "{{ __('noise.filter_error_1') }}",
            filterError2: "{{ __('noise.filter_error_2') }}",
            noData: "{{ __('noise.no_data') }}"
        };
    </script>
    <script src="/js/pages/noise.js"></script>
</body>
