<?php

//print_r($_SERVER);
$pag = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."public/";
?>
<META HTTP-EQUIV=Refresh CONTENT="0; URL=http://<?php echo $pag; ?>">
