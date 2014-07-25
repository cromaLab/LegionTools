<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');

  try {
      $dbh = getDatabaseHandle();
  } catch( PDOException $e ) {
      echo $e->getMessage();
  }


if( $dbh ) {
    
    $worker = $_REQUEST['workerId'];

    $sql = "SELECT count(*) as wCount FROM workers WHERE wid=:worker"; 
    $sth = $dbh->prepare($sql); 
    $sth->execute(array(':worker'=>$worker)); 
    $result = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
    //$numPrevEntries = $result->fetchColumn() + 1;
    $numPrevEntries = $result['wCount'];
    
    // $queryPrevEntries = $dbh->prepare("SELECT COUNT(*) as wCount FROM workers WHERE wid=:worker"); //prepares statement to get number of previous times this same worker has been logged
//     $numPrevEntries = $queryPrevEntries->execute(array(':worker'=>$worker));
// 
//     echo($numPrevEntries['wCount']);

    $query = $dbh->prepare("INSERT INTO workers(wid, idx) VALUES(:wid, :idx)");
    $query->execute(array(':wid' => $worker, ':idx' => $numPrevEntries));

}

?>
