<?php

namespace App\Jobs;

use App\Models\Humidity\HumidityMinute;
use App\Models\Humidity\HumiditySecond;
use App\Models\Noise\NoiseMinute;
use App\Models\Noise\NoiseSecond;
use App\Models\Temperature\TemperatureMinute;
use App\Models\Temperature\TemperatureSecond;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessSensorData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('app/api_data.json');
    }

    public function handle()
    {
        if (!file_exists($this->filePath)) {
            return;
        }
        $json = file_get_contents($this->filePath);
        $data = json_decode($json, true);
        if (!$data || !is_array($data)) {
            file_put_contents($this->filePath, '[]');
            return;
        }

        $temperatureData = [];
        $humidityData = [];
        $noiseData = [];

        foreach ($data as $item) {
            try {
                if (isset($item['temperature'], $item['humidity'], $item['noise'], $item['timestamp'])) {
                    $timestamp = $item['timestamp'];

                    if (preg_match('/^\d{2}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $timestamp)) {
                        $timestamp = date('Y') . substr($timestamp, 2);
                    }

                    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $timestamp)) {
                        $year = substr($timestamp, 0, 4);

                        if ($year < 2020 || $year > 2100) {
                            $timestamp = date('Y', strtotime('now')) . substr($timestamp, 4);
                        }
                    } else {
                        $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));
                    }

                    $temperatureData[] = [
                        'value' => $item['temperature'],
                        'received_at' => $timestamp
                    ];

                    $humidityData[] = [
                        'value' => $item['humidity'],
                        'received_at' => $timestamp
                    ];

                    $noiseData[] = [
                        'value' => $item['noise'],
                        'received_at' => $timestamp
                    ];
                }
            } catch (\Throwable $e) {
                Log::error('Erro ao processar item: ' . json_encode($item) . ' - ' . $e->getMessage());
            }
        }

        DB::beginTransaction();
        try {
            $chunkSize = 100;

            foreach (array_chunk($temperatureData, $chunkSize) as $chunk) {
                DB::table('temperature_seconds')->insert($chunk);
            }

            foreach (array_chunk($humidityData, $chunkSize) as $chunk) {
                DB::table('humidity_seconds')->insert($chunk);
            }

            foreach (array_chunk($noiseData, $chunkSize) as $chunk) {
                DB::table('noise_seconds')->insert($chunk);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao inserir dados em lote: ' . $e->getMessage());
            throw $e;
        }

        file_put_contents($this->filePath, '[]');

        $this->processMinuteAverages();
    }

    protected function processMinuteAverages()
    {
        $now = Carbon::now();
        $lastMinute = $now->copy()->startOfMinute();

        $this->calculateMinuteAverages(
            TemperatureSecond::class,
            TemperatureMinute::class,
            $lastMinute
        );

        $this->calculateMinuteAverages(
            HumiditySecond::class,
            HumidityMinute::class,
            $lastMinute
        );

        $this->calculateMinuteAverages(
            NoiseSecond::class,
            NoiseMinute::class,
            $lastMinute
        );
    }

    protected function calculateMinuteAverages($secondsModel, $minutesModel, $lastMinute)
    {
        try {
            $secondsTable = $secondsModel::getModel()->getTable();
            $minutesTable = $minutesModel::getModel()->getTable();

            $averages = DB::select("
                SELECT
                    DATE_FORMAT(s.received_at, '%Y-%m-%d %H:%i:00') as minute_timestamp,
                    AVG(s.value) as average_value
                FROM {$secondsTable} s
                LEFT JOIN {$minutesTable} m ON DATE_FORMAT(s.received_at, '%Y-%m-%d %H:%i:00') = DATE_FORMAT(m.minute, '%Y-%m-%d %H:%i:00')
                WHERE s.received_at < ? AND m.id IS NULL
                GROUP BY minute_timestamp
            ", [$lastMinute]);

            if (empty($averages)) {
                return;
            }

            $insertData = [];

            foreach ($averages as $avg) {
                $insertData[] = [
                    'average_value' => $avg->average_value,
                    'minute' => $avg->minute_timestamp
                ];
            }

            if (!empty($insertData)) {
                $minutesModel::insert($insertData);
            }
        } catch (\Throwable $e) {
            Log::error('Erro ao calcular médias por minuto: ' . $e->getMessage());
        }
    }
}
