/**
 * Called when list of HITs was loaded and approve button is clicked
 * @param assignmentId Assignment to approve
 * @param hitId Overall HIT ID
 * @param id
 * @param bonus Bonus if you want to pay it
 * @param workerId ID of worker that completed assignment
 */
function approveHit(assignmentId, hitId, id, bonus, workerId){
	$.ajax({
	    url: "Retainer/php/processHIT.php",
	    type: "POST",
	    async: true,
	    data: {id: assignmentId, operation: "Approve", useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
	    success: function(d) {
	        // alert(d);
	    },
	    fail: function() {
	        alert("Sending number of workers failed [approve]");
	    }
	});

	// Bonus via AJAX call to processHIT.php
	if(bonus > 0){
		$.ajax({
		    url: "Retainer/php/processHIT.php",
		    type: "POST",
		    async: true,
		    data: {id: assignmentId, operation: "Bonus", useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(), amount: bonus, workerId: workerId},
		    success: function(d) {
		        // alert(d);
		    },
		    fail: function() {
		        alert("Sending number of workers failed [bonus]");
		    }
		});
	}
	
	//replaceWithDisposeButton(assignmentId, hitId, id);
    $("#" + id + " .approveButton," + " #" + id + " .rejectButton").fadeOut( function() { $(this).remove(); });
    $("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>");
	
}

/**
 * Called when list of HITs was loaded and reject button is clicked
 * @param assignmentId Assignment to reject
 * @param hitId Overall HIT ID
 * @param id
 */
function rejectHit(assignmentId, hitId, id){
	$.ajax({
	    url: "Retainer/php/processHIT.php",
	    type: "POST",
	    async: true,
	    data: {id: assignmentId, operation: "Reject", useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
	    success: function(d) {
	        // alert(d);
	    },
	    fail: function() {
	        alert("Sending number of workers failed [reject]");
	    }
	});

	//replaceWithDisposeButton(assignmentId, hitId, id);
    $("#" + id + " .approveButton," + " #" + id + " .rejectButton").fadeOut( function() { $(this).remove(); });
    $("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button><button type='button' onclick = 'unrejectHit(&quot;" + assignmentId + "&quot;, &quot;" + null + "&quot;, &quot;" + id + "&quot;)' class='approveButton btn btn-warning btn-sm'>Unreject</button>");
}

/**
 * Removes HIT from list
 * @param hitId
 * @param id
 */
function disposeHit(hitId, id){
	$.ajax({
	    url: "Retainer/php/processHIT.php",
	    type: "POST",
	    async: true,
	    data: {id: hitId, operation: "Dispose", useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
	    success: function(d) {
	        $(id).remove();
	        $("#" + id).fadeOut( function() { $(this).remove(); });
	    },
	    fail: function() {
	        alert("Sending number of workers failed [dispose]");
	    }
	});

}

/**
 * Approves HIT after it has been rejected ("unreject")
 * @param assignmentId ID of assignment to unreject
 * @param workerId ID of worker who was rejected earlier
 * @param listId
 */
function unrejectHit(assignmentId, workerId, listId){
    $("#" + listId + " .approveButton").remove();
    $.ajax({
        url: "Retainer/php/processHIT.php",
        type: "POST",
        async: true,
        data: {id: assignmentId, workerId: workerId, operation: "Unreject", useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        success: function(d) {
            //alert("Sending number of workers succeeded [unreject]");
            $("#" + listId + ".approveButton").fadeOut( function() { $(this).remove(); });
        },
        fail: function() {
            //alert("Sending number of workers failed [unreject]");
        }
    });
}

// function replaceWithDisposeButton(assignmentId, hitId, id){
// 	$("#" + id + " .approveButton," + " #" + id + " .rejectButton").fadeOut( function() { $(this).remove(); });
// 	$("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>");//<button type='button' onclick = 'unrejectHit(&quot;" + assignmentId + "&quot;, &quot;" + null + "&quot;, &quot;" + id + "&quot;)' class='approveButton btn btn-warning btn-sm'>Unreject</button>
// }



