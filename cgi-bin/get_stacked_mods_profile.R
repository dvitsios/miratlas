#Get overall modifications profile for a sample/dataset



# Read input
args = commandArgs(trailingOnly = TRUE)
curInputFile = args[1]
mir_to_filter <- args[2]

webapp_root = "/net/isilonP/public/rw/homes/w3_enr01/enright-dev/miratlas"

outputProfilesDir = paste(webapp_root, "tmp", sep="/")
#print(curInputFile)
#print(outputProfilesDir)


dataset_prefix = curInputFile
dataset_prefix = gsub("\\.tmp", "", dataset_prefix)

curInputFile = paste(webapp_root, "/tmp/", curInputFile, sep="")

dir.create(outputProfilesDir, showWarnings=F)
#outputProfilesDir = paste(outputProfilesDir, "/", sep="")

# Maximum modifications length to use when creating the profile of NTs distribution in all the modifications.
MAX_MOD_LENGTH_FOR_GLOBAL_NT_DISTR_PROFILE = 7

#dbg_file = "/net/isilonP/public/rw/homes/w3_enr01/enright-dev/chimira/cgi-bin/dbg_file.txt"
#write(tempdir(), dbg_file, append=TRUE)




# constant global variables??rmyawl
CONSTANT_MIRCOUNTS_COLUMN_NAMES = vector()
NTs <- c("A", "U", "C", "G")
ARM_SPECIFIC_ANALYSIS <- F # get mods profile only at one end (5p or 3p) of the miRNAs
UNIFORM_MIRNA_LENGTH <- 22 # constant, no need to change it ever.



ntFreqsAtEachPositionInMods_both_arms = 0



# ======================================================
#################################################
######***********   Run exec   ************######
#################################################
# read input:
mircounts <- read.table(curInputFile, header=TRUE, sep=",", stringsAsFactors=F, row.names=NULL)
mircounts = mircounts[ , 3:ncol(mircounts)]

mircounts[ is.na(mircounts) ] = ""

colnames(mircounts) = c("MIRNA", "MODIFICATION_TYPE", "MODIFICATION_ARM", "MODIFICATION_PATTERN", "MODIFICATION_POSITION", "INTERNAL_MOD_TYPE", "INTERNAL_MOD_PATTERN", "INTERNAL_MOD_POSITION", "DOUBLED", "processed.counts")

#print(head(mircounts))

#normalizeByQuantile <- function(mircounts){
#  library(preprocessCore)
#  normcounts <- normalize.quantiles(as.matrix(mircounts))
#  return(normcounts)
#}

#normalizeByReadsDepth <- function(mircounts){
#    samplecounts = apply(mircounts,2,sum)
#    scales = max(samplecounts)/samplecounts

#    normcounts = sweep(mircounts,2,scales,'*')

#    return(normcounts)
#}

#mircounts = normalizeByQuantile(mircounts)


CONSTANT_MIRCOUNTS_COLUMN_NAMES <- colnames(mircounts)[1:9]

# filter for specific miRNA
if(!is.null(mir_to_filter) & !is.na(mir_to_filter)){
    mircounts = mircounts[ mircounts$MIRNA %in% mir_to_filter, ]
    outputProfilesDir = paste(outputProfilesDir, "/", mir_to_filter, "_", sep="")
    
    # debug only
    #write(mir_to_filter, file = paste("../../tmp/", mir_to_filter, ".txt", sep=""))
    #write(outputProfilesDir, file = paste("../../tmp/", mir_to_filter, ".txt", sep=""), append=TRUE)
    	
}

#print(paste("mir_to_filter:",mir_to_filter))

#print(head(mircounts))

#write.table(head(mircounts), file = paste("../../tmp/", mir_to_filter, ".txt", sep=""), append=TRUE)



# > extra feature to add...
# focus on specific modification pattern
# tt <- mircounts[mircounts$MODIFICATION_PATTERN == "GGAG", ]
# print(head(tt,30))




#colnames(mircounts) <- gsub("_processed.counts", "", colnames(mircounts))


# import mature miRNA IDs vs universal mirbase IDs mappings into a data frame
#univIdToMatureIdsDf <- read.table(file="../data//matureToUnivIDMappings.txt", header=TRUE, sep="\t")

