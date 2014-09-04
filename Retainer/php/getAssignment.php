<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');
include('../../Overview/turk/turk_functions.php');
include("../../amtKeys.php");
include("../../isSandbox.php");


$SANDBOX = false;
$data = turk_easyHitToAssn('32MVG4819JB64VXGV4TDBJ3HVCVUCD');

print_r($data);

// print_r(turk_easyApprove('3RWE2M8QWHAAWXIAUKJYGOIQLQV0N6'));
?>
