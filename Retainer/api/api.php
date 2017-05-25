<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include("../php/_db.php");

// AWS SDK
require '../php/lib/aws-autoloader.php';

// Logger - exposes logDebug("message") to log to REST backend found in extras/logserver.py
require '../../extras/ImprovisedLogger.php';

/**
 * creates a client object for AWS either for MTurk's sandbox or productive platform
 * @param $sandbox Whether to use sandbox (boolean, true => sandbox)
 * @param $accessKey AWS MTurk IAM Access Key
 * @param $secretKey AWS MTurk IAM Secret Key
 * @return \Aws\MTurk\MTurkClient Client object
 */
function createClient($sandbox, $accessKey, $secretKey) {

    // Put credentials in array for API call
    $credentialsArray = array(
        "key" => $accessKey,
        "secret" => $secretKey,
    );

    // Request AWS SDK MTurk client either for sandbox or productive MTurk
    if ($sandbox) {
        $client = new Aws\MTurk\MTurkClient([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => 'https://mturk-requester-sandbox.us-east-1.amazonaws.com', // Use sandbox
            'credentials' => $credentialsArray
        ]);
    } else {
        $client = new Aws\MTurk\MTurkClient([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => 'https://mturk-requester.us-east-1.amazonaws.com', // Use productive MTurk
            'credentials' => $credentialsArray
        ]);
    }
    return $client;
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/')); // http://localhost:4041/api.php/uno/dos => ["uno","dos"]
//$input = json_decode(file_get_contents('php://input'),true);
//$param = $_REQUEST['api_key'];

//if ($_REQUEST['api_key'] != 'uI96oLP297k813b' || $_REQUEST['token'] != 'asj3klj51kHJqKHs27PaHImUuH9183DLXWi') {
//    http_response_code(401); // 403 would mean authentication worked, but user is not permitted to access resource
//    die("Authorization failed. Please check your API key and token.");
//}


// Can authenticate by comparing hash (AUTH_USER is accessKey, AUTH_PW secretKey)
//$tableName = hash("sha256", hash('sha256', $_SERVER['PHP_AUTH_USER']) . hash('sha256', $_SERVER['PHP_AUTH_PW']));
//if($tableName!='<hashToCompareTo>') {
//    http_response_code(401); // 403 would mean authentication worked, but user is not permitted to access resource
//    die("Authorization failed. Please check your API key and token.");
//}

switch ($method) {
    case 'GET':

        http_response_code(404);
        die("Error: GET not supported.");

        // .:: http://localhost:2345/Retainer/api/api.php/listReviewableHits
//        if ($request[0]==='listReviewableHits') {
//            if (isset($_REQUEST['useSandbox'])) {
//                // Obtain AWS client object
//                $client = createClient($_REQUEST['useSandbox'], $_REQUEST['accessKey'], $_REQUEST['secretKey']);
//            } else {
//                http_response_code(422); // 422: Unprocessable entity
//                die("Not all required parameters specified to create MTurk client.");
//            }
//            $result = $client->listReviewableHITs();
//            echo $result;
//        } else {
//            http_response_code(404);
//            die("Error: Method not supported.");
//        }

        break;

    case 'POST':

        logDebug('POST called.');

        if(count($request)!=2) {
            http_response_code(404);
            die("Incorrect path length.");
        }

        /**
         * 3 methods:
         * - prepare(int numFire, String task)
         * - call(int numFire, String fireToURL, String task)
         * - query_available(String task)
         */
        if($request[0]==='retainer' && $request[1]==='preparation') {
            if( isset($_REQUEST['numFire']) && isset($_REQUEST['task']) && isset($_REQUEST['useSandbox']) &&
                isset($_REQUEST['requireUniqueWorkers']) && isset($_REQUEST['tutPageUrl']) &&
                isset($_REQUEST['waitPageUrl']) && isset($_REQUEST['instrPageUrl']) ) {

                echo '<h1>Preparation</h1>';

                $data = array(
                    //'numFire' => $_REQUEST['numFire'],
                    'task' => $_REQUEST['task'],
                    'useSandbox' => $_REQUEST['useSandbox'],
                    'requireUniqueWorkers' => $_REQUEST['requireUniqueWorkers'],
                    'accessKey' => $_SERVER['PHP_AUTH_USER'],
                    'secretKey' => $_SERVER['PHP_AUTH_PW'],
                    'mode' => 'retainer',
                    'tutPageUrl' => $_REQUEST['tutPageUrl'],
                    'waitPageUrl' => $_REQUEST['waitPageUrl'],
                    'instrPageUrl' => $_REQUEST['instrPageUrl']
                );

                // Could add type checking like this
//                if (!is_string($task) || !is_numeric($num_fire)){
//                    http_response_code(400);
//                    die("Incorrect parameters for preparation.");
//                }

                // Make actual call (preparation)
                // data: {task: $("#taskSession").val(), useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(), mode: "retainer", requireUniqueWorkers: $("#requireUniqueWorkers").is(':checked'), tutPageUrl: tutPageUrl, waitPageUrl: waitPageUrl, instrPageUrl: instrPageUrl}

//                $data = array('task'=>'', 'useSandbox'=>'', 'accessKey'=>'', 'secretKey'=>'', 'mode'=>'retainer',
//                    'requireUniqueWorkers'=>'', 'tutPageUrl'=>'', ''=>'', 'waitPageUrl'=>'', 'instrPageUrl'=>'');

                $queryString = http_build_query($data);

                $api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/php/startRecruiting.php';
                echo $api_request_url;
//
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_USERPWD => $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']
                ));
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_URL, $api_request_url);
                $result = curl_exec($ch);
                $api_response_info = curl_getinfo($ch);
                curl_close($ch);
                echo $result;

                // $api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/fakeService.php?';
                // $ch = curl_init();
                // curl_setopt_array($ch, array(
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_USERPWD => $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']
                // ));
                // curl_setopt($ch, CURLOPT_POST, TRUE);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                // curl_setopt($ch, CURLOPT_URL, $api_request_url);
                // $result = curl_exec($ch);
                // $api_response_info = curl_getinfo($ch);
                // curl_close($ch);
                // echo $result;
            } else {
                http_response_code(400);
                die("Incorrect parameters for preparation.");
            }

        } else if($request[0]==='retainer' && $request[1]==='prep2') {

            echo '<h1>Prep2</h1>';

            $data = array(
                //'numFire' => $_REQUEST['numFire'],
                'task' => $_REQUEST['task'],
                'useSandbox' => $_REQUEST['useSandbox'],
                'requireUniqueWorkers' => $_REQUEST['requireUniqueWorkers'],
                'accessKey' => $_SERVER['PHP_AUTH_USER'],
                'secretKey' => $_SERVER['PHP_AUTH_PW'],
                'mode' => 'retainer',
                'tutPageUrl' => $_REQUEST['tutPageUrl'],
                'waitPageUrl' => $_REQUEST['waitPageUrl'],
                'instrPageUrl' => $_REQUEST['instrPageUrl']
            );

            $api_request_url = 'https://legionpowered.net/FP/LegionTools/Overview/turk/getAnswers.php';
            echo $api_request_url;
//
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD => $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']
            ));
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_URL, $api_request_url);
            $result = curl_exec($ch);
            $api_response_info = curl_getinfo($ch);
            curl_close($ch);
            echo $result;


            //
            // TODO CallX might be obsolete, can potentially be removed
            //
        }
        else if($request[0]==='retainer' && $request[1]==='callX') {
            // OBSOLETE ?
//            if( isset($_REQUEST['numFire']) && isset($_REQUEST['task']) && isset($_REQUEST['link']) ) {
//
//                echo '<h1>Call</h1>';
//
//                $data = array(
//                    'max' => $_REQUEST['numFire'],
//                    'link' => $_REQUEST['link'],
//                    'task' => $_REQUEST['task'],
//                    'accessKey' => $_SERVER['PHP_AUTH_USER'],
//                    'secretKey' => $_SERVER['PHP_AUTH_PW'],
//                );
//
//                // Could add type checking like this
////                if (!is_string($task) || !is_string($fire_to_url) || !is_numeric($num_fire)){
////                    http_response_code(400);
////                    die("Incorrect parameters for preparation.");
////                }
//
//                // Need 2 calls: One to Retainer/php/setFire.php and on success call Retainer/php/updateReleased.php
//                $queryString = http_build_query($data);
//                $api_request_url = 'http://localhost:8080/Retainer/php/startRecruiting.php?' . $queryString;
//                echo $api_request_url;
//
//                $ch = curl_init($api_request_url);
//                $result = curl_exec($ch);
//
//                curl_close($ch);
//
//                //Retainer/php/setFire.php
////                $.ajax({
////            url: retainerLocation + "php/setFire.php",
////            type: "POST",
////            async: true,
////            data: {url: link, task: task, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
////            dataType: "text",
////            success: function(d) {
////                    $.ajax({
////                    url: retainerLocation + "php/updateReleased.php",
////                    type: "POST",
////                    async: true,
////                    data: {url: link, max: numFire, task: task, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
////                    dataType: "text",
////                    success: function(d) {
////
////                    },
////                    fail: function() {
////                        statusbar.innerHTML = "Sending number of workers failed";
////                    }
////                });
////            },
//
//                // Make actual call
//
//
//            } else {
//                http_response_code(400);
//                die("Incorrect parameters for preparation.");
//            }
        }
        else if($request[0]==='retainer' && $request[1]==='call') {

            echo '<h1>Call</h1>';

            $data = array(
                //'numFire' => $_REQUEST['numFire'],
                'url' => $_REQUEST['link'],
                'task' => $_REQUEST['task'],
                'useSandbox' => $_REQUEST['useSandbox'],
                'requireUniqueWorkers' => $_REQUEST['requireUniqueWorkers'],
                'accessKey' => $_SERVER['PHP_AUTH_USER'],
                'secretKey' => $_SERVER['PHP_AUTH_PW'],
                'mode' => 'retainer',
                'tutPageUrl' => $_REQUEST['tutPageUrl'],
                'waitPageUrl' => $_REQUEST['waitPageUrl'],
                'instrPageUrl' => $_REQUEST['instrPageUrl']
            );

            $api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/php/setFire.php';
            echo $api_request_url;
//
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD => $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']
            ));
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_URL, $api_request_url);
            $result = curl_exec($ch);
            $api_response_info = curl_getinfo($ch);
            curl_close($ch);
            echo $result;


        } else if($request[0]==='retainer' && $request[1]==='call2') {

            echo '<h1>Call2</h1>';

            $data = array(
                'max' => $_REQUEST['numFire'],
                'url' => $_REQUEST['link'],
                'task' => $_REQUEST['task'],
                'useSandbox' => $_REQUEST['useSandbox'],
                'requireUniqueWorkers' => $_REQUEST['requireUniqueWorkers'],
                'accessKey' => $_SERVER['PHP_AUTH_USER'],
                'secretKey' => $_SERVER['PHP_AUTH_PW'],
                'mode' => 'retainer',
                'tutPageUrl' => $_REQUEST['tutPageUrl'],
                'waitPageUrl' => $_REQUEST['waitPageUrl'],
                'instrPageUrl' => $_REQUEST['instrPageUrl']
            );

            $api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/php/updateReleased.php';
            echo $api_request_url;
//
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD => $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']
            ));
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_URL, $api_request_url);
            $result = curl_exec($ch);
            $api_response_info = curl_getinfo($ch);
            curl_close($ch);
            echo $result;



            //
            // Query
            //
        } else if($request[0]==='retainer' && $request[1]==='query') {

            if( isset($_REQUEST['task']) ) {
                $task        = $_REQUEST['task'];
                if (!is_string($task)){
                    http_response_code(400);
                    die("Incorrect parameters for preparation.");
                }
                echo '<h1>Query</h1>';

                // Copied code from Retainer/php/ajax_whosonline.php
                try {
                    $dbh = getDatabaseHandle();
                } catch(PDOException $e) {
                    echo $e->getMessage();
                }
                if($dbh) {
                    $sth = $dbh->query("SELECT COUNT(*) AS count FROM `whois_online` WHERE `task`='".$task."'");
                    $row1 = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
                    echo $row1['count'];
                }

            } else {
                http_response_code(400);
                die("Incorrect parameters for preparation.");
            }
            //
        } else if($request[0]==='bonus') {
            if($request[1]==='set') {
                $AccessKey = $_REQUEST['accessKey'];
                $SecretKey = $_REQUEST['secretKey'];
                $authenticationCode = hash("sha256", hash('sha256', $AccessKey) . hash('sha256', $SecretKey));

                if( file_exists('../../db/' . $authenticationCode . '.db') && isset($_REQUEST['workerId']) &&
                    isset($_REQUEST['assignmentId']) && isset($_REQUEST['bonusAmount']) ) {
                    logDebug('DB exists for hash of accessKey and secretKey => Request OK, serving.');
                    try {
                        $dbh = getDatabaseHandle();
                    } catch( PDOException $e ) {
                        echo $e->getMessage();
                    }
                } else {
                    logDebug('ERROR: DB DOES NOT EXIST for hash of accessKey and secretKey => REJECTING REQUEST');
                    break;
                }

                if($dbh) {

                    $workerId = $_REQUEST['workerId'];
                    $assignmentId = $_REQUEST['assignmentId'];
                    $bonusAmount = $_REQUEST['bonusAmount'];

                    $sthSelect = $dbh->prepare("SELECT bonusAmount FROM bonusLog WHERE workerId = :workerId AND assignmentId = :assignmentId AND authHash = :hash");
                    $sthSelect->execute(array(':workerId' => $workerId, ':assignmentId' => $assignmentId, ':hash' => $authenticationCode));
                    if (sizeof($sthSelect->fetchAll()) > 0) {
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

                }

            }
            if($request[1]==='get') {

                $AccessKey = $_REQUEST['accessKey'];
                $SecretKey = $_REQUEST['secretKey'];
                $authenticationCode = hash("sha256", hash('sha256', $AccessKey) . hash('sha256', $SecretKey));
                if (file_exists('../../db/' . $authenticationCode . '.db') && isset($_REQUEST['workerId']) &&
                    isset($_REQUEST['assignmentId'])
                ) {
                    logDebug('DB exists for hash of accessKey and secretKey => Request OK, serving.');
                    try {
                        $dbh = getDatabaseHandle();
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                        break;
                    }
                } else {
                    logDebug('ERROR: DB DOES NOT EXIST for hash of accessKey and secretKey => REJECTING REQUEST');
                    break;
                }

                if ($dbh) {

                    $workerId = $_REQUEST['workerId'];
                    $assignmentId = $_REQUEST['assignmentId'];

                    $sthSelect = $dbh->prepare("SELECT bonusAmount FROM bonusLog WHERE workerId = :workerId AND assignmentId = :assignmentId AND authHash = :hash");
                    $sthSelect->execute(array(':workerId' => $workerId, ':assignmentId' => $assignmentId, ':hash' => $authenticationCode));
                    $bonusAmount = $sthSelect->fetchAll();

                    $returnValue = 0.00; // Default value: 0 (bonus not found)
                    foreach ($bonusAmount as $entry) {
                        if (is_array($entry)) {
                            if (sizeof($entry) > 0) {
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
                    //echo $returnValue;

                    // Return bonus amount as JSON
                    $returnValue = array
                    (
                        'bonusAmount' => floatval($returnValue),
                    );
                    // Return return array as JSON
                    echo json_encode($returnValue);
                }
            } else if($request[1]==='delete') {

                $AccessKey = $_REQUEST['accessKey'];
                $SecretKey = $_REQUEST['secretKey'];
                $authenticationCode = hash("sha256", hash('sha256', $AccessKey) . hash('sha256', $SecretKey));
                if( file_exists('../../db/' . $authenticationCode . '.db') && isset($_REQUEST['workerId']) &&
                    isset($_REQUEST['assignmentId']) ) {
                    logDebug('DB exists for hash of accessKey and secretKey => Request OK, serving.');
                    try {
                        $dbh = getDatabaseHandle();
                    } catch( PDOException $e ) {
                        echo $e->getMessage();
                    }
                } else {
                    logDebug('ERROR: DB DOES NOT EXIST for hash of accessKey and secretKey => REJECTING REQUEST');
                }

                if($dbh) {

                    $workerId = $_REQUEST['workerId'];
                    $assignmentId = $_REQUEST['assignmentId'];

                    $sthDelete = $dbh->prepare("DELETE FROM bonusLog WHERE workerId = :workerId AND assignmentId = :assignmentId AND authHash = :hash");
                    $sthDelete->execute(array(':workerId' => $workerId, ':assignmentId' => $assignmentId, ':hash' => $authenticationCode));

                }

            }
        }
        else if($request[0]==='mturk') {

            // .:: Obtain AWS client ::. (needed by all functions in this category)
            if (isset($_REQUEST['useSandbox'])) {
                // Obtain AWS client object
                $client = createClient($_REQUEST['useSandbox'], $_REQUEST['accessKey'], $_REQUEST['secretKey']);
            } else {
                http_response_code(422); // 422: Unprocessable entity
                die("Not all required parameters specified to create MTurk client.");
            }

            if($request[1]==='sendBonus') {

                logDebug("sendBonus called.");

                if (isset($_REQUEST['workerId']) && isset($_REQUEST['assignmentId']) &&
                    isset($_REQUEST['bonusAmount']) ) {

                    logDebug("sendBonus parameters OK.");

                    if (isset($_REQUEST['bonusReason'])) {
                        $reason = $_REQUEST['bonusReason'];
                    } else {
                        // Default message to use if no explicit reason was specified
                        $reason = "Great job. Thank you and please accept this bonus as a token of our appreciation.";
                    }

                    // Actual bonus call to MTurk
                    $result = $client->sendBonus([
                        'AssignmentId' => $_REQUEST['assignmentId'], // REQUIRED
                        'WorkerId' => $_REQUEST['workerId'], // REQUIRED
                        'BonusAmount' => $_REQUEST['bonusAmount'], // REQUIRED
                        'Reason' => $reason
                    ]);

                    logDebug("sendBonus result: " . $result);

                } else {
                    http_response_code(422); // 422: Unprocessable entity
                    die("Not all required parameters specified.");
                }
            }
            else if($request[1]==='listAssignmentsForHit') {

                if (isset($_REQUEST['assignmentStatus']) && isset($_REQUEST['hitId'])  ) {
                    $assignmentStatus = $_REQUEST['assignmentStatus'];
                    if ($assignmentStatus==="Submitted" || $assignmentStatus==="Approved" ||
                        $assignmentStatus==="Rejected") {
                        // Functionality
                        $result = $client->listAssignmentsForHIT([
                            'AssignmentStatuses' => [$_REQUEST['assignmentStatus']],
                            'HITId' => $_REQUEST['hitId'], // REQUIRED
                            'MaxResults' => 10
                        ]);
                        echo $result;
                    } else {
                        http_response_code(422); // 422: Unprocessable entity
                        die("Error: Incorrect value for assignment status. " .
                            "Allowed values: Submitted, Approved and Rejected.");
                    }
                } else {
                    http_response_code(422); // 422: Unprocessable entity
                    die("Error: Not all required parameters specified.");
                }

            } else if($request[1]==='unreject') {

                if (isset($_REQUEST['assignmentId']) && isset($_REQUEST['workerId'])  ) {
                    $result = $client->approveAssignment([
                        'AssignmentId' => $assignmentId,
                        'WorkerId' => $workerId,
                        'OverrideRejection' => true, // Override causes unreject
                        'RequesterFeedback' => 'Amazing job.'
                    ]);
                } else {
                    http_response_code(422); // 422: Unprocessable entity
                    die("Not all required parameters specified.");
                }

            } else if($request[1]==='postHit') {

                if (isset($_REQUEST['assignmentDuration']) && isset($_REQUEST['keywords']) &&
                    isset($_REQUEST['description']) && isset($_REQUEST['hitUrl']) && isset($_REQUEST['bonusAmount']) &&
                    isset($_REQUEST['title']) ) {
                    $result = $client->createHIT([
                        'AssignmentDurationInSeconds' => $_REQUEST['assignmentDuration'], // REQUIRED
                        //'AssignmentReviewPolicy' => -1,
                        //'AutoApprovalDelayInSeconds' => -1,
                        'Description' => $_REQUEST['description'], // REQUIRED
                        //'HITLayoutId' => '',
                        //'HITLayoutParameters' => [],
                        //'HITReviewPolicy' => [],
                        'Keywords' => $_REQUEST['keywords'],
                        'LifetimeInSeconds' => 6000, // REQUIRED
                        'MaxAssignments' => 1,
                        'QualificationRequirements' => [
                            [
                                'Comparator' => 'EqualTo', // REQUIRED
                                //'IntegerValues' => [],
                                'LocaleValues' => [
                                    [
                                        'Country' => 'US', // REQUIRED
                                        //'Subdivision' => 'US-MI',
                                    ],
                                    // ...
                                ],
                                'QualificationTypeId' => '00000000000000000071', // REQUIRED
                                //'RequiredToPreview' => false,
                            ],
                            // ...
                        ],
                        'Question' => '<ExternalQuestion xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2006-07-14/ExternalQuestion.xsd"><ExternalURL>' . $_REQUEST['hitUrl'] . '</ExternalURL><FrameHeight>400</FrameHeight></ExternalQuestion>',
                        //'RequesterAnnotation' => null,
                        'Reward' => $_REQUEST['reward'], // REQUIRED
                        'Title' => $_REQUEST['title'], // REQUIRED
                        //'UniqueRequestToken' => null,
                    ]);
                    print $result;
                } else {
                    http_response_code(422); // 422: Unprocessable entity
                    die("Not all required parameters specified.");
                }

            } else if($request[1]==='extractSuggestedBonusFromResult') {

                if ( !isset($_REQUEST['assignmentId']) ) {
                    http_response_code(422); // 422: Unprocessable entity
                    die("Not all required parameters specified.");
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

            } else if ($request[1]==='listReviewableHits') {
                $result = $client->listReviewableHITs();
                echo $result;
            }
        }
//        else if($request[0]==='CATEGORY') {
//            if ($request[1]==='COMMAND') {
//                if (isset($_REQUEST['PARAM1']) && isset($_REQUEST['PARAM2'])  ) {
//                    // Functionality
//                } else {
//                    http_response_code(422); // 422: Unprocessable entity
//                    die("Not all required parameters specified.");
//                }
//            }
//        }
        else {
            http_response_code(404);
            die("Requested method not found.");
        }
//}
        break;
    case 'PUT':
        http_response_code(404);
        die("PUT is not supported.");
        break;
    case 'DELETE':
        http_response_code(404);
        die("DELETE is not supported.");
        break;
}
