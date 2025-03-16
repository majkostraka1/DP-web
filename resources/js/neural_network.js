export default () => {
    return {
        recording: false,
        sensorData: {
            accelerometer: [],
            gyroscope: [],
            magnetometer: [],
            absOrientation: [],
            relOrientation: []
        },
        accelerometer: null,
        gyroscope: null,
        magnetometer: null,
        absOrientation: null,
        relOrientation: null,
        sendTimer: null,
        predictedClass: '-',

        // Trieda pre prácu so senzorom
        SensorService: class {
            constructor(type, frequency = 10) {
                this.frequency = frequency;
                this.sensor = new type({ frequency: this.frequency });
                this.type = type.name;
                this.enabled = false;
            }

            quaternionToEuler = (quaternion) => {
                const [q0, q1, q2, q3] = quaternion;
                const ysqr = q2 * q2;

                const t0 = 2.0 * (q0 * q1 + q2 * q3);
                const t1 = 1.0 - 2.0 * (q1 * q1 + ysqr);
                const roll = Math.atan2(t0, t1);

                let t2 = 2.0 * (q0 * q2 - q3 * q1);
                t2 = Math.max(Math.min(t2, 1.0), -1.0);
                const pitch = Math.asin(t2);

                const t3 = 2.0 * (q0 * q3 + q1 * q2);
                const t4 = 1.0 - 2.0 * (ysqr + q3 * q3);
                const yaw = Math.atan2(t3, t4);

                const alpha = yaw * (180 / Math.PI);
                const beta = pitch * (180 / Math.PI);
                const gamma = roll * (180 / Math.PI);

                return { alpha, beta, gamma };
            };

            streamData = async function* () {
                if (this.enabled) return;
                this.enabled = true;
                this.sensor.start();

                try {
                    while (this.enabled) {
                        const data = {
                            t: Date.now(),
                            x: this.sensor.x,
                            y: this.sensor.y,
                            z: this.sensor.z,
                        };

                        if (this.type === 'AbsoluteOrientationSensor' || this.type === 'RelativeOrientationSensor') {
                            const q = this.sensor.quaternion;
                            if (q) {
                                const { alpha, beta, gamma } = this.quaternionToEuler(q);
                                data.x = alpha;
                                data.y = beta;
                                data.z = gamma;
                            } else {
                                data.x = null;
                                data.y = null;
                                data.z = null;
                            }
                        }

                        yield data;
                        await new Promise(resolve => setTimeout(resolve, 1000 / this.frequency));
                    }
                } finally {
                    this.sensor.stop();
                }
            };

            turnOff = () => {
                this.enabled = false;
            };
        },

        startSensorStream(service, sensorKey, dataHandler) {
            if (!service) {
                console.warn(`Service pre sensor "${sensorKey}" nie je dostupný.`);
                return;
            }

            (async () => {
                for await (const data of service.streamData()) {
                    if (!this.recording) break;

                    const t = Date.now();
                    const entry = dataHandler(t, data);

                    // null hodnoty dopĺňam 0
                    const entryWithDefaults = {
                        ...entry,
                        x: entry.x ?? 0,
                        y: entry.y ?? 0,
                        z: entry.z ?? 0,
                    };


                    // Vypíše jednotlivé meranie do konzoly
                    // console.log(`Measurement from ${sensorKey}:`, entry);

                    this.sensorData[sensorKey].push(entryWithDefaults);
                }
            })();
        },

        // Inicializácia senzorov
        init() {
            const sensors = [
                { class: 'Accelerometer', id: 'accelerometer' },
                { class: 'Gyroscope', id: 'gyroscope' },
                { class: 'Magnetometer', id: 'magnetometer' },
                { class: 'RelativeOrientationSensor', id: 'relOrientation' },
                { class: 'AbsoluteOrientationSensor', id: 'absOrientation' },
            ];

            Livewire.on('prediction-updated', data => {
                this.predictedClass = data.pop().predicted_class;
            });

            sensors.forEach(({ class: sensorClass, id }) => {
                if (sensorClass in window) {
                    const SensorClass = window[sensorClass];
                    this[id] = new this.SensorService(SensorClass, 20);
                } else {
                    console.log("Sensor " + sensorClass + " not available");
                }
            });
        },

        // Spustí zber senzorových dát a každú sekundu odošle aktuálny buffer dát cez Livewire emit
        startPrediction() {
            this.recording = true;

            this.sensorData = {
                accelerometer: [],
                gyroscope: [],
                magnetometer: [],
                absOrientation: [],
                relOrientation: []
            };

            const sensors = [
                { service: this.accelerometer, key: 'accelerometer' },
                { service: this.gyroscope, key: 'gyroscope' },
                { service: this.magnetometer, key: 'magnetometer' },
                { service: this.absOrientation, key: 'absOrientation' },
                { service: this.relOrientation, key: 'relOrientation' },
            ];

            sensors.forEach(({ service, key }) => {
                if (service) {
                    this.startSensorStream(service, key, (t, data) => ({ t, x: data.x, y: data.y, z: data.z }));
                }
            });

            this.sendTimer = setInterval(() => {
                this.sendDataToLivewire();
                this.sensorData = {
                    accelerometer: [],
                    gyroscope: [],
                    magnetometer: [],
                    absOrientation: [],
                    relOrientation: []
                };
            }, 1000);
        },

        // Vyvolá Livewire event 'sendSensorData' s aktuálnymi senzorovými dátami
        sendDataToLivewire() {
            const dataToSend = {
                sensorData: this.sensorData,
                timestamp: Date.now()
            };
            // console.log("Odosielam senzorové dáta do Livewire:", dataToSend);
            Livewire.dispatch('sendSensorData', {data: dataToSend});
        },

        // Zastaví zber dát a odosielanie
        stopPrediction() {
            this.recording = false;
            clearInterval(this.sendTimer);
            const sensors = [this.accelerometer, this.gyroscope, this.magnetometer, this.absOrientation, this.relOrientation];
            Livewire.dispatch('clearData');
            sensors.forEach(service => service?.turnOff());
        }
    };
};
