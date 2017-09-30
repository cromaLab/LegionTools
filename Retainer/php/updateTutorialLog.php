<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
error_log(".:: ".basename(__FILE__),0); // Debugging

include('_db.php');

try {
    $dbh = getDatabaseHandle();
} catch( PDOException $e ) {
    echo $e->getMessage();
}


if( $dbh ) {

    $worker = $_REQUEST['workerId'];
    $projectName = $_REQUEST['task']; // TODO: should project name be task or should it be an extra parameter? 

    $stmt = $dbh->prepare("INSERT INTO `tutorialLog` (workerId, projectName) VALUES (:id, :pName);");
    $stmt->execute(array(':id' => $worker, ':pName' => $projectName));
    echo $worker . ";;" . $projectName;
}

?>
