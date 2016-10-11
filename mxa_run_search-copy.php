<html>
    <head>
        <meta charset="utf-8" />

        <link href="css/main.css" type="text/css" rel="stylesheet">
        <link href="./css/tablesorter.theme.default.css" rel="stylesheet">
        <link href="./css/jquery.dataTables.min.css" rel="stylesheet"> 
        <link href="./css/tablesorter_blue_style.css" rel="stylesheet"> 
        <link href="./assets/DataTables-1.10.4/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet">
        <link rel="stylesheet" href="./css/jquery-ui.css">
        
        <script type="text/javascript" src="./assets/jquery.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.dataTables.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.tablesorter.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.tablesorter.pager.js"></script> 
        <script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
        <script type="text/javascript" src="./assets/jquery-ui.js"></script> 

        <script>
            $(document).ready(function() {
                //should define that dynamically
                var org_taxIds = {
                    "Homo sapiens": 9606,
                    "Mus musculus":10090
                }; 


                $('table.display').dataTable( {
                        "dom": 'C<"clear">lfrtip',
                        "processing": true,
                        "iDisplayLength": 10,
                        "aLengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]],
                        "caseInsensitive": true,
                        "searching": true,   
                        "deferRender": true,
                        "order": [[ 1, "desc" ]],
                } );

                $(function() {
                    $( "#mxa_accordion" ).accordion({
                        collapsible: true,
                        animate: 300,
                        heightStyle: 'content',
                        active: 1, 
                    });
                });


            } );

            function get_mircounts_file(){
                $.get("generate_csv_get_file.php");

            }
        </script>

    </head>

    <body>

    <div style=font-size:18px;">
    <b>Search results:</b><br/>
    </div>

<div id='run_search_div'>

<?php

include('mysql_connect.php');

$mxa_search_arr = $_GET['mxa'];

end($mxa_search_arr);
$mxa_arr_end_key = key($mxa_search_arr);

foreach($mxa_search_arr as $key=>$val){

    if($key !== $mxa_arr_end_key){
        //$val = preg_replace('/\s+/', '', $val);
        $val = preg_replace('/[\\\<>;\/\[\]]/', '', $val);

        $mxa_search_arr[$key] = $val;
    }    
}

$mirs_to_search = $mxa_search_arr[0];
$mir_expr_thres = $mxa_search_arr[1]/100; // expressed in %
$sample_properties_to_search = $mxa_search_arr[2];
$dataset_names_to_search = $mxa_search_arr[3];
$organism_to_search = $mxa_search_arr[4];


//print_r($mxa_search_arr);


// search in which datasets a list of miRNAs is expressed higher than
// a default (e.g. top 10%) or user defined ratio
if($mirs_to_search !== ''){
 

    $mirna_ids_delimimters = '/[,\n\s]/';
    $mirs_arr = preg_split($mirna_ids_delimimters, $mirs_to_search);
    $mirs_arr = array_filter($mirs_arr);

echo    "<div id='mxa_accordion' style='font-size:14px;'>";

echo    "<h3><b>miRNAs co-expression results:</b></h3>
        <div id='mirna_coexpression_results'>";

        if(count($mirs_arr) <= 1){
            echo "None.<br/>";    
        } else{
// miRNA co-expression results


        }
    
echo    "</div><h3><b>Individual miRNA results:</b></h3>
        <div id='individual_mirna_results'>";

    // get expression ratios among all datasets for each given miRNA
    foreach($mirs_arr as $cur_mir){
        
        $where = "WHERE MATURE_MIR_ID='$cur_mir'";
        //print $where;

        $sql_query = "SELECT ACCESSION_NUMBER, PLAIN_COUNTS, EXPR_RATIO_IN_DATASET FROM MIRCOUNTS_RAW_NO_MODS_TABLE ".$where;
        //echo "sql_query: $sql_query";
        
        $result = mysqli_query($conn, $sql_query)
            or die("Error: " . mysqli_error($conn));


        $acc_num_arr = array();
        $plain_counts_arr = array();
        $expr_ratios_arr = array();

        $cur_table_str = "";

        if (mysqli_num_rows($result) > 0) {


            while($row = mysqli_fetch_assoc($result)) {

                $acc_num_arr[] = $row['ACCESSION_NUMBER'];
                $plain_counts_arr[] = $row['PLAIN_COUNTS'];
                $expr_ratios_arr[] = $row['EXPR_RATIO_IN_DATASET'];

            }


            
            //array_multisort($expr_ratios_arr, SORT_NUMERIC, SORT_DESC,  $acc_num_arr, $plain_counts_arr);

            $cur_table_str .= "<table id='aaa' class='display' style='font-size:12px; text-align:center;'><thead>";
            $cur_table_str .= "<tr><th>Accession Number</th><th>Expression ratio</th><th>Raw counts</th><th>Description</th></tr>";
            $cur_table_str .= "</thead><tbody>";

            foreach(array_keys($acc_num_arr) as $key){


                if( $expr_ratios_arr[$key] >= $mir_expr_thres){

                    # get description for cur_acc_num
                    $descr_sql_query = "SELECT DESCRIPTION FROM DATASETS WHERE ACCESSION_NUMBER='$acc_num_arr[$key]'";

                    $descr_result = mysqli_query($conn, $descr_sql_query)
                        or die("Error: " . mysqli_error($conn));

                    $cur_descr = '-';
                    if (mysqli_num_rows($result) > 0) {
                        $descr_row = mysqli_fetch_assoc($descr_result);
                        $cur_descr = $descr_row["DESCRIPTION"];
                    }

                    $cur_table_str .= "<tr><td><a href=/enright-dev/miratlas/show_table.php?acc_num=".$acc_num_arr[$key].">".$acc_num_arr[$key]."</a></td><td>".$expr_ratios_arr[$key]."</td><td>".$plain_counts_arr[$key]."</td><td style='text-align:left'>$cur_descr</td></tr>";
                } 
            }

            $cur_table_str .= "</tbody></table>";

            echo "<div style='font-size:16px; color:blue; background-color:#ECF8E0; padding:8px; margin-bottom:5px;'><b>'$cur_mir'</b> expression:</div>";
            echo $cur_table_str;
            echo "<br/><br/><br/>";

        }
    }   

    echo "</div></div>";
}

