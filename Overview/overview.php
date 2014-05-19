<?php
error_reporting(E_ALL);
ini_set('display_errors','On');


require_once 'includes/vars.php';
require_once 'turk/turk_functions.php';


if( isset($_REQUEST["sandbox"]) ) {
	$SANDBOX = ($_REQUEST["sandbox"] == "true");
}

if($SANDBOX == true) {
	echo("Using sandbox. </br>");
}
else {
	echo("NOT using sandbox. </br>");
}

echo "Filter tasks with the URL param \"title\" </br>";
if (isset($_REQUEST['title']))    
{    
	$hitTitle = $_REQUEST['title'];
	echo("Only displaying hits with the title " . '"' . $hitTitle . '".');

}    

$hitIDs[0] = 0;

//$search = turk_easyGetReviewable();
//print_r($search);


$msg="";
if( isset($_REQUEST['action'])) {
		
	switch ($_REQUEST['action']) {
		case 'createHit':
			$loop = max(1,$_REQUEST['numberHits']);
			$loop = min($loop,100);
			
			$msg= $loop." hit(s) created.";
			while ($loop >0) {
				turk50_hit($_REQUEST['title'],$_REQUEST['description'],$_REQUEST['price'], $_REQUEST['url'], $_REQUEST['duration'], $_REQUEST['lifetime']);
				$loop--;
			}
			break;

			case'expireAll':
				$msg= 'All asssignable hits expired and disposed of.';
				$array = turk50_searchAllHits();
				if(isset($hitTitle) && isset($num))
				{	 
					foreach($array as $hit)
					{
						if($hit->Title != $hitTitle) unset($array[$num]);
					}
				}
			
				foreach($array as $hit) {
					if ($hit->HITStatus == 'Assignable' && ((isset($hitTitle) && $hit->Title == $hitTitle) || !(isset($hitTitle)))) {
						turk_easyExpireHit($hit->HITId);
						turk_easyDispose($hit->HITId);
					}
				}
			break;


		default:
			break;
	}
	
	//die('action performed.');
}

?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>
<head>
	<title> Overview - What was that?</title>
	<!-- 
<script type="text/javascript" src="scripts/jquery-1.6.1"></script>
	<script type="text/javascript" src="scripts/jqueryui-1.8.13.min.js"></script>
	<link type="text/css" href="css/redmond/jquery-ui-1.8.13.custom.css" rel="stylesheet" />	
 -->
 
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
 <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
 <script src="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"></script>

</head>

<body>


	<script type="text/javascript">
		
		$(function() {
			$(".approveLink").click(function() {
				$(this).parent().find('.approveDiv').slideToggle();
			
			});
		
		});
		
	</script>
		
	<style>
		div {
			padding-bottom:30px;
		}
	</style>






<br />
<h1>Create Hits</h1>
<h3 style="color:blue"><?=$msg;?></h3>

<div>
	
	<form action="" method="post">
		<br />
		<input type="hidden" name="action" value="createHit">
		Title: <input type ="text" size="40" name="title">
		<br />
		Description: <input type ="text" size="50" name="description">
		<br />
	URL: <input type ="text" name="url" size="45" value="http://roc.cs.rochester.edu/tagging/tag.php">
		<br />
		Price Each: <input type ="text" name="price" size="4" value='.01'>
		<br />
		Number of Hits: <input type="text" size="2" name="numberHits" value="1" />
		<br />
		Duration of Hits in seconds: <input type="text" size="4" name="duration" value="300" />
		<br />
		Lifetme of Hits in seconds: <input type="text" size="5" name="lifetime" value="86400" />
		<br />
		Create a hit on Mechanical Turk: <input type="submit" value="Create Hit">
	</form>

	

</div>

<?
 //$array = turk_easySearchHits();
 $array = turk50_searchAllHits();
 //print_r($array);

if(isset($hitTitle) && is_array($array))
{	 
	foreach($array as $num=>$hit)
	{
		if($hit->Title != $hitTitle) unset($array[$num]);
		else array_push($hitIDs, $hit->HITId);
	}
}
                             
echo "<h1>".sizeof($array)." pending hits on your account</h1>";                                

  // [0] => Array
        // (
            // [HITId] => 2162L92HECWAL6LOZY3F5CP5FL9HIL
            // [HITTypeId] => 2TZSK9RJ85OZX8W7XW1PROOQ50OU6P
            // [CreationTime] => 2011-10-14T20:10:26Z
            // [Title] => Boring task for cheap
            // [Description] => Seriously this sucks
            // [HITStatus] => Unassignable
            // [MaxAssignments] => 1
            // [Reward] => Array
                // (
                    // [0] => Array
                        // (
                            // [Amount] => 0.01
                            // [CurrencyCode] => USD
                            // [FormattedPrice] => $0.01
                        // )

                // )

            // [AutoApprovalDelayInSeconds] => 2592000
            // [Expiration] => 2011-10-15T20:10:26Z
            // [AssignmentDurationInSeconds] => 300
            // [NumberOfAssignmentsPending] => 1
            // [NumberOfAssignmentsAvailable] => 0
            // [NumberOfAssignmentsCompleted] => 0

if(is_array($array)){
	foreach($array as $hit) {
	//	print_r($array);
		echo "<u>".$hit->Title."</u> with ".$hit->MaxAssignments."/".$hit->NumberOfAssignmentsPending."/".$hit->NumberOfAssignmentsAvailable." assignments. ---> <i>".$hit->HITStatus."</i>: ".$hit->Reward->FormattedPrice."\n<br />\n";
	}
}
?>
<br />
	<form action="" method="post">
		<input type="hidden" name="action" value="expireAll">
		Force early expire of all live hits:	<input type="submit" value="Expire">
	</form>
<p>max/pending/completed</p>
<p>Note: You can only expire/dispose of hits that are still "assignable".</p>
<p>Note: Unassignable means somebody is currently working on it.</p>

<h1> Review Hits</h1>
<?
	include 'turk/reviewHits.php';

?>



</body>
</html>
