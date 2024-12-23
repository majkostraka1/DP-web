import 'bootstrap';
import sensor from './sensor.js';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'

Alpine.data('sensor', sensor);
Alpine.plugin(Clipboard);

Livewire.start()
