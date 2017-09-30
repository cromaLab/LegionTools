<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
error_log(".:: ".basename(__FILE__),0); // Debugging

include('_db.php');
include("../../amtKeys.php");
include("../../isSandbox.php");

include("../includes/vars.php");

// Logger - exposes logDebug("message") to log to REST backend found in extras/logserver.py
require '../../extras/ImprovisedLogger.php';


logDebug('-------- New Bonus DB Call --------');
logDebug($API_BASE_URL);

// .:: "Authentication"
// Check whether DB with hash of secretKey and accessKey exists in order to "authenticate"
// (if not, no DB handle is requested and thus rest is skipped and request effectively rejected)
$AccessKey = $_REQUEST['accessKey'];
$SecretKey = $_REQUEST['secretKey'];
$authenticationCode = hash("sha256", hash('sha256', $AccessKey) . hash('sha256', $SecretKey));
if(file_exists('../../db/' . $authenticationCode . '.db')) {
    logDebug('DB exists for hash of accessKey and secretKey => Request OK, serving.');
    try {
        $dbh = getDatabaseHandle();
    } catch( PDOException $e ) {
        echo $e->getMessage();
    }
} else {
    logDebug('ERROR: DB DOES NOT EXIST for hash of accessKey and secretKey => REJECTING REQUEST');
}

// TODO Details
// - TODO Investigate proper authentication (https://stackoverflow.com/questions/5507234/how-to-use-basic-auth-with-jquery-and-ajax ?)
// - TODO Test Retainer mode again (maybe also document testing it), remove of callX?
// - TODO Need global variables $API_BASE_URL
// - TODO Put common code into functions?
// - TODO Write method that checks list of parameters and accurately reports errors
// - TODO Make sure incorrect API call does not stop program
// - TODO Remove BonusDbHelper, BonusHelper and credentials from client.php

// Note: If 3rd party creates many bonus entries we might store dead entries that are potentially never deleted

if( $dbh ) {

    // Parameters
    $workerId = '123';     // $_REQUEST['workerId']
    $assignmentId = '456'; // $_REQUEST['assignmentId']
    $bonusAmount = 65;     // $_REQUEST['bonusAmount']

    $command = 'getBonus'; // $_REQUEST['command']
    // setBonus:    Insert or update when 3rd party sends workerId, assignmentId & bonusAmount
    // getBonus:    Select bonus for assignmentId and workerId (0.00 if not known)
    // deleteBonus: Delete entry (when entry is disposed)

    if($command === 'setBonus') {

        $sthSelect = $dbh->prepare("SELECT bonusAmount FROM bonusLog WHERE workerId = :workerId AND assignmentId = :assignmentId AND authHash = :hash");
        $sthSelect->execute(array(':workerId' => $workerId, ':assignmentId' => $assignmentId, ':hash' => $authenticationCode));
        if(sizeof($sthSelect->fetchAll())>0) {
            // Update existing bonus entry
            $sthUpdate = $dbh->prepare("UPDATE bonusLog SET bonusAmount = :bonusAmount WHERE workerId = :workerId AND assignmentId = :assignmentId AND authHash = :hash");
            $sthUpdate->execute(array(':workerId' => $workerId, ':assignmentId' => $assignmentId, ':hash' => $authenticationCode, 'bonusAmount' => $bonusAmount));
        } else {
            // Timestamp so zombie entries can be removed manually should they come into existence
            $currentTime = date(DATE_RSS);
            // Insert new bonus entry
            $sthInsert = $dbh->prepare("INSERT INTO bonusLog (workerId, assignmentId, bonusAmount, authHash, creationTime) VALUES (:workerId, :assignmentId, :bonusAmount, :hash, :creationTime)");
            $sthInsert->execute(array(':workerId' => $workerId, ':assignmentId' => $assignmentId, ':hash' => $authenticationCode, 'bonusAmount' => $bonusAmount, ':creationTime' => $currentTime));
        }

    } else if($command === 'getBonus') {

        $sthSelect = $dbh->prepare("SELECT bonusAmount FROM bonusLog WHERE workerId = :workerId AND assignmentId = :assignmentId AND authHash = :hash");
        $sthSelect->execute(array(':workerId' => $workerId, ':assignmentId' => $assignmentId, ':hash' => $authenticationCode));
        $bonusAmount = $sthSelect->fetchAll();

        $returnValue = 0; // Default value: 0 (bonus not found)
        foreach ($bonusAmount as $entry) {
            if(is_array($entry)){
                if(sizeof($entry)>0) {
                    $returnValue = $entry[0];
                    break;
                    // Could iterate through all results, but in our setting we have/need only one match
                    //foreach ($entry as $wha) {
                    //   logDebug($wha);
                    //}
                }
            }
        }
        logDebug($returnValue);
        echo $returnValue;

    } else if($command === 'deleteBonus') {

        $sthDelete = $dbh->prepare("DELETE FROM bonusLog WHERE workerId = :workerId AND assignmentId = :assignmentId AND authHash = :hash");
        $sthDelete->execute(array(':workerId' => $workerId, ':assignmentId' => $assignmentId, ':hash' => $authenticationCode));

    } else {
        // Command called is not available - could perform error handling here
        logDebug('Error: Requested command not found');
    }

}
