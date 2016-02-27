<?php
#require_once('scripts/fkt_jkw.php');

if($_GET['ansicht']=='Print'){
	define('FPDF_FONTPATH','font/');
	require('scripts/fpdf16/fpdf.php');

	class PDF extends FPDF
	{
	// Header
	function Header()
	{
		//Select Arial bold 15
		$this->SetFont('Arial','',11);
		//Move to the right
		//Framed title
		$this->Cell(195,5,'Alle aktuellen Kontost'.chr(228).'nde per eMail oder online auf www.akakraft.de/drinks | Passwort: akapw ',0,0,'C');
		//Line break
		$this->Ln();
		$this->Cell(195,5,'Falls ihr euch (noch) nicht in der Liste befindet, tragt euch einfach in die leeren Zeilen am Ende ein.',0,0,'C');
		$this->Ln();
	} 
	// Überlagerung der Footer() Methode
    function Footer() 
    { 
            // Über dem unteren Seitenrand positionieren 
            $this->SetY(-15); 
            // Schriftart festlegen
            $this->SetFont('Arial','',8); 
            // Zentrierte Ausgabe der Seitenzahl
			$this->Cell(30,5,date('d.m.y',time()),0,0,'L'); 
            $this->Cell(135,5,'Anregung und Fragen an KKoolljjaa@gmail.com',0,0,'C'); 
			$this->Cell(30,5,'Seite '.$this->PageNo(),0,0,'R'); 
    }  
	//Colored table
	function FancyTable($header,$data)
	{
		global $inactiv;
		//Colors, line width and bold font
		$this->SetFillColor(220,220,220);
		$this->SetTextColor(255);
		$this->SetDrawColor(0,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B');
		//Header
		$w=array(10,60,30,95);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C',1);
		$this->Ln();
		//Color and font restoration
		$this->SetFillColor(240,240,240);
		$this->SetTextColor(0);
		$this->SetFont('');
		//Data
		$fill=0;
		$a=0; $b=0;
		foreach($data as $row)
		{
			if($fill) {$border='LRT'; } 
			else { $border='LRT'; };
			if($a==0){
				$this->SetFont('Arial','',9);
				$height=4;
				$this->Cell($w[0],$height,'',$border,0,'L',$fill);
				$this->Cell($w[1],$height,'- Top 5 -',$border,0,'C',$fill);
				$this->Cell($w[2],$height,'---',$border,0,'C',$fill);
				$this->Cell($w[3],$height,'- Sortiert nach Strichanzahl der letzen Abrechnung -',$border,0,'C',$fill);
				$this->Ln();
				$fill=!$fill;
				$this->SetFont('Arial','',14);
			} elseif($row[1]=='' && $b==0) {
				$b++;
				$this->SetFont('Arial','',9);
				$height=6;
				$this->Cell($w[0],$height,'',$border,0,'L',$fill);
				$this->Cell($w[1],$height,'- Name eintragen -',$border,0,'C',$fill);
				$this->Cell($w[2],$height,'- leer lassen -',$border,0,'C',$fill);
				$this->Cell($w[3],$height,'- Bitte unter der Tabelle eMail Adresse mit passender Zeilennummer -',$border,0,'C',$fill);
				$this->Ln();
				$fill=!$fill;
				$this->SetFont('Arial','',14);
			} elseif($a==$inactiv){
				#echo "drin!!";
				$this->SetFont('Arial','',9);
				$height=6;
				$this->Cell($w[0],$height,'',$border,0,'L',$fill);
				$this->Cell($w[1],$height,'- Seit 120 Tagen kein Strich gemacht -',$border,0,'C',$fill);
				$this->Cell($w[2],$height,'--',$border,0,'C',$fill);
				$this->Cell($w[3],$height,'- Personen die lange nichts getrunken haben -',$border,0,'C',$fill);
				$this->Ln();
				$fill=!$fill;
				$this->SetFont('Arial','',14);
			};
			#echo "gehe durch dir pdf, bin bei ".$row[1].' und zeile '.$row[0].'<br>';
			
			$height=10;
			$this->Cell($w[0],$height,$row[0],$border,0,'L',$fill);
			$this->Cell($w[1],$height,$row[1],$border,0,'L',$fill);
			$this->Cell($w[2],$height,$row[2],$border,0,'R',$fill);
			$this->Cell($w[3],$height,'',$border,0,'R',$fill);
			$this->Ln();
			$fill=!$fill;
			#echo '$row[0] ist gerade '.$row[0].'<br>';
			if($row[0]=='#5'){
				$this->SetFont('Arial','',9);
				$height=4;
				$this->Cell($w[0],$height,'',$border,0,'L',$fill);
				$this->Cell($w[1],$height,'- Rest alphabetisch -',$border,0,'C',$fill);
				$this->Cell($w[2],$height,'---',$border,0,'C',$fill);
				$this->Cell($w[3],$height,' - Nach Nachnamen - ',$border,0,'C',$fill);
				$this->Ln();
				$fill=!$fill;
				$this->SetFont('Arial','',14);
			};
			$a++;
		}
		$this->Cell(array_sum($w),0,'','T');
	}
	}

	$pdf=new PDF();
	//Column titles
	$header=array('#','Name',chr(128),'Striche');

	//temp
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
	function convert2money($geld){
		if(empty($geld)) { $geld=0; };
		$geld_arr=explode('.',$geld);
		if(count($geld_arr)==1) { $geld.='.00'; }
		elseif(strlen($geld_arr[1])==1) { $geld.='0'; }
		return $geld;
	};
	// require
	require_once('scripts/config.php');
	### 1. mal daten laden und sortieren nach dem umsatz vom letzen monat
	include('collect_data.php');
	$daten=kolja_sort($daten,9);
	$daten=array_reverse($daten);
	
	$platz_counter=1;
	
	$daten_remember=array();
	array_push($daten_remember,$daten[0][0]);
	array_push($daten_remember,$daten[1][0]);
	array_push($daten_remember,$daten[2][0]);
	
	// add double line for first 3 
	unset($daten_temp);
	$daten_temp[0]=$daten[0];
	$daten_temp[0][0]='#'.$platz_counter;
	$daten_temp[1]=$daten[0];
	$daten_temp[1][1]=' ';
	$daten_temp[1][0]=''; $platz_counter++;
	$daten_temp[1][8]=''; // kein Guthaben
	
	$daten_temp[2]=$daten[1];
	$daten_temp[2][0]='#'.$platz_counter; 
	$daten_temp[3]=$daten[1];
	$daten_temp[3][1]=' ';
	$daten_temp[3][0]=''; $platz_counter++;
	$daten_temp[3][8]='';
	
	$daten_temp[4]=$daten[2];
	$daten_temp[4][0]='#'.$platz_counter;
	$daten_temp[5]=$daten[2];
	$daten_temp[5][1]=' ';
	$daten_temp[5][0]=''; $platz_counter++;
	$daten_temp[5][8]='';
	// add further 2 
	for($a=6;$a<=7;$a++){
		$nr=$a-3;
		$daten_temp[$a]=$daten[$nr];
		$daten_temp[$a][0]='#'.$platz_counter; $platz_counter++;
		array_push($daten_remember,$daten[$nr][0]);
		};
	unset($daten);
	### now load data again and sort by name
	include('collect_data.php');
	//Data loading
	$daten=kolja_sort($daten,11);
	$daten=array_reverse($daten);
	$platz_counter=1;
	$a=8;
	for($b=0;$b<count($daten);$b++){
		#echo "ich bin bei: ".$daten[$b][1]." und der hat n datum von ".date("d.m.Y H:i:s",$daten[$b][7])."<br>";
		if(!in_array($daten[$b][0],$daten_remember) && $daten[$b][7]>(time()-120*86400)){
			#echo "den hab ich<br>";
		#if(!in_array($daten[$b][0],$daten_remember)){
			array_push($daten_remember,$daten[$b][0]);
			$daten_temp[$a]=$daten[$b];
			$daten_temp[$a][0]=$platz_counter; $platz_counter++;
			$a++;
		};
	};
	$inactiv=$a;
	#echo "inaktiv ist gesetzt auf ".$inactiv."<br>";
	### now load data again and sort by name
	### now load all die nie da sind ;)
	include('collect_data.php');
	$a=$inactiv;
	//Data loading
	$daten=kolja_sort($daten,11);
	$daten=array_reverse($daten);
	for($b=0;$b<count($daten);$b++){
		if(!in_array($daten[$b][0],$daten_remember)){
			$daten_temp[$a]=$daten[$b];
			$daten_temp[$a][0]=$platz_counter; $platz_counter++;
			$a++;
		};
	};
	###
	unset($daten);
	$daten=$daten_temp;
	
	// prepare data
	### daten auswerten und schick machen
	for($a=0;$a<count($daten);$a++){
		#echo "schick machen: bin bei zeile ".$a." und name ".$daten[$a][1]."<br>";
		// namen kürzen und umlaute lesbar machen
		$was = array("&auml;", "&ouml;", "&uuml;", "&Auml;", "&Ouml;", "&Uuml;", "&szlig;");
		$wie = array(chr(228), chr(246), chr(252), chr(192), chr(214), chr(220),chr(223));
		$daten[$a][1] = str_replace($was, $wie, $daten[$a][1]);
		if(strlen($daten[$a][1])>21) { $daten[$a][1]=substr($daten[$a][1],0,19).'...'; } 
		else { $daten[$a][1]=$daten[$a][1]; };	
		
		// geld berechnen und smilies hinzufügen und ausgeben wenns nicht eine wiederholzeile ist
		$geld=round($daten[$a][8]*100)/100;
		
		if($geld>25){$smile=':) '; }
		elseif($geld<-5){$smile=':( '; }
		else { $smile=''; };
		
		if($a<>1 AND $a<>3 AND $a<>5){
			$daten[$a][2]=$smile.convert2money($geld).' '.chr(128);
		} else {
			$daten[$a][2]='';
		};
		// striche
		$daten[$a][3]='';
		};
	### zusätzlich 5 leere zeilen einbauen am ende
	$start_empty_lines=count($daten)+3;
	for($a=$start_empty_lines; $a<$start_empty_lines+5; $a++) {
		#echo 'setze $daten['.$a.'][0] auf '.$c.'<br>';
		$daten[$a][0]=$platz_counter;
		$daten[$a][1]='';
		$daten[$a][2]=' -----   ';
		$platz_counter++;
		};
	#print_r($daten);
	
	$pdf->SetFont('Arial','',15);
	$pdf->AddPage();
	$pdf->FancyTable($header,$daten);
	$pdf->Output(); 
	}
elseif($_GET['ansicht']=='leer'){ ########### leere tabelle
	
	define('FPDF_FONTPATH','font/');
	require('scripts/fpdf16/fpdf.php');

	class PDF extends FPDF
	{
	// Header
	function Header()
	{
		//Select Arial bold 15
		$this->SetFont('Arial','',11);
		//Move to the right
		//Framed title
		$this->Cell(195,8,'Ich bin gerade auf dem Weg eine neue Liste zu drucken.  ',0,0,'C');
		//Line break
		$this->Ln();
		$this->Cell(195,5,'Daher bitte hier Namen und Striche eintragen. Ich werde das dann '.chr(252).'bertragen.',0,0,'C');
		$this->Ln();
	} 
	// Überlagerung der Footer() Methode
	function Footer() 
	{ 
			// Über dem unteren Seitenrand positionieren 
			$this->SetY(-15); 
			// Schriftart festlegen
			$this->SetFont('Arial','',8); 
			// Zentrierte Ausgabe der Seitenzahl
			$this->Cell(30,5,date('d.m.y',time()),0,0,'L'); 
			$this->Cell(135,5,'Anregung und Fragen an KKoolljjaa@gmail.com',0,0,'C'); 
			$this->Cell(30,5,'Seite '.$this->PageNo(),0,0,'R'); 
	}  
	//Colored table
	function FancyTable($header,$data)
	{
		//Colors, line width and bold font
		$this->SetFillColor(220,220,220);
		$this->SetTextColor(255);
		$this->SetDrawColor(0,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B');
		//Header
		$w=array(10,60,25,100);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C',1);
		$this->Ln();
		//Color and font restoration
		$this->SetFillColor(240,240,240);
		$this->SetTextColor(0);
		$this->SetFont('');
		//Data
		$fill=0;
		foreach($data as $row)
		{
			if($fill) {$border='LRT'; } 
			else { $border='LRT'; };
			$height=10;
			$this->Cell($w[0],$height,$row[0],$border,0,'L',$fill);
			$this->Cell($w[1],$height,$row[1],$border,0,'L',$fill);
			$this->Cell($w[2],$height,$row[2],$border,0,'R',$fill);
			$this->Cell($w[3],$height,'',$border,0,'R',$fill);
			$this->Ln();
			$fill=!$fill;
			
		}
		$this->Cell(array_sum($w),0,'','T');
	}
	}

	$pdf=new PDF();
	//Column titles
	$header=array('#','Name',chr(128),'Striche');


	$b=0;
	for($a=$b; $a<$b+20; $a++) {
		$c=$a+1;
		$daten[$a][0]=$c;
		$daten[$a][1]='';
		$daten[$a][2]='';
		};

	$pdf->SetFont('Arial','',15);
	$pdf->AddPage();
	$pdf->FancyTable($header,$daten);
	$pdf->Output(); 
	}
else{
	if($_SESSION['session_user_typ']<>$aka_drinks_admin_state && $_SESSION['session_user_typ']<>$aka_super_admin_state) { exit('falsches passwort'); };
	##################### security ################################
	echo '<br><br><center><a href="liste.php?ansicht=Print" target="_blank">Liste</a> | <a href="liste.php?ansicht=leer" target="_blank">Blanko</a></center><br><br><br>';
	};
	
?>
