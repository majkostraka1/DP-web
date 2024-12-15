export default () => {
    return {
        recording: false,
        activity: 'lie',
        startTime: 0,
        elapsedTime: 0,
        uid: '12345', // Unique device ID
        sensorData: {
            acce: [],
            gyro: [],
            magnet: [],
            absOri: [],
            relOri: [],
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
                        // console.log(this.sensor);

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
                console.warn(`Service pre sensor "${sensorKey}" nie je dostupný (senzor nie je podporovaný).`);
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
            if ('Accelerometer' in window)
                this.accelerometer = new this.SensorService(Accelerometer, 20);
            if ('Gyroscope' in window)
                this.gyroscope = new this.SensorService(Gyroscope, 20);
            if ('Magnetometer' in window)
                this.magnetometer = new this.SensorService(Magnetometer, 20);
            if ('RelativeOrientationSensor' in window)
                this.relOrientation = new this.SensorService(RelativeOrientationSensor, 20);
            if ('AbsoluteOrientationSensor' in window)
                this.absOrientation = new this.SensorService(AbsoluteOrientationSensor, 20);

            window.addEventListener('sensorDataSaved', (event) => {
                console.log(event);
                alert(event.detail.message); // Zobraz správu používateľovi
            });
        },


        start() {
            this.recording = true;
            this.startTime = Date.now();
            this.sensorData = {acce: [], gyro: [], magnet: [], absOri: [], relOri: []};

            // Akcelerometer
            this.startSensorStream(
                this.accelerometer,
                'acce',
                (t, data) => ({t: t, x: data.x, y: data.y, z: data.z})
            );

            // Gyroskop
            this.startSensorStream(
                this.gyroscope,
                'gyro',
                (t, data) => ({t: t, x: data.x, y: data.y, z: data.z})
            );

            // Magnetometer
            this.startSensorStream(
                this.magnetometer,
                'magnet',
                (t, data) => ({t: t, x: data.x, y: data.y, z: data.z})
            );

            // Absolútna orientácia
            this.startSensorStream(
                this.absOrientation,
                'absOri',
                (t, data) => ({t: t, x: data.x, y: data.y, z: data.z})
            );

            // Relatívna orientácia
            this.startSensorStream(
                this.relOrientation,
                'relOri',
                (t, data) => ({t: t, x: data.x, y: data.y, z: data.z})
            );
        },

        stop() {
            this.recording = false;

            const sensors = [this.accelerometer, this.gyroscope, this.magnetometer, this.absOrientation, this.relOrientation];

            sensors.filter(service => service && typeof service.turnOff === 'function')
                .forEach(service => service.turnOff());

            console.log("Meranie bolo zastavené.");

            this.sendDataToServer();
        },


        sendDataToServer() {
            const dataToSend = {
                activity: this.activity,
                elapsedTime: Date.now() - this.startTime,
                uid: this.uid,
                sensorData: this.sensorData,
            };

            Livewire.dispatch('saveSensorData', {data: dataToSend});
        }
    };
};
