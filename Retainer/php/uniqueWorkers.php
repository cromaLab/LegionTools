<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
error_log(".:: ".basename(__FILE__),0); // Debugging

include('_db.php');
include("../../amtKeys.php");
include("../../isSandbox.php");

// If requester is currently logged in to LTools
// This means requester is changing unique worker requirement from trigger.js
if(isset($_REQUEST['accessKey']) && isset($_REQUEST['secretKey'])) {
	$AccessKey = $_REQUEST['accessKey']; 
	$SecretKey = $_REQUEST['secretKey'];
}
// Otherwise, need to check unique worker qualification
// This means worker is on taskLanding.js (and requester requires unique workers)
else {
	if(isset($_REQUEST['dbName'])) {

		// Get access/secret from server store
		// Get path to sys tmp dir
		$tmp_path = sys_get_temp_dir();

		// sys_get_temp_dir() does not always add trailing slash
		// http://php.net/manual/en/function.sys-get-temp-dir.php
		if(substr($tmp_path, -1) != "/" ) $tmp_path .= "/";

		// Read from key store
		$key_store_path = $tmp_path."legionTools-tmpKeyStore";
		$fname = $key_store_path."/".$_REQUEST['dbName'];

		// Check for key store, then set keys
		if (file_exists($fname)) {
			$fh = fopen($fname, 'r');
			$line = fgets($fh);
			$line = explode(",",$line);
			fclose($fh);

			$AccessKey = $line[0];
			$SecretKey = $line[1];

			// error_log('$AccessKey:'.$AccessKey);	
			// error_log('$SecretKey:'.$SecretKey);
			// error_log('$SecretKeyLen:'.strlen($SecretKey));		
		}
	}
	else {
		error_log('ERROR in uniqueWorkers.php :: No AccessKey or SecretKey!');
	}
}

// Including this down here b/c turk_functions.php requires $AccessKey and $SecretKey
include('../../Overview/turk/turk_functions.php');

try {
	$dbh = getDatabaseHandle();
} catch( PDOException $e ) {
	echo $e->getMessage();
}

if( $dbh ) {

	$task = $_REQUEST['task'];
	error_log("Task name: ".$task);

	if(isset($_REQUEST['useSandbox'])){
		$useSandbox = filter_var($_REQUEST['useSandbox'],FILTER_VALIDATE_BOOLEAN);
		if($useSandbox){
			$dbCol = "noRepeatQualIdSandbox";
			$SANDBOX = true;
		}
		else{
			$dbCol = "noRepeatQualIdLive";
			$SANDBOX = false;
		}
	}
	else if(isset($_REQUEST['turkSubmitTo'])){
		$turkSubmitTo = $_REQUEST['turkSubmitTo'];
		if (strpos($turkSubmitTo, 'workersandbox') !== FALSE){
			$dbCol = "noRepeatQualIdSandbox";
		    $SANDBOX = true;
		}
		else{
			$dbCol = "noRepeatQualIdLive";
		    $SANDBOX = false;
		}
	}

	$sql = ("SELECT * FROM retainer WHERE task = :task");
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(":task"=>$task));
	$result = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$noRepeatQualId = $result[$dbCol];

	error_log('$result: '.print_r($result,true),0);	
	error_log('$noRepeatQualId: '.print_r($noRepeatQualId,true),0);

	// If the no-repeat qualification does not yet exist (or resetting it), then (re)create it
	if(($noRepeatQualId == null || $noRepeatQualId == "") || isset($_REQUEST['reset'])){
		$qual = turk50_createQualificationType(date("Ymd-His").generateRandomString(), "This qualification is for people who have worked for me on this task(".$_REQUEST['task'].") before.", "Worked for me before", $SANDBOX);
		error_log("Qualification: ".print_r($qual,true),0);
		
		$noRepeatQualId = $qual->QualificationType->QualificationTypeId;		
		error_log(date("Ymd-His").":Task(".$task.") generated one qualifitcation type(".$noRepeatQualId.") from uniqueWorkers.php. (Sandbox:".$SANDBOX.",reset:". isset($_REQUEST['reset']).")\n",0);
		
		if($SANDBOX) $sql = ("UPDATE retainer set noRepeatQualIdSandbox = :noRepeatQualId WHERE task = :task");
		else $sql = ("UPDATE retainer set noRepeatQualIdLive = :noRepeatQualId WHERE task = :task");
		$sth = $dbh->prepare($sql); 
		$sth->execute(array(":task"=>$task, ":noRepeatQualId"=>$noRepeatQualId));

        echo "New qualification(".$noRepeatQualId.") is generated in ";
        if($SANDBOX) echo "SANDBOX\n";
        else echo "LIVE MTURK\n";

	} else {
		// echo "The experiment will use qualification (".$noRepeatQualId.")";
	}

	if(isset($_REQUEST['assignQualification']) && $_REQUEST['assignQualification'] = "true"){
        $mt = turk50_assignQualification($_REQUEST['workerId'], $noRepeatQualId, $SANDBOX);
        error_log("uniqueWorkers.php :: assignQualification: ".$_REQUEST['assignQualification']."WorkerID: ".$_REQUEST['workerId'],0);
	}
	if(isset($_REQUEST['revokeQualification']) && $_REQUEST['revokeQualification'] = "true"){
        $mt = turk50_revokeQualification($_REQUEST['workerId'], $noRepeatQualId, $SANDBOX);
        error_log("uniqueWorkers.php :: revokeQualification: ".$_REQUEST['revokeQualification']."WorkerID: ".$_REQUEST['workerId'],0);
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
