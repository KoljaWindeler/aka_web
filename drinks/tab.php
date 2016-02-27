<?php
##################### Daten sammeln und sortieren ################################
include('collect_data.php');
if(!empty($HTTP_POST_VARS['sort'])) 	{  $daten=kolja_sort($daten,$HTTP_POST_VARS['sort']); }
else 														{	$daten=kolja_sort($daten,6); };
if($HTTP_POST_VARS['dir']==1) 			{  $daten=array_reverse($daten); };
##################### Daten sammeln und sortieren ################################
###### hinweis
tab_go("100%",250,'left','Kontodaten');
echo '<table border="0" width="100%">
<tr><td width="150"><u><b>Name</b></u>:</td><td width="*"> Kolja Windeler</td></tr>
<tr><td><u><b>Kontonummer</b></u>:</td><td> 1014919250</td></tr>
<tr><td><u><b>Bankleitzahl</b></u>:</td><td> 12030000</td></tr>
<tr><td><u><b>IBAN</b></u>:</td><td>DE61 1203 0000 1014 9192 50</td></tr>
<tr><td><u><b>BIC</b></u>:</td><td>BYLADEM 1001 </td></tr>
<tr><td><u><b>Wichtig!</b></u>:</td><td>Wenn ihr f&uuml;r jemand anderen Geld &uuml;berweist muss der Betreff mit den Buchstaben "xxx" beginnen, gefolgt vom Namen</td></tr>
</table>';
tab_end();
###### hinweis
echo '</td></tr><tr><td>&nbsp;</td><td>';
##################### Tabelle #############################################
tab_go("100%",250,'left','&Uuml;bersichtstabelle');
##### sortierfelder
$values="6,3,1,11,8,5";
$options="Getr&auml;nke ( seit letztem Update ),Getr&auml;nke ( gesamt ),Vorname,Nachname,Kontostand,Letzer Einzahlungsbetrag";
echo'<form method="POST" action="index.php?'.SID.'"><div align="center"><select name="sort">'.select($values,$options,$HTTP_POST_VARS['sort']).'</select>
<select name="dir">'.select('0,1','Fallend,Steigend',$HTTP_POST_VARS['dir']).'</select>
<input type="submit" name="sortieren" value="sortieren"></center></form>';
##### sortierfelder
## ###TABELLEN KOPF
echo	'<table width="100%" class="singletable">
			<tr><th>Nr</td><th><a href="#" title="Platz bezogen auf die letze Abrechnung / Platz auf gesamten Abrechnungszeitraum seit 1.1.09">Ranking</a></td><th>Name</td><th>Getr&auml;nkestriche ( Datum ) </td><th>Letzte Einzahlung</td><th>Aktueller Kontostand</td></tr>';
##### TABELLEN KOPF
##### TABELLEN schleife 
for ($b=0;$b<=$max_user;$b++) {
##### TABELLEN berechnen 
	if($b%2==1) { $bg_color=' class="gray"'; } else { $bg_color=''; };
	if($daten[$b][8] <= -20 ) { $bg_guthaben = ' style="border: 1px solid #cccccc; background: rgba( 255,0,0,0.85); text-align: center !important; font-weight: bold;" '; }
	elseif($daten[$b][8] >= 50 ) { $bg_guthaben = ' style="border: 1px solid #cccccc; background: rgba(0,255,0,0.6); text-align: center !important; font-weight: bold;" '; }
	elseif($daten[$b][8] >= 0 )  { 
		$temp=round(255-255*$daten[$b][8]*2/100); # bis 50
		$temp2=$temp;
		if($temp<16){$temp2='0'+$temp2; };			
		$bg_guthaben = ' style="border: 1px solid #cccccc; background: rgba('.$temp2.', 255, '.$temp2.', 0.85); text-align: center !important; font-weight: bold;" ';
		}
	elseif($daten[$b][8] < 0 )  { 
		$temp=round(255-255*$daten[$b][8]*-5/100); # bis -20
		$temp2=$temp;
		if($temp<16){$temp2='0'+$temp2; };	
		$bg_guthaben = ' style="border: 1px solid #cccccc; background: rgba( 255,'.$temp2.','.$temp2.', 0.85); text-align: center !important; font-weight: bold;" ';
		};
		
	$c=$b+1;
	if($daten[$b][9]==1){$daten[$b][9].=' <img src="design/krone.gif"><img src="design/krone.gif"><img src="design/krone.gif"> ';}
	elseif($daten[$b][9]==2){$daten[$b][9].=' <img src="design/krone.gif"><img src="design/krone.gif"> ';}
	elseif($daten[$b][9]==3){$daten[$b][9].=' <img src="design/krone.gif"> '; };
	if($daten[$b][10]==1){$daten[$b][10].=' <img src="design/krone.gif"><img src="design/krone.gif"><img src="design/krone.gif"> ';}
	elseif($daten[$b][10]==2){$daten[$b][10].=' <img src="design/krone.gif"><img src="design/krone.gif"> ';}
	elseif($daten[$b][10]==3){$daten[$b][10].=' <img src="design/krone.gif"> '; };
	if(empty($daten[$b][12])){$email='.'; } else { $email=''; };
##### TABELLEN berechnen
##### TABELLEN anzeigen
	if($daten[$b][4]>$daten[$b][7])	{	$aktuell = $daten[$b][4]; } else {	$aktuell=$daten[$b][7];};
	if($daten[$b][4]>0) { $daten[$b][4]='<i> (am '.date("d.m.y",$daten[$b][4]).')</i>'; } else { $daten[$b][4]=''; };
	if($daten[$b][7]>0) { $daten[$b][7]=' Fl.<i> (am '.date("d.m.y",$daten[$b][7]).')</i>'; } else { $daten[$b][7]=''; };
	
	
	echo '<tr><td'.$bg_color.'>'.$c.'</td><td'.$bg_color.'>'.$daten[$b][9].' ( '.$daten[$b][10].' )</td><td'.$bg_color.'><a href="index.php?mod=show&id='.$daten[$b][0].'">'.$email.$daten[$b][1].'</a></td><td'.$bg_color.'>'.$daten[$b][6].''.$daten[$b][7].' | Gesamt: '.$daten[$b][3].' Fl.</td><td'.$bg_color.'>'.$daten[$b][5].' &euro; '.$daten[$b][4].'</td><td '.$bg_guthaben.'>'.$daten[$b][8].' &euro; ('.date("d.m.y",$aktuell).')</td></tr>';
##### TABELLEN anzeigen
	};
##### TABELLEN schleife 
echo '</table>';
##################### Tabelle #############################################
tab_end();

?>
