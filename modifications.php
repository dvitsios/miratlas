<html>


<head>
<meta charset="utf-8" />
<link href="css/main.css" type="text/css" rel="stylesheet">
<link href="./css/jquery.dataTables.min.css" rel="stylesheet">
<link rel="stylesheet" href="./assets/jquery_ui/jquery-ui.css">
<link rel="stylesheet" href="./assets/jquery_ui/jquery-ui.theme.css">

<script type="text/javascript" src="./assets/jquery.min.js"></script>
<script type="text/javascript" src="./assets/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="./assets/jquery_ui/jquery-ui.js"></script>

<script>

    $(document).ready(function(){
        $(function() {
            $( "#accordion" ).accordion({
                collapsible: true,
                animate: 400,
                heightStyle: 'content',
                });
        });
        
        $(function() {
            $( "#accordion2" ).accordion({
                collapsible: true,
                animate: 400,
                heightStyle: 'content',
                });
            });


        $(function() {
            $( "#accordion3" ).accordion({
                collapsible: true,
                animate: 400,
                heightStyle: 'content',
                });
        });

	$( "#mods_mirs_input" ).keypress(function() {
		$('#mirna_id').prop('checked', 'checked');
	});

    });




    function dmp_edit(base_elem, elem1)
    {
        elem1.value = '';
        //base_elem.style.color = "black";
        //elem1.style.color = "#D8D8D8";
    }

    function enable_text(status)
    {
    	status=!status;	
   	document.mods_form_by_mirid.mods_mirs_input_text.disabled = status;
    }


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

<div style="font-size:20; font-weight:bold; text-align:left ">Modification Analysis:</div>
<br/>

<div class='adv_mod_search_category' style='width:68%'> 
Modification Stats by: 'miRNA'
</div>

<div id="form_template" name="mods_form_by_mirid" style="margin-bottom:30px; width:70%">
<div id='mods_advanced_search_form'>
<form method="get" id='mods_adv_search_form' action="query_modifications.php">
<ul style="list-style: none;">
<li>
<b>miRNA ID(s)<span style='color:red'>*</span></b>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input id='mods_mirs_input' name="mods_advanced_search1[]" type="text" onkeyup="if($(this).val() != '') $('.checkbox_class').enable()" size="40" value="<?php echo $_GET['mods_advanced_search1'][0]?>"/>
<span style="font-size:14px; padding-left:20px; font-family:Palatino"><i>(e.g. hsa-let-7, mmu-mir-100, mir-21a- )</i></span>
</li>
<br />

<li>
<b>Sample properties<span style='color:red'>*</span></b>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id='mods_sample_properties' name="mods_advanced_search1[]" type="text" size="40" value="<?php echo $_GET['mods_advanced_search1'][1]?>"/>
<span style="font-size:14px; padding-left:20px; font-family:Palatino"><i>(e.g. liver, cancer, brain)</i></span>
<br />
</li>
<br />
<input type="submit" value="Search">
</li>

</ul>
</form>

</div>
</div>


<div class='adv_mod_search_category' style='width:68%'>
Modification Stats by: 'mod. pattern'
</div>

<div id="form_template2" style="margin-bottom:30px; width:70%">
<div id='mods_advanced_search_form2'>
<form method="get" id='mods_adv_search_form2' action="query_modifications2.php">
<ul style="list-style: none;">

<li>
<!-- <input type="checkbox" name="mod_search_terms2" value="mod_pattern"> -->
<b>Modification pattern<span style='color:red'>*</span></b>:&nbsp;&nbsp;<input id='mods_mod_pattern' name="mods_advanced_search2[]" type="text" size="40" value="<?php echo $_GET['mods_advanced_search2'][1]?>"/>
<span style="font-size:14px; padding-left:20px; font-family:Palatino"><i>(e.g. U, AA)</i></span>
<br />
</li>
<br />

<li>
<!-- <input type="checkbox" name="mod_search_terms2" value="sample_properties"> -->
<b>Sample properties<span style='color:red'>*</span></b>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id='mods_sample_properties2' name="mods_advanced_search2[]" type="text" size="40" value="<?php echo $_GET['mods_advanced_search2'][1]?>"/>
<span style="font-size:14px; padding-left:20px; font-family:Palatino"><i>(e.g. brain, leukemia, serum)</i></span>
<br />
</li>
<br />
<li>
<b>Select organism</b>:&nbsp;&nbsp;
<select name="mods_advanced_search2[]" style='margin-left:33px'>
<option value="All">All</option>
<option value="Homo sapiens">Homo sapiens</option>
<option value="Mus musculus">Mus musculus</option>
</select>
<br />
</li>
<input type="submit" value="Search">
</li>

</ul>
</form>

</div>
</div>





<div style='margin-top:50px'>
<hr/ style='width:66%; border-color:#e8cad2; margin-left:20px; margin-right:20px'>

<div class='adv_mod_search_category' style='margin-top:40px; width:68%'> 
Overall Modification Stats by Dataset / Sample properties
</div>
<div id="form_template" style="margin-bottom:30px; width:70%">

<div id="dataset_mods_advanced_search_form">
<form method="get" id='dataset_mods_adv_search_form' action="query_dataset_modifications.php">
<ol>
<li>
<input type="radio" name="query_dataset_mods_strategy" id="dataset_names_radio_btn" value="search_dataset_names" checked="checked">&nbsp;<b>Dataset names</b>:&nbsp;<span style="padding-left:23px;"></span>
<input id='dmp_dataset_names_input' name="dmp[]" type="text" size="40" tabindex=-1 onfocus="this.form.dataset_names_radio_btn.checked=true; dmp_edit(document.getElementById('dmp_dataset_names_input'), document.getElementById('dmp_sample_properties_input'));"/>
<span style="font-size:14px; padding-left:13px; font-family:Palatino"><i>(e.g. PRJNA177892) <span style='font-size:13px'>- <b>leave blank for all datasets</b></span></i></span>

<br/><br/><input type="radio" name="query_dataset_mods_strategy" id="sample_properties_radio_btn" value="search_sample_properties">&nbsp;<b>Sample properties</b>:&nbsp;
<input id='dmp_sample_properties_input' name="dmp[]" type="text" size="40" tabindex=-1  onfocus="this.form.sample_properties_radio_btn.checked=true; dmp_edit(document.getElementById('dmp_sample_properties_input'), document.getElementById('dmp_dataset_names_input'));">
<span style="font-size:14px; padding-left:13px; font-family:Palatino"><i>(e.g. liver, cancer) <span style='font-size:13px'>- <b>leave blank for all datasets</b></span></i></span>

</li>
<br />
<li>
<b>Select organism</b>:
<select name="dmp[]" style='margin-left:40px'>
<option value="All">All</option>
<option value="Homo sapiens">Homo sapiens</option>
<option value="Mus musculus">Mus musculus</option>
</select>
</li>
<br/>
<input type="submit" value="Search">
</li>
</ol>
</form>
</div>


    <br/><br/><br/>

    <span style='color:red'>*</span>: <i>required fields</i>

</div>




    <br/><br/>

    <?php include('footer.php') ?>
    </div>

</body>
</html>
