<html>


<head>
<meta charset="utf-8" />
<link href="css/main.css" type="text/css" rel="stylesheet">
<link href="./assets/DataTables-1.10.4/media/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="./assets/DataTables-1.10.4/extensions/ColVis/css/dataTables.colVis.css" rel="stylesheet">
<link href="./assets/DataTables-1.10.4/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet">

<link rel="stylesheet" href="./css/jquery-ui.css">
<link href="./css/tablesorter.theme.default.css" rel="stylesheet">

<script type="text/javascript" src="./assets/jquery.min.js"></script>
<script type="text/javascript" src="./assets/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="./assets/jquery-ui.js"></script>
<script type="text/javascript" src="./assets/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/TableTools/js/dataTables.tableTools.min.js"></script>

<script>

    TableTools.BUTTONS.download = {
    	"sAction": "text",
	"sTag": "default",
	"sFieldBoundary": "",
	"sFieldSeperator": "\t",
	"sNewLine": "<br>",
	"sToolTip": "",
	"sButtonClass": "DTTT_button_text",
	"sButtonClassHover": "DTTT_button_text_hover",
	"sButtonText": "Download",
	"mColumns": "all",
	"bHeader": true,
	"bFooter": true,
	"sDiv": "",
	"fnMouseover": null,
	"fnMouseout": null,
	"fnClick": function( nButton, oConfig ) {
		var oParams = this.s.dt.oApi._fnAjaxParameters( this.s.dt );
		var iframe = document.createElement('iframe');
		iframe.style.height = "0px";
		iframe.style.width = "0px";
		iframe.src = oConfig.sUrl+"?"+$.param(oParams);
		document.body.appendChild( iframe );
	},
	"fnSelect": null,
	"fnComplete": null,
	"fnInit": null
	};


    $(document).ready(function(){
        $(function() {
            $( "#accordion" ).accordion({
                collapsible: true,
                animate: 400,
                heightStyle: 'content',
                active: 0, 
                });
            });

	    $('table#mod_stats_by_pattern').dataTable( {
	    	"dom": 'T<"clear">lfrtip',
		"tableTools": {
			"aButtons":
				[
				{
					"sExtends":    "collection",
					"sButtonText": "Save Page as...",
					"aButtons":    [ "csv", "xls"],
					"bFooter": false,
				}
				],
				"sSwfPath": "./assets/DataTables-1.10.4/extensions/TableTools/swf/copy_csv_xls_pdf.swf"
		},
		"columnDefs": [
			{ "width": "13%", "targets": [0, 1] }
		],
		"processing": true,
		"iDisplayLength": 20,
		"aLengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]],
		"caseInsensitive": true,
		"searching": true,
		"deferRender": false,
		"order": [[ 10, "desc" ]],
		"bProcessing": true,
		});
    });


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



<?php 

# Need to query database here,
# then probably create a tmp text file
# that will be shortly analysed by an R script.

include('mysql_connect.php');

$mods_form1_arr = $_GET['mods_advanced_search2'];

end($mods_form1_arr);
$mods_form1_end_key = key($mods_form1_arr);

foreach($mods_form1_arr as $key=>$val){

	if($key !== $mods_form1_end_key){
		//$val = preg_replace('/\s+/', '', $val);
		$val = trim($val);
		$val = preg_replace('/[()\\\<>;\/\[\]]/', '', $val);

		$mods_form1_arr[$key] = $val;
	}
}

$patterns_to_search = $mods_form1_arr[0];
$sample_properties_to_search = $mods_form1_arr[1];
$selected_organism = $mods_form1_arr[2];
$species_to_query = '';
if($selected_organism == 'Homo sapiens'){
	$species_to_query = 'hsa';
} else if($selected_organism == 'Mus musculus'){
	$species_to_query = 'mmu';
} else{
	$species_to_query = 'hsa|mmu';
}



echo    "<div class='adv_mod_search_category' style='width:98%'>";
echo "<b>Modification stats by 'pattern'</b>";
echo    "</div>";
echo "<div style='padding:20px; border:1px solid black; background-color:#e8ecef;'>";
echo "<b>Pattern query:</b>&nbsp;";
echo $patterns_to_search;
echo "<br/><br/>";
echo "<b>Sample property query:</b>&nbsp;";
echo $sample_properties_to_search."<br/>";
echo "</div>";

