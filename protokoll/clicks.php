<?php
include('../a_common_scripts/config.php');

$db_filename=$_GET['file'];
list($clicks)=mysqli_fetch_row($mysqli->query("SELECT `clicks` FROM aka_pro_clicks WHERE `Filename`='".basename($db_filename)."' LIMIT 0,1;"));
if(mysqli_num_rows($mysqli->query("SELECT `clicks` FROM aka_pro_clicks WHERE `Filename`='".basename($db_filename)."' LIMIT 0,1;"))==0) {
	$mysqli->query("INSERT INTO aka_pro_clicks (`ID` ,`Filename` ,`clicks`) VALUES (NULL , '".basename($db_filename)."', '1');");
	}
else {
	$clicks++;
	$mysqli->query("UPDATE `aka_pro_clicks` SET `clicks`='".$clicks."' WHERE `Filename`='".basename($db_filename)."';");
	};
	

header("Location: ".$db_filename);
exit;
	
echo'	<script type="text/javascript" />
		<!--
		location.href="'.$db_filename.'";
		//-->
		</script>';
		echo'<b><font color="green">Sie werden weitergeleitet, sollte sich nichts tun klicken sie bitte <a href="'.$db_filename.'">hier</a> und aktivieren sie f&uuml;r die Zukunft Javascript</font></b>';


?>
