<?php
if(
	isset($_GET['logout']) ||
	mysqli_num_rows($mysqli->query("Select * FROM `sec` WHERE `key`='".$_SESSION['session_key']."' AND `ip`='".$_SERVER['REMOTE_ADDR']."'"))<1 ||
	$_SESSION['session_user_typ']==$aka_reserve_watcher_state){

		$_SESSION['session_key']="";
		include('login.php');
};

?>
