<?php
include_once('../a_common_mailer/class.phpmailer.php');

// datan sammeln
include('collect_data.php');
$daten=kolja_sort($daten,8); // nach guthaben
$daten=array_reverse($daten); // aufsteigend

for ($a=0;$a<=$max_user;$a++) { 
	unset($add_text);
	$current_user=$daten[$a];
	
	if($changed[$current_user[0]]==1 && !empty($current_user[12])) { // �nderung vorhanden und eine mail adresse da
		$temp_user=$a; // f�r die pranger info an den admin;

		// create firstname from data
		$temp=explode(" ",$current_user[1]);
		$firstname=$temp[0];

		// color for total money
		$totalmoney_text_color="4dff4d";
		if($current_user[8]<-5){
			$totalmoney_text_color="ff3333";
		} else if($current_user[8]<0){ 
			$totalmoney_text_color="fcca04";
		} else if($current_user[8]<10){ 
			$totalmoney_text_color="ffff33";
		};

		// wir haben x modes:
		// 1. striche wurden in das abrechnungssystem �bertragen, der nutzer hat aber noch >10� geld: identifikation des eintrags durch: 								datum letzer abbuchung > datum letzer aufbuchung && guthaben
		// 2. striche wurden in das abrechnungssystem �bertragen, der nutzer hat aber noch  >-5  && <10� geld: identifikation des eintrags durch: 				datum letzer abbuchung > datum letzer aufbuchung && guthaben
		// 3. striche wurden in das abrechnungssystem �bertragen, der nutzer hat  <-5� geld: identifikation des eintrags durch: 													datum letzer abbuchung > datum letzer aufbuchung && guthaben
		// 4. dem nutzern wurden -5 euro �bertragen -> mahngeb�hr																																											datum letzer aufbuchung > datum letzer abbuchung && betrag 
		// 5. der nutzer hat geld �berwiesen, -> gutschrift -> der nutzer hat nun >10�																																			datum letzer aufbuchung > datum letzer abbuchung && betrag && guthaben
		// 6. der nutzer hat geld �berwiesen, -> gutschrift -> der nutzer hat noch immer <-5�																															datum letzer aufbuchung > datum letzer abbuchung && betrag && guthaben
		// 7. der nutzer hat geld �berwiesen, -> gutschrift -> der nutzer hat nun >-5� <10�																																	datum letzer aufbuchung > datum letzer abbuchung && betrag && guthaben

		$mode=0;
		if($current_user[4]>$current_user[7]){ // hier wurde gerade etwas aufgebucht
			if($current_user[8]<0){ // Mahnung
				$mode=4;	// mahnung
			}  else { // aufbuchung
				if($current_user[8]>10){
					$mode=5; // gutschrift auf >10�
				} else if($current_user[8]<-5){
					$mode=6; // gutschrift ohne sinn
				} else {
					$mode=7; // gutschrift so mittel
				};
			}
		} else {
			if($current_user[8]>10){
				$mode=1; // abrechnung ohne folgen
			} else if($current_user[8]<-5){
				$mode=3; // abrechnung mahnungsdrohung
			} else {
				$mode=2; // abrechnung so mittel
			};
		}


		$text=$firstname.' ,beim Getr&auml;nkesystem ist etwas schief gelaufen, bitte leite diese zur&uuml;ck an den Absender.';

		if($mode==1){
			$text='Hallo '.$firstname.', soeben wurden die Getr&auml;nkestriche in das <a href="https://akaweb.illuminum.de/drinks><span style="color: #eeeeee !important;" >Online System</span></a> (<i>PW: akapw</i>) &uuml;bertragen.<br>
		Nach Abzug deiner '.$current_user[6].' Striche liegt dein neuer Kontostand nun bei  <b><font color="#'.$totalmoney_text_color.'"><u>'.$current_user[8].' eur</u></b></font> ! <br>
			<br>Vielen Dank das du genug Geld vorh&auml;lst. Bur mit einem gemeinsamen Guthaben neue Getr&auml;nke bestellen k&ouml;nnen.<br>';
		} else if($mode==2){
			$text='Hallo '.$firstname.', soeben wurden die Getr&auml;nkestriche in das <a href="https://akaweb.illuminum.de/drinks><span style="color: #eeeeee !important;" >Online System</span></a> (<i>PW: akapw</i>) &uuml;bertragen.<br>
		Nach Abzug deiner '.$current_user[6].' Striche liegt dein neuer Kontostand nun bei  <b><font color="#'.$totalmoney_text_color.'"><u>'.$current_user[8].' eur</u></b></font> ! <br>
		Bitte gib dir M&uuml;he immer mindestens +10 eur auf deinem Konto zu haben, da wir nur mit einem gemeinsamen Guthaben neue Getr&auml;nke bestellen k&ouml;nnen.<br>';
		} else if($mode==3){
			$text='Hallo '.$firstname.', soeben wurden die Getr&auml;nkestriche in das <a href="https://akaweb.illuminum.de/drinks><span style="color: #eeeeee !important;" >Online System</span></a> (<i>PW: akapw</i>) &uuml;bertragen.<br>
		Nach Abzug deiner '.$current_user[6].' Striche liegt dein neuer Kontostand nun bei  <b><font color="#'.$totalmoney_text_color.'"><u>'.$current_user[8].' eur</u></b></font> ! <br><br><u>Daher ist diese Mail als Mahnung zu verstehen.</u><br><br> Lade bitte dein Konto wieder auf &uuml;ber +10 eur auf,da wir nur mit einem gemeinsamen Guthaben neue Getr&auml;nke bestellen k&ouml;nnen.<br>
		Solltest dein Konto am '.date('d.M.Y',time()+10*86400).' noch immer Schulden aufweisen werde ich dir leider 5 eur Mahngeb&uuml;hren aufschreiben m&uuml;ssen.<br>';
		} else if($mode==4){
			$text='Hallo '.$firstname.', ich habe festgestellt das du trotz vorheriger Mahnung dein Konto leider <u>nicht</u> innerhalb der gegebenen Frist aufgef&uuml;llt hast.<br>
		Wie dir bekannt sein d&uuml;rfte erh&auml;lst du als Motivationshilfe nun 5 eur Mahngeb&uuml;hr. Dein aktueller Kontostand liegt somit bei  <b><font color="#'.$totalmoney_text_color.'"><u>'.$current_user[5].' eur</u></b></font> !<br>
		Bitte gib dir M&uuml;he immer mindestens +10 eur auf deinem Konto zu haben, da wir nur mit einem gemeinsamen Guthaben neue Getr&auml;nke bestellen k&ouml;nnen.<br>';
		} else if($mode==5){
			$text=$firstname.', du bist ein gro&szlig;artiger Mensch!<br><br>Ich habe eine eingehende &Uuml;berweisung &uuml;ber   <b><font color="#4dff4d"><u>'.$current_user[5].' eur</u></b></font> gefunden und auf dein Getr&auml;nkekonto gutgeschrieben.<br>
		Du hast jetzt ein akutelles Guthaben von: <b><font color="#'.$totalmoney_text_color.'"><u>'.$current_user[8].' eur</u></b></font><br><br>
		Nur mit Mitgliedern wie dir kann die Getr&auml;nkekasse funktionieren!<br>
		Bitte gib dir M&uuml;he weiterhin mindestens +10 eur auf deinem Konto zu haben, da wir nur mit einem gemeinsamen Guthaben neue Getr&auml;nke bestellen k&ouml;nnen.<br>';
		} else if($mode==6){
			$text=$firstname.', <br><br>Ich habe eine eingehende &Uuml;berweisung &uuml;ber   <b><font color="#4dff4d"><u>'.$current_user[5].' eur</u></b></font> gefunden und auf dein Getr&auml;nkekonto gutgeschrieben.<br>
		Du hast jetzt ein akutelles Guthaben von: <b><font color="#'.$totalmoney_text_color.'"><u>'.$current_user[8].' eur</u></b></font><br><br>
		Leider wird diese Gutschrift nicht ausreichen um dich vor kommenden Mahngeb&uuml;hren zu sch&uuml;tzen.<br>
		Bitte gib dir M&uuml;he mindestens +10 eur auf deinem Konto zu haben, da wir nur mit einem gemeinsamen Guthaben neue Getr&auml;nke bestellen k&ouml;nnen.<br>';
		} else if($mode==7){
			$text=$firstname.',<br><br>Ich habe eine eingehende &Uuml;berweisung &uuml;ber   <b><font color="#4dff4d"><u>'.$current_user[5].' eur</u></b></font> gefunden und auf dein Getr&auml;nkekonto gutgeschrieben.<br>
		Du hast jetzt ein aktuelles Guthaben von: <b><font color="#'.$totalmoney_text_color.'"><u>'.$current_user[8].' eur</u></b></font><br><br>
		Bitte gib dir M&uuml;he mindestens +10 eur auf deinem Konto zu haben, da wir nur mit einem gemeinsamen Guthaben neue Getr&auml;nke bestellen k&ouml;nnen.<br>';
		};
	
		$common_header='<html><head></head><body style="background-color:#354791; 	font-weight:bold; 	color:#eeeeee; 	font-family:Arial; 	font-size:90%;  padding:10px"> <img width="1000" height="125" src="akaheaderlogo_luh.jpg"><br><br><br>';

		$common_footer='<br><br><div style="font-weight:normal; font-size:80%;">
		Falls du neu sein solltest hier unser Ablauf/Regeln:<br>
		<ul>
		<li>Wir z&auml;hlen in unregelm&auml;&szlig;igen Abst&auml;nden die Striche auf der Getr&auml;nkeliste und &uuml;bertragen diese in das <a href="https://akaweb.illuminum.de/drinks><span style="color: #eeeeee !important;" >Online System</span></a> (<i>PW: akapw</i>)</li>
		<li>Du bekommst eine pers&ouml;nliche Mail mit deinem Guthaben an deine E-Mail Adresse <span style="color: #eeeeee !important;" >'.$current_user[12].'</span> zugestellt. </li>
		<li>Solltest du <u>weniger als +10 eur</u> auf deinem Konto haben, &uuml;berweise bitte genug Geld um im n&auml;chsten Zeitraum nicht ins Minus zu fallen.</li>
			<li>Falls du sogar <u>mehr als 5 eur Schulden</u> haben solltest greift z&uuml;s&auml;tzlich unsere "Mahnungsregel": 
				<ul>
					<li>Du hast nach der Zustellung deiner Mahnung 10 Tage Zeit, gen&uuml;genden Geld zu &uuml;berweisen, damit dein Konto nicht mehr im Minus steht.</li>
					<li>Wenn du dies nicht tust wirst du eine Mahngeb&uuml;hr von 5 eur erhalten.</li>
					<li>Bitte denk daran das andere Mitglieder deine Schulden ausgleichem m&uuml;ssen</li>
					<li>Au&szlig;erdem k&ouml;nnen wir keine neuen Getr&auml;nke bestellen, solange wir nicht genug Geld auf dem Konto haben</li>
				</ul>
			</li> 
		</ul>

		<br><b><u>Wichtig!</u></b></u><br>
		Die &Uuml;berweisungen werden automatisiert anhand des Absenders zugeodnet. Wenn du f&uuml;r jemand anderen Geld &uuml;berweisen m&ouml;chtest, muss der Betreff mit den Buchstaben "xxx" beginnen, gefolgt von dessen Namen. Sonst wird das Geld deinem eigenen Konto gutgeschrieben.<br>
		<br>
		<b><u>Kontodaten</u></b><br>
		<b>Name:</b> AKAKraft<br>
		<b>IBAN:</b> DE83 2519 0001 0550 9904 01<br>
		</div>

		<br>
		Diese Mail wurde automatisch im Namen der Getr&auml;nkekasse der AkaKraft e.V. erstellt und verschickt.<br>
		Falls ein Fehler bei der Buchung aufgetreten sein sollte oder falls du mir etwas mitteilen willst, dann kann du trotzdem direkt an diese Adresse antworten.<br>
		<br>
		Ich w&uuml;nsche viel Spa&szlig; in der AkA und allzeit guten Durst.<br>
		Kolja<br></body></html>';

		$mail    = new PHPMailer();
		$body    = $common_header.$text.$common_footer;
		$body    = preg_replace("[\\\\]",'',$body);
		$mail->AddReplyTo('Kolja.Windeler@gmail.com');
		$mail->From 	= 'noreply@akakraft.de';
		$mail->FromName = "AKA Getr".chr(228)."nkemailer";
		$mail->Subject = "AkA Getr".chr(228)."nkelisten Update ".date('d.M.Y',time());
		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 
		$mail->MsgHTML($body);
		$mail->ContentType=("multipart/related");
		$mail->AddAddress($current_user[12], $current_user[1]);
		$mail->AddAddress('KKoolljjaa@gmail.com');
		
		if(!$mail->Send()) 	{	
			echo '<font color="red"><b>Die Mail an '.$current_user[1].' konnte nicht verschickt werden.</b></font><br>';	
		} else {	
			echo '<font color="green"><b>Die Mail an '.$current_user[1].' ('.$current_user[12].') wurde verschickt.</b></font><br>';  
		};	
	}	elseif($changed[$current_user[0]]==1 && empty($current_user[12])	) { //das oben wir ausgefuert wenn der user ein/auszahlungen hatte das hier unten wenn keine email
		echo '<font color="red"><b>Die Mail an '.$current_user[1].' konnte nicht verschickt werden,da sich keine Adresse in der Datenbank befindet.</b></font><br>';
	};
};
############## prangerliste mailen #######################	
if(time()-$daten[$temp_user][7]<60){ # eigentlich doof aber total praktisch f�r den email button
	$mysqli->query( "DELETE FROM `aka_mahnomat`" ); # alles l�schen
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
