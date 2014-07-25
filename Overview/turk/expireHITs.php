<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('max_execution_time', 10000);
set_time_limit ( 10000);

include("../../amtKeys.php");
include("../../config.php");
include("../../isSandbox.php");
include("../../getDB.php");
include 'turk_functions.php';


try {
    $dbh = getDatabaseHandle();
  } catch(PDOException $e) {
    echo $e->getMessage();
  }

function expireHit($hitId){
	global $dbh;
	turk_easyExpireHit($hitId);
	sleep(.25); //Give the HIT a moment to expire
	$mt = turk_easyDispose($hitId);
	sleep(.25); //Give the HIT a moment to dispose
}


  $sql = ("SELECT * from hits WHERE task = :task");
  $sth = $dbh->prepare($sql);
  $sth->execute(array(':task' => $_REQUEST['task']));
  $hits = $sth->fetchAll();

  foreach ($hits as $hit) {
  	$hitId = $hit['hit_Id'];
  	$hitInfo = turk50_getHit($hitId);
  	if(property_exists($hitInfo->HIT, "HITStatus")){
  		expireHit($hitId);
  		sleep(.25);
  		if($hitInfo->HIT->HITStatus == "Disposed"){
  			// expireHit($hitId);
  			$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
  			$sth = $dbh->prepare($sql);
  			$sth->execute(array(':hit_Id' => $hitId));
  		}
  		else if($hitInfo->HIT->HITStatus == "Reviewable"){
  			$sql = ("UPDATE hits SET assignable = 0 WHERE hit_Id = :hit_Id");
  			$sth = $dbh->prepare($sql);
  			$sth->execute(array(':hit_Id' => $hitId));
  		}
  	}
  	// else{
  	// 	$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
  	// 	$sth = $dbh->prepare($sql);
  	// 	$sth->execute(array(':hit_Id' => $hitId));
  	// }
  	sleep(1); //Don't overload mturk with getHit
  }

?>