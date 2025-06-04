from fastapi import FastAPI

from models import PredictionData, ClearData
import features as ft

app = FastAPI()


@app.post("/lstm-predict")
def lstm_predict(data: PredictionData):
    df_features = ft.get_features(data.model_dump()['sensorData'], data.uid)

    predicted_class_name, predicted_index = ft.predict_lstm(df_features)
    print(predicted_class_name, predicted_index)

    return {
        "message": "successfully predicted",
        # "data": features,
        "predicted_class_index": int(predicted_index),
        "predicted_class": predicted_class_name,
        # "global_df": json.loads(df_features.to_json(orient='records')),
        # "normalized": json.loads(normalized_df.to_json(orient='records'))
    }


@app.post("/gru-predict")
def lstm_predict(data: PredictionData):
    df_features = ft.get_features(data.model_dump()['sensorData'], data.uid)

    predicted_class_name, predicted_index = ft.predict_gru(df_features)

    return {
        "message": "successfully predicted",
        "predicted_class_index": int(predicted_index),
        "predicted_class": predicted_class_name,
    }


@app.post("/clear-data")
def clear_data(data: ClearData):
    ft.delete_uid_features(data.uid)
    return {"message": f"Data pre uid {data.uid} boli vymazané."}



@app.post("/third-predict")
def third_predict(data: PredictionData):
    df_raw = ft.store_raw_for_user_third(data.model_dump()['sensorData'], data.uid)
    df_features = ft.get_features(data.model_dump()['sensorData'], data.uid)

    predicted_raw = ft.predict_third(df_raw)
    predicted_lstm, predicted_gru = ft.predict_lstm_gru(df_features)

    final_class, final_score = ft.weighted_ensemble_top3(predicted_raw, predicted_lstm, predicted_gru, w_raw=0.2, w_lstm=0.4, w_gru=0.4)

    return {
        "message": "successfully predicted",
        "predicted_class": final_class,
        "score": final_score,
        "lstm": predicted_lstm,
        "gru": predicted_gru,
        "raw": predicted_raw,
    }



if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="127.0.0.1", port=5050, reload=True)
