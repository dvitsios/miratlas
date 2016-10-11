<?php
	$uuid = $_GET['uuid'];

	header('Content-Description: File Transfer');
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$uuid.artefacts.txt");
	header('Content-Transfer-Encoding: binary');
	header('Connection: Keep-Alive');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$fullPath = "data/".$uuid."_artefacts_table.txt";

	if ($fd = fopen ($fullPath, "r")) {
		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);

		while(!feof($fd)) {
     			$buffer = fread($fd, 2048);
        		echo $buffer;
    		}
	}

	fclose($fd);
	exit;
?>
