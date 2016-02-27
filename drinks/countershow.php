<?
session_start();
$time=time();
$time2=time()-86400;
$mode = $_GET['mode'];
tab_go("100%",200,"center","Counter");
if($mode=='heute') 
	{
	echo'<u><b>Heute bisher:</b></u><br>
	<center><img src="img/counter_img.php?time='.$time.'" ></center><br><hr><br>
	<u><b>Gestern gesamt:</b></u><br>
	<center><img src="img/counter_img.php?time='.$time2.'" ></center><br>';
	}
elseif($mode=='gestern')
	{
	echo'<u><b>Gestern gesamt:</b></u><br>
	<center><img src="img/counter_img.php?time='.$time2.'" ></center><br><hr><br>
	<u><b>Heute bisher:</b></u><br>
	<center><img src="img/counter_img.php?time='.$time.'" ></center><br>';
	};
echo '<div align="center"><a href="index.php?'.SID.'">Zur&uuml;ck</a></div><br>';
tab_end();
?>