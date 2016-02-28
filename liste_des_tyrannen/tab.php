<?php
##################### Daten sammeln und sortieren ################################
include('collect_data.php');
##################### Daten sammeln und sortieren ################################
echo '</td></tr><tr><td>&nbsp;</td><td>';
##################### Tabelle #############################################
tab_go("100%",250,'left','&Uuml;bersichtstabelle');
##### sortierfelder
/*$values="6,3,1,11,8,5";
$options="Getr&auml;nke ( seit letztem Update ),Getr&auml;nke ( gesamt ),Vorname,Nachname,Kontostand,Letzer Einzahlungsbetrag";
echo'<form method="POST" action="index.php?'.SID.'"><div align="center"><select name="sort">'.select($values,$options,$_POST['sort']).'</select>
<select name="dir">'.select('0,1','Fallend,Steigend',$_POST['dir']).'</select>
<input type="submit" name="sortieren" value="sortieren"></center></form>';*/
##### sortierfelder
## ###TABELLEN KOPF
echo	'<table width="100%" class="singletable" cellpadding="0">
			<tr><th>Nr</td><th>Name</td><th>Aktuelle Aufgabe</th><th>Anzahl erfolgreicher Aufgaben</td><th>Nicht erledigte Aufgaben</th><th>Gefehlte Arbeitstage</th><th>Status</th></tr>';
##### TABELLEN KOPF
##### TABELLEN schleife
$b=0;
foreach ($daten as $index => $datum) {
##### TABELLEN berechnen 
	if($b%2==1) { $bg_color=' class="gray"'; } else { $bg_color=''; };
	if(empty($datum[1])){ $datum[1]=0; };
	if(empty($datum[2])){ $datum[2]=0; };
	if($datum[6]==1){ $state='Aktiv oder freiwillig'; } else { $state='Passiv'; };
	$c=$b+1;
	
	//get desc of task
	if(!empty($datum[3])){	list($task_desc)=mysql_fetch_row(mysql_db_query($db,"SELECT `title` FROM `aka_tasks` WHERE `id`=".$datum[3].";",$verbindung));}
	else { $task_desc=''; };
	
	if(!empty($datum[3]) AND $datum[3]!=0){ $aktuelle_aufgabe='<a href="index.php?mod=show&id='.$datum[3].'">'.$task_desc.'</a>'; }
	else { $aktuelle_aufgabe=''; };
	
	$erfolge='';
	for($a=0;$a<$datum[1];$a++){
		$erfolge.='<img src="img/smilie_ok.gif" width="11" height="22">';
	};
	
	$misserfolge='';
	for($a=0;$a<$datum[2];$a++){
		$misserfolge.='<img src="img/smilie_bad.gif" width="25" height="25">';
	};
	
	if(!empty($datum[4]) || !empty($datum[3])){
		$temp=explode(',',$datum[4]);
		if($datum[3]!='0'){	array_push($temp,$datum[3]);	};
		//$temp=@array_merge($temp,$datum[5]); ??
		$temp=@implode(',',$temp);
		while(substr($temp,0,1)==','){$temp=substr($temp,1);};
		while(substr($temp,-1)==','){$temp=substr($temp,0,-1);};
		$link1='<a href="index.php?mod=show&id='.$temp.'">';
		$link2='';
	}else{
		$link1='';
		$link2='';
	};
	
##### TABELLEN berechnen
##### TABELLEN anzeigen
	echo '<tr>
		<td'.$bg_color.' style="height:30px">'.$c.'</td>
		<td'.$bg_color.'>'.$link1.$datum[0].' '.$datum[11].$link2.'</td>
		<td'.$bg_color.'>'.$aktuelle_aufgabe.'</td>
		<td'.$bg_color.'>'.$erfolge.'</td>
		<td'.$bg_color.'>'.$misserfolge.'</td>
		<td'.$bg_color.'>'.$datum[7].'</td>
		<td'.$bg_color.'>'.$state.'</td>
	      </tr>';
##### TABELLEN anzeigen
	$b++;
	};
##### TABELLEN schleife 
echo '</table>';
##################### Tabelle #############################################
tab_end();

?>
