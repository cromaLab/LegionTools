/**
 * Called when list of HITs was loaded and approve button is clicked
 * @param assignmentId Assignment to approve
 * @param hitId Overall HIT ID
 * @param id
 * @param bonus Bonus if you want to pay it
 * @param workerId ID of worker that completed assignment
 */
function approveHit(assignmentId, workerId, hitId, id, bonus, workerId){
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
	
	// Remove approve and reject button
    $("#" + id + " .approveButton," + " #" + id + " .rejectButton").fadeOut( function() { $(this).remove(); });

    // Add bonus buttons
    // $("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button><button type='button' onclick = 'bonusHit(&quot;" + assignmentId + "&quot;, &quot;" + null + "&quot;, &quot;" + id + "&quot;, &quot;" + bonusAmount + "&quot;)' class='approveButton btn btn-warning btn-sm'>Bonus $" + bonusAmount + "</button>");

    $.ajax({
        url: 'Retainer/api/api.php/bonus/get',
        type: 'POST',
        async: false,
        data: {useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(),
            workerId: workerId, assignmentId: assignmentId},
        success: function(data, status) {
            console.log("Bonus successfully extracted: ");

            var returnValue = JSON.parse(data); // JavaScript parser (JQuery might be $.parseJSON(data);)
            bonusAmount = returnValue['bonusAmount'];
            if (isNaN(returnValue['bonusAmount'])) bonusAmount = 0.00;

            // Use extracted bonus suggestion if extraction was successful
            $("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>&nbsp;&nbsp;<button type='button' onclick = 'showBonusModal(&quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + id + "&quot;, &quot;" + bonusAmount + "&quot;)' class='bonusModalButton btn btn-warning btn-sm'>Edit Bonus</button>&nbsp;&nbsp;<button type='button' onclick = 'bonusImmediately(&quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + id + "&quot;, &quot;" + bonusAmount + "&quot;)' class='bonusQuickButton btn btn-warning btn-sm'>Bonus: $" + bonusAmount + "</button>");

        },
        error: function(xhr, desc, err) {
            // Should never happen (if no DB entry bonus/get should return 0.00)

            console.log("Error: Could not extract bonus, using default.");
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);

            $("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>");

        }
    });

    // // Fetch (old version that grabbed bonusAmount from bonusSuggestion field in return value)
    // $.ajax({
    //     url: 'Retainer/php/BonusHelper.php',
    //     type: 'POST',
    //     data: {
    //         'action': 'fetchBonus',
    //         assignmentId: assignmentId,
    //         workerId: workerId,
    //         useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()
    //     },
    //     success: function (bonusAmount) {
    //         console.log("Success: Fetched bonus");
    //         // data should now contain bonusAmount
    //         if(isNaN(bonusAmount)) bonusAmount = 0.00;
    //         console.log(bonusAmount);
    //
    //         // Use extracted bonus suggestion if extraction was successful
    //         $("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>&nbsp;&nbsp;<button type='button' onclick = 'showBonusModal(&quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + id + "&quot;, &quot;" + bonusAmount + "&quot;)' class='bonusModalButton btn btn-warning btn-sm'>Edit Bonus</button>&nbsp;&nbsp;<button type='button' onclick = 'bonusImmediately(&quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + id + "&quot;, &quot;" + bonusAmount + "&quot;)' class='bonusQuickButton btn btn-warning btn-sm'>Bonus: $" + bonusAmount + "</button>");
    //
    //     },
    //     error: function (xhr, desc, err) {
    //         console.log("Error: Could not fetch bonus, using default.");
    //         console.log(xhr);
    //         console.log("Details: " + desc + "\nError:" + err);
    //
    //         $("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>");
    //     }
    // });
}

/**
 * Called when list of HITs was loaded and reject button is clicked
 * @param assignmentId Assignment to reject
 * @param hitId Overall HIT ID
 * @param id
 */
function rejectHit(assignmentId, workerId, hitId, id){
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
    $("#" + id).append("&nbsp;&nbsp;<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>&nbsp;&nbsp;<button type='button' onclick = 'unrejectHit(&quot;" + assignmentId + "&quot;, &quot;" + null + "&quot;, &quot;" + id + "&quot;)' class='approveButton btn btn-warning btn-sm'>Unreject</button>");
}

/**
 * Removes HIT from list
 * @param hitId
 * @param id
 */
