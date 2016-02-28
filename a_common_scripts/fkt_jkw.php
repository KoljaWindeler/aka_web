<?php
function find_and_hack($post_text)
	{
	$nok=1;
	while($nok==1)
			{
			while($post_text[0]==' ')	{$post_text=substr($post_text,1); }; 						## leerzeichen vorne weg
			$wo=strpos($post_text,' ');											## such das nächste freizeichen
			if ($wo === false)												# falls keins da ist gut
				{ 
				$such_str.=','.$post_text;
				$nok=0; 
				}													# beende schleife
			elseif(div(substr_count(substr($post_text,0,$wo),'"'),2)+div(substr_count(substr($post_text,$wo+1),'"'),2)<2)  	# dann gibt es ein leerzeichen aber das ist NICHT in quotationmarks => einfacher schnitt
				{	
				$such_str.=','.substr($post_text,0,$wo);								## einfaches übernehmen
				$post_text=substr($post_text,$wo+1);									# mit dem rest fortfahren
				}
			else														## ein leerzeichen gefunden mitten in quotation marks
				{
				$such_str.=','.substr($post_text,1,$wo);								## anfügen bis zum freizeichen
				$post_text=substr($post_text,$wo+1);
				$such_str.=substr($post_text,0,strpos($post_text,'"'));    						# anfuegen bis n&auml;chstes quotation
				$post_text=substr($post_text,strpos($post_text,'"')+1);							# mit dem rest fortfahren
				};
			};
	return str_replace('"','',substr($such_str,1));
	};



function impressum()
	{
	echo'<center><span class="little">Dieses Skript wurde zuletzt modifiziert am '.date("d.m.y", getlastmod()).' um 
		'.date("G:i", getlastmod()).'</center>';
	};

function div($zahl1,$zahl2)
	{	
	while($zahl1 >= $zahl2)
		{$zahl1 = $zahl1 - $zahl2;};
	return $zahl1;	
	};  
	
function addzero($zahl) {   return addzero2($zahl,3);	};

function addzero2($string,$count){
    while(strlen($string)<$count){
        $string='0'.$string;
    };
    return $string;
};




function session2post($SESSION,$POST)
	{
	if(!isset($POST['submit']) AND $SESSION['post']['resume']==1)
		{
	//	echo '<br>folgende werte wurden in der session gefunden:<br><br>';
		foreach ($SESSION['post'] as $key => $value) 
			{
	//		echo 'name: '.$key.' und wert '.$value.'<br>';
			$POST[$key]=$value;
			};
	//	echo '<br>ende<br>';
		};
	return $POST;
	};

function post2session($SESSION,$POST)
	{
	$SESSION['post']='';
	//echo '<br>folgende werte wurden in post gefunden:<br><br>';
	foreach ($POST as $key => $value) 
		{
	//	echo 'name: '.$key.' und wert '.$value.'<br>';
		$SESSION['post'][$key]=$value;
		};
	$SESSION['post']['resume']=1;
	//echo '<br>ende<br>';
	return $SESSION;
	};

function resume($type,$name,$value,$resume,$default,$_my_POST)
	{
	$add='';
	if($resume==1) 
		{
		if($type=='checkbox')
			if(empty($_my_POST) && $default=='1') { $add=' checked';}
			elseif($_my_POST[$name]==$value) { $add=' checked';};
		if($type=='radio')
			if(empty($_my_POST) && $default=='1') { $add=' checked';}
			elseif($_my_POST[$name]==$value) { $add=' checked';};
		if($type=='text')
			if(!empty($_my_POST)) { $value=$_my_POST[$name];};
		}
	echo '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" '.$add.' alt="0">';
	};

function checkmail($email,$modus) // use "email => emailadresse" modus: 0=true/false 1=fehlermeldung
	{
	$ok[0]=true;
	$email_array=explode('@',$email);
	$userName=$email_array[0];
	$mailDomain=$email_array[1];
	if(empty($userName))
		{
		$ok[0]=false; 
		$ok[1]='Es scheint als habe die Emailadresse keinen Nutzer Teil';
		}
	elseif(!@checkdnsrr($mailDomain, "MX")) // liefert 1 falls doamin da => liefert 0 falls domain da // geht rein falls domain NICHT da
		{
		if(!@checkdnsrr('t-online.de', "MX")) // FALLS der check generell nicht geht 
			{
			$mailDomain_array=explode('.',$mailDomain);
			if(empty($mailDomain_array[0]) || empty($mailDomain_array[1])) // falls domain string oder l&#228;nderkennung leer 
				{
				$ok[0]=false;
				$ok[1]='Es scheint als ob der Domainteil der Email Adresse ung&#252;ltig ist. 
					<br><i>Hinweis: Der Server hat ebenfalls kein Internetkontakt</i>';
				};
			}
		else
			{
			$ok[0]=false;
			$ok[1]='Der Domainteil der Email Adresse ist ung&#252;ltig.';
			};
		};
	if($modus=='1')
		return $ok[1];
	else
		return $ok[0];
	};

function select($values,$options,$selected)
	// Useage: <option value="$value" ?selected? >$options</option>
	// Option { String mit "," getrennt  /  Array }
	// Value { String mit "," getrennt  /  Array  /  Startint  / Leer (Start=0) }
	{
	if(!is_array($options)) { $options=explode(',',$options); };

	if(is_int($values)) 		{ $b=$values;	unset($values);	for($a=0;$a<count($options);$a++)	$values[$a]=$a+$b;		}
	elseif(empty($values))		{				for($a=0;$a<count($options);$a++)	$values[$a]=$a;			}
	elseif(!is_array($values)) 	{ 									$values=explode(',',$values); 	};

	unset($result,$add);
	for($a=0;$a<count($values) ;$a++)
		{
		if($values[$a]==$selected)	
			{ $add = ' selected '; }
		else
			{ $add = ''; };
		$result .= '<option value="'.$values[$a].'"'.$add.'>'.$options[$a].'</option>';
		};
	return $result;
	};


function pages($start_entry,$rows_pp,$db_rows,$file) // 1. eintrag diese seite, Anzahl an Eintraegen pro Seite, anzahl an eintraegen als ganzes
	// bei der pages: wird sqlstart zurueck gegeben als start_entry
	{
	if($db_rows>0) 
		{
	
		if(floor($db_rows/$rows_pp) < ($db_rows/$rows_pp)) 	// gesamt seiten berechen falls pages%1 > 0 => pages +1 
			{$pages=floor($db_rows/$rows_pp)+1; } 
		else 	
			{ $pages=$db_rows/$rows_pp; };
	
		unset($return);
		$return='<br><br><table width="98%" border="0"  align="left"><tr><td align="right"><b>Seite';
		if($pages>1) { $return.='n';};
		$return.=': </b>';
		
		if($pages>1)
			{
			$page_ak=$start_entry/$rows_pp+1;		// die aktuelle Seite berechnen, + 1 da z.B. 0/30=0 => 1 , kein round da start immer n*row ist
			if($pages<=11)					// einfache variante: einfach alle eintraege anzeigen 
				{
				for($a=1; $a<=$pages; $a++)
					{
					$start_entry=($a-1)*$rows_pp;
					if($a==$page_ak) 
						{ $return.= '<b> ['.$a.'] </b>'; }
					else 
						{ $return.='<a href="'.$file.'?'.SID.'&sqlanfang='.$start_entry.'">'.$a.'</a> ';	};
					};
				}
			else
				{ 					// aufwand
				for($a=1;  $a<=3; $a++)			// ersten 3 anzeigen
					{
					$start_entry=($a-1)*$rows_pp;
					if($a==$page_ak) 		{ $return.= '<b> ['.$a.'] </b>'; } 
					else 				{ $return.= '<a href="'.$file.'?'.SID.'&sqlanfang='.$start_entry.'">'.$a.'</a> ';};
					};
				if($page_ak==1 | $page_ak==$pages) { $return.= '...'; }; // sind wir bei 1 oder letze seite dann ...
				for($a=$page_ak-2;$a<=$page_ak+2;$a++)			// alle seiten aus d. zwischen der aktuelle seite - 2 und aktuelle seite + 2 liegt
					{
					if($a>3 && $a<=($pages-3))			// bsp: akt = 2; gesamt seite = 3 => keine ausgabe da oben schon ausgegeben, 
						{
						if($page_ak>=7 && $a==$page_ak-2 ) { $return.= ' ... ';}; // zwischen 7 und max-2 ... VOR dem mittelteil
						
						$start_entry=($a-1)*$rows_pp;
						if($a==$page_ak) 
							{ $return.='<b> ['.$a.'] </b>'; } 
						else 
							{ $return.= '<a href="'.$file.'?'.SID.'&sqlanfang='.$start_entry.'">'.$a.'</a> ';	};
			
						if($page_ak+5<$pages && $a==$page_ak+2  ) { $return.=' ... ';};	 // noch vor dem ende aber sind hinter dem letzen ...
						};
				};
				for($a=$pages-2;  $a<=$pages; $a++)	// letzten 3 anzeigen
					{
					$start_entry=($a-1)*$rows_pp;
					if($a==$page_ak) 	{ $return.='<b> ['.$a.'] </b>';} 
					else 			{ $return.= '<a href="'.$file.'?'.SID.'&sqlanfang='.$start_entry.'">'.$a.'</a> ';};
					};
				};
			$return.='</td></tr></table>';
			}
		else	
			{
			$return.='<b>[1]</b></td></tr></table>';
			};
		return $return;
		};
	};
	

