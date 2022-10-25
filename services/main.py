from datetime import datetime, timedelta
from fastapi import FastAPI

from database import engine
from sqlalchemy import select
from sqlalchemy.orm import Session
from libs.weather import get_alerts, forecast
from libs.commons import CONFIG
from models import Preference, Place, Attendence
from uvicorn import run

from libs.commons import middle

app = FastAPI()

@app.get("/alerts/{voivodeship}")
async def _alerts(voivodeship: str, type_: str = "meteo"):
    return get_alerts(voivodeship, type_)

@app.get("/forecast/geo/{lat}-{lon}")
async def _forecast_geo(lat: float, lon: float, moment: datetime | None = None):
    moment = moment or datetime.now() + timedelta(hours=4)
    return forecast(lat, lon, moment)

def forecast_place(place_id: int, moment: datetime | None = None):
    moment = moment or datetime.now() + timedelta(hours=4)
    query = select(Place).where(Place.id==place_id)
    with Session(engine) as db:
        place = db.scalar(query)
    return forecast(place.lat, place.lon, moment)

def check(weather, preferences):
    preferences = preferences or CONFIG['default_pref']
    group=int(str(weather['code'])[0])
    # Temperature
    if preferences['min_temp'] is not None and weather['temp'] < preferences['min_temp']:
        yield 'temperature low'

    if preferences['max_temp'] is not None and weather['temp'] > preferences['max_temp']:
        yield 'temperature high'

    #Pressure
    if preferences['min_pressure'] is not None and weather['pressure'] < preferences['min_pressure']:
        yield 'pressure low'

    if preferences['max_pressure'] is not None and weather['pressure'] > preferences['max_pressure']:
        yield 'pressure high'

    if preferences['sun'] and weather['name'] == "Clear":
        yield 'sun'
    elif preferences['cloudy'] and weather['code'] > 800:
        yield 'cloudy'
    elif preferences['light_rain'] and (group == 3 or weather['code'] == 500):
        yield 'light rain'
    elif preferences['heavy_rain'] and group in {2, 5} and weather['code'] != 500:
        yield 'heavy rain'
    elif preferences['snow'] and group == 6:
        yield 'snow'
    elif group == 7:
        yield 'atmosphere'

@app.get("/forecast/attend/{attend_id}")
def forecast_attendence(attend_id:int):
    with Session(engine) as db:
        query = select(Attendence).where(Attendence.id==attend_id)
        attendence = db.scalar(query)
        preference = attendence.user_.preference_
        event = attendence.event_
        moment = middle(event.start, event.end)

        if (weather := forecast_place(event.place, moment)) is None: return []

    return {
        "forecast": weather, 
        "warns": list(check(weather, preference))
    }