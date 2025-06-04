import pandas as pd
import numpy as np
from collections import defaultdict
from scipy.signal import correlate
from scipy.fftpack import dct
from tensorflow.keras.preprocessing.sequence import pad_sequences
from tensorflow.keras.models import load_model
import pickle

user_features = {}

lstm_model = load_model("my_lstm_model.h5")
gru_model = load_model("my_gru_model.h5")
third_model = load_model("raw_data_model.h5")

with open("scaler.pkl", "rb") as f:
    scaler = pickle.load(f)


def calculate_periodicity(signal):
    """Vypočíta periodicitu a dĺžku periódy pomocou FFT."""
    if len(signal) < 2:
        return 0
    fft_values = np.fft.fft(signal)
    frekvencie = np.fft.fftfreq(len(signal))
    dominantna_frekvencia = frekvencie[np.argmax(np.abs(fft_values[1:])) + 1]  # Ignorujeme DC komponent
    dlzka_periody = 1 / dominantna_frekvencia if dominantna_frekvencia != 0 else 0
    return dlzka_periody

def calculate_autocorrelation(signal):
    """Vypočíta maximálnu autokoreláciu."""
    if len(signal) < 2:
        return 0
    autokorelacia = correlate(signal, signal, mode='full')[len(signal)-1:]
    maximalna_autokorelacia = np.max(autokorelacia[1:])  # Ignorujeme nultý posun
    return maximalna_autokorelacia

def calculate_dct_coefficients(signal, num_coefficients=10):
    """Vypočíta prvých `num_coefficients` DCT koeficientov."""
    if len(signal) < 2:
        return [0] * num_coefficients  # Ak je signál príliš krátky, vráť NaN
    signal_values = signal.values  # Konvertuj Pandas Series na NumPy pole
    dct_coefficients = dct(signal_values, norm='ortho')
    if len(dct_coefficients) < num_coefficients:
        dct_coefficients = np.append(dct_coefficients, [np.nan] * (num_coefficients - len(dct_coefficients)))
    return dct_coefficients[:num_coefficients]

def compute_features_for_sensor(df, prefix):
    """
    Pre daný DataFrame a prefix (napr. 'acc' pre akcelerometer) vypočíta agregované štatistiky
    pre os x, y a z.
    """
    features = {}
    for axis in ['x', 'y', 'z']:
        col = f"{axis}_{prefix}"
        series = df[col].dropna()
        features[f"{col}_mean"] = series.mean()
        features[f"{col}_std"] = series.std()
        features[f"{col}_max"] = series.max()
        features[f"{col}_min"] = series.min()
        features[f"{col}_sma"] = series.abs().sum()
        features[f"{col}_energy"] = (series**2).mean()
        features[f"{col}_iqr"] = series.quantile(0.75) - series.quantile(0.25)
        features[f"{col}_skew"] = series.skew()
        features[f"{col}_kurtosis"] = series.kurtosis()
        features[f"{col}_periodicity"] = calculate_periodicity(series)
        features[f"{col}_autocorr"] = calculate_autocorrelation(series)
        dct_coeffs = calculate_dct_coefficients(series, 5)
        for i, coeff in enumerate(dct_coeffs):
            features[f"{col}_dct_{i}"] = coeff
    return features


