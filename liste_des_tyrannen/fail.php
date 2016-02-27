<?php
session_start();

if(!empty($_POST)){
    foreach( $_POST as $task_id => $index){
        if($index!="versaut"){
            $task_id=explode(',',$task_id);
            // jetzt steht in task_id[0] die task_id und in task_id[1] die user_id
            
            // such dir die liste, und die anzahl für die success raus
            list($db_task_user_fail,$db_task_user_num_fail)=
            mysql_fetch_row(mysql_db_query($db,"SELECT `FAIL`,`NUM_FAILED` FROM `aka_tasks_user` WHERE `id`=".$task_id[1].";",$verbindung));
            if(!empty($db_task_user_fail)) { $db_task_user_fail.=','; };
            $db_task_user_fail.=$task_id[0];
            if(!empty($db_task_user_num_fail)) { $db_task_user_num_fail++; } else { $db_task_user_num_fail=1; };
            
            //get task details
            list($db_old_task_cap,$db_old_task_desc)=
            mysql_fetch_row(mysql_db_query($db,"SELECT `title`,`desc` FROM `aka_tasks` WHERE `id`=".$task_id[0].";",$verbindung));
            
            if(mysql_query( "UPDATE `aka_tasks_user` SET `ACTIVE_TASK`='0' WHERE `id`='".$task_id[1]."';" ) &&
               mysql_query( "UPDATE `aka_tasks_user` SET `FAIL`='".$db_task_user_fail."' WHERE `id`='".$task_id[1]."';" ) &&
               mysql_query( "UPDATE `aka_tasks_user` SET `NUM_FAILED`='".$db_task_user_num_fail."' WHERE `id`='".$task_id[1]."';" ) &&
               mysql_query( "UPDATE `aka_tasks` SET `status`='2' WHERE `id`='".$task_id[0]."';" ) &&
               mysql_query( "UPDATE `aka_tasks` SET `date_post`='".time()."' WHERE `id`='".$task_id[0]."';" )){
                tab_box("100%",100,'left','Info','Fehlschlag gespeichert, <a href="index.php?mod=addtask&bypass_cap='.$db_old_task_cap.'&bypass_desc='.$db_old_task_desc.'">weitergeben</a>.<br><br>');
            }
               
        };
    }
};

// liste alle offenen aufgaben
echo '<table width="500" border="1" class="singletable">
<form name="edit" action="index.php?mod=fail&'.SID.'" method="POST">
<tr><th width="20">Id</th><th>Akaler</th><th>Title</th><th width="20">Versaut</th></tr>';

// get all task infos, id der aufgabe, beschreibung, titel und die user id, user name
$abfrage="SELECT aka_tasks.id, aka_tasks.desc, aka_tasks.title, aka_tasks_user.id, aka_id.name FROM aka_tasks,aka_tasks_user,aka_id WHERE aka_id.id=aka_tasks_user.id AND aka_tasks.id=aka_tasks_user.ACTIVE_TASK AND aka_tasks_user.ACTIVE_TASK!='0' order by aka_tasks.id asc;";
$erg=mysql_db_query($db,$abfrage,$verbindung);
while(list($db_id,$db_desc,$db_title,$db_user_id, $db_user_name) = mysql_fetch_row($erg)) {

    echo '<tr><td>'.$db_id.'</td><td>'.$db_user_name.'</td><td>'.$db_title.'</td><td><input type="checkbox" name="'.$db_id.','.$db_user_id.'"></td></tr>';
};
echo '<tr><td colspan="4" align="right"><input type="submit" name="abharken" value="versaut"></td></tr></table>';

?>