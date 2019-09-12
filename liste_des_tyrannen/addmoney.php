<?php
if($_SESSION['session_user_typ']<>2) { exit('falsches passwort'); };
##################### security ################################
##################### eingabe auswerten #########################
$time=time();
list($max)=mysqli_fetch_row($mysqli->query("SELECT `id` FROM `aka_id` ORDER BY `id` DESC LIMIT 0,1"));
for($a=0;$a<=$max;$a++){	$changed[$a]=0; }

if(isset($_POST['senden'])) {
	for($a=0;$a<=$max;$a++){
		if(!empty($_POST['addmoney_'.$a]) AND $_POST['addmoney_'.$a] <> '0') {
			$money=str_replace(",",".",$_POST['addmoney_'.$a]);
			$changed[$a]=1;
			if(!$mysqli->query( "INSERT INTO `aka_money` (`id` ,`value`, `date`) VALUES ('".$a."', '".$money."', ".$time.")" )){
				echo 'ohoh';
				};
			};
		};
	};
##################### eingabe auswerten #########################
# email
include('email.php');
# email
##################### daten auswerten #########################
include('collect_data.php');
$daten=kolja_sort($daten,1);
$daten=array_reverse($daten);
##################### daten auswerten #########################
##################### daten anzeigen #########################
tab_go("100%",250,'left','&Uuml;bersichtstabelle');

echo	'<table width="100%"  class="singletable"><tr><th width="10">#</th><th>Name</th><th>Letzte Einzahlung</th><th>Aktuelles Guthaben</th><th>Aufbuchungsbetrag</th></tr><form name="addbill" method="POST" action="index.php?mod=money&'.SID.'">';
for ($b=0;$b<=$max_user;$b++) {
	if($b%2==1) { $bg_color=' class="gray"'; } else { $bg_color=''; };
	$c=$b+1;
	echo '<tr><td '.$bg_color.'>'.$c.'</td><td '.$bg_color.'>'.$daten[$b][1].'</td><td '.$bg_color.'>'.$daten[$b][5].' &euro; ('.date("d.m.y",$daten[$b][4]).')</td><td '.$bg_color.'>'.$daten[$b][8].' &euro;</td><td '.$bg_color.'><input type="text" name="addmoney_'.$daten[$b][0].'"></td></tr>';
	};
echo '</table><input type="submit" value="Absenden" name="senden"  onclick="return confirmLink(this, \'Wirklich aufbuchen,Konto f&uuml;llen?\')">';
##################### daten anzeigen #########################
tab_end();
echo '</td></table>'.impressum().'</body></html>';
?>