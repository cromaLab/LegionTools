<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');
include("../../amtKeys.php");
include("../../isSandbox.php");
if(isset($_REQUEST['accessKey']) && isset($_REQUEST['secretKey'])){
    $AccessKey = $_REQUEST['accessKey']; 
    $SecretKey = $_REQUEST['secretKey'];
}
else{
    if(isset($_REQUEST['dbName']) && $_REQUEST['dbName']=='fde3d30df56968f4d13c1bb7eef8e5c805a3c2adccd60ff54e8c1897d297a1df')
    {
        $myfile = fopen("../../../../cromalab-mturk", "r") or die("Unable to open file!");
        $keys = explode(",",fread($myfile,filesize("../../../../cromalab-mturk")));
        $AccessKey = $keys[0];
        $SecretKey = substr($keys[1], 0, 40);// AWS secret key is 40 digits. This is to get rid of newline feed at the end. 
        fclose($myfile);
    }
    else{
        error_log("ERROR: no access key, no secret key and no db handle", 3,"../../qualification-error.log");
        echo "ERROR: no access key, no secret key and no db handle";
    }
}
// waiting for accessskey from the previous if statement. 
include('../../Overview/turk/turk_functions.php');

try {
    $dbh = getDatabaseHandle();
} catch( PDOException $e ) {
    echo $e->getMessage();
}


if( $dbh ) {

    $task = $_REQUEST['task'];
	if(isset($_REQUEST['useSandbox'])){
        $useSandbox = filter_var ($_REQUEST['useSandbox'], FILTER_VALIDATE_BOOLEAN);
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
	if(($noRepeatQualId == null || $noRepeatQualId == "") || isset($_REQUEST['reset'])){
		$qual = turk50_createQualificationType(date("Ymd-His").generateRandomString(), "This qualification is for people who have worked for me on this task(".$task.") before.", "Worked for me before", $SANDBOX);
        
        $noRepeatQualId = $qual->QualificationType->QualificationTypeId;
        error_log("log:".date("Ymd-His").":Task(".$task.") generated one qualifitcation type(".$noRepeatQualId.") from uniqueWorkers.php. (Sandbox:".$SANDBOX.",reset:". isset($_REQUEST['reset']).")\n", 3, "../../qualification-error.log");

		if($SANDBOX) $sql = ("UPDATE retainer set noRepeatQualIdSandbox = :noRepeatQualId WHERE task = :task");
		else $sql = ("UPDATE retainer set noRepeatQualIdLive = :noRepeatQualId WHERE task = :task");
		$sth = $dbh->prepare($sql); 
        $sth->execute(array(":task"=>$task, ":noRepeatQualId"=>$noRepeatQualId));
        
        echo "new qualification(".$noRepeatQualId.") is generated in ";
        if($SANDBOX) echo "SANDBOX\n";
        else echo "LIVE MTURK\n";
    }else
    {
        echo "The experiment will use the qualification (".$noRepeatQualId.").";
    }
    
	if(isset($_REQUEST['assignQualification']) && $_REQUEST['assignQualification'] = "true"){
        $mt = turk50_assignQualification($_REQUEST['workerId'], $noRepeatQualId, $SANDBOX);
        error_log("assignQualification:".$_REQUEST['assignQualification']."\n",3,"../../qualification-error.log");
	}
	if(isset($_REQUEST['revokeQualification']) && $_REQUEST['revokeQualification'] = "true"){
        $mt = turk50_revokeQualification($_REQUEST['workerId'], $noRepeatQualId, $SANDBOX);
        error_log("revkoeQualification:".$_REQUEST['revokeQualification']."\n",3,"../../qualification-error.log");
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
