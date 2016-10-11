<?php

    include 'db_connections.inc';
    $db_name = "miratlas_db";

    // Create connection
    $conn = mysqli_connect($db_host, $db_username, $db_pass, $db_name, $db_port);

    // Check connection
    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $sql_query = "SELECT ACCESSION_NUMBER, DATA_SOURCE FROM DATASETS";
    $result = mysqli_query($conn, $sql_query) 
        or die("Error: " . mysqli_error($conn));
    
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {        
            $data_sources_arr[$row["ACCESSION_NUMBER"]] = $row["DATA_SOURCE"];
        }
    }

?>
