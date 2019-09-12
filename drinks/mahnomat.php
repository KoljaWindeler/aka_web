<?php
include('../a_common_scripts/config.php');
include('../a_common_scripts/fkt_jkw.php');

include('collect_data.php');
$daten=kolja_sort($daten,8); // nach guthaben
$daten=array_reverse($daten); // aufsteigend

list($max)=mysqli_fetch_row($mysqli->query("SELECT `id` FROM `aka_id` ORDER BY `id` DESC LIMIT 0,1"));
for($a=0;$a<=$max;$a++){	$changed[$a]=0; }
$time=time();

// guthaben zuordnen
$geld=array();
for($b=0;$b<count($daten);$b++){
    $geld[$daten[$b][0]]=$daten[$b][8];
}
//

$abfrage="SELECT `user` FROM `aka_mahnomat` WHERE `time`<'".$time."'";
$erg=mysqli_db_query($db,$abfrage,$verbindung);
while(list($db_user) = mysqli_fetch_row($erg)) {
    $users=explode(',',$db_user);
    for($a=0;$a<count($users);$a++){
        if($geld[$users[$a]]<-5){
            $changed[$users[$a]]=1;
            echo 'User id '.$users[$a].' gemahnt<br>';
            $sql="INSERT INTO `aka_money` (`id` ,`value`, `date`) VALUES ('".$users[$a]."', '-5', ".$time.")";
            #echo $sql.'<br>';
            $mysqli->query($sql);
        } else {
            echo 'User id '.$users[$a].' hat sich nochmal gerettet<br>';
        }
    }    
};
$mysqli->query( "DELETE FROM `aka_mahnomat` WHERE `time`<'".$time."'" );
# email
include('email.php');
# email
#echo $time;
?>
