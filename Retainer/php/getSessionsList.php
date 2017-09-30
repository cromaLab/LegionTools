<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
error_log(".:: ".basename(__FILE__),0); // Debugging

include('_db.php');

  try {
      $dbh = getDatabaseHandle();
  } catch( PDOException $e ) {
      echo $e->getMessage();
  }


if( $dbh ) {

	$resultHitIds = array();
	$resultHits = array();

	$sql = "SELECT task FROM retainer";
	$sth = $dbh->prepare($sql); 
	$sth->execute();
	$result = $sth->fetchAll();
	// print_r($result);
	
	echo json_encode($result);

}

?>
