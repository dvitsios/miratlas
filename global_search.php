<html>

    <head>
        <meta charset="utf-8" />
        <link href="css/main.css" type="text/css" rel="stylesheet">

        <script type="text/javascript" src="./assets/jquery.min.js"></script> 

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
        <?php 
            $_GET['global_search'] = $_POST['global_search'];
            include('global_run_search.php');?>
        </div>

        <?php include('footer.php') ?>
</div>

    </body>
</html>
