<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
error_log(".:: ".basename(__FILE__),0); // Debugging

include("../../amtKeys.php");
include("../../isSandbox.php");

// Get MTurk keys of current requester, if they exist
if(isset($_REQUEST['accessKey']) && isset($_REQUEST['secretKey'])) {
    $accessKey = $_REQUEST['accessKey'];
    $secretKey = $_REQUEST['secretKey'];
    
    $tableName  = hash('sha256', $accessKey) . hash('sha256', $secretKey);
    $tableName = hash("sha256", $tableName);
}
else {
	error_log('ERROR in tempKeyStore.php :: No AccessKey or SecretKey.');
}

// Get path to sys tmp dir
$tmp_path = sys_get_temp_dir();

// sys_get_temp_dir() does not always add trailing slash
// http://php.net/manual/en/function.sys-get-temp-dir.php
if(substr($tmp_path, -1) != "/" ) $tmp_path .= "/";

// Create key store dir, if it isn't already there
$key_store_path = $tmp_path."legionTools-tmpKeyStore";
if (!file_exists($key_store_path)) {
    mkdir($key_store_path, 0777, true);
    error_log("Creating key store dir @ ".$key_store_path);
}

// Store keys in tmp folder
$fname = $key_store_path."/".$tableName;
if (!file_exists($fname)) {
    $fh = fopen($fname, 'a');
    fwrite($fh, $accessKey.",".$secretKey);
    fclose($fh);
}

// Delete keys if reset requested
if(isset($_REQUEST['reset']) && $_REQUEST['reset']) {
    if (file_exists($fname)) {        
        error_log('Deleting key store: '.$tableName);
        unlink($fname);
    }
}

?>
