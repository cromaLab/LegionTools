<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
error_log(".:: ".basename(__FILE__),0); // Debugging

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


if( $dbh ) {


	$reviewableHits = turk50_getAllReviewableHits();

	print_r($reviewableHits);

	$hitsFromTurk = array();
	foreach($reviewableHits as $hit){
		array_push($hitsFromTurk, $hit->HITId);
	}

	// if(is_array($hitsFromTurk)){
	// 	foreach($hitsForTask as $hit){
	// 		if(in_array($hit["hit_Id"], $hitsFromTurk)){
	// 			array_push($resultHitIds, $hit["hit_Id"]);
	// 		}
	// 	}
	// }

	foreach($hitsFromTurk as $hitId){
		// print_r(turk_easyHitToAssn($hitId));
		$hitInfo = turk_easyHitToAssn($hitId);
		print_r($hitInfo);
		if($hitInfo["TotalNumResults"] == 1){
			turk_easyApprove($hitInfo["Assignment"]["AssignmentId"]);
		}
		else if($hitInfo["TotalNumResults"] > 1){
			for($i = 0; $i < $hitInfo["TotalNumResults"]; $i++){
				$assignmentId = $hitInfo["Assignment"][$i]["AssignmentId"];
				turk_easyApprove($assignmentId);
			}
		}		
		echo "</br></br>";
		$mt = turk_easyDispose($hitId);
		print_r($mt);
		sleep(.1);
		if($mt->FinalData["Request"]["IsValid"] == "True"){
			$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
			$sth = $dbh->prepare($sql);
			$sth->execute(array(':hit_Id' => $hitId));
		}
	}
	
	// echo json_encode($resultHits);

}

?>
