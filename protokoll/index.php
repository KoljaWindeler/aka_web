<?php
########### vorbereitungen ###############
session_start($SID);
require_once('../a_common_scripts/config.php');
require_once('../a_common_scripts/sec.php');
require_once('../a_common_scripts/fkt_jkw.php');
require_once('design/box.php');
htmlhead('Aka Protokollverwaltung','',0);
require_once("../a_common_scripts/jsc.php");
########### vorbereitungen ###############
########### menü ###############
echo '<table border=0 width="100%"><tr><td width="5%">&nbsp;</td><td width="90%"  class="head">';

#tab_go("100%",250,'center','Men&uuml; der Protokollverwaltung Version 0.1 alpha ');

if(!isset($_GET['from']))		{ $_GET['from']=1; 			};
if(!isset($_GET['to']))			{ $_GET['to']=time(); 	};

$this_year_start=mktime(0,0,0,1,1,date('Y',time()));
$this_year_end=mktime(0,0,0,1,1,date('Y',time())+1)-1;

$last_year_start=mktime(0,0,0,1,1,date('Y',time())-1);
$last_year_end=mktime(0,0,0,1,1,date('Y',time()))-1;

$this_month_start=mktime(0,0,0,date('m',time()),1,date('Y',time()));
$this_month_end=mktime(0,0,0,date('m',time()),date('t',time()),date('Y',time()));

echo'<div style="float:right;">
<a href="index.php?from=1&to='.time().'&'.SID.'" class="head">Alle</a> &nbsp; | &nbsp; 
<a href="index.php?from='.$this_year_start.'&to='.$this_year_end.'&'.SID.'" class="head">Dieses Jahr</a> &nbsp; | &nbsp; 
<a href="index.php?from='.$last_year_start.'&to='.$last_year_end.'&'.SID.'" class="head">Letztes Jahr</a> &nbsp; | &nbsp; 
<a href="index.php?from='.$this_month_start.'&to='.$this_month_end.'&'.SID.'" class="head">Diesen Monat</a> &nbsp; | &nbsp; ';

if($_SESSION['session_user_typ']==$aka_protokoll_admin_state || $_SESSION['session_user_typ']==$aka_super_admin_state){
	echo'<a href="index.php?mod=upload&'.SID.'" class="head">Upload</a> &nbsp; | &nbsp; ';
	};
echo'
<a href="index.php?logout=1" class="head">Logout</a> &nbsp; v 1.1b</div><br>';
#tab_end();
echo	'</td><td width="5%">&nbsp;</td></tr><tr><td>&nbsp;</td><td>';
########### menü ###############
########### reinladen ###############
if($_GET['mod']=='upload')		 	{	include('upload.php'); 			}
elseif($_GET['mod']=='edit') 		{	include('edit.php');				}
elseif($_GET['mod']=='counter')	{ include('countershow.php');	}
else 												{	include('tab.php'); 				};
########### reinladen ###############
echo '</td><td>&nbsp;</td></tr></table><center>';
include("counter.php");
echo impressum().'</center></body></html>'
?>
