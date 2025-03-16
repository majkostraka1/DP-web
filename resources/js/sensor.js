export default () => {
    return {
        recording: false,
        countdown: 0,
        timer: null,
        activity: 'walk',
        selectedActivity: '',
        startTime: 0,
        sensorData: {
            accelerometer: [],
            gyroscope: [],
            magnetometer: [],
            absOrientation: [],
            relOrientation: [],
        },

        accelerometer: null,
        gyroscope: null,
        magnetometer: null,
        relOrientation: null,
        absOrientation: null,

        SensorService: class {
            constructor(type, frequency = 10) {
                this.frequency = frequency;
                this.sensor = new type({frequency: this.frequency});
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
            }

            streamData = async function* (){
                if (this.enabled) {
                    return;
                }

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

                    const t = Date.now() - this.startTime;

                    const entry = dataHandler(t, data);

                    this.sensorData[sensorKey].push(entry);
                }
            })();
        },

        init() {
            const sensors = [
                { class: 'Accelerometer', id: 'accelerometer' },
                { class: 'Gyroscope', id: 'gyroscope' },
                { class: 'Magnetometer', id: 'magnetometer' },
                { class: 'RelativeOrientationSensor', id: 'relOrientation' },
                { class: 'AbsoluteOrientationSensor', id: 'absOrientation' },
            ];

            sensors.forEach(({ class: sensorClass, id }) => {
                const element = document.getElementById(id);

                if (sensorClass in window) {
                    const SensorClass = window[sensorClass];
                    this[id] = new this.SensorService(SensorClass, 20);
                    element.classList.add('bg-success');
                } else {
                    element.classList.add('bg-danger');
                }
            });
        },


        start() {
            this.selectedActivity = this.activity;
            this.countdown = 5;
            this.sensorData = {accelerometer: [], gyroscope: [], magnetometer: [], absOrientation: [], relOrientation: []};

            const sensors = [
                { service: this.accelerometer, key: 'accelerometer' },
                { service: this.gyroscope, key: 'gyroscope' },
                { service: this.magnetometer, key: 'magnetometer' },
                { service: this.absOrientation, key: 'absOrientation' },
                { service: this.relOrientation, key: 'relOrientation' },
            ];


            this.timer = setInterval(() => {
                this.countdown -= 1;

                if (this.countdown === 0) {
                    clearInterval(this.timer);
                    this.timer = null;
                    this.recording = true;
                    this.startTime = Date.now();

                    sensors.forEach(({ service, key }) => {
                        if (service) {
                            this.startSensorStream(
                                service,
                                key,
                                (t, data) => ({ t: t, x: data.x, y: data.y, z: data.z })
                            );
                        }
                    });
                }
            }, 1000);



        },

        stop() {
            this.recording = false;
            const sensors = [this.accelerometer, this.gyroscope, this.magnetometer, this.absOrientation, this.relOrientation];
            sensors.forEach(service => service?.turnOff());

            this.sendDataToServer();
        },


        sendDataToServer() {
            const dataToSend = {
                activity: this.selectedActivity,
                uid: '',
                elapsedTime: this.startTime,
                sensorData: this.sensorData,
            };

            Livewire.dispatch('saveSensorData', {data: dataToSend});
        }
    };
};
