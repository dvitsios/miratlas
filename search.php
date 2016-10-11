<?php
    include("server_conf.php");
?>


<html>

    <head>
        <meta charset="utf-8" />
        <link href="css/main.css" type="text/css" rel="stylesheet">
        <link href="./css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="./css/tablesorter.theme.default.css" rel="stylesheet">
        <link rel="stylesheet" href="./assets/jquery_ui/jquery-ui.css">
        <link rel="stylesheet" href="./assets/jquery_ui/jquery-ui.theme.css">

        <script type="text/javascript" src="./assets/jquery.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.dataTables.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.watermarkinput.js"></script> 
        <script type="text/javascript" src="./assets/jquery.tablesorter.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery_ui/jquery-ui.js"></script>

        <script>
            $(document).ready(function(){
                jQuery(function($){
                    //$("#dataset_input").Watermark("e.g. PRJNA190003, E-GEOD-16579");
                    //$("#description_input").Watermark("e.g. liver, cancer");
                });
                
                var reset_button = document.getElementById('reset_search_datasets_form');
                reset_button.onclick = reset_form;


                var reset_mxa_button = document.getElementById('reset_mxa_form');
                reset_mxa_button.onclick = reset_mxa_form;
            });

            $(function() {
                $( "#accordion" ).accordion({
                    collapsible: true,
                    animate: 400,
                    heightStyle: 'content',
                    active: 0, 
                });
            });


            $(function() {
                $( "#accordion2" ).accordion({
                    collapsible: true,
                    animate: 400,
                    heightStyle: 'content',
                    active: 0, 
                });
            });
            
            
            function edit(elem)
            {
                elem.value = '';
            }


            function mxa_edit(base_elem, elem1, elem2)
            {
                elem1.value = '';
                elem2.value = '';
                //base_elem.style.color = "black";
                //elem1.style.color = "#D8D8D8";
                //elem2.style.color = "#D8D8D8";
            }


            function reset_form(){
                $("#adv_search_form")[0].reset();
            }

            function reset_mxa_form(){
                $("#mirna_expr_atlas_form")[0].reset();
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

<div style="font-size:20; font-weight:bold; text-align:left ">Advanced Search Tools:</div>
<br/>


  <div class='adv_mod_search_category' id='expr_search' style='width:68%'>
  <b>miRNA expression atlas</b>
  </div>
<div id="adv_form_template" style="font-size:14px; width:70%">
  <div id="advanced_search_form" style="padding-bottom:0px;">
        <form id='mirna_expr_atlas_form' action="mxa_search_results.php" method="get">
        <ol>
        <li>
    <input type="radio" name="mxa_search_strategy" id="mirna_ids_radio_btn" value="search_mirna_ids" checked="checked"><b>miRNA ID(s)</b>:<br/>    
<textarea id='mxa_mir_input' style="width: 400px; height: 80px; resize:none; font-size: 15px;" name="mxa[]" rows="4" cols="15" onfocus="this.form.mirna_ids_radio_btn.checked=true; mxa_edit(document.getElementById('mxa_mir_input'),document.getElementById('mxa_sample_properties_input'), document.getElementById('mxa_dataset_names_input'));"></textarea>
        <div style="font-size:14px; padding-top:4px; font-family:Palatino"> <i>(e.g. hsa-miR-122-5p, hsa-let-7a-5p, mmu-miR-127-3p)</i></div>
        <br />
<input type="radio" name="mxa_search_strategy" id="sample_properties_radio_btn" value="search_sample_properties"><b>Sample properties</b>:
<input id='mxa_sample_properties_input' name="mxa[]" type="text" size="40" value="<?php echo $_GET['mxa'][2]?>" tabindex=-1  onfocus="this.form.sample_properties_radio_btn.checked=true; mxa_edit(document.getElementById('mxa_sample_properties_input'), document.getElementById('mxa_mir_input'), document.getElementById('mxa_dataset_names_input'));">
    <span style="font-size:14px; padding-left:20px; font-family:Palatino"><i>(e.g. liver, lymphoma)</i></span>

    <br/><br/><input type="radio" name="mxa_search_strategy" id="dataset_names_radio_btn" value="search_dataset_names"><b>Dataset names</b>:<span style="padding-left:23px;"></span>
<input id='mxa_dataset_names_input' name="mxa[]" type="text" size="40" value="<?php echo $_GET['mxa'][3]?>" tabindex=-1 onfocus="this.form.dataset_names_radio_btn.checked=true; mxa_edit(document.getElementById('mxa_dataset_names_input'), document.getElementById('mxa_sample_properties_input'), document.getElementById('mxa_mir_input'));"/>
    <span style="font-size:14px; padding-left:20px; font-family:Palatino"><i>(e.g. PRJNA177892, E-GEOD-37616)</i></span>
        
        </li>
        <br /> 
        <br />
        <li>
        <b>Expression threshold:</b>
<input id='mxa_mir_expr_thres' name="mxa[]" type="text" size="2" value="0" style='text-align:right;'/><b>&nbsp;%</b> 
        </li> 
        <br /> 
        <br /> 
        <li>
        <b>Select organism</b>:
        <select name="mxa[]">
        <option value="All" <?php if($_GET['mxa'][2]=='All') echo "selected='selected'";?>>All</option>
        <option value="Homo sapiens" <?php if($_GET['mxa'][4]=='Homo sapiens') echo "selected='selected'";?>>Homo sapiens</option>
        <option value="Mus musculus" <?php if($_GET['mxa'][4]=='Mus musculus') echo "selected='selected'";?>>Mus musculus</option>
        </select> 
        </li>
        <br/>
        <input type="submit" value="Search">
        <input type="button" id="reset_mxa_form" value="Reset"/>
        </li>
        </ol>
        </form> 
  </div>
</div>

<br/>
<br/>
<br/>

<div class='adv_mod_search_category' id='data_search' style='width:68%'>
  <b>Search for Datasets</b>
</div>

<div id="adv_form_template" style="font-size:14px; margin-bottom:30px; width:70%">
<div id='advanced_search_form'>
<form method="post" id='adv_search_form' action="search_results.php">
<ol>
<li>
<b>Dataset name(s)</b>:&nbsp;&nbsp;&nbsp;&nbsp;
<input id='dataset_input' name="advanced_search[]" type="text" size="40" value="<?php echo $_POST['advanced_search'][0]?>" onfocus="edit(document.getElementById('description_input'));"/>
<span style="font-size:14px; padding-left:20px; font-family:Palatino"><i>(e.g. PRJNA190003, PRJNA1, E-GEOD)</i></span>
</li>
<br />
or
<br />
<br />
<b>Sample properties</b>:&nbsp;<input id='description_input' tabindex=-1 name="advanced_search[]" type="text" size="40" value="<?php echo $_POST['advanced_search'][1]?>" onfocus="edit(document.getElementById('dataset_input'));"/>
<span style="font-size:14px; padding-left:20px; font-family:Palatino"><i>(e.g. liver, cancer - multiple terms allowed)</i></span>
<br />
<br />
<br />
<li>
<b>Select organism</b>:
<select name="advanced_search[]">
<option value="All" <?php if($_POST['advanced_search'][2]=='All') echo "selected='selected'";?>>All</option>
<option value="Homo sapiens" <?php if($_POST['advanced_search'][2]=='Homo sapiens') echo "selected='selected'";?>>Homo sapiens</option>
<option value="Mus musculus" <?php if($_POST['advanced_search'][2]=='Mus musculus') echo "selected='selected'";?>>Mus musculus</option>
</select>
</li>
<br />
<input type="submit" value="Search">
<input type="button" id="reset_search_datasets_form" value="Reset"/>
</li>
</ol>
</form>

</div>
</div>

<br/><br/><br/><br/><br/><br/>


        <?php include('footer.php') ?>

        <?php
            if($_POST){
            }
        ?>

</div>
    </body>
</html>
