<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');

  try {
      $dbh = getDatabaseHandle();
  } catch( PDOException $e ) {
      echo $e->getMessage();
  }


if( $dbh ) {

	$workerId = $_REQUEST['workerId'];
	
	// Get the worker's most recent entry and update it's endTime
	$sql = "UPDATE workers SET endTime=(DATETIME('now')) WHERE id=(SELECT id FROM workers WHERE wid=:wid ORDER BY id DESC LIMIT 1)";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(":wid"=>$workerId));

	
}

?>
