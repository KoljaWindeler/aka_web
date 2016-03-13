<?php
session_start();
require_once('scripts/fkt_jkw.php');
require_once('design/box.php');
htmlhead('Aka Portal','',1);

echo'<frameset rows="25,*" frameborder="0">
  <frame src="verweise.php" name="Navigation" noresize scrolling=no>
  <frame src="startseite.php" name="Daten">
</frameset>
</html>';

?>
