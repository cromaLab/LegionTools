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

function createQualificationRequirement($row){

	$percentApproved = (string)$row[0]["percentApproved"];
	// require Worker_PercentAssignmentsApproved >= IntegerValue
	if($percentApproved != ""){
		$Worker_PercentAssignmentsApproved = array(
		 "QualificationTypeId" => "000000000000000000L0",
		 "Comparator" => "GreaterThanOrEqualTo",
		 "IntegerValue" => $percentApproved
		);
	}	

	// require Worker_Locale == Country
	$country = $row[0]["country"];
	if($country != "" && $country != "All"){
		$Worker_Locale = array(
		 "QualificationTypeId" => "00000000000000000071",
		 "Comparator" => "EqualTo",
		 "LocaleValue" => array("Country" => $country)
		);
		return array($Worker_Locale, $Worker_PercentAssignmentsApproved);
	}

	return array($Worker_PercentAssignmentsApproved);
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
		// print_r($mt);
		if(is_array($mt)){
write($debug, "Expiration process valid\n");
		}
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

if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "retainer"){
	$url = $baseURL . "/Retainer/index.php?task=" . $_REQUEST['task'];
}
else if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "direct"){
	$url = urlencode($_REQUEST['URL']);
}

$debugFile = "debugFile.txt";
$debug = fopen($debugFile, 'w');

$numAssignableHits = 0;
while(!iShouldQuit()){
fwrite($debug, "Start loop\n");

 	// Post HITs
	$result = getTaskRowInDb();
	$qualification = createQualificationRequirement($result);
	while(!isTargetReached() && ($numAssignableHits < ($result[0]["target_workers"] + 5))) //Number of HITs to post: target number of workers + 5
	// while($numAssignableHits < 3) //Number of HITs to post: target number of workers + 5
	{
		$minPrice = $result[0]["min_price"];
		$maxPrice = $result[0]["max_price"];
		$price = rand( $minPrice, $maxPrice ) / 100;

		// turk50_hit($title,$description,$money,$url,$duration,$lifetime);
		$hitResponse = turk50_hit($result[0]['task_title'], $result[0]['task_description'], $price, $url, 3000, 3000, $qualification);
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
$propExist = property_exists($hitInfo->HIT, "HITStatus");
fwrite($debug, "Property exist test: " . $propExist . "\n");
		if(property_exists($hitInfo->HIT, "HITStatus")){
fwrite($debug, "Hit status: " . $hitInfo->HIT->HITStatus . "\n");
			if($hitInfo->HIT->HITStatus == "Disposed"){
				// expireHit($hitId);
				$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
				$sth = $dbh->prepare($sql);
				$sth->execute(array(':hit_Id' => $hitId));
			}
			else if($hitInfo->HIT->HITStatus == "Assignable"){
				$numAssignableHits++;
				if((time() - $hit['time']) > 200){
fwrite($debug, "Expire old hit\n");
					sleep(1);
					expireHit($hitId);
				}
			}
		}
		else{
			expireHit($hitId);
		}
fwrite($debug, $numAssignableHits . " - num Assignable hits\n");
		// echo $hit['time'] . "</br>";
		// echo time() . "</br>";
// fwrite($debug, time() . " " . $hit['time'] . "\n");
		sleep(1); //Don't overload mturk with getHit
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
	if(property_exists($hitInfo->HIT, "HITStatus")){
		expireHit($hitId);
		sleep(.25);
		fwrite($debug, "Hit status: " . $hitInfo->HIT->HITStatus . "\n");
		if($hitInfo->HIT->HITStatus == "Disposed"){
			// expireHit($hitId);
			$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
			$sth = $dbh->prepare($sql);
			$sth->execute(array(':hit_Id' => $hitId));
		}
	}
	else{
		$sql = ("DELETE FROM hits WHERE hit_Id = :hit_Id");
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':hit_Id' => $hitId));
	}
	sleep(.75); //Don't overload mturk with getHit
}

fwrite($debug, "Exit\n");
fclose($debug);

?>