def predict_lstm(df):
    normalized_df = df.copy()

    with open("scaler.pkl", "rb") as f:
        scaler = pickle.load(f)
    # columns_to_ignore = ['index', 'ms_index']
    ordered_columns = list(scaler.feature_names_in_)
    print(normalized_df.columns)

    columns_to_normalize = [col for col in ordered_columns if col in normalized_df.columns]
    normalized_df[columns_to_normalize] = scaler.transform(normalized_df[columns_to_normalize])

    # potrebujem to dať do presného poradia v akom to bolo trenovane
    new_order = ["index", "ms_index"] + ordered_columns
    new_order = [col for col in new_order if col in normalized_df.columns]

    normalized_df = normalized_df[new_order]

    # print(normalized_df)

    x_test = prepare_sequences(normalized_df)
    x_test_padded = pad_sequences(x_test, padding='post', dtype='float32')
    print(x_test)

    # Predikcia pomocou načítaného LSTM modelu
    y_pred = lstm_model.predict(np.array(x_test_padded))
    # target_names = ['ontable', 'lie', 'sit', 'stand', 'walk', 'car', 'bus', 'tram', 'train', 'metro',
    #                 'other', 'stairsUp', 'stairsDown', 'run']
    target_names = ['ontable', 'lie', 'sit', 'stand', 'walk', 'car', 'run', 'jumping', 'spinning']
    predicted_index = np.argmax(y_pred, axis=1)[0]

    # Pridáme do odpovede aj názov triedy, ak máte mapovanie
    predicted_class_name = target_names[predicted_index]
    print("Predikovaná trieda:", predicted_class_name)
    print("Predikovaná trieda index:", predicted_index)

    return predicted_class_name, predicted_index


def predict_gru(df):
    normalized_df = df.copy()

    with open("scaler.pkl", "rb") as f:
        scaler = pickle.load(f)
    ordered_columns = list(scaler.feature_names_in_)

    columns_to_normalize = [col for col in ordered_columns if col in normalized_df.columns]
    normalized_df[columns_to_normalize] = scaler.transform(normalized_df[columns_to_normalize])

    # potrebujem to dať do presného poradia v akom to bolo trenovane
    new_order = ["index", "ms_index"] + ordered_columns
    new_order = [col for col in new_order if col in normalized_df.columns]

    normalized_df = normalized_df[new_order]

    # print(normalized_df)

    x_test = prepare_sequences(normalized_df)
    x_test_padded = pad_sequences(x_test, padding='post', dtype='float32')
    print(x_test)

    # Predikcia pomocou načítaného GRU modelu
    y_pred = gru_model.predict(np.array(x_test_padded))
    # target_names = ['ontable', 'lie', 'sit', 'stand', 'walk', 'car', 'bus', 'tram', 'train', 'metro',
    #                 'other', 'stairsUp', 'stairsDown', 'run']
    target_names = ['ontable', 'lie', 'sit', 'stand', 'walk', 'car', 'run', 'jumping', 'spinning']
    predicted_index = np.argmax(y_pred, axis=1)[0]

    # Pridáme do odpovede aj názov triedy, ak máte mapovanie
    predicted_class_name = target_names[predicted_index]
    print("Predikovaná trieda:", predicted_class_name)
    print("Predikovaná trieda index:", predicted_index)

    return predicted_class_name, predicted_index

def predict_lstm_gru(df):
    normalized_df = df.copy()

    ordered_columns = list(scaler.feature_names_in_)

    columns_to_normalize = [col for col in ordered_columns if col in normalized_df.columns]
    normalized_df[columns_to_normalize] = scaler.transform(normalized_df[columns_to_normalize])

    # potrebujem to dať do presného poradia v akom to bolo trenovane
    new_order = ["index", "ms_index"] + ordered_columns
    new_order = [col for col in new_order if col in normalized_df.columns]

    normalized_df = normalized_df[new_order]

    x_test = prepare_sequences(normalized_df)
    x_test_padded = np.array(pad_sequences(x_test, padding='post', dtype='float32'))

    # Predikcia pomocou načítaného GRU modelu
    y_pred_gru = gru_model.predict(x_test_padded)
    y_pred_lstm = lstm_model.predict(x_test_padded)

    target_names = ['ontable', 'lie', 'sit', 'stand', 'walk', 'car', 'run', 'jumping', 'spinning']

    # ---- Top-3 pre GRU ----
    scores_gru = y_pred_gru[0]
    top3_idx_gru = np.argsort(scores_gru)[-3:][::-1]  # 3 najväčšie indexy v zostupnom poradí
    top3_gru = []
    for idx in top3_idx_gru:
        top3_gru.append({
            "class": target_names[idx],
            "probability_percent": float(scores_gru[idx] * 100.0)
        })

    # ---- Top-3 pre LSTM ----
    scores_lstm = y_pred_lstm[0]
    top3_idx_lstm = np.argsort(scores_lstm)[-3:][::-1]
    top3_lstm = []
    for idx in top3_idx_lstm:
        top3_lstm.append({
            "class": target_names[idx],
            "probability_percent": float(scores_lstm[idx] * 100.0)
        })

    return top3_lstm, top3_gru


