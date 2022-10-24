from datetime import datetime
from fastapi import FastAPI


from .libs.weather import get_alerts, forecast

app = FastAPI()

app.get("/alerts/{voivodeship}")
async def _alerts(voivodeship: str, type_: str = "meteo"):
    return get_alerts(voivodeship, type_)

app.get("/forecast/{lat}-{lon}/{moment}")
async def _forecast(lat: float, lon: float, moment: datetime):
    return forecast(lat, lon, moment)