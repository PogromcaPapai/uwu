from asyncio import events
from datetime import datetime, timedelta

from database import engine
from sqlalchemy import DDL, select, event
from sqlalchemy.orm import Session
from libs.weather import get_alerts, forecast, current
from libs.commons import CONFIG
from models import Preference, Place, Attendence, full_date, Event
from apscheduler import
from uvicorn import run
from unidecode import unidecode
from apscheduler.schedulers.background import BackgroundScheduler

from libs.commons import middle

scheduler = BackgroundScheduler()

def send_weather():
    now = datetime.now()
    date = (now + timedelta(days=1)).date()
    query = select(Event).where(Event.start==date)
    with Session(engine) as db:
        events = db.scalar(query)
        for i in events:
            query = select(Attendence).where(Event.id==Attendence.event and now.hour == Attendence.user_.preference_.send_summary)
            attendences = db.scalar(query)
            

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
