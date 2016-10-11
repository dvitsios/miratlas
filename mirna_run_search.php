<html>
<head>
<meta charset="utf-8" />

<link href="css/main.css" type="text/css" rel="stylesheet">
<link href="./css/tablesorter.theme.default.css" rel="stylesheet">
<link href="./css/jquery.dataTables.min.css" rel="stylesheet">
<link href="./css/tablesorter_blue_style.css" rel="stylesheet">
<link href="./assets/DataTables-1.10.4/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet">
<link rel="stylesheet" href="./assets/jquery_ui/jquery-ui.css">
<link rel="stylesheet" href="./assets/jquery_ui/jquery-ui.theme.css">

<script type="text/javascript" src="./assets/jquery.min.js"></script>
<script type="text/javascript" src="./assets/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="./assets/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="./assets/jquery.tablesorter.pager.js"></script>
<script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="./assets/jquery_ui/jquery-ui.js"></script>

<script>
            $(document).ready(function() {
                  //should define that dynamically
                  var org_taxIds = {
                  "Homo sapiens": 9606,
                  "Mus musculus":10090
                  };
                              
                  $('#global_coex_table').dataTable( {
                    "dom": 'C<"clear">lfrtip',
                    "processing": true,
                    "iDisplayLength": 20,
                    "aLengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]],
                    "caseInsensitive": true,
                    "searching": true,
                    "deferRender": true,
                    "order": [[ 1, "desc" ]],
                    "bProcessing": false,
                    } );

                  
                  
                  $('table#individual_expres_tables').dataTable( {
                    "dom": 'C<"clear">lfrtip',
                    "processing": true,
                    "iDisplayLength": 10,
                    "aLengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]],
                    "caseInsensitive": true,
                    "searching": true,
                    "deferRender": true,
                    "order": [[ 2, "desc" ]],
                    "bProcessing": false,
                    } );
                              
                              
                  /*$(function() {
                    $( "#mxa_accordion" ).accordion({
                    collapsible: true,
                    animate: 300,
                    heightStyle: 'content',
                    active: 0,
                    });
                });
                  
                  
                  $(function() {
                    $( "#mxa_accordion2" ).accordion({
                         collapsible: true,
                         animate: 300,
                         heightStyle: 'content',
                         active: 0, 
                         });
                });*/
            });

                  
            function get_mircounts_file(){
                $.get("generate_csv_get_file.php");
            }
                  
</script>

</head>

<body>

<div style=font-size:18px;">
<b>Search results:</b><br/>
</div>


<?php
    include('mysql_connect.php');
    include('hsv2rgb.php');

                              
    $mirna_search = $_GET['mirna_search'];
    $mirna_search = preg_replace('/[\\\<>;\/\[\]]/', '', $mirna_search);

