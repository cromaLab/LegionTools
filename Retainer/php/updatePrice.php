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
	$min_price = $_REQUEST['min_price'];
	$max_price = $_REQUEST['max_price'];

	$sql = "UPDATE retainer SET min_price = :min_price, max_price = :max_price WHERE task = :task";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':task' => $task, ':min_price' => $min_price, ':max_price' => $max_price));
	
}

?>
