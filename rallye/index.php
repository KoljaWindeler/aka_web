<?php
session_start($SID);
include('../a_common_scripts/config.php');
include('../a_common_scripts/fkt_jkw.php');
include('../drinks/design/box.php');
htmlhead('Aka Rallye','',1);
include("../a_common_scripts/jsc.php");
include('../drinks/collect_data.php');
$daten=kolja_sort($daten,1);
$daten=array_reverse($daten);


if(!empty($_POST['send'])){
    if($_POST['rm2']=='-'){ $name=$_POST['rm']; }
    else { $name=$_POST['rm2']; };
    
    if($_POST['action']=='add'){ $group=$_POST['group']; };
    if($_POST['action']=='new'){
        $nfound=true;
        $i=0;
        while($nfound){
            $i++;
            $qry=mysql_query("SELECT count(*) from rallye where id='".$i."'");
            list($count)=mysql_fetch_row($qry);
            if($count==0){ $nfound=false; }
        };
        $group=$i;
    };
    if($_POST['action']=='rem'){
        mysql_query("DELETE FROM rallye where name='".$name."'");
        #echo "DELTE FROM rallye where name='".$name."' limit 0,1";
    } else {
        //1. checken obs den user schon gibt
        $qry=mysql_query("SELECT count(*) from rallye where name='".$name."'");
        list($db)=mysql_fetch_row($qry);
        if($db>0){ // update
            #echo "update:";
            mysql_query("UPDATE `d00b711e`.`rallye` SET `id` = '".$group."' WHERE `rallye`.`name` ='".$name."' LIMIT 1 ;");
        } else { // insert
            #echo "insert";
            mysql_query("INSERT INTO `d00b711e`.`rallye` (`id` ,`name`)VALUES ('".$group."','".$name."');");
        }
    };
}

echo '<body text="#000055">';

