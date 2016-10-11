<html>

    <head>
        <meta charset="utf-8" />
        <link href="css/main.css" type="text/css" rel="stylesheet">

        <script type="text/javascript" src="./assets/jquery.min.js"></script> 

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


	<div class='adv_mod_search_category' style='width:99%; margin-bottom:0px; padding:5px '>
	Help Index
	</div>
	<div style='background-color:#fbfbfb;  padding:20px; padding-left:20px; border:1px black solid'>
	<li><a href='#browse_help'>Browse</a></li>
	<li><a href='#adv_search_help'>Advanced search</a></li>
	<li><a href='#mods_analysis_help'>Modification analysis</a></li>
	</div>

	<br/><br/><br/>

	<div class='adv_mod_search_category' style='width:98%'>
        <a href="browse_all_datasets.php">Browse</a> tab
	</div>
        <div id='browse_help' style="background-color:#fafcfa; padding:10px; margin-bottom: 20px; border-radius:0px; border:1px solid grey">
            <style>
            li span { padding-left: 10px; line-height: 2em; }
            </style>
            <ul>
            <li><span>
            You can browse to a dataset by clicking at its Accession Number.
            </span></li>
	    <li><span>
	    The modification profile is displayed as a stacked barplot at the dataset's main page.
	    </span></li>
	    <img src='images/help/png/mods_profile.png' style='width:98%;height:auto; border:1px solid #ccc;'>
	    <br/><br/>
	    <li><span>
	    Template and Modification Counts are available for each dataset to download and preview.
	    </span></li>
            <li><span>
            In <b>Preview Table</b>, you can download the full miRNA counts table by clicking the <b>'Download Full Table'</b> button.
            </span></li>
            <li><span>
            You may also filter the table using the <b>'Search text'</b> fields for each column.
            </span></li>
            <li><span>
            The original or filtered version of the current Counts Table page can be downloaded by clicking the <b>'Save Page as...'</b> button.
            </span></li>
	    <li><span>
	    You can get the most expressed miRNAs (over a user specified threshold compared to all counts) through the <b>miRNA expression profiler</b> utility.<br/>
	    </span></li>
	    <img src='images/help/png/expr_profiler.png' style='width:35%;height:auto; border:1px solid #ccc;'>
            </ul>
	    <br/>
	    	<center>
		<a href="#header_top">Back to Top</a>
		</center>
	</div>

        <br/><br/><br/>

	<div class='adv_mod_search_category' style='width:98%'>
	<a href="search.php">Advanced search</a> tab
	</div>
	<div id='adv_search_help' style="background-color:#fafcfa; padding:10px; margin-bottom: 20px; border-radius:0px; border:1px solid grey">
	    <style>
		li span { padding-left: 10px; line-height: 2em; }
	    </style>
	<ul>
	 	<li><span>
		<b>miRNA expression atlas</b><br/> 
		- Display the expression profiles for a list of input miRNAs: 
		</span></li>
		<img src='images/help/png/mxa_mirs_search_1.png' style='width:80%;height:auto; border:1px solid #ccc;'>
		<br/><br/><br/>
		- Retrieve the list with the most highly expressed miRNAs for a specific condition (e.g. tissue/disease):
		<img src='images/help/png/mxa_mirs_search_2.png' style='width:80%;height:auto; border:1px solid #ccc;'>
		<br/><br/><br/>
		- Display individual expression profiles for a particular miRNA across all miratlas datasets:
		<img src='images/help/png/mxa_mirs_search_3.png' style='width:80%;height:auto; border:1px solid #ccc;'>
		<br/><br/>
		<li><span>
		<b>Search for Datasets</b>:<br/>
		search in the <i>miratlas</i> database for any registered datasets based on their name or associated condition.
		</span></li>
        </ul>
	<br/>
		<center>
		<a href="#header_top">Back to Top</a>           
		</center>
	</div>

	<br/><br/><br/>

	<div class='adv_mod_search_category' style='width:98%'>
	<a href="modifications.php">Modification analysis</a> tab
	</div>
	        <div id='mods_analysis_help' style="background-color:#fafcfa; padding:10px; margin-bottom: 20px; border-radius:0px; border:1px solid grey">
		<style>
		li span { padding-left: 10px; line-height: 2em; }
		</style>
		<ul>
		<li><span>
		<b>Modification Stats by: 'miRNA'</b>: display aggregate modifications profile for a particular miRNA or set of miRNAs across a specific condifition (e.g. tissue or disease).
		</span></li>
		<br/>
		<img src='images/help/png/custom_mir_mod_profile.png' style='width:80%;height:auto; border:1px solid #ccc;'>
		<br/><br/>
		<li><span>
		<b>Modification Stats by: 'modification pattern'</b>: display table with modification counts for a particular modification pattern across a specific condifition (e.g. tissue or disease).
		</span></li>
		<br/>
		<img src='images/help/png/custom_pattern_mod_profile.png' style='width:80%;height:auto; border:1px solid #ccc;'>
		<br/><br/>
		<li><span>		
		<b>Overall Modification Stats by Dataset / Sample properties</b>: display modification ratios for all or selected datasets based on the modification end (5p/3p) and the modification type (modification / ADAR edit / SNP).
		</span></li>
		<br/>
		<img src='images/help/png/all_mods_profile_1.png' style='width:80%;height:auto; border:1px solid #ccc;'>
		<br/><br/>
		</ul>
			<center>
			<a href="#header_top">Back to Top</a>           
			</center>
		</div>

		<br/><br/>

<div style='padding-top:150px;'>
</div>

        <?php include('footer.php') ?>

</div>
    </body>
</html>
