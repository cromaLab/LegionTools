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

	$sql = "SELECT * FROM retainer WHERE task = :task";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':task' => $task));
	$result = $sth->fetchAll();
	echo json_encode($result[0]);
}

?>
