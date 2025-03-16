<?php

return [
    'title' => 'Detekcia Senzorov',
    'select-activity' => 'Vyberte aktivitu:',
    'activities' => [
        'walk' => 'Kráčanie',
        'car' => 'Jazda autom',
        'train' => 'Jazda vlakom',
        'tram' => 'Jazda električkou',
        'lie' => 'Ležanie',
        'sit' => 'Sedenie',
        'stand' => 'Státie',
        'bus' => 'Jazda autobusom',
        'ontable' => 'Mobil položený na stole',
        'stairsUp' => 'Chôdza hore schodmi',
        'stairsDown' => 'Chôdza dole schodmi',
        'metro' => 'jazda metrom',
        'run' => 'Bežanie',
        'other' => 'Ostatné',
        'jumping' => 'Skákanie (na mieste)',
        'spinning' => 'Otáčanie (na mieste)',
    ],

    'identifier' => 'Identifikátor:',
    'start-measure' => 'Spusti meranie',
    'stop-measure' => 'Zastav meranie',
    'measure-start-msg' => 'Meranie začne o: <span x-text="countdown"></span> sekúnd...',
    'measure-start-msg-2' => 'Telefón je potrebné vložiť do vrecka na nohaviciach. Telefón ale bohužiaľ musí byť odblokovaný',

    'accelerometer' => 'Akcelerometer',
    'gyroscope' => 'Gyroskop',
    'magnetometer' => 'Magnetometer',
    'relative-orientation' => 'Relatívna orientácia',
    'absolute-orientation' => 'Absolútna orientácia',
    'not-available' => ' NaN ',

    'activity-prediction-label' => 'Predikcia aktivity',
    'activity-predition' => 'Predikovaná aktivita',

    'readme' => [
        'header' => 'Návod na použitie',
        'p1' => 'Web meria dáta z piatich senzorov: akcelerometer, gyroskop, magnetometer, absolútna a relatívna orientácia.',
        'p2' => 'Ak je senzor dostupný, karta senzora bude zelená. Ak senzor nie je dostupný, karta bude červená.',
        'p3' => 'Na povolenie magnetometra v prehliadači Google Chrome zadajte do adresného riadku <code>chrome://flags/</code> a povoľte <strong>#enable-generic-sensor-extra-classes</strong>.',
        'p4' => 'Pre najlepšie výsledky používajte Google Chrome. <strong>Safari nie je podporované.</strong>',
        'p5' => 'Pred meraním vyberte aktivitu a voliteľne zadajte identifikátor zariadenia (slúži na fikltrovanie meraní pokiaľ uživateľ nebude vedieť čas merania).',
        'p6' => 'Po stlačení tlačidla <strong>Spusti meranie</strong> sa spustí 10-sekundový odpočet. Počas tohto času vložte zariadenie do vrecka nohavíc a uistite sa, že je odblokované.',
        'p7' => 'Po uplynutí odpočtu začne meranie s frekvenciou 50 Hz.',
        'p8' => 'Meranie ukončíte stlačením tlačidla <strong>Zastav meranie</strong>.',
    ],

];