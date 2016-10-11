<?php
 
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
$table = 'mircounts_full_view';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'ACCESSION_NUMBER_REF', 'dt' => 0 ),
    array( 'db' => 'MATURE_MIR_ID_REF',  'dt' => 1 ),
    array( 'db' => 'modification_type',   'dt' => 2 ),
    array( 'db' => 'arm',     'dt' => 3 ),
    array( 'db' => 'pattern',     'dt' => 4 ),
    array( 'db' => 'position',     'dt' => 5 ),
    array( 'db' => 'internal_modification_type',    'dt' => 6 ),
    array( 'db' => 'internal_pattern',     'dt' => 7 ),
    array( 'db' => 'internal_position',     'dt' => 8 ),
    array( 'db' => 'doubled',      'dt' => 9 ),
    array( 'db' => 'raw_counts',      'dt' => 10 ),
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


$where = "ACCESSION_NUMBER_REF = '$acc_num'";

echo json_encode(
    SSPCustom::simpleCustom( $_GET, $sql_details, $table, $primaryKey, $columns, $where )
);

#echo json_encode(
#    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $where)
#); 

?>
