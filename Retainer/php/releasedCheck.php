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
	$url = $_REQUEST["url"];
	$task = $_REQUEST['task'];

	//get max and sent
	$sql = "SELECT * FROM released WHERE url=:url and task=:task ORDER BY ID DESC LIMIT 1";
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(":url"=>$url, ":task"=>$task));
	$result = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$max = $result['max'];
	$sent = $result['sent'];
	$ID = $result['ID'];

	if($sent < $max)
	{
		$sent = $sent + 1;
		$sql = "UPDATE released SET sent=:sent WHERE ID=:ID";
		$sth = $dbh->prepare($sql); 
		$sth->execute(array(":ID"=>$ID, ":sent"=>$sent));
		echo("true");
		
	}
	else {
		echo("false");
	}
}

?>
