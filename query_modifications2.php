<?php
	# generate tmp uuid 
	$uuid = uniqid();
?>

<html>


<head>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="./viz_factory/assets/c3.css">
<link href="css/main.css" type="text/css" rel="stylesheet">
<link href="./assets/DataTables-1.10.4/media/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="./assets/DataTables-1.10.4/extensions/ColVis/css/dataTables.colVis.css" rel="stylesheet">
<link href="./assets/DataTables-1.10.4/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet">

<link rel="stylesheet" href="./css/jquery-ui.css">
<link href="./css/tablesorter.theme.default.css" rel="stylesheet">

<script src="http://d3js.org/d3.v3.min.js"></script>
<script type="text/javascript" src="./viz_factory/assets/c3.js"></script>
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

	    var uuid = "<?php echo $uuid; ?>";
	    var plain_ajax_src = 'tmp/'+uuid+'.ajax.table.counts';

	    $('table#mod_stats_by_pattern').dataTable( {
	    	"dom": 'T<"clear">lfrtip',
		"sAjaxSource": plain_ajax_src,
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
		"deferRender": true,
		"order": [[ 10, "desc" ]],
		"bProcessing": true
		});
    });


</script>
<?php include_once("analyticstracking.php") ?>
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
if($patterns_to_search == '' or $sample_properties_to_search == ''){
#if($patterns_to_search == ''){
	echo "No results found. Please specify an argument at all input fields.";
	echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/>";
	include('footer.php');
	exit;

} else{



# select datasets that correspond to the input sample properties
$selected_datasets = array();

$cond_query_term = '';
$cond_term_cnt = 0;
$conditions_to_query = split(",", $sample_properties_to_search);
foreach ($conditions_to_query as $cond){
	$cond = trim($cond);
	$cond_query_term .= $cond."|";
	$cond_term_cnt++;
}
$cond_query_term = substr($cond_query_term, 0, -1);
if($cond_term_cnt > 2){
	echo "Please try again with fewer sample property arguments. <b>Max. allowed</b>: 2";
	echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/>";
	include('footer.php');
	exit;
}


#patterns
$patterns_query_term = '';
$patterns_term_cnt = 0;
$patterns_to_query = split(",", $patterns_to_search);
foreach ($patterns_to_query as $mod_pattern){

	$mod_pattern = trim($mod_pattern);

	$pattern = "/^[ACGUTNX]*$/";
	if(preg_match($pattern, $mod_pattern)){
		$patterns_query_term .= '[[:<:]]'.$mod_pattern."[[:>:]]|";
		$patterns_term_cnt++;
	}
}

$patterns_query_term = substr($patterns_query_term, 0, -1);

if( $patterns_query_term == ''){
	echo "No results found. Please use at least one <b>valid modification pattern</b> argument.";
	echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/>";
	include('footer.php');
	exit;
} elseif( $patterns_term_cnt > 4 ){
	echo "Please try again with fewer modification pattern arguments. <b>Max. allowed</b>: 4";
	echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/>";
	include('footer.php');
	exit;
}

#if($sample_properties_to_search == ''){
#	$cond_query_term ="^.*$";
#}

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


echo "<div class='adv_mod_search_category' style='width:98%'>";
echo "<b>Aggregate modifications profile</b>";
echo "</div>";
echo "<div style='background-color:#f7f7f7; padding:5px'>";
echo "<div id='global_mods_profile' style='height:300px; width:auto'></div>";
echo "</div>";
echo "<br/><hr/>";
#$patterns_query_term = $patterns_to_search;

#echo $patterns_query_term."<br/>";

# main query
$mod_sql_query = "SELECT * FROM mircounts_full_view WHERE ACCESSION_NUMBER_REF REGEXP '".$datasets_query_term."' AND pattern REGEXP '".$patterns_query_term."'";

if($species_to_query !== ''){
	$mod_sql_query .= " AND MATURE_MIR_ID_REF REGEXP '".$species_to_query."'";
}

#echo $mod_sql_query;

$query_result = mysqli_query($conn, $mod_sql_query)
    or die("Error: " . mysqli_error($conn));


$tmp_file = "tmp/".$uuid.".tmp";
$tmp_ajax_file = "tmp/".$uuid.".ajax.table.counts";
#file_put_contents($tmp_file, $tmp_file_header);


$full_table_str = "Num_id,Accession Number,miRNA id,Modification type,Arm,Pattern,Position,Internal modification type, Internal pattern, Internal position, Doubled, Raw Counts\n";
$full_ajax_str = "{ \"aaData\": [\n";


$cur_descr = '-';
$tt = '';
$ommit = True;
if (mysqli_num_rows($query_result) > 0) {

	while($mod_row = mysqli_fetch_assoc($query_result)) {

		if($ommit == True){
			$ommit = False;
		} else{
			$full_ajax_str .= ",\n";
		}


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

		$full_table_str .= "0,$tmp_dataset,$mir,$mod_type,$mod_arm,$mod_pattern,$mod_position,$internal_mod_type,$internal_pattern,$internal_position,$doubled,$raw_counts\n";

		$full_ajax_str .= "[\"$tmp_dataset\", \"$mir\", \"$mod_type\", \"$mod_arm\", \"$mod_pattern\", \"$mod_position\", \"$internal_mod_type\", \"$internal_pattern\", \"$internal_position\", \"$doubled\", \"$raw_counts\"]";

	}
}

$full_ajax_str .= "] }";

file_put_contents($tmp_file, $full_table_str);
file_put_contents($tmp_ajax_file, $full_ajax_str);


$tmp_file = $uuid.".tmp";
$query_mod_out = shell_exec("./cgi-bin/query_modifications.sh $tmp_file");
echo $query_mod_out."<br/>";

$viz_input_file = $uuid.".global_mods_profile_data.csv";
$global_mods_profile_path = "tmp/".$viz_input_file;

echo "<script>\n";
echo "var uuid = '".$uuid."';\n";
echo "var div_for_bind = '#global_mods_profile';\n";
#echo "var global_mods_profile_path = './tmp/".$uuid.".global_mods_profile_data.csv'";
echo "var global_mods_profile_path = '".$global_mods_profile_path."';\n";

echo "var chart = c3.generate({\n";
echo "bindto: div_for_bind,\n";
echo "data: {\n";
echo "x : 'mirna_index',\n";
echo "url: global_mods_profile_path,\n";
echo "type: 'bar',\n";
echo "groups: [\n";
echo "['U', 'A', 'C', 'G', 'G_adar', 'A_snp', 'U_snp', 'G_snp', 'C_snp']\n";
echo "]\n";
echo "},\n";
echo "axis: {\n";
echo "x: {\n";
echo "type: 'category'\n";
echo "}},\n";
echo "grid: {\n";
echo "x: {\n";
echo "show: true\n";
echo "},\n";
echo "y: {\n";
echo "show: true }\n";
echo "}\n";
echo "});\n";
echo "</script>\n";

}

?>
<table id='mod_stats_by_pattern' class='display'>
<thead>
	<tr>
		<th>Dataset</th>
		<th>miRNA</th>
		<th>mod type</th>
		<th>arm</th>
		<th>pattern</th>
		<th>position</th>
		<th>internal mod type</th>
		<th>internal pattern</th>
		<th>internal position</th>
		<th>Doubled</th>
		<th>counts</th>
	</tr>
</thead>
</table>
																<br/><br/><br/>
<br/><br/><br/>

    <?php include('footer.php') ?>
    </div>

</body>
</html>
