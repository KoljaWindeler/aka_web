<?php
$_GET['mod']="";
########### vorbereitungen ###############
session_start();
if(!isset($SID)) { $SID = session_id(); };
include('../a_common_scripts/config.php');
include('../a_common_scripts/fkt_jkw.php');
include('design/box.php');
htmlhead('Aka Arbeitsliste','',0);
include("../a_common_scripts/jsc.php");
include('../a_common_scripts/sec.php');
########### vorbereitungen ###############
########### menü ###############
echo '<table border=0 width="100%"><tr><td width="5%">&nbsp;</td><td width="90%"  class="head">';
#tab_go("100%",250,'center','');
echo'<div style="float:right;"><a href="index.php?'.SID.'" class="head">&Uuml;bersicht</a> &nbsp; | &nbsp; ';
if($_SESSION['session_user_typ']==$aka_tyran_admin_state || $_SESSION['session_user_typ']==$aka_super_admin_state){
	echo'		<a href="index.php?mod=addtask&'.SID.'" class="head">Neue Aufgabe anlegen</a> &nbsp; | &nbsp;
			<a href="index.php?mod=state&'.SID.'" class="head">Aktiv/passiv &auml;ndern</a> &nbsp; | &nbsp; 
			<a href="index.php?mod=addperson&'.SID.'" class="head">Personal aufstocken</a> &nbsp; | &nbsp; 
			<a href="index.php?mod=success&'.SID.'" class="head">Aufgabe erledigt</a> &nbsp; | &nbsp; 
			<a href="index.php?mod=fail&'.SID.'" class="head">Aufgabe versaut</a> &nbsp; | &nbsp; ';
};
	echo'
<a href="index.php?logout=1&'.$SID.'" class="head">Logout</a> &nbsp; v 0.2b</div><br>';
#tab_end();
echo	'</td><td width="5%">&nbsp;</td></tr><tr><td>&nbsp;</td><td>';
########### menü ###############
########### reinladen ###############
if($_GET['mod']=='addperson')	{	include('adduser.php'); 	}
elseif($_GET['mod']=='addtask') {	include('addtask.php');		}
elseif($_GET['mod']=='movetask'){	include('movetask.php');	}
elseif($_GET['mod']=='success') {	include('success.php');		}
elseif($_GET['mod']=='fail') 	{	include('fail.php');		}
elseif($_GET['mod']=='counter')	{ 	include('countershow.php');	}
elseif($_GET['mod']=='show')    { 	include('show.php');		}
elseif($_GET['mod']=='state')   { 	include('state.php');		}
else 				{	include('tab.php'); 		};
########### reinladen ###############
echo '</td><td>&nbsp;</td></tr></table><center>';
include("counter.php");
echo impressum().'</center></body></html>';
?>
