<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temperature\TemperatureMinute;
use App\Models\Temperature\TemperatureSecond;
use Illuminate\View\View;

class TemperatureController extends Controller
{
    public function show(): View
    {
        return view('layout.base', [
            'page' => 'temperature',
        ]);
    }

    public function index(Request $request)
    {
        $startDate = $request->input('startDate');
        $startTime = $request->input('startTime');
        $endDate = $request->input('endDate');
        $endTime = $request->input('endTime');
        $aggregation = $request->input('aggregation', 'minute');

        $start = $startDate ? $startDate . ($startTime ? ' ' . $startTime : ' 00:00:00') : null;
        $end = $endDate ? $endDate . ($endTime ? ' ' . $endTime : ' 23:59:59') : null;

        if ($aggregation === 'second') {
            $query = TemperatureSecond::query();
            if ($start) {
                $query->where('received_at', '>=', $start);
            }
            if ($end) {
                $query->where('received_at', '<=', $end);
            }
            $data = $query->selectRaw('received_at as period, value')
                ->orderBy('received_at')
                ->get();
        } else {
            $query = TemperatureMinute::query();
            if ($start) {
                $query->where('minute', '>=', $start);
            }
            if ($end) {
                $query->where('minute', '<=', $end);
            }
            if ($aggregation === 'hour') {
                $data = $query->selectRaw('DATE_FORMAT(minute, "%Y-%m-%d %H:00:00") as period, AVG(average_value) as value')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
            } elseif ($aggregation === 'day') {
                $data = $query->selectRaw('DATE(minute) as period, AVG(average_value) as value')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
            } else {
                $data = $query->selectRaw('minute as period, average_value as value')
                    ->orderBy('minute')
                    ->get();
            }
        }
        return response()->json($data);
    }

    public function availableDates(Request $request)
    {
        $dates = TemperatureMinute::selectRaw('DATE(minute) as date')
            ->distinct()
            ->orderBy('date')
            ->pluck('date');
        return response()->json($dates);
    }
}
