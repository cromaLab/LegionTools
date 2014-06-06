<?
	require_once 'mturk.lib.php';

	require_once("Turk50.php");
	// require_once("../amtKeys.php");
	// require_once("../isSandbox.php");

	/*
	   var $validOps   = array("ApproveAssignment", "CreateHIT", "CreateQualificationType", "DisableHIT", "DisposeHIT", "ExtendHIT",
	   "GetAccountBalance", "GetAssignmentsForHIT", "GetHIT", "GetQualificationRequests", "GetQualificationScore",
	   "GetQualificationType", "GetRequesterStatistic", "GetReviewableHITs", "GrantQualification", "Help", "NotifyWorkers",
	   "RejectAssignment", "SearchQualificationTypes", "UpdateQualificationScore", "UpdateQualificationType",
	   "SetHITAsReviewing", "RegisterHITType", "SearchHITs", "ForceExpireHIT", "SetHITTypeNotification", "SendTestEventNotification",
	   "GrantBonus", "GetFileUploadURL", "RejectQualificationRequest", "GetQualificationsForQualificationType");
	*/   


	function turk_debug($mt) {
		echo "<br /><br />\n\nRawData<br />\n".$mt->RawData."\n\n<br /><br />";
		echo "<br /><br />\n\nSOAPData<br />\n".$mt->SOAPData."\n\n<br /><br />";

		echo $mt->Fault;
		echo $mt->Error;	
	}


	// function turk_easyHit($title,$description,$money,$url,$duration,$lifetime) {
	// 	//turk_easyHit('donato sample','description',.05,'http://roc.cs.rochester.edu/donato/tukerpage.php');	   
	// 		global $SANDBOX, $DEBUG, $AccessKey, $SecretKey;


	// 	   $mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

	// 	   $mt->SetOperation('CreateHIT');
	// 	   $mt->SetVar('Title', $title);
	// 	   $mt->SetVar('Description',$description);
	// 	   $mt->SetVar('Amount',$money);
	// 	   $mt->SetVar('MaxAssignments',1);
	// 	   $mt->SetVar('AssignmentDurationInSeconds',$duration);
	// 	   $mt->SetVar('LifetimeInSeconds',$lifetime);
	// 	   $mt->SetVar('Question',$url);
	// 	   $mt->Invoke();

	// 	  turk_debug($mt);
	// }

    function turk50_hit($title,$description,$money,$url,$duration,$lifetime,$qualification) {
    	global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
    	
    	if($SANDBOX)
    		$turk50 = new Turk50($AccessKey, $SecretKey);
    	else
    		$turk50 = new Turk50($AccessKey, $SecretKey, array("sandbox" => FALSE));

    	// prepare ExternalQuestion
    	$Question =
    	 "<ExternalQuestion xmlns='http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2006-07-14/ExternalQuestion.xsd'>" .
    	 "<ExternalURL>$url</ExternalURL>" .
    	 "<FrameHeight>600</FrameHeight>" .
    	 "</ExternalQuestion>";

    	// prepare Request
    	$Request = array(
    	 "Title" => $title,
    	 "Description" => $description,
    	 "Question" => $Question,
    	 "Reward" => array("Amount" => $money, "CurrencyCode" => "USD"),
    	 "AssignmentDurationInSeconds" => $duration,
    	 "LifetimeInSeconds" => $lifetime,
         "QualificationRequirement" => $qualification
    	);

    	// invoke CreateHIT
    	$CreateHITResponse = $turk50->CreateHIT($Request);
        // $hitId = $CreateHITResponse->HIT->HITId;
        // $assignId = turk_easyHitToAssn($hitId);
        return $CreateHITResponse;
    	
    }



	function turk_easyApprove($asn, $encouragement="") {
			//venar303@gmail.com account
	   global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;


		$mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

		$mt->SetOperation('ApproveAssignment');
		$mt->SetVar('AssignmentId', $asn);
		//$mt->SetVar('RequesterFeedback', $encouragement);

		$result = $mt->Invoke();

		if (!$mt->FinalData['Request']['IsValid'] ) {
			echo "error with Approval";
			print_r($mt);
		}
		return $mt;
	}	

	function turk_easyReject($asn, $encouragement="") {
			//venar303@gmail.com account
	   global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;


		$mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

		$mt->SetOperation('RejectAssignment');
		$mt->SetVar('AssignmentId', $asn);
		//$mt->SetVar('RequesterFeedback', $encouragement);

		$result = $mt->Invoke();

		if (!$mt->FinalData['Request']['IsValid'])
			echo "error with Rejection";
		return $mt;
	}


	function turk_easyBonus($worker_id, $assignment_id, $bonus, $reason) { 

		global $SANDBOX, $DEBUG, $AccessKey, $SecretKey;
		$mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

		$mt->SetOperation('GrantBonus');
		$mt->SetVar('AssignmentId', $assignment_id);
		$mt->SetVar('BonusAmount',$bonus);
		$mt->SetVar('Reason',$reason);
		$mt->SetVar('WorkerId',$worker_id);

		$mt->Invoke();
		return $mt;
	}


	function turk_easyDispose($hitId) {
			//venar303@gmail.com account
	   global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;


		$mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

		$mt->SetOperation('DisposeHIT');
		$mt->SetVar('HITId', $hitId);
		$result = $mt->Invoke();
		if (!$mt->FinalData['Request']['IsValid'])
			echo "error with disposal";

		return $mt;
	}

	function turk_easySearchHits() {

           global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
           
                $mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

                $mt->SetOperation('SearchHITs');
                $mt->SortProperty = "CreationTime";
                $mt->SortDirection = "Descending";
                $mt->setVar('PageSize',100);
                $result = $mt->Invoke();
				//print_r($mt->FinalData);
                return $mt->FinalData['HIT'];
        }
        
    function turk50_search($pageNum) {
    	global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
    	
    	if($SANDBOX)
    		$turk50 = new Turk50($AccessKey, $SecretKey);
    	else
    		$turk50 = new Turk50($AccessKey, $SecretKey, array("sandbox" => FALSE));

    	$Request = array(
	 		"PageSize" => 100,
	 		"SortProperty" => "Enumeration",
	 		"PageNumber" => $pageNum
		);
    	
    	$search = $turk50->SearchHITs($Request);
    	//print_r($search);
    	if($search->SearchHITsResult->NumResults == 1)return array($search->SearchHITsResult->HIT);
    	if($search->SearchHITsResult->NumResults > 0)return $search->SearchHITsResult->HIT;
    	
    }
    
    function turk50_searchReviewable($pageNum) {
    	global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
    	
    	if($SANDBOX)
    		$turk50 = new Turk50($AccessKey, $SecretKey);
    	else
    		$turk50 = new Turk50($AccessKey, $SecretKey, array("sandbox" => FALSE));

    	$Request = array(
	 		"PageSize" => 100,
	 		"SortProperty" => "Enumeration",
	 		"PageNumber" => $pageNum
		);
    	
    	$search = $turk50->GetReviewableHITs($Request);
    	//print_r($search);
    	if($search->GetReviewableHITsResult->NumResults == 1) return array($search->GetReviewableHITsResult->HIT);
    	if($search->GetReviewableHITsResult->NumResults > 0) return $search->GetReviewableHITsResult->HIT;
    	
    }
    
    function turk50_getNumHits() {
    	global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
    	
    	if($SANDBOX)
    		$turk50 = new Turk50($AccessKey, $SecretKey);
    	else
    		$turk50 = new Turk50($AccessKey, $SecretKey, array("sandbox" => FALSE));

    	$Request = array(
	 		"PageSize" => 100,
	 		"SortProperty" => "Enumeration"
		);
    	
    	$search = $turk50->SearchHITs($Request);
    	//print_r($search);
    	return $search->SearchHITsResult->TotalNumResults;
    	
    }
    
    function turk50_getNumReviewableHits() {
    	global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
    	
    	if($SANDBOX)
    		$turk50 = new Turk50($AccessKey, $SecretKey);
    	else
    		$turk50 = new Turk50($AccessKey, $SecretKey, array("sandbox" => FALSE));

    	$Request = array(
	 		"PageSize" => 100,
	 		"SortProperty" => "Enumeration"
		);
    	
    	$search = $turk50->GetReviewableHITs($Request);
    	//print_r($search);
    	return $search->GetReviewableHITsResult->TotalNumResults;
    	
    }
    
    function turk50_searchAllHits() {
    	global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
    	
    	$totalNumHits = turk50_getNumHits();
    	$numPages = ceil($totalNumHits / 100);
    	$array = turk50_search(1);
    	for($i = 2; $i <= $numPages; $i++)
    	{
    		$array = array_merge($array, turk50_search($i));
    	}
    	
    	return $array;
    }
    
    function turk50_getAllReviewableHits() {
    	global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
    	
    	$totalNumHits = turk50_getNumReviewableHits();
    	$numPages = ceil($totalNumHits / 100);
    	$array = turk50_searchReviewable(1);
    	for($i = 2; $i <= $numPages; $i++)
    	{
    		$array = array_merge($array, turk50_searchReviewable($i));
    	}
    	
    	return $array;
    }


      function turk_easyExpireHit($hitId) {

           global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;

                $mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

                $mt->SetOperation('ForceExpireHIT');
                $mt->setVar('HITId',$hitId);
                $result = $mt->Invoke();

				print_r($mt->FinalData);
        }

	function turk_easyGetReviewable() {
	   global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;

		$mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

		$mt->SetOperation('GetReviewableHITs');
		$mt->setVar('Status',"Reviewable");
		$mt->setVar('PageSize',100);
		$result = $mt->Invoke();

		if (isset ($mt->FinalData['HIT']))
			$x = sizeOf($mt->FinalData['HIT']);
		else
			die("No hits to review");
		$tempArray = array();
		for ($i = 0; $i<$x ; $i++) {
			if (isset($mt->FinalData['HIT'][$i]))
				$val = $mt->FinalData['HIT'][$i]['HITId'][$i];
			else
				$val = $mt->FinalData['HIT']['HITId'];
			$tempArray[] = $val;
		}
		return $tempArray;
	}

	function turk_easyGetReviewing() {
           global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;

                $mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

                $mt->SetOperation('GetReviewableHITs');
                $mt->setVar('Status',"Reviewing");
		$mt->setVar('PageSize',100);
                $result = $mt->Invoke();

                if (isset ($mt->FinalData['HIT']))
                        $x = sizeOf($mt->FinalData['HIT']);
                else
                        die("No hits to review");
                $tempArray = array();
                for ($i = 0; $i<$x ; $i++) {
                        if (isset($mt->FinalData['HIT'][$i]))
                                $val = $mt->FinalData['HIT'][$i]['HITId'][$i];
                        else
                                $val = $mt->FinalData['HIT']['HITId'];
                        $tempArray[] = $val;
                }
                return $tempArray;
        }

	function turk_easyHitToAssn($hit) {
		global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;

		$mt = new mturkinterface($AccessKey, $SecretKey, $SANDBOX);

		$mt->SetOperation('GetAssignmentsForHIT');
		$mt->SetVar('HITId', $hit);
		$mt->Invoke();

		return $mt->FinalData;
	}

    function turk50_getHit($hitId){
        global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
        
        if($SANDBOX)
            $turk50 = new Turk50($AccessKey, $SecretKey);
        else
            $turk50 = new Turk50($AccessKey, $SecretKey, array("sandbox" => FALSE));

        $Request = array(
            "HITId" => $hitId
        );

        return $turk50->GetHIT($Request);
    }

    function turk50_getAccountBalance(){
        global $DEBUG, $SANDBOX, $AccessKey ,$SecretKey;
        
        if($SANDBOX)
            $turk50 = new Turk50($AccessKey, $SecretKey);
        else
            $turk50 = new Turk50($AccessKey, $SecretKey, array("sandbox" => FALSE));

        $Request = array(
            "HITId" => $hitId
        );

        return $turk50->GetAccountBalance();
    }


?>