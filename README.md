# Obsah nahratého pamäťového média

Obsah celého pamäťového média tejto práce je nahratý v cloudovom úložisku:  
[https://nextcloud.fit.vutbr.cz/s/Nf5tzby5CoYko25](https://nextcloud.fit.vutbr.cz/s/Nf5tzby5CoYko25)  
Tu je možné stiahnuť komprimovaný súbor, kde sa nachádzajú všetky zkompilovateľné súbory potrebné pre chod webovej aplikácie (zložka `website/`), implementovaného API rozhrania (zložka `api`), ako aj skompilovaný Python notebook obsahujúci kroky tvorby dátovej sady a vytváranie jednotlivých modelov (súbor `features_and_models.ipynb`).  
Zložka `doc/` obsahuje súbor práce a LaTeX zdrojový kód tejto práce.

V pamäťovom médiu sú obsiahnuté aj dáta namerané cez webovú aplikáciu v zložke `data/my_data/` a okrem toho aj vygenerované dáta v zložke `data/generated_data/`.  
Taktiež je tu databáza extrahovaných príznakov z nameraných dát (súbor `features_db.sql`) a exportované modely a škálovače (zložka `models_and_scalers`).  
Ako posledný je súbor `README.md`, ktorý obsahuje okrem popisu aj návod na inštaláciu potrebných knižníc.

Podrobný strom nahratého pamäťového média:

- .
- ├── api/ # Súbory API rozhrania
- ├── data/ # Súbory senzorových meraní
- │ ├── generated_data/ # Generované dáta
- │ └── my_data/ # Dáta namerané aplikáciou
- ├── doc/ # Súbory s dokumentmi k diplomovej práci
- │ ├── latex/ # LaTeX zdrojový kód tejto diplomovej práce
- │ └── thesis.pdf # Práca vo formáte PDF súboru
- ├── models_and_scalers/ # Exportované modely a škálovače
- ├── website/ # Webová stránka
- ├── features_and_models.ipynb # Extrakcia príznakov a tréning modelov
- ├── features_db.sql # Databáza príznakov
- ├── README.md # README súbor s popisom obsahu jednotlivých súborov
- └── requirements.txt # Súbor potrebných Python knižníc


---

# Inštalačný manuál

Pre inštaláciu všetkých potrebných balíkov pre súbor `features_and_models.ipynb` a rozbehnutie API rozhrania stačí nainštalovať balíky obsiahnuté v súbore `requirements.txt` nasledovne:

```bash
pip install -r requirements.txt
```

Pri implementácia a testovanie týchto súbor bol vyvíjaný v interpreteri  `Python 3.11` Pri používaní notebookú je odporúčané využiť nejaké interaktívne vývojové prostredie (v práci využívaný jupyter (Skupina sotvérových produktov pre  jazyk Python (https://jupyter.org/)). Zapnutie API rozhrania je automatizované kedy na serveri kde beží webová aplikácia stačí zapnúť skript `api/manage_api.sh start` ktorý následne spustí implementované API rozhranie.

Pre rozbehnutie webovej aplikácie je potrebné mať na serveri nainštalovanú verziu jazyka `php` 8.2 a vyššiu (v práci využívaná verzia `8.4.1`), balíček `Composer` verzie `2.8` a vyššej (v práci využívaná verzia `2.8.4`) a rámcový balíček `Node.js` verzie 18 a vyššej (využívaná verzia `18.19.1`). Pre inštaláciu potrebných balíkov cez `Composer` je potrebné v zložke kde sa nachádza webová stránka spustiť príkaz:

```bash
composer install
```

Následne je potrebné nainštalovať všetky potrebné `javascript` balíčky a ich následnú kompiláciu sériou príkazov:

```bash
npm install
npm run build
```

Ak všetko zbehlo správne tak aplikáciu je možné pustiť príkazom:

```bash
php artisan serve
```

Pre správne fungovanie tejto webovej aplikácie je potrebné mať spustené aplikačné rozhranie cez spomínaný skript na tom istom serveri (resp. zariadení), na ktorom je spustená webová aplikácia. Pre správne fungovanie Generic Sensor API je taktiež potrebné mať správne nastavenie SSH certifikátu v konfigurácií webového servera.
