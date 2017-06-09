<html>


<head>
<meta charset="utf-8" />
<link href="css/main.css" type="text/css" rel="stylesheet">
<link href="./css/jquery.dataTables.min.css" rel="stylesheet">

<script type="text/javascript" src="./assets/jquery.min.js"></script>
<script type="text/javascript" src="./assets/jquery.dataTables.min.js"></script>


<script>

    $(document).ready(function() {
                      
                    $('#datasets_expression_table').dataTable( {
                        "dom": 'C<"clear">lfrtip',
                        "processing": true,
                        "iDisplayLength": 20,
                        "aLengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]],
                        "caseInsensitive": true,
                        "searching": true,
                        "deferRender": true,
                        "order": [[ 1,"asc" ]],
                        "bProcessing": false,
			"scrollY": false,
                        "scrollCollapse": true
                    } );
                      
      } );

</script>
<?php include_once("analyticstracking.php") ?>
</head>


<body id="full_page_div">

<?php
    include('mysql_connect.php');
    include('hsv2rgb.php');
?>

<div id="wrapper">
<div id="header">
<?php include 'header.php'; ?>
</div>


<div id='menu_bar'>
<?php include('menu_bar.php'); ?>
</div>



<?php
    
    session_start();
    //$datasets_str = $_SESSION['acc_num'];
    $datasets_str = $_GET['acc_num'];

    
    $mir_expr_thres_percent_val = $_POST['dataset_mir_expr_thres']; 
    $mir_expr_thres =  $mir_expr_thres_percent_val/100;
    
    $dataset_hits = array($datasets_str);
    
    
    
$top_mir_hits_multiD_arr = array();

$all_identified_mirnas_arr = array();


foreach($dataset_hits as $dataset){
    $cur_mir_hits_arr = get_top_mirs_for_a_dataset($dataset, $mir_expr_thres, $conn);
    
    $cur_mirna_keys = array_keys($cur_mir_hits_arr);
    
    
    $tmp_merged_arr = array_unique(array_merge($all_identified_mirnas_arr, $cur_mirna_keys));
    $all_identified_mirnas_arr = $tmp_merged_arr;

}

# fill in the missing mirnas with 0.0 expression values
foreach($dataset_hits as $dataset){
    
    $cur_mir_hits_arr = get_top_mirs_for_a_dataset($dataset, $mir_expr_thres, $conn);
    
    $cur_mirna_keys = array_keys($cur_mir_hits_arr);
    
    $tmp_diff_arr = array_diff($all_identified_mirnas_arr, $cur_mirna_keys);
    
    
    foreach($tmp_diff_arr as $missing_mir){
        $cur_mir_hits_arr[$missing_mir] = array(0,0);
    }
    
    
    $top_mir_hits_multiD_arr[$dataset] = $cur_mir_hits_arr;
    
}


# print global table of expression
$global_table_str = "";

$global_table_str .= "<table id='datasets_expression_table' class='display'><thead>";
$global_table_str .= "<tr><th>[miRNA ID]</th>";


foreach($dataset_hits as $dataset){
    $global_table_str .= "<th>".$dataset."</th>";
}
$global_table_str .= "</tr></thead><tbody>";

// sort mirnas based on their expression levels
$datasets_to_sort = array();
$mirs_to_sort = array();
$expr_ratios_to_sort = array();

foreach($dataset_hits as $dataset){
    foreach($all_identified_mirnas_arr as $cur_mir_entry){
        $datasets_to_sort[] = $dataset;
        $mirs_to_sort[] = $cur_mir_entry;
        $expr_ratios_to_sort[] = $top_mir_hits_multiD_arr[$dataset][$cur_mir_entry][0];
    }
}
    

array_multisort($expr_ratios_to_sort, SORT_NUMERIC, SORT_DESC, $datasets_to_sort, $mirs_to_sort);


foreach($all_identified_mirnas_arr as $cur_mir_entry){
    
    $global_table_str .= "<tr><td>".$cur_mir_entry."</td>";
    
    
    foreach($dataset_hits as $dataset){
        
        $cur_expr_ratio = $top_mir_hits_multiD_arr[$dataset][$cur_mir_entry][0];
        $expr_lev_color = hsv2rgb(array(0.605, $cur_expr_ratio, 1));
        
        
        $global_table_str .= "<td style='background-color:".$expr_lev_color.";'><span style='visibility:hidden;'>$expr_lev_color</span></td>";
    }
    
    $global_table_str .= "</tr>";
}


$global_table_str .= "</tbody></table>";

echo "<div style='background-color:#f4f3ed; font-size:18px; padding:10px; margin-top:40px; font-weight: bold; border: 1px solid black'>
    miRNA expression profiler for <a href='/enright-dev/miratlas/show_table.php?acc_num=$datasets_str'>$datasets_str</a> 
    </div>";
$mir_expr_ratio = $mir_expr_thres_percent_val/100;
echo "<div style='background-color:#f7f7f7; padding:8px'><b>expression threshold</b>: $mir_expr_ratio</div>";
echo "<br/><br/>";


echo    "miRNA expression level colorbar<div id='mir_expr_colorbar'>
</div>";
echo "0.0<span style='padding-left:210px;'>1.0</span>";
echo "<div style='margin-bottom: 20px;'></div>";

echo    "<div class='adv_mod_search_category' style='width:50%; background-color:#eff4f9;'>";
echo	"Most highly expressed miRNAs<br/><hr/>";

echo    "<div id='mirna_coexpression_results' style=' padding:0px'>";
echo $global_table_str;
echo	"</div>";
echo	"</div>";

?>


<br/>


<br/>
<br/>

<?php include('footer.php') ?>
<?php
    if($_POST){
        //session_start(); // must be called before data is sent
        //$_SESSION['acc_num'] = $_POST['acc_num'];
        
        
        //include('show_table.php');
    }
    ?>
</div>

</body>
</html>

<?php
    
    function get_top_mirs_for_a_dataset($dataset, $expression_thres, $conn){
        
        $where = "WHERE ACCESSION_NUMBER='".$dataset."' AND EXPR_RATIO_IN_DATASET>=".$expression_thres;
        
        $sql_query = "SELECT MATURE_MIR_ID, PLAIN_COUNTS, EXPR_RATIO_IN_DATASET FROM MIRCOUNTS_RAW_NO_MODS_TABLE ".$where;
        
        
        $result = mysqli_query($conn, $sql_query)
        or die("Error: " . mysqli_error($conn));
        
        $mir_hits_multiD_arr = array();
        
        $mir_id_hits = array();
        $expr_ratio_hits = array();
        $plain_counts_hits = array();
        
        if (mysqli_num_rows($result) > 0) {
            
            while($row = mysqli_fetch_assoc($result)) {
                
                $cur_hit = array();
                
                //$cur_hit[$row["MATURE_MIR_ID"]] = array($row["EXPR_RATIO_IN_DATASET"], $row["PLAIN_COUNTS"]);
                
                //$mir_hits[] = $row["MATURE_MIR_ID"];
                //$expr_ratio_hits[] = $row["EXPR_RATIO_IN_DATASET"];
                //$plain_counts_hits[] = $row["PLAIN_COUNTS"];
                
                $mir_hits_multiD_arr[$row["MATURE_MIR_ID"]] = array($row["EXPR_RATIO_IN_DATASET"], $row["PLAIN_COUNTS"]);
                
            }
            
            //$mir_hits_multiD_arr[] = array($mir_hits, $expr_ratio_hits, $plain_counts_hits);
            
            return($mir_hits_multiD_arr); 
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
