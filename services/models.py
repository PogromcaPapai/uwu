import os
from laravel_export import *
from sqlalchemy import Column, Integer, ForeignKey, Boolean, Time
from sqlalchemy.orm import relationship, backref
from database import engine
from libs.commons import CONFIG

class Preference(Base):
    __tablename__ = "preferences"

    id = Column(Integer, primary_key=True)
    min_temp = Column(Integer, nullable=True, default=CONFIG['default_pref']["min_temp"])
    max_temp = Column(Integer, nullable=True, default=CONFIG['default_pref']["max_temp"])
    min_pressure = Column(Integer, nullable=True, default=CONFIG['default_pref']["min_pressure"])
    max_pressure = Column(Integer, nullable=True, default=CONFIG['default_pref']["max_pressure"])
    sun = Column(Boolean, default=CONFIG['default_pref']["sun"])
    cloudy = Column(Boolean, default=CONFIG['default_pref']["cloudy"])
    light_rain = Column(Boolean, default=CONFIG['default_pref']["light_rain"])
    heavy_rain = Column(Boolean, default=CONFIG['default_pref']["heavy_rain"])
    snow = Column(Boolean, default=CONFIG['default_pref']["snow"])
    send_summary = Column(Time, nullable=True)

    default_place = Column(ForeignKey('places.id'))
    user = Column(ForeignKey('users.id'))

    user_ = relationship("User", backref=backref("preference_", uselist=False))
    default_place_ = relationship("Place")

if __name__=="__main__": 
    metadata.drop_all(engine)
    metadata.create_all(engine)
    try:
        os.system("cd .. && php artisan db:seed && cd services")
    except Exception:
        print("I wasn't able to seed the DB")