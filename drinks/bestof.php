<?php
include('scripts/config.php');
$abfrage="SELECT `ID`, `Name`,`EMAIL`, `LAST_MAIL` FROM `aka_id`";
$erg=mysql_db_query($db,$abfrage,$verbindung);
$a=0;
while(list($db_id,$db_name,$db_email,$db_email_pol) = mysql_fetch_row($erg)) {
	echo $db_name.":";
	$abfrage2="SELECT SUM(value) FROM `aka_verbrauch` WHERE `id`=".$db_id." and date>1355119200";
	$erg2=mysql_db_query($db,$abfrage2,$verbindung);
	list($db_verbrauch_erg) = mysql_fetch_row($erg2);
	echo $db_verbrauch_erg."<br>";
}
?>
