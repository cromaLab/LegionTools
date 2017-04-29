<?php

$logFilePath = './debug.txt';
ob_start();
if (file_exists($logFilePath)) {
        include($logFilePath);
}
$currentTime = date(DATE_RSS);

echo "\n\n$currentTime - Fake service was called.";
//foreach($_REQUEST[0] as $child) {
//    echo $child . "\n";
//}

echo $_REQUEST['test'] . $_SERVER['PHP_AUTH_USER'];

// Log output to file
$logFile = fopen($logFilePath, 'w');
fwrite($logFile, ob_get_contents());
fclose($logFile);
ob_end_flush();

return "test";

