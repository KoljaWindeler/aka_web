<?php
////////////////////////////VARS UND BILD ///////////////////////
$x = 600;
$y = 200;
$im = imagecreatetruecolor($x,$y);
$weiss  = ImageColorAllocate ($im, 255, 255, 255);
imagefilledrectangle($im,0,0,$x,$y,$weiss);
$time = $_GET['time'];
include('../scripts/config.php');
$db_counter="aka_counter";
////////////////////////////VARS UND BILD ///////////////////////
////////////////////////////db in array///////////////////////
$array = $mysqli->query("SELECT time,typ FROM `".$db_counter."` WHERE typ<>'9'"); 
$a=0;
while(list($l_time,$l_typ) = mysqli_fetch_row($array))
	{
	$data[$a][0]=$l_time;
	$data[$a][1]=$l_typ;
	$a++;
	};
////////////////////////////db in array///////////////////////
////////////////////////////array sort///////////////////////
for($i=0;$i<=23;$i++)
	{
	$imin=mktime($i,0,0,date("n",$time),date("j",$time),date("Y",$time));
	$imax=mktime($i+1,0,0,date("n",$time),date("j",$time),date("Y",$time));
	
	$nr=0;	$nr2=0;
	for($i2=0;$i2<=count($data);$i2++)
		{
		if($data[$i2][0]>$imin AND $data[$i2][0]<$imax)
			{	
			$count0[$i][$nr2]=$data[$i2][0];	
			$nr2++;	
			};
		};
	};
////////////////////////////array sort////////////////////////
////////////////////////////array scale //////////////////////
$maxi=0;
for($i=0;$i<=23;$i++)
	{	
	if(count($count0[$i])>$maxi) 
		{	$maxi = count($count0[$i]);	};
	};
if($maxi>0) {
	for($i=0;$i<=23;$i++)
		{ 
		$h0[$i] = round((count($count0[$i])/$maxi)*100);
		};
	};
//////////////////////////array scale/////////////////////////
$lueckebr=5;
$luecke=$lueckebr*30;   //150
$breite= round((($x-30)-$luecke)/23);  //19
$i=0;
while($i<=23)
	{	
	$xnull=($lueckebr*($i))+($breite*($i));
	$ynull=30;
	$xeins=$xnull+$breite;
	imageline($im,$xnull-3,0,$xnull-3,$y,ImageColorAllocate ($im, 234, 234, 234));
	$i++;
	};

$i=0;
while($i<=23)
	{
	$xnull=($lueckebr*($i))+($breite*($i));
	$ynull=30;
	$xeins=$xnull+$breite;
	
	if(count($count0[$i])>0)
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
		imagestring($im,"Arial",$xnull+round($breite/6),$y-$ynull+5,count($count0[$i]),0);
		};
	imagestring($im,"Arial",$xnull+3,$y-10,$i.'.',0); 
	$i++;
	};
imagestring($im,"Arial",$xnull+$breite+15,$y-35,'Zugriffe',0); 
imagestring($im,"Arial",$xnull+$breite+15,$y-10,'Uhrzeit',0); 
imagejpeg($im);
imagedestroy($im);

?>