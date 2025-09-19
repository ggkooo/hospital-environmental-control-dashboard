<?php

namespace App\Jobs;

use App\Models\Humidity\HumiditySecond;
use App\Models\Noise\NoiseSecond;
use App\Models\Temperature\TemperatureSecond;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

                    TemperatureSecond::create([
                        'value' => $item['temperature'],
                        'received_at' => $timestamp,
                    ]);

                    HumiditySecond::create([
                        'value' => $item['humidity'],
                        'received_at' => $timestamp,
                    ]);

                    NoiseSecond::create([
                        'value' => $item['noise'],
                        'received_at' => $timestamp,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Erro ao processar item: ' . json_encode($item) . ' - ' . $e->getMessage());
            }
        }
        file_put_contents($this->filePath, '[]');
    }
}
