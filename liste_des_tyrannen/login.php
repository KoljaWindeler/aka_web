<?php
if(!isset($_SESSION))
{
	$SID=session_start();
}

############## tiddyup ###
$time=time()-10*60*60; # 10 Std
$mysqli->query("DELETE FROM `sec`  WHERE `date`<".$time.";");
############## tiddyup ###

############# logout ####
if(isset($_GET['logout'])) {
	if ($_GET['logout'] == 1) {
		$mysqli->query("DELETE FROM `sec` WHERE `key`=" . $_SESSION['session_key'] . ";");
		unset($_SESSION['session_key']);
		session_unset();
		$msg = 'Erfolgreich ausgeloggt.';
	};
}
############# logout ####

if(!empty($_POST['pw']))
	{
	$msg = '<br /><font color="green" />Login, bitte warten...</font>';	
	if($_POST['pw']==$aka_pw) {
		//session_register('session_user_typ');
		$_SESSION['session_user_typ']=1; }
	elseif($_POST['pw']==$aka_tyran_pw) {
		//session_register('session_user_typ');
		$_SESSION['session_user_typ']=$aka_tyran_admin_state; }
	elseif($_POST['pw']==$aka_super_admin_pw) {
		//session_register('session_user_typ');
		$_SESSION['session_user_typ']=$aka_super_admin_state; };
		
	if($_POST['pw']==$aka_pw OR $_POST['pw']==$aka_tyran_pw OR $_POST['pw']==$aka_super_admin_pw) {
		//session_register('session_key');
		$_SESSION['session_key']=rand(0,99999);
		$mysqli->query("INSERT INTO `sec` ( `id` ,  `date` , `ip` , `key` ) VALUES ( NULL , '".time()."', '".$_SERVER['REMOTE_ADDR']."', '".$_SESSION['session_key']."');");
		}
	else{
		$msg='Passwort falsch';
		};
};

################################################# GUI #################################################
if(empty($_SESSION['session_key']))
	{
		$msg="";
	require_once('design/box.php');
	htmlhead('AkAKraft Drinks Login','','');
	echo	'<table style="height: 100%; width: 100%" border="0" ><tr><td valign="middle" align="center"><img src="img/Logo.gif"><br /><br /><br /><br />
		<form method=post action="index.php?'.$_SERVER['QUERY_STRING'].'" name="login">';

	tab_go(600,'','center','Login');
	echo	'	<table align="center" width="400" style="height: 100%">	
			<tr><td width="40%">Passwort:</td><td align="right" width="40%"><INPUT type="password" name="pw" tabindex="2" class="inputbox" /></td>
			<td><INPUT type="submit" name="submit" value="Login" tabindex="3" class="inputbox" /></td></tr>
			<tr>	<td align="center" colspan="3">&nbsp;'.$msg.'</td></tr></table>';
	tab_end();

	echo	'</form></td></tr><tr><td align="right" height="20"><a href="mailto:KKoolljjaa+aka@googlemail.com">Admin</a></td></tr></table>
	<script type="text/javascript" language="Javascript">document.login.pw.focus();</script></body></html>'; 
	exit;
	};
################################################# GUI #################################################
?>
