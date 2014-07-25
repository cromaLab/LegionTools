<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');
include('../../Overview/turk/turk_functions.php');
include("../../amtKeys.php");
include("../../isSandbox.php");

  try {
      $dbh = getDatabaseHandle();
  } catch( PDOException $e ) {
      echo $e->getMessage();
  }


if( $dbh ) {

	$rewardTotal = 0;
	$count = 0;

	$sql = ("SELECT * from hits WHERE task = :task");
	$sth = $dbh->prepare($sql);
	$sth->execute(array(':task' => $_REQUEST['task']));
	$hits = $sth->fetchAll();

	foreach ($hits as $hit) {
		$hitId = $hit['hit_Id'];
		$hitInfo = turk50_getHit($hitId);
		if(property_exists($hitInfo->HIT, "HITStatus")){
			if($hitInfo->HIT->HITStatus == "Assignable"){
				print_r($hitInfo);
				$rewardTotal += $hitInfo->HIT->Reward->Amount;
				$count++;
			}
		}
		sleep(1); //Don't overload mturk with getHit
	}
	
	echo $count . "," . $rewardTotal;

}

?>
