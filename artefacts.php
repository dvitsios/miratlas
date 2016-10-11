<html>

    <head>
        <meta charset="utf-8" />
        <link href="css/main.css" type="text/css" rel="stylesheet">

        <script type="text/javascript" src="./assets/jquery.min.js"></script> 

	<style>
		table { 
		color: #333; /* Lighten up font color */
		font-family: Helvetica, Arial, sans-serif; /* Nicer font */
		width: 640px; 
		border-collapse: 
		collapse; border-spacing: 0; 
		}

		td, th { border: 1px solid #CCC; height: 30px; } /* Make cells a bit taller */

		th {
		background: #F3F3F3; /* Light grey background */
		font-weight: bold; /* Make sure they're bold */
		}

		td {
		background: #FAFAFA; /* Lighter grey background */
		text-align: center; /* Center our text */
		}

	</style>

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
	Artefacts Index
	</div>
	<div style='background-color:#fbfbfb;  padding:20px; padding-left:20px; border:1px black solid'>
	<li><a href='#hsa_artefacts'>Human miRNA artefacts</a></li>
	<li><a href='#mmu_artefacts'>Mouse miRNA artefacts</a></li>
	<li><a href='#control_artefacts'>CONTROL</a></li>
	</div>

<br/>
<br/>


       <div id='hsa_artefacts' style='padding:10px'></div>
       <div class='adv_mod_search_category' style='width:99%; margin-bottom:0px; padding:5px '>
       Human miRNA artefacts
       </div>
       <div style='background-color:#f4f4f4; padding:8px'>
	List and coverage profiles of miRNAs that have been detected in human samples as potential artefacts. miRNAs with reads in less than 15 samples (5% of all samples) have been excluded from this analysis.
	</div>
	<br/>
	<div style='font-size:11pt; padding-bottom:4px; padding-left:5px'><b>List of human miRNA artefacts</b>
	<a href='./download_artefacts_table.php?uuid=hsa'>
	(download table)</a>
	</div>

	<table style='width:60%; text-align:center; font-size:11pt'>
	<tr>
	<th><b>#</b></th>
	<th><b>miRNA</b></th>
	<th><b>Total depth</b></th>
	<th><b>Sample hits</b></th>
	</tr>
	<?php
	
		$fh = fopen('data/hsa_artefacts.txt','r');

		$cnt = -1;
		while ($line = fgets($fh)) {
			
			$cnt++;
			if($cnt == 0){
				continue;
			}
			$vals = explode("\t", $line);

			echo "<tr>";
			echo "<td>".$cnt."</td><td><a href='http://mirbase.org/cgi-bin/mirna_entry.pl?acc=".$vals[3]."'>".$vals[0]."</td>"."<td>".$vals[1]."</td>"."<td>".$vals[2]."</td>";
			echo "</tr>";

		}
		fclose($fh);

	?>
	
	</table>
<br/>

	<div style='font-size:11pt; padding:6px; background-color:#f4f4f4'><b>Coverage profiles of human miRNA artefacts</b>
        <a href='data/Figure_S16.pdf' download="hsa_artefact_profiles.pdf">(download as pdf)</a>
	</div>

	<div style="font-size:18px; background-color:#ffffff; padding:20px; border-radius:5px; margin-bottom:30px; border:1px solid #f4ada4">
	<img src='supp_miratlas/Figure_S16.png' width='100%'>
	</div>

	<center>
        <a href="#" style="padding-bottom:20px">Back to Top</a>
	</center>

<div style="margin-bottom:50px"></div>

       <div id='mmu_artefacts' style='padding:10px'></div>
       <div class='adv_mod_search_category' style='width:99%; margin-bottom:0px; padding:5px '>
       Mouse miRNA artefacts
       </div>
       <div style='background-color:#f4f4f4; padding:8px'>
	List and coverage profiles of miRNAs that have been detected in mouse samples as potential artefacts. miRNAs with reads in less than 5 samples (5% of all samples) have been excluded from this analysis.
	</div>
	<br/>
	<div style='font-size:11pt; padding-bottom:4px; padding-left:5px'><b>List of mouse miRNA artefacts</b>
	<a href='./download_artefacts_table.php?uuid=mmu'>
	(download table)</a>
	</div>

	<table style='width:60%; text-align:center; font-size:11pt'>
	<tr>
	<th><b>#</b></th>
	<th><b>miRNA</b></th>
	<th><b>Total depth</b></th>
	<th><b>Sample hits</b></th>
	</tr>
	<?php
	
		$fh = fopen('data/mmu_artefacts.txt','r');

		$cnt = -1;
		while ($line = fgets($fh)) {
			
			$cnt++;
			if($cnt == 0){
				continue;
			}
			$vals = explode("\t", $line);

			echo "<tr>";
			echo "<td>".$cnt."</td><td><a href='http://mirbase.org/cgi-bin/mirna_entry.pl?acc=".$vals[3]."'>".$vals[0]."</td>"."<td>".$vals[1]."</td>"."<td>".$vals[2]."</td>";
			echo "</tr>";

		}
		fclose($fh);

	?>
	
	</table>
	<br/>
	<div style='font-size:11pt; padding:6px; background-color:#f4f4f4'><b>Coverage profiles of mouse miRNA artefacts</b>
	<a href='data/Figure_S17.pdf' download="mmu_artefact_profiles.pdf">
	(download as pdf)</a>
	</div>


	
	
	<div style="font-size:18px; background-color:#ffffff; padding:20px; border-radius:5px; margin-bottom:40px; border:1px solid #f4ada4">
	<img src='supp_miratlas/Figure_S17.png' width='100%'>
	</div>

	<center>
        <a href="#" style="padding-bottom:20px">Back to Top</a>
	</center>

	<div style="margin-bottom:50px"></div>

        <div id='control_artefacts' style='padding:10px'></div>
	<div class='adv_mod_search_category' style='width:99%; margin-bottom:0px; padding:5px '>
       	CONTROL for miRNA artefacts detection (Human & Mouse)
      	</div>
     	<div style='padding:6px; background-color:#f4f4f4'>
	Coverage profiles of miRNAs that are highly expressed in: (a) all 34 human datasets  and (b) all 18 mouse datasets of this study and have validation via northern blot in miRBase.  These profiles serve as the control reference for the detection of potential miRNA artefacts from the analysis of miRNA coverage profiles.
     	</div>
     	<br/>

	<div style='font-size:11pt; padding:6px; background-color:#f4f4f4'><b>Canonical coverage profiles of control miRNAs</b>
	<a href='data/Figure_S18.pdf' download="CONTROL-miRNAs_artefact_profiles.pdf">
	(download as pdf)</a>
	</div>
	<div style="font-size:18px; background-color:#ffffff; padding:20px; border-radius:5px; width:80%; border:1px solid #badfe8">
	<img src='supp_miratlas/Figure_S18.png' width='100%'>
	</div>
	<br/>
	<center>
	<a href="#" style="margin-top:20px">Back to Top</a>
	</center>
	

<br/><br/><br/><br/>

        <?php include('footer.php') ?>

    </body>
</html>
