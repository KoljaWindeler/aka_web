<?php
if($_SESSION['session_user_typ']<>$aka_protokoll_admin_state && $_SESSION['session_user_typ']<>$aka_super_admin_state) {
	exit('falsches passwort'); 
};

##################### Tabelle #############################################
tab_go("100%",250,'left','Edit');
#################### löschen verarbeiten #########################
if(isset($_GET['remove'])){
	list($filename)=mysql_fetch_row(mysql_query("SELECT `Filename`FROM `aka_pro_list` WHERE `ID`='".$_GET['remove']."' LIMIT 0,1;"));
	if(mysql_query("DELETE FROM `aka_pro_list` WHERE `ID`='".$_GET['remove']."' LIMIT 1;")){
		@unlink($filename);
		echo'	<script type="text/javascript" />
		<!--
		location.href="index.php?'.SID.'&from='.$_GET['from'].'&to='.$_GET['to'].'";
		//-->
		</script>';
		}
	else {
		echo 'ohoh da ist was schief gelaufen!';
		};
	}
elseif(isset($_GET['edit'])){
	list($beschr,$date,$filename)=mysql_fetch_row(mysql_query("SELECT `Bes`,`date`,`filename` FROM `aka_pro_list` WHERE `ID`='".$_GET['edit']."' LIMIT 0,1;"));
#################### upload tabelle #########################
	$options_tag='';	$options_monat='';		$options_jahr='';
	for($a=0;$a<=30;$a++) { $b=$a+1; $options_tag[$a]=$b; };
	for($a=0;$a<=11;$a++) { $b=$a+1; $options_monat[$a]=$b; };
	for($a=date('Y',time())-5;$a<=date('Y',time())+5;$a++) { $b=$a-(date('Y',time())-5); $options_jahr[$b]=$a; };
	$values_tag=$options_tag;
	$values_monat=$options_monat;
	$values_jahr=$options_jahr;

	echo	'<form enctype="multipart/form-data" action="index.php?mod=edit&'.SID.'&from='.$_GET['from'].'&to='.$_GET['to'].'" method="post">
				<input type="hidden" name="ID" value="'.$_GET['edit'].'">
			<table width="100%" class="singletable">
				<tr><th><font color="#ffffff"><b>Dateiname:<b></th>
						<td>'.$filename.'</td></tr>
				<tr><th><font color="#ffffff"><b>Beschreibung &auml;ndern:<b><br><i>"//" trennt die Punkte</i></th>
						<td><textarea name="beschr" cols="60">'.$beschr.'</textarea></td></tr>
				<tr><th><font color="#ffffff"><b>Datum manipulieren:<b></th>
						<td><select name="tag">'.select($values_tag,$options_tag,date('d',$date)).'</select>
								<select name="monat">'.select($values_monat,$options_monat,date('m',$date)).'</select>
								<select name="jahr">'.select($values_jahr,$options_jahr,date('Y',$date)).'</select>
								</td></tr>
				<tr><th><font color="#ffffff"><b>Speichern:<b></th>
						<td><input type="submit" name="save" value="Speichern"></td></tr>
			</table></form>';
	}
elseif(isset($_POST['save'])){
	$date=mktime(0,0,0,$_POST['monat'],$_POST['tag'],$_POST['jahr']);
	if(mysql_query("UPDATE `aka_pro_list` SET `Bes` = '".$_POST['beschr']."',`date`='".$date."' WHERE `ID` ='".$_POST['ID']."' LIMIT 1 ;")){
		echo'	<script type="text/javascript" />
		<!--
		location.href="index.php?'.SID.'&from='.$_GET['from'].'&to='.$_GET['to'].'";
		//-->
		</script>';
		}
	else {
		echo 'ohoh da ist was schief gelaufen!';
		};
	};

tab_end();

?>
