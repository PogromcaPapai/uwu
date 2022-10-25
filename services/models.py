import os
from laravel_export import *
from sqlalchemy import Column, Integer, ForeignKey, Boolean, Time
from sqlalchemy.orm import relationship, backref
from database import engine

class Preference(Base):
    __tablename__ = "preferences"

    id = Column(Integer, primary_key=True)
    min_temp = Column(Integer, nullable=True)
    max_temp = Column(Integer, nullable=True)
    min_pressure = Column(Integer, nullable=True)
    max_pressure = Column(Integer, nullable=True)
    sun = Column(Boolean)
    cloudy = Column(Boolean)
    light_rain = Column(Boolean)
    heavy_rain = Column(Boolean)
    snow = Column(Boolean)
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