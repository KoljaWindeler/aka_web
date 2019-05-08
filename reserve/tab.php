<?php
$options=array();
$options[0]='Grube rechts';
$options[1]='Grube links';
$options[2]='Mehrzweckarbeitsplatz';
$options[3]='linke B&uuml;hne';
$options[4]='rechte B&uuml;hne';
$options[5]='Empore 1';
$options[6]='Empore 2';
$options[7]='Empore 3';

$max_time=array();
$max_time[0]=86400*7*2;
$max_time[1]=86400*30*6;
$max_time[2]=86400*7*2;
$max_time[3]=86400*30*6;
$max_time[4]=86400*3;
$max_time[5]=86400*7*4;
$max_time[6]=86400*7*4;
$max_time[7]=86400*7*4;


$sql_done=0;
####### anfrage verarbeiten####
if(!empty($_POST['save'])){
	$temp=explode('-',$_POST['j_from_time-date']);
	$_POST['j_from_time-ts']=mktime($_POST['j_from_time-hh'],$_POST['j_from_time-mi'],0,$temp[1],$temp[2],$temp[0]);
	$temp=explode('-',$_POST['j_to_time-date']);
	$_POST['j_to_time-ts']=mktime($_POST['j_to_time-hh'],$_POST['j_to_time-mi'],0,$temp[1],$temp[2],$temp[0]);

	$stop=0;
	$recover=0;
	$reason=0;
	########### eingetragene daten checken #################
	if($_POST['j_to_time-ts']-$_POST['j_from_time-ts']>$max_time[$_POST['wo']]){
		$stop=1;
		$recover=1;
		$reason=1;
	} else {
		$query="select grund from aka_reserve where ort=\"".$options[$_POST['wo']]."\" and ((bis>".$_POST['j_from_time-ts']." and von<".$_POST['j_to_time-ts'].") OR (von<".$_POST['j_to_time-ts']." AND bis>".$_POST['j_to_time-ts'].")) AND active=1";
		$sql=mysql_query($query);
		$count=mysql_num_rows($sql);
		if($count>0){
			$stop=1;
			$recover=1;
			$reason=4;
		};
	};
	########### eingetragene daten checken #################
	if(!$stop){
		$sql="INSERT INTO `aka_reserve` (`id` ,`von`, `bis`, `ort`, `person`, `grund`, `time_create`, `ip_create`, `time_delete`, `ip_delete`, `active`) VALUES
				('', ".$_POST['j_from_time-ts'].", ".$_POST['j_to_time-ts'].", '".$options[$_POST['wo']]."', '".$_POST['name']."', '".$_POST['warum']."', ".time().", '".$_SERVER['REMOTE_ADDR']."', '','','1')";
		if(!mysql_query( $sql )){
			echo 'ohoh';
			echo '<hr>'.$sql.'<hr>';
			$recover=1;			
			$reason=3;
		} else {
			$sql_done=1;
		};
	};
} elseif(!empty($_GET['delete'])){
	$sql="UPDATE `aka_reserve` SET `active` = '0',`ip_delete`='".$_SERVER['REMOTE_ADDR']."',`time_delete`=".time()." WHERE `aka_reserve`.`id` =".$_GET['delete']." LIMIT 1 ;";
	if(!mysql_query( $sql )){
		echo 'ohoh';
		echo '<hr>'.$sql.'<hr>';
	} else {
		$info='<font color="red"><b>Erfolgreich gel&ouml;scht</b></font>';
	};		
}
####### anfrage verarbeiten####
##################### Daten sammeln und sortieren ################################
#echo "<font size=\"72\" color=\"red\"> ICH BAUE GERADE UM,FUNKTIONSAUSFALL MÖGLICH</font><br>";
tab_go("100%",250,'left','Neue Reservierung');
if($recover==1){
	$result=java_cal2('',$_POST['j_from_time-ts'],$_POST['j_to_time-ts'],time(),mktime(0,0,0,1,1,2020),'_time','h,i',false);
	$wer=$_POST['name'];
	$warum=$_POST['warum'];
	$wo=$_POST['wo'];
	if($reason==2){
		$info='<font color="red"><b>Abgelehnt, bitte alle Felder ausf&uuml;llen und darauf achten das "von" kleiner als "bis" ist!</b></font>';
	} else if($reason==1){
		$info='<font color="red"><b>Abgelehnt, maximale Dauer für "'.$options[$wo].'" betr&auml;gt '.($max_time[$wo]/86400).' Tage!</b></font>';
	} else if($reason==3){
		$info='<font color="red"><b>Abgelehnt, es gibt scheinbar ein Problem mit der Datenbank, bitte probier es nochmal oder sag mir, Kolja, bescheid!</b></font>';
	} else if($reason==4){
		$info='<font color="red"><b>Abgelehnt, in diesem Zeitraum ist der Platz bereits reserviert!</b></font>';				
	};
} else {
	$result=java_cal2('',floor(time()/3600)*3600,floor(time()/3600)*3600+3600*3,time(),mktime(0,0,0,1,1,2020),'_time','h,i',false);
	$wer='';
	$wo='';
	$warum='';
	$info='';
	if($sql_done==1){
		$info='<font color="red"><b>Eintrag erfolgreich!</b></font>';		
	};
}


