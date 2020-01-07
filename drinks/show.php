<?php
###session_start();
$id = intval($_GET['id']);
if(!empty($_GET['del']))
{$del = intval($_GET['del']);}
############ löschen ####################
if($_SESSION['session_user_typ']==$aka_drinks_admin_state || $_SESSION['session_user_typ']==$aka_super_admin_state){ // admin check
	if(!empty($del) && !empty($id)){
		$verbrauch=mysqli_num_rows($mysqli->query("SELECT * FROM `aka_verbrauch` WHERE `id`='".$id."' AND `date`='".$del."';"));
		$money=mysqli_num_rows($mysqli->query("SELECT * FROM `aka_money` WHERE `id`='".$id."' AND `date`='".$del."';"));
		if(($money+$verbrauch)==1){
			if($money==1){ $db_select='aka_money'; }
			else { $db_select='aka_verbrauch'; };
			if($mysqli->query("DELETE FROM `".$db_select."` WHERE `id`='".$id."' AND `date`='".$del."' LIMIT 1;")){
				echo '<font color="green"><b>Posten gel&ouml;scht!</b></font><br>'; 
				}
			else {
				echo 'Posten nicht gel&ouml;scht! SQL Problem.<br>'; 
				};
			}
		else {
			echo 'Zuordnung nicht eindeutig!<br>';
			};
		};
	};
############ löschen ####################
############ Kontobewegung ###############
$eintrag=array();
$k=0;

$abfrage="SELECT `value`,`date` FROM `aka_money` WHERE `ID`=".$id." ";
$erg=$mysqli->query($abfrage);
while(list($db_value,$db_date) = mysqli_fetch_row($erg)) {
	$eintrag[$k][0]=$db_date;
	$eintrag[$k][1]=$db_value; 
	$eintrag[$k][2]='-';
	
	$curve_add[$k][0]=$db_date;
	$curve_add[$k][1]=$db_value;
	$k++;};

$l=0;
$abfrage="SELECT `value`,`date` FROM `aka_verbrauch` WHERE `ID`=".$id." ";
$erg=$mysqli->query($abfrage);
while(list($db_value,$db_date) = mysqli_fetch_row($erg)) {
	$eintrag[$k][0]=$db_date;
	$eintrag[$k][1]='-';
	$eintrag[$k][2]=$db_value; 
	
	$curve_rem[$l][0]=$db_date;
	$curve_rem[$l][1]=$db_value;
	$k++; $l++;};
	
$eintrag=kolja_sort($eintrag,0);
$eintrag=array_reverse($eintrag);
$kontostand=0;

if(!isset($_GET['showall'])) {
	if($k>=10){
		$max=$k-1; $min=$k-10; 
		###### guthaben berechnen #########
		for($i=0;$i<=$k-11;$i++){
			if($eintrag[$i][2]=='-')  {	$kontostand+=$eintrag[$i][1]; }
			elseif ($eintrag[$i][1]=='-') { $kontostand-=$eintrag[$i][2]*0.75; };
			};
		###### guthaben berechnen #########
		} ### hier max 10 auswählen
	else { $max=$k-1; $min=0; };
	$link='Die letzen 10 Kontobewegungen &nbsp; <a href="index.php?mod=show&id='.$id.'&showall&'.SID.'">Alle anzeigen ('.$k.')</a>';
	}
else    {
	$min=0; $max=$k-1;
	$link='Alle Kontobewegungen &nbsp; <a href="index.php?mod=show&id='.$id.'&'.SID.'">Nur letzten 10 anzeigen</a>';
	};


tab_go("100%",200,"center",$link);
echo '<table border="0" width="100%" class="singletable"><tr><th>Aktion</th><th>Betrag</th><th>Datum</th><th>Kontostand</th></tr>';

for($a=$min;$a<=$max;$a++){
	if($a%2==1) { $bg_color="#dddddd"; } else { $bg_color="#ffffff"; };
	if($eintrag[$a][2]=='-')  {	
		$text='Einzahlung';
		$betrag=convert2money($eintrag[$a][1]);
		if($betrag==-5){ $text='Mahngeb&uuml;hren';};
		$datum=date("d.m.Y",$eintrag[$a][0]); 
		$kontostand+=$betrag;
		}
	elseif ($eintrag[$a][1]=='-') {
		$text='Getr&auml;nkeabrechnung <i>( '.$eintrag[$a][2].' Striche * 0,75 &euro; )</i>';
		$betrag=convert2money('-'.$eintrag[$a][2]*0.75);
		$datum=date("d.m.Y",$eintrag[$a][0]);
		$kontostand+=$betrag; };
	$kontostand=convert2money($kontostand);
	echo '<tr><td bgcolor="'.$bg_color.'">'.$a.'. '.$text.'</td><td bgcolor="'.$bg_color.'">'.$betrag.' &euro;</td>
					<td bgcolor="'.$bg_color.'">'.$datum.'</td><td bgcolor="'.$bg_color.'">'.$kontostand.' &euro;';
	if($_SESSION['session_user_typ']==$aka_drinks_admin_state || $_SESSION['session_user_typ']==$aka_super_admin_state){ // admin check
		echo '<div style="float:right;" class="little" ><a href="index.php?mod=show&id='.$_GET['id'].'&del='.$eintrag[$a][0].'" span="little" onclick="return confirmLink(this, \'Wirklich entfernen ?\')">Entfernen</a></div>'; 
	};
	echo '</td></tr>';
	};
