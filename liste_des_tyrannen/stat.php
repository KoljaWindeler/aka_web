<?php
if($_SESSION['session_user_typ']<>$aka_tyran_admin_state && $_SESSION['session_user_typ']<>$aka_super_admin_state) { exit('falsches passwort'); };
##################### security ################################
##################### daten annehmen ###########################
if(isset($_POST['senden'])) {
	if(!$mysqli->query( "INSERT INTO `aka_verbrauch` (`id` ,`value`, `date`) VALUES ('".$a."', '".$_POST['addbill_'.$a]."', ".$time.")" )){
		echo 'ohoh';
		};
	};
##################### daten annehmen ###########################
###### oberfläche ####
- Datum auswählen
- Betrag eingeben

Konto übernommen mit x €
einnahmen von allen summe ist : y €
Summe der Söffker auszahlungen ist: z €
feld für geschätzen besitz an getränken IN der aka: a €
summe der geldbestände in der datenbank ( alle endbeträge aufsummieren): b €

ist gewinn: Y-Z-B-X+A
das was ich bekommen hab - das was an söffker ging - was die leute noch in der db/konto haben - mit was ich das konto übernommen habe + was ich noch in der aka habe

geschätzer soll gewinn: alle striche gesamt * 0,15