def get_features(sensor_dict, uid):
    sensor_df = pd.DataFrame(columns=['AcceExtractedData', 'GyroExtractedData', 'MagnetExtractedData', 'AbsoluteOrientationExtractedData', 'RelativeOrientatioExtractedData'])

    sensor_df.loc[0, 'AcceExtractedData'] = sensor_dict['accelerometer']
    sensor_df.loc[0, 'GyroExtractedData'] = sensor_dict['gyroscope']
    sensor_df.loc[0, 'MagnetExtractedData'] = sensor_dict['magnetometer']
    sensor_df.loc[0, 'AbsoluteOrientationExtractedData'] = sensor_dict['absOrientation']
    sensor_df.loc[0, 'RelativeOrientatioExtractedData'] = sensor_dict['relOrientation']

    acce_flat_data = pd.json_normalize(sensor_df.iloc[0]['AcceExtractedData'])
    gyro_flat_data = pd.json_normalize(sensor_df.iloc[0]['GyroExtractedData'])
    magnet_flat_data = pd.json_normalize(sensor_df.iloc[0]['MagnetExtractedData'])
    absorientation_flat_data = pd.json_normalize(sensor_df.iloc[0]['AbsoluteOrientationExtractedData'])
    relorientation_flat_data = pd.json_normalize(sensor_df.iloc[0]['RelativeOrientatioExtractedData'])

    # Odstránenie nepotrebných stĺpcov
    acce_flat_data = acce_flat_data[['t', 'x', 'y', 'z']]
    gyro_flat_data = gyro_flat_data[['t', 'x', 'y', 'z']]
    magnet_flat_data = magnet_flat_data[['t', 'x', 'y', 'z']]
    absorientation_flat_data = absorientation_flat_data[['t', 'x', 'y', 'z']]
    relorientation_flat_data = relorientation_flat_data[['t', 'x', 'y', 'z']]

    # Pre správnu funkciu compute_features_for_sensor premenovávame stĺpce
    acce_flat_data = acce_flat_data.rename(columns={'x': 'x_acc', 'y': 'y_acc', 'z': 'z_acc'})
    gyro_flat_data = gyro_flat_data.rename(columns={'x': 'x_gyro', 'y': 'y_gyro', 'z': 'z_gyro'})
    magnet_flat_data = magnet_flat_data.rename(columns={'x': 'x_mag', 'y': 'y_mag', 'z': 'z_mag'})
    absorientation_flat_data = absorientation_flat_data.rename(columns={'x': 'x_abs', 'y': 'y_abs', 'z': 'z_abs'})
    relorientation_flat_data = relorientation_flat_data.rename(columns={'x': 'x_rel', 'y': 'y_rel', 'z': 'z_rel'})

    features = {}
    features.update(compute_features_for_sensor(acce_flat_data, 'acc'))
    features.update(compute_features_for_sensor(gyro_flat_data, 'gyro'))
    features.update(compute_features_for_sensor(magnet_flat_data, 'mag'))
    features.update(compute_features_for_sensor(absorientation_flat_data, 'abs'))
    features.update(compute_features_for_sensor(relorientation_flat_data, 'rel'))

    return push_features_for_user(features, uid)


