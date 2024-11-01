<?php
header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="'.$_REQUEST['course'].'"');
var_dump($_REQUEST);
?>