<html>
    <head>
        <meta charset="utf-8" />

        <link href="./css/tablesorter.theme.default.css" rel="stylesheet">
        <link href="./css/jquery.dataTables.min.css" rel="stylesheet"> 

        
        <script type="text/javascript" src="./assets/jquery.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.dataTables.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.tablesorter.min.js"></script> 
    

    </head>

    <body>
    
<?php
    header('Content-Type: application/csv-tab-delimited-table');
    header('Content-Disposition: attachment');

    session_start();
    $acc_num = $_SESSION['acc_num'];


    include 'db_connections.inc';
        $db_name = "miratlas_db";

        // Create connection
        $conn = mysqli_connect($db_host, $db_username, $db_pass, $db_name);
    
        // Check connection
        if (mysqli_connect_errno()){
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }


    $sql_query = "SELECT ACCESSION_NUMBER_REF, MATURE_MIR_ID_REF, modification_type, arm, pattern, raw_counts FROM mircounts_full_view WHERE ACCESSION_NUMBER_REF='".$acc_num."'";
    

    $result = mysqli_query($conn, $sql_query)
                or die("Error: " . mysqli_error($conn));

    $table_str = "";

    if (mysqli_num_rows($result) > 0) {
            $table_str .= "<table id='myTable' class='tablesorter'><thead><tr><th>Accession Number</th><th>miRNA id</th><th>Modification type</th><th>Arm</th><th>Pattern</th><th>Counts</th></tr></thead><tbody>";
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {
                $table_str .= "<tr><td>".$row["ACCESSION_NUMBER_REF"]."</td><td>".$row["MATURE_MIR_ID_REF"]."</td><td>".$row["modification_type"]."</td><td>".$row["arm"]."</td><td>".$row["pattern"]."</td><td>".$row["raw_counts"]."</td></tr>";
            }
            $table_str .= "</tbody></table>";

            echo $table_str;
    } else {
        echo "'<b>$acc_num</b>': non valid dataset";
    }
    mysqli_close($conn);

?>
    
    </body>
</html>
