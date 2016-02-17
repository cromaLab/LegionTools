$(document).ready(function() {
    var worker = gup("workerId");
    var assignment = gup("assignmentId");
    var task = gup('task') ? gup('task') : "default";

    var isBanned = false;
    //checks if worker is banned
    $.ajax({
        async: false,
        url: "php/checkBanned.php",
        data: {workerId: worker, dbName: gup('dbName')},
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
        data: {workerId: worker, dbName: gup('dbName')},
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
            data: {workerId: worker, task: task, dbName: gup('dbName')},
            dataType: "text",
            success: function(d) {
                //url = "wait.php";
                //alert(url);

                // at this point, "Accept HIT" button on MTurk was clicked on
                // IF this workerId already finished the tutorial, send directly to wait.php
                // otherwise, send to third party url

                $.ajax({
                    url: "php/checkTutorialLog.php", 
                data: {workerId: worker, task: task, dbName: gup('dbName')}, 
                dataType: "text", 
                async: false, 
                success: function (dd) {
                    if (dd == 1) {
                        url = "wait.php?";
                    } else {
                        //alert("gotta do the tutorial first!"); 
                        url = "tutorial.php?"; 
                        //url += "&thirdPartyUrl=" + decodeURIComponent(gup('thirdPartyUrl'));  
                        url += "&thirdPartyTutUrl=" + gup('thirdPartyTutUrl');  
                        //url += "&thirdPartyUrl=" + "https://legionpowered.net/Glance/coding_tools/tutorial/tutorial.php?assignmentId=2o3u4324";  
                    }
                    url += "&thirdPartyInstrUrl=" + gup('thirdPartyInstrUrl'); 
                    url += "&workerId=" + gup('workerId');
                    url += "&assignmentId=" + gup('assignmentId');
                    url += "&hitId=" + gup('hitId');
                    url += "&turkSubmitTo=" + gup('turkSubmitTo');
                    url += "&task=" + gup('task');
                    url += "&min=" + gup('min');
                    url += "&instructions=" +  gup('instructions');
                    url += "&dbName=" +  gup('dbName');
                    window.location = url; 
                },
                fail: function () {
                    alert("something in checkTutorialLog.php failed!"); 
                },
                }); 
            },
                fail: function() {
                    alert("setLive failed!")
                },
        });
    }
});
