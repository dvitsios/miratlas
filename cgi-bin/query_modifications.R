args <- commandArgs(TRUE)


print("1234567")

stop()
# Database Connection details
usr = "dvitsios"
pass = "appelstroop"
hostname = "mysql-enright-rnagen-prod"
database_name = "miratlas_db"


write('try me.', file='./temp_MYSQL_FROM_R_generated.txt')


# SHOULD RUN IT EVERY TIME THE FILES THAT POPULATE
# THE 'MIRCOUNTS_TABLE' CHANGE!
# (try creating a view instead)


# *************************************
# **** CONNECT TO A MySQL DATABASE ****
# *************************************
mysql_db_connect <- function(usr, pass, hostname, database_name){
  
  #con <- dbConnect(MySQL(), user=usr, password=pass, host=hostname, dbname=database_name, unix.socket="/Applications/MAMP/tmp/mysql/mysql.sock")  
  con <- dbConnect(MySQL(), user=usr, password=pass, host=hostname, dbname=database_name)  
  return(con)
}

con <- mysql_db_connect(usr, pass, hostname, database_name)


#get all dataset names
query = "SELECT ACCESSION_NUMBER FROM DATASETS"
datasets = dbGetQuery(con, query)  
datasets = datasets$ACCESSION_NUMBER

# get all mirnas expressed in each dataset
for(d in datasets){

# get mod patterns and raw counts for each dataset
    query = paste("SELECT MATURE_MIR_ID_REF, modification_type, arm, pattern, position, raw_counts FROM mircounts_full_view WHERE ACCESSION_NUMBER_REF='", d, "'", sep="")
    
    mir_entries_for_cur_dataset = dbGetQuery(con, query)

    mir_entries_for_cur_dataset <- lapply(mir_entries_for_cur_dataset, function(x){replace(x, x=='','-')})
    setattr(mir_entries_for_cur_dataset, 'class', c('data.table','data.frame'))
    #write.table(mir_entries_for_cur_dataset, file='./temp_MYSQL_FROM_R_generated.txt')


    print(head(mir_entries_for_cur_dataset))


# sum all counts from e.g. 'U" mod for each miRNA in order to get
# the global score for U, or do it using only the counts from that miRNA?

# !!!
# also maybe include a ratio of the overall_expr_ratio
# to the significance ratio calculated among all miRNAs for a specific mod pattern.
       

print.table(head(mir_entries_for_cur_dataset))


stop()
}
