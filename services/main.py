from datetime import datetime, timedelta
from fastapi import FastAPI

from database import engine
from sqlalchemy import select
from sqlalchemy.orm import Session
from libs.weather import get_alerts, forecast
from models import Preference, Place
from uvicorn import run

app = FastAPI()

@app.get("/alerts/{voivodeship}")
async def _alerts(voivodeship: str, type_: str = "meteo"):
    return get_alerts(voivodeship, type_)

@app.get("/forecast/geo/{lat}-{lon}")
async def _forecast_geo(lat: float, lon: float, moment: datetime | None = None):
    moment = moment or datetime.now()
    return forecast(lat, lon, moment)

@app.get("/forecast/place/{place_id}")
async def _forecast_place(place_id: int, moment: datetime | None = None):
    moment = moment or datetime.now()
    query = select(Place).where(Place.id==place_id)
    with Session(engine) as db:
        place = db.scalar(query).one()
    return forecast(place.lat, place.lon, moment)

def forecast_for_user(user:int, lat: float, lon: float, moment: datetime):
    query = select(Preference).where(Preference.user_id==user)
    with Session(engine) as db:
        pref = db.scalar(query).one()
    if (frc := forecast(lat, lon, moment)) is None: return None
    
    # alerts = {
        
    # 