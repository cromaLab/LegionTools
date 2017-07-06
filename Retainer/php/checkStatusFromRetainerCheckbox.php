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

	// update the checked status from the retainer when a checkbox is clicked  
	$sql = "SELECT checked from retainerRoutingCheck WHERE workerId=:wid"; 
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(":wid"=>$workerId));

}

?>
