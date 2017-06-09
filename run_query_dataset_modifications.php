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
              
              
              
              
              $('#datasets_profile_table').dataTable( {
                                                        "dom": 'C<"clear">lfrtip',
                                                        "processing": true,
                                                        "iDisplayLength": 1000,
                                                        "aLengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]],
                                                        "caseInsensitive": true,
                                                        "searching": true,
                                                        "deferRender": true,
                                                        "order": [[ 1,"desc" ]],
                                                        "bProcessing": false,
                                                        "scrollX": true,
                                                        } );
              
                  
              
              $(function() {
                $( "#dmp_accordion" ).accordion({
                                                collapsible: true,
                                                animate: 300,
                                                heightStyle: 'content',
                                                active: 0,
                                                });
                });
              
              
              $(function() {
                $( "#dmp_accordion2" ).accordion({
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
<?php include_once("analyticstracking.php") ?>
</head>

<body>

<div style=font-size:18px;">
<b>Search results:</b><br/>
</div>
<br/>
<div class='adv_mod_search_category' style='width:98%'">
Modifications prevalence distribution for the selected datasets
</div>

<div id='run_search_div' style='padding-top:0px; margin-top:0px'>

<?php

include('mysql_connect.php');
include('hsv2rgb.php');

$dmp_search_arr = $_GET['dmp'];


    
end($dmp_search_arr);
$dmp_arr_end_key = key($dmp_search_arr);

foreach($dmp_search_arr as $key=>$val){
    
    if($key !== $dmp_arr_end_key){
        //$val = preg_replace('/\s+/', '', $val);
        $val = trim($val);
        $val = preg_replace('/[()\\\<>;\/\[\]]/', '', $val);
        
        $dmp_search_arr[$key] = $val;
    }
}

$dataset_names_to_search = $dmp_search_arr[0];
$sample_properties_to_search = $dmp_search_arr[1];
$organism_to_search = $dmp_search_arr[2];


//print_r($dmp_search_arr);


// search in which datasets a list of miRNAs is expressed higher than
// a default (e.g. > 1%) or user defined ratio

$dataset_hits = array();
    
if($dataset_names_to_search != ''){
    
    $dataset_names_delimimters = '/[,\n\s]/';
    $dataset_hits = preg_split($dataset_names_delimimters, $dataset_names_to_search);
    
    $dataset_hits = array_filter($dataset_hits);
    $dataset_hits = array_unique($dataset_hits);
    
    /*
    $dataset_mod_stats["ALL_COUNTS"] = $row["ALL_COUNTS"];
    $dataset_mod_stats["MODIFIED_MIRS_UNIQUE_COUNTS"] = $row["MODIFIED_MIRS_UNIQUE_COUNTS"];
    $dataset_mod_stats["ALL_NONT_IN_3P_COUNTS"] = $row["ALL_NONT_IN_3P_COUNTS"];
    $dataset_mod_stats["ALL_NONT_IN_5P_COUNTS"] = $row["ALL_NONT_IN_5P_COUNTS"];
    $dataset_mod_stats["MODIFIED_MIR_COUNTS_3P"] = $row["MODIFIED_MIR_COUNTS_3P"];
    $dataset_mod_stats["MODIFIED_MIR_COUNTS_5P"] = $row["MODIFIED_MIR_COUNTS_5P"];
    $dataset_mod_stats["SNP_COUNTS_3P"] = $row["SNP_COUNTS_3P"];
    $dataset_mod_stats["SNP_COUNTS_5P"] = $row["SNP_COUNTS_5P"];
    $dataset_mod_stats["ADAR_COUNTS_3P"] = $row["ADAR_COUNTS_3P"];
    $dataset_mod_stats["ADAR_COUNTS_5P"] = $row["ADAR_COUNTS_5P"];
    $dataset_mod_stats["INTERNAL_SNP_COUNTS"] = $row["INTERNAL_SNP_COUNTS"];
    $dataset_mod_stats["INTERNAL_ADAR_COUNTS"] = $row["INTERNAL_ADAR_COUNTS"];
    */
    
}
else if($sample_properties_to_search != ''){
    
    $descr_terms_delimimters = '/[,\n\s]/';
    $description_terms_arr = preg_split($descr_terms_delimimters, $sample_properties_to_search);
    
    
    $description_terms_arr = array_filter($description_terms_arr);
    $description_terms_arr = array_unique($description_terms_arr);
    
    if(count($description_terms_arr) < 10){
        $where = "WHERE (DESCRIPTION LIKE '%".$description_terms_arr[0]."%'";
        
        if(count($description_terms_arr) > 1){
            foreach($description_terms_arr as $descr_term){
                $where .= " OR DESCRIPTION LIKE '%".$descr_term."%'";
            }
        }
        $where .= ")";
        
        if($organism_to_search !== 'All'){
            $where .= " AND TAXON='".$organism_to_search."'";
        }
        
        
        $dataset_hits = get_dataset_hits($where, $conn);
    }
} else if($dataset_names_to_search === '' & $sample_properties_to_search === ''){
    
    if($organism_to_search !== 'All'){
        $where .= "WHERE TAXON='".$organism_to_search."'";
    }
    
    $sql_query = "SELECT ACCESSION_NUMBER FROM DATASETS ".$where;
    //echo "sql_query: $sql_query";
    
    $result = mysqli_query($conn, $sql_query)
    or die("Error: " . mysqli_error($conn));
    
    $dataset_hits = array();
    
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            
            $dataset_hits[] = $row["ACCESSION_NUMBER"];
        }
    }
}
    
    
    
    
    
    
    
    
        
$all_dataset_stats = array();

foreach($dataset_hits as $dataset){

$where = "WHERE ACCESSION_NUMBER='".$dataset."'";


$dataset_mod_stats = get_mod_stats_for_dataset($where, $conn);


//$all_dataset_stats[$dataset]["nont_ratio"] = $dataset_mod_stats["MODIFIED_MIRS_UNIQUE_COUNTS"]/$dataset_mod_stats["ALL_COUNTS"];

$all_dataset_stats[$dataset]["nont_3p_ratio"] = $dataset_mod_stats["ALL_NONT_IN_3P_COUNTS"]/$dataset_mod_stats["ALL_COUNTS"];
$all_dataset_stats[$dataset]["nont_5p_ratio"] = $dataset_mod_stats["ALL_NONT_IN_5P_COUNTS"]/$dataset_mod_stats["ALL_COUNTS"];

$all_dataset_stats[$dataset]["mod_3p_ratio"] = $dataset_mod_stats["MODIFIED_MIR_COUNTS_3P"]/$dataset_mod_stats["ALL_NONT_IN_3P_COUNTS"];
$all_dataset_stats[$dataset]["adar_3p_ratio"] = $dataset_mod_stats["ADAR_COUNTS_3P"]/$dataset_mod_stats["ALL_NONT_IN_3P_COUNTS"];
$all_dataset_stats[$dataset]["snp_3p_ratio"] = $dataset_mod_stats["SNP_COUNTS_3P"]/$dataset_mod_stats["ALL_NONT_IN_3P_COUNTS"];

$all_dataset_stats[$dataset]["mod_5p_ratio"] = $dataset_mod_stats["MODIFIED_MIR_COUNTS_5P"]/$dataset_mod_stats["ALL_NONT_IN_5P_COUNTS"];
$all_dataset_stats[$dataset]["adar_5p_ratio"] = $dataset_mod_stats["ADAR_COUNTS_5P"]/$dataset_mod_stats["ALL_NONT_IN_5P_COUNTS"];
$all_dataset_stats[$dataset]["snp_5p_ratio"] = $dataset_mod_stats["SNP_COUNTS_5P"]/$dataset_mod_stats["ALL_NONT_IN_5P_COUNTS"];

$all_dataset_stats[$dataset]["internal_adar_ratio"] = $dataset_mod_stats["INTERNAL_ADAR_COUNTS"]/$dataset_mod_stats["ALL_COUNTS"];
$all_dataset_stats[$dataset]["internal_snp_ratio"] = $dataset_mod_stats["INTERNAL_SNP_COUNTS"]/$dataset_mod_stats["ALL_COUNTS"];



}


$datasets_profile_table_columns = array('5p all mods', '5p mod', '5p ADAR', '5p SNP', 'Internal ADAR', 'Internal SNP', '3p SNP', '3p ADAR', '3p mod', '3p all mods');


    
# print global table of expression
$global_table_str = "";

$global_table_str .= "<table id='datasets_profile_table' class='display' style='font-size:14px'><thead>";
$global_table_str .= "<tr style='font-size:13px'><th>[Accession Number]</th>";


foreach($datasets_profile_table_columns as $column){
$global_table_str .= "<th>".$column."</th>";
}
$global_table_str .= "</tr></thead><tbody>";


// sort mirnas based on their expression levels
$datasets_to_sort = array();
$mirs_to_sort = array();
$expr_ratios_to_sort = array();


    $nont_color_hue = 0.556;
    $nont_3p_color_hue = 0.222;
    $nont_5p_color_hue = 0.096;
    $internal_mor_color_hue = 0.014;
    
    
foreach($dataset_hits as $dataset){

$global_table_str .= "<tr><td><a href=/enright-dev/miratlas/show_table.php?acc_num=".$dataset.">".$dataset."</a></td>";

//$tmp_lev_color = hsv2rgb(array(0.6667, $all_dataset_stats[$dataset]["nont_ratio"], 1));
//$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_lev_color</span></td>";

$num_of_decimal_points_to_keep = 4;

    

    
$tmp_level_val = $all_dataset_stats[$dataset]["nont_5p_ratio"];
$tmp_lev_color = hsv2rgb(array($nont_color_hue, $tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";

$tmp_level_val = $all_dataset_stats[$dataset]["mod_5p_ratio"];
$tmp_lev_color = hsv2rgb(array($nont_5p_color_hue, $tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";

$tmp_level_val = $all_dataset_stats[$dataset]["adar_5p_ratio"];
$tmp_lev_color = hsv2rgb(array($nont_5p_color_hue, $tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";

$tmp_level_val = $all_dataset_stats[$dataset]["snp_5p_ratio"];
$tmp_lev_color = hsv2rgb(array($nont_5p_color_hue, $tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";
    
#-------------------
    
$tmp_level_val = $all_dataset_stats[$dataset]["internal_adar_ratio"];
$tmp_lev_color = hsv2rgb(array($internal_mor_color_hue, $tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";
    
$tmp_level_val = $all_dataset_stats[$dataset]["internal_snp_ratio"];
$tmp_lev_color = hsv2rgb(array($internal_mor_color_hue, $tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";

#------------------- 
    
$tmp_level_val = $all_dataset_stats[$dataset]["snp_3p_ratio"];
$tmp_lev_color = hsv2rgb(array($nont_3p_color_hue, 0.8*$tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";

$tmp_level_val = $all_dataset_stats[$dataset]["adar_3p_ratio"];
$tmp_lev_color = hsv2rgb(array($nont_3p_color_hue, 0.8*$tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";

$tmp_level_val = $all_dataset_stats[$dataset]["mod_3p_ratio"];
$tmp_lev_color = hsv2rgb(array($nont_3p_color_hue, 0.8*$tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";

$tmp_level_val = $all_dataset_stats[$dataset]["nont_3p_ratio"];
$tmp_lev_color = hsv2rgb(array($nont_color_hue, $tmp_level_val, 1));
$tmp_level_val = round($tmp_level_val, $num_of_decimal_points_to_keep);
$global_table_str .= "<td style='background-color:".$tmp_lev_color.";'><span >$tmp_level_val</span></td>";


$global_table_str .= "</tr>";
/*foreach($all_identified_mirnas_arr as $cur_mir_entry){
 $datasets_to_sort[] = $dataset;
 $mirs_to_sort[] = $cur_mir_entry;
 $expr_ratios_to_sort[] = $top_mir_hits_multiD_arr[$dataset][$cur_mir_entry][0];
 }*/
}

array_multisort($expr_ratios_to_sort, SORT_NUMERIC, SORT_DESC, $datasets_to_sort, $mirs_to_sort);



foreach($all_identified_mirnas_arr as $cur_mir_entry){

$global_table_str .= "<tr><td>".$cur_mir_entry."</td>";

foreach($dataset_hits as $dataset){

$cur_expr_ratio = $top_mir_hits_multiD_arr[$dataset][$cur_mir_entry][0];
$expr_lev_color = hsv2rgb(array(0.605, $cur_expr_ratio, 1));


$global_table_str .= "<td style='background-color:".$expr_lev_color.";'><span >$expr_lev_color</span></td>";
}

$global_table_str .= "</tr>";
}


$global_table_str .= "</tbody></table>";


echo "<div id='individual_mirna_results' style='background-color:#f3f3f3; padding:20px'>";
echo $global_table_str;
    
  
echo "<div style='margin-bottom:450px'>";
echo "<table style='background-color:#E6E6E6; padding:15px; border:1px solid; float:left; margin-bottom:20px; text-align:center; width:43%; margin-top:50px; font-size:14px; background-color:#fafafa'>";
   
    echo "<tr style='text-align:center; font-size:18px'>";
    echo "<td><b>Colorbars Legend</b></td>";
    echo "</tr>";

    echo "<tr style='text-align:left'>";
    echo "<td style='padding-bottom: 1em;'>";
    echo    "Modifications ratio in each arm (compared to overall expression)<div class='mods_ratio_colorbar' id='arm_mods_ratio_colorbar'></div>";
    echo "0.0<span style='padding-left:410px;'>1.0</span>";
    echo "</td>";
  echo "</tr>";

    
    echo "<tr style='text-align:left'>";
    echo "<td style='padding-bottom: 1em;'>";
    echo    "Modification types distribution in <b>5p arm</b><div class='mods_ratio_colorbar' id='arm_5p_mods_ratio_colorbar'></div>";
    echo "0.0<span style='padding-left:410px;'>1.0</span>";
    echo "</td>";
  echo "</tr>";
    
    
  echo "<tr style='text-align:left'>";
    echo "<td style='padding-bottom: 1em;'>";
    echo    "Internal modifications ratio (compared to overall expression)<div class='mods_ratio_colorbar' id='internal_mods_ratio_colorbar'></div>";
    echo "0.0<span style='padding-left:410px;'>1.0</span>";
    echo "</td>";
  echo "</tr>";

  echo "<tr style='text-align:left'>";
    echo "<td>";
    echo    "Modification types distribution in <b>3p arm</b><div class='mods_ratio_colorbar' id='arm_3p_mods_ratio_colorbar'></div>";
    echo "0.0<span style='padding-left:410px;'>1.0</span>";
    echo "</td>";
  echo "</tr>";
    
echo "</table>";
echo "</div>";
echo "</div>";

mysqli_close($conn);

?>

</div>
</body>
</html>


<!-- php routines -->
<?php

    function get_mod_stats_for_dataset($where, $conn){
        
        $sql_query = "SELECT ALL_COUNTS, MODIFIED_MIRS_UNIQUE_COUNTS, ALL_NONT_IN_3P_COUNTS, ALL_NONT_IN_5P_COUNTS, MODIFIED_MIR_COUNTS_3P, MODIFIED_MIR_COUNTS_5P, SNP_COUNTS_3P, SNP_COUNTS_5P, ADAR_COUNTS_3P, ADAR_COUNTS_5P, INTERNAL_SNP_COUNTS, INTERNAL_ADAR_COUNTS FROM DATASET_MOD_BASIC_STATS ".$where;
        //echo "sql_query: $sql_query";
        
        $result = mysqli_query($conn, $sql_query)
        or die("Error: " . mysqli_error($conn));
        
        $dataset_mod_stats = array();
        
        if (mysqli_num_rows($result) > 0) {
            
            while($row = mysqli_fetch_assoc($result)) {
                
                $dataset_mod_stats["ALL_COUNTS"] = $row["ALL_COUNTS"];
                $dataset_mod_stats["MODIFIED_MIRS_UNIQUE_COUNTS"] = $row["MODIFIED_MIRS_UNIQUE_COUNTS"];
                $dataset_mod_stats["ALL_NONT_IN_3P_COUNTS"] = $row["ALL_NONT_IN_3P_COUNTS"];
                $dataset_mod_stats["ALL_NONT_IN_5P_COUNTS"] = $row["ALL_NONT_IN_5P_COUNTS"];
                $dataset_mod_stats["MODIFIED_MIR_COUNTS_3P"] = $row["MODIFIED_MIR_COUNTS_3P"];
                $dataset_mod_stats["MODIFIED_MIR_COUNTS_5P"] = $row["MODIFIED_MIR_COUNTS_5P"];
                $dataset_mod_stats["SNP_COUNTS_3P"] = $row["SNP_COUNTS_3P"];
                $dataset_mod_stats["SNP_COUNTS_5P"] = $row["SNP_COUNTS_5P"];
                $dataset_mod_stats["ADAR_COUNTS_3P"] = $row["ADAR_COUNTS_3P"];
                $dataset_mod_stats["ADAR_COUNTS_5P"] = $row["ADAR_COUNTS_5P"];
                $dataset_mod_stats["INTERNAL_SNP_COUNTS"] = $row["INTERNAL_SNP_COUNTS"];
                $dataset_mod_stats["INTERNAL_ADAR_COUNTS"] = $row["INTERNAL_ADAR_COUNTS"];
            }
            
            return($dataset_mod_stats);
        } else{
            echo "<b>No results found.</b><br/><br/>Please try again using different search criteria.<br/><br/>";
        }

    
    }


    function get_dataset_hits($where, $conn){
        
        $sql_query = "SELECT ACCESSION_NUMBER FROM DATASETS ".$where;
        //echo "sql_query: $sql_query";
        
        $result = mysqli_query($conn, $sql_query)
        or die("Error: " . mysqli_error($conn));
        
        $dataset_hits = array();
        
        if (mysqli_num_rows($result) > 0) {
            
            while($row = mysqli_fetch_assoc($result)) {
                
                $dataset_hits[] = $row["ACCESSION_NUMBER"];
            }
            
            return($dataset_hits); 
        } else{
            echo "<b>No results found.</b><br/><br/>Please try again using different search criteria.<br/><br/>";
        }
        
        
    }
?>

































