<?php
function getDatabaseHandle() {
  //The database with tables for the retainer tool
	$dbh = new PDO('sqlite:' . dirname(__FILE__) . '/db/retainer.db'); 
	// $dbh->setAttribute(PDO::ATTR_TIMEOUT, 10);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, 
	                            PDO::ERRMODE_EXCEPTION); 
	return $dbh;
}
?>
