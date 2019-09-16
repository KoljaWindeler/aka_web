<?php
include_once('../a_common_mailer/class.phpmailer.php');
include('collect_data.php');

$daten=kolja_sort($daten,8); // nach guthaben
$daten=array_reverse($daten); // aufsteigend

for ($a=0;$a<=$max_user;$a++) { 
	unset($add_text);
	if($changed[$daten[$a][0]]==1 && !empty($daten[$a][12])	) {
		$temp_user=$a; // für die pranger info an den admin;
		//art der email
		if($daten[$a][4]>$daten[$a][7]){ // hier wurde gerade etwas aufgebucht
			if($daten[$a][5]<0){ // Mahnung
				$add_text='dir wurden Mahngeb&uuml;hren aufgetischt, weil du trotz Ank&uuml;ndigung dein Konto nicht aus den Miesen geholt hast. <br><u>Bitte k&uuml;mmere dich drum! </u><br>'; 
				}
			else { // konto wurde geladen
				$add_text='dein Konto wurde um '.$daten[$a][5].' eur aufgeladen.';
				};
			}
		elseif($daten[$a][4]<$daten[$a][7]){ // Getränkestriche // abbuchung nach aufbuchung
			if($daten[$a][8]<(-5)) {
				$add_text.='die Getr&auml;nkeliste wurde ausgewertet und auf deinem Konto befindet sich nun mit mehr als 5 eur in den Miesen.<br> Laut Clubabendbeschluss hast du nun <b>bis zum '.date('d.M.Y',time()+10*86400).'</b> Zeit, dein Konto &uuml;ber 0 zu bringen.<br>Ansonsten werden 5 eur Mahngeb&uuml;hren f&auml;llig.';
			} elseif($daten[$a][8]<5) {  //  und Guthaben < 5€
				$add_text='die Getr&auml;nkeliste wurde ausgewertet und auf deinem Konto befindet sich nun unter 5 eur.<br> Bitte lad es schnellstm&ouml;glich auf.'; //2do
			} elseif($daten[$a][8]>0 && $daten[$a][13]) { // und ich will es immer wissen
				$add_text='die Getr&auml;nkeliste wurde online gestellt.'; 
				};			
			};
		}
	elseif($changed[$daten[$a][0]]==1 && empty($daten[$a][12])	) { // keine email
		echo '<font color="red"><b>Die Mail an '.$daten[$a][1].' konnte nicht verschickt werden,da sich keine Adresse in der Datenbank befindet.</b></font><br>';
		};
	if(!empty($add_text)){
		############# farbe berechnen ###########################
		if($daten[$a][8] <= -20 ) { $bg_guthaben = "#ff0000"; }
		elseif($daten[$a][8] >= 50 ) { $bg_guthaben = "#00aa00"; }
		elseif($daten[$a][8] >= 0 )  { 
			$temp=round(170-170*$daten[$a][8]*2/100); # bis 50
			$temp2=dechex($temp);
			if($temp<16){$temp2='0'+$temp2; };	
			$bg_guthaben = "#".$temp2."ff".$temp2;}
		elseif($daten[$a][8] < 0 )  { 
			$temp=round(170-170*$daten[$a][8]*-5/100); # bis -20
			$temp2=dechex($temp);
			if($temp<16){$temp2='0'+$temp2; };	
			$bg_guthaben = "#ff".$temp2."".$temp2;}
		############# farbe berechnen ###########################
		

		$mail    = new PHPMailer();
		$body    = '<html><body>Hallo '.str_replace(' ','',str_replace($daten[$a][11],'',$daten[$a][1])).', <br><br>'.$add_text.'<br>
 Dein akutelles Guthaben liegt bei:<br><b><font color="'.$bg_guthaben.'"><u>'.$daten[$a][8].' eur</u></b></font><br><br>
Bitte sei bem&uuml;ht, stets ein Polster ( ~ 10 eur ) auf deinem Konto zu haben. <br>Allzeit guten Durst.<br>
<a href="https://akaweb.illuminum.de/drinks/">Online-System</a> | <b>PW: </b>akapw<br><hr>
<b><u>Kontodaten</u></b><br>
<b>Name:</b> Aka Kraft<br>
<b>IBAN:</b> DE83 2519 0001 0550 9904 01<br>
<b>Wichtig!</b></u>: Wenn ihr f&uuml;r jemand anderen Geld &uuml;berweist muss der Betreff mit den Buchstaben "xxx" beginnen, gefolgt vom Namen<br>
</body></html>';
		$body    = preg_replace("[\\\\]",'',$body);
		$mail->AddReplyTo('Kolja.Windeler@gmail.com');
		$mail->From 	= 'noreply@akakraft.de';
		$mail->FromName = "AKA Getr".chr(228)."nkemailer";
		$mail->Subject = "AKA Getr".chr(228)."nkelisten Update";
		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 
		$mail->MsgHTML($body);
		$mail->AddAddress($daten[$a][12], $daten[$a][1]);
		//$mail->AddAddress('KKoolljjaa@gmail.com');
		
		if(!$mail->Send()) 	{	echo '<font color="red"><b>Die Mail an '.$daten[$a][1].' konnte nicht verschickt werden.</b></font><br>';	} 
		else 	{	echo '<font color="green"><b>Die Mail an '.$daten[$a][1].' ('.$daten[$a][12].') wurde verschickt.</b></font><br>';  };	
		};
	};
	
############## prangerliste mailen #######################
if(isset ($temp_user) && time()-$daten[$temp_user][7]<60){ # eigentlich doof aber total praktisch für den email button
	$mysqli->query( "DELETE FROM `aka_mahnomat`" ); # alles löschen
	$mail    = new PHPMailer();
	$body    = '<html><body><center>Hey Kolja, <br>die Aka Prangerliste: <br><br>';
	$sql_user=array();
	for($a=0;$a<=$max_user;$a++){
		if($daten[$a][3]==0){$aktiv=' (INAKTIV)';} else { $aktiv='';};
		if($daten[$a][8]<(-5)) {
			$body.=$daten[$a][1].$aktiv.' mit nem Guthaben von '.$daten[$a][8].' eur<br>';
			array_push($sql_user,$daten[$a][0]);
		};
	};
	$sql="INSERT INTO `aka_mahnomat` (`id` ,`user`, `time`) VALUES ('', '".implode(',',$sql_user)."', ".(time()+10*86400).")";
	$mysqli->query($sql);
	
	$body.='	<br><br>(bis zum '.date('d.M.Y',time()+10*86400).') </center></body></html>';
	$body    = preg_replace("[\\\\]",'',$body);
	$mail->AddReplyTo('Kolja.Windeler@gmail.com');
	$mail->From 	= 'noreply@akakraft.de';
	$mail->FromName = "AKA Getr".chr(228)."nkemailer";
	$mail->Subject = 'AKA Getr'.chr(228).'nkelisten Pranger (bis zum '.date('d.M.Y',time()+10*86400).')';
	$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($body);
	$mail->AddAddress('KKoolljjaa+AKA@gmail.com');

	if(!$mail->Send()) 	{	echo '<font color="red"><b>Die Mail an den Admin konnte nicht verschickt werden.</b></font><br>';	} 
	else 	{	echo '<font color="green"><b>Die Mail an den Admin wurde verschickt.</b></font><br>';  };	

	};
############## prangerliste mailen #######################

?>
