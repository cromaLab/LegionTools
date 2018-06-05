<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
error_log(".:: ".basename(__FILE__),0); // Debugging

include("_db.php");


try {
  $dbh = getDatabaseHandle();
} catch( PDOException $e ) {
  echo $e->getMessage();
}


if( $dbh ) {

	$task = $_REQUEST['task'];
	$newTask = $_REQUEST['newTask'];

	$sql = "INSERT INTO retainer(task_title, task_description, task_keywords, task, country, state, percentApproved) select task_title, task_description, task_keywords, :newTask, country, :state, percentApproved from retainer where task = :task limit 1";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':task' => $task, ':newTask' => $newTask));
	echo $newTask;
}

?>
