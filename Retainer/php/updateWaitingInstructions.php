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

	$task = $_REQUEST['task'];
	$instructions = $_REQUEST['instructions'];

	$sql = "UPDATE retainer SET instructions = :instructions WHERE task =:task";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':task' => $task, ':instructions' => $instructions));
	
}

?>
