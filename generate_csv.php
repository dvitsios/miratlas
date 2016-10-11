<?php
    //session_start();
    //$acc_num = $_SESSION['acc_num'];
    
    $acc_num = $_GET['acc_num'];
    $acc_num = preg_replace("/draw=1/", '', $acc_num);
    $acc_num = substr_replace($acc_num, "", -1);

    header('Content-Type: application/csv-tab-delimited-table');
    header("Content-Disposition: attachment; filename=$acc_num-mod_counts.txt");



    include 'db_connections.inc';
    include 'mysql_connect.php';

    $sql_query = "SELECT ACCESSION_NUMBER_REF, MATURE_MIR_ID_REF, modification_type, arm, pattern, position, internal_modification_type, internal_pattern, internal_position, doubled, raw_counts FROM mircounts_full_view WHERE ACCESSION_NUMBER_REF='".$acc_num."'";
    
    $result = mysqli_query($conn, $sql_query)
                or die("Error: " . mysqli_error($conn));

    $table_str = "";

    $cnt = 0;
    if (mysqli_num_rows($result) > 0) {

            $table_str .= "Num_id,Accession Number,miRNA id,Modification type,Arm,Pattern,Position,Internal modification type, Internal pattern, Internal position, Doubled, Raw Counts";
 
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {
                
                $cnt = $cnt +1;

                $table_str .= "\n".$cnt.",".$row["ACCESSION_NUMBER_REF"].",".$row["MATURE_MIR_ID_REF"].",".$row["modification_type"].",".$row["arm"].",".$row["pattern"].",".$row["position"].",".$row["internal_modification_type"].",".$row["internal_pattern"].",".$row["internal_position"].",".$row["doubled"].",".$row["raw_counts"];
            
            }
            //echo $cnt;
            echo $table_str;
    } 

    mysqli_close($conn);
?> 
