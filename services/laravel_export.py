# coding: utf-8
from datetime import datetime, time
from sqlalchemy import Column, Date, Float, ForeignKey, Index, String, TIMESTAMP, Table, Text, text, Time
from sqlalchemy.dialects.mysql import BIGINT, INTEGER, LONGTEXT, TINYINT
from sqlalchemy.orm import relationship
from sqlalchemy.ext.declarative import declarative_base

Base = declarative_base()
metadata = Base.metadata

def full_date(event, start = True):
    if start:
        return datetime.combine(event.start, event.start_time)
    else:
        return datetime.combine(event.end, event.end_time)

class FailedJob(Base):
    __tablename__ = 'failed_jobs'

    id = Column(BIGINT(20), primary_key=True)
    uuid = Column(String(255, 'utf8mb4_unicode_ci'), nullable=False, unique=True)
    connection = Column(Text(collation='utf8mb4_unicode_ci'), nullable=False)
    queue = Column(Text(collation='utf8mb4_unicode_ci'), nullable=False)
    payload = Column(LONGTEXT, nullable=False)
    exception = Column(LONGTEXT, nullable=False)
    failed_at = Column(TIMESTAMP, nullable=False, server_default=text("current_timestamp()"))


class Migration(Base):
    __tablename__ = 'migrations'

    id = Column(INTEGER(10), primary_key=True)
    migration = Column(String(255, 'utf8mb4_unicode_ci'), nullable=False)
    batch = Column(INTEGER(11), nullable=False)


t_password_resets = Table(
    'password_resets', metadata,
    Column('email', String(255, 'utf8mb4_unicode_ci'), nullable=False, index=True),
    Column('token', String(255, 'utf8mb4_unicode_ci'), nullable=False),
    Column('created_at', TIMESTAMP, server_default=text("current_timestamp()"))
)


class PersonalAccessToken(Base):
    __tablename__ = 'personal_access_tokens'
    __table_args__ = (
        Index('personal_access_tokens_tokenable_type_tokenable_id_index', 'tokenable_type', 'tokenable_id'),
    )

    id = Column(BIGINT(20), primary_key=True)
    tokenable_type = Column(String(255, 'utf8mb4_unicode_ci'), nullable=False)
    tokenable_id = Column(BIGINT(20), nullable=False)
    name = Column(String(255, 'utf8mb4_unicode_ci'), nullable=False)
    token = Column(String(64, 'utf8mb4_unicode_ci'), nullable=False, unique=True)
    abilities = Column(Text(collation='utf8mb4_unicode_ci'))
    last_used_at = Column(TIMESTAMP)
    created_at = Column(TIMESTAMP, server_default=text("current_timestamp()"))
    updated_at = Column(TIMESTAMP)


class Place(Base):
    __tablename__ = 'places'

    id = Column(BIGINT(20), primary_key=True)
    name = Column(String(50, 'utf8mb4_unicode_ci'), nullable=False)
    desc = Column(String(58, 'utf8mb4_unicode_ci'), nullable=False)
    gmina = Column(String(33, 'utf8mb4_unicode_ci'), nullable=False)
    powiat = Column(String(34, 'utf8mb4_unicode_ci'), nullable=False)
    wojew = Column(String(29, 'utf8mb4_unicode_ci'), nullable=False)
    lat = Column(Float(8), nullable=False)
    lon = Column(Float(8), nullable=False)


class User(Base):
    __tablename__ = 'users'

    id = Column(BIGINT(20), primary_key=True)
    name = Column(String(255, 'utf8mb4_unicode_ci'), nullable=False)
    email = Column(String(255, 'utf8mb4_unicode_ci'), nullable=False, unique=True)
    email_verified_at = Column(TIMESTAMP)
    password = Column(String(255, 'utf8mb4_unicode_ci'), nullable=False)
    remember_token = Column(String(100, 'utf8mb4_unicode_ci'))
    created_at = Column(TIMESTAMP, server_default=text("current_timestamp()"))
    updated_at = Column(TIMESTAMP)
    is_mod = Column(TINYINT(1), default=0)


class Event(Base):
    __tablename__ = 'events'

    id = Column(BIGINT(20), primary_key=True)
    title = Column(String(255, 'utf8mb4_unicode_ci'), nullable=False)
    start = Column(Date, nullable=False)
    start_time = Column(Time, nullable=False, server_default="00:00:01")
    end = Column(Date, nullable=False)
    end_time = Column(Time, nullable=False, server_default="23:59:59")
    description = Column(Text(collation='utf8mb4_unicode_ci'), nullable=False)
    created_at = Column(TIMESTAMP, server_default=text("current_timestamp()"))
    updated_at = Column(TIMESTAMP)
    place = Column(ForeignKey('places.id', ondelete='CASCADE'), nullable=False, index=True)

    place_ = relationship('Place')


class Attendence(Base):
    __tablename__ = 'attendances'
    
    id = Column(BIGINT(20), primary_key=True)
    is_admin = Column(TINYINT(1), nullable=False)
    user = Column(ForeignKey('users.id', ondelete='CASCADE'), nullable=False, index=True)
    event = Column(ForeignKey('events.id', ondelete='CASCADE'), nullable=False, index=True)
    created_at = Column(TIMESTAMP, server_default=text("current_timestamp()"))
    updated_at = Column(TIMESTAMP)
    
    event_ = relationship('Event')
    user_ = relationship('User')
