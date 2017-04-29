<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include("../php/_db.php");

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
        echo '<h1>You asked for GET</h1>';
        break;
    case 'POST':

        if(count($request)!=2) {
            http_response_code(404);
            die("Incorrect path length.");
        }

        echo '<h1>You asked for POST</h1>';

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
                // Call
                //
        } else if($request[0]==='retainer' && $request[1]==='callX') {
            if( isset($_REQUEST['numFire']) && isset($_REQUEST['task']) && isset($_REQUEST['link']) ) {

                echo '<h1>Call</h1>';

                $data = array(
                    'max' => $_REQUEST['numFire'],
                    'link' => $_REQUEST['link'],
                    'task' => $_REQUEST['task'],
                    'accessKey' => $_SERVER['PHP_AUTH_USER'],
                    'secretKey' => $_SERVER['PHP_AUTH_PW'],
                );

                // Could add type checking like this
//                if (!is_string($task) || !is_string($fire_to_url) || !is_numeric($num_fire)){
//                    http_response_code(400);
//                    die("Incorrect parameters for preparation.");
//                }

                // Need 2 calls: One to Retainer/php/setFire.php and on success call Retainer/php/updateReleased.php
                $queryString = http_build_query($data);
                $api_request_url = 'http://localhost:8080/Retainer/php/startRecruiting.php?' . $queryString;
                echo $api_request_url;

                $ch = curl_init($api_request_url);
                $result = curl_exec($ch);

                curl_close($ch);

                //Retainer/php/setFire.php
//                $.ajax({
//            url: retainerLocation + "php/setFire.php",
//            type: "POST",
//            async: true,
//            data: {url: link, task: task, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
//            dataType: "text",
//            success: function(d) {
//                    $.ajax({
//                    url: retainerLocation + "php/updateReleased.php",
//                    type: "POST",
//                    async: true,
//                    data: {url: link, max: numFire, task: task, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
//                    dataType: "text",
//                    success: function(d) {
//
//                    },
//                    fail: function() {
//                        statusbar.innerHTML = "Sending number of workers failed";
//                    }
//                });
//            },

                // Make actual call


            } else {
                http_response_code(400);
                die("Incorrect parameters for preparation.");
            }


        } else if($request[0]==='retainer' && $request[1]==='call') {

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
        } else {
            http_response_code(404);
            die("Requested method not found.");
        }
        break;
    case 'PUT':
        http_response_code(404);
        die("No PUT supported.");
        break;
    case 'DELETE':
        http_response_code(404);
        die("No DELETE supported.");
        break;
}
