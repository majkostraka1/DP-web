<?php

return [
    'title' => 'Sensor Detection',
    'select-activity' => 'Select an activity:',
    'activities' => [
        '-' => '-',
        'walk' => 'Walking',
        'car' => 'Driving a car',
        'train' => 'Riding a train',
        'tram' => 'Riding a tram',
        'lie' => 'Lying down',
        'sit' => 'Sitting',
        'stand' => 'Standing',
        'bus' => 'Riding a bus',
        'ontable' => 'Phone lying on a table',
        'stairsUp' => 'Walking upstairs',
        'stairsDown' => 'Walking downstairs',
        'metro' => 'Riding the subway',
        'run' => 'Running',
        'other' => 'Other',
        'jumping' => 'Jumping (in place)',
        'spinning' => 'Rotation (in place)',
    ],

    'identifier' => 'Identifier:',
    'start-measure' => 'Start measuring',
    'stop-measure' => 'Stop measuring',
    'measure-start-msg' => 'Measurement will start in: <span x-text="countdown"></span> seconds...',
    'measure-start-msg-2' => 'You need to put the phone into your pants pocket. Unfortunately, the phone must remain unlocked',

    'accelerometer' => 'Accelerometer',
    'gyroscope' => 'Gyroscope',
    'magnetometer' => 'Magnetometer',
    'relative-orientation' => 'Relative orientation',
    'absolute-orientation' => 'Absolute orientation',
    'not-available' => ' NaN ',

    'activity-prediction-label' => 'Activity prediction',
    'activity-predition' => 'Predicted activity',

    'readme' => [
        'header' => 'User Guide',
        'p1' => 'This web application measures data from five sensors: accelerometer, gyroscope, magnetometer, absolute orientation, and relative orientation.',
        'p2' => 'If a sensor is available, its card will appear green. If a sensor is unavailable, its card will appear red.',
        'p3' => 'To enable the magnetometer in Google Chrome, enter <code>chrome://flags/</code> in the address bar and enable <strong>#enable-generic-sensor-extra-classes</strong>.',
        'p4' => 'For best results, use Google Chrome. <strong>Safari is not supported.</strong>',
        'p5' => 'Before measuring, select an activity and optionally enter a device identifier (useful for filtering measurements if the user doesn’t remember the measuring time).',
        'p6' => 'After clicking <strong>Start measuring</strong>, a 10-second countdown will begin. During this time, place the device in your pants pocket and ensure it remains unlocked.',
        'p7' => 'Once the countdown ends, measurements will begin at a frequency of 50 Hz.',
        'p8' => 'Stop the measurement by pressing <strong>Stop measuring</strong>.',
    ],

    'classification-detail' => [
        'label' => 'Detailed classifier results',
        'raw-model' => 'RAW model (weight 0.2): ',
        'lstm-model' => 'LSTM model (weight 0.4): ',
        'gru-model' => 'GRU model (weight 0.4): ',
        'ensembled-prob' => 'Total weighted probability:'
    ],
];
