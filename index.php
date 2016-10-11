<?php 
    $web_app_name = "miratlas"; 
    
    exec('perl ./cgi-bin/test.pl');

    // may have to include '/usr/bin/' for the Rscript path 
    exec('Rscript ./cgi-bin/test.R');

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

        <div id="homepage_descr">
        <ul>
        <li>
        <b>Miratlas</b> is a comprehensive catalogue of <b>miRNA expression data & modifications</b> from already published datasets (derived from <a href="http://www.ebi.ac.uk/ena">ENA</a> and <a href="http://www.ebi.ac.uk/arrayexpress">ArrayExpress</a>). <br />
        </li>
        <br />
        <li>    
        <b>Miratlas</b> release 1.0 contains 52 analysed datasets from human and mouse samples.
        </li>
        <br />
        <li>
        Along with the quantification of plain miRNA counts, it incorporates <b>miRNA modification data</b> for each dataset.
        </li>
        </div>

        <br/>


<!-- miRNAs expression search form -->
        <div style='font-weight: bold; font-size:16px; border:1px groove #D8D8D8; padding: 4px;height: 20px; width: 529px; background-color: #EFF4F9; padding-left:10px'>Search for miRNAs expression</div>
        <div class='search_form' id='index_mirna_search_form' style='background-color:#fefefe; margin-bottom: 30px'>
        <form action="mirna_search_results.php" method="get">
        <div style="padding-bottom:8px;">Insert a miRNA ID (or list of IDs):</div>
            <input name="mirna_search" type="text" value="<?php echo $mirna_global_search ?>" style='width:90%' font-size="22px"/>
            <input type="submit" value="Go">
            <span style="float: left; text-align:left;  margin-top: 3px; font-size:15px; padding:4px; font-family:Palatino">e.g.: <i><a href="/enright-dev/miratlas/mirna_search_results.php?mirna_search=hsa-let-7a-5p">hsa-let-7a-5p</a>, <a href="/enright-dev/miratlas/mirna_search_results.php?mirna_search=hsa-miR-122-5p">hsa-miR-122-5p</a></i></span>
            <span style="float: right; text-align:left; padding:4px;  margin-top: 3px; margin-right:14px;"><a href="search.php#expr_search" style="font-size:15px">Advanced search</a></span>
        </form>
        </div>


<!-- Datasets search form -->
        <div style='font-weight: bold; font-size:16px; border:1px groove #D8D8D8; padding: 4px;    height: 20px; width: 529px; background-color: #EFF4F9; padding-left:10px'>Search for datasets</div>
        <div class='search_form' id='index_search_form' style='background-color:#fefefe'>
        <form action="global_search_results.php" method="get">
        <div style="padding-bottom:8px;">Insert a dataset name or annotation term:</div>
            <input name="global_search" type="text" value="<?php echo $global_search ?>" style='width:90%' font-size="22px"/>
            <input type="submit" value="Go">
            <span style="float: left; margin-top: 3px; text-align:left; font-size:15px; padding:4px; font-family:Palatino">e.g.: <i><a href="/enright-dev/miratlas/global_search_results.php?global_search=PRJNA190003">PRJNA190003</a>, <a href="/enright-dev/miratlas/global_search_results.php?global_search=liver">liver</a>, <a href="/enright-dev/miratlas/global_search_results.php?global_search=GEO">GEO</a></i></span>
            <span style="float: right; text-align:left; margin-top: 3px; padding:4px; margin-right:14px;"><a href="search.php#data_search" style="font-size:15px">Advanced search</a></span>
        </form>
        </div>

        <br/>


        <br/><br/><br/><br/><br/><br/><br/>
        

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