echo $result['css'];
echo	'<form action="index.php" method="POST">
<table width="100%" class="singletable" cellpadding="0">
			<tr><th>Von wann</th><th>Bis wann</th><th>Wo</th><th>Wer bist du</td><th>Warum</th></tr>
			<tr><td>'.$result['from'].'</td>
			<td>'.$result['to'].'</td>
			<!--<td><input type="text" size="20" name="wo" value="'.$wo.'"></td>-->
			<td><select name="wo">'.select('',$options,$wo,'').'</select></td>
			<td><input type="text" size="20" name="name"  value="'.$wer.'"></td>
			<td><input type="text" size="20" name="warum" value="'.$warum.'"></td></tr>
			<tr><td colspan="5" valign="right"><input type="submit" value="speichern" name="save"> &nbsp; &nbsp; '.$info.'</td></tr>
			
</table></form>';
echo $result['java'];
tab_end();
##################### Daten sammeln und sortieren ################################
echo '</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>';
##################### Tabelle #############################################
tab_go("100%",250,'left','&Uuml;bersichtstabelle generiert am: '.date("d-m-Y H:i",time()));
##### sortierfelder
/*$values="6,3,1,11,8,5";
$options="Getr&auml;nke ( seit letztem Update ),Getr&auml;nke ( gesamt ),Vorname,Nachname,Kontostand,Letzer Einzahlungsbetrag";
echo'<form method="POST" action="index.php?'.SID.'"><div align="center"><select name="sort">'.select($values,$options,$_POST['sort']).'</select>
<select name="dir">'.select('0,1','Fallend,Steigend',$_POST['dir']).'</select>
<input type="submit" name="sortieren" value="sortieren"></center></form>';*/
##### sortierfelder
## ###TABELLEN KOPF
echo	'<table width="100%" class="singletable" cellpadding="0"><tr><th>Nr</td><th>Von wann</th><th>Bis wann</th><th>Wo</th><th>Wer</td>';
if($_SESSION['session_user_typ']!=$aka_reserve_watcher_state){
	echo '<th>Warum</th>';
};
echo '<th>Wann angemeldet</th>';
if($_SESSION['session_user_typ']!=$aka_reserve_watcher_state){
	echo '<th>Entfernen</th></tr>';
};
##### TABELLEN KOPF
### daten
unset($daten); $a=0;
$abfrage="SELECT id, von, bis, ort,person,grund,time_create FROM aka_reserve where active=1 and bis>=".time()." order by ort asc,von asc";
$erg=mysql_db_query($db,$abfrage,$verbindung);
while(list($db_id,$db_von,$db_bis, $db_ort,$db_person,$db_grund,$db_time_create) = mysql_fetch_row($erg)) {
	$daten[$a]['id']=$db_id;
	$daten[$a]['von']=$db_von;
	$daten[$a]['bis']=$db_bis;
	$daten[$a]['ort']=$db_ort;
	$daten[$a]['person']=$db_person;
	$daten[$a]['grund']=$db_grund;
	$daten[$a]['time_create']=$db_time_create;
	$a++;
	unset($db_id,$db_von,$db_bis, $db_ort,$db_person,$db_grund,$db_time_create);
};
### daten
if($a>0){
	##### TABELLEN schleife
	$b=0;
	foreach ($daten as $index => $datum) {
	##### TABELLEN berechnen
		$fett_a=''; $fett_e=''; $red_a=''; $red_e='';
		if($b%2==1) { $bg_color=' class="gray"'; } else { $bg_color=''; };
		if($datum['von']<time() && $datum['bis']>time()){	$fett_a='<b>'; $fett_e="</b>"; };
		if(floor($datum['bis']/86400)==floor(time()/86400)){	$red_a='<font color="#880000">'; $red_e="</font>"; };
	##### TABELLEN berechnen
	##### TABELLEN anzeigen
		echo '<tr>
			<td'.$bg_color.' style="height:30px">'.$fett_a.$red_a.$datum['id'].$fett_e.$red_e.'</td>
			<td'.$bg_color.'>'.$fett_a.$red_a.date("d-m-Y H:i",$datum['von']).$fett_e.$red_e.'</td>
			<td'.$bg_color.'>'.$fett_a.$red_a.date("d-m-Y H:i",$datum['bis']).$fett_e.$red_e.'</td>
			<td'.$bg_color.'>'.$fett_a.$red_a.$datum['ort'].$fett_e.$red_e.'</td>
			<td'.$bg_color.'>'.$fett_a.$red_a.$datum['person'].$fett_e.$red_e.'</td>';

		if($_SESSION['session_user_typ']!=$aka_reserve_watcher_state){
			echo'<td'.$bg_color.'>'.$fett_a.$red_a.$datum['grund'].$fett_e.$red_e.'</td>';
		}

		echo '	<td'.$bg_color.'>'.$fett_a.$red_a.date("H:i:s d-m-Y",$datum['time_create']).$fett_e.$red_e.'</td>';
		if($_SESSION['session_user_typ']!=$aka_reserve_watcher_state){
			echo '<td'.$bg_color.'><a href="index.php?delete='.$datum['id'].'" onclick="return confirmLink(this, \'Bitte nur eigene Reservierungen entfernen. Deine ID wird geloggt!\')">Entfernen</a></td>';
		}
		echo   '</tr>';
	##### TABELLEN anzeigen
		$b++;
		};
	##### TABELLEN schleife
} else {
	echo '<tr><td style="height:30px" colspan="8">keine Reservierungen mehr vorhanden</td></tr>';
}
echo '<tr><td style="height:30px" colspan="8"><b>Fett</b>=Aktuelle Belegung, <font color="#880000">Rot</font>=Reservierung endet heute</td></tr></table>';
##################### Tabelle #############################################
tab_end();

?>