tab_box("650",100,'center','Aka Rallye 2015',
'<table width="100%">
<tr>
	<td><img src="../drinks/img/Logo.gif"></td>
	<td valign="center"><font size="+4"><b>Aka Rallye 2015</b></font></center></td>
</tr>
<tr><td colspan="2" align="center">
am <b>Samstag, der 27. Juni 2015</b> findet die diesjährige Rallye der Akademischen Gruppe für Kraftfahrwesen an der Universität Hannover e.V. (AKAKRAFT) statt.<br>
<br>
Start und Ziel der diesjährigen Rallye ist das <b>Gebäude 1108, Leibniz Universität Hannover, Im Moore 11b, 30167 Hannover</b>.<br>
Wir treffen uns um <b>9 Uhr</b>, spätestens gegen 15 Uhr sollten auch die letzten Teams wieder da sein.<br>
<br>
<br>
<b>Interesse?</b> <br>
Dann folgen hier die drei einfachen Teilnahmebedingungen:<br>
<br>
- Ein Team muss aus mindestens zwei Personen bestehen, höchsten vier Personen sind pro Team zugelassen.<br>
Gefahren werden kann die Rallye mit jedem amtlich zugelassen Kraftfahrzeug (von jedem Team zu stellen).<br>
Alle TeilnehmerInnen müssen mindestens 18 Jahre alt und im Besitz einer gültigen Fahrerlaubnis sein.<br>
<br>
- Pro Person ist eine Startgebühr von 10 Euro zu entrichten. Die Startgebühr beinhaltet Getränke <br>
und Essen bei der gemeinsamen Feier im Anschluss an die Rallye.<br>
- Es wird diesmal einen Snackpoint auf der Strecke geben, bei dem ein kleiner Imbiss gereicht wird.<br>
- Für die weitere Verpflegung während der Rallye sind die Teams selbst verantwortlich.<br>
<br>
<br>
<b>Wichtiger Hinweis</b><br>
<br>
Wie immer stehen bei der AKA-Rallye der Spaß und die Gemeinschaft im Vordergrund. Wir wollen weder uns <br>
noch andere gefährden. Deshalb ist während der gesamten Rallye unbedingt die StVO zu beachten! <br>
Der Verein übernimmt keine Haftung für eventuelle Personen- und/oder Sachschäden!<br>
<br>
<br>
<b><u>Anmeldeschluss ist Mittwoch, der 24. Juni 2015!</u></b><br>
<br>
</font></b><br><br><br></td></tr>
</table>
');

$liste='';
$group_list='';
$qry=mysql_query("SELECT distinct id from rallye");
while(list($id)=mysql_fetch_row($qry)){
    $name_in_group='';
    $numbers_in_group=0;
    $liste.='<tr><td>';
    $qry2=mysql_query("SELECT name from rallye where id='".$id."'");
    while(list($name)=mysql_fetch_row($qry2)){
        $name_in_group.=$name.',';
        $liste.=$name.',';
        $numbers_in_group++;
    };
    $name_in_group=substr($name_in_group,0,strlen($name_in_group)-1); // schneidet letztes komma ab
    $liste=substr($liste,0,strlen($liste)-1); // schneidet letztes komma ab
    if($numbers_in_group>1 && $numbers_in_group<5){
        $liste.='</td><td align="right"><font color="green"><b>Gruppe '.$id.'</b></font> <i>Anzahl ('.$numbers_in_group.') gut!</i></td></tr>';
    } else { 
        $liste.='</td><td align="right"><font color="red"><b>Gruppe '.$id.'</b></font> <i>Noch etwas wenige  ('.$numbers_in_group.'). Min 2 Teilnehmer</i></td></tr>';
    };
    if($numbers_in_group<4){
        $group_list.='<option value="'.$id.'">'.$name_in_group.'</option>';
    };
    
};
if(empty($liste)){$liste='<tr><td colspan="2"><i>Noch keine Anmeldung</i></td></tr>';};
if(empty($group_list)){$group_list='<option>Noch keine Gruppe da</option>';};

echo '<br><br>';

tab_box("650",100,'center','Anmeldungen',
'<table border="0" width="100%">
<tr><td width="50%"><b>Namen</b></td><td align="right"><b>Teamnummer</b></td></tr>'.$liste.'
</table>');

echo '<br><br>';

for($a=0;$a<count($daten);$a++){
	$options[$a]=$daten[$a][1];
	$values[$a]=$daten[$a][1];
	};

tab_box("650",100,'center','Anmelden',
'<form name="edit" action="index.php" method="POST"><table border="0" width="100%">
<tr><td width="50%"><b>Vorstellen</b></td><td align="right"><b>Was m&ouml;chtest du tun ?</b></td></tr>
<tr><td valign="top">Na, wer bist du denn? <br><select name="rm">'.select($values,$options,$HTTP_POST_VARS['rm']).'</select><br>
Ich bin gar nicht in der Liste, ich heiße: <input type="text" name="rm2" value="-">
</td><td align="right" valign="top">
<input type="radio" name="action" value="new" />Ich m&ouml;chte eine neue Gruppe gr&uuml;nden<br><br>
<input type="radio" name="action" value="add" checked />Ich m&ouml;chte mich einer Gruppe <br>anschließen und zwar der Gruppen von:
<select name="group" style="width:250px">'.$group_list.'</select><br><i>Wenn du deine(n) Freund(e) hier nicht<br> findest, sind die schon zu 4.</i>
<br><br>
<input type="radio" name="action" value="rem" /> Nicht mehr mitspielen!</td></tr>
<tr><td colspan="2" align="center"><br><input type="submit" name="send" value="ab geht die Lutzi">
</form>
</td></tr>
</table>');
echo '<br><br>';
tab_box("650",100,'center','Anmerkung',
'Man kann sich auch nachtr&auml;glich wieder umentscheiden. Dazu einfach erneut die Felder ausf&uuml;llen.
<br>Solltet ihr euch nicht in der Liste gefunden haben, achtet auf exakt identische Schreibweise!<br>
<i>Und sagt mir bei Zeiten mal bescheid, dann seid ihr auch nicht in der Getr&auml;nkedatenbank.<br><br>
Das ganze ist ein 60-Minuten-Konstrukt, es kann h&ouml;llisch viele Fehler/Fehlfunktionen geben.<br>
Falls die Funktion dadurch beeintr&auml;chtigt sein sollte, gebt bitte Bescheid.');

echo'</body></html>';
?>
