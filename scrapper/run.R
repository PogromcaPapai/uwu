# "C:\Program Files\R\R-4.1.1\bin\Rscript.exe" scrapper\run.R

library(plumber)
pr_run(pr("scrapper/scrapperAPI.R"), port=3447)