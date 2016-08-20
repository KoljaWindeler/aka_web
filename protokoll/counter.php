<?php
$heute = mktime(0,0,0,date("m",time()),date("d",time()),date("Y"),time());
$gestern = $heute - 86400;
$jetzt = time();
$db_counter="aka_counter";

if(! $_SESSION['counter'])
	{
	mysql_query("INSERT INTO `".$db_counter."` (time,typ) VALUES ($jetzt,'0')");
	$_SESSION['counter'] = $jetzt;
	};

if(mysql_num_rows(mysql_query("SELECT time,typ FROM `".$db_counter."` WHERE typ<>'9' AND time<$gestern")) > 200)
	{
	$alte_c = mysql_num_rows(mysql_query("SELECT time,typ FROM `".$db_counter."` WHERE time < '$gestern' AND typ='0'"));
	list($alte_ges) = mysql_fetch_row(mysql_query("SELECT time,typ FROM `".$db_counter."` WHERE typ='9'")); 
	$neue_ges=$alte_ges + $alte_c;
	mysql_query("UPDATE `".$db_counter."` SET time = '$neue_ges' WHERE typ='9'");
	mysql_query("DELETE FROM `".$db_counter."` WHERE time < '$gestern' AND typ='0'");
	};

$Allelines=mysql_num_rows(mysql_query("SELECT time,typ FROM `".$db_counter."` WHERE typ<>'9'"));
list($grossezahl) = mysql_fetch_row(mysql_query("SELECT time,typ FROM `".$db_counter."` WHERE typ='9'")); 
$ges=$Allelines+$grossezahl;

$gerade = $jetzt - 3600*2;
$heute_db = mysql_num_rows(mysql_query("SELECT * FROM `".$db_counter."` WHERE time > '$heute' AND typ='0'"));
$gestern_db = mysql_num_rows(mysql_query("SELECT * FROM `".$db_counter."` WHERE time > '$gestern' AND time < '$heute' AND typ<>'9'"));
$gerade_db = mysql_num_rows(mysql_query("SELECT * FROM `".$db_counter."` WHERE time > '$gerade' AND typ<>'9'"));
			
echo '<div class="little"><a href="index.php?mod=counter&mode=heute&'.SID.'" class="little">Heute</a> gesamt: '.$heute_db.' | In den letzen 2 Std: '.$gerade_db.' | <a href="index.php?mod=counter&mode=gestern&'.SID.'"  class="little">Gestern</a> gesamt: '.$gestern_db.' | Gesamt: '.$ges.'</div>';
?>
