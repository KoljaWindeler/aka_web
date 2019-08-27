<?php
$abfrage="SELECT `ID`, `Name`,`EMAIL`, `LAST_MAIL` FROM `aka_id`";
$erg=$mysqli->query($abfrage);
$a=0;
while(list($db_id,$db_name,$db_email,$db_email_pol) = mysqli_fetch_row($erg)) {
	$daten[$a][0]=$db_id; 
	$daten[$a][1]=$db_name;
	unset($temp);
	$temp=explode(' ',$db_name);
	$daten[$a][11]=$temp[1];
	$daten[$a][12]=$db_email;
	$daten[$a][13]=$db_email_pol;
	unset($db_id,$db_name,$db_email,$db_email_pol);
	$a++;};
######## user abfragen ################
######## guthaben abfragen ################
for($k=0;$k<$a;$k++) {
	$daten[$k][2]=0;
	$abfrage="SELECT `value` FROM `aka_money` WHERE `ID`=".$daten[$k][0]."";
	$erg=$mysqli->query($abfrage);
	while(list($db_value) = mysqli_fetch_row($erg)) {
		$daten[$k][2]+=$db_value; };
		};
######## guthaben abfragen ################
######## striche abfragen ################
for($k=0;$k<$a;$k++) {
	$daten[$k][3]=0;
	$abfrage="SELECT `value` FROM `aka_verbrauch` WHERE `ID`=".$daten[$k][0]."";
	$erg=$mysqli->query($abfrage);
	while(list($db_value) = mysqli_fetch_row($erg)) {
		$daten[$k][3]+=$db_value; };
		};
######## striche abfragen ################
######## datum letzter aufbuchen abfragen ################
for($k=0;$k<$a;$k++) {
	list($daten[$k][4],$daten[$k][5])=
		mysqli_fetch_row($mysqli->query("SELECT `date`,`value` FROM `aka_money` WHERE `ID`=".$daten[$k][0]." ORDER BY `date`DESC LIMIT 0,1"));
	$daten[$k][4]+=1-1;
	#$daten[$k][5]+=1-1;
	$daten[$k][5]=convert2money($daten[$k][5]);
	};
######## datum letzter aufbuchen abfragen ################
######## striche  letzter abbuchen abfragen ################
for($k=0;$k<$a;$k++) {
	list($daten[$k][6],$daten[$k][7])=
		mysqli_fetch_row($mysqli->query("SELECT `value`, `date` FROM `aka_verbrauch` WHERE `ID`=".$daten[$k][0]." ORDER BY `date`DESC LIMIT 0,1"));
	$daten[$k][6]+=1-1;
	$daten[$k][7]+=1-1;
	};
######## striche  letzter abbuchen abfragen ################
######## guthaben ################
for($k=0;$k<$a;$k++) {
	$daten[$k][8]=convert2money(round(100*($daten[$k][2]-0.75*$daten[$k][3]))/100);
	};
######## guthaben ################
######## ranking / aktuell ################
$daten=kolja_sort($daten,6);
for($k=0;$k<$a;$k++) {
	$daten[$k][9]=$k+1;
	};
######## ranking / aktuell ################
######## ranking / gesamt ################
$daten=kolja_sort($daten,3);
for($k=0;$k<$a;$k++) {
	$daten[$k][10]=$k+1;
	};
######## ranking / gesamt ################
$max_user=$a-1;

