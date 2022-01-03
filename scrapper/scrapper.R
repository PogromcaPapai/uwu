library(xml2)
library(dplyr)
library(pdftools)
library(stringr)
library(RMariaDB)
library(DBI)

#args = commandArgs(trailingOnly=TRUE)
args = c("uvvv")

preprocess <- function(txt) {
  txt <- gsub("\n+| {2,}\t+", " ", txt)
  txt <- gsub("strona \\d+ z \\d+", "", txt, ignore.case=T)
  txt <- gsub(" Instytut Meteorologii i Gospodarki Wodnej .+  www: www\\.imgw\\.pl", "", txt, ignore.case=T)
  txt <- gsub("Zjawisko/Stopień", "\nZjawisko/Stopień", gsub("ostrzeżenia dla powiatu\\) ", "", txt))
  txt <- paste(txt, collapse=" ")
  txt <- gsub(" {2,}", " ", txt)
  
  return (txt)
}

START_PATTERN <- gsub("\n", " ", readr::read_file('scrapper/start_pattern.txt'))
PATTERN <- gsub("\n", " ", readr::read_file('scrapper/pattern.txt'))
START_PATTERN <- gsub("\r", "", START_PATTERN)
PATTERN <- gsub("\r", "", PATTERN)

extract <- function(txt) {
  pat <- str_match(txt, START_PATTERN)
  df <- as.data.frame(str_match_all(pat[4], PATTERN))
  print(df)
  df$voivodeship <- str_to_lower(pat[2], locale = 'pl')
  df$warn_id <- pat[3]
  df$author <- pat[5]
  df$infile <- rownames(df)
  return (df)
}

get_warns <- function(file) {
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
  if (is.null(date) || is.null(time)) return ("")
  t <- strptime(paste(date, time), "%d.%m.%Y %H:%M")
  return (t)
}

get_places <- function(place_list) {
  PATTERN <- "([^, ]+)\\(\\d+\\),?"
  match <- str_match_all(place_list, PATTERN)
  return (str_to_lower(match[[1]][,2]))
}


# Cleaning the tmp directory
unlink("tmp", recursive = TRUE)
dir.create("tmp")


# Connecting to the database
mydb <-  dbConnect(MariaDB(), user = 'warn-scrap', password = 'FW2BL(@qHE)vS*nY',
                   dbname = args[1], host = 'localhost', port = 3306)
old_data <- dbReadTable(mydb, 'warnings') %>% select('file', 'downloaded_at') %>% distinct()

# Get file list
webpage_url <- "https://danepubliczne.imgw.pl/data/current/ost_meteo/"
webpage <- xml2::read_html(webpage_url)

ost_files <- rvest::html_table(webpage)[[1]] %>% 
  tibble::as_tibble(.name_repair = "unique") %>%
  filter(Name != "" & Name != "Parent Directory") %>%
  select(Name, `Last modified`) %>% 
  rename_with(function(x){"modified_at"}, "Last modified")

to_delete <- old_data %>% filter(!(file %in% ost_files$Name)) %>% c

# Clean unwanted files
dbExecute(mydb, "DELETE FROM warned_places WHERE warning_id = ?", immediate = TRUE, params=list(to_delete$file))
dbExecute(mydb, "DELETE FROM warnings WHERE file = ?", immediate = TRUE, params=list(to_delete$file))

# Download and process files
file <- ost_files$Name[1]
df <- get_warns(file)

for (file in ost_files$Name[2:length(ost_files$Name)]) {
  df <- rbind(df, get_warns(file))
}
df <- df %>% filter(messtype == '0')

# Insert to DB
if (nrow(df)>0)
{
  for(i in 1:nrow(df)) {
    row <- df[i,]
    # dbExecute(mydb, "INSERT INTO warnings(event, lvl, starttime, endtime, prob, how, canceltime, cause, sms, rso, remarks, file, downloaded_at) 
    #   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", immediate = TRUE,
    #           params = list(row$event, row$lvl, format_time(row$startday, row$starthour), format_time(row$endday, row$endhour), row$prob, row$how, 
    #                         format_time(row$day, row$hour), row$cause, row$sms, row$rso, row$remarks, row$file, row$downloaded))
    for (j in get_places(row$regions)) {
      res <- dbGetQuery(mydb, "SELECT id FROM places WHERE powiat = ?", params = j)
      res['max'] <- dbGetQuery(mydb, "SELECT MAX(id) FROM warnings")
      # dbExecute(mydb, "INSERT INTO warned_places(place_id, warning_id) VALUES (?, ?)", immediate = TRUE,
      #           params = list(res$id, res$max))
      }
    }
}
on.exit(dbDisconnect(mydb))