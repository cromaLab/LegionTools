<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('max_execution_time', 10000);
set_time_limit ( 10000);

include("../../amtKeys.php");
include("../../baseURL.php");
include("../../isSandbox.php");
include("../../getDB.php");
include 'turk_functions.php';


try {
    $dbh = getDatabaseHandle();
  } catch(PDOException $e) {
    echo $e->getMessage();
  }

function getTaskRowInDb(){
	global $dbh;
	$sql = "SELECT * FROM retainer WHERE task = :task";
	$sth = $dbh->prepare($sql);
	$sth->execute(array(':task' => $_REQUEST['task']));
	$result = $sth->fetchAll(PDO::FETCH_ASSOC);

	return $result;
}

//Expires all HITs for given task
function expireAllHits(){
	global $dbh, $debug;

	$task = $_REQUEST['task'];
	$sql = "SELECT * FROM hits WHERE task = :task";
	$sth = $dbh->prepare($sql);
	$sth->execute(array(':task' => $_REQUEST['task']));
	$hits = $sth->fetchAll();
// print_r($hits);
	foreach ($hits as $hit) {
		$hitId = $hit['hit_Id'];
		expireHit($hitId);
	}
}

function expireHit($hitId){
		global $dbh, $debug, $numAssignableHits;

fwrite($debug, "Expire " . $hitId . "\n");
		turk_easyExpireHit($hitId);
fwrite($debug, "Expire done " . $hitId . "\n");
		sleep(.25); //Give the HIT a moment to expire
		$mt = turk_easyDispose($hitId);
fwrite($debug, "Dispose done " . $hitId . "\n");
		sleep(.25); //Give the HIT a moment to dispose

// 		// If hit was successfully disposed, delete from db
// 		if($mt->FinalData['Request']['IsValid']){
// fwrite($debug, "Remove from array " . $hitId . "\n");
// 			$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
// fwrite($debug, "Query formed " . $hitId . "\n");

// 			$count = 0;
// 			$maxTries = 5;
// 			$success = false;
// 			while(!$success) {
// 				try {
// fwrite($debug, "Try: " . $hitId . "\n");
// 					$sth = $dbh->prepare($sql);
// fwrite($debug, "Prepared " . $hitId . "\n");
// 				    $sth->execute(array(':hit_Id' => $hitId));
// fwrite($debug, "Success " . $hitId . "\n");
// 				    $success = true; //break out of loop on success
// 				}
// 				catch(PDOException $e) {
// 				    // echo "Statement failed: " . $e->getMessage();
// 				    // return false;
// fwrite($debug, "Catch: " . $e->getMessage() . "\n");
// 					$count++;
// 					if($count >= $maxTries){
// fwrite($debug, "Throw e: " . $e->getMessage() . "\n");
// 						// throw $e;
// 						return false;
// 					}
// 				}
// 			}
// 			$numAssignableHits--;
// 		}
fwrite($debug, "Expiration process completed\n");
}

function iShouldQuit(){
	global $dbh, $debug;
	$result = getTaskRowInDb();
	if($result[0]['done'] == 1){
		expireAllHits();
		return true;
	}
	else return false;
}

function isTargetReached(){
	global $dbh, $debug;
	// Get target number of workers
	$result = getTaskRowInDb();
	$numWorkersTarget = $result[0]["target_workers"];

// 	// Quit if no workers are needed
// 	if($numWorkersTarget == 0){
// 		expireAllHits($dbh);
// 		exit();
// 	}

	// Get num workers currently online
	$sth = $dbh->query("SELECT COUNT(*) AS count FROM `whois_online` WHERE `task`='".$_REQUEST['task']."'");
	$row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	$numWorkersOnline = $row['count'];

	// If target has been reached
	if($numWorkersOnline >= $numWorkersTarget){
fwrite($debug, "Target number of workers reached\n");
		expireAllHits();
		return true;
	}
	else{
		return false;
	}
}

////////MAIN/////////

$task = $_REQUEST['task'];

$debugFile = "testFile.txt";
$debug = fopen($debugFile, 'w');

$numAssignableHits = 0;
while(!iShouldQuit()){
fwrite($debug, "Start loop\n");

 	// Post HITs
	$result = getTaskRowInDb();
	while(!isTargetReached() && ($numAssignableHits < ($result[0]["target_workers"] + 5))) //Number of HITs to post: target number of workers + 5
	// while($numAssignableHits < 3) //Number of HITs to post: target number of workers + 5
	{
		$minPrice = $result[0]["min_price"];
		$maxPrice = $result[0]["max_price"];
		$price = rand( $minPrice, $maxPrice ) / 100;

		// turk50_hit($title,$description,$money,$url,$duration,$lifetime);
		$hitResponse = turk50_hit($result[0]['task_title'], $result[0]['task_description'], $price, $baseURL . "/Retainer/index.php?task=" . $_REQUEST['task'], 3000, 3000);
		$hitId = $hitResponse->HIT->HITId;
		$currentTime = time();
		$sql = "INSERT INTO hits (task, hit_Id, time) values (:task, :hit_Id, :time)";
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':task' => $_REQUEST['task'], ':hit_Id' => $hitId, ':time' => $currentTime));
		$numAssignableHits++;
fwrite($debug, "Post HIT\n");

		sleep(1);
	}

	// Delete old HITs and get num assignable
	$sql = ("SELECT * from hits WHERE task = :task");
	$sth = $dbh->prepare($sql);
	$sth->execute(array(':task' => $_REQUEST['task']));
	$hits = $sth->fetchAll();

	$numAssignableHits = 0;

	foreach ($hits as $hit) {
		//Is assignable?
		$hitId = $hit['hit_Id'];
		$hitInfo = turk50_getHit($hitId);
// print_r($hitInfo);
		if($hitInfo->HIT->HITStatus == "Disposed"){
			$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
			$sth = $dbh->prepare($sql);
			$sth->execute(array(':hit_Id' => $hitId));
		}
		else if($hitInfo->HIT->HITStatus == "Assignable"){
			$numAssignableHits++;
		}
fwrite($debug, $numAssignableHits . " - num Assignable hits\n");
		// echo $hit['time'] . "</br>";
		// echo time() . "</br>";
// fwrite($debug, time() . " " . $hit['time'] . "\n");
		if((time() - $hit['time']) > 200){
fwrite($debug, "Expire old hit\n");
			expireHit($hitId);
		}
		sleep(.5); //Don't overload mturk with getHit
	}
	sleep(2);
}

$sql = ("SELECT * from hits WHERE task = :task");
$sth = $dbh->prepare($sql);
$sth->execute(array(':task' => $_REQUEST['task']));
$hits = $sth->fetchAll();

foreach ($hits as $hit) {
	$hitId = $hit['hit_Id'];
fwrite($debug, "Enter foreach " . $hitId . "\n");
	$hitInfo = turk50_getHit($hitId);
	if($hitInfo->HIT->HITStatus == "Disposed"){
		$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
		$sth = $dbh->prepare($sql);
fwrite($debug, "Prepared " . $hitId . "\n");
		$sth->execute(array(':hit_Id' => $hitId));
fwrite($debug, "Executed " . $hitId . "\n");
	}
	sleep(.5); //Don't overload mturk with getHit
}

fwrite($debug, "Exit\n");
fclose($debug);

?>
