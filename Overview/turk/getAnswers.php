<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('max_execution_time', 10000);
set_time_limit ( 10000);

include("../../amtKeys.php");
include("../../config.php");
include("../../isSandbox.php");
include("../../getDB.php");
include 'turk_functions.php';

$AccessKey = $_REQUEST['accessKey'];
$SecretKey = $_REQUEST['secretKey'];

if($_REQUEST['accessKey'] == "use_file" && $_REQUEST['secretKey'] == "use_file"){
	$tableName = 'retainer.db';
}
else {
    $hash1= hash('sha256', $_REQUEST['accessKey']) . hash('sha256', $_REQUEST['secretKey']);
    $tableName = hash('sha256', $hash1); 
}
try {
    $dbh = getDatabaseHandle();
  } catch(PDOException $e) {
    echo $e->getMessage();
  }

function getTaskRowInDb(){
	global $dbh, $SANDBOX;
	$sql = "SELECT * FROM retainer WHERE task = :task";
	$sth = $dbh->prepare($sql);
	$sth->execute(array(':task' => $_REQUEST['task']));
	$result = $sth->fetchAll(PDO::FETCH_ASSOC);

	return $result;
}

function createQualificationRequirement($row){
	global $dbh, $SANDBOX;

	$qualsArray = array();

	$percentApproved = (string)$row[0]["percentApproved"];
	// require Worker_PercentAssignmentsApproved >= IntegerValue
	if($percentApproved != ""){
		$Worker_PercentAssignmentsApproved = array(
		 "QualificationTypeId" => "000000000000000000L0",
		 "Comparator" => "GreaterThanOrEqualTo",
		 "IntegerValue" => $percentApproved
		);

		array_push($qualsArray, $Worker_PercentAssignmentsApproved);
	}	

	// require Worker_Locale == Country
	$country = $row[0]["country"];
	if($country != "" && $country != "All"){
		$Worker_Locale = array(
		 "QualificationTypeId" => "00000000000000000071",
		 "Comparator" => "EqualTo",
		 "LocaleValue" => array("Country" => $country)
		);
		array_push($qualsArray, $Worker_Locale);
	}

	if($_REQUEST['requireUniqueWorkers'] == "true"){
		if($SANDBOX) $dbCol = "noRepeatQualIdSandbox";
		else $dbCol = "noRepeatQualIdLive";

		$noRepeatQualId = $row[0][$dbCol];

		if($noRepeatQualId == null || $noRepeatQualId == ""){
			$qual = turk50_createQualificationType(generateRandomString(), "This qualification is for people who have worked for me on this task before.", "Worked for me before", $SANDBOX);
			// print_r($qual);
			$noRepeatQualId = $qual->QualificationType->QualificationTypeId;

			if($SANDBOX) $sql = ("UPDATE retainer set noRepeatQualIdSandbox = :noRepeatQualId WHERE task = :task");
			else $sql = ("UPDATE retainer set noRepeatQualIdLive = :noRepeatQualId WHERE task = :task");
			$sth = $dbh->prepare($sql); 
			$sth->execute(array(":task"=>$_REQUEST['task'], ":noRepeatQualId"=>$noRepeatQualId));
		}

		$Unique_Workers_Qual = array(
		 "QualificationTypeId" => (string)$noRepeatQualId,
		 "Comparator" => "DoesNotExist"
		);
		array_push($qualsArray, $Unique_Workers_Qual);
	}

	return $qualsArray;
}

//Expires all HITs for given task
function expireAllHits(){
	global $dbh, $debug, $SANDBOX;

	$task = $_REQUEST['task'];
	$sql = "SELECT * FROM hits WHERE task = :task AND assignable = 1 AND sandbox = :sandbox";
	$sth = $dbh->prepare($sql);
	$sth->execute(array(':task' => $_REQUEST['task'], ':sandbox' => $SANDBOX));
	$hits = $sth->fetchAll();
// print_r($hits);
	foreach ($hits as $hit) {
		$hitId = $hit['hit_Id'];
		expireHit($hitId);
	}
}

function expireHit($hitId){
	global $dbh, $debug, $numAssignableHits, $SANDBOX;
	turk_easyExpireHit($hitId);
	sleep(.25); //Give the HIT a moment to expire
	$mt = turk_easyDispose($hitId);
	sleep(.25); //Give the HIT a moment to dispose
	return $mt;
}

function iShouldQuit(){
	// echo "checking to quit";
	global $dbh, $debug, $SANDBOX;
	$result = getTaskRowInDb();
	// print_r($result);
	if($result[0]['done'] == 2){
		expireAllHits();
		return true;
	}
	else return false;
}