function disposeHit(hitId, assignmentId, workerId, id){
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

	// Attempt deletion of DB bonus entry that is now not needed anymore
    $.ajax({
        url: 'Retainer/api/api.php/bonus/delete/',
        type: 'POST',
        data: {
            assignmentId: assignmentId, workerId: workerId,
            useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()
        },
        success: function (data, status) {
            console.log("Success: Bonus entry deleted");

        },
        error: function (xhr, desc, err) {
            console.log("Error: Could not delete DB entry on dispose.");
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
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

// $("#debugButton").on("click", function(event) {
//     console.log("Button create user clicked")
//     // $('#bonusModal').modal({
//     //     backdrop: 'static',
//     //     keyboard: false
//     // });
//     $('#bonusModal').modal('show');
// });

// Shows modal with prepopulated values
function showBonusModal(assignmentId, workerId, listId, bonusAmount) {
	console.log("Showing bonusing modal.")

    $.ajax({
        url: 'Retainer/api/api.php/bonus/get',
        type: 'POST',
        async: false,
        data: {useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(),
            workerId: workerId, assignmentId: assignmentId},
        success: function(data, status) {
            console.log("Bonus successfully extracted: ");

            var returnValue = JSON.parse(data); // JavaScript parser (JQuery might be $.parseJSON(data);)
            bonusAmount = returnValue['bonusAmount'];
            if (isNaN(returnValue['bonusAmount'])) bonusAmount = DEFAULT_BONUS;

            // Show dialog box
            $('#bonusModal').modal('show');

            // Prepopulate fields
            document.getElementById('modalBonusAmount').value = bonusAmount;
            document.getElementById('modalBonusAssignmentId').value = assignmentId;
            document.getElementById('modalBonusWorkerId').value = workerId;

        },
        error: function(xhr, desc, err) {
            // Should never happen (if no DB entry bonus/get should return 0.00)

            console.log("Error: Could not extract bonus, using default.");
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);

            // Show dialog box
            $('#bonusModal').modal('show');

            // Prepopulate fields
            document.getElementById('modalBonusAmount').value = 0.00;
            document.getElementById('modalBonusAssignmentId').value = assignmentId;
            document.getElementById('modalBonusWorkerId').value = workerId;
        }
    });


}

function bonusImmediately(assignmentId, workerId, listId, bonusAmount) {

        console.log("Quick bonus button clicked");

        console.log("Calling AJAX for bonus.");
        $.ajax({
            url: 'Retainer/php/BonusHelper.php',
            type: 'POST',
            data: {
                'action': 'executeBonus', bonusAmount: bonusAmount,
                assignmentId: assignmentId,
                workerId: workerId,
                bonusReason: "Automatic bonus for a great job.",
                useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()
            },
            success: function (data, status) {
                console.log("Success: Automatic bonus sent");

                console.log("List ID for current bonus: " + listId);

                // // .:: Update bonus buttons
                // // Remove old bonus button
                // $("#" + listId + " .bonusQuickButton").remove();
                // // Append new bonus buttons with bonus value 0.00
                // $("#" + listId).append("<button type='button' onclick = 'bonusImmediately(&quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + 0.00 + "&quot;)' class='bonusQuickButton btn btn-warning btn-sm'>Bonus: $0.00</button>");

            },
            error: function (xhr, desc, err) {
                console.log("Error: Could not send bonus, using default.");
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });

    $.ajax({
        url: 'Retainer/api/api.php/bonus/delete/',
        type: 'POST',
        data: {
            assignmentId: assignmentId, workerId: workerId,
            useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()
        },
        success: function (data, status) {
            console.log("Success: Bonus entry deleted");
            console.log("List ID for current bonus: " + listId);

            // .:: Update bonus buttons
            // Remove old bonus button
            $("#" + listId + " .bonusQuickButton").remove();
            $("#" + listId + " .bonusQuickButton").remove();
            // Append new bonus buttons with bonus value 0.00
            $("#" + listId).append("<button type='button' onclick = 'bonusImmediately(&quot;" + assignmentId + "&quot;, &quot;" + workerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + 0.00 + "&quot;)' class='bonusQuickButton btn btn-warning btn-sm'>Bonus: $0.00</button>");

        },
        error: function (xhr, desc, err) {
            console.log("Error: Could not send bonus, using default.");
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
        }
    });
}

// function replaceWithDisposeButton(assignmentId, hitId, id){
// 	$("#" + id + " .approveButton," + " #" + id + " .rejectButton").fadeOut( function() { $(this).remove(); });
// 	$("#" + id).append("<button type='button' onclick = 'disposeHit(&quot;" + hitId + "&quot;, &quot;" + id + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>");//<button type='button' onclick = 'unrejectHit(&quot;" + assignmentId + "&quot;, &quot;" + null + "&quot;, &quot;" + id + "&quot;)' class='approveButton btn btn-warning btn-sm'>Unreject</button>
// }