if($mirna_search != ''){
                              
    $mirna_ids_delimimters = '/[,\n\s]/';
    $mirs_arr = preg_split($mirna_ids_delimimters, $mirna_search);
    $mirs_arr = array_filter($mirs_arr);


    $mxa_results_multiD_array = array();

    # get data from the database
    # =========>
                        
    //set default mirna expression threshold to 0
    $mir_expr_thres = 0;

    foreach($mirs_arr as $cur_mir){

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
        }
    }



    # <========



    echo    "<div id='mxa_accordion' style='font-size:14px;'>";

    echo    "<div class='adv_mod_search_category' style='width:98%'>";
    echo    "<b>miRNAs co-expression results:</b>";
    echo    "</div>";

    echo "
    <div id='mirna_coexpression_results' style='background-color:#fafafa; padding:20px'>";

    if(count($mirs_arr) <= 1){
        echo "<br/>None.<br/>";
    } else{
    // miRNA co-expression results


    $all_coexpr_datasets = array();


    $coexpr_mirs = array_keys($mxa_results_multiD_array);


    foreach($mxa_results_multiD_array as $cur_mir_key=>$indiv_arr_ext){

        $indiv_arr = $indiv_arr_ext[0];


        $cur_expr_datasets = $indiv_arr[0];


        $all_coexpr_datasets = array_unique(array_merge($all_coexpr_datasets, $cur_expr_datasets));

    }


    $global_expr_arr = array();

    foreach($mxa_results_multiD_array as $cur_mir_key=>$indiv_arr_ext){

        $indiv_arr = $indiv_arr_ext[0];

        $cur_expr_datasets = $indiv_arr[0];
        $cur_expr_ratios = $indiv_arr[1];

        $tmp_arr_diff = array_diff($all_coexpr_datasets, $cur_expr_datasets);

        foreach($tmp_arr_diff as $missing_dataset){

        $cur_expr_datasets[] = $missing_dataset;
        $cur_expr_ratios[] = 0;

        }

        $assoc_arr_for_cur_mir = array();

        for($i=0; $i<count($cur_expr_datasets); $i++){
            $cur_dataset = $cur_expr_datasets[$i];
            $cur_expr_ratio = $cur_expr_ratios[$i];

            $assoc_arr_for_cur_mir[$cur_dataset] = $cur_expr_ratio;
        }


        $global_expr_arr[$cur_mir_key] = $assoc_arr_for_cur_mir;
    }




    # print global table of expression
    $global_coex_table_str = "";

    $global_coex_table_str .= "<table id='global_coex_table' class='display' style='font-size:14px; text-align:center; padding:0'><thead>";
    $global_coex_table_str .= "<tr><th>[Accession Number]</th>";


    foreach($coexpr_mirs as $cur_mir_entry){
        $global_coex_table_str .= "<th>".$cur_mir_entry."</th>";
    }
    $global_coex_table_str .= "</tr></thead><tbody>";


    foreach($all_coexpr_datasets as $dataset){

        $global_coex_table_str .= "<tr><td><a href=/enright-dev/miratlas/show_table.php?acc_num=".$dataset.">".$dataset."</a></td>";

        foreach($coexpr_mirs as $cur_mir_entry){

            $cur_expr_ratio = $global_expr_arr[$cur_mir_entry][$dataset];
            $expr_lev_color = hsv2rgb(array(0.601, $cur_expr_ratio, 1));


            $global_coex_table_str .= "<td style='background-color:".$expr_lev_color.";'><span style='visibility:hidden;'>$expr_lev_color</span></td>";
        }

        $global_coex_table_str .= "</tr>";
    }


    $global_coex_table_str .= "</tbody></table>";

    echo    "miRNA expression level colorbar<div id='mir_expr_colorbar'>
    </div>";
    echo "0.0<span style='padding-left:215px;'>1.0</span>";
    echo "<div style='margin-bottom: 30px;'></div>";
    echo $global_coex_table_str;

    // <<<<=================

    }

    echo    "</div></div>";

    echo    "<div id='mxa_accordion2' style='font-size:14px; margin-top:50px'>"; 
    echo    "<div class='adv_mod_search_category' style='width:98%'>";
    echo "<b>Individual miRNA results:</b>";
    echo "</div>";

    echo "
    <div id='individual_mirna_results' style='background-color:#fafafa; padding:20px'>";


    //$tt = hsv2rgb(array(0.6667,1,1));  //end: rgb(0,0,255)
    //$tt = hsv2rgb(array(0.6667,0.1,1));  //start: rgb(229,229,255)
    //print_r($tt);


    echo    "miRNA expression level colorbar<div id='mir_expr_colorbar'> 
    </div>";
    echo "0.0<span style='padding-left:215px;'>1.0</span>";
    echo "<div style='margin-bottom: 20px;'></div>";

    // display expression ratios among all datasets for each given miRNA
    foreach($mirs_arr as $cur_mir){

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


            //if( $expr_ratio >= $mir_expr_thres){ //filtered already with the sql query

            $expr_lev_color = hsv2rgb(array(0.601, $expr_ratio, 1));

            $cur_table_str .= "<tr><td><a href=/enright-dev/miratlas/show_table.php?acc_num=".$dat_name.">".$dat_name."</a></td><td style='background-color:".$expr_lev_color.";'><span style='visibility:hidden;'>$expr_lev_color</span></td><td>".$expr_ratio."</td><td>".$plain_counts."</td><td style='text-align:left'>".$descr."</td></tr>";
            //}
        }


        $cur_table_str .= "</tbody></table>";
        //A9E2F3
        echo "<div style='font-size:16px; color: black; border:1px solid black; background-color:#f7f5f0; padding:8px; margin-bottom:5px;'><b>'$cur_mir'</b> expression:</div>";
        echo $cur_table_str;
        echo "<br/><br/><br/>";


        }

    echo "</div></div>";
    }
    
                              
                              
else{
  echo '<br/><b>No results found.</b><br/>';
  echo 'Please provide a valid miRNA name or list of miRNA names.<br/><br/>';
}
?>

</body>
</html>