echo "<h2>Results</h2><hr/>";
#if($patterns_to_search == '' or $sample_properties_to_search == ''){
if($patterns_to_search == ''){
        echo "Please specify a modification pattern for your query.";
} else{
# select datasets that correspond to the input sample properties
$selected_datasets = array();

$cond_query_term = '';
$conditions_to_query = split(",", $sample_properties_to_search);
foreach ($conditions_to_query as $cond){
	$cond = trim($cond);
	$cond_query_term .= $cond."|";
}
$cond_query_term = substr($cond_query_term, 0, -1);
if($sample_properties_to_search == ''){
	$cond_query_term ="^.*$";
}

$sql_query = "SELECT ACCESSION_NUMBER,TAXON FROM DATASETS WHERE DESCRIPTION REGEXP '".$cond_query_term."'";
$query_result = mysqli_query($conn, $sql_query)
    or die("Error: " . mysqli_error($conn));

if (mysqli_num_rows($query_result) > 0) {
	while($res_row = mysqli_fetch_assoc($query_result)) {
		$cur_dataset = $res_row["ACCESSION_NUMBER"];
		$cur_taxon = $res_row["TAXON"];
		if($selected_organism !== 'All'){
			if($cur_taxon == $selected_organism){
				$selected_datasets[] = $cur_dataset;
			}
		} else{
			$selected_datasets[] = $cur_dataset;
		}
	}
}

foreach($selected_datasets as $d){
	$descr_sql_query = "SELECT DESCRIPTION FROM DATASETS WHERE ACCESSION_NUMBER='".$d."'";
	$descr_result = mysqli_query($conn, $descr_sql_query)
		or die("Error: " . mysqli_error($conn));

	$cur_descr = '-';
	if (mysqli_num_rows($result) > 0) {
		$descr_row = mysqli_fetch_assoc($descr_result);
		$descr_arr[$d] = $descr_row["DESCRIPTION"];
	}
}

$annotation_table = "<br/><table id='annotation_table' class='display' style='width:650px; margin:0 auto; padding:5px; background-color:#fefefe'><thead>";
$annotation_table .= "<tr><th>Accession Number</th><th>Description</th></tr></thead><tbody>";

foreach($selected_datasets as $dataset){
	$annotation_table .= "<tr><td><a href=/enright-dev/miratlas/show_table.php?acc_num=".$dataset.">".$dataset."</a></td>";
	$annotation_table .= "<td>$descr_arr[$dataset]</td></tr>";
}

$annotation_table .= "</tbody></table><br/><br/>";

echo $annotation_table;

# datasets
$datasets_query_term = '';
foreach ($selected_datasets as $d){
	$datasets_query_term .= $d."|";        
}
$datasets_query_term = substr($datasets_query_term, 0 , -1);

#$datasets_query_term_to_echo = $datasets_query_term;
#$datasets_query_term_to_echo = str_replace("|", " | ", $datasets_query_term_to_echo);

#echo $datasets_query_term_to_echo."<br/>";

#mirnas
$patterns_query_term = '';
$patterns_to_query = split(",", $patterns_to_search);
foreach ($patterns_to_query as $pattern){
	$patterns_query_term .= $pattern."|";
}

$patterns_query_term = $patterns_to_search;

#echo $patterns_query_term."<br/>";

# main query
$mod_sql_query = "SELECT * FROM mircounts_full_view WHERE ACCESSION_NUMBER_REF REGEXP '".$datasets_query_term."' AND pattern='".$patterns_query_term."'";

if($species_to_query !== ''){
	$mod_sql_query .= " AND MATURE_MIR_ID_REF REGEXP '".$species_to_query."'";
}

#echo $mod_sql_query;

$query_result = mysqli_query($conn, $mod_sql_query)
    or die("Error: " . mysqli_error($conn));

# generate tmp uuid - look chimira's index.php page
#$uuid =  123;
#$tmp_file = "tmp/$uuid.tmp";
#$tmp_file_header = "MIRNA\tMODIFICATION_TYPE\tMODIFICATION_ARM\tMODIFICATION_PATTERN\tMODIFICATION_POSITION\tINTERNAL_MOD_TYPE\tINTERNAL_MOD_PATTERN\tINTERNAL_MOD_POSITION\tDOUBLED\tprocessed.counts";
#file_put_contents($tmp_file, $tmp_file_header);

echo "<table id='mod_stats_by_pattern' class='display'>";
echo "<thead>";
echo "<tr>";
echo "<th>Dataset</th><th>miRNA</th><th>mod type</th><th>arm</th><th>pattern</th><th>position</th><th>internal mod type</th><th>internal pattern</th><th>internal position</th><th>Doubled</th><th>counts</th>";
echo "</tr></thead><tbody>";

$cur_descr = '-';
$tt = '';
if (mysqli_num_rows($query_result) > 0) {

	while($mod_row = mysqli_fetch_assoc($query_result)) {

	# ----> continue here
	# - store results to file
	# - then parse file with R script in order to make graphics with D3.js 
	# - display D3.js content on results page
		$tmp_dataset = $mod_row["ACCESSION_NUMBER_REF"];
		$mir = $mod_row["MATURE_MIR_ID_REF"];
		$mod_type = $mod_row["modification_type"];
		$mod_arm = $mod_row["arm"];
		$mod_pattern = $mod_row["pattern"];
		$mod_position = $mod_row["position"];
		$internal_mod_type = $mod_row["internal_modification_type"];
		$internal_pattern = $mod_row["internal_pattern"];
		$internal_position = $mod_row["internal_position"];
		$doubled = $mod_row["doubled"];
		$raw_counts = $mod_row["raw_counts"];

		echo "<tr>";
		echo "<td>$tmp_dataset</td><td>$mir</td><td>$mod_type</td><td>$mod_arm</td><td>$mod_pattern</td><td>$mod_position</td><td>$internal_mod_type</td><td>$internal_pattern</td><td>$internal_position</td><td>$doubled</td><td>$raw_counts</td>";
		echo "</tr>";
		

		$tt = $mod_row;

#		echo "$tmp_dataset<br/>";
#		print_r($tt);
	}
}

echo "</tbody></table><br/><br/><br/>";
#print_r($tt);

#$query_mod_out = shell_exec("./cgi-bin/query_modifications.sh");
#echo $query_mod_out."<br/>";
}

?>

    <br/><br/><br/>

    <?php include('footer.php') ?>
    </div>

</body>
</html>
