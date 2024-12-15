<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
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
        $date = now()->format('Y-m-d_H-i-s');
        $fileName = "sensor_data_{$date}.json";

        Storage::disk('local')->put("sensor_data/{$fileName}", json_encode($data, JSON_PRETTY_PRINT));
    }

    public function render()
    {
        return view('livewire.measure-sensor-data');
    }
}
