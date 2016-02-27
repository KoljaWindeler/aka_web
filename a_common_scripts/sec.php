<?php
if(isset($_GET['logout'])){		require_once('login.php'); 			};
if(mysql_num_rows(mysql_query("Select * FROM `sec` WHERE `key`='".$_SESSION['session_key']."' AND `ip`='".$_SERVER['REMOTE_ADDR']."'"))<1)
	{
	include('login.php');
	};
?>