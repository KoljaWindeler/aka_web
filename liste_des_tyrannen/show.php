<?php
session_start();
$id_arr = explode(',',$_GET['id']);

for($a=0;$a<count($id_arr);$a++){
	$id=$id_arr[$a];
		
	$abfrage="SELECT `desc`,`title`,`status`,`date_pre`,`date_post` FROM `aka_tasks` WHERE `ID`=".$id."";
	$erg=$mysqli->query($abfrage);
	list($db_desc,$db_title,$db_status,$db_pre,$db_post) = mysqli_fetch_row($erg);
	
	// select the user id for a task
	$task_user='';
	$abfrage="SELECT `id` FROM `aka_tasks_user` WHERE `ACTIVE_TASK`=".$id.";";
	$erg=$mysqli->query($abfrage);
	while(list($db_task_user_id) = mysqli_fetch_row($erg)) {
		// get user name 
		if(!empty($db_task_user_id)){
		list($db_task_user)=mysqli_fetch_row($mysqli->query("SELECT `name` FROM `aka_id` WHERE `id`=".$db_task_user_id.";"));
		$task_user.=$db_task_user.', ';
		};
	};
	
	// remove the last ","
	$task_user=substr($task_user,0,-2);
	
	if(!empty($task_user)){
		$zustaendig='<tr><td class="gray"><b>Zust&auml;ndig</b></td><td class="gray">'.$task_user.'</td></tr>';
	} else {
		$zustaendig='';
	}
	
	if($db_status=='0'){ $db_status='offen'; }
	elseif($db_status=='1'){ $db_status='erledigt'; }
	elseif($db_status=='2'){ $db_status='versaut'; };
	
	if(!empty($db_post)){ $db_post=date("d-m-y",$db_post); }
	else { $db_post='Warte auf R&uuml;ckmeldung'; };
	
	
	
	tab_go("500",200,"center","Aufgabenansicht");
	echo '<table width="500" border="1" class="singletable">
	<tr><td width="150"><b>Status</b></td><td>'.$db_status.'</td></tr>
	<tr><td class="gray"><b>Aufgegeben</b></td><td class="gray">'.date("d-m-y",$db_pre).'</td></tr>
	<tr><td><b>Status ge&auml;ndert</b></td><td>'.$db_post.'</td></tr>
	<tr><td class="gray"><b>Titel</b></td><td class="gray">'.$db_title.'</td></tr>
	<tr><td><b>Beschreibung</b></td><td>'.$db_desc.'</td></tr>
	'.$zustaendig.'
	</table>';
	tab_end();
};
echo '<div align="center"><a href="index.php?'.SID.'">Zur&uuml;ck</a></div><br>';
?>
