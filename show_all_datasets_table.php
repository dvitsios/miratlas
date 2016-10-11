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

		var taxa_hash = new Object();
		taxa_hash['Homo sapiens'] = 9606;
		taxa_hash['Mus musculus'] = 10090;

                var dt = $('#example').dataTable( {
                        "processing": true,
                        "scrollCollapse": true,
                        "scrollY": 700,
                        "aLengthMenu": [[20, 40, 60, -1], [20, 40, 60, "All"]],
                        "sClass": 'center',
                        "iDisplayLength": 20,
                        "caseInsensitive": true,
                        "searching": true,    
                        "search": {
                            "regex": true
                        },
                        "serverSide": true,
                        "ajax": 'server_side/scripts/show_all_datasets_serverproc.php', 
                        "deferRender": true,
                        "order": [[ 0, "asc" ]],
                        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                            //$('td:eq(1)', nRow).html('<a href="/enright-dev/miratlas/show_table.php">asdf</a>');
                            //'<a href="/enright-dev/miratlas/show_table.php?acc_num='.'PRJNA190003'.'">temp</   a>'
                            $('td:eq(0)', nRow).html('<a href="/enright-dev/miratlas/show_table.php?acc_num='+aData[0]+'">'+aData[0]+'</a>');
                            $('td:eq(1)', nRow).html('<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id='+taxa_hash[aData[1]]+'">'+aData[1]+'</a>');
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
<!--            <th class="table_col">NCBI Taxonomy ID</th> -->
                <th>Species</th>
                <th>Description</th>
            </tr>
        </thead>
        </table>
        </div>

    </body>
</html>
