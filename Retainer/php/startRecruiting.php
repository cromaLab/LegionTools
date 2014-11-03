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

	$task = $_REQUEST['task'];

	$sql = "UPDATE retainer SET done = 0 WHERE task = :task";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':task' => $task));
	echo "success";
	
}

?>
