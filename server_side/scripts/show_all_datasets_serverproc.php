<?php


// DB table to use
$table = 'DATASETS';
 
// Table's primary key
$primaryKey = 'ACCESSION_NUMBER';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'ACCESSION_NUMBER', 'dt' => 0 ),
    array( 'db' => 'TAXON',   'dt' => 1 ),
    array( 'db' => 'DESCRIPTION',     'dt' => 2 ),
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



echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns)
); 

?>
