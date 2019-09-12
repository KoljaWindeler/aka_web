<?php
if(isset($_GET['logout'])){		require_once('login.php'); 			};
if(mysqli_num_rows($mysqli->query("Select * FROM `sec` WHERE `key`='".$_SESSION['session_key']."' AND `ip`='".$_SERVER['REMOTE_ADDR']."'"))<1)
	{
	require_once('login.php');
	};
?>