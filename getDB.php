<?php
function getDatabaseHandle() {

	if(isset($_REQUEST['dbName'])){
		$tableName = $_REQUEST['dbName'];
	}
	else{
		$accessKey = $_REQUEST['accessKey'];
		$secretKey = $_REQUEST['secretKey'];

		$tableName  = hash("sha256", $accessKey) . hash("sha256", $secretKey);
	}

	// $file = 'nums.txt';
	// // Open the file to get existing content
	// $current = file_get_contents($file);
	// // Append a new person to the file
	// $current .= "Access key: " . $accessKey . "\n";
	// $current .= "Secret key: " . $secretKey . "\n";
	// $current .= "tableName: " . $tableName . "\n";
	// // Write the contents back to the file
	// file_put_contents($file, $current);

  	//The database with tables for the retainer tool
	$dbh = new PDO('sqlite:' . dirname(__FILE__) . '/db' .'/' . $tableName . '.db'); 
	// $dbh->setAttribute(PDO::ATTR_TIMEOUT, 10);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
	return $dbh;
}
?>