def push_features_for_user(new_features, uid):
    """
    Aktualizuje per-user DataFrame (uložený v slovníku user_features)
    tak, aby vždy obsahoval maximálne 5 riadkov pre daného používateľa.
    """
    max_records = 5

    # Ak pre daného používateľa ešte DataFrame neexistuje, vytvoríme ho
    if uid not in user_features:
        new_features["index"] = 1
        new_features["ms_index"] = 1000
        user_features[uid] = pd.DataFrame([new_features])
    else:
        df = user_features[uid]
        new_features["index"] = 1
        if len(df) < max_records:
            new_index = (len(df) + 1) * 1000
            new_features["ms_index"] = new_index
            df = pd.concat([df, pd.DataFrame([new_features])], ignore_index=True)
        else:
            df = df.iloc[1:].copy()
            df["ms_index"] = df["ms_index"] - 1000
            new_features["ms_index"] = max_records * 1000
            df = pd.concat([df, pd.DataFrame([new_features])], ignore_index=True)
        user_features[uid] = df
    return user_features[uid]


def prepare_sequences(df):
    sequences = []

    # Skupina dát podľa 'index'
    grouped = df.groupby('index')
    for _, group in grouped:
        # Zoraď podľa 'ms_index' pre časovú postupnosť
        group = group.sort_values(by='ms_index')

        # Vstup (X): všetky stĺpce okrem 'activity', 'index', 'ms_index'
        X = group.drop(columns=['index', 'ms_index']).values
        sequences.append(X)

    return np.array(sequences, dtype=object)


def weighted_ensemble_top3(raw_top3, lstm_top3, gru_top3, w_raw=0.2, w_lstm=0.4, w_gru=0.4):
    """
    Zoberie top-3 predikciu z 3 rôznych modelov (raw, LSTM, GRU),
    pričom parametre `raw_top3`, `lstm_top3`, `gru_top3` vyzerajú ako:
      [
        {"class": "...", "probability_percent": 99.99},
        ...
      ]
    Spolupriemeruje ich s váhami (w_raw, w_lstm, w_gru).

    Vráti:
      - final_class: názov triedy s najväčšou váženou pravdepodobnosťou
      - final_score_percent: jej pravdepodobnosť (v %)
      - top3_list: usporiadaný zoznam (trieda + %), zhora nadol
    """
    combined_scores = defaultdict(float)

    # 1) Načítaj tri top-3 a vážené príspevky
    for item in raw_top3:
        cls = item["class"]
        p = item["probability_percent"] / 100.0
        combined_scores[cls] += w_raw * p

    for item in lstm_top3:
        cls = item["class"]
        p = item["probability_percent"] / 100.0
        combined_scores[cls] += w_lstm * p

    for item in gru_top3:
        cls = item["class"]
        p = item["probability_percent"] / 100.0
        combined_scores[cls] += w_gru * p

    # Získaj triedu s najvyšším skóre
    final_class = max(combined_scores, key=combined_scores.get)
    final_score_percent = combined_scores[final_class] * 100

    return final_class, float(final_score_percent)




user_features_third = {}


def apply_min_max_third(row_dict, stats_file: str):
    """
    Zoberie obyčajný slovník row_dict, kde kľúče sú "acce_raw", "gyro_raw", ...
    (každý: np.array shape (N,3)),
    a aplikuje min-max normalizáciu podľa min_max_scaler.pkl.
    Vráti ten istý row_dict s preškálovanými poliami.
    """
    with open(stats_file, "rb") as f:
        min_max_stats = pickle.load(f)

    sensor_map = {
        "acce_raw": "acce_resampled",
        "gyro_raw": "gyro_resampled",
        "mag_raw":  "magnet_resampled",
        "abs_raw":  "absorient_resampled",
        "rel_raw":  "relorient_resampled"
    }

    for raw_col, stats_key in sensor_map.items():
        if raw_col not in row_dict:
            continue
        arr = row_dict[raw_col]
        if not isinstance(arr, np.ndarray) or arr.ndim != 2 or arr.shape[1] != 3:
            continue

        if stats_key not in min_max_stats:
            continue

        # Normalizácia
        stats = min_max_stats[stats_key]
        x_min, x_max = stats['x']['min'], stats['x']['max']
        y_min, y_max = stats['y']['min'], stats['y']['max']
        z_min, z_max = stats['z']['min'], stats['z']['max']

        dx = (x_max - x_min) if (x_max != x_min) else 1
        dy = (y_max - y_min) if (y_max != y_min) else 1
        dz = (z_max - z_min) if (z_max != z_min) else 1

        arr[:, 0] = (arr[:, 0] - x_min) / dx
        arr[:, 1] = (arr[:, 1] - y_min) / dy
        arr[:, 2] = (arr[:, 2] - z_min) / dz

        row_dict[raw_col] = arr

    return row_dict



