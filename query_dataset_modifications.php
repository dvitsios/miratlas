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

        <div style="padding:10px; padding-bottom:56px;">
        <?php include('run_query_dataset_modifications.php');?>
        </div>

        <div style="position: absolute; bottom: 0; height: 56px; width: 100%">  
        <?php include('footer.php') ?>
        </div>

</div>
    </body>
</html>
