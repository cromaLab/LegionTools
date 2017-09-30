<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
error_log(".:: ".basename(__FILE__),0); // Debugging

include('_db.php');
include('../../Overview/turk/turk_functions.php');
include("../../amtKeys.php");
include("../../isSandbox.php");

$AccessKey = $_REQUEST['accessKey']; 
$SecretKey = $_REQUEST['secretKey'];

$SANDBOX = false;
// $data = turk_easyHitToAssn('3NZ1E5QA6Z154BHKXXFZI5J21CWB5U‏');

// print_r($data);

print_r(turk_easyApprove('3KB8R4ZV1E756S4Y5W3SAL5CCJNGBK'));
?>
