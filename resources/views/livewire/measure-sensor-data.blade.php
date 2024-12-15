<div>
    <div class="container mt-5" x-data="sensor">
        <h1 class="text-center mb-4">Detekcia Senzorov</h1>

        <div class="text-center mb-4">
            <div class="form-group">
                <label for="activitySelector">Vyberte aktivitu:</label>
                <select id="activitySelector" class="form-control d-inline w-50" x-model="activity">
                    <option value="lie">Ležanie</option>
                    <option value="sit">Sedenie</option>
                    <option value="walk">Chôdza</option>
                </select>
            </div>
            <button class="btn btn-success mr-2" x-on:click="start">Spusti meranie</button>
            <button class="btn btn-danger" x-on:click="stop" :disabled="!recording">Zastav meranie</button>
        </div>

        <div class="sensor-card">
            <h4>Akcelerometer:</h4>
            <div id="accelerometer">Nedostupné</div>
        </div>
        <div class="sensor-card">
            <h4>Gyroskop:</h4>
            <div id="gyroscope">Nedostupné</div>
        </div>
        <div class="sensor-card">
            <h4>Magnetometer:</h4>
            <div id="magnetometer">Nedostupné</div>
        </div>
        <div class="sensor-card">
            <h4>Absolútna orientácia:</h4>
            <div id="absoluteOrientation">Nedostupné</div>
        </div>
        <div class="sensor-card">
            <h4>Relatívna orientácia:</h4>
            <div id="relativeOrientation">Nedostupné</div>
        </div>
        <div class="sensor-card" id="test">
            <h4>Relatívna orientácia:</h4>
            <div id="relativeOrientation">Nedostupné</div>
        </div>
    </div>
</div>