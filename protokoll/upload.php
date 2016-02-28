<?php
if($_SESSION['session_user_typ']<>$aka_protokoll_admin_state && $_SESSION['session_user_typ']<>$aka_super_admin_state) { exit('falsches passwort'); };
##################### Tabelle #############################################
tab_go("100%",250,'left','Upload');
$beschr_post='';
#################### upload verarbeiten #########################
if(!empty($_POST['upload'])) {
	$uploadDir = '/var/www/aka_web/protokoll/files/';
	$uploadFile = $uploadDir.$_FILES['userfile']['name'];
	$date=mktime(0,0,0,$_POST['monat'],$_POST['tag'],$_POST['jahr']);
	//if($_FILES['userfile']['type'] == 'application/pdf'){
	if (1==1) { // filetyp klappt zuoft nicht
		$uploadFile = str_replace('.pdf','',strtolower($uploadFile));
		$uploadFile = str_replace(' ','_',$uploadFile.'_'.time().'.pdf');
		echo "<pre>";
		#echo 'von:'.$_FILES['userfile']['tmp_name'].' nach '.$uploadFile.' <br><br>';
		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)){ 
			list($id)=mysql_fetch_row(mysql_query("SELECT `ID` FROM `aka_pro_list` ORDER BY `ID` DESC LIMIT 0,1"));
			$id++;
			$uploadFile='files/'.basename($uploadFile);
			if(mysql_query("INSERT INTO `aka_pro_list` (`ID` ,`date` ,`Filename` ,`Bes`) VALUES ('".$id."', '".$date."', '".$uploadFile."', '".$_POST['beschr']."') ")){
				echo '<font color="green"><b>Datei ist in Ordnung und Sie wurde erfolgreich hochgeladen.</b></font>';
				#echo "Hier sind die Fehler informationen:\n";
				#print_r($_FILES);
				########### email ##############
				include_once('mailer/class.phpmailer.php');
				$mail    = new PHPMailer();
				$mail->From = "KKoolljjaa@googlemail.com";
				$mail->FromName = "Kolja Windeler";
				$mail->AddAddress("akakraft-l@listserv.uni-hannover.de");
				#$mail->AddAddress("KKoolljjaa@gmail.com");
				$mail->Subject = "Aka Protokoll Update";
				### umlaute rauswerfen
				$umlaute = array('ä', 'ö', 'ü','Ä','Ö','Ü','ß');
				$htmlcode = array(chr(228), chr(246),chr(252),chr(196),chr(214),chr(220),chr(223));
				$punkte = str_replace($umlaute, $htmlcode, $_POST['beschr']);
				### umlaute rauswerfen
				$text="Hallo Leute, \r\nes wurde soeben ein neues Protokoll hochgeladen.\r\n\r\nDie wesentlichen Punkte: \r\n -".str_replace('//',' -',$punkte);
				$text.="\r\n\r\nEinzusehen unter:\r\nhttp://portal.akakraft.de/protokoll/ | PW: akapw  \r\nBeste Gr".chr(252).chr(223)."e, der Protokollwart.\r\n\r\n Diese Mail wurde automatisch erzeugt und hat nur den Absender wegen des Verteilers.";
				$mail->Body  = $text;

				if(!$mail->Send())
					{	echo '<br><font color="red"><b>Die Mail konnte nicht verschickt werden.</b></font>';	} 
				else 	{	echo '<br><font color="green"><b>Die Mail wurde erfolgreich verschickt.</b></font>';  };	
				########### email ##############
				};
			}
		else{
			echo "Es wurde ein Fehler gemeldet!\nHier sind die Fehler informationen:\n";
			print_r($_FILES);
			}
		echo "</pre>";
		}
	else {
		echo 'Der Typ der Datei ist :<b>'.$_FILES['userfile']['type'].'</b> und nur <b>application/pdf</b> dateien sind erlaubt! <br>Upload abgelehnt!';
		$beschr_post=$_POST['beschr'];
		};
	};
#################### upload verarbeiten #########################
#################### upload tabelle #########################
$options_tag='';	$options_monat='';		$options_jahr='';
for($a=0;$a<=30;$a++) { $b=$a+1; $options_tag[$a]=$b; };
for($a=0;$a<=11;$a++) { $b=$a+1; $options_monat[$a]=$b; };
for($a=date('Y',time())-5;$a<=date('Y',time())+5;$a++) { $b=$a-(date('Y',time())-5); $options_jahr[$b]=$a; };
$values_tag=$options_tag;
$values_monat=$options_monat;
$values_jahr=$options_jahr;

echo	'<input type="hidden" name="MAX_FILE_SIZE" value="100000">
			<table width="100%" class="singletable">
				<tr><th width="250"><font color="#ffffff" ><b>Datei ausw&auml;hlen:</b></th>
						<td><form enctype="multipart/form-data" action="index.php?mod=upload&'.SID.'" method="post">
								<input name="userfile" type="file" size="60"></td></tr>
				<tr><th><font color="#ffffff"><b>Beschreibung eingeben:<b><br><i>"//" trennt die Punkte</i></th>
						<td><textarea name="beschr" cols="60" >'.$beschr_post.'</textarea></td></tr>
				<tr><th><font color="#ffffff"><b>Datum manipulieren:<b></th>
						<td><select name="tag">'.select($values_tag,$options_tag,date('d',time())).'</select>
								<select name="monat">'.select($values_monat,$options_monat,date('m',time())).'</select>
								<select name="jahr">'.select($values_jahr,$options_jahr,date('Y',time())).'</select>
								</td></tr>
				<tr><th><font color="#ffffff"><b>Upload:<b><br><i>Maximale Filesize: 100 MB</i></th>
						<td><input type="submit" name="upload" value="Upload starten"></td></tr>
			</table></form>';

tab_end();
?>