def store_raw_for_user_third(sensor_dict, uid):
    """
    Uloží raw dáta (accelerometer, gyroscope, ...) do user_features_third[uid].
    Každý hovor endpointu pridá JEDEN riadok s piatimi stĺpcami:
       acce_raw, gyro_raw, magnet_raw, abs_raw, rel_raw
    Každý stĺpec obsahuje np.array tvaru (N,3).

    Udržujeme max 5 riadkov na používateľa (posledných 5 volaní).
    """

    # 1) Vytiahni raw dáta z prichádzajúceho sensor_dict
    #    Každý sensor_dict[senzor] je list dict-ov: [{"t":..., "x":..., "y":..., "z":...}, ...]
    #    Urobíme z toho Numpy array (N,3)
    def build_array(measurements):
        # measurements je list s t,y,z
        arr = []
        for m in measurements:
            arr.append([m['x'], m['y'], m['z']])
        return np.array(arr, dtype=float)  # shape (N, 3)

    acce_arr = build_array(sensor_dict["accelerometer"])    # (N,3)
    gyro_arr = build_array(sensor_dict["gyroscope"])        # (N,3)
    mag_arr = build_array(sensor_dict["magnetometer"])      # (N,3)
    abs_arr = build_array(sensor_dict["absOrientation"])    # (N,3)
    rel_arr = build_array(sensor_dict["relOrientation"])    # (N,3)

    # 2) Postav nový riadok (dict)
    #    Napr. stĺpce: acce_raw, gyro_raw, magnet_raw, abs_raw, rel_raw, index, ms_index
    new_row = {
        "acce_raw": acce_arr,
        "gyro_raw": gyro_arr,
        "mag_raw": mag_arr,
        "abs_raw": abs_arr,
        "rel_raw": rel_arr,
    }

    # Aplikuj min-max normalizáciu (podľa precomputed stats)
    new_row = apply_min_max_third(new_row, "min_max_scaler.pkl")

    # 3) Ak pre daného používateľa ešte DF neexistuje, vytvoríme ho
    if uid not in user_features_third:
        # Prvý záznam
        df = pd.DataFrame(columns=["acce_raw", "gyro_raw", "mag_raw",
                                   "abs_raw", "rel_raw", "index", "ms_index"])
        new_row["index"] = 1
        new_row["ms_index"] = 1000
        df = pd.concat([df, pd.DataFrame([new_row])], ignore_index=True)
    else:
        df = user_features_third[uid]

        # Všetky existujúce záznamy posúvame nižšie (ms_index += 1000)
        df["ms_index"] = df["ms_index"] + 1000

        # Nastavíme nový rad na vrch
        max_records = 5
        new_row["index"] = 1
        new_row["ms_index"] = 1000

        # Nový rad pridáme na začiatok => pd.concat([ nový_rad, df ])
        new_df = pd.concat([pd.DataFrame([new_row]), df], ignore_index=True)

        # Ak je teraz dĺžka > max_records, odstránime posledný (index=5 a vyššie)
        if len(new_df) > max_records:
            new_df = new_df.iloc[:max_records].copy()

        df = new_df  # nech to voláme df konzistentne

    user_features_third[uid] = df

    return df


