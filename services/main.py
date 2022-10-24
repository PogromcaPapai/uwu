from fastapi import FastAPI
from services.weather import forecast

from weather import get_alerts, forecast

app = FastAPI()

app.get("/alerts/{voivodeship}")(get_alerts)
app.get("/forecast/geo/{lat}-{lon}/{moment}")(forecast)
