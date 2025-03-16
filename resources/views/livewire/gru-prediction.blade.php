<div>
    <div class="container my-5">
        <h1 class="text-center">@lang('locale.activity-prediction-label')</h1>

        <div class="mb-4 p-3" x-data="neuralNetwork">

            <div class="card">
                <div class="row g-0">
                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <template x-if="predictedClass == 'walk'">
                            <i class="fa-solid fa-person-walking fa-3x"></i>
                        </template>
                        <template x-if="predictedClass == 'car'">
                            <i class="fa-solid fa-car fa-3x"></i>
                        </template>
                        <template x-if="predictedClass == 'lie'">
                            <i class="fa-solid fa-bed fa-3x"></i>
                        </template>
                        <template x-if="predictedClass == 'sit'">
                            <i class="fa-solid fa-wheelchair fa-3x"></i>
                        </template>
                        <template x-if="predictedClass == 'stand'">
                            <i class="fa-solid fa-person fa-3x"></i>
                        </template>
                        <template x-if="predictedClass == 'ontable'">
                            <i class="fa-solid fa-mobile-screen fa-3x"></i>
                        </template>
                        <template x-if="predictedClass == 'run'">
                            <i class="fa-solid fa-person-running fa-3x"></i>
                        </template>

                        <template x-if="!['walk', 'car', 'lie', 'sit', 'stand', 'ontable', 'run'].includes(predictedClass)">
                            <i class="fas fa-question fa-3x"></i>
                        </template>
                    </div>

                    <div class="col-8">
                        <div class="card-body">
                            <h5 class="text-center">@lang('locale.activity-predition')</h5>
                            <p class="text-center" x-text="predictedClass ? predictedClass : '-'"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 text-center">
                <div>
                    <template x-if="!recording">
                        <button class="btn btn-success" x-on:click="startPrediction">@lang('locale.start-measure')</button>
                    </template>

                    <template x-if="recording">
                        <button class="btn btn-danger" x-on:click="stopPrediction">@lang('locale.stop-measure')</button>
                    </template>
                </div>
            </div>

        </div>
    </div>
</div>
