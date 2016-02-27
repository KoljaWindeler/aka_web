<?php
########### vorbereitungen ###############
session_start($SID);
include('scripts/config.php');
include('scripts/fkt_jkw.php');
include('design/box.php');
htmlhead('Aka Getr&auml;nkeabrechnung','',0);
include("scripts/jsc.php");
include('scripts/sec.php');
########### vorbereitungen ###############
########### menü ###############
echo '<table border=0 width="100%"><tr><td width="5%">&nbsp;</td><td width="90%"  class="head">';
#tab_go("100%",250,'center','');
echo'<div style="float:right;"><a href="index.php?'.SID.'" class="head">&Uuml;bersicht</a> &nbsp; | &nbsp; ';
if($_SESSION['session_user_typ']==$aka_drinks_admin_state || $_SESSION['session_user_typ']==$aka_super_admin_state){ // admin check
	echo'<a href="index.php?mod=adduser&'.SID.'" class="head">User managment</a> &nbsp; | &nbsp; 
			<a href="index.php?mod=money&'.SID.'" class="head">Geld einzahlen</a> &nbsp; | &nbsp; 
			<a href="index.php?mod=bill&'.SID.'" class="head">Striche abrechnen</a> &nbsp; | &nbsp; 
			<a href="index.php?mod=liste&'.SID.'" class="head">Liste drucken</a> &nbsp; | &nbsp; ';
	};
echo'	<a href="index.php?'.SID.'&mod=rules" class="head">Regeln</a> &nbsp; | &nbsp;
	<a href="index.php?logout=1&'.$SID.'" class="head">Logout</a> &nbsp; v 4.1b</div><br>';
#tab_end();
echo	'</td><td width="5%">&nbsp;</td></tr><tr><td>&nbsp;</td><td>';
########### menü ###############
########### reinladen ###############
if($_GET['mod']=='adduser')		 {	include('adduser.php'); 		}
elseif($_GET['mod']=='rmuser') {	include('rmuser.php');			}
elseif($_GET['mod']=='money') {	include('addmoney.php');			}
elseif($_GET['mod']=='rules') {	include('rules.php');			}
elseif($_GET['mod']=='bill')        {	include('rmmoney.php');				}
elseif($_GET['mod']=='counter'){ include('countershow.php');	}
elseif($_GET['mod']=='show')    { include('show.php');				}
elseif($_GET['mod']=='liste')     {	include('liste.php'); 				}
else 											{	include('tab.php'); 				};
########### reinladen ###############
echo '</td><td>&nbsp;</td></tr></table><center>';
include("counter.php");
echo impressum().'</center></body></html>';
?>
