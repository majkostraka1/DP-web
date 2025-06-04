from pydantic import BaseModel
from typing import Dict, List

class SensorMeasurement(BaseModel):
    t: int
    x: float
    y: float
    z: float

class PredictionData(BaseModel):
    uid: str
    sensorData: Dict[str, List[SensorMeasurement]]
    timestamp: int

class ClearData(BaseModel):
    uid: str