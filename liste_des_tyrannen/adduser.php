<?php
session_start();
if($_SESSION['session_user_typ']<>$aka_tyran_admin_state && $_SESSION['session_user_typ']<>$aka_super_admin_state) { exit('falsches passwort'); };
include_once('collect_data.php');
include_once('mailer/class.phpmailer.php');
##################################################

if(!empty($_POST)){
	if(mysql_query( "UPDATE `aka_tasks_user` SET `ACTIVE_TASK`='".$_GET['task_id']."' WHERE `id`='".$_POST['user_id']."';" )){

		// Frag alle user ab
		$task_user='';
		$abfrage="SELECT aka_id.name FROM aka_id, aka_tasks_user where aka_tasks_user.id=aka_id.id AND aka_id.id!=".$_POST['user_id']." AND aka_tasks_user.ACTIVE_TASK=".$_GET['task_id'];
		$erg=mysql_db_query($db,$abfrage,$verbindung);
		while(list($db_task_user) = mysql_fetch_row($erg)) {
			$task_user.=$db_task_user;
		};
		
		list($db_task_cap,$db_task_desc)=mysql_fetch_row(mysql_db_query($db,"SELECT `title`,`desc` from aka_tasks where `id`='".$_GET['task_id']."';",$verbindung));
			
		$mail    = new PHPMailer();
		$body    = '<html><body>Hallo '.$daten[$_POST['user_id']][0].', <br>
du wurdest ausgew&auml;hlt um bei der folgenden Aufgabe mit zuhelfen:<br><br>
<b>'.$db_task_cap.'</b><br>
<i>'.$db_task_desc.'</i><br><br>
Wenn du diese Aufgabe nicht erledigen kannst und daf&uuml;r nachvollziehbare Gr&uuml;de hast<br>
melde dich bitte rechtzeitig bei mir. Ansonsten hast du f&uuml;r die Aufgabe bis zum n&auml;chsten<br>
Clubabend Zeit. Wir versuchen nat&uuml;rlich immer etwa Aufgaben mit gleichem Umfang zu verteilen,<br>
aber wenn du der Meinung sein solltest, dass sie doch zu umfangreich sein sollte, dann gib bitte ebenfalls<br>
Bescheid. Da du als Verst&auml;rkung zur Aufgabe gezogen wurdest sind damit dein(e) Ansprechpartner: <br>
<br>
'.$task_user.'<br>
<br>
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
	}
};

if(!empty($_GET['task_id'])){
	tab_go("100%",250,'left','Akaler ausw&auml;hlen f&uuml;r Aufgaben ID '.$_GET['task_id']);
	// suche minimale aufgaben anzahl
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

	echo '<form name="edit" action="index.php?mod=addperson&task_id='.$_GET['task_id'].'&'.SID.'" method="POST">
	<select name="user_id">'.select($values,$options,$values[0]).'</select>
	<input type="submit" name="save" value="dazu packen"></form>';
	
}

// liste alle offenen aufgaben
tab_go("100%",250,'left','Aufgabe zum Aufstocken ausw&auml;hlen');
echo '<table width="500" border="1" class="singletable">
<form name="edit" action="index.php?mod=success&'.SID.'" method="POST">
<tr><th width="20">Id</th><th>Title</th></tr>';
$abfrage="SELECT `id`,`desc`,`title` FROM `aka_tasks` WHERE status<>'1' order by `id` asc;";
$erg=mysql_db_query($db,$abfrage,$verbindung);
while(list($db_id,$db_desc,$db_title) = mysql_fetch_row($erg)) {
#echo "suche für die aufgabe mit der id ".$db_id." den user raus<br>";
    list($db_task_user_id)=mysql_fetch_row(mysql_db_query($db,"SELECT `id` FROM `aka_tasks_user` WHERE `ACTIVE_TASK`=".$db_id.";",$verbindung));
#echo "gefunden wurde die user id:".$db_task_user_id."<br>";
    list($db_task_user)=mysql_fetch_row(mysql_db_query($db,"SELECT `name` FROM `aka_id` WHERE `id`=".$db_task_user_id.";",$verbindung));
        
    echo '<tr><td>'.$db_id.'</td><td><a href="index.php?mod=addperson&task_id='.$db_id.'">'.$db_title.'</a></td></tr>';
};
echo '</table>';
tab_end();
?>
