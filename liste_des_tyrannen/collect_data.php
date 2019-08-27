<?php
##########spec
# 00 = vorname
# 01 = number of success
# 02 = number of fails
# 03 = id of active task
# 04 = csv liste der erfolge
# 05 = csv liste der fails
# 06 = active oder passiv
# 07 = gefehlte Arbeitstage

# 08 = 
# 09 = 
# 10 = 

# 11 = Nachname
# 12 = email
##########spec
######## user abfragen ################
unset($daten);
$abfrage="SELECT aka_id.ID, aka_id.Name,aka_id.EMAIL FROM aka_id,aka_tasks_user where aka_tasks_user.id=aka_id.id order by aka_tasks_user.state desc, aka_id.Name asc";
$erg=$mysqli->query($abfrage);
while(list($db_id,$db_name,$db_email) = mysqli_fetch_row($erg)) {
	unset($temp);
	$temp=explode(' ',$db_name);
	$daten[$db_id][0]=$temp[0];
	$daten[$db_id][7]=0;
	$daten[$db_id][11]=$temp[1];
	$daten[$db_id][12]=$db_email;
	unset($db_id,$db_name,$db_email);
	//$a++;
	};
######## user abfragen ################
######## weitere daten abfragen ################
$abfrage="SELECT `ID`, `STATE`,`NUM_SUCCESS`, `NUM_FAILED`, `ACTIVE_TASK`, `SUCCESS`, `FAIL` FROM `aka_tasks_user`";
$erg=$mysqli->query($abfrage);
$a=0;
while(list($db_id,$db_state,$db_num_success,$db_num_failed,$db_active_task,$db_success,$db_fail) = mysqli_fetch_row($erg)) {
	if(!empty($daten[$db_id][0])){
		$daten[$db_id][1]=$db_num_success;
		$daten[$db_id][2]=$db_num_failed;
		$daten[$db_id][3]=$db_active_task;
		$daten[$db_id][4]=$db_success;
		$daten[$db_id][5]=$db_fail;
		$daten[$db_id][6]=$db_state;
	};
};
######## weitere daten abfragen ################
?>
