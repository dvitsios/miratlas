<?php

#$uuid = $_GET['uuid'];
$uuid = 'E-GEOD-15229';

?>


<html>
<head>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script type="text/javascript" src="./assets/c3.js"></script>
<script type="text/javascript" language="javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>

<link rel="stylesheet" type="text/css" href="./assets/c3.css">

</head>

<body>

<div id="global_mods_profile" style='height:300px; width:auto'></div>


<script>

var uuid = "<?php echo $uuid; ?>";


// global profile
var global_mods_profile_path = './viz/'+uuid+'.global_mods_profile_data.csv';

var chart = c3.generate({
        bindto: '#global_mods_profile',
        data: {
            x : 'mirna_index',
            url: global_mods_profile_path,
            type: 'bar',
            groups: [
                ['U', 'A', 'C', 'G', 'G_adar', 'A_snp', 'U_snp', 'G_snp', 'C_snp']
            ]
        },
        axis: {
            x: {
            type: 'category' // this needed to load string x value
            }
        },
        grid: {
                x: {
                        show: true
                },
                y: {
                        show: true
                }
        }
});

</script>




</body>
</html>


