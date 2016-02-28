<?php
echo'<script type="text/javascript">
function confirmLink(theLink, theSqlQuery)
	{
    if (confirmMsg == \'\' || typeof(window.opera) != \'undefined\') {        return true;    }
	var confirmMsg  = \'\';
    var is_confirmed = confirm(theSqlQuery);
	    return is_confirmed;
	} 
</script>';
?>