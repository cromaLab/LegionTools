<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include("_db.php");
$task = $_REQUEST['task'];
$role = $_REQUEST['role'];
if($role=='crowd'){
	$id = $_REQUEST['worker'];
}

try {
    $dbh = getDatabaseHandle();
  } catch(PDOException $e) {
    echo $e->getMessage();
  }

  if($dbh) {
	if ($role=='crowd') {
		// Checking wheter the worker is already marked as being online:
		$sth = $dbh->query("SELECT * FROM `whois_online` WHERE `id`='".$id."'");
		$row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
		if($row){
			// If the visitor is already online, just update the time value of the row:
			$result=$dbh->query("UPDATE `whois_online` SET `time`= (DATETIME('now')) WHERE `id`='".$id."' AND `task`='".$task."'");
		}
		else{
			//	$dbh->prepare("INSERT INTO `whois_online` (id, task) VALUES (:id, :task)");
			//	$dbh->execute(array(':id'=>$id, ':task'=>$task));
			$sql="INSERT INTO `whois_online` (id,task) VALUES ('".$id."','".$task."')";
			$sth = $dbh->query($sql);
			$result=$dbh->query("UPDATE `whois_online` SET `time`= (DATETIME('now')) WHERE `id`='".$id."' AND `task`='".$task."'");
		}
	}


	// Removing entries not updated in the last x seconds:
	$expireTime = time() - 5;
	$dbh->query("DELETE FROM `whois_online` WHERE (CAST(strftime('%s', `time`) AS INTEGER) < $expireTime) OR `time` IS NULL");	
		
	// Counting all the online visitors:
	$sth = $dbh->query("SELECT COUNT(*) AS count FROM `whois_online` WHERE `task`='".$task."'");
	$row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
	// Outputting the number for majority value as plain text:
	//for testing: echo floor((intval($row['count'])-1)*.65);
	//echo floor((intval($row['count']))*.65);
	echo $row['count'];
	
	// If you want to view who is online uncomment this
// 	 $sth = $dbh->query("SELECT * FROM `whois_online` WHERE `task`='$task'");	
// 	 while($row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
// 	 	print_r($row);
// 	 }
}

?>
