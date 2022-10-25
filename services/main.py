from datetime import datetime, timedelta
from fastapi import FastAPI

from database import engine
from sqlalchemy import select
from sqlalchemy.orm import Session
from libs.weather import get_alerts, forecast
from models import Preference, Place, Attendence
from uvicorn import run

from services.libs.commons import middle

app = FastAPI()

@app.get("/alerts/{voivodeship}")
async def _alerts(voivodeship: str, type_: str = "meteo"):
    return get_alerts(voivodeship, type_)

@app.get("/forecast/geo/{lat}-{lon}")
async def _forecast_geo(lat: float, lon: float, moment: datetime | None = None):
    moment = moment or datetime.now() + timedelta(hours=4)
    return forecast(lat, lon, moment)

@app.get("/forecast/place/{place_id}")
async def _forecast_place(place_id: int, moment: datetime | None = None):
    moment = moment or datetime.now() + timedelta(hours=4)
    query = select(Place).where(Place.id==place_id)
    with Session(engine) as db:
        place = db.scalar(query)
    return forecast(place.lat, place.lon, moment)

def check(forecast, preferences):
    group=int(str(forecast['code'])[0])
    # Temperature
    if forecast['temp'] < preferences['temp_min']:
        yield 'temperature low'

    if forecast['temp'] > preferences['temp_max']:
        yield 'temperature high'

    #Pressure
    if forecast['pressure'] < preferences['pressure_min']:
        yield 'pressure low'

    if forecast['pressure'] > preferences['pressure_max']:
        yield 'pressure high'

    if preferences['sun'] and forecast['name'] == "Clear":
        yield 'sun'
    elif preferences['cloudy'] and forecast['code'] > 800:
        yield 'cloudy'
    elif preferences['light_rain'] and (group == 3 or forecast['code'] == 500):
        yield 'light rain'
    elif preferences['heavy_rain'] and group in {2, 5} and forecast['code'] != 500:
        yield 'heavy rain'
    elif preferences['snow'] and group == 6:
        yield 'snow'
    elif group == 7:
        yield 'atmosphere'

@app.get("/forecast/attend/{attend_id}")
def forecast_attendence(attend_id:int):
    query = select(Attendence).where(Attendence.id==attend_id)
    with Session(engine) as db:
        attendence = db.scalar(query)
    preference = attendence.user_.preference_
    moment = middle(attendence.start, attendence.end)

    if (weather := _forecast_place(attendence.place, moment)) is None: return None

    return {
        "forecast": weather, 
        "warns": list(check(weather, preference))
    }