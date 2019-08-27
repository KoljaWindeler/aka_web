<?php
include_once("db.php");
############################# Connect to DB ################
$text = 'Konnte die Verbindung zur Datenbank nicht herstellen. <br><i> Bitte versuchen sie es in wenigen Minuten erneut!</i>';
$mysqli = new mysqli($host,$db_user,$db_pw) or die ($text);
$mysqli->select_db($db) or die ($text);
$connection = @mysqli_connect($host,$db_user,$db_pw);
############################# Connect to DB ################
$bg='#ededed';

$query="SELECT * FROM aka_passwords";
$result=$mysqli->query($query);
while(list($db_id,$db_bedeutung,$db_wert) = $result->fetch_row()) {
	if($db_bedeutung=="aka_passwort")
		$aka_pw=$db_wert;
	if($db_bedeutung=="aka_super_admin_pw")
		$aka_super_admin_pw=$db_wert;
	if($db_bedeutung=="aka_files_pw")
		$aka_files_pw=$db_wert;
	if($db_bedeutung=="aka_drinks_pw")
		$aka_drinks_pw=$db_wert;
	if($db_bedeutung=="aka_protokoll_pw")
		$aka_protokoll_pw=$db_wert;
	if($db_bedeutung=="aka_tyran_pw")
		$aka_tyran_pw=$db_wert;
	if($db_bedeutung=="aka_reserve_pw")
		$aka_reserve_pw=$db_wert;
	if($db_bedeutung=="aka_reserve_watcher_pw")
		$aka_reserve_watcher_pw=$db_wert;


	if($db_bedeutung=="aka_files_admin_state")
		$aka_files_admin_state=$db_wert;
	if($db_bedeutung=="aka_drinks_admin_state")
		$aka_drinks_admin_state=$db_wert;
	if($db_bedeutung=="aka_protokoll_admin_state")
		$aka_protokoll_admin_state=$db_wert;
	if($db_bedeutung=="aka_tyran_admin_state")
		$aka_tyran_admin_state=$db_wert;
	if($db_bedeutung=="aka_reserve_admin_state")
		$aka_reserve_admin_state=$db_wert;
	if($db_bedeutung=="aka_super_admin_state")
		$aka_super_admin_state=$db_wert;
	if($db_bedeutung=="aka_reserve_watcher_state")
		$aka_reserve_watcher_state=$db_wert;
};
?>
