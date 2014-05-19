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

	$id = $_REQUEST['id']; //either AssignmentId or HITId
	$operation = $_REQUEST['operation'];

	// $assignmentInfo = turk_easyHitToAssn($hitId);
	// $assignmentId = $assignmentInfo["Assignment"]["AssignmentId"];

	if($operation == "Approve"){
		$mt = turk_easyApprove($id); //AssignmentId
	}
	else if($operation == "Reject"){
		$mt = turk_easyReject($id); //AssignmentId
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

	echo $mt->FinalData['Request']['IsValid']; //True or False

}

?>
