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
	$task = $_REQUEST["task"];
	$isFirst = ($_REQUEST["first"] == "true"); // Need to convert the string to logic

	// Get the number of entries total
	$sql = "SELECT count(*) as numEntries FROM triggerFlag WHERE task=:task";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(":task"=>$task));
	$result = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$numEntries = $result['numEntries'];

	// Get the highest index link
	$sql = "SELECT link FROM triggerFlag WHERE task=:task ORDER BY id DESC LIMIT 1";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(":task"=>$task));
	$result = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$url = $result['link'];

	if( ($isFirst && $url != null) || $numEntries == 0 ) {
		// If there is no current task of the assigned name, create one
		$sql = "INSERT INTO triggerFlag(task) VALUES(:task)";
        	$sth = $dbh->prepare($sql);
        	$sth->execute(array(":task"=>$task));
	}
	else {
		echo($url);
	}
	
}

?>