def prepare_raw_sequences_third(df):
    """
    Spojí VŠETKY RIADKY do jednej dlhej sekvencie.
    T.j. pre sensor x (acce_raw) pospája za sebou (N,3) z každého riadku,
    v poradí ms_index stúpajúco.

    Nakoniec spojí (acce, gyro, mag, abs, rel) pozdĺž osi=1,
    čím dostane (T, 15). Vráti list s 1 prvkom: [ (T,15) ].
    """
    df = df.sort_values(by="ms_index")

    # "akumulátory" pre každý senzor
    acce_parts = []
    gyro_parts = []
    mag_parts  = []
    abs_parts  = []
    rel_parts  = []

    for row_idx in range(len(df)):
        acce_arr = df.iloc[row_idx]["acce_raw"]  # (N1, 3)
        gyro_arr = df.iloc[row_idx]["gyro_raw"]  # (N2, 3)
        mag_arr  = df.iloc[row_idx]["mag_raw"]   # (N3, 3)
        abs_arr  = df.iloc[row_idx]["abs_raw"]   # (N4, 3)
        rel_arr  = df.iloc[row_idx]["rel_raw"]   # (N5, 3)

        # zistíme minimálnu dĺžku, aby sedia senzory
        min_len = min(acce_arr.shape[0], gyro_arr.shape[0], mag_arr.shape[0],
                      abs_arr.shape[0],  rel_arr.shape[0])

        # skrátime
        acce_arr = acce_arr[:min_len]
        gyro_arr = gyro_arr[:min_len]
        mag_arr  = mag_arr[:min_len]
        abs_arr  = abs_arr[:min_len]
        rel_arr  = rel_arr[:min_len]

        # odložíme do zoznamov
        acce_parts.append(acce_arr)
        gyro_parts.append(gyro_arr)
        mag_parts.append(mag_arr)
        abs_parts.append(abs_arr)
        rel_parts.append(rel_arr)

    # Ak je DF prázdny, vrátime prázdnu sekvenciu
    if len(acce_parts) == 0:
        return [np.zeros((0, 15), dtype=np.float32)]

    # zlepíme pre každý senzor (zvisle, axis=0)
    acce_merged = np.concatenate(acce_parts, axis=0)  # (SumaN, 3)
    gyro_merged = np.concatenate(gyro_parts, axis=0)
    mag_merged  = np.concatenate(mag_parts, axis=0)
    abs_merged  = np.concatenate(abs_parts, axis=0)
    rel_merged  = np.concatenate(rel_parts, axis=0)

    # spojíme senzory do shape (T, 15)
    merged = np.concatenate([acce_merged, gyro_merged, mag_merged, abs_merged, rel_merged],
                            axis=1)

    # Vrátime list s 1 sekvenciou -> pad_sequences -> (1, T, 15)
    return [merged]




def predict_third(df):
    # 1) Vytvor sekvencie
    X_list = prepare_raw_sequences_third(df)

    # 2) pad_sequences -> (batch_size, max_T, 15)
    X_padded = pad_sequences(X_list, padding='post', dtype='float32')

    # 3) Predikcia
    y_pred = third_model.predict(X_padded)

    target_names = ['ontable', 'lie', 'sit', 'stand', 'walk', 'car', 'run', 'jumping', 'spinning']

    scores = y_pred[-1]

    # Index 3 najväčších
    top3_indices = np.argsort(scores)[-3:][::-1]  # zoradené zostupne

    top3_predictions_raw = []

    for idx in top3_indices:
        class_name = target_names[idx]
        prob_percent = scores[idx] * 100.0
        top3_predictions_raw.append({
            "class": class_name,
            "probability_percent": float(prob_percent)
        })

    return top3_predictions_raw











def delete_uid_features(uid):
    if uid in user_features:
        del user_features[uid]
        print("vymazane")