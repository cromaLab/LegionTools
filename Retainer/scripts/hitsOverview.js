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
	        alert("Sending number of workers failed");
	    }
	});

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
		        alert("Sending number of workers failed");
		    }
		});
	}
	
	replaceWithDisposeButton(hitId, id);
	
}

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
	        alert("Sending number of workers failed");
	    }
	});

	replaceWithDisposeButton(hitId, id);
}

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
	        alert("Sending number of workers failed");
	    }
	});

}

function replaceWithDisposeButton(hitId, id){
	$("#" + id + " .approveButton," + " #" + id + " .rejectButton").fadeOut( function() { $(this).remove(); });
	$("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>");

}



