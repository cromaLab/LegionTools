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

	$worker = $_REQUEST['workerId'];
	
	$sql1 = "SELECT startTime from workers WHERE wid=:wid ORDER BY id DESC LIMIT 1";
	$sth1 = $dbh->prepare($sql1); 
	$sth1->execute(array(':wid' => $worker));
	$result = $sth1->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$startTime = $result['startTime'];
	
	$sql1 = "SELECT endTime from workers WHERE wid=:wid ORDER BY id DESC LIMIT 1";
	$sth1 = $dbh->prepare($sql1); 
	$sth1->execute(array(':wid' => $worker));
	$result = $sth1->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$endTime = $result['endTime'];
	
	
	$sql1 = "SELECT TIME_TO_SEC(TIMEDIFF(:endTime, :startTime)) AS time";
	$sth1 = $dbh->prepare($sql1); 
	$sth1->execute(array(':startTime' => $startTime, ':endTime' => $endTime));
	$result = $sth1->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$timeWaited = $result['time'];
	
	echo($timeWaited);
	
}

?>
