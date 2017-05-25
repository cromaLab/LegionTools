<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');
include('../../Overview/turk/turk_functions.php');
include("../../amtKeys.php");
include("../../isSandbox.php");

// AWS SDK
require 'lib/aws-autoloader.php';

// Not sure why the following three methods exist - obsolete?
function generate_timestamp($time) {
    return gmdate("Y-m-d\TH:i:s\\Z", $time);
}
function hmac_sha1($key, $s) {
    return pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
        pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $s))));
}
function generate_signature($service, $operation, $timestamp, $secret_access_key) {
    $string_to_encode = $service . $operation . $timestamp;
    $hmac = hmac_sha1($secret_access_key, $string_to_encode);
    $signature = base64_encode($hmac);
    return $signature;
}

$AccessKey = $_REQUEST['accessKey']; 
$SecretKey = $_REQUEST['secretKey'];

// Try to get database handle
try {
    $dbh = getDatabaseHandle();
} catch( PDOException $e ) {
    echo $e->getMessage();
}

if( $dbh ) {

    // Get parameters from request data
	$id = $_REQUEST['id']; // Either AssignmentId or HITId
	$operation = $_REQUEST['operation'];

	// $assignmentInfo = turk_easyHitToAssn($hitId);
	// $assignmentId = $assignmentInfo["Assignment"]["AssignmentId"];

    // Branch into operation
    // Approve
	if($operation == "Approve"){
		$mt = turk_easyApprove($id); //AssignmentId
	}
    // Bonusing - note: this is new bonus, old code is below under "Bonus"
    else if($operation == "Bonusing"){
		//
    }
	// Unreject
    else if($operation == "Unreject"){

        $workerId = $_REQUEST['workerId'];

        // Initiate log file - can be called via: echo "\n\n$currentTime - Message";
//        $logFilePath = './debug.txt';
//        ob_start();
//        if (file_exists($logFilePath)) {
//            include($logFilePath);
//        }
//        $currentTime = date(DATE_RSS);

        // Obtain boolean whether sandbox is used
        $sandbox = $_REQUEST['useSandbox'];

        // Put credentials in array for API call
        $credentialsArray = array(
            "key" => $AccessKey,
            "secret" => $SecretKey,
        );

        // Request AWS SDK MTurk client either for sandbox or productive MTurk
        if($sandbox) {
            $client = new Aws\MTurk\MTurkClient([
                'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://mturk-requester-sandbox.us-east-1.amazonaws.com', // Use sandbox
                'credentials' => $credentialsArray
            ]);
        } else {
            $client = new Aws\MTurk\MTurkClient([
                'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://mturk-requester.us-east-1.amazonaws.com', // Use productive MTurk
                'credentials' => $credentialsArray
            ]);
        }

        $result = $client->approveAssignment([
            'AssignmentId' => $id,
            'WorkerId' => $workerId,
            'OverrideRejection' => true, // Override causes unreject
            'RequesterFeedback' => 'Amazing job.',
        ]);

        echo "\n\n$currentTime - Status: " . $result . " Sandbox: " . $sandbox;

        // Log output to file
//        $logFile = fopen($logFilePath, 'w');
//        fwrite($logFile, ob_get_contents());
//        fclose($logFile);
//        ob_end_flush();

        //return;
    }
    // Reject
	else if($operation == "Reject"){
		$mt = turk_easyReject($id); //AssignmentId
	}
	// Bonus
	else if($operation == "Bonus"){
		if(isset($_REQUEST['reason'])){
			$reason = $_REQUEST['reason'];
		}
		else $reason = "Did extra work.";
		$mt = turk_easyBonus($_REQUEST['workerId'], $id, $_REQUEST['amount'], $reason);
		// print_r($mt);
	}
	// Dispose
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
