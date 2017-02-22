<?php

$accessKey = $_REQUEST['accessKey'];
$secretKey = $_REQUEST['secretKey'];

$tableName  = hash('sha256', $accessKey) . hash('sha256', $secretKey);
$tableName = hash("sha256", $tableName);

//The database with tables for the retainer tool

// $file = 'nums.txt';
// // Open the file to get existing content
// $current = file_get_contents($file);
// // Append a new person to the file
// $current .= "CREATE Access key: " . $accessKey . "\n";
// $current .= "CREATE Secret key: " . $secretKey . "\n";
// $current .= "CREATE tableName: " . $tableName . "\n";
// // Write the contents back to the file
// file_put_contents($file, $current);

try {
	$dbh = new PDO('sqlite:../../db/' . $tableName . '.db'); 
	$dbh->setAttribute(PDO::ATTR_TIMEOUT, 10);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	echo "herefirst";

	$sql = 'CREATE TABLE IF NOT EXISTS "banned" (
	  "workerId" varchar(99) DEFAULT NULL
	);
	CREATE TABLE IF NOT EXISTS hits(task varchar, hit_Id varchar, time varchar, assignable int default 1, sandbox int);
	CREATE TABLE IF NOT EXISTS "liveStatus" (
	  "session" varchar(99) DEFAULT NULL,
	  "status" varchar(99) DEFAULT NULL
	);
	CREATE TABLE IF NOT EXISTS "released" (
	  "ID" INTEGER PRIMARY KEY ,
	  "url" text,
	  "max" int(11) DEFAULT NULL,
	  "sent" int(11) DEFAULT NULL,
	  "task" varchar(99) DEFAULT NULL
	);
	CREATE TABLE IF NOT EXISTS retainer(id INTEGER PRIMARY KEY AUTOINCREMENT, task_title VARCHAR(100) NOT NULL, task_description VARCHAR(100) NOT NULL, task_keywords VARCHAR(100) NOT NULL, min_price VARCHAR(10), max_price VARCHAR(10), target_workers INT, task VARCHAR(100), done integer default 1, country VARCHAR(100), percentApproved INTEGER, instructions TEXT, noRepeatQualId text, noRepeatQualIdSandbox text, noRepeatQualIdLive text);
	CREATE TABLE IF NOT EXISTS "triggerFlag" (
	  "id" INTEGER PRIMARY KEY ,
	  "task" text,
	  "link" text,
	  "fireTime" timestamp
	);
	CREATE TABLE IF NOT EXISTS "whois_online" (
	  "id" text,
	  "task" text,
	  "time" time DEFAULT NULL
	);
	CREATE TABLE IF NOT EXISTS "workers" (
	  "id" INTEGER PRIMARY KEY ,
	  "wid" text,
	  "idx" text,
	  "startTime" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  "endTime" timestamp NOT NULL DEFAULT "0000-00-00 00:00:00"
    );
    CREATE TABLE IF NOT EXISTS "tutorialLog" (
        "id" INTEGER PRIMARY KEY,
        "workerId" text,
        "projectName" text
    );
    CREATE TABLE IF NOT EXISTS "retainerRoutingCheck" (
        "id" INTEGER PRIMARY KEY,
        "workerId" text,
        "checked" text
    );';

	$dbh->exec($sql);
} catch(PDOException $e) {
    echo $e->getMessage();//Remove or change message in production code
}

$dbh = null;
chmod('../../db/' . $tableName . '.db', 0777); 
?>
