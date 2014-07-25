<?
	require_once 'turk_functions.php';
	require_once 'mturk.lib.php';
	
	function checkHITs($tempArray)
	{
		if(isset($_REQUEST['title']) && is_array($tempArray))
		{
			global $hitIDs;
			print_r($hitIDs);
			foreach($tempArray as $hitId)
			{
				if(!(in_array($hitId->HITId, $hitIDs)))
				{
					if(($key = array_search($hitId, $tempArray)) !== false) 
					{
						unset($tempArray[$key]);
					}
				}
			}
		}
	}

	
if (isset($_REQUEST['action'])) {

	switch($_REQUEST['action']) {
	
		case 'Approve':
			turk_easyApprove($_REQUEST['assignment_id']);
		break;	
		case 'Reject':
			turk_easyReject($_REQUEST['assignment_id']);
		break;	
		case 'dispose':
			//echo "disposing hit: ".$_REQUEST['hit_id'];
			turk_easyDispose($_REQUEST['hit_id']);
		break;
		case 'approveAndDisposeAll':
			$hits = turk50_getAllReviewableHits();
			checkHITs($hits);
			if( $hits ) {
				foreach($hits as $hit){
					$data = turk_easyHitToAssn($hit->HITId);
					$assign = $data['Assignment']['AssignmentId'];
					turk_easyApprove($assign);
					echo("<p>Just Approved and Disposed HIT: ".$hit->HITId."</p>");
					turk_easyDispose($hit->HITId);
				}
			}
		break;
		case 'disposeAll':
			$hits = turk50_getAllReviewableHits();
			checkHITs($hits);
			foreach($hits as $hit){
				turk_easyDispose($hit->HITId);
				echo("<p>Just Disposed HIT: ".$hit->HITId."</p>");
			}
		break;
		/*case 'approveAll':
			$hits = turk_easyGetReviewable();
			foreach($hits as $hit){
				turk_easyApprove($hit);
				echo("<p>Just Approved HIT: ".$hit."</p>");
			}
		break;*/
		case 'bonus':
			$mt = turk_easyBonus($_REQUEST['worker_id'], $_REQUEST['assignment_id'], $_REQUEST['bonus'], $_REQUEST['reason']);
			//turk_debug($mt);
		break;
		case 'bonusAndApprove':
			$mt = turk_easyBonus($_REQUEST['worker_id'], $_REQUEST['assignment_id'], $_REQUEST['bonus'], $_REQUEST['reason']);
			turk_easyApprove($_REQUEST['assignment_id']);
			//turk_debug($mt);
		break;
	}
}
?>
		<form method='post'>
			<input type='submit' name='action' value='disposeAll' />
		</form>
		
		<form method='post'>
			<input type='submit' name='action' value='approveAndDisposeAll' />
		</form>

		<table>
		<?
		$tempArray = turk50_getAllReviewableHits();
		//echo print_r($tempArray);
		checkHITs($tempArray);
		//echo print_r($tempArray);
		
		if(is_array($tempArray)){
		foreach($tempArray as $hitId) {
			$data = turk_easyHitToAssn($hitId->HITId);
			// print_r($data);
			//echo "<tr><td><input type='checkbox' class='hitBox' name='hitBox' value='".$data['Assignment']['AssignmentId']."' /> ".$data['Assignment']['WorkerId']."</td><td>".$data['Assignment']['AssignmentId']."</td>";
			//echo "<td>Assignments: ".$data['TotalNumResults'];
			//print_r($mt->FinalData);
			if ($data['TotalNumResults'] > 0)
				if ($data['Assignment']['AssignmentStatus']=='Approved') {
					?>
					<td>
						<span class='approveLink' style='color:green;'>Approved [click]	</span>	<?php
						$xml = simplexml_load_string($data['Assignment']['Answer']); //gets xml containing bonus amount
						echo "Session name: " . $xml->Answer[0]->FreeText;?>
							<div style='display:none;' class='approveDiv'>
								<form method='post'>
									Bonus: <input type="text" size=4 name='bonus' value='<?php 
									$xml = simplexml_load_string($data['Assignment']['Answer']); //gets xml containing bonus amount
									$bonus = $xml->Answer->FreeText;
									if(is_numeric($bonus)) echo $bonus;
									echo $bonus;
									//echo $data['Assignment']['Answer'];
									?>'  />
									<br />
									Explanation: <input type="text" name='reason' value='Submitted extra work' />
									<br />
									<input type='hidden' name='worker_id' value='<?=$data['Assignment']['WorkerId'];?>' />
									<input type='hidden' name='assignment_id' value='<?=$data['Assignment']['AssignmentId'];?>' />
									<!---<input type='submit' name='action' value='approve' /> --->
									<input type='submit' name='action' value='bonus' />
								</form>
								
							</div>
								
				
				
					</td>
					<?
				}
				elseif ($data['Assignment']['AssignmentStatus']=="Submitted") {
					?>
					<td>
						<span class='approveLink' style='color:blue;'>Needs Approval [click]</span> <?php
						$xml = simplexml_load_string($data['Assignment']['Answer']); //gets xml containing bonus amount
						echo "Session name: " . $xml->Answer[0]->FreeText;
						echo "    workerId: " . $data['Assignment']['WorkerId'];?>
						<div style='display:none;' class='approveDiv'>
							<form method='post'>
								Bonus: <input type="text" size=4 name='bonus' value='<?php 
									$bonus = $xml->Answer[1]->FreeText;
									echo $bonus;
									//echo $data['Assignment']['Answer'];
									?>'  />
								<br />
								Explanation: <input type="text" name='reason' value='Submitted extra work' />
								<br />
								<input type='hidden' name='worker_id' value='<?=$data['Assignment']['WorkerId'];?>' />
								<input type='hidden' name='assignment_id' value='<?=$data['Assignment']['AssignmentId'];?>' />
								<input type='submit' name='action' value='Approve' />
								<input type='submit' name='action' value='Reject' />
								<input type='submit' name='action' value='bonus' />
								<input type='submit' name='action' value='bonusAndApprove' />

							</form>
							
						</div>
						
					</td>
					<?
				} elseif ($data['Assignment']['AssignmentStatus']=='Rejected') {
					?>
					<td>
						<span class='approveLink' style='color:red;'>Rejected [click]	</span>	
							<div style='display:none;' class='approveDiv'>
							</div>
								
				
				
					</td>
					<?
				}

			else 
				echo "<td></td>";
			echo "</td>";
			echo "<td><form method='post'><input type='hidden' name='action' value='dispose' /><input type='hidden' name='hit_id' value='$hitId->HITId' /><input type='submit' value='dispose' /></form></td>";
			echo "</tr>";
		}
		}
?>
