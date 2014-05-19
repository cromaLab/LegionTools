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
	$target_workers = $_REQUEST['target_workers'];

	$sql = "UPDATE retainer SET target_workers = :target_workers WHERE task =:task";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':task' => $task, ':target_workers' => $target_workers));
	
}

?>
