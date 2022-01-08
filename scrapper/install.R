load.lib<-c(
    "xml2", 
    "dplyr", 
    "pdftools", 
    "stringr", 
    "plumber"
)

install.lib<-load.lib[!load.lib %in% installed.packages()]
for(lib in install.lib) install.packages(lib,dependencies=TRUE)
sapply(load.lib,require,character=TRUE)
