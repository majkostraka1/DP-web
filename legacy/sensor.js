function motion(event){
    let aX = event.accelerationIncludingGravity.x*10;
    let aY = event.accelerationIncludingGravity.y*10;
    let aZ = event.accelerationIncludingGravity.z*10;

    // ix aY is negative, switch rotation
	if (aY <0) {
		aX = -aX - 180;
	}

    document.getElementById('accelerometer').textContent = "x: "
        + aX.toFixed(2) + ", y: "
        + aY.toFixed(2) + ", z: "
        + aZ.toFixed(2);

    document.querySelector("#accelerometer-block").style.transform="rotate("+ ( -aX) +"deg)";
}

document.addEventListener('DOMContentLoaded', function () {
    // Scéna
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / 400, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ alpha: true });
    const width = document.getElementById('accelerometerScene').clientWidth;
    const height = document.getElementById('accelerometerScene').clientHeight;
    renderer.setSize(width, height);
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    document.getElementById('accelerometerScene').appendChild(renderer.domElement);

    // Pridaj biele pozadie
    scene.background = new THREE.Color(0xffffff);

    // Pridaj obrázok telefónu
    const textureLoader = new THREE.TextureLoader();
    const phoneTexture = textureLoader.load('phone.png'); // Uprav názov súboru podľa potreby
    const geometry = new THREE.PlaneGeometry(1.5, 3); // Zvýšená šírka a výška pre telefón
    const material = new THREE.MeshBasicMaterial({ map: phoneTexture });
    const phone = new THREE.Mesh(geometry, material);
    scene.add(phone);
    camera.position.z = 2.5; // Posuň kameru späť, aby sa telefón zmestil do zorného poľa

    // Animácia
    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }
    animate();


    // Akcelerometer
    if(window.DeviceMotionEvent){
        window.addEventListener("devicemotion", motion, false);
    } else{
        document.getElementById('accelerometer').textContent = "DeviceMotionEvent nie je podporovaný.";
    }

    // Gyroskop
    if ('Gyroscope' in window) {
        try {
            const gyroscope = new Gyroscope({ frequency: 60 });
            gyroscope.addEventListener('reading', () => {
                let x = gyroscope.x;
                let y = gyroscope.y;
                let z = gyroscope.z;

                document.getElementById('gyroscope').textContent =
                    `X: ${x.toFixed(2)}, Y: ${y.toFixed(2)}, Z: ${z.toFixed(2)}`;

                // Aktualizácia orientácie telefónu
                phone.rotation.x += x * Math.PI / 180; // Záporná hodnota pre správne naklonenie
                phone.rotation.y += y * Math.PI / 180;  // Y-osa pre naklonenie do strán
            });
            gyroscope.start();
        } catch (error) {
            document.getElementById('gyroscope').textContent = 'Chyba: ' + error;
        }
    } else {
        document.getElementById('gyroscope').textContent = 'Gyroskop nie je podporovaný.';
    }

    // Magnetometer
    if ('Magnetometer' in window) {
        try {
            let magnetometer = new Magnetometer({ frequency: 60 });
            magnetometer.addEventListener('reading', () => {
                document.getElementById('magnetometer').textContent =
                    `X: ${magnetometer.x.toFixed(2)}, Y: ${magnetometer.y.toFixed(2)}, Z: ${magnetometer.z.toFixed(2)}`;
            });
            magnetometer.start();
        } catch (error) {
            document.getElementById('magnetometer').textContent = 'Chyba: ' + error;
        }
    } else {
        document.getElementById('magnetometer').textContent = 'Magnetometer nie je podporovaný.';
    }

    // Senzor okolitého svetla
    if ('AmbientLightSensor' in window) {
        try {
            let ambientLight = new AmbientLightSensor();
            ambientLight.addEventListener('reading', () => {
                document.getElementById('ambientLight').textContent =
                    `Intenzita svetla: ${ambientLight.illuminance.toFixed(2)} lux`;
            });
            ambientLight.start();
        } catch (error) {
            document.getElementById('ambientLight').textContent = 'Chyba: ' + error;
        }
    } else {
        document.getElementById('ambientLight').textContent = 'Senzor okolitého svetla nie je podporovaný.';
    }

    // Absolútna orientácia
    if ('AbsoluteOrientationSensor' in window) {
        try {
            let absoluteOrientation = new AbsoluteOrientationSensor({ frequency: 60 });
            absoluteOrientation.addEventListener('reading', () => {
                document.getElementById('absoluteOrientation').textContent =
                    `Quaternion: [${absoluteOrientation.quaternion.map(q => q.toFixed(2)).join(', ')}]`;
            });
            absoluteOrientation.start();
        } catch (error) {
            document.getElementById('absoluteOrientation').textContent = 'Chyba: ' + error;
        }
    } else {
        document.getElementById('absoluteOrientation').textContent = 'Absolútna orientácia nie je podporovaná.';
    }

    // Relatívna orientácia
    if ('RelativeOrientationSensor' in window) {
        try {
            let relativeOrientation = new RelativeOrientationSensor({ frequency: 60 });
            relativeOrientation.addEventListener('reading', () => {
                document.getElementById('relativeOrientation').textContent =
                    `Quaternion: [${relativeOrientation.quaternion.map(q => q.toFixed(2)).join(', ')}]`;
            });
            relativeOrientation.start();
        } catch (error) {
            document.getElementById('relativeOrientation').textContent = 'Chyba: ' + error;
        }
    } else {
        document.getElementById('relativeOrientation').textContent = 'Relatívna orientácia nie je podporovaná.';
    }

});