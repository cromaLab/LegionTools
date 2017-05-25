<?php

// FIXME KEYS - REMEMBER TO REMOVE THIS BEFORE SHARING !!!
$MTURK_ACCESS_KEY = '';
$MTURK_SECRET_KEY = '';

// Retainer - URLs and Parameters
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/preparation';
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/prep2';
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/call';
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/call2';
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/query';
//$api_request_parameters = array(
//    'mode' => 'retainer',
//    'accessKey' => $MTURK_ACCESS_KEY,
//    'secretKey' => $MTURK_SECRET_KEY,
//    'numFire' => 1,
//    'task' => 'Experiment',
//    'useSandbox' => true,
//    'requireUniqueWorkers' => false,
//    'instrPageUrl' => 'https://legionpowered.net/instructions/robocrowd/task.html',
//    'tutPageUrl' => 'https://legionpowered.net/FP/FakeTutorial.html',
//    'waitPageUrl' => 'https://legionpowered.net/instructions/robocrowd/waiting.html',
//    'link' => 'https://legionpowered.net/FP/Test/testHIT.html' // fireToURL
//);

// Bonus - URLs and Parameters
$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/bonus/set';  // Tested
//$api_request_url = 'http://localhost:2345/Retainer/api/api.php/bonus/get';  // Tested
//$api_request_url = 'http://localhost:2345/Retainer/api/api.php/bonus/delete'; // Tested
$api_request_parameters = array(
    'mode' => 'bonus',
    'accessKey' => $MTURK_ACCESS_KEY,
    'secretKey' => $MTURK_SECRET_KEY,
    'workerId' => 'A2PHXJFXOHM976', // Change this to bonus
    'assignmentId' => '33CID57105IEXXDF4H0RIZCGJWOL3C', // Change this to bonus
    'bonusAmount' => 0.05
);

// MTurk - URLs and Parameters
//$api_request_url = 'http://localhost:2345/Retainer/api/api.php/mturk/sendBonus'; // Tested
//$api_request_url = 'http://localhost:2345/Retainer/api/api.php/mturk/listAssignmentsForHit'; // Tested
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/mturk/unreject';
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/mturk/postHit';
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/mturk/extractSuggestedBonusFromResult';
//$api_request_parameters = array(
//    'mode' => 'bonus',
//    'accessKey' => $MTURK_ACCESS_KEY,
//    'secretKey' => $MTURK_SECRET_KEY,
//    'workerId' => 'A2PHXJFXOHM976',
//    'assignmentId' => '3WETL7AQWUX0V8AUCUKK1AGJX6J533',
//    'bonusAmount' => 0.03,
//    'bonusReason' => 'Reason is an API Test',
//    'hitId' => '3OQQD2WO8IVAKC7FTCYD10P7PIVI3Z',
//    'assignmentStatus' => 'Approved',
//    'assignmentDuration' => 60, // in seconds
//    'keywords' => 'keyword1,keyword2',
//    'description' => 'This is a description.',
//    'hitUrl' => 'https://legionpowered.net/FP/Test/testHIT.html',
//    'title' => 'Great Title',
//    'useSandbox' => true
//);


// Method to use - GET, POST, PUT or DELETE
// NOTE: At time of implementation, POST was used exclusively (all others placeholders)
$method_name = 'POST';

// Authenticate user
//$api_request_parameters = array(
//    'numFire' => 1337,
//    'task' => "Test",
//    'fireToURL' => 'Test2'
//);

// Execute actual request

$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD => $MTURK_ACCESS_KEY . ':' . $MTURK_SECRET_KEY
));

if ($method_name == 'GET')
{
    // NOP
}

if ($method_name == 'POST')
{
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_request_parameters));
}

if ($method_name == 'PUT')
{
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_request_parameters));
}

if ($method_name == 'DELETE')
{
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_request_parameters));
}

// Preferred response content type (application/json, application/xml, text/html, text/plain)
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

curl_setopt($ch, CURLOPT_URL, $api_request_url);

curl_setopt($ch, CURLOPT_HEADER, TRUE);

// If using HTTPS
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Execute
$api_response = curl_exec($ch);

$api_response_info = curl_getinfo($ch);

curl_close($ch);

// Extract response header and body
$api_response_header = trim(substr($api_response, 0, $api_response_info['header_size']));
$api_response_body = substr($api_response, $api_response_info['header_size']);

// Response HTTP Status Code
echo $api_response_info['http_code'] . "\n";

// Response Header
echo $api_response_header  .  "\n";

// Response Body
echo $api_response_body;
