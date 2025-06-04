<?php

return [
    'title' => 'Detekce senzorů',
    'select-activity' => 'Vyberte aktivitu:',
    'activities' => [
        '-' => '-',
        'walk' => 'Chůze',
        'car' => 'Jízda autem',
        'train' => 'Jízda vlakem',
        'tram' => 'Jízda tramvají',
        'lie' => 'Ležení',
        'sit' => 'Sezení',
        'stand' => 'Stání',
        'bus' => 'Jízda autobusem',
        'ontable' => 'Mobil položený na stole',
        'stairsUp' => 'Chůze do schodů',
        'stairsDown' => 'Chůze ze schodů',
        'metro' => 'Jízda metrem',
        'run' => 'Běh',
        'other' => 'Ostatní',
        'jumping' => 'Skákání (na místě)',
        'spinning' => 'Otáčení (na místě)',
    ],

    'identifier' => 'Identifikátor:',
    'start-measure' => 'Spustit měření',
    'stop-measure' => 'Zastavit měření',
    'measure-start-msg' => 'Měření začne za: <span x-text="countdown"></span> sekund...',
    'measure-start-msg-2' => 'Telefon je třeba vložit do kapsy u kalhot. Telefon ale bohužel musí být odemčený',

    'accelerometer' => 'Akcelerometr',
    'gyroscope' => 'Gyroskop',
    'magnetometer' => 'Magnetometr',
    'relative-orientation' => 'Relativní orientace',
    'absolute-orientation' => 'Absolutní orientace',
    'not-available' => ' NaN ',

    'activity-prediction-label' => 'Predikce aktivity',
    'activity-predition' => 'Predikovaná aktivita',

    'readme' => [
        'header' => 'Návod k použití',
        'p1' => 'Tento web měří data z pěti senzorů: akcelerometr, gyroskop, magnetometr, absolutní a relativní orientace.',
        'p2' => 'Pokud je senzor dostupný, karta senzoru bude zelená. Pokud není dostupný, karta bude červená.',
        'p3' => 'Pro povolení magnetometru v prohlížeči Google Chrome zadejte do adresního řádku <code>chrome://flags/</code> a povolte <strong>#enable-generic-sensor-extra-classes</strong>.',
        'p4' => 'Pro nejlepší výsledky použijte Google Chrome. <strong>Safari není podporováno.</strong>',
        'p5' => 'Před měřením vyberte aktivitu a volitelně zadejte identifikátor zařízení (slouží k filtrování měření, pokud uživatel nezná přesný čas měření).',
        'p6' => 'Po stisku tlačítka <strong>Spustit měření</strong> se spustí 10sekundové odpočítávání. Během této doby vložte zařízení do kapsy u kalhot a ujistěte se, že je odemčené.',
        'p7' => 'Po uplynutí odpočtu začne měření s frekvencí 50 Hz.',
        'p8' => 'Měření ukončíte stisknutím tlačítka <strong>Zastavit měření</strong>.',
    ],


    'classification-detail' => [
        'label' => 'Detailní výsledky klasifikátorů',
        'raw-model' => 'RAW model (váha 0.2): ',
        'lstm-model' => 'LSTM model (váha 0.4): ',
        'gru-model' => 'GRU model (váha 0.4): ',
        'ensembled-prob' => 'Celková váhová pravděpodobnost:'
    ],
];
