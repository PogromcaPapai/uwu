from datetime import datetime
from json import load as json_load

with open(__file__.removesuffix('libs/commons.py')+"config.json") as f:
    CONFIG = json_load(f)

def middle(a: datetime, b: datetime):
    delta = (max(a, b) - min(a, b))
    return min(a, b)+(delta/2)