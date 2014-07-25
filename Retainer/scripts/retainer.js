$(document).ready(function() {
    var worker = gup("workerId");
    var assignment = gup("assignmentId");
    var task = gup('task') ? gup('task') : "default";

    var isBanned = false;
    //checks if worker is banned
    $.ajax({
        async: false,
        url: "php/checkBanned.php",
        data: {workerId: worker},
        dataType: "text",
        success: function(d){
            if(d > 0){
            isBanned = true;
                alert("You are banned from this task.");
            }
            else{
             isBanned = false;
        }
        },
        fail: function(){
            alert("isBanned failed!");
        },
    });

    var isAllowed = true;
    // checks if worker is already waiting for another task
    $.ajax({
    	async: false,
    	url: "php/isWorkerActive.php",
    	data: {workerId: worker},
    	dataType: "text",
    	success: function(d){
            // alert(d);
    		if(d == 1){
			isAllowed = false;
    			alert("You are already waiting for another task.");
                $("body").html("<h3>Sorry, you cannot wait for multiple tasks at the same time.</h3>")
    		}
    		else{
			 isAllowed = true;
		}
    	},
    	fail: function(){
    		alert("isWorkerActive failed!");
    	},
    });
    //alert("1 before stop");
    if( assignment != "ASSIGNMENT_ID_NOT_AVAILABLE" && isAllowed == true && isBanned == false) {
        $.ajax({
            url: "php/setLive.php",
            data: {workerId: worker, task: task},
            dataType: "text",
            success: function(d) {
		url = "wait.php";
		//alert(url);
		url += "?workerId=" + gup('workerId');
                url += "&assignmentId=" + gup('assignmentId');
                url += "&hitId=" + gup('hitId');
                url += "&turkSubmitTo=" + gup('turkSubmitTo');
                url += "&task=" + gup('task');
                url += "&min=" + gup('min');
                url += "&instructions=" +  gup('instructions');

		window.location = url;
            },
            fail: function() {
                alert("setLive failed!")
            },
        });
    }
});
