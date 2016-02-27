<?php
include('scripts/config.php');

$db_filename=$_GET['file'];
list($clicks)=mysql_fetch_row(mysql_query("SELECT `clicks` FROM aka_pro_clicks WHERE `Filename`='".basename($db_filename)."' LIMIT 0,1;"));
if(mysql_num_rows(mysql_query("SELECT `clicks` FROM aka_pro_clicks WHERE `Filename`='".basename($db_filename)."' LIMIT 0,1;"))==0) { 
	mysql_query("INSERT INTO `d00b711e`.`aka_pro_clicks` (`ID` ,`Filename` ,`clicks`) VALUES (NULL , '".basename($db_filename)."', '1');");
	}
else {
	$clicks++;
	mysql_query("UPDATE `d00b711e`.`aka_pro_clicks` SET `clicks`='".$clicks."' WHERE `Filename`='".basename($db_filename)."';");
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