#write.table(head(univIdToMatureIdsDf), file = paste("../../tmp/", mir_to_filter, ".txt", sep=""), append=TRUE)






##########################################
############## FUNCTIONS #################
##########################################
outersect <- function(x, y) {
  sort(c(setdiff(x, y),
         setdiff(y, x)))
}


createFreqTableForPatternOfACertainLength <- function(patternLength, nts){
  
  
  totalNumOfPatterns <- length(nts)^patternLength
  
  ntFreqMat <- matrix(numeric(0), totalNumOfPatterns, 1) 
  ntFreqsDf <- as.data.frame(ntFreqMat)
  ntFreqsDf[,] <- 0
  
  colnamesTmpVec <- c("Frequency")
  colnames(ntFreqsDf) <- colnamesTmpVec
  
  
  
  ntsPatternsDf <- 0
  
  
  if(patternLength == 1){
    ntsPatternsDf <- nts
  } else if(patternLength == 2){
    ntsPatternsDf <- expand.grid(nts,nts)
  } else if(patternLength == 3){
    ntsPatternsDf <- expand.grid(nts,nts,nts)
  } else if(patternLength == 4){
    ntsPatternsDf <- expand.grid(nts,nts,nts,nts)
  }
  
  
  
  ntsPatterns <- do.call(paste, as.data.frame(ntsPatternsDf, stringsAsFactors=FALSE))
  ntsPatterns <- gsub(" ","",ntsPatterns)
  
  rownames(ntFreqsDf) <- ntsPatterns
  
  
  return(ntFreqsDf)
}





