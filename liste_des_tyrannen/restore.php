<?php
include('scripts/config.php');
include('scripts/fkt_jkw.php');


$daten="
(1, 'Stephan Baron', '', 0),
(2, 'Florian Bartsch', '', 0),
(4, 'Vincent Bertram', '', 0),
(5, 'Tim Bierschwale', '', 0),
(6, 'Matthias Binner', '', 0),
(7, 'Boris Iwanzik', '', 0),
(8, 'Bj&ouml;rn Meyer', '', 0),
(9, 'Oliver Buse', '', 0),
(10, 'Thadd&auml;us Delebinski', '', 0),
(11, 'Marc D&ouml;rrie', '', 0),
(12, 'Daniel Drechsler', '', 0),
(13, 'Fabian L&uuml;cking', '', 0),
(14, 'Henning Feldmann', '', 0),
(15, 'Stefan Galler', '', 0),
(16, 'Uwe Gerken', '', 0),
(17, 'Arne Gercken', '', 0),
(18, 'Alfred Gr&uuml;nder', '', 0),
(19, 'Jonas Hahlbohm', '', 0),
(20, 'Karsten Heinig', '', 0),
(21, 'Holger ?', '', 0),
(22, 'Harald H&uuml;per', '', 0),
(23, 'Holger J&uuml;rgens', '', 0),
(24, 'Andreas Kautz', '', 0),
(25, 'Uwe Kalle', '', 0),
(26, 'Oliver Kerker', '', 0),
(27, 'J&uuml;rn Kirbach', '', 0),
(28, 'J&uuml;rgen Klotzek', '', 0),
(29, 'Mathias Kotyrba', '', 0),
(30, 'Dirk Krome', '', 0),
(31, 'Maik Krumm', '', 0),
(32, 'Marc Kr&uuml;ger', '', 0),
(33, 'Richard Lochte', '', 0),
(34, 'Walter Marten', '', 0),
(35, 'Heiner Meuser', '', 0),
(36, 'Nima Monzavi', '', 0),
(37, 'Eike Ness', '', 0),
(38, 'Jens Nolden', '', 0),
(39, 'Karsten Oehlert', '', 0),
(40, 'Christian P&ouml;pper', '', 0),
(60, 'Christian Schickedanz', '', 0),
(42, 'Sebastian Rakowski', '', 0),
(43, 'Carsten Reinhardt', '', 0),
(44, 'Arne Roßberg', '', 0),
(45, 'Michael Rottmann', '', 0),
(46, 'Mark Schaper', '', 0),
(48, 'Christian Seefisch', '', 0),
(49, 'Henning Seefisch', '', 0),
(50, 'Frank Siefken', '', 0),
(51, 'Christian Stahlhut', '', 0),
(52, 'Hannes Str&uuml;bing', '', 0),
(53, 'Frank Tunnat', '', 0),
(54, 'Conrad Wadepohl', '', 0),
(56, 'Kolja Windeler', 'KKoolljjaa@gmail.com,1', 1252023087),
(70, 'Jan Wegner', '', 0),
(58, 'Lars Wollenweber', '', 0),
(59, 'Frank Zohren', '', 0),
(61, 'Christoph Heinemann', '', 0),
(62, 'Thomas Ameling', '', 0),
(63, 'Knut Milbradt', '', 0),
(64, 'Rafael Kascha', '', 0),
(65, 'Philip Kurz', '', 0),
(66, 'Simon Weber', '', 0),
(67, 'Daniel Wickert', '', 0),
(68, 'Robert Reichwald', '', 0),
(69, 'Felix HÃ¤gele', '', 0);";

$daten=explode('),',$daten);
for($i=0;$i<=75;$i++) {
	#echo $daten[$i];
	$temp=$daten[$i];
	$temp=str_replace(" '",'',$temp);
	$temp=str_replace("'",'',$temp);
	$temp=str_replace("(",'',$temp);
	$daten[$i]=explode(',',$temp);
	$request="UPDATE `aka_id` set `name`='".$daten[$i][1]."' WHERE `id`='".$daten[$i][0]."';";
	if($mysqli->query($request)) {
	echo 'Datum nr:'.$daten[$i][0].' und der name ist '.$daten[$i][1].'<br>'; }
	else { echo 'error!! -> '.$request.'<br>';};
	
	};