<html>
    <head>
        <meta charset="utf-8" />

        <link href="css/main.css" type="text/css" rel="stylesheet">
        <link href="./assets/DataTables-1.10.4/media/css/jquery.dataTables.min.css" rel="stylesheet"> 
        <link href="./assets/DataTables-1.10.4/extensions/ColVis/css/dataTables.colVis.css" rel="stylesheet">
        <link href="./assets/DataTables-1.10.4/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet">

        
        <script type="text/javascript" src="./assets/jquery.min.js"></script> 
        <script type="text/javascript" src="./assets/DataTables-1.10.4/media/js/jquery.dataTables.min.js"></script> 
        <script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/ColVis/js/dataTables.colVis.min.js"></script>
        <script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
        <!-- <script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/dataTables.bootstrap.js"></script> -->

        <?php
            $datasets_str = $_GET['acc_num'];

            session_start();
            $_SESSION['acc_num'] = $datasets_str;
            $datasets_str = preg_replace('/\s+/', '', $datasets_str);
            $datasets_str = preg_replace('/[\\\<>;\/\[\]]/', '', $datasets_str);
            //$datasets_arr=explode(",",$datasets_str);
        ?>
            
        
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


            $(document).ready(function() {

                var dt = $('#example').dataTable( {
                        "dom": 'T<"clear">lrtip',
                        /*"oSearch": "sSearch": "a", "bRegex":true, "bSmart": false},    
                        "searchCols": { 
                            "search" : {
                                "regex": true,
                                "smart": false,
                            }
                        },*/    
                        "tableTools": {
                            "aButtons": 
                                [
                                {
                                    "sExtends":    "collection",
                                    "sButtonText": "Save Page as...",
                                    "aButtons":    [ "csv", "xls", "pdf" ],
                                    "bFooter": false,
                                },
                                {
                                    "sExtends":    "download",
                                    "sButtonText": "Download Full Table",
                                    "aButtons":    [ "csv" ],
                                    "bFooter": false,
                                    "sUrl": 'generate_csv.php?acc_num=<?php echo $datasets_str ?>',
                                }
                                ],
                            "sSwfPath": "./assets/DataTables-1.10.4/extensions/TableTools/swf/copy_csv_xls_pdf.swf"
                        },    
                        "processing": true,
                        "scrollCollapse": true,
                        "iDisplayLength": 15,
                        "scrollY": 700,
                        "aLengthMenu": [[15, 30, 60, -1], [15, 30, 60, "All"]],
                        "caseInsensitive": true,
                        "searching": true,
                        "bFilter": false,   
                        "serverSide": true,
                        "ajax": 'server_side/scripts/show_table_serverproc.php?acc_num=<?php echo $datasets_str ?>', 
                        "deferRender": true,
                        "order": [[ 10, "desc" ]],
                        "columns": [
                            { "width": "15%" },
                            { "width": "19%" },
                            null,
                            null,
                            { "width": "15%" },
                            null,
                            null,
                            null,
                            null,
                            null,
                            null
                        ]
                } );

                
                $('input.global_filter').on( 'keyup click', function () {
                    filterGlobal();
                } );
                     
                $('input.column_filter').on( 'keyup click', function () {
                    filterColumn( $(this).parents('tr').attr('data-column') );
                } );

            } );



            function filterGlobal () {
                $('#example').DataTable().search(
                    $('#global_filter').val()//,
                    //$('#global_regex').prop('checked'),
                    //$('#global_smart').prop('checked')
                ).draw();
            }
             
            function filterColumn ( i ) {

                $('#example').DataTable().column( i ).search(
                    $('#col'+i+'_filter').val()//,
                    //$('#col'+i+'_regex').prop('checked'),
                    //$('#col'+i+'_smart').prop('checked')
                ).draw();
            }


            function clear_filter_form(){
                var elems = document.getElementsByClassName('column_filter');
                for (var i = 0; i < elems.length; i++) {
                    elems[i].value = "";
                }
            }

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

        <?php include 'mysql_connect.php'; ?>
        
        <br />

     

        <div class='single_dataset_table_title'>
        <div style="float:left; text-align:left;"><b>miRNA expression data for: </b><a href="/enright-dev/miratlas/show_table.php?acc_num=<?php echo $datasets_str?>"><?php echo $datasets_str?></a></b>&nbsp;&nbsp;<span class='ref_link'></div><div style="float:center; text-align:right;"><a href='browse_all_datasets.php'>View all datasets</a></div>
        </div>
        
        
        <div id='index_body' class='table_style'>
    
        <table cellpadding="1" cellspacing="0" border="0" bgcolor=#f7f5f2 style="border: 2px solid grey; box-shadow: 4px 4px 2px #888888;
    border-radius: 10px; padding-left: 20px; padding-right: 15px; padding-bottom: 5px; padding-top: 5px; width: 55%; margin: 0 auto 2em auto;">
        <thead>
            <tr>
                <th><h3>Filter</h3></th>
                <th><h3>Search text</h3></th>
            </tr>
        </thead>

        <tbody>
            <tr id="filter_global">
                <td>Global search</td>
                <td align="center"><input type="text" class="global_filter" id="global_filter"></td>
            </tr>
            <tr id="filter_col1" data-column="1">
                <td>Column - <b>miRNA id</b></td>
                <td align="center"><input type="text" class="column_filter" id="col1_filter"></td>
            </tr>
            <tr id="filter_col2" data-column="2">
                <td>Column - <b>Mod Type</b>&nbsp;&nbsp;&nbsp;<i>(mod, adar, snp)</i></td>
                <td align="center"><input type="text" class="column_filter" id="col2_filter"></td>
            </tr>
            <tr id="filter_col3" data-column="3">
                <td>Column - <b>Arm</b>&nbsp;&nbsp;&nbsp;<i>(5p, 3p)</i></td>
                <td align="center"><input type="text" class="column_filter" id="col3_filter"></td>
            </tr>
            <tr id="filter_col4" data-column="4">
                <td>Column - <b>Pattern</b></td>
                <td align="center"><input type="text" class="column_filter" id="col4_filter"></td>
            </tr>
            <tr id="filter_col5" data-column="5">
                <td>Column - <b>Position</b></td>
                <td align="center"><input type="text" class="column_filter" id="col5_filter"></td>
            </tr>
            <tr id="filter_col6" data-column="6">
                <td>Column - <b>Internal Mod Type</b>&nbsp;&nbsp;&nbsp;<i>(internal_adar, internal_snp)</i></td>
                <td align="center"><input type="text" class="column_filter" id="col6_filter"></td>
            </tr>
            <tr id="filter_col7" data-column="7">
                <td>Column - <b>Internal Pattern</b></td>
                <td align="center"><input type="text" class="column_filter" id="col7_filter"></td>
            </tr>
            <tr id="filter_col8" data-column="8">
                <td>Column - <b>Internal Position</b></td>
                <td align="center"><input type="text" class="column_filter" id="col8_filter"></td>
            </tr>
            <tr id="filter_col9" data-column="9">
                <td>Column - <b>Doubled</b>&nbsp;&nbsp;&nbsp;<i>(1: true, 0: false)</i></td>
                <td align="center"><input type="text" class="column_filter" id="col9_filter"></td>
            </tr>
            <tr id="filter_col10" data-column="10">
                <td></td>
<!--<td style='text-align:center'><input type='submit' onclick='clear_filter_form()' value='Reset form' name='clear_filter_form_btn'></td>-->
            </tr>
        </tbody>
        </table>

        <table id="example" class="display" cellspacing="0" width="80%">
        <thead>
            <tr>
                <th>Accession Number</th>
                <th>miRNA Id</th>
                <th>Mod Type</th>
                <th>Arm</th>
                <th>Pattern</th>
                <th>Position</th>
                <th>Internal Mod Type</th>
                <th>Internal Pattern</th>
                <th>Internal Position</th>
                <th>Doubled</th>
                <th>Raw Counts</th>
            </tr>
        </thead>
    </table>
    </div>

    <br /><br /><br /><br /><br /><br />

    <?php include('footer.php') ?>    

</div>
    </body>
</html>
