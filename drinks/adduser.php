<?php
if($_SESSION['session_user_typ']<>$aka_drinks_admin_state && $_SESSION['session_user_typ']<>$aka_super_admin_state) { exit('falsches passwort'); };
##################### security ################################
##################### incoming post ############################
$error=0;
$titel='Neuen User anlegen';
$send='<input type="submit" name="anlegen" value="anlegen">';
if(!empty($HTTP_POST_VARS['anlegen'])) {
	if(mysql_num_rows(mysql_query("SELECT * FROM `aka_id` WHERE name='".$HTTP_POST_VARS['name']."' LIMIT 0,1"))>0){
		$error=1; ## hier block ausgeben
		tab_box("100%",100,'left','Fehler','Achtung: Dieser Name existiert schon in der Datenbank');
	}
	else {
		list($max)=mysql_fetch_row(mysql_query("SELECT `ID` FROM `aka_id` ORDER BY `ID` DESC LIMIT 0,1"));
		$max++;
		$was = array("&auml;", "&ouml;", "&uuml;", "&Auml;", "&Ouml;", "&Uuml;", "&szlig;");
		$wie = array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß");
		$name = str_replace($wie, $was, $HTTP_POST_VARS['name']); 
		
		if(mysql_query( "INSERT INTO `aka_id` (`id` ,`name`,`EMAIL`,`LAST_MAIL`) VALUES ('".$max."', '".$name."', '".$HTTP_POST_VARS['email']."', '".$HTTP_POST_VARS['email_pol']."')" ) &&
		   mysql_query( "INSERT INTO `aka_tasks_user` (`id`, `state`,`NUM_SUCCESS`,`NUM_FAILED`,`ACTIVE_TASK`,`SUCCESS`,`FAIL`) VALUES ('".$max."', '1', '0', '0', '0','', '')" )){
			$ok=1;
			tab_box("100%",100,'left','Info','User erfolgreich angelegt');
			}
		else {
			$error=2;
			tab_box("100%",100,'left','Fehler','Datenbank Fehler');
			};
		};
}
elseif(!empty($HTTP_POST_VARS['entfernen'])) {
	if( 	mysql_query("DELETE FROM `aka_id` WHERE `id`=".$_POST['rm'].";") AND 
			mysql_query("DELETE FROM `aka_money` WHERE `id`=".$_POST['rm'].";") AND
			mysql_query("DELETE FROM `aka_tasks_user` WHERE `id`=".$_POST['rm'].";") AND 
			mysql_query("DELETE FROM `aka_verbrauch` WHERE `id`=".$_POST['rm'].";")) { 
		tab_box("100%",100,'left','Info','User erfolgreich gel&ouml;scht'); };
	}
elseif(!empty($HTTP_POST_VARS['bearbeiten'])) {
	list($vorgabe_id,$name_vorgabe,$email_vorgabe,$email_pol_vorgabe)=
			mysql_fetch_row(mysql_query("SELECT `id`,`name`,`EMAIL`,`LAST_MAIL` FROM `aka_id` WHERE `ID`='".$_POST['rm']."';"));
	$titel='User bearbeiten';
	$send='<input type="submit" name="speichern" value="speichern">';
	}
elseif(!empty($HTTP_POST_VARS['speichern'])) {
	if(mysql_query( "UPDATE `aka_id` SET `name`='".$_POST['name']."',`EMAIL`='".$_POST['email']."',`LAST_MAIL`='".$_POST['email_pol']."' WHERE `id`='".$_POST['id']."';" )){
			tab_box("100%",100,'left','Info','&Auml;nderungen erfolgreich gespeichert.');
			};
	};
##################### incoming post ############################
##################### interface ###############################
include('collect_data.php');
$daten=kolja_sort($daten,1);
$daten=array_reverse($daten);

for($a=0;$a<count($daten);$a++){
	$options[$a]=$daten[$a][1];
	$values[$a]=$daten[$a][0];
	};
//echo'<form method="POST" action="index.php?mod=rmuser&'.SID.'"><div align="center"><b>User entfernen</b>'

tab_box("100%",100,'left','User l&ouml;schen',
'<form name="edit" action="index.php?mod=adduser&'.SID.'" method="POST"><table width="100%">
<tr><td width="200">Bitte User ausw&auml;hlen: </td><td><select name="rm">'.select($values,$options,$HTTP_POST_VARS['rm']).'</select></td></tr>
<tr><td colspan="2"><input type="submit" name="entfernen" value="entfernen" onclick="return confirmLink(this, \'Wirklich den ausgew&auml;lten User l&ouml;schen?\')"><input type="submit" name="bearbeiten" value="bearbeiten"></td></tr></table></form>');	

tab_box("100%",100,'left',$titel,
'<form name="create" action="index.php?mod=adduser&'.SID.'" method="POST"><table width="100%">
<tr><td width="200">Name: </td><td><input type="text" size="30" name="name" value="'.$name_vorgabe.'"></td></tr>
<tr><td>eMail Adresse: </td><td><input type="text" size="30" name="email" value="'.$email_vorgabe.'"></td></tr>
<tr><td>eMail Policy: </td><td><select name="email_pol">'.select('1,0','Immer,Nur im Minus',$email_pol_vorgabe).'</select>
	<input type="hidden" name="id" value="'.$vorgabe_id.'"></td></tr>
<tr><td colspan="2">'.$send.'</td></tr></table></form>');
##################### interface ###############################
//echo '<br><br><br><br><br><hr><br>';
//include('tab.php');
?>