#### kolja bubblesort
function kolja_sort($array,$nr){
	$k=0; 
	while(isset($array[$k+1][$nr])){
		if($array[$k][$nr]<$array[$k+1][$nr]){
			$temp=$array[$k];
			$array[$k]=$array[$k+1];
			$array[$k+1]=$temp;
			$k=0;
			}
		else{
			$k++; 
			};
		};
	return $array;
	};

#### 		
function reverse($array,$elemente){
	for($a=0;$a<$elemente;$a++){
		$temp[$elemente-$a-1]=$array[$a];
		};
	return $temp;
	};
	
#### 
function convert2money($geld){
	if(empty($geld)) { $geld=0; };
	$geld_arr=explode('.',$geld);
	if(count($geld_arr)==1) { $geld.='.00'; }
	elseif(strlen($geld_arr[1])==1) { $geld.='0'; }
	return $geld;
	};

#### get name 
function get_id_for_name($name_replaced,$daten){
	##########spec
	# 01 = $most_likely_id
	# 02 = $most_likely_name;
	# 03 = $most_likely_hits; percentage of the hit
	##########spec
	$debug=false;
	// cut
	$separator=explode('*'," *-*_*,*;*%20"); // add more separators if needed
	// have a shared separater to use the explode command next
	for($a=0; $a<count($separator); $a++){
		$name_replaced=str_replace($separator[$a],';',$name_replaced);
	}
	$name_search_short=strlen(str_replace(";","",$name_replaced));
	// avoid empty cells -> due to dobble explode char
	while(!(strpos($name_replaced,";;")===false)){
		$name_replaced=str_replace(";;",";",$name_replaced);
	}
	// split it
	$name_explode=explode(';',strtolower($name_replaced));

	// find the most appropiate one
	$most_likely_name="";
	$most_likely_id=-1;
	$most_likely_hits=0;
	for($a=0; $a<count($daten);$a++){ // loop over all db entries, first loop dimension
		if($debug) echo "<hr>=> DB name: ".$daten[$a][1]."<br>";
		$db_name=str_replace('-',' ',$daten[$a][1]); // to explode the name from the database 
		$db_name=explode(' ',strtolower($db_name)); // to be able to loop over parts of the name
		$hits=0;
		for($i=0;$i<count($name_explode);$i++){ // loop over all parts of the given arg name, second loop dimension
			if($debug) echo "-> such name: ".$name_explode[$i]."<br>";
			if($debug) echo "Suche vollen Namen<br>";

			$direct_found=false;
			for($ii=0; $ii<count($db_name); $ii++){ // third dimension: loop over the parts from the current database name
				if(!(strpos($db_name[$ii], $name_explode[$i])===false)){ // vollen string gefunden
					$hits+=round((100*strlen($name_explode[$i]))/$name_search_short); // add procentage hit
					$direct_found=true;
					if($debug) echo "---->Full hit: ".$db_name[$ii]." in ".$name_explode[$i]." -> ".$hits."<br>";
				}
			}
			if($debug && !$direct_found) echo "Suche teile<br>";
			for($ii=0; $ii<count($db_name) && !$direct_found; $ii++){ // haven't found the full part of the given name in all parts of database name, trying "just a few letters" again third dimension
				$length=strlen($name_explode[$i]); // avoid overrunning the shorter name
				if(strlen($db_name[$ii])<$length){
					$length=strlen($db_name[$ii]);
				};
				if($debug) echo "compare:".$db_name[$ii]." ggn ".$name_explode[$i]."<br>";
				for($iii=0;$iii<$length;$iii++){ // loop over length of name, forth loop dimension
					if($db_name[$ii][$iii]==$name_explode[$i][$iii]){
						$hits+=round(100/$name_search_short); // one letter was correct
						if($debug) echo "i'd like to add (100/".strlen($name_explode[$i]).") somit haben wir ".$hits." da ".substr($db_name[$ii],0,$iii)."[".substr($db_name[$ii],$iii,1)."]".substr($db_name[$ii],$iii+1)." == ".substr($name_explode[$i],0,$iii)."[".substr($name_explode[$i],$iii,1)."]".substr($name_explode[$i],$iii+1)."<br>";
					} else {
						break; // stop search as soon as we have a failed part
					};
				}
			}
		}
		if($debug) echo "Total hits rate here ".$hits."%<br>";
		if($most_likely_hits<$hits){
			$most_likely_hits=$hits;
			$most_likely_id=$daten[$a][0];
			$most_likely_name=$daten[$a][1];
		} else if($most_likely_hits==$hits){ // we have a tie -> so no real clue what we do -> delete the suggestion
			$most_likely_id=-1;
			$most_likely_hits=0;
			$most_likely_name="";
		}
	}
	$result[1]=$most_likely_id; // -1 if failed otherwise the id
	$result[2]=$most_likely_name;
	$result[3]=$most_likely_hits; // percentage
	return $result;
}




