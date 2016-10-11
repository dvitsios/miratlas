<html>
    <head>
        <meta charset="utf-8" />

        <link href="./assets/DataTables-1.10.4/media/css/jquery.dataTables.min.css" rel="stylesheet"> 
        <link href="./assets/DataTables-1.10.4/extensions/ColVis/css/dataTables.colVis.css" rel="stylesheet">
        <link href="./assets/DataTables-1.10.4/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet">

        
        <script type="text/javascript" src="./assets/jquery.min.js"></script> 
        <script type="text/javascript" src="./assets/DataTables-1.10.4/media/js/jquery.dataTables.min.js"></script> 
        <script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/ColVis/js/dataTables.colVis.min.js"></script>
        <script type="text/javascript" src="./assets/DataTables-1.10.4/extensions/TableTools/js/dataTables.tableTools.min.js"></script>

        <script>
            

            $(document).ready(function() {

                var dt = $('#example').dataTable( {
                        /*"dom": 'C<"clear">lfrtip',
                        "colVis": {
                            "buttonText": "Customize table",
                            "label": function ( index, title, th ) {
                                return (index+1) +'. '+ title;
                            }
                        },*/
                        "processing": true,
                        "scrollCollapse": true,
                        "scrollY": 500,
                        "aLengthMenu": [[15, 30, 60, -1], [15, 30, 60, "All"]],
                        "sClass": 'center',
                        "iDisplayLength": 15,
                        "caseInsensitive": true,
                        "searching": true,    
                        "serverSide": true,
                        "ajax": 'server_side/scripts/show_all_datasets_serverproc.php', 
                        "deferRender": true,
                        "order": [[ 0, "asc" ]],
                        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                            //$('td:eq(1)', nRow).html('<a href="/enright-dev/miratlas/show_table.php">asdf</a>');
                            //'<a href="/enright-dev/miratlas/show_table.php?acc_num='.'PRJNA190003'.'">temp</   a>'
                            $('td:eq(0)', nRow).html('<a href="/enright-dev/miratlas/show_table.php?acc_num='+aData[0]+'">'+aData[0]+'</a>');
                            $('td:eq(1)', nRow).html('<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id='+aData[1]+'">'+aData[1]+'</a>');
                            return nRow;
                        },
                } );

            } );

        </script>
    </head>

    <body>

        <div id="all_datasets_table">
        <table id="example" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="table_col">Accession Number</th>
                <th class="table_col">NCBI Taxonomy ID</th>
                <th>Species</th>
                <th>Description</th>
            </tr>
        </thead>
        </table>
        </div>

    </body>
</html>
