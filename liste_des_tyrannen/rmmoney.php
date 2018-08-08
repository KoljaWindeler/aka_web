<?php
if($_SESSION['session_user_typ']<>2) { exit('falsches passwort'); };
##################### security ################################
##################### daten annehmen ###########################
$time=time();
list($max)=mysql_fetch_row(mysql_query("SELECT `id` FROM `aka_id` ORDER BY `id` DESC LIMIT 0,1"));
for($a=0;$a<=$max;$a++){	$changed[$a]=0; }

if(isset($_POST['senden'])) {
	for($a=0;$a<=$max;$a++){
		if(!empty($_POST['addbill_'.$a]) AND $_POST['addbill_'.$a] > 0) {
			if($_POST['send_mail']=='on'){ $changed[$a]=1; } // hier ist a die id des users 
			else { echo 'Es wurden absichtlich keine Mails verschickt!'; }
			if(!mysql_query( "INSERT INTO `aka_verbrauch` (`id` ,`value`, `date`) VALUES ('".$a."', '".$_POST['addbill_'.$a]."', ".$time.")" )){
				echo 'ohoh';
				};
			};
		};
	};
##################### daten annehmen ###########################
# email
include('email.php');
# email
##################### daten sammeln ############################
include('collect_data.php');
$daten=kolja_sort($daten,9);
$daten=array_reverse($daten);
##################### daten sammeln ############################
##################### anzeige ############################
tab_go("100%",250,'left','&Uuml;bersichtstabelle');
echo	'<table width="100%"  class="singletable"><tr><th width="10">#</th><th>Name</th><th>Striche letzter Abrechnung</th><th>Aktuelles Guthaben</th><th>Getr&auml;nkestriche</th></tr><form name="addbill" method="POST" action="index.php?mod=bill&'.SID.'">';
$summe_geld=0;
$remember_user=array();
$summe_getraenke=0;
for($b=0;$b<5;$b++) {
	if($b%2==1) { $bg_color=' class="gray"'; } else { $bg_color=''; };
	$c=$b+1;
	array_push($remember_user,$daten[$b][0]);
	$summe_geld+=$daten[$b][8];
	$summe_getraenke+=$daten[$b][3];
	echo '<tr><td'.$bg_color.'>#'.$c.'</td><td'.$bg_color.'>'.$daten[$b][1].'</td><td'.$bg_color.'>'.$daten[$b][6].'  ('.date("d.m.y",$daten[$b][7]).')</td><td'.$bg_color.'>'.$daten[$b][8].' &euro;</td><td'.$bg_color.'><input type="text" name="addbill_'.$daten[$b][0].'"></td></tr>';
	};
##################### daten sammeln ############################
include('collect_data.php');
$daten=kolja_sort($daten,11);
$daten=array_reverse($daten);
##################### daten sammeln ############################
$i=0;
for($b=0;$b<count($daten);$b++){
	if(!in_array($daten[$b][0],$remember_user)){
		if($i%2==0) { $bg_color=' class="gray"'; } else { $bg_color=''; };
		$c=$i+1;
		array_push($remember_user,$daten[$b][0]);
		$summe_geld+=$daten[$b][8];
		$summe_getraenke+=$daten[$b][3];
		echo '<tr><td'.$bg_color.'>'.$c.'</td><td'.$bg_color.'>'.$daten[$b][1].'</td><td'.$bg_color.'>'.$daten[$b][6].'  ('.date("d.m.y",$daten[$b][7]).')</td><td'.$bg_color.'>'.$daten[$b][8].' &euro;</td><td'.$bg_color.'><input type="text" name="addbill_'.$daten[$b][0].'"></td></tr>';
		$i++;
	};
};

$gewinn=$summe_getraenke*0.15;
$durchsatz_aus=$summe_getraenke*0.65;
$durchsatz_ein=$summe_getraenke*0.80;
echo '</table>
<br>Sollstand: '.$summe_geld.' &euro; ( Konto + Best&auml;nde in der AKA )<br>
<!--Getr&auml;nkedurchsatz gesamt: '.$summe_getraenke.' Flaschen<br>
Gelddurchsatz gesamt etwa: '.$durchsatz_aus.' &euro; zu S&ouml;ffker<br>
Gelddurchsatz gesamt etwa: '.$durchsatz_ein.' &euro; eingenommen<br>
Gewinn theoretisch: '.$gewinn.' &euro;<br>-->
<input type="checkbox" name="send_mail" checked>eMail senden? &nbsp;  &nbsp; 
<input type="submit" value="Absenden" name="senden" onclick="return confirmLink(this, \'Wirklich abziehen,Kosten verursachen?\')">';
##################### anzeige ############################
tab_end();
echo '</td></table>'.impressum().'</body></html>';
?>