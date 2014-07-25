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
	
	$sql = "DELETE FROM whois_online WHERE id=:wid";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(':wid'=>$worker));

	echo $worker;
}

?>
