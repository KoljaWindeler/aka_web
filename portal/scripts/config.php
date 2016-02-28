<?php
#############################Sql Login################
$db='d00b711e';
$db_user = 'd00b711e';
$db_pw = 'SuVPH3bat7ydWPr7';
$host = 'localhost';
#############################Sql Login################
#############################DB Abrufn################
$text = 'Konnte die Verbindung zur Datenbank nicht herstellen. <br><i> Bitte versuchen sie es in wenigen Minuten erneut!</i>';
MYSQL_CONNECT($host,$db_user,$db_pw) or die ($text);
MYSQL_SELECT_DB($db) or die ($text);
$verbindung = @mysql_connect($host,$db_user,$db_pw);
#############################DB Abrufn################
$bg='#ededed';
$aka_pw='akapw';
$aka_admin_pw='kryptisch';
?>