function confirm(){
echo'<script type="text/javascript">
var lock_val= \'0\';
var load_reload= \'1\';
function confirmLink(theLink, theSqlQuery)
	{
    if (confirmMsg == \'\' || typeof(window.opera) != \'undefined\') {        return true;    }
	var confirmMsg  = \'\';
    var is_confirmed = confirm(theSqlQuery);
	    return is_confirmed;
	} 
function confirmLock(theLink, theSqlQuery)
	{
	if( lock_val == \'0\' )
		return is_confirmed;	
	else	{
		if (confirmMsg == \'\' || typeof(window.opera) != \'undefined\') {        return true;    }
		var confirmMsg  = \'\';
		var is_confirmed = confirm(\'Continue without saving ?\');
			return is_confirmed;
		}
	} 
function lock(value)	{	lock_val= value;	};
</script>'; };

	


// saumäßig wichtige funktion die ich für rfm total häufig benutze!
// remove_a_para(hallo.php?test=ja&test2=nein,test) => hallo.php?test2=nein
function remove_a_para($the_link,$search){
	$the_link=explode('&',$the_link); 
	for($a=0;$a<count($the_link);$a++){
		$one_para=explode('=',$the_link[$a]); 
		$option[$a]=$one_para[0];
		$value[$a]=$one_para[1];
		};
	$link_array=array();
	for($a=0;$a<count($the_link);$a++){
		
		if($option[$a]<>$search){
			$one_para[0]=$option[$a];
			$one_para[1]=$value[$a];
			array_push($link_array,implode('=',$one_para));
			};
		};
	$link_str=implode('&',$link_array);
	return $link_str;
	};

/* This function gives back a javascript select field
 * for changing Content without reloading
 * 
 *
 * Parameters: 
 * $conn = Oracle Connection Handle
 * $box_arr = Array Of boxes
 * $value_l = Array of left handed Values
 * $value_r = Array of right handed Values
 * $pb_2 = $_GET['pb_2'] the prebox2 value
 * $pp_1 = $_GET['pp_1'] the preparameter1 value
 * $query_string = $_SERVER['query_string']
 * $meas = Values from hashlookup
 * 
 * Return value:
 * $return = String with all var's and functions
 * 
 * Author: Kolja Windeler
 */        
function java_select($conn,$box_arr,$value_l,$value_r,$pb_2,$pp_1,$query_string,$meas){
    $result='';
    // mehrere möglichekeiten:
    //1. eine box und ein parameter => beide auswahlboxen 1 funktion
    //2. eine box, statistik => beide auswahlboxen 1 funktion
    //3. eine box und mehr parameter => auswahlbox für die boxen
    //4. mehrere boxen und nur ein parameter => parameter auswählbar
  
    // echo java script start
    $result.='<script type="text/javascript">';
    
    if(1==1){ // nur um das zusammenzuklappen
    //boxen array
    $result.='
var box2=new Array;';
    
    // add all boxes to Java Array
    $result.="
box2['pro'] = new Array;";
    
    $box_2_options_pro=array();
    $box_2_values_pro=array();
    $box_2_tt_pro=array();
    $available_project_ID=array();
    // List all id's and titles in RFM_PROJECT
    $qry=OCIParse($conn,"SELECT distinct id,title,description FROM RFM_PROJECT WHERE valid_to IS NULL ORDER BY id desc"); 
    OCIExecute($qry);        $i=1;
    while( list($db_id,$db_title,$db_tooltip)  = oci_fetch_array($qry,OCI_NUM)){
            $result.="
                            box2['pro'][".$i."] = new Array;
                            box2['pro'][".$i."][0]=".$db_id.";
                            box2['pro'][".$i."][1]='".$db_title."';
                            box2['pro'][".$i."][2]='".$db_tooltip."';\r\n";
            $i++;
            array_push($box_2_options_pro,$db_title);
            array_push($box_2_values_pro,$db_id);
            array_push($box_2_tt_pro,$db_tooltip);
            array_push($available_project_ID,$db_id);
    };
    // copy project array to used array's by default
    $box_2_options=$box_2_options_pro;
    $box_2_values=$box_2_values_pro;
    $box_2_tt=$box_2_tt_pro;
    
    // generate 3rd Box
    $result.="
var box3=new Array;
box3['pro']=new Array;";
    for($a=0;$a<count($available_project_ID);$a++){ // durch alle project ID's gehen
            $result.= "
box3['pro'][".$available_project_ID[$a]."] = new Array;";
            $data=rfmGetProjectConfigureConstellation($available_project_ID[$a]);
            ### 
            $i=0;
            foreach($data as $grpNo => $grp){ // in grp[bx] sind alle boxen drin
                    $box_in_project=$grp["bx"];
                    for($b=0;$b<count($box_in_project);$b++){
                            $result.="
                                            box3['pro'][".$available_project_ID[$a]."][".$i."] = new Array;
                                            box3['pro'][".$available_project_ID[$a]."][".$i."][0] = '".$box_in_project[$b]["id"]."';
                                            box3['pro'][".$available_project_ID[$a]."][".$i."][1] = '".$box_in_project[$b]["title"]."';
                                            box3['pro'][".$available_project_ID[$a]."][".$i."][2] = '".$box_in_project[$b]["descr"]."';";
                            $box_3_values_pro[$available_project_ID[$a]][$i]=$box_in_project[$b]["id"];
                            $box_3_options_pro[$available_project_ID[$a]][$i]=$box_in_project[$b]["title"];
                            $box_3_tt_pro[$available_project_ID[$a]][$i]=$box_in_project[$b]["descr"];
                            $i++;
                            };
                    };
    };
        
    // copy project array to used array's by default, use id off $_GET
    $box_3_options=$box_3_options_pro[$pb_2];
    $box_3_values=$box_3_values_pro[$pb_2];
    $box_3_tt=$box_3_tt_pro[$pb_2];
    
        
    // para array
    $result.='
var para1=new Array;
para1[0]=new Array;
para1[0][0]=\'all\';
para1[0][1]=\'all\';
para1[0][2]=\'all\';';

    ### 1. Box: auswahl:
    $para_1_options[0]='all';
    $para_1_values[0]='all';
    $para_1_tt[0]='all';
    $i=1;
    foreach ($meas as $key => $v) {
            if(!in_array($meas[$key][1],$para_1_options) && !empty($meas[$key][1])){
                    $result.="
para1[".$i."]=new Array;
para1[".$i."][0]='".$meas[$key][1]."';
para1[".$i."][1]='".$meas[$key][1]."';
para1[".$i."][2]='".$meas[$key][1]."';";
                    array_push($para_1_options,$meas[$key][1]);
                    array_push($para_1_values,$meas[$key][1]);
                    array_push($para_1_tt,$meas[$key][1]);
                };
            };
    ### 2. Box auswahl:
    #$para_2_values[0]='9999999';
    #$para_2_options[0]='-';
    $result.="
var para2=new Array;";
    // List for "all" selection
    $a=0;
    $result.="
para2['all']=new Array;";
    foreach ($meas as $key => $v) {
            $para_2_values_all[$a]=$key;
            $para_2_options_all[$a]=$meas[$key][4]; // das hier wird angezeigt
            $para_2_tt_all[$a]=$meas[$key][2];
            $result.="
para2['all'][".$a."]=new Array;
para2['all'][".$a."][0]='".$key."';
para2['all'][".$a."][1]='".$meas[$key][3]." (".$meas[$key][1].")';
para2['all'][".$a."][2]='".$meas[$key][2]."';";
            $a++;
    };
    // List if a special unit is selected
    ### preprare vars
    $a=0; $b=1; $remember_unit=''; $remember_count=array();
    foreach ($meas as $key => $v) {
            $meas[$key][1]=str_replace('% ','pH',$meas[$key][1]);
            $remember_count[$meas[$key][1]]=0;
    };
    ### go
    foreach ($meas as $key => $v) {
            // replace für % => pH
            $meas[$key][1]=str_replace('% ','pH',$meas[$key][1]);
            // um sicher zu sein das man das array anmeldet
            if($meas[$key][1]<>$remember){
                    if($remember_count[$meas[$key][1]]==0){
                        if($meas[$key][1]<>'' && !empty($meas[$key][1])){
                            $result.="
para2['".$meas[$key][1]."']=new Array;";
                        };
                    };
                    $remember=$meas[$key][1];
                    $b=$remember_count[$meas[$key][1]];
            };
            // wenns das ausgewählte ist auch gleich für anzeige sammeln
            if($meas[$key][1]==$pp_1 ){
                    #echo "jaha bei a=".$a;
                    $para_2_values_sp[$a]=$key;
                    $para_2_options_sp[$a]=$meas[$key][3];
                    $para_2_tt_sp[$a]=$meas[$key][2];
                    $a++;
            };
            // solange das ein index hat auch zu java schicken
            if($meas[$key][1]<>'' && !empty($meas[$key][1])){
                    $result.="
para2['".$meas[$key][1]."'][".$b."]=new Array;
para2['".$meas[$key][1]."'][".$b."][0]='".$key."';
para2['".$meas[$key][1]."'][".$b."][1]='".$meas[$key][3]."';
para2['".$meas[$key][1]."'][".$b."][2]='".$meas[$key][2]."';";
$b++;
$remember_count[$meas[$key][1]]++;
            };
    };
    
    if($pp_1=='all'){
            $para_2_options=$para_2_options_all;
            $para_2_values=$para_2_values_all;
            $para_2_tt=$para_2_tt_all;
    } else {
            #echo "ich fetch das auch";
            $para_2_options=$para_2_options_sp;
            $para_2_values=$para_2_values_sp;
            $para_2_tt=$para_2_tt_sp;
    }
   
    // add choos box switch
    $link_para_only='index.php?'.remove_a_para(remove_a_para(remove_a_para(remove_a_para($query_string,'value_r'),'value_l'),'pp_1'),'ident').'&data_generator=1&value_l=';
    $link_para_only=str_replace('&amp;','&',$link_para_only);
    }; // das hier ist nur die klammer um den ganzen quelltext für die vars auszublenden
        
	
    // java funktionen
    // generate_para2()
    // generate_box3()
    if(2==2){
    $result.=  '
                        function generate_para2(){
                            if(document.formular.para_1.value==\'all\'){
                                    <!-- clean para 2 -->
                                    for (var i=document.formular.para_2.length; i>=0; i--) {
                                            document.formular.para_2[i] = null;
                                    };
                                    <!-- fill para 2 -->
                                    for (var i=0; i < para2[\'all\'].length; i++) {
                                            var addme = new Option(para2[\'all\'][i][1], para2[\'all\'][i][0]);
                                            document.formular.para_2[i] = addme;
                                            document.formular.para_2[i].setAttribute(\'onMouseOver\', "showHelpMessage(\'Description\',\'"+para2[\'all\'][i][2]+"\',this)");
                                            document.formular.para_2[i].setAttribute(\'onMouseOut\', "hideHelpMessage(this)");
                                    };
                            }
                            else {
                                    var para_1=document.formular.para_1.value;
                                    para_1=para_1.replace(\'% \',\'pH\');
                                    
                                    <!-- clean para 2 -->
                                    for (var i=document.formular.para_2.length; i>=0; i--) {
                                            document.formular.para_2[i] = null;
                                    };
                                    <!-- fill para 2 -->
                                    for (var i=0; i < para2[para_1].length; i++) {
                                            var addme = new Option(para2[para_1][i][1], para2[para_1][i][0]);
                                            document.formular.para_2[i] = addme;
                                            document.formular.para_2[i].setAttribute(\'onMouseOver\', "showHelpMessage(\'Description\',\'"+para2[para_1][i][2]+"\',this)");
                                            document.formular.para_2[i].setAttribute(\'onMouseOut\', "hideHelpMessage(this)");
                                    };
                                }
                            }
     
                        function generate_box3(value) {
                            var proj_id=document.formular.box_2.value;
                            
                            <!-- clean box 3 -->
                            for (var i=document.formular.box_3.length; i>=0; i--) {
                                            document.formular.box_3[i] = null;
                            }
                            
                            <!-- fill box 3 -->
                            if(box3[\'pro\'][proj_id].length==0){
                                    alert(\'No boxes in this project\');
                            }
                            for (var i=0; i < box3[\'pro\'][proj_id].length; i++) {
                                    var addme = new Option(box3[\'pro\'][proj_id][i][1], box3[\'pro\'][proj_id][i][0]);
                                    document.formular.box_3[i] = addme;
                                    document.formular.box_3[i].setAttribute(\'onMouseOver\',"showHelpMessage(\'Description\',\'"+box3[\'pro\'][proj_id][i][2]+"\',this)");
                                    document.formular.box_3[i].setAttribute(\'onMouseOut\', "hideHelpMessage(this)");
                            }
                            
                            <!-- aktivate box_3 -->
                            document.formular.box_3.disabled=false;
			};';
    };                    

    
    // hier unterscheiden zwischen 1-4
    
    
    if(count($value_l)==1 && count($value_r)==0 && count($box_arr)==1){
        // Fall 1 eine box und ein parameter
        $link='index.php?'.remove_a_para(remove_a_para(remove_a_para(remove_a_para(remove_a_para(remove_a_para($query_string,'pb_2'),'box_id'),'value_l'),'value_r'),'ident'),'pp_1').'&value_l=';
        $link=str_replace('&amp;','&',$link);
        $result.='      function reload_now() {
                            showLoadingLogo();
                            var box_2=document.formular.box_2.value;
                            var box_3=document.formular.box_3.value;
                            var para_1=document.formular.para_1.value;
                            para_1=para_1.replace(\'% \',\'pH\');
                            var para_2=document.formular.para_2.value;
                            self.location.href="'.$link.'"+para_2+"&pp_1="+para_1+"&box_id="+box_3+"&pb_2="+box_2;
                    };
                    </script>
                    <form action="post" name="formular"><img src="images/parameter0.png" border="0" alt="parameter"/>  
                    Choose data source:
                    <select name="para_1" id="chartStyle1" class="chartDropDown" style="width:100px" onchange="generate_para2()">'.select($para_1_values,$para_1_options,str_replace('pH','% ',$pp_1),$para_1_tt).'</select>&nbsp;
                    <select name="para_2" id="chartStyle2" class="chartDropDown" style="width:100px" onchange="">'.select($para_2_values,$para_2_options,$value_l[0],$para_2_tt).'</select>&nbsp;
                    <br><img src="images/receiver0.png" border="0" alt="receiver" /> Choose box: &nbsp;
                    <select name="box_2" id="chartStyle1" class="chartDropDown" style="width:100px" onchange="generate_box3(this.options[this.selectedIndex].value);">'.select($box_2_values,$box_2_options,$pb_2,$box_2_tt).'</select>&nbsp;
                    <select name="box_3" id="chartStyle2" class="chartDropDown" style="width:100px" onchange="">'.select($box_3_values,$box_3_options,$box_arr[0],$box_3_tt).'</select>
                    '.chart_button('reload','javascript:reload_now();',0,1).'</form>';
                    
    } elseif(count($box_arr)==1 && strpos($value_l[1],'_max')>0){
        // fall 2 eine box, statistik daten=> parameter+box wählen 
        $link='index.php?'.remove_a_para(remove_a_para(remove_a_para(remove_a_para(remove_a_para(remove_a_para($query_string,'pb_2'),'box_id'),'value_l'),'value_r'),'ident'),'pp_1').'&value_l=';
        $link=str_replace('&amp;','&',$link);
        $result.='      function reload_now() {
                            showLoadingLogo();
                            var box_2=document.formular.box_2.value;
                            var box_3=document.formular.box_3.value;
                            var para_1=document.formular.para_1.value;
                            para_1=para_1.replace(\'% \',\'pH\');
                            var para_2=document.formular.para_2.value+";"+document.formular.para_2.value+"_max;"+document.formular.para_2.value+"_min;"+document.formular.para_2.value+"_sp;"+document.formular.para_2.value+"_sm";
                            self.location.href="'.$link.'"+para_2+"&pp_1="+para_1+"&box_id="+box_3+"&pb_2="+box_2;
                    };
                    </script>
                    <form action="post" name="formular"><img src="images/parameter0.png" border="0" alt="parameter"/>  
                    Choose data source:
                    <select name="para_1" id="chartStyle1" class="chartDropDown" style="width:100px" onchange="generate_para2()">'.select($para_1_values,$para_1_options,str_replace('pH','% ',$pp_1),$para_1_tt).'</select>&nbsp;
                    <select name="para_2" id="chartStyle2" class="chartDropDown" style="width:100px" onchange="">'.select($para_2_values,$para_2_options,$value_l[0],$para_2_tt).'</select>&nbsp;
                    <br><img src="images/receiver0.png" border="0" alt="receiver" /> Choose box: &nbsp;
                    <select name="box_2" id="chartStyle1" class="chartDropDown" style="width:100px" onchange="generate_box3(this.options[this.selectedIndex].value);">'.select($box_2_values,$box_2_options,$pb_2,$box_2_tt).'</select>&nbsp;
                    <select name="box_3" id="chartStyle2" class="chartDropDown" style="width:100px" onchange="">'.select($box_3_values,$box_3_options,$box_arr[0],$box_3_tt).'</select>
                    '.chart_button('reload','javascript:reload_now();',0,1).'</form>';
                    
    } elseif(count($box_arr)==1 && (count($value_l)+count($value_r))>1){
        // Fall 3 eine box und mehr als ein parameter => auswahl der box
        $link='index.php?'.remove_a_para(remove_a_para(remove_a_para($query_string,'pb_2'),'box_id'),'ident').'&box_id=';
        $link=str_replace('&amp;','&',$link);
        $result.='      function reload_now() {
                            showLoadingLogo();
                            var box_2=document.formular.box_2.value;
                            var box_3=document.formular.box_3.value;
                            self.location.href="'.$link.'"+box_3+"&pb_2="+box_2;
			};
                	</script>
                        <form action="post" name="formular"><img src="images/receiver0.png" border="0" alt="receiver" /> Choose box: &nbsp;
                        <select name="box_2" id="chartStyle1" class="chartDropDown" style="width:100px" onchange="generate_box3(this.options[this.selectedIndex].value);">'.select($box_2_values,$box_2_options,$pb_2,$box_2_tt).'</select>&nbsp;
                        <select name="box_3" id="chartStyle2" class="chartDropDown" style="width:100px" onchange="">'.select($box_3_values,$box_3_options,$box_arr[0],$box_3_tt).'</select>
			'.chart_button('reload','javascript:reload_now();',0,1).'</form>';
    
    } elseif(count($box_arr)>1 && count($value_l)==1 && count($value_r)==0 ){
        // Fall 4 mehr als eine box dafür nur ein parameter => auswahl des parameters
        $link='index.php?'.remove_a_para(remove_a_para(remove_a_para(remove_a_para($query_string,'value_l'),'value_r'),'ident'),'pp_1').'&value_l=';
        $link=str_replace('&amp;','&',$link);
        $result.='      function reload_now() {
                            showLoadingLogo();
                            var para_1=document.formular.para_1.value;
                            para_1=para_1.replace(\'% \',\'pH\');
                            var para_2=document.formular.para_2.value;
                            self.location.href="'.$link.'"+para_2+"&pp_1="+para_1;
                    };
                    </script>
                    <form action="post" name="formular"><img src="images/parameter0.png" border="0" alt="parameter"/>  
                    Choose data source:
                    <select name="para_1" id="chartStyle1" class="chartDropDown" style="width:100px" onchange="generate_para2()">'.select($para_1_values,$para_1_options,str_replace('pH','% ',$pp_1),$para_1_tt).'</select>&nbsp;
                    <select name="para_2" id="chartStyle2" class="chartDropDown" style="width:100px" onchange="">'.select($para_2_values,$para_2_options,$value_l[0],$para_2_tt).'</select>&nbsp;
                    '.chart_button('reload','javascript:reload_now();',0,1).'</form>';       
    };
                    
    return $result;
};

function getAcqTimestamp($acqtime){
		$db_time=explode(' ',$acqtime);
		$db_time[0]=explode('-',$db_time[0]);
		$db_time[1]=explode(':',$db_time[1]);
                if(!empty($db_time[1][0]) && !empty($db_time[1][1]) && !empty($db_time[1][2]) && !empty($db_time[0][1]) && !empty($db_time[0][0]) && !empty($db_time[0][2])){
                    // checken wie das format ist entweder isses dd-mm-yyyy oder yyyy-mm-dd
                    if($db_time[0][0]>31){
                        $temp=$db_time[0][2];
                        $db_time[0][2]=$db_time[0][0];
                        $db_time[0][0]=$temp;
                    };
                    
                    return mktime($db_time[1][0],$db_time[1][1],$db_time[1][2],$db_time[0][1],$db_time[0][0],$db_time[0][2]);
                } else {
                    if($_GET['debug']>=1){
                    echo "<hr>getAcqTimestamp error. Incoming db_string:-->".$acqtime."<--<hr>";
                    return "ERROR:".$acqtime;
                    } else {
                        return 0;
                    }
                }
		
	};
        
/* This function gives back a javascript mouseover image
 * for changing Content without reloading
 * 
 *
 * Parameters: 
 * $name = unique(!) identifier for the image
 * $image = path to "not selected" image
 * $image_select = path to "selected" image
 * $width = width of image
 * $height = height of image
 * $alt_text = Text, shown if image does not exist
 * $value = something which will be compared with selected_value
 * $selected_value = to compare with
 * $event = e.G. Java event, used by "onclick"
 *
 * 
 * Return value:
 * $return = String with the image
 * 
 * Author: Kolja Windeler
 */        
function mouseover_image($name,$image,$image_select,$width,$height,$alt_text,$value,$selected_value,$event){

	if($value==$selected_value){
		$image_norm=$image_select;
                $add_st='<b>';
                $add_en='</b>';
	} else {
		$image_norm=$image;
                unset($add_st,$add_en);
	};
        $name='js_'.$name; // da java keine objekte die mit zahlen beginnen adressieren kann
	$image_mouseover=$image_select;
        return '<a href="'.$event.'" onmouseover="'.$name.'.src=\''.$image_mouseover.'\';" onmouseout="'.$name.'.src=\''.$image_norm.'\';">'.$add_st.'<img name="'.$name.'" src="'.$image_norm.'" border="0" width="'.$width.'" height="'.$height.'" alt="'.$alt_text.'" />'.$add_en.'</a>';
};

/* This function gives back a javascript calendar
 * 
 *
 * Parameters:
 * $select_time_activ = '' or 'disabled', fields activ or not
 * $from = preselection
 * $to = preselection
 * $from_mini = time of first date
 * $to_max = time of last date
 * $name = unique(!) identifier for the calendar
 * $button = type of button like 'type="hidden"'
 *
 * 
 * Return value:
 * $return = String with the calendar
 * 
 * Author: Kolja Windeler 
 */        
function java_cal($select_time_activ,$from,$to,$from_mini,$to_max,$name,$button){
    if($select_time_activ<>'' && $select_time_activ<>'disabled'){
        echo "illegal activ state";
        return 1;
    };
    
    // create time 
    for($i=0;$i<60;$i++) { $option_min[$i]=addzero2($i,2);    $value_min[$i]=$i; };
    for($i=0;$i<24;$i++) { $option_h[$i]=addzero2($i,2);      $value_h[$i]=$i; };
    
    $from_ts=$from;
    $from_day=date('d',$from);
    $from_month=date('m',$from);
    $from_year=date('Y',$from);
    $from_hour=date('H',$from);
    $from_min=date('i',$from);
    $from_sec=date('s',$from);
    
    $from_mini_day=date('d',$from_mini);
    $from_mini_month=date('m',$from_mini);
    $from_mini_year=date('Y',$from_mini);
    $from_mini_hour=date('H',$from_mini);
    $from_mini_mini=date('i',$from_mini);
    $from_mini_sec=date('s',$from_mini);
    
    $to_ts=$to;
    $to_day=date('d',$to);
    $to_month=date('m',$to);
    $to_year=date('Y',$to);
    $to_hour=date('H',$to);
    $to_min=date('i',$to);
    $to_sec=date('s',$to);
    
    $to_max_day=date('d',$to_max);
    $to_max_month=date('m',$to_max);
    $to_max_year=date('Y',$to_max);
    $to_max_hour=date('H',$to_max);
    $to_max_min=date('i',$to_max);
    $to_max_sec=date('s',$to_max);
    
    $relativ_from_year=$from_mini_year-date('Y',time());     if($relativ_from_year>=0)    {$relativ_from_year='+'.$relativ_from_year; };
    $relativ_from_month=$from_mini_month-date('m',time());   if($relativ_from_month>=0)   {$relativ_from_month='+'.$relativ_from_month; };
    $relativ_from_day=$from_mini_day-date('d',time());       if($relativ_from_day>=0)     {$relativ_from_day='+'.$relativ_from_day; };
    
    $relativ_to_year=$to_max_year-date('Y',time());     if($relativ_to_year>=0)     {$relativ_to_year='+'.$relativ_to_year; };
    $relativ_to_month=$to_max_month-date('m',time());   if($relativ_to_month>=0)    {$relativ_to_month='+'.$relativ_to_month; };
    $relativ_to_day=$to_max_day-date('d',time());       if($relativ_to_day>=0)      {$relativ_to_day='+'.$relativ_to_day; };
    
    $from='
	<table width="410" border="0">
            <colgroup>
                <col width="50"/>
                <col width="40"/>
                <col width="40"/>
                <col width="200"/>
                <col width="40"/>
                <col width="40"/>
            </colgroup>
	    <tr>
		<th rowspan="2" style="valign:top"><input type="hidden" name="arc" value="1"><b>From:</b></th>
		<td valign="bottom"><input type="text" size="3" id="j_from'.$name.'-dd" name="j_from'.$name.'-dd" value="'.$from_day.'" maxlength="2"  '.$select_time_activ.' style="width:20px"/></td>
		<td valign="bottom"><input type="text" size="3" id="j_from'.$name.'-mm" name="j_from'.$name.'-mm" value="'.$from_month.'" maxlength="2"  '.$select_time_activ.' style="width:20px"/></td>
		<td valign="bottom"><input type="text" size="3" id="j_from'.$name.'-yyyy" name="j_from'.$name.'-yyyy" value="'.$from_year.'" maxlength="4"  '.$select_time_activ.' style="width:40px"/></td>
		<td valign="bottom"></td>
                <td valign="bottom"><select id="j_from'.$name.'-hh" name="j_from'.$name.'-hh"  '.$select_time_activ.'>'.select($value_h,$option_h,$from_hour,'').'</select></td>
		<td valign="bottom"><select id="j_from'.$name.'-mi"  name="j_from'.$name.'-mi"  '.$select_time_activ.'>'.select($value_min,$option_min,$from_min,'').'</select></td>
	    </tr>
            <tr>
                <td valign="top">DD</td>
		<td valign="top">MM</td>
		<td valign="top">YYYY</td>
		<td valign="top"></td>
		<td valign="top">HH</td>
		<td valign="top">MM</td>
            </tr>
	</table>
		
            <!-- java script part -->
		<script type="text/javascript">
		var today     = new Date(),
				rangeLow  = new Date(today.getFullYear()'.$relativ_from_year.', today.getMonth()'.$relativ_from_month.', today.getDate()'.$relativ_from_day.'),
				rangeHigh = new Date(today.getFullYear()'.$relativ_to_year.' , today.getMonth()'.$relativ_to_month.', today.getDate()'.$relativ_to_day.') // highest always today

                var opts = {                            
                    formElements:{"j_from'.$name.'-yyyy":"Y","j_from'.$name.'-mm":"m","j_from'.$name.'-dd":"d"},
                showWeeks:true,
                statusFormat:"l-cc-sp-d-sp-F-sp-Y", 
				// Set some dynamically calculated ranges
                rangeLow:rangeLow.getFullYear() + "" + pad(rangeLow.getMonth()+1) + pad(rangeLow.getDate()),
                rangeHigh:rangeHigh.getFullYear() + "" + pad(rangeHigh.getMonth()+1) + pad(rangeHigh.getDate())                	
                };           
			datePickerController.createDatePicker(opts);
		</script> 
		<!-- java script part -->';

    $to='
	<table width="400" border="0">
            <colgroup>
                <col width="50"/>
                <col width="40"/>
                <col width="40"/>
                <col width="190"/>
                <col width="40"/>
                <col width="40"/>
            </colgroup>
	    <tr>
		<th rowspan="2"><input type="hidden" name="arc" value="1"><b>To:</b></th>
		<td valign="bottom"><input type="text" size="3" id="j_to'.$name.'-dd" name="j_to'.$name.'-dd" value="'.$to_day.'" maxlength="2"  '.$select_time_activ.'  style="width:20px"/></td>
		<td valign="bottom"><input type="text" size="3" id="j_to'.$name.'-mm" name="j_to'.$name.'-mm" value="'.$to_month.'" maxlength="2"  '.$select_time_activ.' style="width:20px"/></td>
		<td valign="bottom" class="lastTD"><input type="text" size="3" id="j_to'.$name.'-yyyy" name="j_to'.$name.'-yyyy" value="'.$to_year.'" maxlength="4"  '.$select_time_activ.' style="width:40px"/></td>
                <td valign="bottom"></td>
                <td valign="bottom"><select id="j_to'.$name.'-hh" name="j_to'.$name.'-hh"  '.$select_time_activ.'>'.select($value_h,$option_h,$to_hour,'').'</select></td>
		<td valign="bottom"><select id="j_to'.$name.'-mi"  name="j_to'.$name.'-mi"  '.$select_time_activ.'>'.select($value_min,$option_min,$to_min,'').'</select></td>
            </tr>
            <tr>
                <td valign="top">DD</td>
		<td valign="top">MM</td>
		<td valign="top">YYYY</td>
		<td valign="top"></td>
		<td valign="top">HH</td>
		<td valign="top">MM</td>
            </tr>
	</table>
	
            <!-- java script part -->
		<script type="text/javascript">
		var today     = new Date(),
				rangeLow  = new Date(today.getFullYear()'.$relativ_from_year.', today.getMonth()'.$relativ_from_month.', today.getDate()'.$relativ_from_day.'),
				rangeHigh = new Date(today.getFullYear()'.$relativ_to_year.' , today.getMonth()'.$relativ_to_month.', today.getDate()'.$relativ_to_day.') // highest always today

                var opts = {                            
                    formElements:{"j_to'.$name.'-yyyy":"Y","j_to'.$name.'-mm":"m","j_to'.$name.'-dd":"d"},
                showWeeks:true,
                statusFormat:"l-cc-sp-d-sp-F-sp-Y", 
				// Set some dynamically calculated ranges
                rangeLow:rangeLow.getFullYear() + "" + pad(rangeLow.getMonth()+1) + pad(rangeLow.getDate()),
                rangeHigh:rangeHigh.getFullYear() + "" + pad(rangeHigh.getMonth()+1) + pad(rangeHigh.getDate())                	
                };           
			datePickerController.createDatePicker(opts);
		</script> 
		<!-- java script part -->';
    $css='<link href="javascript/date-picker-v5/css/datepicker.css" rel="stylesheet" type="text/css" />';
    $java_once='<script type="text/javascript">
            function pad(value, length) { 
                    length = length || 2; 
                    return "0000".substr(0,length - Math.min(String(value).length, length)) + value; 
            };
            </script>
            <script type="text/javascript" src="javascript/date-picker-v5/js/datepicker.js">{"describedby":"fd-dp-aria-describedby"}</script>';
            
    $java='<script type="text/javascript">
            function calc_time'.$name.'(){
                var from_mi=document.getElementById(\'j_from'.$name.'-mi\').value;
                var from_hh=document.getElementById(\'j_from'.$name.'-hh\').value;
                var from_dd=document.getElementById(\'j_from'.$name.'-dd\').value;
                var from_mm=document.getElementById(\'j_from'.$name.'-mm\').value;
                var from_yyyy=document.getElementById(\'j_from'.$name.'-yyyy\').value;
                
                var to_mi=document.getElementById(\'j_to'.$name.'-mi\').value;
                var to_hh=document.getElementById(\'j_to'.$name.'-hh\').value;
                var to_dd=document.getElementById(\'j_to'.$name.'-dd\').value;
                var to_mm=document.getElementById(\'j_to'.$name.'-mm\').value;
                var to_yyyy=document.getElementById(\'j_to'.$name.'-yyyy\').value;
                
                <!-- calculate timestamp -->
                var from_mts = new Date.UTC(from_yyyy,from_mm-1,from_dd,from_hh,from_mi,0);
                
                <!--alert(\'berechne: \'+from_yyyy+\':\'+from_mm+\':\'+from_dd+\':\'+from_hh+\':\'+from_mi+\':\'+\'0\');-->
                var from_ts=from_mts / 1000;
                
                var to_mts = new Date.UTC(to_yyyy,to_mm-1,to_dd,to_hh,to_mi,0);
                var to_ts=to_mts / 1000;
            
                document.getElementById(\'j_from'.$name.'-ts\').value=from_ts;    
                document.getElementById(\'j_to'.$name.'-ts\').value=to_ts;
                setTimeout("calc_time'.$name.'()",500);
            };
            setTimeout("calc_time'.$name.'()",1);
            </script>';
    
    $output_to='<input '.$button.' id="j_from'.$name.'-ts" name="j_from'.$name.'-ts" value="'.$from_ts.'" />
                <input '.$button.' id="j_to'.$name.'-ts" name="j_to'.$name.'-ts" value="'.$to_ts.'" />';
    
    $return['from']=$from;
    $return['to']=$to;
    $return['java_once']=$java_once;
    $return['java']=$java;
    $return['css']=$css;
    $return['output_to']=$output_to;
    
    return $return;
};

/* Returns a link with a chartbutton class
*/
  function chart_button($title,$link,$value,$compare){
	if($value==$compare)	{ $class='chartButtonActive'; }
	else 			{ $class='chartButton'; };
	return '<a class="'.$class.'" href="'.$link.'">'.$title.'</a>';
};

/* function to replace a empty string with an "n/a" */
function etna($str){
    //echo '<!--hi, ich hab "'.$str.'" bekommen das hat eine länge von '.strlen($str).' und den Typ '.gettype($str).' und den char wert von '.ord($str[0]).' und sende jetzt ';
    if((strlen($str)==0 && $str!='0')||(gettype($str)=='boolean' && strlen($str)==0)){
        //echo "n/a-->\n";
        return 'n/a';
    } else {    //echo $str."-->\n";
        return $str;
    };
};

/* This function gives back a binary string to write in a file
 * formated based on a length attribut
 * 
 * Parameters: 
 * $int = the int value 
 * $length = the length of the string in byte
 *
 * Return value:
 * $return = formated String 
 * 
 * Author: Kolja Windeler
 */
function int2blob($int,$length){
    $total_length=$length;
    $return='';
    $length--;
    while($length>=0){
        $teiler=pow(256,$length);
        $return.=pack("C",floor($int/$teiler)%256);
        $length--;
    };
    return $return;
}

/* Javavbased Check And Fill
* This function checks if a java obj exists and
 * changes properties if possible
 * 
 * Parameters: 
 * $obj = the id of the object
 * $prop = the property, document.getElementById('bla').src => "src"
 * $value = the new value
 *
 * Return value:
 * no real, indirect:the java statement
 * 
 * Author: Kolja Windeler
 */
function jcaf($obj,$prop,$value){
    // für funktionen, aufruf als array, dann wird zuerste das objekt auf existenz und dann dessen funktion geprüft
    // aufruf jcaf(array('amstock_'.$b,'setData'),'setData(\''.$data[$b]['rpsd_out'].'\')','');
    if(is_array($obj) && count($obj)==2){
        echo 'if(parent.document.getElementById(\''.$obj[0].'\')) { if(parent.document.getElementById(\''.$obj[0].'\').'.$obj[1].') { ';
        $obj=$obj[0];
        $ending='};};';
    } else {
        echo 'if(parent.document.getElementById(\''.$obj.'\')) {';
        $ending='};';
    };
    if(!empty($value)){
        echo 'parent.document.getElementById(\''.$obj.'\').'.$prop.'=\''.$value.'\';';
    } else {
        echo 'parent.document.getElementById(\''.$obj.'\').'.$prop.';';
    };
    echo $ending."\n";
}

/* Function to get box icon
*
* Parameter:
* $ident better
* $id if ident not available
*
* Return Parameter
* $str the box image
* $status: Box status
*
* Author: Kolja Windeler
*/
function get_box_color($ident,$id){
    if(empty($ident)){
        list($ident)=oci_list($conn,"SELECT `ident` FROM `rfm_box` where id = '".$id."';");
    }
    if($debug && empty($ident)){
        return 1; //
    };
    return 0;
}

/* This function returns a javascript calendar using jquery
 * 
 *
 * Parameters:
 * $select_time_activ = '' or 'disabled', fields activ or not
 * $from = preselection
 * $to = preselection
 * $from_mini = time of first date
 * $to_max = time of last date
 * $name = unique(!) identifier for the calendar
 * $fields = show H:i:s fields [ comma seperated string, CAN contain "h","i","s"] e.g to show hours and seconds 'h,s'
 * $submit_on_change = boolean, if true the form will be submitted as soon as the value of one of the times changes
 *
 * 
 * Return value:
 * $return = String with the calendar
 *
 * Note:
 * Do not add java_once ... only css, java
 * 
 * Author: Kolja Windeler 26.10
 */        

function java_cal2($select_time_activ,$from,$to,$from_mini,$to_max,$name,$fields,$submit_on_change){
    $debug=false;
    if($debug){ echo 'got: from='.$from.' to='.$to.' from_mini='.$from_mini.' to_max='.$to_max.'<br>'; };
    if(!isset($jquery_included)){
        global $jquery_included;
        $jquery_included=true;
    
        $css.='
        <link type="text/css" href="javascript/jquery-ui-1.8.9.custom/css/redmond/jquery-ui-1.8.9.custom.css" rel="Stylesheet" />	
        <!--<script type="text/javascript" src="javascript/jquery-ui-1.8.9.custom/js/jquery-1.4.2.min.js"></script>-->
        <script type="text/javascript" src="javascript/jquery-ui-1.8.9.custom/js/jquery-ui-1.8.9.custom.min.js"></script>';
    };
    
    // check state
    if($select_time_activ<>'' && $select_time_activ<>'disabled'){
        echo "illegal activ state";
        return 1;
    };
    
    // create time 
    for($i=0;$i<60;$i++) { $option_min[$i]=addzero2($i,2);    $value_min[$i]=$i; };
    for($i=0;$i<60;$i++) { $option_sec[$i]=addzero2($i,2);    $value_sec[$i]=$i; };
    for($i=0;$i<24;$i++) { $option_h[$i]=addzero2($i,2);      $value_h[$i]=$i; };
    
    
    //create vars    
    $fields_arr=explode(',',$fields);
    
    $from_date_formated=date('Y-m-d',$from);
    $from_h_formated=date('H',$from);
    $from_m_formated=date('i',$from);
    $from_s_formated=date('s',$from);
    
    $to_date_formated=date('Y-m-d',$to);
    $to_h_formated=date('H',$to);
    $to_m_formated=date('i',$to);
    $to_s_formated=date('s',$to);
    
    
    if(in_array('h',$fields_arr) || in_array('i',$fields_arr) || in_array('s',$fields_arr)){
        $from_arr['col_def'][0]   = '<col width="30"/>';
        $from_arr['spacer'][0]    = '<td valign="bottom">&nbsp;</td>';
        $to_arr['col_def'][0]     = '<col width="30"/>';
        $to_arr['spacer'][0]      = '<td valign="bottom">&nbsp;</td>';
    } else {
        $from_arr['col_def'][0]   = '';
        $from_arr['spacer'][0]    = '';
        $to_arr['col_def'][0]     = '';
        $to_arr['spacer'][0]      = '';
    };

    $his_short=explode(',','h,i,s');
    $his_long=explode(',','hh,mi,ss');
    $selects_from[0]=$from_h_formated;
    $selects_from[1]=$from_m_formated;
    $selects_from[2]=$from_s_formated;
    $selects_to[0]=$to_h_formated;
    $selects_to[1]=$to_m_formated;
    $selects_to[2]=$to_s_formated;
    $options[0]=$option_h;
    $options[1]=$option_min;
    $options[2]=$option_sec;
    $values[0]=$option_h;
    $values[1]=$option_min;
    $values[2]=$option_sec;

    for($a=0;$a<3;$a++){
        if(in_array($his_short[$a],$fields_arr)){
            $from_arr['col_def'][$his_short[$a]] = '<col width="40"/>';
            $from_arr['field'][$his_short[$a]]   = '<td valign="bottom"><select id="j_from'.$name.'-'.$his_long[$a].'" name="j_from'.$name.'-'.$his_long[$a].'"  '.$select_time_activ.' onchange="calc_time'.$name.'();">'.select($values[$a],$options[$a],$selects_from[$a],'').'</select></td>';
            $from_arr['desc'][$his_short[$a]]    = '<td valign="top">'.strtoupper($his_long[$a]).'</td>';
            $to_arr['col_def'][$his_short[$a]]   = '<col width="40"/>';
            $to_arr['field'][$his_short[$a]]     = '<td  style="vertical-align: bottom"><select id="j_to'.$name.'-'.$his_long[$a].'" name="j_to'.$name.'-'.$his_long[$a].'"  '.$select_time_activ.' onchange="calc_time'.$name.'();">'.select($values[$a],$options[$a],$selects_to[$a],'').'</select></td>';
            $to_arr['desc'][$his_short[$a]]      = '<td style="vertical-align: top">'.strtoupper($his_long[$a]).'</td>';
        } else {
            $from_arr['col_def'][$his_short[$a]] = '';
            $from_arr['field'][$his_short[$a]]   = '';
            $from_arr['desc'][$his_short[$a]]    = '';
            $to_arr['col_def'][$his_short[$a]]   = '';
            $to_arr['field'][$his_short[$a]]     = '';
            $to_arr['desc'][$his_short[$a]]      = '';
        };
    };
    
    
    $from='
	<table width="*" border="0">
            <colgroup>
                <col width="40"/>
                <col width="110"/>'.
                $from_arr['col_def'][0]."\n".
                $from_arr['col_def']['h']."\n".
                $from_arr['col_def']['i']."\n".
                $from_arr['col_def']['s']."\n".
            '</colgroup>
	    <tr>
		<td rowspan="2" style="vertical-align: top;font-weight: bold">From:</td>
		<td valign="bottom"><input type="text" size="3" id="j_from'.$name.'-date" name="j_from'.$name.'-date" value="'.$from_date_formated.'" maxlength="10"  '.$select_time_activ.' style="width:100px"/ onchange="calc_time'.$name.'();"></td>'.
                $from_arr['spacer'][0]."\n".
                $from_arr['field']['h']."\n".
                $from_arr['field']['i']."\n".
                $from_arr['field']['s']."\n".
            '</tr>
            <tr>
                <td valign="top">YYYY-MM-DD</td>'.
                $from_arr['spacer'][0]."\n".
		$from_arr['desc']['h']."\n".
                $from_arr['desc']['i']."\n".
                $from_arr['desc']['s']."\n".
	    '</tr>
	</table><input name="j_from'.$name.'-ts" id="j_from'.$name.'-ts" type="hidden" value="">';
    
    $to='
	<table width="*" border="0">
            <colgroup>
                <col width="40"/>
                <col width="110"/>'.
                $to_arr['col_def'][0]."\n".
                $to_arr['col_def']['h']."\n".
                $to_arr['col_def']['i']."\n".
                $to_arr['col_def']['s']."\n".  
            '</colgroup>
	    <tr>
		<td rowspan="2" style="vertical-align: top;font-weight: bold"><input type="hidden" name="arc" value="1">To:</td>
		<td  style="vertical-align: bottom"><input type="text" size="3" id="j_to'.$name.'-date" name="j_to'.$name.'-date" value="'.$to_date_formated.'" maxlength="10"  '.$select_time_activ.' style="width:100px"/ onchange="calc_time'.$name.'();"></td>'.
                $to_arr['spacer'][0]."\n".
                $to_arr['field']['h']."\n".
                $to_arr['field']['i']."\n".
                $to_arr['field']['s']."\n".
            '</tr>
            <tr>
                <td style="vertical-align: top">YYYY-MM-DD</td>'.
                $to_arr['spacer'][0]."\n".
		$to_arr['desc']['h']."\n".
                $to_arr['desc']['i']."\n".
                $to_arr['desc']['s']."\n".
	    '</tr>
	</table><input name="j_to'.$name.'-ts" id="j_to'.$name.'-ts" type="hidden">';
        
        
    $jquery='
        <script>
	$(function() {
		$( "#j_from'.$name.'-date" ).datepicker({
                        showWeek: true,
                        numberOfMonths: 3,
			showButtonPanel: true,
                        dateFormat: \'yy-mm-dd\',
                        minDate: new Date('.date("Y",$from_mini).', '.(date("m",$from_mini)-1).', '.date("d",$from_mini).'),
                        maxDate: new Date('.date("Y",$to_max).', '.(date("m",$to_max)-1).', '.date("d",$to_max).')
		});
                
                $( "#j_to'.$name.'-date" ).datepicker({
                        showWeek: true,
                        numberOfMonths: 3,
			showButtonPanel: true,
			dateFormat: \'yy-mm-dd\',
                        minDate: new Date('.date("Y",$from_mini).', '.(date("m",$from_mini)-1).', '.date("d",$from_mini).'),
                        maxDate: new Date('.date("Y",$to_max).', '.(date("m",$to_max)-1).', '.date("d",$to_max).')
		});
	});
	</script>';
        
    $java='<script type="text/javascript">
            var lock=0;
            
            function calc_time'.$name.'(){
                var from_ss=0;
                var from_mi=0;
                var from_hh=0;
                if(document.getElementById(\'j_from'.$name.'-ss\')){    from_ss=document.getElementById(\'j_from'.$name.'-ss\').value;  }
                if(document.getElementById(\'j_from'.$name.'-mi\')){    from_mi=document.getElementById(\'j_from'.$name.'-mi\').value;  }
                if(document.getElementById(\'j_from'.$name.'-hh\')){    from_hh=document.getElementById(\'j_from'.$name.'-hh\').value;  }
                
                var from_date=document.getElementById(\'j_from'.$name.'-date\').value;
                var from_dd = from_date.substr(8,2);
                var from_mm=from_date.substr(5,2);
                var from_yyyy=from_date.substr(0,4);
                
                var to_ss=0;
                var to_mi=0;
                var to_hh=0;
                if(document.getElementById(\'j_to'.$name.'-ss\')){    to_ss=document.getElementById(\'j_to'.$name.'-ss\').value;  }
                if(document.getElementById(\'j_to'.$name.'-mi\')){    to_mi=document.getElementById(\'j_to'.$name.'-mi\').value;  }
                if(document.getElementById(\'j_to'.$name.'-hh\')){    to_hh=document.getElementById(\'j_to'.$name.'-hh\').value;  }
                
                var to_date=document.getElementById(\'j_to'.$name.'-date\').value;
                var to_dd = to_date.substr(8,2);
                var to_mm=to_date.substr(5,2);
                var to_yyyy=to_date.substr(0,4);
                
                <!-- calculate timestamp -->
                var from_mts = new Date(from_yyyy,from_mm-1,from_dd,from_hh,from_mi,0);
                
                <!--alert(\'berechne: \'+from_yyyy+\':\'+from_mm+\':\'+from_dd+\':\'+from_hh+\':\'+from_mi+\':\'+\'0\');-->
                var jetzt_UTC = new Date();
                var from_ts=(from_mts-(jetzt_UTC.getTimezoneOffset()+60)*60000) / 1000; <!-- zum verbessern -->
                <!--alert(new Date(from_yyyy,from_mm-1,from_dd,from_hh,from_mi,0).getTime()/1000);-->
                
                var to_mts = new Date(to_yyyy,to_mm-1,to_dd,to_hh,to_mi,0);
                var to_ts=(to_mts-(jetzt_UTC.getTimezoneOffset()+60)*60000) / 1000;
            
                document.getElementById(\'j_from'.$name.'-ts\').value=from_ts;    
                document.getElementById(\'j_to'.$name.'-ts\').value=to_ts;
                <!--setTimeout("calc_time'.$name.'()",500);-->'."\n";
    if($submit_on_change){
        $java.='if(lock>=1){ document.forms[0].submit(); };'."\n";
    };
        $java.='lock++;
                };
            setTimeout("calc_time'.$name.'()",1);
            </script>';
        
    $return['from']=$from;
    $return['to']=$to;
    $return['java']=$java.$jquery;
    $return['css']=$css;
    
    return $return;
};

?> 
