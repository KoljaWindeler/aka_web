<?php
////////////////////////////VARS UND BILD ///////////////////////
$x = 600;
$y = 200;
$time = time();
$im = imagecreatetruecolor($x,$y);
$weiss  = ImageColorAllocate ($im, 255, 255, 255);
imagefilledrectangle($im,0,0,$x,$y,$weiss);
$db_id = $_GET['id'];
include('../scripts/config.php');
$db=$_GET['db'];
////////////////////////////VARS UND BILD ///////////////////////
////////////////////////////db in array///////////////////////
$jahr_anfang=mktime(0,0,1,0,0,date("Y",$time));
$array = mysql_query("SELECT `value`,`date` FROM `".$db."` WHERE `id`=".$db_id." AND `date`>".$jahr_anfang.";"); 
$a=0;
while(list($l_value,$l_time) = mysql_fetch_row($array))
	{
	if($l_value[0]<>"-") {			 // will das neg startguthaben nicht drin haben
		$data[$a][0]=$l_time;
		$data[$a][1]=$l_value;
		$a++;
		};
	};
////////////////////////////db in array///////////////////////
////////////////////////////array sort///////////////////////
for($i=0;$i<12;$i++){$count[$i]=0;};
for($i=0;$i<=count($data);$i++){
	$mon = round(date("m",$data[$i][0]));
	$count[$mon]+=$data[$i][1];
	};
////////////////////////////array sort////////////////////////
////////////////////////////array scale //////////////////////
$maxi=0;
for($i=0;$i<12;$i++)
	{	
	if($count[$i]>$maxi) 
		{	$maxi = $count[$i];	};
	};
if($maxi>0) {
	for($i=0;$i<12;$i++)
		{ 
		$h0[$i] = round($count[$i]/$maxi*100); // h0 => gescaltes in prozent
		};
	};
//////////////////////////array scale/////////////////////////
for($i=0;$i<11;$i++)
	{	
	$xnull=round(($x-60)/12*($i+1));
	$ynull=30;
	$xeins=$xnull+$breite;
	imageline($im,$xnull-3,0,$xnull-3,$y,ImageColorAllocate ($im, 234, 234, 234));
	};
######### mal sehen

for($i=1;$i<=12;$i++)
	{
	$xnull=round(($x-60)/12*($i-1))+5; // 5 px lücke
	$ynull=30;
	######
	$breite=15;
	$xeins=$xnull+$breite;
	
	if($count[$i]>0)
		{
		$ynull=round($h0[$i]*($y-30)/100);
		$points[0]=$xnull;		$points[1]=$y-$ynull;
		$points[2]=$xnull+5;		$points[3]=$y-$ynull-5;
		$points[4]=$xnull+5+$breite;		$points[5]=$y-$ynull-5;
		$points[6]=$xnull+5+$breite;		$points[7]=$y-6;
		$points[8]=$xnull+$breite;		$points[9]=$y-1;
		$points[10]=$xnull+$breite;		$points[11]=$y-$ynull;
		imagerectangle($im,$xnull,$y-$ynull,$xnull+$breite,$y-1,0);
		imagefilledpolygon($im,$points,6,ImageColorAllocate ($im, 222, 222, 222));
		imagepolygon($im,$points,6,0);
		imageline($im,$xnull+$breite,$y-$ynull,$xnull+5+$breite,$y-$ynull-5,0);
		imagestring($im,"Arial",$xnull+round($breite/6),$y-$ynull-20,$count[$i].chr(128),0);
		};
	imagestring($im,"Arial",$xnull+3,$y-10,$i.'.',0); 
	};

imagestring($im,"Arial",$x-45,$y-10,'Monat',0); 
imagejpeg($im);
imagedestroy($im);

?>