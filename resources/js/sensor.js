export default () => ({
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



    AccelerometerService: {
        accelerometer: null,
        frequency: 10, // Hz - 10x za sekundu, každých 100 ms
        intervalId: null,
        isOn: false,
        callback: null,

        init: function(freq = 10) {
            this.frequency = freq;
            this.accelerometer = new Accelerometer({ frequency: this.frequency });
            this.accelerometer.addEventListener('error', (event) => {
                console.error('Chyba akcelerometra:', event.error.name, event.error.message);
            });
        },

        turnOn: function(callback) {
            if (!this.accelerometer) {
                console.error("Akcelerometer nie je inicializovaný. Zavolajte najskôr init().");
                return;
            }
            if (this.isOn) return; // Už beží

            this.isOn = true;
            this.callback = callback;
            this.accelerometer.start();

            const intervalMs = 1000 / this.frequency;
            this.intervalId = setInterval(() => {
                if (this.isOn) {
                    const data = {
                        x: this.accelerometer.x,
                        y: this.accelerometer.y,
                        z: this.accelerometer.z,
                        timestamp: Date.now()
                    };
                    if (typeof this.callback === 'function') {
                        this.callback(data);
                    } else {
                        console.log("Namerané dáta:", data);
                    }
                }
            }, intervalMs);
        },

        turnOff: function() {
            if (!this.isOn) return;
            this.isOn = false;
            this.accelerometer.stop();
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
            this.callback = null;
        }
    },

    init() {
        console.log("Komponent inicializovaný");
        this.AccelerometerService.init(10); // Pripravíme akcelerometer
    },


    startAccelerometer() {
        this.recording = true;
        this.startTime = Date.now();
        this.sensorData = { acce: [], gyro: [], magnet: [], absOri: [], relOri: [] };

        // Spustíme akcelerometer a každých 100 ms ukladáme dáta
        this.AccelerometerService.turnOn((data) => {
            const t = Date.now() - this.startTime;
            this.sensorData.acce.push({ t, x: data.x, y: data.y, z: data.z });
            // Tu môžete dáta posielať na server, alebo zobraziť v UI
            document.getElementById('accelerometer').textContent = `x: ${data.x.toFixed(2)}, y: ${data.y.toFixed(2)}, z: ${data.z.toFixed(2)}`;
        });
    },

    stopAccelerometer() {
        this.recording = false;
        this.AccelerometerService.turnOff();
        console.log("Meranie bolo zastavené.");

        // Teraz môžeme dáta odoslať na server
        this.sendDataToServer();
    },










    startRecording() {
        this.recording = true;
        this.startTime = Date.now();
        this.sensorData = { acce: [], gyro: [], magnet: [], absOri: [], relOri: [] };
        console.log(`Meranie spustené pre aktivitu: ${this.activity}`);
        this.startSensorListeners();
    },

    stopRecording() {
        this.recording = false;
        console.log('Meranie zastavené.');
        this.sendDataToServer();
    },

    startSensorListeners() {
        if (window.DeviceMotionEvent) {
            window.addEventListener('devicemotion', (event) => {
                console.log('ahoj');
                if (!this.recording) return;
                const t = Date.now() - this.startTime;
                const aX = event.accelerationIncludingGravity.x * 10;
                const aY = event.accelerationIncludingGravity.y * 10;
                const aZ = event.accelerationIncludingGravity.z * 10;
                this.sensorData.acce.push({ t, x: aX, y: aY, z: aZ });
                document.getElementById('accelerometer').textContent = `x: ${aX.toFixed(2)}, y: ${aY.toFixed(2)}, z: ${aZ.toFixed(2)}`;
            });
        } else {
            document.getElementById('accelerometer').textContent = "DeviceMotionEvent nie je podporovaný.";
        }

        if ('Gyroscope' in window) {
            try {
                const gyroscope = new Gyroscope({ frequency: 60 });
                gyroscope.addEventListener('reading', () => {
                    if (!this.recording) return;
                    const t = Date.now() - this.startTime;
                    this.sensorData.gyro.push({ t, x: gyroscope.x, y: gyroscope.y, z: gyroscope.z });
                    document.getElementById('gyroscope').textContent = `x: ${gyroscope.x.toFixed(2)}, y: ${gyroscope.y.toFixed(2)}, z: ${gyroscope.z.toFixed(2)}`;
                });
                gyroscope.start();
            } catch (error) {
                document.getElementById('gyroscope').textContent = 'Chyba: ' + error;
            }
        } else {
            document.getElementById('gyroscope').textContent = 'Gyroskop nie je podporovaný.';
        }

        if ('Magnetometer' in window) {
            try {
                let magnetometer = new Magnetometer({ frequency: 60 });
                magnetometer.addEventListener('reading', () => {
                    if (!this.recording) return;
                    const t = Date.now() - this.startTime;
                    this.sensorData.magnet.push({ t, x: magnetometer.x, y: magnetometer.y, z: magnetometer.z });
                    document.getElementById('magnetometer').textContent = `x: ${magnetometer.x.toFixed(2)}, y: ${magnetometer.y.toFixed(2)}, z: ${magnetometer.z.toFixed(2)}`;
                });
                magnetometer.start();
            } catch (error) {
                document.getElementById('magnetometer').textContent = 'Chyba: ' + error;
            }
        } else {
            document.getElementById('magnetometer').textContent = 'Magnetometer nie je podporovaný.';
        }

        if ('AbsoluteOrientationSensor' in window) {
            try {
                let absoluteOrientation = new AbsoluteOrientationSensor({ frequency: 60 });
                absoluteOrientation.addEventListener('reading', () => {
                    if (!this.recording) return;
                    const t = Date.now() - this.startTime;
                    this.sensorData.absOri.push({ t, quaternion: absoluteOrientation.quaternion.map(q => q.toFixed(2)) });
                    document.getElementById('absoluteOrientation').textContent = `Quaternion: [${absoluteOrientation.quaternion.map(q => q.toFixed(2)).join(', ')}]`;
                });
                absoluteOrientation.start();
            } catch (error) {
                document.getElementById('absoluteOrientation').textContent = 'Chyba: ' + error;
            }
        } else {
            document.getElementById('absoluteOrientation').textContent = 'Absolútna orientácia nie je podporovaná.';
        }

        if ('RelativeOrientationSensor' in window) {
            try {
                let relativeOrientation = new RelativeOrientationSensor({ frequency: 60 });
                relativeOrientation.addEventListener('reading', () => {
                    if (!this.recording) return;
                    const t = Date.now() - this.startTime;
                    this.sensorData.relOri.push({ t, quaternion: relativeOrientation.quaternion.map(q => q.toFixed(2)) });
                    document.getElementById('relativeOrientation').textContent = `Quaternion: [${relativeOrientation.quaternion.map(q => q.toFixed(2)).join(', ')}]`;
                });
                relativeOrientation.start();
            } catch (error) {
                document.getElementById('relativeOrientation').textContent = 'Chyba: ' + error;
            }
        } else {
            document.getElementById('relativeOrientation').textContent = 'Relatívna orientácia nie je podporovaná.';
        }
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
});
