<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function handle(Request $request)
    {
        $apiKey = $request->header('X-API-KEY');
        $validKey = env('API_KEY');
        if (!$validKey || $apiKey !== $validKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->json()->all();
        if (!is_array($data)) {
            return response()->json(['error' => 'O corpo da requisição deve ser um array JSON.'], 422);
        }

        foreach ($data as $index => $item) {
            if (!is_array($item) || !isset($item['temperature'], $item['humidity'], $item['noise'], $item['timestamp'])) {
                return response()->json([
                    'error' => "Item na posição $index está incompleto ou mal formatado."
                ], 422);
            }
            if (!is_numeric($item['temperature']) || !is_numeric($item['humidity']) || !is_numeric($item['noise'])) {
                return response()->json([
                    'error' => "Os campos temperature, humidity e noise devem ser numéricos (item $index)."
                ], 422);
            }
            if (!is_string($item['timestamp'])) {
                return response()->json([
                    'error' => "O campo timestamp deve ser uma string (item $index)."
                ], 422);
            }
        }

        $dir = storage_path('app');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filePath = $dir . '/api_data.json';
        $existingData = [];
        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            $existingData = json_decode($json, true) ?: [];
        }
        $existingData = array_merge($existingData, $data);
        $result = file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if ($result === false) {
            Log::error('Falha ao salvar api_data.json em ' . $filePath);
            return response()->json(['error' => 'Falha ao salvar os dados no servidor.'], 500);
        }

        ProcessSensorData::dispatch();

        return response()->json(['success' => true]);
    }
}
