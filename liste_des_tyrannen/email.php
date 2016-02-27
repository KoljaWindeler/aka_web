<?php
include_once('mailer/class.phpmailer.php');
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
			if($daten[$a][8]<5) {  //  und Guthaben < 5€
				$add_text='die Getr&auml;nkeliste wurde ausgewertet und dein Konto befindet sich nun unter 5 eur.<br> Bitte lad es schnellstm&ouml;glich auf.'; //2do
				}
			elseif($daten[$a][8]>0 && $daten[$a][13]) { // und ich will es immer wissen
				$add_text='die Getr&auml;nkeliste wurde online gestellt.'; 
				};
			// an den pranger stellen wenn es zu den flop 5 + unter 5 € minus kommt
			$pranger=0;
			for($d=0;$d<=5;$d++){ if($daten[$d][0]==$b){ $pranger=1; }; };
			if($pranger==1 && $daten[$a][8]<(-5)) { $add_text.='<br>Desweiteren hast du es geschafft, unter die mit dem wenigsten Guthaben zu kommen. <br>Laut Clubabendbeschluss hast du nun <b>bis zum '.date('d.M.Y',time()+10*86400).'</b> Zeit, dein Konto aus den Miesen zu holen.<br>Ansonsten drohen dir 5 eur Mahngeb&uuml;hren.'; };
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
<a href="http://portal.akakraft.de/drinks/index.php">http://portal.akakraft.de/drinks/index.php</a> | <b>PW: </b>akapw<br><hr>
<b><u>Kontodaten</u></b><br>
<b>Name: </b> Kolja Windeler<br>
<b>Kontonummer: </b> 82753300<br>
<b>Bankleitzahl: </b> 29165681<br>
<b>Betreff: </b> Falls ihr nicht von eurem Konto &uuml;berweist, bitte euren Namen angeben. <br>
</body></html>';
		$body    = eregi_replace("[\]",'',$body);
		$mail->AddReplyTo('Kolja.Windeler@gmail.com');
		$mail->From 	= 'noreply@akakraft.de';
		$mail->FromName = "AKA Getr".chr(228)."nkemailer";
		$mail->Subject = "AKA Getr".chr(228)."nkelisten Update";
		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 
		$mail->MsgHTML($body);
		$mail->AddAddress($daten[$a][12], $daten[$a][1]);
		
		if(!$mail->Send()) 	{	echo '<font color="red"><b>Die Mail an '.$daten[$a][1].' konnte nicht verschickt werden.</b></font><br>';	} 
		else 	{	echo '<font color="green"><b>Die Mail an '.$daten[$a][1].' ('.$daten[$a][12].') wurde verschickt.</b></font><br>';  };	
		};
	};
	
############## prangerliste mailen #######################	
if(time()-$daten[$temp_user][7]<60){ # eigentlich doof aber total praktisch für den email button
	mysql_query( "DELETE FROM `aka_mahnomat`" ); # alles löschen
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
	mysql_query($sql);
	
	$body.='	<br><br>(bis zum '.date('d.M.Y',time()+10*86400).') </center></body></html>';
	$body    = eregi_replace("[\]",'',$body);
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
