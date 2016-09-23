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


if( $dbh ) {

	$id = $_REQUEST['id']; //either AssignmentId or HITId
	$operation = $_REQUEST['operation'];
	// echo $operation;

	// $assignmentInfo = turk_easyHitToAssn($hitId);
	// $assignmentId = $assignmentInfo["Assignment"]["AssignmentId"];

	if($operation == "Approve"){
		$mt = turk_easyApprove($id); //AssignmentId
	}
	else if($operation == "Reject"){
		$mt = turk_easyReject($id); //AssignmentId
	}
	else if($operation == "Bonus"){
		if(isset($_REQUEST['reason'])){
			$reason = $_REQUEST['reason'];
		}
		else $reason = "Did extra work.";
		$mt = turk_easyBonus($_REQUEST['workerId'], $id, $_REQUEST['amount'], $reason);
		// print_r($mt);
	}
	else if($operation == "Dispose"){
		$mt = turk_easyDispose($id); //HITId

		// Remove from DB
		echo $mt->FinalData['Request']['IsValid'];
		if($mt->FinalData['Request']['IsValid']){
			echo "delete from db";
			$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
			$sth = $dbh->prepare($sql); 
			$sth->execute(array(':hit_Id' => $id));
		}
	}

	if($mt->FinalData['Request']['IsValid'] == "True") echo "True";
	else print_r($mt->ArrayData);
	 //True or False
	// echo $mt->FinalData;


}

?>
