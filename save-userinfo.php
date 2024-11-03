<?php
$xml = file_get_contents('php://input');
$fh = fopen('userinfo.xml', 'r+');
fwrite($fh, $xml);
fclose($fh);
?>