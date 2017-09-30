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

	$taskTitle = $_REQUEST['taskTitle']; //TODO: project name 
	$workerId= $_REQUEST['workerId'];

	$sql = "INSERT INTO tutorialLog(workerId, projectName) VALUES (:workerId, :task_title)";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':task_title' => $taskTitle, ':workerId' => $workerId)); 
	
}

?>
