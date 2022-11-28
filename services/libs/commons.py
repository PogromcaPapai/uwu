from datetime import datetime
from json import load as json_load
from pathlib import Path

with open(Path(__file__).parent.with_name('config.json')) as f:
    CONFIG = json_load(f)

MAIL_DIR = Path(__file__).parent.parent.joinpath("mails")
GOOGLE_SECRET_PATH = Path(__file__).parent.parent.joinpath('google_secret.json').resolve()

def middle(a: datetime, b: datetime):
    delta = (max(a, b) - min(a, b))
    return min(a, b)+(delta/2)