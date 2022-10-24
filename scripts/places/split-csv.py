FILES = 5
HEADER = 'name, desc, gmina, powiat, wojew\n'

out = [open(f'scripts/places/miejsca-{i}.csv', 'w') for i in range(FILES)]

for i in out:
    i.write(HEADER)

with open("scripts/places/miejsca-commas.csv") as f:
    n = 0
    while (i := f.readline()):
        out[n % FILES].write(i)
        n += 1
        

for i in out:
    i.close()