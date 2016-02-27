<?php
if($_SESSION['session_user_typ']<>$aka_tyran_admin_state && $_SESSION['session_user_typ']<>$aka_super_admin_state) { exit('falsches passwort'); };
##################### security ################################
include('collect_data.php');
include_once('mailer/class.phpmailer.php');
##################### incoming post ############################
$error=0;
if(!empty($HTTP_POST_VARS['save'])) {
	if(mysql_query( "INSERT INTO `aka_tasks` (`id`, `desc`, `title`, `status`, `date_pre`, `date_post`) VALUES ('','".$_POST['task_desc']."', '".$_POST['task_cap']."', '0', '".time()."', '');")){
		list($task_id)=mysql_fetch_row(mysql_query("SELECT id from `aka_tasks` order by id desc"));
		if(mysql_query( "UPDATE `aka_tasks_user` SET `ACTIVE_TASK`='".$task_id."' WHERE `id`='".$_POST['user_id']."';" )){
			tab_box("100%",100,'left','Info','Aufgabe erfolgreich angelegt');

			$mail    = new PHPMailer();
					$body    = '<html><body>Hallo '.$daten[$_POST['user_id']][0].', <br>
dir wurde folgende Aufgabe zugeteilt:<br><br>
<b>'.htmlspecialchars($_POST['task_cap']).'</b><br>
<i>'.htmlspecialchars($_POST['task_desc']).'</i><br><br>
Wenn du diese Aufgabe nicht erledigen kannst und daf&uuml;r nachvollziehbare Gr&uuml;nde hast<br>
melde dich bitte rechtzeitig bei mir. Ansonsten hast du f&uuml;r die Aufgabe bis zum n&auml;chsten<br>
Clubabend Zeit. <br>
<br>
Wir versuchen nat&uuml;rlich immer Aufgaben in gleichem Umfang zu verteilen,<br>
aber wenn du der Meinung sein solltest, dass sie doch zu umfangreich ist, gib bitte ebenfalls<br>
Bescheid. Dann werden weitere Personen zu der Aufgabe hinzugezogen, wobei du nach wie vor<br>
Verantwortlicher, oder positiv formuliert "Teamleiter-Leiter" sein wirst.<br><br>
Alle Aufgaben sind einzusehen unter <a href="http://akakraft.de/liste_des_tyrannen/"> akakraft.de/liste_des_tyrannen</a>.<br>
Mit besten Gr&uuml;&szlig;en, der Arbeitsverteiler Kolja 8)
</body></html>';
			$body    = eregi_replace("[\]",'',$body);
			$mail->AddReplyTo('Kolja.Windeler@gmail.com');
			$mail->From 	= 'noreply@akakraft.de';
			$mail->FromName = "AKA Arbeitsliste";
			$mail->Subject = "AKA Aufgabe";
			$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 
			$mail->MsgHTML($body);
			$mail->AddAddress($daten[$_POST['user_id']][12], $daten[$_POST['user_id']][0].' '.$daten[$_POST['user_id']][11]);

			if($mail->Send()){
				tab_box("100%",100,'left','Info','Aufgabe erfolgreich verschickt an '.$daten[$_POST['user_id']][12]);
			} else { 
				tab_box("100%",100,'left','Info','Aufgabe konnte nicht verschickt werden. Adresse: "'.$daten[$_POST['user_id']][12].'"');
			};

		} else {
			tab_box("100%",100,'left','Info','FEHLER, die Aufgabe konnte nicht zugewiesen werden');
		}
	} else {
		tab_box("100%",100,'left','Info','FEHLER, die Aufgabe konnte nicht erstellt werden');
	};
};
##################### incoming post ############################
##################### interface ###############################
list($db_min_success) = mysql_fetch_row(mysql_db_query($db,"SELECT `NUM_SUCCESS` FROM `aka_tasks_user` where state=1 order by `NUM_SUCCESS` asc",$verbindung));
list($db_max_success) = mysql_fetch_row(mysql_db_query($db,"SELECT `NUM_SUCCESS` FROM `aka_tasks_user` where state=1 order by `NUM_SUCCESS` desc",$verbindung));
// keine activ task
$a=0;
for($i=0;$i<=($db_max_success-$db_min_success);$i++){
	$abfrage="SELECT aka_id.id FROM aka_tasks_user,aka_id where aka_tasks_user.id=aka_id.id and aka_tasks_user.NUM_SUCCESS=".($db_min_success+$i)." AND aka_tasks_user.active_task='' AND aka_tasks_user.state=1 order by aka_id.name asc";
	$erg=mysql_db_query($db,$abfrage,$verbindung);
	while(list($db_id) = mysql_fetch_row($erg)) {
		$values[$a]=$db_id;
		$options[$a]=$daten[$db_id][0].' '.$daten[$db_id][11];
		$a++;
	};
};

//echo'<form method="POST" action="index.php?mod=rmuser&'.SID.'"><div align="center"><b>User entfernen</b>'

tab_box("100%",100,'left','Aufgabe anlegen',
'<form name="edit" action="index.php?mod=addtask&'.SID.'" method="POST"><table width="100%">
<tr><td width="200">Bitte User ausw&auml;hlen: </td><td><select name="user_id">'.select($values,$options,$values[0]).'</select></td></tr>
<tr><td colspan="2"><input type="text" name="task_cap" size="60" value="'.$_GET['bypass_cap'].'"></td>
<tr><td colspan="2"><textarea name="task_desc" cols="80" rows="10">'.$_GET['bypass_desc'].'</textarea></td>
<tr><td colspan="2"><input type="submit" name="save" value="Speichern"></td></tr></table></form>');	

##################### interface ###############################
//echo '<br><br><br><br><br><hr><br>';
//include('tab.php');
?>
