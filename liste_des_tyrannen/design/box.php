<?php

function tab_go($width,$height,$align,$title){

if(substr($title,0,4)<>'<div') $title=' &nbsp; &nbsp; '.$title;
echo '<div class="headline">'.$title.'</div>';
/*echo
'<table width="'.$width.'" style="height: '.$height.'" align="'.$align.'" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
	<TR>	<TD style="background-image:url(design/top.png)" width="24" height="24"></TD>
	<TD style="background-image:url(design/top.png)" colspan="2" height="24"><b>'.$title.'</b></TD></TR>
	<TR><TD >	&nbsp;</TD><TD>';
#	<TR>	<TD style="background-image:url(design/corner_ul.png)" width="24" height="24"></TD>
#<TD style="background-image:url(design/top.png)" colspan="2"><b>'.$title.'</b></TD></TR>
#<TR>	<TD style="background-image:url(design/left.png)"></TD><TD>';*/
};

function tab_end() {
/*echo '</TD>
		<TD ></TD></TR>
	<TR>	<TD height="10" ></TD>
		<TD></TD>
		<TD></TD>
	</TR>
</table>';*/
echo '&nbsp;';
};


function tab_box($width,$height,$align,$title,$text)
	{
	tab_go($width,$height,$align,$title);
	echo $text;
	tab_end();
	};

function htmlhead($title,$add_header,$nobody)
	{
	echo '	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="de">
			<head>
   				<title>'.$title.'</title>
				<link href="design/template.css" rel="stylesheet" type="text/css" />
   				<meta http-equiv="content-type" content="text/html;charset=utf-8" />
   				<meta http-equiv="Content-Style-Type" content="text/css" />
				'.$add_header.'
			</head>';
	if($nobody<>'1')
	echo	'<body>';		
	};

function sub_tab_box($width,$height,$align,$title,$text,$sub)
	{
	sub_tab_go($width,$height,$align,$title,$sub);
	echo $text;
	sub_tab_end($sub);
	};

function sub_tab_go($width,$height,$align,$title,$sub){
	if(substr($title,0,4)<>'<div') $title=' &nbsp; &nbsp; '.$title;
	
	echo
	'<table width="'.$width.'" style="height: '.$height.'" align="'.$align.'" cellspacing="0" cellpadding="0" border="0">
		<TR>	<TD style="background-image:url('.$sub.'/design/corner_ul.png)" width="24" height="24"></TD>
			<TD style="background-image:url('.$sub.'/design/top.png)" colspan="2"><b>'.$title.'</b></TD></TR>
		<TR>	<TD style="background-image:url('.$sub.'/design/left.png)"></TD><TD>';
};

function sub_tab_end($sub) {
echo '</TD>
		<TD ></TD></TR>
	<TR>	<TD height="10" style="background-image:url('.$sub.'/design/left.png)"></TD>
		<TD></TD>
		<TD></TD>
	</TR>
</table>';
};


function sub_htmlhead($title,$add_header,$nobody,$sub)
	{
	echo '	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="de">
			<head>
   				<title>'.$title.'</title>
				<link href="'.$sub.'/design/template.css" rel="stylesheet" type="text/css" />
   				<meta http-equiv="content-type" content="text/html;charset=utf-8" />
   				<meta http-equiv="Content-Style-Type" content="text/css" />
				'.$add_header.'
			</head>';
	if($nobody<>'1')
	echo	'<body>';		
	};
?>

