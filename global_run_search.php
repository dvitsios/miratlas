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

                var dt = $('#global_name_results_table').dataTable( {
                        "dom": 'C<"clear">frtip',
                        "processing": true,
                        "scrollCollapse": true,
                        "iDisplayLength": 10,
                        "scrollY": 350,
                        "aLengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]],
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


                var dt = $('#global_description_results_table').dataTable( {
                        "dom": 'C<"clear">frtip',
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
                              
                              
                      $('table#individual_expres_tables').dataTable( {
                        "dom": 'C<"clear">lfrtip',
                        "processing": true,
                        "iDisplayLength": 10,
                        "aLengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]],
                        "caseInsensitive": true,
                        "searching": true,   
                        "deferRender": true,
                        "order": [[ 1, "desc" ]],
                        "bProcessing": false,
                        } );
                              
                    $(function() {
                        $( "#global_mir_accordion" ).accordion({
                            collapsible: true,
                            animate: 300,
                            heightStyle: 'content',
                            active: 0, 
                            });
                        });
                      
            } );

            function get_mircounts_file(){
                $.get("generate_csv_get_file.php");

            }
        </script>

    </head>

    <body>

    <div style=font-size:20px;">
    <b>Search results:</b><br/>
    </div>

<div id='run_search_div'>

<?php

include('mysql_connect.php');
include('hsv2rgb.php');

$global_search = $_GET['global_search'];

$global_search = trim($global_search);
$global_search = preg_replace('/[\\\<>;\/\[\]]/', '', $global_search);


if($global_search != ''){

    // ***** MIRNA GLOBAL SEARCH ******
    // check if the search entry is a miRNA
    $where = "WHERE MATURE_MIR_ID='".$global_search."'";

    $mir_sql_query = "SELECT MATURE_MIR_ID FROM MATURE_MIRNAS ".$where;

    $mir_result = mysqli_query($conn, $mir_sql_query)
        or die("Error: " . mysqli_error($conn));
    
    if (mysqli_num_rows($mir_result) > 0) {
        
        $cur_mir = $global_search;
        $mir_expr_thres = 0.01;
        
        $mxa_results_multiD_array = array();
        $cur_2D_array = array();
        
        $where = "WHERE MATURE_MIR_ID='$cur_mir' AND EXPR_RATIO_IN_DATASET>=$mir_expr_thres";
        //print $where;
        
        $sql_query = "SELECT ACCESSION_NUMBER, PLAIN_COUNTS, EXPR_RATIO_IN_DATASET FROM MIRCOUNTS_RAW_NO_MODS_TABLE ".$where;
        //echo "sql_query: $sql_query";
        
        $result = mysqli_query($conn, $sql_query)
        or die("Error: " . mysqli_error($conn));
        
        
        
        $acc_num_arr = array();
        $plain_counts_arr = array();
        $expr_ratios_arr = array();
        $descr_arr = array();
        
        $cur_table_str = "";
        
        
        if (mysqli_num_rows($result) > 0) {
            
            
            while($row = mysqli_fetch_assoc($result)) {
                
                $acc_num_arr[] = $row['ACCESSION_NUMBER'];
                $plain_counts_arr[] = $row['PLAIN_COUNTS'];
                $expr_ratios_arr[] = $row['EXPR_RATIO_IN_DATASET'];
                
                
                # get description for $row['ACCESSION_NUMBER']
                $descr_sql_query = "SELECT DESCRIPTION FROM DATASETS WHERE ACCESSION_NUMBER='".$row['ACCESSION_NUMBER']."'";
                
                $descr_result = mysqli_query($conn, $descr_sql_query)
                or die("Error: " . mysqli_error($conn));
                
                $cur_descr = '-';
                if (mysqli_num_rows($result) > 0) {
                    $descr_row = mysqli_fetch_assoc($descr_result);
                    $descr_arr[] = $descr_row["DESCRIPTION"];
                }
                
                
            }
            
            /*
             print_r($acc_num_arr);
             print_r($expr_ratios_arr);
             print_r($plain_counts_arr);
             print_r($descr_arr);
             */
            
            array_multisort($expr_ratios_arr, SORT_NUMERIC, SORT_DESC,  $acc_num_arr, $plain_counts_arr, $descr_arr);
            
            $cur_2D_array[] = array($acc_num_arr, $expr_ratios_arr, $plain_counts_arr, $descr_arr);
            //print_r($cur_2D_array);
            
            $mxa_results_multiD_array[$cur_mir] = $cur_2D_array;
            
            echo    "<div id='global_mir_accordion' style='font-size:14px; margin-top:10px'>";
	    echo    "<div class='adv_mod_search_category' style='width:98%'>";
            echo "<b>Individual miRNA results:</b>";
            echo    "</div>";
            
            echo "<div id='individual_mirna_results' style='background-color:#eff4f9; padding:20px'>";
            

            
            echo    "miRNA expression level colorbar<div id='mir_expr_colorbar'>
            </div>";
            echo "0.0<span style='padding-left:215px;'>1.0</span>";
            echo "<div style='margin-bottom: 20px;'></div>";
            
            // display expression ratios among all datasets for each given miRNA
            
            $cur_mir_results_arr = $mxa_results_multiD_array[$cur_mir][0];
            
            $cur_num_of_hits = count($cur_mir_results_arr[0]);
            
            
            
            $cur_table_str = "";
            
            
            $cur_table_str .= "<table id='individual_expres_tables' class='display' style='font-size:14px; text-align:center;'><thead>";
            $cur_table_str .= "<tr><th>Accession Number</th><th>Expression level</th><th>Expression ratio</th><th>Raw counts</th><th>Description</th></tr>";
            $cur_table_str .= "</thead><tbody>";
            
            
            /* ($cur_mir_results_arr) - 1st index value mapping:
             [0]: dataset name
             [1]: expression ratio
             [2]: plain counts
             [3]: description
             */
            
            for($hit_cnt=0; $hit_cnt<$cur_num_of_hits; $hit_cnt++){
                
                $dat_name = $cur_mir_results_arr[0][$hit_cnt];
                $expr_ratio = $cur_mir_results_arr[1][$hit_cnt];
                $plain_counts = $cur_mir_results_arr[2][$hit_cnt];
                $descr = $cur_mir_results_arr[3][$hit_cnt];
                
                
                $expr_lev_color = hsv2rgb(array(0.605, $expr_ratio, 1));
                
                $cur_table_str .= "<tr><td><a href=/enright-dev/miratlas/show_table.php?acc_num=".$dat_name.">".$dat_name."</a></td><td style='background-color:".$expr_lev_color.";'></td><td>".$expr_ratio."</td><td>".$plain_counts."</td><td style='text-align:left'>".$descr."</td></tr>";
            }
            
            
            $cur_table_str .= "</tbody></table>";
            //A9E2F3
            echo "<div style='font-size:16px; color: black; border:1px solid black; background-color:#f7f5f0; padding:8px; margin-bottom:5px;'><b>'$cur_mir'</b> expression:</div>";
            echo $cur_table_str;
            echo "<br/><br/><br/>";
 
            
            echo "</div></div>";
        }
        else{
            echo "No dataset was found expressing miRNA: <b>'$global_search'</b>";
        }
    

    } else{

        // MAIN ROUTINE FOR ** DATASET/ANNOTATION SEARCH **
        // max input str length to accept as input
        $max_input_str_length_allowed = 80;

        // Run dataset names search first:
        ?>
	<div class='adv_mod_search_category' style='width:98%'>
	Datasets with names similar to: <i><?php echo "'".$global_search."'"?></i>
        </div>

        <?php
        if(strlen($global_search) < $max_input_str_length_allowed){

            $where = "ACCESSION_NUMBER LIKE '%$global_search%'";
            
            $table_str = get_datasets_info($where, $conn, 'global_name_results_table');

            echo $table_str;

        } else{
            echo "<b>Error:</b>search parameter string is too long. Please try again using a shorter string.<br/>";
        }
        ?>

        <br/>
        <br/>
        <br/>

	<div class='adv_mod_search_category' style='width:98%'>
        Datasets with annotation related to: <i><?php echo "'".$global_search."'"?></i>
        </div>

        <?php
        // Run dataset names search first:
        if(strlen($global_search) < $max_input_str_length_allowed){

            $where = "DESCRIPTION LIKE '%$global_search%'";
            
            $table_str = get_datasets_info($where, $conn, 'global_description_results_table');

            echo $table_str;

        } else{
            echo "<b>Error:</b>search parameter string is too long. Please try again using a shorter string.<br/>";
        }

        mysqli_close($conn);
    }
} else{
    echo '<br/>No results found.<br/><br/>';
#    echo 'Please provide a valid search term (e.g. a dataset name or terms that may match with dataset annotations).<br/><br/>';
}


?>

</div>

<br/><br/><br/><br/><br/>
</body>
</html>


<!-- php routines -->
<?php

function get_datasets_info($where, $conn, $table_id){

        $sql_query = "SELECT ACCESSION_NUMBER, TAXON, DESCRIPTION FROM DATASETS WHERE ".$where;
        //echo "sql_query: $sql_query";

        $result = mysqli_query($conn, $sql_query)
            or die("Error: " . mysqli_error($conn));

        if (mysqli_num_rows($result) > 0) {
 
            $table_str .= "<table id='".$table_id."' class='display'><thead><tr><th>Accession Number</th><th>Organism</th><th>Description</th></tr></thead><tbody>";

            while($row = mysqli_fetch_assoc($result)) {

                $table_str .= "<tr><td>".$row["ACCESSION_NUMBER"]."</td><td>".$row["TAXON"]."</td><td>".$row["DESCRIPTION"]."</td></tr>";
            }
            $table_str .= "</tbody></table>";

            return($table_str); 
        } else{
            echo '<br/>No results found.<br/><br/>';
                #echo 'Please try again using different search criteria.<br/><br/>';
        }


}
?>
