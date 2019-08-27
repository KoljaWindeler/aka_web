<?php
$heute = mktime(0,0,0,date("m",time()),date("d",time()),date("Y"));
$gestern = $heute - 86400;
$jetzt = time();
$db_counter="aka_counter";


if(! $_SESSION['counter'])
	{
		$mysqli->query("INSERT INTO `".$db_counter."` (time,typ) VALUES ($jetzt,'0')");
		$_SESSION['counter'] = $jetzt;
	};

if(mysqli_num_rows($mysqli->query("SELECT time,typ FROM `".$db_counter."` WHERE typ<>'9' AND time<$gestern")) > 200)
	{
	$alte_c = mysqli_num_rows($mysqli->query("SELECT time,typ FROM `".$db_counter."` WHERE time < '$gestern' AND typ='0'"));
	list($alte_ges) = mysqli_num_rows($mysqli->query("SELECT time,typ FROM `".$db_counter."` WHERE typ='9'"));
	$neue_ges=$alte_ges + $alte_c;
	$mysqli->query("UPDATE `".$db_counter."` SET time = '$neue_ges' WHERE typ='9'");
	$mysqli->query("DELETE FROM `".$db_counter."` WHERE time < '$gestern' AND typ='0'");
	};

$Allelines=mysqli_num_rows($mysqli->query("SELECT time,typ FROM `".$db_counter."` WHERE typ<>'9'"));
list($grossezahl) = mysqli_fetch_row($mysqli->query("SELECT time FROM `".$db_counter."` WHERE typ='9'"));
$ges=$Allelines+$grossezahl;

//backup
if(empty($grossezahl)) {$mysqli->query("INSERT INTO `".$db_counter."` (time,typ) VALUES ('0','9')"); }


$gerade = $jetzt - 3600*2;
$heute_db = mysqli_num_rows($mysqli->query("SELECT * FROM `".$db_counter."` WHERE time > '$heute' AND typ='0'"));
$gestern_db = mysqli_num_rows($mysqli->query("SELECT * FROM `".$db_counter."` WHERE time > '$gestern' AND time < '$heute' AND typ<>'9'"));
$gerade_db = mysqli_num_rows($mysqli->query("SELECT * FROM `".$db_counter."` WHERE time > '$gerade' AND typ<>'9'"));
			
echo '<div class="little"><a href="index.php?mod=counter&mode=heute&'.SID.'">Heute</a> gesamt: '.$heute_db.' | In den letzen 2 Std: '.$gerade_db.' | <a href="index.php?mod=counter&mode=gestern&'.SID.'">Gestern</a> gesamt: '.$gestern_db.' | Gesamt: '.$ges.'</div>';
?>
