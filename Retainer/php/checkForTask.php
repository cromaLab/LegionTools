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
	
	$sql = "SELECT link as url FROM triggerFlag ORDER BY id DESC LIMIT 1";
	$sth = $dbh->prepare($sql); 
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$urlCheck = $result['url'];
	if($url != null) //if there are no empty tasks it means there are no users in the pool
	{
		$query = $dbh->prepare("INSERT INTO triggerFlag(task) VALUES(:task)");
		$sth1 = $dbh->prepare($query); 
		$sth1->execute(array(':task' => $task));

	}
	
}

?>
