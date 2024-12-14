import './bootstrap';
// import Alpine from 'alpinejs';
// // import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import sensor from './sensor.js';
//
// window.Alpine = Alpine;
//
// Alpine.data('sensor', sensor);
//
// Alpine.start();

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'

Alpine.data('sensor', sensor);
Alpine.plugin(Clipboard);


Livewire.start()
