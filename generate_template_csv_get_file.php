<?php
    session_start();
    $acc_num = $_GET['acc_num'];
    
    //header("Content-type: text/plain");
    header('Content-Description: File Transfer');
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$acc_num-template_counts.txt");
    header('Content-Transfer-Encoding: binary');
    header('Connection: Keep-Alive');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');



    include 'db_connections.inc';
    include 'mysql_connect.php';

    //$db_name = "miratlas_db";



    
    $sql_query = "SELECT ACCESSION_NUMBER, MATURE_MIR_ID, PLAIN_COUNTS, EXPR_RATIO_IN_DATASET FROM MIRCOUNTS_RAW_NO_MODS_TABLE WHERE ACCESSION_NUMBER='".$acc_num."'";


    $result = mysqli_query($conn, $sql_query)
                or die("Error: " . mysqli_error($conn));

    $table_str = "";

    $cnt = 0;
    if (mysqli_num_rows($result) > 0) {

            $table_str .= "Num_id,Accession Number,miRNA id,Template Counts, Expression ratios";
 
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {
                
                $cnt = $cnt +1;

                $table_str .= "\n".$cnt.",".$row["ACCESSION_NUMBER"].",".$row["MATURE_MIR_ID"].",".$row["PLAIN_COUNTS"].",".$row["EXPR_RATIO_IN_DATASET"];

            
            }
            //echo $cnt;
            echo $table_str;
    } 

    mysqli_close($conn);
?> 