exit;


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

        $where = "ACCESSION_NUMBER LIKE '%$datasets_arr[0]%'";
        
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
<div class='search_result_table_title'> 
Datasets with names similar to: <i><?php echo "'".$datasets_to_search."'"?></i>
</div>
<br/>
<?php
        echo $table_str;

    } else{
        echo "<b>Error:</b>too many input datasets. Please try again with max. 50 input datasets<br/>";
    }

} elseif($SEARCH_TERMS_SET == true){

    $description_terms_arr = explode(",", $description_terms_to_search);
    $description_terms_arr = array_filter($description_terms_arr);

    if(count($description_terms_arr) < 10){
        $where = "DESCRIPTION LIKE '%".$description_terms_arr[0]."%'";
        
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
<div class='search_result_table_title'>
Datasets with annotation related to: <i><?php echo "'".$description_terms_to_search."'"?></i>
</div>
<br/>
<?php
        echo $table_str;
    } else{
        echo "Too many search terms. Please use a more defined set of search parameters.<br/>";
    }

} else{ //if($DATASET_NAME_SET == false && $SEARCH_TERMS_SET == false){
    echo '<b>No results found.</b><br/><br/>';
    echo 'Please provide a valid list of datasets or look for datasets based on a list of search terms.<br/><br/>';
}

mysqli_close($conn);

?>

</div>
</body>
</html>


<!-- php routines -->
<?php

function get_datasets_info($where, $conn){

        $sql_query = "SELECT ACCESSION_NUMBER, TAXON, DESCRIPTION FROM DATASETS WHERE ".$where;
        //echo "sql_query: $sql_query";

        $result = mysqli_query($conn, $sql_query)
            or die("Error: " . mysqli_error($conn));

        if (mysqli_num_rows($result) > 0) {
 
            $table_str .= "<table id='adv_search_res_table' class='display'><thead><tr><th>Accession Number</th><th>Organism</th><th>Description</th><th>Mircounts file</th></tr></thead><tbody>";

            while($row = mysqli_fetch_assoc($result)) {

                $table_str .= "<tr><td>".$row["ACCESSION_NUMBER"]."</td><td>".$row["TAXON"]."</td><td>".$row["DESCRIPTION"]."</td><td><a href=\"generate_csv_get_file.php?acc_num=".$row['ACCESSION_NUMBER']."\">".$row['ACCESSION_NUMBER']."-counts.txt</a></td></tr>";
            }
            $table_str .= "</tbody></table>";

            return($table_str); 
        } else{
            $table_str = "<b>No results found.</b><br/><br/>Please try again using different search criteria.<br/><br/>";
            return($table_str);
        }


}
?>
