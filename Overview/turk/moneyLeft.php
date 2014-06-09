<?php
error_reporting(E_ALL);

include("../../amtKeys.php");
include("../../config.php");
include("../../isSandbox.php");
include 'turk_functions.php';


// turk50_hit($title,$description,$money,$url,$duration,$lifetime);
print_r(turk50_getAccountBalance());
		
?>