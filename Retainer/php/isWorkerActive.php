<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');
$isActive = 1;

  try {
      $dbh = getDatabaseHandle();
  } catch( PDOException $e ) {
      echo $e->getMessage();
  }


if( $dbh ) {

	$worker = $_REQUEST['workerId'];
	$stmt = $dbh->prepare("SELECT `id` FROM `whois_online` WHERE `id` = :id LIMIT 1");
    $stmt->execute(array(':id' => $worker));

    //if there is already an entry for this worker is whois_live, return true
    if ( $stmt->rowCount() > 0 ) {
        echo 1;
    }
    //if the worker is not currently active, return false
    else echo 0;
		
}

//echo isActive();

?>
