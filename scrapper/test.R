library(xml2)
library(dplyr)
library(pdftools)
library(stringr)
library(RMariaDB)
library(DBI)

txt <- " Zasięg ostrzeżeń w województwie WOJEWÓDZTWO PODKARPACKIE OSTRZEŻENIA METEOROLOGICZNE ZBIORCZO NR 1 WYKAZ OBOWIĄZUJĄCYCH OSTRZEŻEŃ o godz. 11:36 dnia 01.01.2022 \nZjawisko/Stopień zagrożenia Oblodzenie/1 Obszar (w nawiasie numer powiaty: bieszczadzki(1), jasielski(1), krośnieński(1), leski(1), sanocki(1) Ważność od godz. 00:00 dnia 02.01.2022 do godz. 08:00 dnia 02.01.2022 Prawdopodobieństwo 70% Przebieg Prognozuje się miejscami zamarzanie mokrej nawierzchni dróg i chodników po opadach deszczu powodujące ich oblodzenie. Temperatura minimalna około -1°C, temperatura minimalna przy gruncie około -2°C. SMS IMGW-PIB OSTRZEGA: OBLODZENIE/1 podkarpackie (5 powiatów) od 00:00/02.01 do 08:00/02.01.2022 temp. min. -1 st, przy gruncie -2 st., ślisko. Dotyczy powiatów: bieszczadzki, jasielski, krośnieński, leski i sanocki. RSO Woj. podkarpackie (5 powiatów), IMGW-PIB wydał ostrzeżenie pierwszego stopnia o oblodzeniu Uwagi Brak. \nZjawisko/Stopień zagrożenia Roztopy/2 ZMIANA Obszar (w nawiasie numer powiaty: bieszczadzki(147), jasielski(140), krośnieński(142), leski(147), sanocki(146) Ważność od godz. 00:00 dnia 31.12.2021 do godz. 24:00 dnia 01.01.2022 Prawdopodobieństwo 80% Przebieg Prognozuje się wzrost temperatury powietrza powodujący odwilż i topnienie pokrywy śnieżnej w górach. Temperatura minimalna od 0 °C do 3°C. Temperatura maksymalna od 3°C do 6°C. Suma opadów deszczu od 10 mm do 20 mm w pierwszej dobie ostrzeżenia, od 5 mm do 10 mm w drugiej dobie ostrzeżenia. Wiatr o średniej prędkości od 
20 km/h do 30 km/h, w porywach do 50 km/h, z południowego zachodu i zachodu, w szczytowych partiach gór od 35 km/h do 45 km/h, w porywach do około 80 km/h, zachodni i północno-zachodni. SMS IMGW-PIB OSTRZEGA: ROZTOPY/2 podkarpackie (5 powiatów) od 00:00/31.12 do 24:00/01.01.2022 Wzrost temp. do 6 st, opady do 20 mm, wiatr 30 km/h, porywy 50 km/h. Dotyczy powiatów: bieszczadzki, jasielski, krośnieński, leski i sanocki. RSO Woj. podkarpackie (5 powiatów), IMGW-PIB wydał ostrzeżenie drugiego stopnia o Uwagi W późniejszym okresie nadal prognozuje się dodatnią temperaturę. Zmiana dotyczy błędnego podania ważności Ostrzeżenia. Dyżurny synoptyk Małgorzata Marcinek IMGW-PIB Opracowanie niniejsze i jego format, jako przedmiot prawa autorskiego podlega ochronie prawnej, zgodnie z przepisami ustawy z dnia 4 lutego 1994r o prawie autorskim i prawach pokrewnych (dz. U. z 2006 r. Nr 90, poz. 631 z późn. zm.). Wszelkie dalsze udostępnianie, rozpowszechnianie (przedruk, kopiowanie, wiadomość sms) jest dozwolone wyłącznie w formie dosłownej z bezwzględnym wskazaniem źródła informacji tj. IMGW-PIB. "

PATTERN <- gsub("\n", " ", readr::read_file('scrapper/pattern.txt'))
START_PATTERN <- gsub("\n", " ", readr::read_file('scrapper/start_pattern.txt'))
START_PATTERN <- gsub("\r", "", START_PATTERN)
PATTERN <- gsub("\r", "", PATTERN)

pat <- str_match(txt, START_PATTERN)
df <- as.data.frame(str_match_all(pat[4], PATTERN))
print(df)