import 'bootstrap';
import sensor from './sensor.js';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'
import neural_network from "./neural_network.js";

Alpine.data('sensor', sensor);
Alpine.data('neuralNetwork', neural_network);
Alpine.plugin(Clipboard);

Livewire.start()
