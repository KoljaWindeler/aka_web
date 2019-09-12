<?php
include('scripts/config.php');
$abfrage="SELECT `ID`, `Name`,`EMAIL`, `LAST_MAIL` FROM `aka_id`";
$db= mysqli_select_db($verbindung,$db);
$erg=$db->query($db,$abfrage,$verbindung);
$a=0;
while(list($db_id,$db_name,$db_email,$db_email_pol) = mysqli_fetch_row($erg)) {
	echo $db_name.":";
	$abfrage2="SELECT SUM(value) FROM `aka_verbrauch` WHERE `id`=".$db_id." and date>1355119200";
	$erg2=$db->query($abfrage2);
	list($db_verbrauch_erg) = mysqli_fetch_row($erg2);
	echo $db_verbrauch_erg."<br>";
}
?>
