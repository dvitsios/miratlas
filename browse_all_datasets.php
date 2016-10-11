<?php 

    $web_app_name = "MIRATLAS"; 
    exec('perl ./cgi-bin/test.pl');

?>

<?php


	include("server_conf.php");
?>


<html>

    <head>
        <meta charset="utf-8" />
        <link href="css/main.css" type="text/css" rel="stylesheet">
        <link href="./css/jquery.dataTables.min.css" rel="stylesheet">

        <script type="text/javascript" src="./assets/jquery.min.js"></script> 
        <script type="text/javascript" src="./assets/jquery.dataTables.min.js"></script> 


        <script>
           $(document).ready(function(){
                $('#myTable').DataTable( {
                    searching: true,
                    paging: true,
                    stateSave: false,
                    scrollCollapse: true,
                    scrollY: 400,
                    caseInsensitive: true,
                }
                );
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

<!--
        <div id='all_datasets_browse_form'> 
        <b>- Get miRNA expression data for a dataset</b>
        <br />
        <br />
        <form action="show_table.php" method="get">
        Enter a dataset name:<input name="acc_num" type="text" value="<?php echo $acc_num ?>" />
            <input type="submit" value="Browse">
        </form>
        </div>
-->

        <br/>

        <div style="text-align:center; font-size:22px;"><b>Browse all datasets</b><br/><br/></div>
        <div id="all_datasets_table" class="table_style">
        <?php include 'show_all_datasets_table.php'?>
        </div>




        <br/><br/><br/><br/><br/>



        <?php include('footer.php') ?>

        <?php
            if($_POST){
                //session_start(); // must be called before data is sent
                //$_SESSION['acc_num'] = $_POST['acc_num'];
                //include('show_table.php'); 
            }
        ?>
</div>
    </body>
</html>