echo '</table>';
tab_end();
############ Kontobewegung ###############

tab_go("100%",200,"center","Einzahlungsmoral");
echo '<!-- amcolumn script--><script type="text/javascript" src="amcolumn/swfobject.js"></script>
	<div id="flashcontent1">
		<strong>You need to upgrade your Flash Player</strong></div><script type="text/javascript">
	// <![CDATA[		
		var so = new SWFObject("amcolumn/amcolumn.swf", "amcolumn1", "100%", "400", "8", "#FFFFFF");
		so.addVariable("path", "amcolumn/");
		so.addVariable("chart_settings", "<settings><depth>20</depth><angle>20</angle><column><grow_time>3</grow_time><sequenced_grow>true</sequenced_grow><hover_brightness>30</hover_brightness></column><plot_area><margins><left>50</left><top>50</top><right>10</right><bottom>10</bottom></margins></plot_area><grid><category><alpha>5</alpha></category><value><alpha>0</alpha><fill_color>000000</fill_color><fill_alpha>5</fill_alpha></value></grid><values><category><rotate>45</rotate></category></values><axes><category><width>1</width></category><value><width>1</width></value></axes><graphs><graph gid=\'1\'><type>column</type><color>B92F2F</color><balloon_text> <![CDATA[Einzahlung {series}: {value} eur]]></balloon_text></graph><legend><enabled>false</enabled></legend></settings>"); 
		so.addVariable("chart_data", "<chart><series>';
		for($a=0;$a<count($curve_add);$a++){	echo '<value xid=\''.$a.'\'>'.date('d.m.y',$curve_add[$a][0]).'</value>'; };
		echo'</series><graphs><graph gid=\'1\'>';
		for($a=0;$a<count($curve_add);$a++){	echo '<value xid=\''.$a.'\' color=\'#318DBD\'>'.$curve_add[$a][1].'</value>'; };
		
		echo'</graph></graphs></chart>");	so.write("flashcontent1");	// ]]>		</script>		<!-- end of amcolumn script -->';


#echo'	<center><img src="img/data_img.php?id='.$id.'&db=aka_money&'.SID.'" ></center><br><hr><br>';
tab_end();
tab_go("100%",200,"center","Trinkmoral");
#echo'	<center><img src="img/data_img.php?id='.$id.'&db=aka_verbrauch&'.SID.'" ></center><br><hr><br>';
echo '<!-- amcolumn script--><script type="text/javascript" src="amcolumn/swfobject.js"></script>
	<div id="flashcontent2">
		<strong>You need to upgrade your Flash Player</strong></div><script type="text/javascript">
	// <![CDATA[		
		var so = new SWFObject("amcolumn/amcolumn.swf", "amcolumn1", "100%", "400", "8", "#FFFFFF");
		so.addVariable("path", "amcolumn/");
		so.addVariable("chart_settings", "<settings><depth>20</depth><angle>20</angle><column><grow_time>3</grow_time><sequenced_grow>true</sequenced_grow><hover_brightness>30</hover_brightness></column><plot_area><margins><left>50</left><top>50</top><right>10</right><bottom>10</bottom></margins></plot_area><grid><category><alpha>5</alpha></category><value><alpha>0</alpha><fill_color>000000</fill_color><fill_alpha>5</fill_alpha></value></grid><values><category><rotate>45</rotate></category></values><axes><category><width>1</width></category><value><width>1</width></value></axes><graphs><graph gid=\'1\'><type>column</type><color>B92F2F</color><balloon_text> <![CDATA[Verbrauch {series}: {value} Striche]]></balloon_text></graph></settings>"); 
		so.addVariable("chart_data", "<chart><series>';
		for($a=0;$a<count($curve_rem);$a++){	echo '<value xid=\''.$a.'\'>'.date('d.m.y',$curve_rem[$a][0]).'</value>'; };
		echo'</series><graphs><graph gid=\'1\'>';
		for($a=0;$a<count($curve_rem);$a++){	echo '<value xid=\''.$a.'\' color=\'#318DBD\'>'.$curve_rem[$a][1].'</value>'; };
		
		echo'</graph></graphs></chart>");	so.write("flashcontent2");	// ]]>		</script>		<!-- end of amcolumn script -->';

tab_end();
echo '<div align="center"><a href="index.php?'.SID.'">Zur&uuml;ck</a></div><br>';
?>
