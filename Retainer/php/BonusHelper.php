<?php

// AWS SDK
require 'lib/aws-autoloader.php';

// executeBonus: Bonus person
// OBSOLETE Methods:
//   extractBonus: Extracts bonus from existing Answer
//   fetchBonus:   Fetches bonus from MTurk if it was added as data field "bonusSuggestion" to result

if($_POST['action'] == "extractBonus") {

// Obtain input parameter 'param' and load it as XML
    $resultXml = simplexml_load_string($_REQUEST['param']);
// Parse out bonus suggestion
    if ($resultXml->Answer[1]->QuestionIdentifier == "bonusSuggestion") {
        $value = $resultXml->Answer[1]->FreeText;
        //echo floatval($value);
    }

// Build array with return values
    $returnValue = array
    (
        'bonusAmount' => floatval($value),
        'message' => 'normal'
    );

// Return return array as JSON
    echo json_encode($returnValue);

} else if ($_POST['action'] == "executeBonus") {

    // If we wanted to log payments
//    $logFilePath = './debug.txt';
//    ob_start();
//    if (file_exists($logFilePath)) {
//        include($logFilePath);
//    }
//    $currentTime = date(DATE_RSS);
//    echo "\n\n$currentTime - Fake service was called.";
//    $logFile = fopen($logFilePath, 'w');
//    fwrite($logFile, ob_get_contents());
//    fclose($logFile);
//    ob_end_flush();

    // Obtain boolean whether sandbox is used
    $sandbox = $_REQUEST['useSandbox'];

    // Put credentials in array for API call
    $credentialsArray = array(
        "key" => $_REQUEST['accessKey'],
        "secret" => $_REQUEST['secretKey'],
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

    // Actual bonus call to MTurk
    $result = $client->sendBonus([
        'AssignmentId' => $_REQUEST['assignmentId'], // REQUIRED
        'WorkerId' => $_REQUEST['workerId'], // REQUIRED
        'BonusAmount' => $_REQUEST['bonusAmount'], // REQUIRED
        'Reason' => $_REQUEST['bonusReason']
    ]);

} else if ($_POST['action'] == "fetchBonus") {

    // Obtain boolean whether sandbox is used
    $sandbox = $_REQUEST['useSandbox'];

    // Put credentials in array for API call
    $credentialsArray = array(
        "key" => $_REQUEST['accessKey'],
        "secret" => $_REQUEST['secretKey'],
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

    $resultArray = $client->getAssignment(["AssignmentId" => $_REQUEST['assignmentId']]);
    $resultAnswer = $resultArray['Assignment']['Answer'];
    //echo ($resultArray['Assignment']['AssignmentStatus']);

    // Parse out bonus suggestion from XML
    $bonusAmount = 0.00;
    $resultXml = simplexml_load_string($resultAnswer);

    foreach ($resultXml->Answer as $resultEntry) {
        if ($resultEntry->QuestionIdentifier == "bonusSuggestion") {
            $value = $resultXml->Answer[1]->FreeText;
            $bonusAmount = floatval($value);
        }
    }

    echo $bonusAmount;
}