from datetime import datetime, timedelta, date,  time
from urllib.request import urlopen
from json import load as json_load
from cachetools import TTLCache, cached
from libs.commons import CONFIG

TIME_FORMAT = f = "%Y-%m-%d %H:%M:%S.%f"

def get_alerts(voivodeship: str, type_: str = 'meteo'):
    """
    Get weather alerts for a voivodeship

    Possible values:
        - wszystkie
        - meteo (default)
        - hydrologiczne?
        - drogowe?
        - ogolne?
        - stany-wod
    """
    url = f'https://komunikaty.tvp.pl/komunikatyxml/{voivodeship}/{type_}/0?_format=json'
    contents = json_load(urlopen(url))
    if contents.get('newses', None) is None:
        return []
    else:
        return [
            {
                # "id": i['id'],
                "title": i['title'],
                "shortcut": i["shortcut"],
                "start": datetime.strptime(i['valid_from'],f),
                "end": datetime.strptime(i['valid_to'],f),
            }
            for i in contents['newses']
        ]
     
@cached(cache=TTLCache(maxsize=1024, ttl=60*60*3))   
def _download(lat:float, lon:float, mode:str):
    if mode == "forecast":
        url = f"https://api.openweathermap.org/data/2.5/forecast?lat={lat}&lon={lon}&appid={CONFIG['free_weather_key']}&lang=pl&units=metric"
    elif mode == "current":
        url = f"https://api.openweathermap.org/data/2.5/weather?lat={lat}&lon={lon}&appid={CONFIG['free_weather_key']}&lang=pl&units=metric"
    else: 
        raise NotImplementedError()
    
    contents = json_load(urlopen(url))
    return contents['list'] if mode == "forecast" else contents

def forecast(lat: float, lon: float, moment: date|datetime):
    if isinstance(moment, date):
        moment = datetime.combine(moment, time(hour=12))
        
    frcsts = _download(lat, lon, "forecast")
    if frcsts[0]['dt'] > moment.timestamp():
        return None
    return next(({
        "name": i['weather'][0]['main'],
        "code": i['weather'][0]['id'],
        "description": i['weather'][0]['description'],
        
        "wind_speed": i["wind"]["speed"],
        "wind_gust": i["wind"]["gust"],
        
        "temp_min": i["main"]["temp_min"],
        "temp_max": i["main"]["temp_max"],
        "temp": i["main"]["feels_like"],
        "pressure": i["main"]["pressure"]
    } for i in frcsts if i['dt'] > (moment - timedelta(hours=3)).timestamp()), None)
    
def current(lat: float, lon: float):
    i = _download(lat, lon, "current")
    return {
        "name": i['weather'][0]['main'],
        "code": i['weather'][0]['id'],
        "description": i['weather'][0]['description'],
        
        "wind_speed": i["wind"]["speed"],
        "wind_gust": i["wind"]["gust"],
        
        "temp_min": i["main"]["temp_min"],
        "temp_max": i["main"]["temp_max"],
        "temp": i["main"]["feels_like"],
        "pressure": i["main"]["pressure"]
    }