<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');

  try {
      $dbh = getDatabaseHandle();
  } catch( PDOException $e ) {
      echo $e->getMessage();
  }


if( isset($_REQUEST['workerId']) && $dbh ) {

    $worker = $_REQUEST['workerId'];
	
	$sql = "SELECT COUNT(*) FROM banned WHERE workerId=:wId";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':wId'=>$worker));
    $count = $sth->fetchColumn();

    echo $count;
}

?>
