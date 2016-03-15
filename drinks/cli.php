<?php
$time=time();


// http://portal.akakraft.de/drinks/cli.php?action=add&name=Kolja%20Windeler&amount=6.83
if($_GET['action']=="add" && !empty($_GET['name']) && !empty($_GET['amount'])){
	if($_GET['c']!=$_GET['a']*$_GET['a']+$_GET['b'] || empty($_GET['c'])){
		exit("Error 5");
	}
	if(intval($_GET['amount'])){
		$amount=floatval($_GET['amount']);
		if($amount>0 && $amount<500){
			#echo "zahl gefunden ".$amount."<br>";

			include('../a_common_scripts/config.php');
			include('../a_common_scripts/fkt_jkw.php');
			include('collect_data.php');

			$res=get_id_for_name($_GET['name'],$daten); // get id to name	
			if($res[3]>30){ // just accept the name if the likelyhood is at least 30% 
				$id=$res[1];
				// db works
				$request="SELECT date FROM `aka_money` where id='".$id."' and value>-5 order by date desc limit 0,1";
				list($last_transfer)=mysql_fetch_row(mysql_query($request));
				if($last_transfer<$time-5*86400){
					$changed[$id]=1;
					if(!mysql_query( "INSERT INTO `aka_money` (`id` ,`value`, `date`) VALUES ('".$id."', '".$amount."', ".$time.")" )){
						exit("Exit 2");
					} else {
						echo "ok";
						//include('email.php');
						include('email2.php');
					}
				} else {
					exit("exit 4");
				}
				// db works
			} else {
				exit("Error 3");
			}
		} else {
			exit("Exit 9");
		}
	} else {
		exit("Exit 1");
	}
} else if($_GET['action']=="hb"){ 
	include('../a_common_scripts/config.php');
	include('../a_common_scripts/fkt_jkw.php');
	$request="UPDATE aka_hb SET hb_ts =".$time." WHERE id=1 LIMIT 1";
	mysql_query($request);
	exit("exit 10");
} else if($_GET['action']=="hb_check"){
	include('../a_common_scripts/config.php');
	include('../a_common_scripts/fkt_jkw.php');
	$request="SELECT `hb_ts` FROM `aka_hb`  WHERE `id`=1 LIMIT 0,1";
	list($ts)=mysql_fetch_row(mysql_query($request));	
	if($time-$ts>5*86400){	// more then 5 days
		include_once('../a_common_mailer/class.phpmailer.php');
		$mail    = new PHPMailer();
		$body    = '<html><body>Alert: no heartbeat for 5 days</body></html>';
		$body    = eregi_replace("[\]",'',$body);
		$mail->AddReplyTo('Kolja.Windeler@gmail.com');
		$mail->From 	= 'noreply@akakraft.de';
		$mail->FromName = "AKA Checker";
		$mail->Subject = "AKA Checker";
		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 
		$mail->MsgHTML($body);
		$mail->AddAddress('KKoolljjaa@gmail.com');
		$mail->Send();
	}
	exit("exit 11");
} else {
	exit("error 0");
}

?>
