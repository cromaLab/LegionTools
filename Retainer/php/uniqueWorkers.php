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

	$task = $_REQUEST['task'];

	$sql = ("SELECT * FROM retainer WHERE task = :task");
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(":task"=>$task));
	$result = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$noRepeatQualId = $result['noRepeatQualId'];

	if($noRepeatQualId == null || $noRepeatQualId == "" || isset($_REQUEST['reset']) ){
		$qual = turk50_createQualificationType(generateRandomString(), "This qualification is for people who have worked for me on this task before.", "Worked for me before");
		// print_r($qual);
		$noRepeatQualId = $qual->QualificationType->QualificationTypeId;

		$sql = ("UPDATE retainer set noRepeatQualId = :noRepeatQualId WHERE task = :task");
		$sth = $dbh->prepare($sql); 
		$sth->execute(array(":task"=>$task, ":noRepeatQualId"=>$noRepeatQualId));
	}

	if(isset($_REQUEST['assignQualification']) && $_REQUEST['assignQualification'] = "true"){
		$mt = turk50_assignQualification($_REQUEST['workerId'], $noRepeatQualId);
		echo $mt;
	}

}

function generateRandomString($length = 50) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

?>
