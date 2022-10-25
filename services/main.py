from datetime import datetime, timedelta
from fastapi import FastAPI

from database import engine
from sqlalchemy import select
from sqlalchemy.orm import Session
from libs.weather import get_alerts, forecast
from models import Preference
from uvicorn import run

app = FastAPI()

@app.get("/services/alerts/{voivodeship}")
async def _alerts(voivodeship: str, type_: str = "meteo"):
    return get_alerts(voivodeship, type_)

@app.get("/services/forecast/{lat}-{lon}/{moment}")
async def _forecast(lat: float, lon: float, moment: datetime):
    return forecast(lat, lon, moment)

def forecast_for_user(user:int, lat: float, lon: float, moment: datetime):
    query = select(Preference).where(user_id=user)
    with Session(engine) as db:
        pref = db.scalar(query).one()
    if (frc := forecast(lat, lon, moment)) is None: return None
    
    # alerts = {
        
    # }