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

	$taskTitle = $_REQUEST['taskTitle'];
	$taskDescription = $_REQUEST['taskDescription'];
	$taskKeywords = $_REQUEST['taskKeywords'];
	$task = $_REQUEST['task'];
	$country = $_REQUEST['country'];
	$state = $_REQUEST['state'];
	$percentApproved = $_REQUEST['percentApproved'];

	$sql = "UPDATE retainer SET task_title = :task_title, task_description = :task_description, task_keywords = :task_keywords, country = :country, state = :state, percentApproved = :percentApproved WHERE task = :task";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':task_title' => $taskTitle, ':task_description' => $taskDescription, ':task_keywords' => $taskKeywords, ':country' => $country, ':state' => $state, ':percentApproved' => $percentApproved, ':task' => $task));
	
}

?>
