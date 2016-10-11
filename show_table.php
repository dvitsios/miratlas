<html>
    <head>
        <meta charset="utf-8" />

        <link href="css/main.css" type="text/css" rel="stylesheet">
        <link href="./assets/DataTables-1.10.4/media/css/jquery.dataTables.min.css" rel="stylesheet"> 
        <link href="./assets/DataTables-1.10.4/extensions/ColVis/css/dataTables.colVis.css" rel="stylesheet">
        <link href="./assets/DataTables-1.10.4/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="./viz_factory/assets/c3.css">

        <script src="http://d3js.org/d3.v3.min.js"></script>
	<script type="text/javascript" src="./viz_factory/assets/c3.js"></script>
        <script type="text/javascript" src="./assets/jquery.min.js"></script> 
        <script type="text/javascript" src="./assets/DataTables-1.10.4/media/js/jquery.dataTables.min.js"></script> 
        <script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/ColVis/js/dataTables.colVis.min.js"></script>
        <script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/TableTools/js/dataTables.tableTools.min.js"></script>

        <?php
            $datasets_str = $_GET['acc_num'];
            $datasets_str = preg_replace('/\s+/', '', $datasets_str);
            $datasets_str = preg_replace('/[\\\<>;\/\[\]]/', '', $datasets_str);
            //$datasets_arr=explode(",",$datasets_str);
            $_SESSION['acc_num'] = $datasets_str;

        ?>
            
        
        <script>
            

        </script>
    </head>

    <body id="full_page_div">
<div id="wrapper">
        
        <div id="header">
        <?php include 'header.php'; ?>
        </div>

        <div id='menu_bar'>
        <?php include('menu_bar.php'); ?>
        </div>

        <?php include 'mysql_connect.php'; ?>

<div style="background-color:#f4f3ed; font-size:18px; padding:10px; margin-top:30px;">
            <div style="float:left; text-align:left; font-size:20; font-weight: bold;"><?php echo $datasets_str ?></div><div style="float:center; text-align:right;"><a href='browse_all_datasets.php'>View all datasets</a></div>
</div>

            <div class='dataset_annotation_entry' style='border-bottom: none; background-color:#fefefe'><b>Description:</b>

            <div style="padding: 6px;">
            <?php
                # get description for cur_acc_num
                $descr_sql_query = "SELECT DESCRIPTION FROM DATASETS WHERE ACCESSION_NUMBER='$datasets_str'";


                $descr_result = mysqli_query($conn, $descr_sql_query)
                    or die("Error: " . mysqli_error($conn));

                $cur_descr = '-';
                if (mysqli_num_rows($result) > 0) {
                    $descr_row = mysqli_fetch_assoc($descr_result);
                    $cur_descr = $descr_row["DESCRIPTION"];
                }

                echo "$cur_descr<br/>";
            ?>
            </div>
            </div>


            <div class='dataset_annotation_entry' style='border-bottom: none; background-color:#fafafa'><b>Organism:</b>

            <div style="padding: 6px;">
            <?php
                # get description for cur_acc_num
                $descr_sql_query = "SELECT TAXON FROM DATASETS WHERE ACCESSION_NUMBER='$datasets_str'";
                
                
                $descr_result = mysqli_query($conn, $descr_sql_query)
                or die("Error: " . mysqli_error($conn));
                
                $cur_descr = '-';
                if (mysqli_num_rows($result) > 0) {
                    $descr_row = mysqli_fetch_assoc($descr_result);
                    $cur_descr = $descr_row["TAXON"];
                }
                
                echo "<i>$cur_descr</i><br/>";
                ?>
            </div>
            </div>


            <div class='dataset_annotation_entry' style='background-color:#fefefe'><b>Reference Database:</b>

            <div style="padding: 6px;">
            <?php
                $base_datasource_link = '';
                if($data_sources_arr[$datasets_str] === "ArrayExpress"){
                    $base_datasource_link = 'http://www.ebi.ac.uk/arrayexpress/experiments/';
                } else if($data_sources_arr[$datasets_str] === "ENA"){
                    $base_datasource_link = 'http://www.ebi.ac.uk/ena/data/view/';
                }
                
                $datasource_link = $base_datasource_link.$datasets_str;
                
                echo "<a href='".$datasource_link."'>view reference</a>";
            ?>


            </div>
            </div>


<br/><br/>
<div id='analysed_data_table_title' style='width:auto; box-shadow: 0px 0px 0px #ffffff;'>Modifications Profile</div>
<div style='background-color:#fbfbfb; padding:7px'>
<div id="global_mods_profile" style='height:300px; width:auto;'></div>
</div>
<br/>
<script>

var uuid = "<?php echo $datasets_str; ?>";


// global profile
var global_mods_profile_path = './viz_factory/viz_input/'+uuid+'.global_mods_profile_data.csv';

var chart = c3.generate({

	bindto: '#global_mods_profile',
	data: {
		x : 'mirna_index',
		url: global_mods_profile_path,
		type: 'bar',
		groups: [
			['U', 'A', 'C', 'G', 'G_adar', 'A_snp', 'U_snp', 'G_snp', 'C_snp']
		]
	},
	axis: {
		x: {
			type: 'category' // this needed to load string x value
		}
	},
	grid: {
		x: {
			show: true
		},
		y: {
			show: true
		}
	}
});

</script>



<div id='analysed_data_table_title'>Counts data</div>

<div>
<table class="CSS_Table_Dataset_Views" style="float:left; width:650px;height:130px;">
<tr style='background-color:#fcfcfc;'><td style='padding-left:20px; font-size:15px; '>Template miRNA counts:</td>
<td style='text-align:center'><a href="/enright-dev/miratlas/show_table_template_counts.php?acc_num=<?php echo $datasets_str ?>">Preview Table</a></td>
    <td style='text-align:center'><a href="generate_template_csv_get_file.php?acc_num=<?php echo           $datasets_str ?>">Download counts</a></td>
</tr>
<tr style='background-color:#fcfcfc;'><td style='padding-left:20px; font-size:15px;'>Modification counts:</td>
<td style='text-align:center'><a href="/enright-dev/miratlas/show_table_with_mods.php?acc_num=<?php echo $datasets_str ?>">Preview Table</a></td>
    <td style='text-align:center'><a href="generate_csv_get_file.php?acc_num=<?php echo $datasets_str ?>">Download counts</a></td>
</tr>
</table>
</div>

<br/><br/><br/>
<br/><br/><br/>
<br/><br/><br/>

<table id='mir_expr_profile_for_dataset_table' style='background-color:#fafafa'>
<?php $d_mir_prof_link = "/enright-dev/miratlas/mxa_search_results.php?mxa[]=&mxa[]=&mxa_search_strategy=search_dataset_names&mxa[]=$datasets_str&mxa[]=1&mxa[]=All" ?>
<tr><td style='font-weight: bold; color:black'>miRNA expression profiler<br/><br/></td></tr>
<tr>
<form action='single_dataset_expression_profiler.php?acc_num=<?php echo $datasets_str ?>' method='POST'>
<td><span style='color:black; font-size:16px'>expression threshold:</span>&nbsp;
<input id='dataset_mir_expr_thres_id' name="dataset_mir_expr_thres" type="text" size="4" value="1" style='text-align:right; font-size:14px'/> % 
</td>
<td style='width:20%; text-align:right;'>
<input type="submit" value="View profile">
</td>
</form>
</table>

        <br /><br /><br /><br /><br /><br /><br /><br />




    <?php include('footer.php') ?>

</div>
    </body>
</html>
