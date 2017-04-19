<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');
include('../../Overview/turk/turk_functions.php');
include("../../amtKeys.php");
include("../../isSandbox.php");

$AccessKey = $_REQUEST['accessKey']; 
$SecretKey = $_REQUEST['secretKey']; 

try {
      $dbh = getDatabaseHandle();
  } catch( PDOException $e ) {
      echo $e->getMessage();
  }

// Retrieve HITs from database (for specific task and either for Sandbox or productive MTurk mode)
if( $dbh ) {

	$task = $_REQUEST['task'];

	$resultHitIds = array();
	$resultHits = array();

	$sql = "SELECT hit_Id FROM hits WHERE task = :task AND sandbox = :sandbox";
	# $sql = "SELECT hit_Id FROM hits WHERE task = :task";
    $sth = $dbh->prepare($sql);

    //echo "sandbox is ";
    //echo "$SANDBOX"; 
    //echo "...\n"; 

	$sth->execute(array(':task' => $task, ':sandbox' => $SANDBOX));
	# $sth->execute(array(':task' => $task));
    
    $hitsForTask = $sth->fetchAll();
    //echo "\nhitsForTask --> \n"; 
    //print_r($hitsForTask);

	$reviewableHits = turk50_getAllReviewableHits();
    if(!is_array($reviewableHits)) {
        $reviewableHits = array();
    }

	$hitsFromTurk = array();
	foreach($reviewableHits as $hit){
		array_push($hitsFromTurk, $hit->HITId);
	}

    //echo "\nhitsFromTurk --> \n"; 
    //print_r($hitsFromTurk); 

	if(is_array($hitsFromTurk)){
		foreach($hitsForTask as $hit){
			if(in_array($hit["hit_Id"], $hitsFromTurk)){
				array_push($resultHitIds, $hit["hit_Id"]);
			}
		}
	}

    //echo "\nresultHitIds --> \n";
    //print_r($resultHitIds);

	foreach($resultHitIds as $hitId){
		//print_r(turk_easyHitToAssn($hitId));
		//echo "</br></br>";
		$hitInfo = turk_easyHitToAssn($hitId);
		if($hitInfo["TotalNumResults"] <= 0){
			$mt = turk_easyDispose($hitId);
			// sleep(.25);
			if($mt->FinalData["Request"]["IsValid"] == "True"){
				$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
				$sth = $dbh->prepare($sql);
			//	$sth->execute(array(':hit_Id' => $hitId));
			}
		}
		else array_push($resultHits, $hitInfo);
	}
	
    //echo "\njson encoding resultHits --> \n";
    echo json_encode($resultHits);

}
else {
    echo "FAILED TO ACQUIRE DB HANDLE!";
}

?>
