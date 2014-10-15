<?php
function getDatabaseHandle() {

	if(isset($_REQUEST['dbName'])){
		$hash1 = $_REQUEST['dbName'];
		$tableName = hash("sha256", $hash1);
		$dbh = new PDO('sqlite:' . dirname(__FILE__) . '/db' .'/' . $tableName . '.db');
	}
	else if($_REQUEST['accessKey'] == "use_file" && $_REQUEST['secretKey'] == "use_file"){
		//The database with tables for the retainer tool
		$dbh = new PDO('sqlite:' . dirname(__FILE__) . '/db/retainer.db');
	}
	else{
		$accessKey = $_REQUEST['accessKey'];
		$secretKey = $_REQUEST['secretKey'];

		$hash1  = hash("sha256", $accessKey) . hash("sha256", $secretKey);
		$tableName = hash("sha256", $hash1);

	  	//The database with tables for the retainer tool
		$dbh = new PDO('sqlite:' . dirname(__FILE__) . '/db' .'/' . $tableName . '.db'); 
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

	// $dbh->setAttribute(PDO::ATTR_TIMEOUT, 10);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
	return $dbh;
}
?>