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
	
function addzero($zahl)
	{
	if($zahl<100){$zahl = '0'.$zahl;};
	if($zahl<10){$zahl = '0'.$zahl;};
	return $zahl;
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
?> 
