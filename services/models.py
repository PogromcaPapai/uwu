from curses import meta
from dataclasses import dataclass
from laravel_export import *
from sqlalchemy import Column, Integer, ForeignKey, Boolean, Time
from sqlalchemy.orm import relationship
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
    
    default_place_id = Column(ForeignKey('places.id'))
    user_id = Column(ForeignKey('users.id'))
    
    user = relationship("User")
    default_place = relationship("Place")
   
if __name__=="__main__": 
    metadata.drop_all(engine)
    metadata.create_all(engine)