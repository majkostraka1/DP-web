<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class MeasureSensorData extends Component
{
    protected $listeners = ['saveSensorData'];

    public array $options = ['walk', 'car', 'train', 'tram', 'lie', 'sit', 'stand', 'bus', 'ontable', 'stairsUp', 'stairsDown', 'metro', 'run', 'other', 'jumping', 'spinning'];

    public string $identifier;

    public string $option;
    public function saveSensorData($data)
    {
        $data['uid'] = $this->identifier ?? '';

        $date = now()->format('Y-m-d_H-i-s');
        $fileName = "sensor_data_{$date}.json";

        try {
            if (in_array($data['activity'], ['jumping', 'spinning'])) {
                Storage::disk('local')->put("sensor_data_extra/{$fileName}", json_encode($data, JSON_PRETTY_PRINT));
            } else {
                Storage::disk('local')->put("sensor_data/{$fileName}", json_encode($data, JSON_PRETTY_PRINT));
            }
        } catch (\Exception $exception) {
            $errorMessage = [
                'timestamp' => now()->toDateTimeString(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ];

            Storage::disk('local')->put("public/error_{$date}.log", json_encode($errorMessage, JSON_PRETTY_PRINT));
        }

    }

    public function render()
    {
        return view('livewire.measure-sensor-data');
    }
}
