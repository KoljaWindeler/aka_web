<?php
session_start($SID);
require_once('scripts/fkt_jkw.php');
require_once('design/box.php');
htmlhead('Aka Portal','',0);

echo'
<table border=0 width="100%"><tr><td width="5%">&nbsp;</td><td width="90%"  class="head">
<div style="float:left;">
<a target="Daten" href="../drinks2/" class="head">Zur Getr&auml;nkeabrechnung</a> &nbsp; | &nbsp;
<a target="Daten" href="../liste_des_tyrannen/" class="head">Zur Liste des Tyrannen</a> &nbsp; | &nbsp;
<a target="Daten" href="../protokoll/" class="head">Zu den Protokollen</a> &nbsp; | &nbsp;
<a target="Daten" href="../files/" class="head">Zu den Dateiuploads</a> &nbsp; | &nbsp;
<a target="Daten" href="../reserve/" class="head">Zu dem Reservierungssystem</a> &nbsp; | &nbsp;
<a target="Daten" href="startseite.php" class="head">Zum Start</a>
</div><div style="float:right;">v 0.1b</div><br>
<hr style="height:0;  border-bottom:1px dotted #000000; border-top: 0px;">
</td><td width="5%">&nbsp;</td></tr></table>';



echo'</html>';

?>
