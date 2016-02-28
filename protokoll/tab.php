<?php
##################### Tabelle #############################################
if($_GET['from']==1) { $von='Anfang der Zeit'; }
else { $von=date('d.m.y',$_GET['from']); };

tab_go("100%",250,'left','&Uuml;bersichtstabelle vom '.$von.' bis zum '.date('d.m.y',$_GET['to']));
## ###TABELLEN KOPF
echo	'<table width="100%" class="singletable">
			<tr><th width="35">Nr</th><th width="200">Clubabend Datum</th> 
			<th width="400">Dateiname</th><th>Wesentliche Punkte</th></tr>';
##### TABELLEN KOPF
##### TABELLEN schleife 
$abfrage="SELECT `ID`, `date`,`Filename`, `Bes` FROM `aka_pro_list` WHERE `date`>='".$_GET['from']."' AND `date`<='".$_GET['to']."' ORDER BY `date` desc";
$erg=mysql_query($abfrage);	$b=0;
while(list($db_id,$db_date,$db_filename,$db_bes) = mysql_fetch_row($erg)) {
##### TABELLEN berechnen 
	# Hintergrundfarbe
	if($b%2==1) { $bg_color='class="gray"'; } else { $bg_color=""; };
	$b++;
	# beschreibung
	if(!empty($db_bes))  { $db_bes='<b>-</b> '.str_replace('//','<BR><b>-</b> ',$db_bes); };
	# dateiname
	if(	substr(basename($db_filename),strlen(basename($db_filename))-3)=='pdf' ){
		$icon='<img src="img/PDF_File_normal.png" widht="40" height="40"  style="float:left;">';}
	else { $icon=''; };
	$filename_show=strtolower(basename($db_filename));
	$filename_show=substr($filename_show,0,strlen($filename_show)-3);
	$temp=explode('_',$filename_show);
	$temp[count($temp)-1]='_';
	$filename_show=implode('_',$temp);
	$filename_show=str_replace('__','',$filename_show).'.pdf';
	# new iconv_get_encoding
	if((time()-$db_date)<3*86400){
		$new=' <img src="img/softwareUpdate-256.png" width="40" heigth="40" style="float:left;"><br> &nbsp; <b>'.date('d.m.Y',$db_date).'</b>'; }
	else {
		$new=date('d.m.Y',$db_date); };
	# clicks
	list($clicks)=mysql_fetch_row(mysql_query("SELECT `clicks` FROM aka_pro_clicks WHERE `Filename`='".basename($db_filename)."' LIMIT 0,1;"));
	if(mysql_num_rows(mysql_query("SELECT `clicks` FROM aka_pro_clicks WHERE `Filename`='".basename($db_filename)."' LIMIT 0,1;"))==0) { $clicks =0; };
##### TABELLEN berechnen
##### TABELLEN anzeige	
	echo '<tr>	<td '.$bg_color.'># '.$b.'</td>
						<td '.$bg_color.'>'.$new.'</td>
						<td '.$bg_color.'>'.$icon.'<br><a href="clicks.php?file='.$db_filename.'" target="_blank">'.$filename_show.'</a> 
							<i>( '.$clicks.' mal herunter geladen )</i><br>';
	if($_SESSION['session_user_typ']==$aka_protokoll_admin_state || $_SESSION['session_user_typ']==$aka_super_admin_state){
		echo'
						<div align="right"><a href="index.php?mod=edit&edit='.$db_id.'"><font size="-2">edit</font></a> |  <a href="index.php?mod=edit&remove='.$db_id.'&from='.$_GET['from'].'&to='.$_GET['to'].'" onclick="return confirmLink(this, \'Wirklich l&ouml;schen?\')"><font size="-2">remove</font></div>';
	};
	echo '</td>
						<td '.$bg_color.'>'.$db_bes.'</td></tr>';
##### TABELLEN anzeigen
	};
if(mysql_num_rows(mysql_query($abfrage))<1) { 
echo '<tr><td colspan="4" align="center"><center>Keine Eintr&auml;ge verf&uuml;gbar.</center></td></tr>'; };
##### TABELLEN schleife 
echo '</table>';
##################### Tabelle #############################################
tab_end();
?>
