<?php
    //session_start();
    //$acc_num = $_SESSION['acc_num'];
   
   $acc_num = $_GET['acc_num'];
   $acc_num = preg_replace("/draw=1/", '', $acc_num);
   $acc_num = substr_replace($acc_num, "", -1);
   
    header('Content-Type: application/csv-tab-delimited-table');
    header("Content-Disposition: attachment; filename=$acc_num-template_counts.txt");


    include 'db_connections.inc';
    $db_name = "miratlas_db";

    // Create connection
    $conn = mysqli_connect($db_host, $db_username, $db_pass, $db_name, $db_port);

    // Check connection
   if (mysqli_connect_errno())
   {
	   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }


    $sql_query = "SELECT ACCESSION_NUMBER, MATURE_MIR_ID, PLAIN_COUNTS, EXPR_RATIO_IN_DATASET FROM MIRCOUNTS_RAW_NO_MODS_TABLE WHERE ACCESSION_NUMBER='".$acc_num."'";
    
    $result = mysqli_query($conn, $sql_query)
                or die("Error: " . mysqli_error($conn));

    $table_str = "";

    $cnt = 0;
    if (mysqli_num_rows($result) > 0) {

            $table_str .= "Num_id,Accession Number, Template Counts, Expression ratios";
 
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {
                
                $cnt = $cnt +1;

                $table_str .= "\n".$cnt.",".$row["ACCESSION_NUMBER"].",".$row["MATURE_MIR_ID"].",".$row["PLAIN_COUNTS"].",".$row["EXPR_RATIO_IN_DATASET"];
            
            }
            echo $table_str;
    } 

    mysqli_close($conn);
?> 