function isTargetReached(){
	global $dbh, $debug, $SANDBOX;
	// Get target number of workers
	$result = getTaskRowInDb();
	$numWorkersTarget = $result[0]["target_workers"];

	// Get num workers currently online
	$sth = $dbh->query("SELECT COUNT(*) AS count FROM `whois_online` WHERE `task`='".$_REQUEST['task']."'");
	$row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$numWorkersOnline = $row['count'];

	// If target has been reached
	if($numWorkersOnline >= $numWorkersTarget){
// fwrite($debug, "Target number of workers reached\n");
		expireAllHits();
		return true;
	}
	else{
		return false;
	}
}

function removeOldHITs(){
	global $dbh, $debug, $SANDBOX;
	// Get target number of workers
	// Delete old HITs and get num assignable	

	// delete hits with no HITId
	$sql = ("DELETE FROM hits WHERE task = :task AND hit_Id is null");
	$sth = $dbh->prepare($sql);
	$sth->execute(array(':task' => $_REQUEST['task']));

	$sql = ("SELECT * from hits WHERE task = :task AND sandbox = :sandbox");
	$sth = $dbh->prepare($sql);
	$sth->execute(array(':task' => $_REQUEST['task'], ':sandbox' => $SANDBOX));
	$hits = $sth->fetchAll();

	foreach ($hits as $hit) {
		$hitId = $hit['hit_Id'];
		$hitInfo = turk50_getHit($hitId);
		// fwrite($debug,  $hitId . " " . $hitInfo->HIT->Request->IsValid . " IsValid?\n");
		// fwrite($debug,  $hitId . " " . $hitInfo->HIT->HITStatus . " HITStatus?\n");
		if($hitInfo->HIT->Request->IsValid == "False"){
			$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
			$sth = $dbh->prepare($sql);
			$sth->execute(array(':hit_Id' => $hitId));
		}
		else if(property_exists($hitInfo->HIT, "HITStatus")){
			if($hitInfo->HIT->HITStatus == "Disposed"){
				$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
				$sth = $dbh->prepare($sql);
				$sth->execute(array(':hit_Id' => $hitId));
			}
			else if($hitInfo->HIT->HITStatus == "Reviewable"){
				$sql = ("UPDATE hits SET assignable = 0 WHERE hit_Id = :hit_Id");
				$sth = $dbh->prepare($sql);
				$sth->execute(array(':hit_Id' => $hitId));
			}
			else{
				$expired = expireHit($hitId);
				if($expired != "error with disposal"){
					$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
					$sth = $dbh->prepare($sql);
					$sth->execute(array(':hit_Id' => $hitId));
				}
			}
		}
		sleep(1); //Don't overload mturk with getHit
	}
}

////////MAIN/////////

$task = $_REQUEST['task'];

// $debugFile = "debugFile.txt";
// $debug = fopen($debugFile, 'w');

removeOldHITs();

