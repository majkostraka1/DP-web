<div>
    <div class="container mt-5" x-data="sensor" x-ref="sensors">
        <h1 class="text-center mb-4">Detekcia Senzorov</h1>

        <div class="text-center mb-4">
            <div class="form-group">
                <label for="activitySelector">Vyberte aktivitu:</label>
                <select id="activitySelector" class="form-control d-inline w-50" x-model="activity">
                    @foreach($options as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <template x-if="!recording && countdown === 0">
                    <button class="btn btn-success mr-2" x-on:click="start">Spusti meranie</button>
                </template>

                <template x-if="countdown > 0">
                    <div class="text-primary font-weight-bold">
                        Meranie začne o: <span x-text="countdown"></span> sekúnd...
                    </div>
                </template>

                <template x-if="recording">
                    <button class="btn btn-danger" x-on:click="stop">Zastav meranie</button>
                </template>
            </div>
        </div>

        <!-- start:sensors status block -->
        <div class="row">
            <!-- Akcelerometer -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-sm" id="accelerometer">
                    <div class="card-body d-flex align-items-center">
                        <i class="fa-brands fa-accessible-icon fa-3x"></i>
                        <div class="ms-4">
                            <h5>Akcelerometer</h5>
                            <div x-text="sensorData.accelerometer.length
                                        ?  `x: ${sensorData.accelerometer[sensorData.accelerometer.length - 1]?.x.toFixed(3)},
                                            y: ${sensorData.accelerometer[sensorData.accelerometer.length - 1]?.y.toFixed(3)},
                                            z: ${sensorData.accelerometer[sensorData.accelerometer.length - 1]?.z.toFixed(3)}`
                                        : 'Nedostupné'"
                            >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gyroskop -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-sm" id="gyroscope">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-compass fa-3x"></i>
                        <div class="ms-4">
                            <h5>Gyroskop</h5>
                            <div x-text="sensorData.gyroscope.length
                                        ?  `x: ${sensorData.gyroscope[sensorData.gyroscope.length - 1]?.x.toFixed(3)},
                                            y: ${sensorData.gyroscope[sensorData.gyroscope.length - 1]?.y.toFixed(3)},
                                            z: ${sensorData.gyroscope[sensorData.gyroscope.length - 1]?.z.toFixed(3)}`
                                        : 'Nedostupné'"
                            >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Magnetometer -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-sm" id="magnetometer">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-magnet fa-3x"></i>
                        <div class="ms-4">
                            <h5>Magnetometer</h5>
                            <div x-text="sensorData.magnetometer.length
                                        ?  `x: ${sensorData.magnetometer[sensorData.magnetometer.length - 1]?.x.toFixed(3)},
                                            y: ${sensorData.magnetometer[sensorData.magnetometer.length - 1]?.y.toFixed(3)},
                                            z: ${sensorData.magnetometer[sensorData.magnetometer.length - 1]?.z.toFixed(3)}`
                                        : 'Nedostupné'"
                            >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Absolútna orientácia -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-sm" id="absOrientation">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-ruler-combined fa-3x"></i>
                        <div class="ms-4">
                            <h5>Absolútna orientácia</h5>
                            <div x-text="sensorData.absOrientation.length
                                        ?  `α: ${sensorData.absOrientation[sensorData.absOrientation.length - 1]?.x.toFixed(3)},
                                            β: ${sensorData.absOrientation[sensorData.absOrientation.length - 1]?.y.toFixed(3)},
                                            γ: ${sensorData.absOrientation[sensorData.absOrientation.length - 1]?.z.toFixed(3)}`
                                        : 'Nedostupné'"
                            >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relatívna orientácia -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-sm" id="relOrientation">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-sync-alt fa-3x"></i>
                        <div class="ms-4">
                            <h5>Relatívna orientácia</h5>
                            <div x-text="sensorData.relOrientation.length
                                        ?  `α: ${sensorData.relOrientation[sensorData.relOrientation.length - 1]?.x.toFixed(3)},
                                            β: ${sensorData.relOrientation[sensorData.relOrientation.length - 1]?.y.toFixed(3)},
                                            γ: ${sensorData.relOrientation[sensorData.relOrientation.length - 1]?.z.toFixed(3)}`
                                        : 'Nedostupné'"
                            >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end:sensors status block -->
    </div>
</div>