<html>
    <head>
        <meta charset="utf-8" />

        <link href="./css/tablesorter.theme.default.css" rel="stylesheet">
        <link href="./css/jquery.dataTables.min.css" rel="stylesheet"> 
        <link href="./assets/DataTables-1.10.4/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet">

        
        <script type="text/javascript" src="./assets/jquery.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.dataTables.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.tablesorter.min.js"></script> 
        <script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/TableTools/js/dataTables.tableTools.min.js"></script>

        <script>
            $(document).ready(function() {
                //should define that dynamically
                var org_taxIds = {
                    "Homo sapiens": 9606,
                    "Mus musculus":10090
                }; 

                var dt = $('#adv_search_res_table').dataTable( {
                        "dom": 'C<"clear">lfrtip',
                        "processing": true,
                        "scrollCollapse": true,
                        "iDisplayLength": 20,
                        "scrollY": 700,
                        "aLengthMenu": [[20, 40, 60, -1], [20, 40, 60, "All"]],
                        "caseInsensitive": true,
                        "searching": true,   
                        "deferRender": true,
                        "order": [[ 1, "asc" ]],
                        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                            //$('td:eq(1)', nRow).html('<a href="/enright-dev/miratlas/show_table.php">asdf</a>');
                            //'<a href="/enright-dev/miratlas/show_table.php?acc_num='.'PRJNA190003'.'">temp</   a>'
                            $('td:eq(0)', nRow).html('<a href="/enright-dev/miratlas/show_table.php?acc_num='+aData[0]+'">'+aData[0]+'</a>');
                            $('td:eq(1)', nRow).html('<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id='+org_taxIds[aData[1]]+'">'+aData[1]+'</a>');
                            return nRow;
                        }
                } );

            } );

            function get_mircounts_file(){
                $.get("generate_csv_get_file.php");

            }
        </script>

	<?php include_once("analyticstracking.php") ?>
    </head>

    <body>

    <div style=font-size:18px;">
    <b>Search results:</b><br/>
    </div>

<div id='run_search_div'>

<?php

include('mysql_connect.php');

$adv_search_arr = $_POST['advanced_search'];

foreach($adv_search_arr as $key=>$val){

    if($key !==2){
        $val = preg_replace('/\s+/', '', $val);
        $val = preg_replace('/[\\\<>;\/\[\]]/', '', $val);

        $adv_search_arr[$key] = $val;
    }    
}

$datasets_to_search = $adv_search_arr[0];
$description_terms_to_search = $adv_search_arr[1];
$organism_to_search = $adv_search_arr[2];



$DATASET_NAME_SET = true;
if($datasets_to_search === ''){ 
    $DATASET_NAME_SET = false;
} 

$SEARCH_TERMS_SET = false;
if($description_terms_to_search != ''){
    $SEARCH_TERMS_SET = true;
}




// MAIN ROUTINE
if($DATASET_NAME_SET == true && $SEARCH_TERMS_SET == false){

    $datasets_arr = explode(",", $datasets_to_search);
    $datasets_arr = array_filter($datasets_arr);

    // max datasets to accept as input
    $max_input_datasets_allowed = 50;
    if(count($datasets_arr) < $max_input_datasets_allowed){

        $where = "WHERE ACCESSION_NUMBER LIKE '%$datasets_arr[0]%'";
        
        if(count($datasets_arr) > 1){
            for($x=1; $x<count($datasets_arr); $x++){
                 $where .= " OR ACCESSION_NUMBER LIKE '%$datasets_arr[$x]%'";
            }
        } 

        if($organism_to_search !== 'All'){
            $where .= " AND TAXON='".$organism_to_search."'";
        }


        $table_str = get_datasets_info($where, $conn);
?>

<div class='adv_mod_search_category' style='width:98%'>
<?php $datasets_to_search= str_replace(",", " | ", $datasets_to_search); ?>
<b>Datasets with names similar to: </b><i><?php echo "'".$datasets_to_search."'"?></i>
</div>


<div id='mirna_coexpression_results' style='background-color:#eff4f9; padding:20px'>

<br/>
<?php
        echo $table_str;
	echo "</div>";

    } else{
        echo "<b>Error:</b>too many input datasets. Please try again with max. 50 input datasets<br/>";
    }

} elseif($SEARCH_TERMS_SET == true){

    $description_terms_arr = explode(",", $description_terms_to_search);
    $description_terms_arr = array_filter($description_terms_arr);

    if(count($description_terms_arr) < 10){
        $where = "WHERE DESCRIPTION LIKE '%".$description_terms_arr[0]."%'";
        
        if(count($description_terms_arr) > 1){
             for($x=1; $x<count($description_terms_arr); $x++){
                $where .= " OR DESCRIPTION LIKE '%".$description_terms_arr[$x]."%'";
             }
        }


        if($organism_to_search !== 'All'){
            $where .= " AND TAXON='".$organism_to_search."'";
        }

        $table_str = get_datasets_info($where, $conn);
?>
<div class='adv_mod_search_category' style='width:98%'>
<?php $description_terms_to_search = str_replace(",", " | ", $description_terms_to_search); ?>
Datasets with annotation related to: <i><?php echo "'".$description_terms_to_search."'"?></i>
</div>


<div id='mirna_coexpression_results' style='background-color:#eff4f9; padding:20px'>

<br/>
<?php
        echo $table_str;
	echo "</div>";
    } else{
        echo "Too many search terms. Please use a more defined set of search parameters.<br/>";
    }

} else if($DATASET_NAME_SET == false && $SEARCH_TERMS_SET == false){

    $where = '';

    if($organism_to_search !== 'All'){
        $where = "WHERE TAXON='".$organism_to_search."'";;
    }

    $table_str = get_datasets_info($where, $conn);

    ?>
<div class='adv_mod_search_category' style='width:98%'>
All datasets with selected organism: <i><?php echo "'".$organism_to_search."'"?></i>
</div>
<br/>
<div style='background-color:#eff4f9; padding:20px'>
<?php
           echo $table_str;

} else{ 
    echo '<b>No results found.</b><br/><br/>';
    echo 'Please provide a valid list of datasets or look for datasets based on a list of search terms.<br/><br/>';
}

mysqli_close($conn);

?>
</div>

</div>
</body>
</html>


<!-- php routines -->
<?php

function get_datasets_info($where, $conn){

	$table_str = '';

        $sql_query = "SELECT ACCESSION_NUMBER, TAXON, DESCRIPTION FROM DATASETS ".$where;
        //echo "sql_query: $sql_query";

        $result = mysqli_query($conn, $sql_query)
            or die("Error: " . mysqli_error($conn));

        if (mysqli_num_rows($result) > 0) {

// <th>Mircounts file</th>
            $table_str .= "<table id='adv_search_res_table' class='display'><thead><tr><th>Accession Number</th><th>Organism</th><th>Description</th></tr></thead><tbody>";

            while($row = mysqli_fetch_assoc($result)) {

// extra column with a link to download the Mircounts file
//<td><a href=\"generate_csv_get_file.php?acc_num=".$row['ACCESSION_NUMBER']."\">".$row['ACCESSION_NUMBER']."-template_counts.txt</a></td>

                $table_str .= "<tr><td>".$row["ACCESSION_NUMBER"]."</td><td>".$row["TAXON"]."</td><td style='padding-left:30px;'>".$row["DESCRIPTION"]."</td></tr>";
            }
            $table_str .= "</tbody></table>";

            return($table_str); 
        } else{
            $table_str = "<b>No results found.</b><br/><br/>Please try again using different search criteria.<br/><br/>";
            return($table_str);
        }


}
?>
