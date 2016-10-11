<?php


        //session_start();
        $acc_num = $_GET['acc_num'];


/*
 * DataTables example server-side processing script.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
 
// DB table to use
$table = 'MIRCOUNTS_RAW_NO_MODS_TABLE';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'ACCESSION_NUMBER', 'dt' => 0 ),
    array( 'db' => 'MATURE_MIR_ID',  'dt' => 1 ),
    array( 'db' => 'PLAIN_COUNTS',   'dt' => 2 ),
    array( 'db' => 'EXPR_RATIO_IN_DATASET',     'dt' => 3 ),
);
 
// SQL server connection information
$sql_details = array(
    'user' => 'dvitsios',
    'pass' => 'appelstroop',
    'db'   => 'miratlas_db',
    'host' => 'mysql-enright-rnagen-prod',
    'port' => 4181
);
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */



require( 'ssp.class.php' );


$where = "ACCESSION_NUMBER = '$acc_num'";


echo json_encode(
    SSPCustom::simpleCustom( $_GET, $sql_details, $table, $primaryKey, $columns, $where )
);



?>
