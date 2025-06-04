<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SensorPrediction extends Component
{
    public $predictionResult = '';

    public string $_uid;

    public string $prediction = "-";

    protected $listeners = ['sendSensorData', 'clearData'];

    public function mount() {
        $this->_uid = uniqid();
    }

    public function sendSensorData($data = null)
    {
        $data['uid'] = $this->_uid;

        $response = Http::post('http://127.0.0.1:5050/third-predict', $data);

        if ($response->successful()) {
            $result = $response->json();

            $this->prediction = $result['predicted_class'];

            $this->dispatch('prediction-updated', $result);
        } else {
            $this->predictionResult = 'Chyba: ' . $response->status();
        }


    }

    public function clearData()
    {
        $response = Http::post('http://127.0.0.1:5050/clear-data', ['uid' => $this->_uid]);

        $this->prediction = "-";

    }

    public function render()
    {
        return view('livewire.sensor-prediction');
    }
}