if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "retainer" || $_REQUEST['mode'] == "auto"){
	// if($_REQUEST['mode'] == "auto") $url = $_REQUEST['url'];
    if($_REQUEST['mode'] == "auto") { 
        $url = $baseURL . "/taskLanding.php?task=" . $_REQUEST['task'] . "&amp;&amp;requireUniqueWorkers=" . $_REQUEST['requireUniqueWorkers'] . "&amp;&amp;url=" . urlencode($_REQUEST['url']) . "&amp;&amp;dbName=" . $tableName; 
    }
    else {
        $url = $baseURL . "/Retainer/index.php?task=" . $_REQUEST['task'] . "&amp;&amp;dbName=" . $tableName . "&amp;&amp;tutPageUrl=" . urlencode($_REQUEST['tutPageUrl']) . "&amp;&amp;waitPageUrl=" . urlencode($_REQUEST['waitPageUrl']) . "&amp;&amp;instrPageUrl=" . urlencode($_REQUEST['instrPageUrl']); 
    }

	$numAssignableHits = 0;
	while(!iShouldQuit()){
	// fwrite($debug, "Start loop\n");

	 	// Post HITs
		$result = getTaskRowInDb();
		$qualification = createQualificationRequirement($result);
		while(!isTargetReached() && ($numAssignableHits < ($result[0]["target_workers"] + 5))) //Number of HITs to post: target number of workers + 5
		// while($numAssignableHits < 3) //Number of HITs to post: target number of workers + 5
		{
			$minPrice = $result[0]["min_price"];
			$maxPrice = $result[0]["max_price"];
			$price = rand( $minPrice, $maxPrice ) / 100;

			// turk50_hit($title,$description,$money,$url,$duration,$lifetime,$qualification,$maxAssignments,$AutoApprovalDelayInSeconds) 
			$hitResponse = turk50_hit($result[0]['task_title'], $result[0]['task_description'], $price, $url, 1800, 50000, $qualification, 1, $result[0]['task_keywords'],12000);
			//$hitResponse = turk50_hit($result[0]['task_title'], $result[0]['task_description'], $price, $url, 3600, 50000, $qualification, 1, $result[0]['task_keywords'],1200);
			if($hitResponse->HIT->Request->IsValid == "True"){
				$hitId = $hitResponse->HIT->HITId;
				if(!empty($hitId) && $hitId != ""){
					$currentTime = time();
					$sql = "INSERT INTO hits (task, hit_Id, time, sandbox) values (:task, :hit_Id, :time, :sandbox)";
					$sth = $dbh->prepare($sql);
					$sth->execute(array(':task' => $_REQUEST['task'], ':hit_Id' => $hitId, ':time' => $currentTime, ':sandbox' => $SANDBOX));
					$numAssignableHits++;
					// fwrite($debug, "Post HIT\n");
				}
			}
			sleep(1);
		}

		// Delete old HITs and get num assignable
		$sql = ("SELECT * from hits WHERE task = :task AND assignable = 1 AND sandbox = :sandbox");
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':task' => $_REQUEST['task'], ':sandbox' => $SANDBOX));
		$hits = $sth->fetchAll();

		$numAssignableHits = 0;

		foreach ($hits as $hit) {
			$hitId = $hit['hit_Id'];
			$hitInfo = turk50_getHit($hitId);
			if($hitInfo->HIT->Request->IsValid == "False"){
				$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
				$sth = $dbh->prepare($sql);
				$sth->execute(array(':hit_Id' => $hitId));
			}
			else if(property_exists($hitInfo->HIT, "HITStatus")){
				if($hitInfo->HIT->HITStatus == "Disposed"){
					$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
					$sth = $dbh->prepare($sql);
					$sth->execute(array(':hit_Id' => $hitId));
				}
				else if($hitInfo->HIT->HITStatus == "Assignable"){
					if((time() - $hit['time']) > 200){
						sleep(.25);
						expireHit($hitId);
					}
					else $numAssignableHits++;
				}
				else if($hitInfo->HIT->HITStatus == "Reviewable"){
					$sql = ("UPDATE hits SET assignable = 0 WHERE hit_Id = :hit_Id");
					$sth = $dbh->prepare($sql);
					$sth->execute(array(':hit_Id' => $hitId));
				}
			}

	// fwrite($debug, $numAssignableHits . " - num Assignable hits\n");
			sleep(1); //Don't overload mturk with getHit
		}
		sleep(2);
	}

	removeOldHITs();
}
else if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "direct"){
	// $url = $_REQUEST['URL'];
	$result = getTaskRowInDb();

	$url = $baseURL . "/taskLanding.php?task=" . $_REQUEST['task'] . "&amp;&amp;requireUniqueWorkers=" . $_REQUEST['requireUniqueWorkers'] . "&amp;&amp;url=" . urlencode($_REQUEST['url']) . "&amp;&amp;dbName=" . $tableName;

	$qualification = createQualificationRequirement($result);

	$price = $_REQUEST['price']/100;
	$numHITs = $_REQUEST['numHITs'];
	$numAssignments = $_REQUEST['numAssignments'];

	for($i = 0; $i < $numHITs; $i++){
		// turk50_hit($title,$description,$money,$url,$duration,$lifetime,$qualification,$maxAssignments) 
		$hitResponse = turk50_hit($result[0]['task_title'], $result[0]['task_description'], $price, $url, 1800, 50000, $qualification, $numAssignments, $result[0]['task_keywords'],12000);
		//$hitResponse = turk50_hit($result[0]['task_title'], $result[0]['task_description'], $price, $url, 3600, 50000, $qualification, $numAssignments, $result[0]['task_keywords'],1200);
		$hitId = $hitResponse->HIT->HITId;
		$currentTime = time();
		$sql = "INSERT INTO hits (task, hit_Id, time, sandbox) values (:task, :hit_Id, :time, :sandbox)";
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':task' => $_REQUEST['task'], ':hit_Id' => $hitId, ':time' => $currentTime, ':sandbox' => $SANDBOX));
		// $numAssignableHits++;
		// fwrite($debug, "Post HIT\n");
		sleep(1);
	}
}

if(isset($noRepeatQualId)){
	$sql = ("UPDATE retainer set noRepeatQualIdLive = :noRepeatQualId WHERE task = :task");
	$sth = $dbh->prepare($sql); 
	$sth->execute(array(":task"=>$_REQUEST['task'], ":noRepeatQualId"=>$noRepeatQualId));
}

function generateRandomString($length = 50) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

$sql = "UPDATE retainer SET done = 1 WHERE task = :task";
$sth = $dbh->prepare($sql); 
$sth->execute(array(':task' => $_REQUEST['task']));


// fwrite($debug, "Exit\n");
// fclose($debug);

?>