get_modifications_profile <- function(mircounts, CUSTOM_IDENTIFIER){
  
  
  # GET TOTAL NUMBER OF COUNTS FOR EACH MIR-MODS PAIR
  # - For each miRNA, add the counts from all the datasets together
  
  colsToKeep <- CONSTANT_MIRCOUNTS_COLUMN_NAMES
  countsDf <- mircounts[ , !(colnames(mircounts) %in% colsToKeep)] 
  
  # Foreach mirna-mod pair: calc sum of counts in all datasets
  countRowSums <- rowSums(countsDf)
  
  # drop dataset columns as they are not needed anymore
  mircounts <- mircounts[ , colnames(mircounts) %in% colsToKeep]
  
  # get total number of counts for each miRNA-mod pair and for the non-modified miRNAs as well
  mircounts$COUNTS <- countRowSums
  
  
  
  
  
  totalNumOfProfileColumns <- 2*MAX_MOD_LENGTH_FOR_GLOBAL_NT_DISTR_PROFILE + UNIFORM_MIRNA_LENGTH
  
  MIRNA_5P_PIVOT <- MAX_MOD_LENGTH_FOR_GLOBAL_NT_DISTR_PROFILE+1
  MIRNA_3P_PIVOT <- MIRNA_5P_PIVOT + UNIFORM_MIRNA_LENGTH
  
  
  MAX_OFFSET_OF_MOD <- 4
  
  
  
  create_full_freq_table <- function(){
    nts <- c("U", "A", "C", "G", "G_adar", "U_snp", "A_snp", "C_snp", "G_snp")
    ntFreqsAtEachPositionInMods <- createFreqTableForPatternOfACertainLength(1, nts)
    
    for(i in 2:totalNumOfProfileColumns){
      ntFreqsAtEachPositionInMods <- cbind(ntFreqsAtEachPositionInMods, createFreqTableForPatternOfACertainLength(1, nts))
    }
    
    colnames(ntFreqsAtEachPositionInMods) <- seq(1,totalNumOfProfileColumns)
    
    return(ntFreqsAtEachPositionInMods)
  }
  
  
  ntFreqsAtEachPositionInMods <- create_full_freq_table()
  
  
  # .............................................................
  # 1. Fill freq table with mods (also snp, adar) in the arm ends
  
  fillFreqsTableWithArmEndMods <- function(entry){
    
    # deal with mods in arm ends first (nont, snp and adar)
    mod_type <- entry[2]
    mod_arm <- entry[3]
    mod_pattern <- entry[4]
    mod_position <- as.numeric(entry[5])
    
    RUN_FLAG <- (mod_arm == "3p" && mod_position>= -MAX_OFFSET_OF_MOD && mod_position <= MAX_MOD_LENGTH_FOR_GLOBAL_NT_DISTR_PROFILE) || (mod_arm == "5p" && mod_position>= -MAX_MOD_LENGTH_FOR_GLOBAL_NT_DISTR_PROFILE && mod_position+nchar(mod_pattern) <= MAX_OFFSET_OF_MOD+2)  #emperical rought limit (+2)
    
    #RUN_FLAG = TRUE    

    if(RUN_FLAG){
      
      
      extra_mod_identifier <- ''
      
      if(mod_type == 'snp' || mod_type == 'adar'){
        extra_mod_identifier <- mod_type
      }
      
      cur_counts <- as.numeric(entry[length(entry)])
      
      freq_table_start_index <- numeric(0)
      if(mod_arm == "5p"){
        freq_table_start_index <- MIRNA_5P_PIVOT + mod_position
      } else if(mod_arm == "3p"){
        freq_table_start_index <- MIRNA_3P_PIVOT + mod_position
      }
      
      freq_table_end_index <- freq_table_start_index + nchar(mod_pattern)
      
      for(i in 1:nchar(mod_pattern)){
        cur_nt <- substr(mod_pattern, i, i)
        cur_table_col <- freq_table_start_index + i - 1
        
        if(extra_mod_identifier != ''){
          cur_nt <- paste(cur_nt, extra_mod_identifier, sep="_")
        }
        
        if(cur_table_col < 1 || cur_table_col >totalNumOfProfileColumns){
          next
        }
        #print(cur_nt)
        
        prev_table_val <- ntFreqsAtEachPositionInMods[rownames(ntFreqsAtEachPositionInMods) == cur_nt, cur_table_col]
        
        
        ntFreqsAtEachPositionInMods[rownames(ntFreqsAtEachPositionInMods) == cur_nt, cur_table_col] <<- prev_table_val + cur_counts
        
        #print(ntFreqsAtEachPositionInMods)
        
      }
    }
  }
  
  
  # .............................................
  # 2. Fill freq table with internal snp and adar
  
  negative_internal_mod_positions_cnt <- 0
  MAX_ALLOWED_INTERNAL_MOD_POSITION <- 25
  
  fillFreqsTableWithInternalMods <- function(entry){
    
    #print(entry)
    # deal with internal mods (snp and adar)
    internal_mod_type <- entry[6]
    internal_mod_pattern <- entry[7]
    internal_mod_position <- as.numeric(entry[8])
    
    # debug info
    if(internal_mod_position < 0){
      negative_internal_mod_positions_cnt <<- negative_internal_mod_positions_cnt + 1
    }  
    
    if(internal_mod_position >= 0 && internal_mod_position <= MAX_ALLOWED_INTERNAL_MOD_POSITION)
    { 
      
      extra_mod_identifier <- internal_mod_type
      
      
      cur_counts <- as.numeric(entry[length(entry)])
      
      # same reference pivot used for all internal mods, this is the 5p pivot - start of the miRNA
      freq_table_start_index <- MIRNA_5P_PIVOT + internal_mod_position
      
      
      cur_table_col <- freq_table_start_index
      
      cur_nt <- paste(internal_mod_pattern, extra_mod_identifier, sep="_")
      
      #       print(cur_nt)
      prev_table_val <- ntFreqsAtEachPositionInMods[rownames(ntFreqsAtEachPositionInMods) == cur_nt, cur_table_col]
      
      ntFreqsAtEachPositionInMods[rownames(ntFreqsAtEachPositionInMods) == cur_nt, cur_table_col] <<- prev_table_val + cur_counts
      
      #take care of the doubled values above (?)
    }
    
  }
  
  
  GET_ONLY_3P_MODS <- T
  GET_ONLY_5P_MODS <- T
  GET_ONLY_INTERNAL_MODS <- T
  GET_ALL_MODS <- T
  
  
  
  colorsVec <- c("green", "red", "blue", "yellow", "palegreen", "tan1", "skyblue1", "lightgoldenrodyellow", "grey")
  
  
  # ___run___
  # [1] subset of mircounts with 3p mods only.
  if(GET_ONLY_3P_MODS){
    
    #print("Running for subset of mircounts with 3p mods only...")
    
    #mircounts3p <- mircounts[mircounts$MODIFICATION_ARM == '3p', ]
    mircounts3p <- mircounts
    #print(head(mircounts3p))
    
    mircounts_only_mods_3p <- mircounts3p[mircounts3p$MODIFICATION_TYPE != '-', ]
   
	#print(head(mircounts_only_mods_3p))
 
    ntFreqsAtEachPositionInMods <- create_full_freq_table()


   #mircounts_only_mods_3p  <- mircounts_only_mods_3p[ , (mircounts_only_mods_3p$MODIFICATION_ARM == "3p" && mircounts_only_mods_3p$MODIFICATION_POSITION>= -MAX_OFFSET_OF_MOD && mircounts_only_mods_3p$MODIFICATION_POSITION <= MAX_MOD_LENGTH_FOR_GLOBAL_NT_DISTR_PROFILE) || (mircounts_only_mods_3p$MODIFICATION_ARM == "5p" && mircounts_only_mods_3p$MODIFICATION_POSITION>= -MAX_MOD_LENGTH_FOR_GLOBAL_NT_DISTR_PROFILE && mircounts_only_mods_3p$MODIFICATION_POSITION+nchar(mircounts_only_mods_3p$MODIFICATION_PATTERN) <= MAX_OFFSET_OF_MOD+2)]  #emperical rought limit (+2)


#print(mircounts_only_mods_3p)


    apply(mircounts_only_mods_3p, 1, function(x) fillFreqsTableWithArmEndMods(x))


	#print(ntFreqsAtEachPositionInMods)

    ntFreqsAtEachPositionInMods_both_arms = ntFreqsAtEachPositionInMods    

    
    ntFreqsAtEachPositionInMods_only_3p_mods <- t(ntFreqsAtEachPositionInMods) 
    
    
    # keep for global profile
    ntFreqsAtEachPositionInMods_only_3p_mods = ntFreqsAtEachPositionInMods_only_3p_mods[25:nrow(ntFreqsAtEachPositionInMods_only_3p_mods), ]
   
	#print(head(ntFreqsAtEachPositionInMods_only_3p_mods)) 


if(sum(rowSums(ntFreqsAtEachPositionInMods_only_3p_mods)) != 0){ 
    
    # add index names
    #rownames(ntFreqsAtEachPositionInMods_only_3p_mods)[8] = "5p end"
    rownames(ntFreqsAtEachPositionInMods_only_3p_mods) = c(seq(17,22), c("+1","+2","+3","+4","+5","+6"))
    rownames(ntFreqsAtEachPositionInMods_only_3p_mods)[6] = "3p end"
    
    
    ntFreqsAtEachPositionInMods_only_3p_mods = cbind(rownames(ntFreqsAtEachPositionInMods_only_3p_mods), ntFreqsAtEachPositionInMods_only_3p_mods)
    colnames(ntFreqsAtEachPositionInMods_only_3p_mods)[1] = "mirna_index"
    
    
    #print(ntFreqsAtEachPositionInMods_only_3p_mods[25:nrow(ntFreqsAtEachPositionInMods_only_3p_mods), ])
   
    #print(head(ntFreqsAtEachPositionInMods_only_3p_mods))
 
    
    mods_3p_profile_file = paste(outputProfilesDir, "3p_mods_profile_data.csv", sep="")
    #write.csv(ntFreqsAtEachPositionInMods_only_3p_mods, file=mods_3p_profile_file, row.names = FALSE)
    }
    
    # create barplot with 3p mods only profile
    #mat <- as.matrix(ntFreqsAtEachPositionInMods_only_3p_mods)
    
   
    
    #barplot(mat, col=colorsVec, legend = rownames(mat), bty='L', args.legend = list(x = "left", bty = "n", cex=0.6), main=paste("3p modifications profile", CUSTOM_IDENTIFIER, sep=" - "))
    
    #mat <- apply(mat,2, function(x){ x<-signif(100*x/sum(x),digits=3); return(x)})
    #barplot(mat, col=colorsVec, main=paste("3p modifications profile (%ratios)", CUSTOM_IDENTIFIER, sep=" - "))
  }
  
  
  
  
  # [2] subset of mircounts with 5p mods only.
  if(GET_ONLY_5P_MODS){
    
    #print("Running for subset of mircounts with 5p mods only...")
    
#    mircounts5p <- mircounts[mircounts$MODIFICATION_ARM == '5p', ]
#    print(head(mircounts5p))
    
#    mircounts_only_mods_5p <- mircounts5p[mircounts5p$MODIFICATION_TYPE != '-', ]
    #     mircounts_only_mods_5p <- mircounts_only_mods_5p[ as.numeric(mircounts_only_mods_5p$MODIFICATION_POSITION) >= -MAX_MOD_LENGTH_FOR_GLOBAL_NT_DISTR_PROFILE || as.numeric(mircounts_only_mods_5p$MODIFICATION_POSITION) <= MAX_OFFSET_OF_MOD, ]
    
#    ntFreqsAtEachPositionInMods <- create_full_freq_table()
#    apply(mircounts_only_mods_5p, 1, function(x) fillFreqsTableWithArmEndMods(x))
    
#    ntFreqsAtEachPositionInMods_only_5p_mods <- ntFreqsAtEachPositionInMods


    ntFreqsAtEachPositionInMods_only_5p_mods = ntFreqsAtEachPositionInMods_both_arms[ , 1:13]

 if(sum(rowSums(ntFreqsAtEachPositionInMods_only_5p_mods)) != 0){
    #print(ntFreqsAtEachPositionInMods_only_5p_mods)
    		 
   
    ntFreqsAtEachPositionInMods_only_5p_mods = t(ntFreqsAtEachPositionInMods_only_5p_mods)
    
    # keep for global profile
    
    # add index names
    rownames(ntFreqsAtEachPositionInMods_only_5p_mods) = seq(-7,5)
    rownames(ntFreqsAtEachPositionInMods_only_5p_mods)[8] = "5p end"
    

    ntFreqsAtEachPositionInMods_only_5p_mods = cbind(rownames(ntFreqsAtEachPositionInMods_only_5p_mods), ntFreqsAtEachPositionInMods_only_5p_mods)
    colnames(ntFreqsAtEachPositionInMods_only_5p_mods)[1] = "mirna_index"
    
    mods_5p_profile_file = paste(outputProfilesDir, "5p_mods_profile_data.csv", sep="")
    #write.csv(ntFreqsAtEachPositionInMods_only_5p_mods, file=mods_5p_profile_file, row.names = FALSE)
    }
    
    # create barplot with 5p mods only profile
    #mat <- as.matrix(ntFreqsAtEachPositionInMods_only_5p_mods)
    #barplot(mat, col=colorsVec, legend = rownames(mat), bty='L', args.legend = list(x = "right", bty = "n", cex=0.6), main=paste("5p modifications profile", CUSTOM_IDENTIFIER, sep=" - "))
    
    #mat <- apply(mat,2, function(x){ x<-signif(100*x/sum(x),digits=3); return(x)})
    #barplot(mat, col=colorsVec, main=paste("5p modifications profile (% ratios)", CUSTOM_IDENTIFIER, sep=" - "))
  }
  
  
  # [3] fill in the internal mods
  if(GET_ONLY_INTERNAL_MODS){
    
    #print("Running for subset of mircounts with internal mods only...")
    
    mircounts_only_internal_mods <- mircounts[mircounts$INTERNAL_MOD_TYPE != '-', ]
    
    ntFreqsAtEachPositionInMods <- create_full_freq_table()
    apply(mircounts_only_internal_mods, 1, function(x) fillFreqsTableWithInternalMods(x))
   
    # keep for global profile
    ntFreqsAtEachPositionInMods_only_internal_mods_global = ntFreqsAtEachPositionInMods

if(sum(rowSums(ntFreqsAtEachPositionInMods)) != 0){

 
    ntFreqsAtEachPositionInMods_only_internal_mods <- ntFreqsAtEachPositionInMods
    #print(ntFreqsAtEachPositionInMods_only_internal_mods)
    
    
    ntFreqsAtEachPositionInMods_only_internal_mods = t(ntFreqsAtEachPositionInMods_only_internal_mods)
    
    
    
    # add index names
    rownames(ntFreqsAtEachPositionInMods_only_internal_mods) = c(seq(-7,22), seq(1,6))
    rownames(ntFreqsAtEachPositionInMods_only_internal_mods)[8] = "5p end"
    rownames(ntFreqsAtEachPositionInMods_only_internal_mods)[30] = "3p end"
    
    
    ntFreqsAtEachPositionInMods_only_internal_mods = cbind(rownames(ntFreqsAtEachPositionInMods_only_internal_mods), ntFreqsAtEachPositionInMods_only_internal_mods)
    colnames(ntFreqsAtEachPositionInMods_only_internal_mods)[1] = "mirna_index"
    
    
    mods_internal_profile_file = paste(outputProfilesDir, "internal_mods_profile_data.csv", sep="")
    #write.csv(ntFreqsAtEachPositionInMods_only_internal_mods[8:30, ], file=mods_internal_profile_file, row.names = FALSE)
    
    }
    
    # create barplot with internal mods only profile
    #mat <- as.matrix(ntFreqsAtEachPositionInMods_only_internal_mods)
    #colors_for_internal_mods <- colorsVec[5:nrow(mat)]
    
    #mat <- mat[5:nrow(mat), ]
    
    #barplot(mat, col=colors_for_internal_mods, legend = rownames(mat), bty='L', args.legend = list(x = "topright", bty = "n", cex=0.6), main=paste("Internal modifications profile", CUSTOM_IDENTIFIER, sep=" - "))
    
    #mat <- apply(mat,2, function(x){ x<-signif(100*x/sum(x),digits=3); return(x)})
    #barplot(mat, col=colors_for_internal_mods, main=paste("Internal modifications profile (% ratios)", CUSTOM_IDENTIFIER, sep=" - "))
  }
  
  
  
  if(GET_ALL_MODS){
    #print("Running for all mods...")
    # plot all mods (3p, 5p and internal)!
    
    #print(ntFreqsAtEachPositionInMods_only_3p_mods[,2:ncol(ntFreqsAtEachPositionInMods_only_3p_mods)])
    #print(ntFreqsAtEachPositionInMods_only_5p_mods[,2:ncol(ntFreqsAtEachPositionInMods_only_5p_mods)])
    
    
    #df3p = as.matrix(sapply(ntFreqsAtEachPositionInMods_only_3p_mods[,2:ncol(ntFreqsAtEachPositionInMods_only_3p_mods)], as.numeric))
    
    #df5p = as.matrix(sapply(ntFreqsAtEachPositionInMods_only_5p_mods[,2:ncol(ntFreqsAtEachPositionInMods_only_5p_mods)], as.numeric))
    
    #print(df3p)
    
    #stop()
    
    #df = df3p + df5p
    
    #print("***")
    #print(df)
    #print("---")
    
    
    
    ntFreqsAtEachPositionInMods <- t(ntFreqsAtEachPositionInMods_both_arms) + t(ntFreqsAtEachPositionInMods_only_internal_mods_global)
    
    
 if(sum(rowSums(ntFreqsAtEachPositionInMods)) != 0){
   
    
    global_ntFreqsAtEachPositionInMods = ntFreqsAtEachPositionInMods
    rownames(global_ntFreqsAtEachPositionInMods) = c(seq(-7,22), c("+1","+2","+3","+4","+5","+6"))
    rownames(global_ntFreqsAtEachPositionInMods)[8] = "5p end"
    rownames(global_ntFreqsAtEachPositionInMods)[30] = "3p end"
    
    
    global_ntFreqsAtEachPositionInMods = cbind(rownames(global_ntFreqsAtEachPositionInMods), global_ntFreqsAtEachPositionInMods)
    colnames(global_ntFreqsAtEachPositionInMods)[1] = "mirna_index"
    
    

    #print(global_list_of_ntFreqsAtEachPositionInMods)
    
    
    global_profile_file = paste(outputProfilesDir, "global_mods_profile_data.csv", sep="")
    #write.csv(global_ntFreqsAtEachPositionInMods, file=global_profile_file, row.names = FALSE)
   
} 
    # create barplot with all mods profile
    #mat <- as.matrix(ntFreqsAtEachPositionInMods)
    #barplot(mat, col=colorsVec, legend = rownames(mat), bty='L', args.legend = list(x = "top", bty = "n", cex=0.6), main="Global modifications profile")
    
    #mat <- apply(mat,2, function(x){ x<-signif(100*x/sum(x),digits=3); return(x)})
    #barplot(mat, col=colorsVec, main="Global modifications profile (% ratios)")
    
    
  }
  
  
  return(ntFreqsAtEachPositionInMods)
  
}




