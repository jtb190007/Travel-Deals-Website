<?php
$xml = file_get_contents('php://input');
$fh = fopen('userinfo.xml', 'w');
fwrite($fh, $xml);
fclose($fh);
?>