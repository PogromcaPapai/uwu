#
# This is a Plumber API. You can run the API by clicking
# the 'Run API' button above.
#
# Find out more about building APIs with Plumber here:
#
#    https://www.rplumber.io/
#
library(xml2)
library(dplyr)
library(pdftools)
library(stringr)
library(plumber)

#* @apiTitle IMGW warning scrapper
#* @apiDescription Scrapuje ostrzeżenia pogodowe ze strony IMGW

preprocess <- function(txt) {
  txt <- gsub("\n+| {2,}\t+", " ", txt)
  txt <- gsub("strona \\d+ z \\d+", "", txt, ignore.case=T)
  txt <- gsub(" Instytut Meteorologii i Gospodarki Wodnej .+  www: www\\.imgw\\.pl", "", txt, ignore.case=T)
  txt <- gsub("Zjawisko/Stopień", "\nZjawisko/Stopień", gsub("ostrzeżenia dla powiatu\\) ", "", txt))
  txt <- paste(txt, collapse=" ")
  txt <- gsub(" {2,}", " ", txt)
  
  return (txt)
}

extract <- function(txt) {
  START_PATTERN <- paste(
    "Zasięg ostrzeżeń w województwie WOJEWÓDZTWO (?<voivodeship>[\\w\\-]+)",
    "OSTRZEŻENIA METEOROLOGICZNE ZBIORCZO NR (?<id>\\d+) WYKAZ OBOWIĄZUJĄCYCH OSTRZEŻEŃ",
    "o godz\\. \\d\\d:\\d\\d dnia \\d\\d\\.\\d\\d\\.\\d{4}",
    "(?<text>(?:\\n|.)+)",
    "Dyżurny synoptyk (?<creator>.+?(?= IMGW-PIB))",
    sep=" "
  )
  PATTERN <- paste(
    "Zjawisko/Stopień zagrożenia (?<event>[\\w ]+)/(?<lvl>\\d+)(?<messtype>| \\w+)",
    "Obszar \\(w nawiasie numer powiaty: (?<regions>(?:[\\w- ]+\\(\\d+\\)(?:, )*)+)",
    "(?:Ważność od godz\\. (?<starthour>\\d\\d\\:\\d\\d)",
    "dnia (?<startday>\\d\\d\\.\\d\\d\\.\\d{4})",
    "do godz\\. (?<endhour>\\d\\d\\:\\d\\d)",
    "dnia (?<endday>\\d\\d\\.\\d\\d\\.\\d{4})",
    "Prawdopodobieństwo (?<prob>\\d{1,3}\\%)",
    "Przebieg (?<how>.+?(?= SMS))|Czas",
    "odwołania godz\\. (?<hour>\\d\\d:\\d\\d) dnia (?<day>\\d\\d\\.\\d\\d\\.\\d{4})",
    "Przyczyna (?<cause>.+?(?= SMS)))",
    "SMS (?<sms>.+?(?= RSO))",
    "RSO (?<rso>.+?(?= Uwagi))",
    "Uwagi (?<remarks>[^\\n]+)",
    sep=" "
  )
  pat <- str_match(txt, START_PATTERN)
  df <- as.data.frame(str_match_all(pat[4], PATTERN))
  df$voivodeship <- str_to_lower(pat[2], locale = 'pl')
  df$warn_id <- pat[3]
  df$author <- pat[5]
  df$infile <- rownames(df)
  return (df)
}

get_warns <- function(file) {
  webpage_url <- "https://danepubliczne.imgw.pl/data/current/ost_meteo/"
  time <- Sys.time()
  saved.file <- paste('tmp', file, sep="/")
  if (!file.exists(saved.file)) {
    download.file(paste(webpage_url, file, sep=""), saved.file, mode="wb")
  }
  
  txt <- preprocess(pdf_text(saved.file))
  extracted <- extract(txt)
  extracted$file <- file
  extracted['downloaded'] <- time
  extracted$messtype[extracted['messtype']==" ZMIANA"] <- "1"
  extracted$messtype[extracted['messtype']==" WYCOFANIE"] <- "2"
  extracted$messtype[extracted['messtype']==""] <- "0"
  return (extracted)
}


format_time <- function(date, time) {
  if (is.null(date) || is.null(time)) return (NULL)
  t <- strptime(paste(date, time), "%d.%m.%Y %H:%M")
  return (t)
}

get_places <- function(place_list) {
  p <- " ?([^,]+)\\(\\d+\\),?"
  match <- str_match_all(place_list, p)
  return (lapply(match, function(x) {
    return (str_to_lower(x[,2]))
  }))
}


#* Zwraca ostrzeżenia dla powiatu
#* @param place powiat występowania
#* @get /warns
function(place) {
  
  # Get file list
  webpage_url <- "https://danepubliczne.imgw.pl/data/current/ost_meteo/"
  webpage <- xml2::read_html(webpage_url)
  
  ost_files <- rvest::html_table(webpage)[[1]] %>% 
    tibble::as_tibble(.name_repair = "unique") %>%
    filter(Name != "" & Name != "Parent Directory") %>%
    select(Name, `Last modified`) %>% 
    rename_with(function(x){"modified_at"}, "Last modified")
  
  # Download and process files
  if (length(ost_files$Name)>0){
    file <- ost_files$Name[1]
    df <- get_warns(file)
  
    if (length(ost_files$Name)>1){
      for (file in ost_files$Name[2:length(ost_files$Name)]) {
        df <- rbind(df, get_warns(file))
      }
    }
    
    # Formatting
    df$regions <- get_places(df$regions)
    df$include <- unlist(lapply(df$regions, function(x){place %in% x}))
    
    df$starttime <- mapply(format_time, df$startday, df$starthour)
    df$endtime <- mapply(format_time, df$endday, df$endhour)
    df$canceltime <- mapply(format_time, df$day, df$hour)
    
    fin <- df %>% filter(messtype == '0') %>% filter(include) %>% select(-c(V1, include, startday, starthour, endday, endhour, day, hour))
    return(fin)
  }
  else {
    return(c())
  }
}