# DO HEATMAPS ANALYSIS
# 1. Data frame with all modifications and their frequencies (columns) for each miRNA (rows). Only the most prevalent modifications should be represented.
# - THe columns should also contain the frequency of the templated (non-modified) version of each miRNA.
main <- function(mircounts, arm, CUSTOM_IDENTIFIER){
  
  
  # >>>> DATA CONTENT ANALYSIS - ARM SPECIFIC <<<<
  # Mods with non-identified nucleotides 'X' ratio
  intialNumOfMircountsRows <- nrow(mircounts)
  withoutXsNumOfMircountsRows <- nrow(mircounts[grepl("X", mircounts$MODIFICATION_PATTERN), ])
  #print(paste("Ratio of hits with non-untified nucleotides 'X': ",signif((100*withoutXsNumOfMircountsRows/intialNumOfMircountsRows), digits=3),"%", sep=""))
  
  # - Reject the modifications with unknown nucleotides (noted as 'X')
  mircounts <- mircounts[ !grepl("X", mircounts$MODIFICATION_PATTERN), ]
  
  
  
  allModLengths <- nchar(unique(mircounts$MODIFICATION_PATTERN))
  
  
  # ************* DIAGNOSTICS *************
  # CLEAN (without 'the X's) overall number of mircounts rows
  #print(paste("CLEAN Overall number of mircounts rows: ", nrow(mircounts), sep=""))
  cat("\n")
  
  
  # Non-modified and modified miRNAs ratio
  numOfUniqueMirsInTotal <- length(unique(mircounts$MIRNA))
  #print(paste("Number of (unique) miRNAs IN TOTAL: ",numOfUniqueMirsInTotal, sep=""))
  
  modifiedMirnaCounts <- mircounts[mircounts$MODIFICATION_PATTERN != "-" ,]
  #print(paste("Number of (unique) miRNAs that have modifications: ",length(unique(modifiedMirnaCounts$MIRNA)), sep=""))
  
  nonModifiedMirnaCounts <- mircounts[mircounts$MODIFICATION_PATTERN == "-" ,]
  #print(paste("Number of (unique) miRNAs that are non-modified: ",length(unique(nonModifiedMirnaCounts$MIRNA)), sep=""))
  cat("\n")
  
  modifiedAndNonModifiedMircountsNames <- intersect(modifiedMirnaCounts$MIRNA, nonModifiedMirnaCounts$MIRNA)
  numOfModAndNonModMirNames <- length(modifiedAndNonModifiedMircountsNames)
  numOfModAndNonModMirNamesRatio <- numOfModAndNonModMirNames/numOfUniqueMirsInTotal
  #print(paste("Number of miRNAs that are BOTH MODIFIED and NON-MODIFIED: ", numOfModAndNonModMirNames, " (", signif(100*numOfModAndNonModMirNamesRatio, digits=3),"%)", sep=""))
  
  numOfModMirNames <- length(unique(modifiedMirnaCounts$MIRNA)) - length(modifiedAndNonModifiedMircountsNames)
  numOfModMirNamesRatio <- numOfModMirNames/numOfUniqueMirsInTotal
  #print(paste("Number of (unique) miRNAs that are ONLY MODIFIED: ",numOfModMirNames, " (", signif(100*numOfModMirNamesRatio, digits=3), "%)", sep=""))  
  
  
  exclusivelyNonModMirNames <- outersect(unique(nonModifiedMirnaCounts$MIRNA), modifiedAndNonModifiedMircountsNames)
  numOfNonModMirNames <- length(unique(nonModifiedMirnaCounts$MIRNA)) - length(modifiedAndNonModifiedMircountsNames)
  numOfNonModMirNamesRatio <- numOfNonModMirNames/numOfUniqueMirsInTotal
  #print(paste("Number of (unique) miRNAs that are ONLY NON-MODIFIED: ",numOfNonModMirNames," (", signif(100*numOfNonModMirNamesRatio, digits=3), "%)", sep=""))  
  
  
  
  # CHECK MODIFICATIONS CONTENT
  allModsVector <- as.character(mircounts$MODIFICATION_PATTERN[mircounts$MODIFICATION_PATTERN != "-"])
  #print(paste("Number of UNIQUE MODIFICATION TYPES: ", length(unique(allModsVector)), sep=""))
  cat("\n")
  # ************* END OF DIAGNOSTICS *************
  
  
  
  # [1] Get profile of NTs distribution for each dataset!
    
  global_list_of_ntFreqsAtEachPositionInMods <- data.frame()
  
  dataset = colnames(mircounts)[10]
  
  
  subset_mircounts <- mircounts[ , colnames(mircounts) %in% c(CONSTANT_MIRCOUNTS_COLUMN_NAMES, dataset)]
  subset_mircounts <- subset_mircounts[ subset_mircounts[[dataset]] != 0, ]
  
  
  # plot only for the genome(s) specified in the genomeStr argument.
  if(nrow(subset_mircounts) > 0){  
    
    subset_mircounts <- cbind(subset_mircounts, temp=0)
    global_list_of_ntFreqsAtEachPositionInMods <- get_modifications_profile(subset_mircounts, dataset)
    
  }    
  
  
  
  #print(global_list_of_ntFreqsAtEachPositionInMods)
  #print("\n==============\n\n")
  
  #global_list_of_ntFreqsAtEachPositionInMods = t(global_list_of_ntFreqsAtEachPositionInMods)
  #print(global_list_of_ntFreqsAtEachPositionInMods)

  global_list_of_ntFreqsAtEachPositionInMods = data.frame(rownames(global_list_of_ntFreqsAtEachPositionInMods), global_list_of_ntFreqsAtEachPositionInMods)
  colnames(global_list_of_ntFreqsAtEachPositionInMods)[1] = "mirna_index"
  global_list_of_ntFreqsAtEachPositionInMods$mirna_index = c("-7", "-6", "-5", "-4", "-3", "-2", "-1", "5p end", seq(1,21), "3p end", "+1", "+2", "+3", "+4", "+5", "+6")


trim <- function (x) gsub("^\\s+|\\s+$", "", x)


    convert_to_char <-function(x){
        x = trim(x)
        x = as.character(x)
        #new_x = vector()
        #for(el in x){
        #    el = paste("\"", el, "\"", sep="")
        #}
        #new_x = c(new_x, el)
        #return(new_x)
    }
    
    tt = apply(global_list_of_ntFreqsAtEachPositionInMods, 2, function(x) convert_to_char(x))
    global_list_of_ntFreqsAtEachPositionInMods = tt

    global_profile_file = paste(webapp_root, "/tmp/", dataset_prefix, ".global_mods_profile_data.csv", sep="")
    write.csv(global_list_of_ntFreqsAtEachPositionInMods, file=global_profile_file, row.names = FALSE, quote=T)




  #collapsed_rows = apply(global_list_of_ntFreqsAtEachPositionInMods,1, function(x) paste(x,collapse = ','))
  

  #collapsed_rows = paste("['", names(collapsed_rows), "',", collapsed_rows, "]", sep="")
  #collapsed_rows[1:(length(collapsed_rows)-1)] = paste(collapsed_rows[1:(length(collapsed_rows)-1)], ",", sep="")
  #print(collapsed_rows)
  
  #  global_profile_data = paste(collapsed_rows, collapse = "\n")
  #  print(global_profile_data)
  
  
  # global_profile_file = paste(outputProfilesDir, "global_mods_profile.txt", sep="/")
  
  #print(global_profile_file)
  #print("\n==============\n\n")



  # write(global_profile_data, file=global_profile_file)

}


main(mircounts, "") 
  






