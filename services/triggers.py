from asyncio import events
from datetime import datetime, timedelta

from database import engine  # type: ignore
from sqlalchemy import DDL, select, event
from sqlalchemy.orm import Session
from libs.mailing import send_mail
from libs.commons import CONFIG
from models import Preference, Place, Attendence, full_date, Event
from uvicorn import run
from unidecode import unidecode
from apscheduler.schedulers.background import BackgroundScheduler

from libs.commons import middle

scheduler = BackgroundScheduler()

def gen_comments(infos):
    forecast = infos['forecast']
    temp = forecast['temp']
    warns = infos['warns']
    if temp <= -20:
        yield "Nadchodzą duże mrozy, nie zapomnij o czapce i rękawiczkach."
    elif 'temperature low' in warns:
        yield "Dzisiaj będzie zimno."
    if 'temperature high' in warns:
        yield "Jutro ma być powyżej 30 załóż czapkę z daszkiem. Nie zapomnij o kremie z filtrem."

    if forecast['wind_speed'] < 7:
        yield "Słaby wiatr."
    elif forecast['wind_speed'] < 40:
        yield "Wiatr będzie umiarkowany. Małe cząstki mogą się unosić."
    elif forecast['wind_speed'] < 55:
        yield "Silny wiatr zrywający kapelusze i poruszający małymi rzeczami."
    elif forecast['wind_speed'] < 67:
        yield "Bardzo silny wiatr, unikaj wychodzenia z domu i schowaj rzeczy."
    elif forecast['wind_speed'] < 90:
        yield "Wichura! Nie opuszczaj domu."
    else:
        yield "Uwaga! Wiatr na zewnątrz jest bardzo niebezpieczny! Schowaj się w bezpiecznym miejscu, budynki mogą ulec zniszczeniu."


    if "heavy rain" in warns: 
        yield "Będzie dzisiaj ulewa. Nie zapomij parasola i unikaj przebywania za długo w deszczu."
    if "light rain" in warns: 
        yield "Będzie dzisiaj mrzawka. Nie zapomij parasola."
    if "sun" in warns: 
        yield "Dzisiaj będzie bezchmurny dzień, zadbaj o filtr UV."
    if "cloudy" in warns: 
        yield "Dzisiaj będzie pochmurnie."
    if "snow" in warns: 
        yield "Dzisiaj będzie padać deszcz, zadbaj o odpowiednie buty."
    if "atmosphere" in warns:
        description = forecast['description']
        yield f"Uwaga! Dzisiaj pojawi się {description}."

    if "pressure low" in warns: 
        yield "W dniu jutrzejszym przewiduje się niskie ciśnienie atmosferyczne - zadbaj o duża ilość kawy."
    elif "pressure high" in warns: 
        yield "Uwaga wysokie cisnienie przewidywane. Mozliwe spadki samopoczucia."

def send_weather(attendance_forecast_get):
    now = datetime.now()
    date = (now + timedelta(days=1)).date()
    query = select(Event).where(Event.start==date)
    with Session(engine) as db:
        events = db.scalar(query)
        for i in events:
            query = select(Attendence).where(Event.id==Attendence.event and now.hour == Attendence.user_.preference_.send_summary)
            attendences = db.scalar(query)
            for j in attendences:
                infos = attendance_forecast_get(j.id)
                forecast = infos['forecast']
                warns = infos['warns']
                send_mail(
                    [j.user_.email], 
                    'weather.jinja', 
                    "Prognoza pogody na wydarzenie",
                    event_name=i.name,
                    event_id=i.id,
                    info=[
                        {
                            'name':"Prognoza",
                            'description':f"""Prognozujemy {forecast['description']}. Temperatura będzie wynosić {forecast['temp']} stopni, ciśnienie {forecast['pressure']} hPA, a wiatr będzie wiał z prędkością {forecast['wind_speed']} km/h (w porywach do {forecast['wind_gust']})."""
                        }
                    ] + [{'name':'Ostrzeżenie', 'description':i} for i in gen_comments(infos)]
                )
            

scheduler.start()

# update_task_state = DDL('''\
# CREATE TRIGGER event_change_trigger      
# AFTER UPDATE
# ON events
# FOR EACH ROW
# AS
# BEGIN
#   DECLARE updated_row CHAR(255);
#   DECLARE cmd CHAR(255);
#   DECLARE result CHAR(255);
#   SET updated_row = NEW.id;
#   SET cmd = CONCAT('python /sciezka/do/skryptu.py', updated_row);
#   SET result = sys_eval(cmd);
# END;''')
# event.listen(Event.__table__, 'on_update', update_task_state)
