FILES = 5
HEADER = 'name, desc, gmina, powiat, wojew, dlug, szer\n'
cols = 0, 1, 2, 3, 4, 19, 20

out = [open(f'scripts/places/new/{i}.csv', 'r') for i in range(FILES)]
for i in out:
    i.readline()

with open("database/miejsca.csv", 'w') as f:
    f.write(HEADER)
    n = 0
    while (i := out[n % FILES].readline()):
        for nj, j in enumerate(i.split(',')):
            if nj in cols:
                f.write(j)
                if nj == 20:
                    f.write("\n")
                else:
                    f.write(", ")
        n += 1

for i in out:
    i.close()