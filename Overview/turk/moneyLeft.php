<?php
error_reporting(E_ALL);

include("../../amtKeys.php");
include("../../baseURL.php");
include("../../isSandbox.php");
include 'turk_functions.php';


// turk50_hit($title,$description,$money,$url,$duration,$lifetime);
print_r(turk50_getAccountBalance());
		
?>