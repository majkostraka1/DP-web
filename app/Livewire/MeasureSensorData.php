<?php

namespace App\Livewire;

use Livewire\Component;

class MeasureSensorData extends Component
{
    protected $listeners = ['saveSensorData'];

    public function mount()
    {
        //
    }

    public function saveSensorData($data)
    {
        dd($data);
        // Validácia dát
        $validated = collect($data)->only(['activity', 'elapsedTime', 'uid', 'sensorData']);

        // Generovanie názvu súboru (aktuálny dátum + unikátny hash)
        $timestamp = now()->format('Ymd_His');
        $hash = uniqid();
        $filename = "sensor_data/{$timestamp}_{$hash}.json";

        // Uloženie dát vo formáte JSON
        Storage::disk('local')->put($filename, json_encode($validated->toArray(), JSON_PRETTY_PRINT));

        // Vráť odpoveď, že dáta boli uložené
        return response()->json(['success' => true, 'file' => $filename]);
    }

    public function render()
    {
        return view('livewire.measure-sensor-data');
    }
}
