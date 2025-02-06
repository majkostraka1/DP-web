<div>
    <div class="container mt-5" x-data="sensor" x-ref="sensors">
        <h1 class="text-center mb-4">@lang('locale.title')</h1>

        <div wire:loading>
            <div class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center" style="z-index: 1050;">
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <div class="text-center mb-4">
            <div class="row mb-3 align-items-center">
                <label for="activitySelector" class="col-3 text-start text-md-end">@lang('locale.select-activity')</label>
                <div class="col-9">
                    <select id="activitySelector" class="form-control" x-model="activity">
                        @foreach($options as $option)
                            <option value="{{ $option }}">{{ __('locale.activities.' . $option) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label for="deviceIdentifier" class="col-3 text-start text-md-end">@lang('locale.identifier')</label>
                <div class="col-9">
                    <input id="deviceIdentifier" type="text" class="form-control" wire:model="identifier">
                </div>
            </div>


            <div class="row my-5">
                <div>
                    <template x-if="!recording && countdown === 0">
                        <button class="btn btn-success mr-2" x-on:click="start">@lang('locale.start-measure')</button>
                    </template>

                    <template x-if="countdown > 0">
                        <div class="text-muted font-weight-bold">
                            @lang('locale.measure-start-msg')
                            <p class="text-muted mx-5">@lang('locale.measure-start-msg-2')</p>
                        </div>
                    </template>

                    <template x-if="recording">
                        <button class="btn btn-danger" x-on:click="stop">@lang('locale.stop-measure')</button>
                    </template>
                </div>
            </div>

        </div>

        <!-- start:sensors status block -->
        <div class="row" wire:ignore>
            <!-- Akcelerometer -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-sm" id="accelerometer">
                    <div class="card-body d-flex align-items-center">
                        <i class="fa-brands fa-accessible-icon fa-3x"></i>
                        <div class="ms-4">
                            <h5>@lang('locale.accelerometer')</h5>
                            <div x-text="sensorData.accelerometer.length
                                        ?  `x: ${ sensorData.accelerometer[sensorData.accelerometer.length - 1].x ? sensorData.accelerometer[sensorData.accelerometer.length - 1].x.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            y: ${ sensorData.accelerometer[sensorData.accelerometer.length - 1].y ? sensorData.accelerometer[sensorData.accelerometer.length - 1].y.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            z: ${ sensorData.accelerometer[sensorData.accelerometer.length - 1].z ? sensorData.accelerometer[sensorData.accelerometer.length - 1].z.toFixed(3) : '{{ __('locale.not-available') }}' }`
                                        : '{{ __('locale.not-available') }}'">
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
                            <h5>@lang('locale.gyroscope')</h5>
                            <div x-text="sensorData.gyroscope.length
                                        ?  `x: ${ sensorData.gyroscope[sensorData.gyroscope.length - 1].x ? sensorData.gyroscope[sensorData.gyroscope.length - 1].x.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            y: ${ sensorData.gyroscope[sensorData.gyroscope.length - 1].y ? sensorData.gyroscope[sensorData.gyroscope.length - 1].y.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            z: ${ sensorData.gyroscope[sensorData.gyroscope.length - 1].z ? sensorData.gyroscope[sensorData.gyroscope.length - 1].z.toFixed(3) : '{{ __('locale.not-available') }}' }`
                                        : '{{ __('locale.not-available') }}'"
                            >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

{{--            <!-- Magnetometer -->--}}
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-sm" id="magnetometer">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-magnet fa-3x"></i>
                        <div class="ms-4">
                            <h5>@lang('locale.magnetometer')</h5>
                            <div x-text="sensorData.magnetometer.length
                                        ?  `x: ${ sensorData.magnetometer[sensorData.magnetometer.length - 1].x ? sensorData.magnetometer[sensorData.magnetometer.length - 1].x.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            y: ${ sensorData.magnetometer[sensorData.magnetometer.length - 1].y ? sensorData.magnetometer[sensorData.magnetometer.length - 1].y.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            z: ${ sensorData.magnetometer[sensorData.magnetometer.length - 1].z ? sensorData.magnetometer[sensorData.magnetometer.length - 1].z.toFixed(3) : '{{ __('locale.not-available') }}' }`
                                        : '{{ __('locale.not-available') }}'"
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
                            <h5>@lang('locale.absolute-orientation')</h5>
                            <div x-text="sensorData.absOrientation.length
                                        ?  `α: ${ sensorData.absOrientation[sensorData.absOrientation.length - 1].x ? sensorData.absOrientation[sensorData.absOrientation.length - 1].x.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            β: ${ sensorData.absOrientation[sensorData.absOrientation.length - 1].y ? sensorData.absOrientation[sensorData.absOrientation.length - 1].y.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            γ: ${ sensorData.absOrientation[sensorData.absOrientation.length - 1].z ? sensorData.absOrientation[sensorData.absOrientation.length - 1].z.toFixed(3) : '{{ __('locale.not-available') }}' }`
                                        : '{{ __('locale.not-available') }}'"
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
                            <h5>@lang('locale.relative-orientation')</h5>
                            <div x-text="sensorData.relOrientation.length
                                        ?  `α: ${ sensorData.relOrientation[sensorData.relOrientation.length - 1].x ? sensorData.relOrientation[sensorData.relOrientation.length - 1].x.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            β: ${ sensorData.relOrientation[sensorData.relOrientation.length - 1].y ? sensorData.relOrientation[sensorData.relOrientation.length - 1].y.toFixed(3) : '{{ __('locale.not-available') }}' },
                                            γ: ${ sensorData.relOrientation[sensorData.relOrientation.length - 1].z ? sensorData.relOrientation[sensorData.relOrientation.length - 1].z.toFixed(3) : '{{ __('locale.not-available') }}' }`
                                        : '{{ __('locale.not-available') }}'"
                            >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end:sensors status block -->


        <div class="my-5" id="readme">
            <h3>@lang('locale.readme.header')</h3>
            <ul>
                <li>@lang('locale.readme.p1')</li>
                <li>@lang('locale.readme.p2')</li>
                <li>@lang('locale.readme.p3')</li>
                <li>@lang('locale.readme.p4')</li>
                <li>@lang('locale.readme.p5')</li>
                <li>@lang('locale.readme.p6')</li>
                <li>@lang('locale.readme.p7')</li>
                <li>@lang('locale.readme.p8')</li>
            </ul>
        </div>
    </div>
</div>