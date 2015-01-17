var sandbox = true;
var mode;

$(document).ready( function() {
var retainerLocation = "Retainer/";
// $('#send_to_tutorial_button').click(function() {
//     clearQueue('https://roc.cs.rochester.edu/convInterface/videocoding/tutorial/tutorial.php?justTutorial=true');
// });

if(gup('login') =='false'){
    $("#accessKey").val("use_file");
    $("#secretKey").val("use_file");
    $('#accessKey, #secretKey').attr('readonly', true);
    startWriteNumOnline();
    updateSessionsList()
    setInterval(function(){updateSessionsList()},30000);
}
else{
    $('#loginModal').modal({
      backdrop: 'static',
      keyboard: false
    });
    $('#loginModal').modal('show');
}

var sessionLoaded = false;
var isStoppedRecruitingInterval;

$.blockUI.defaults.overlayCSS.cursor = 'not-allowed'; 

$("#updateTask").hide();
$('#overview').block({ message: null }); 
$('#recruitingDiv').block({ message: null }); 
$('#triggerDiv').block({ message: null });

function updateSessionsList(){
    $.ajax({
        url: retainerLocation + "php/getSessionsList.php",
        type: "POST",
        data: {accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "json",
        success: function(d) {
            $("#taskSessionLoad").empty();
            $("#taskSessionLoad").append("<option>---</option>");
            for(var i = 0; i < d.length; i++) {
                var obj = d[i];
                var task = d[i].task;
                $("#taskSessionLoad").append("<option>" + task + "</option>");
            }
            $("#taskSessionLoad").val($("#taskSession").val());
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });
}

$("#modalLoginButton").on("click", function(event){
    event.preventDefault();
    $("#accessKey").val($("#modalAccessKey").val());
    $("#secretKey").val($("#modalSecretKey").val());
    $('#accessKey, #secretKey').attr('readonly', true);
    $('#modalLoginButton').attr('disabled', true);

    $.ajax({
        url: retainerLocation + "php/login.php",
        type: "POST",
        async: true,
        data: {accessKey: $("#modalAccessKey").val(), secretKey: $("#modalSecretKey").val()},
        dataType: "text",
        success: function(d) {
            $('#loginModal').modal('hide');
            startWriteNumOnline();
            updateSessionsList()
            setInterval(function(){updateSessionsList()},30000);
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });
});

$("#addNewTask").on("click", function(event){
    event.preventDefault();
    if($("#hitTitle").val() == ""){
        alert("Please enter an experiment name.");
        return;
    }
    sessionLoaded = true;
    $.ajax({
        url: retainerLocation + "php/addNewTask.php",
        type: "POST",
        async: false,
        data: {taskTitle: $("#hitTitle").val(), taskDescription: $("#hitDescription").val(), taskKeywords: $("#hitKeywords").val(), task: $("#taskSession").val(), country: $("#country").val(), percentApproved: $("#percentApproved").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "text",
        success: function(d) {
            // $('#loadTask').attr('disabled','disabled');
            $('#addNewTask').attr('disabled','disabled');
            $('#updateTask').removeAttr('disabled');
            // $("#taskSessionLoad").val($("#taskSession").val());
            // $('#taskSessionLoad').attr('disabled','disabled');
            $('#taskSession').attr('disabled','disabled');

            $("#taskSessionLoad").append("<option>" + $("#taskSession").val() + "</option>");
            $("#taskSessionLoad").val($("#taskSession").val());

            $("#updateTask").show();
            $('#addNewTask').hide();

            $('#recruitingTabLi').removeClass("disabled"); 

            $('#overview').unblock(); 
            $('#recruitingDiv').unblock(); 
            $('#triggerDiv').unblock(); 

            $('#copyExperiment').removeAttr('disabled');
            $('#deleteExperiment').removeAttr('disabled');

        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });
});

$("#minPrice,#maxPrice").on("change", function(event){
    event.preventDefault();
    $.ajax({
        url: retainerLocation + "php/updatePrice.php",
        type: "POST",
        async: true,
        data: {task: $("#taskSession").val(), min_price: $("#minPrice").val(), max_price: $("#maxPrice").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "text",
        success: function(d) {
            
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });
});

$("#currentTarget").change(function(){

    $.ajax({
        url: retainerLocation + "php/updateTargetNumWorkers.php",
        type: "POST",
        async: true,
        data: {task: $("#taskSession").val(), target_workers: $("#currentTarget").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "text",
        success: function(d) {
            
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });

    // if($("#currentTarget").val() <= 0){
    //     $('#stopRecruiting').attr('disabled','disabled');
    //     $('#startRecruiting').removeAttr('disabled');
    // }
});

$("#stopRecruiting").on("click", function(event){
    event.preventDefault();
    $.ajax({
        url: retainerLocation + "php/stopRecruiting.php",
        type: "POST",
        async: true,
        data: {task: $("#taskSession").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "text",
        success: function(d) {
            
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });
    window.clearInterval(isStoppedRecruitingInterval);
    isStoppedRecruitingInterval = setInterval(function(){isStoppedRecruiting()},3000);

    $('#startRecruiting').html('Please wait while recruiting is stopped');
    $('#stopRecruiting').attr('disabled','disabled');
    // $('#startRecruiting').removeAttr('disabled');

    $('#yesSandbox').removeAttr('disabled');
    $('#noSandbox').removeAttr('disabled');
});

$("#startRecruiting").on("click", function(event){
    event.preventDefault();

    var problem = validateTaskInfo();
    if(problem != ""){
        alert("ERROR: please update " + problem);
    }
    else {
        // Update db, mark ok to recruit
        $.ajax({
            url: retainerLocation + "php/startRecruiting.php",
            type: "POST",
            async: true,
            data: {task: $("#taskSession").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
            dataType: "text",
            success: function(d) {
                // alert(d);
                if(mode == "retainer"){
                    // Start the recruiting tool
                    $.ajax({
                        url: "Overview/turk/getAnswers.php",
                        type: "POST",
                        async: true,
                        data: {task: $("#taskSession").val(), useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(), mode: "retainer", requireUniqueWorkers: $("#requireUniqueWorkers").is(':checked')},
                        dataType: "text",
                        success: function(d) {
                            console.log(d);
                        },
                        fail: function() {
                            alert("Sending number of workers failed");
                        }
                    });
                }
                else if (mode == "auto"){
                    // Start the recruiting tool
                    var urlEscaped = $("#autoSendToURL").val().split("&").join("&amp;&amp;");
                    $.ajax({
                        url: "Overview/turk/getAnswers.php",
                        type: "POST",
                        async: true,
                        data: {task: $("#taskSession").val(), useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(), mode: "auto", url: urlEscaped, requireUniqueWorkers: $("#requireUniqueWorkers").is(':checked'), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
                        dataType: "text",
                        success: function(d) {
                            console.log(d);
                        },
                        fail: function() {
                            alert("Sending number of workers failed");
                        }
                    });
                }
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });
        isStoppedRecruitingInterval = setInterval(function(){isStoppedRecruiting()},5000);

        $('#startRecruiting').attr('disabled','disabled');
        $('#startRecruiting').html("Recruiting...");
        $('#stopRecruiting').removeAttr('disabled');

        if(!sandbox) $('#yesSandbox').attr('disabled','disabled');
        if(sandbox) $('#noSandbox').attr('disabled','disabled');
    }

});

$("#postHITs").on("click", function(event){
    event.preventDefault();

    var problem = validateTaskInfo();
    if(problem != ""){
        alert("ERROR: please update " + problem);
    }
    else {
        if(mode == "direct"){
            var urlEscaped = $("#sendToURL").val().split("&").join("&amp;&amp;");
            // alert(urlEscaped);
            // Start the recruiting tool

            $('#postHITs').attr('disabled','disabled');
            $('#postHITs').text("Posting...");
            $('#expireHITs').attr('disabled','disabled');

            $.ajax({
                url: "Overview/turk/getAnswers.php",
                type: "POST",
                async: true,
                data: {task: $("#taskSession").val(), useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(), mode: "direct", url: urlEscaped, price: $("#price").val(), numHITs: $("#numHITs").val(), numAssignments: $("#numAssignments").val(), requireUniqueWorkers: $("#requireUniqueWorkers").is(':checked'), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
                dataType: "text",
                success: function(d) {
                    alert(d);
                    alert("Posted " + $("#numHITs").val() + " HITs");
                    $('#postHITs').text("Post HITs");
                    $('#postHITs').removeAttr('disabled');
                    $('#expireHITs').removeAttr('disabled');
                },
                fail: function() {
                    alert("Sending number of workers failed");
                }
            });
        }

        // $('#yesSandbox').attr('disabled','disabled');
        // $('#noSandbox').attr('disabled','disabled');
    }

});

$("#expireHITs").on("click", function(event){
    event.preventDefault();

    $('#postHITs').attr('disabled','disabled');
    $('#expireHITs').attr('disabled','disabled');
    $('#expireHITs').text("Expiring...");

    $.ajax({
        url: "Overview/turk/expireHITs.php",
        type: "POST",
        async: true,
        data: {task: $("#taskSession").val(), useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "text",
        success: function(d) {
            // alert(d);
            alert("HITs expired");
            $('#expireHITs').text("Expire All HITs");
            $('#postHITs').removeAttr('disabled');
            $('#expireHITs').removeAttr('disabled');
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });

        // $('#yesSandbox').attr('disabled','disabled');
        // $('#noSandbox').attr('disabled','disabled');
});

$("#taskSessionLoad").on("change", function(event){
    event.preventDefault();

    sessionLoaded = true;

    var taskData;
    $.ajax({
        url: retainerLocation + "php/loadTask.php",
        type: "POST",
        async: false,
        data: {task: $("#taskSessionLoad").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "json",
        success: function(d) {
            taskData = d;
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });

    $("#taskSession").val(taskData.task);
    $("#hitTitle").val(taskData.task_title);
    $("#hitDescription").val(taskData.task_description);
    $("#hitKeywords").val(taskData.task_keywords);
    $("#minPrice").val(taskData.min_price);
    $("#maxPrice").val(taskData.max_price);
    $("#currentTarget").val(taskData.target_workers);
    $("#country").val(taskData.country);
    $("#percentApproved").val(taskData.percentApproved);
    $("#waitingInstructions").val(taskData.instructions);

    $('#addNewTask').attr('disabled','disabled');
    // $('#loadTask').attr('disabled','disabled');
    // $('#taskSessionLoad').attr('disabled','disabled');
    $('#taskSession').attr('disabled','disabled');
    $('#updateTask').removeAttr('disabled');
    $('#copyExperiment').removeAttr('disabled');
    $('#deleteExperiment').removeAttr('disabled');

    $("#updateTask").show();
    $('#addNewTask').hide();

    $('#recruitingTabLi').removeClass("disabled"); 

    $('#overview').unblock(); 
    $('#recruitingDiv').unblock(); 
    $('#triggerDiv').unblock(); 

    // Stopping recruiting
    if(taskData.done == "2"){
        $('#startRecruiting').html('Please wait while recruiting is stopped');
        $('#stopRecruiting').attr('disabled','disabled');
        $('#startRecruiting').attr('disabled','disabled');
        isStoppedRecruitingInterval = setInterval(function(){isStoppedRecruiting()},3000);
    }
    // Recruiting
    else if(taskData.done == "0"){
        $('#startRecruiting').html('Recruiting...');
        $('#startRecruiting').attr('disabled','disabled');
        $('#stopRecruiting').removeAttr('disabled');
    }
    // Finished stopping recruiting, not running
    else if(taskData.done == "1"){
        $('#startRecruiting').html('Start recruiting');
        $('#stopRecruiting').html('Stop recruiting');
        $('#startRecruiting').removeAttr('disabled');
        $('#stopRecruiting').attr('disabled','disabled');
    }
});

$("#updateTask").on("click", function(event){
    event.preventDefault();
    $.ajax({
        url: retainerLocation + "php/updateTask.php",
        type: "POST",
        async: false,
        data: {taskTitle: $("#hitTitle").val(), taskDescription: $("#hitDescription").val(), taskKeywords: $("#hitKeywords").val(), task: $("#taskSession").val(), country: $("#country").val(), percentApproved: $("#percentApproved").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "text",
        success: function(d) {
            alert("Update success");
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });
});

$("#reloadHits").on("click", function(event){
    event.preventDefault();
    $('#hitsList').block({ 
        message: '<h1>Loading, this may take a while...</h1>', 
        css: { border: '3px solid #a00' } 
    }); 

    var hits;
    $.ajax({
        url: "Retainer/php/getHits.php",
        type: "POST",
        async: true,
        data: {task: $("#taskSession").val(), useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "json",
        success: function(d) {
            // alert("done");
            $('#hitsList').unblock(); 
            hits = d;
            console.log(d);

            //Fade out all the old hits, then add the new ones.
            $('#hitsList').children().fadeOut(500).promise().then(function() {
                $('#hitsList').empty();
                var counter = 0;
                // alert(hits);
                for (var i in hits) {
                    var hit = hits[i];
                    var numAssignments = hit.NumResults;
                    for(var j = 0; j < numAssignments; j++){
                        if(hit.hasOwnProperty("Assignment")){
                            if(numAssignments == 1) var assignment = hit.Assignment;
                            else var assignment = hit.Assignment[j];
                            var listId = "hit" + counter;
                            if(assignment.hasOwnProperty("AssignmentStatus")){
                                var answer = assignment.Answer; // If legion.js was used, bonus will be stored in XML of assignment answer
                                var bonus = $(answer).find("FreeText").text().substring(1);
                                if(isNaN(bonus)) bonus = 0; //make sure bonus is a number
                                if(assignment.AssignmentStatus == "Submitted")
                                    $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + assignment.WorkerId + " AssignmentId: " + assignment.AssignmentId + " <button type='button' onclick = 'approveHit(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.HITId + "&quot;, &quot;" + listId + "&quot;, &quot;" + bonus + "&quot;, &quot;" + assignment.WorkerId + "&quot;)' class='approveButton btn btn-success btn-sm'>Approve</button> <button type='button' onclick = 'rejectHit(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.HITId + "&quot;, &quot;" + listId + "&quot;)' class='rejectButton btn btn-danger btn-sm'>Reject</button></li>");

                                else
                                    $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + assignment.WorkerId + " AssignmentId: " + assignment.AssignmentId + " <button type='button' onclick = 'disposeHit(&quot;" + assignment.HITId + "&quot;, &quot;" + listId + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button></li>");

                                counter++;
                            }
                        }
                    }
                }
            });
        },
        error: function() {
            alert("Sending number of workers failed");
        }
    });
});

$("#approveAll").on("click", function(event){
    event.preventDefault();
    // $('#hitsList li').each(function() {
    //     var id = this.id;
    //     $("#" + id + " .approveButton").trigger("click");
    // });

    $('#hitsList li').each(function(index){
        var id = this.id;
        setTimeout(function () {
            $("#" + id + " .approveButton").trigger("click");
        }, index*500);
    });
});


$("#disposeAll").on("click", function(event){
    event.preventDefault();
    // $('#hitsList li').each(function() {
    //     var id = this.id;
    //     $("#" + id + " .disposeButton").trigger("click");
    // });

    $('#hitsList li').each(function(index){
        var id = this.id;
        setTimeout(function () {
            $("#" + id + " .disposeButton").trigger("click");
        }, index*500);
    });
});


$("#clearQueue").on("click", function(event){
    event.preventDefault();
    
    clearQueue(baseURL + '/Retainer/submitOnly.php');
});

function clearQueue(link){
    var numOnline = 0;
    var task = $("#taskSession").val();
    $.ajax({
        url: retainerLocation + "php/ajax_whosonline.php",
        type: "POST",
        async: false,
        data: {task: task, role: "trigger", accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "text",
        success: function(d) {
            //
            // document.getElementById("numOnline").innerHTML= "There are " + d + " worker(s) online for this task";
            numOnline = d;
        },
        fail: function() {
            alert("setOnline failed!")
        },
    });
    var r = confirm("Send all workers in queue to destination?");
    if(r == true){
        $.ajax({
            url: retainerLocation + "php/setFire.php",
            type: "POST",
            async: false,
            data: {url: link, task: task, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
            dataType: "text",
            success: function(d) {
                //
                //alert("Fire successful");
            },
            fail: function() {
                alert("Clear queue failed!");
            }
        });
        
        $.ajax({
            url: retainerLocation + "php/updateReleased.php",
            type: "POST",
            async: false,
            data: {url: link, max: numOnline, task: task, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
            dataType: "text",
            success: function(d) {
                
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });
    }
}

$("#fireWorkers").on("click", function(event){
    event.preventDefault();

    var task = $("#taskSession").val();
    var link  = $("#fireToURL").val();
    var numFire  = $("#numFire").val();

    if( link.substring(0, 8) != "https://") {
        alert('ERROR: link must begin with "https://". No workers will be fired.');
        return;
    }
    else if( numFire == "" ) {
        alert('ERROR: number of workers to fire must be specified. No workers will be fired.');
        return;
    }

    var r = confirm("Fire " + numFire + " workers to: " + link + " ?");
    if(r == true){
        $.ajax({
            url: retainerLocation + "php/setFire.php",
            type: "POST",
            async: true,
            data: {url: link, task: task, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
            dataType: "text",
            success: function(d) {
                $.ajax({
                    url: retainerLocation + "php/updateReleased.php",
                    type: "POST",
                    async: true,
                    data: {url: link, max: numFire, task: task, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
                    dataType: "text",
                    success: function(d) {
                        
                    },
                    fail: function() {
                        alert("Sending number of workers failed");
                    }
                });
            },
            fail: function() {
                alert("Clear queue failed!");
            }
        });
    }
});

$("#yesSandbox, #noSandbox").on("click", function(){
    var id = $(this).attr('id');
    if(id == "yesSandbox"){
        $("#yesSandbox").addClass("active");
        $("#noSandbox").removeClass("active");
        sandbox = true;
    }
    else if (id == "noSandbox"){
        $("#noSandbox").addClass("active");
        $("#yesSandbox").removeClass("active");
        sandbox = false;
    }
});

$("#waitingInstructionsUpdated").on("click", function(){
    $.ajax({
        url: retainerLocation + "php/updateWaitingInstructions.php",
        type: "POST",
        data: {task: $("#taskSession").val(), instructions: $("#waitingInstructions").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "text",
        success: function(d) {
          //
        },
        fail: function() {
          alert("Sending number of workers failed");
        }
    });
});

$("#useRetainerMode").on("click", function(){
    mode = "retainer";
    $("#triggerDiv").show();
    $("#autoSendToURLForm").hide();
    $("#openInstructionsModal").show();

});
$("#useDirectMode").on("click", function(){
    mode = "direct";
    $("#triggerDiv").hide();
});
$("#useAutoMode").on("click", function(){
    mode = "auto";
    $("#triggerDiv").hide();
    $("#autoSendToURLForm").show();
    $("#openInstructionsModal").hide();
});

$("#requireUniqueWorkers").change(function() {
    if(this.checked) {
       $.ajax({
           url: retainerLocation + "php/uniqueWorkers.php",
           type: "POST",
           data: {task: $("#taskSession").val(), useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
           dataType: "text",
           success: function(d) {
             alert(d);
           },
           fail: function() {
             alert("Sending number of workers failed");
           }
       });
    }
});

$("#resetUniqueWorkers").on("click", function(event) {
    event.preventDefault();
    if(confirm("Are you sure you want to reset your history of unique workers?")){
         $.ajax({
            url: retainerLocation + "php/uniqueWorkers.php",
            type: "POST",
            data: {task: $("#taskSession").val(), reset: true, useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
            dataType: "text",
            success: function(d) {
              alert("Reset success");
            },
            fail: function() {
              alert("Sending number of workers failed");
            }
        });
    }
});

$("#copyExperiment").on("click", function(event) {
    event.preventDefault();
    var newTask = prompt("Please enter a unique new task name");
     $.ajax({
        url: retainerLocation + "php/copyExperiment.php",
        type: "POST",
        data: {task: $("#taskSession").val(), newTask: newTask, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "text",
        success: function(d) {
            updateSessionsList();
            alert("Copied to " + newTask);
        },
        fail: function() {
          alert("Copying failed");
        }
    });
});

$("#deleteExperiment").on("click", function(event) {
    event.preventDefault();
    if(confirm("Are you sure you want to delete " + $("#taskSession").val() + " ? This will stop recruiting and prevent you from approving/rejecting submitted HITs.")){
        $("#stopRecruiting").trigger('click');
        $.ajax({
            url: retainerLocation + "php/deleteExperiment.php",
            type: "POST",
            data: {task: $("#taskSession").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
            dataType: "text",
            success: function(d) {
                updateSessionsList();
                alert("Deleted " + $("#taskSession").val());
                $("#taskSessionLoad").val("---");
            },
            fail: function() {
              alert("Deleting failed");
            }
        });
    }
});

function isStoppedRecruiting(){
    var taskData;
    $.ajax({
        url: retainerLocation + "php/loadTask.php",
        type: "POST",
        async: false,
        data: {task: $("#taskSessionLoad").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "json",
        success: function(d) {
            taskData = d;
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });
    if(taskData.done == "1"){
        window.clearInterval(isStoppedRecruitingInterval);
        $('#startRecruiting').html('Start recruiting');
        $('#stopRecruiting').html('Stop recruiting');
        $('#startRecruiting').removeAttr('disabled');
        $('#stopRecruiting').attr('disabled','disabled');
        alert("Recruiting stopped. If this was an unwanted, please make sure there is money in your account.");
        return true;
    }
    else return false;
}


function validateTaskInfo(){
    var taskData;
    $.ajax({
        url: retainerLocation + "php/loadTask.php",
        type: "POST",
        async: false,
        data: {task: $("#taskSession").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        dataType: "json",
        success: function(d) {
            taskData = d;
        },
        fail: function() {
            alert("Sending number of workers failed");
        }
    });

    // console.log(taskData);
    if(taskData.min_price == ""){
        return "min price";
    }
    if(taskData.max_price == ""){
        return "max price";
    }
    if(taskData.task_title == ""){
        return "title";
    }
    if(taskData.task_description == ""){
        return "description";
    }
    if(taskData.task_keywords == ""){
        return "keywords";
    }
    if(mode == "direct"){
        var link  = $("#sendToURL").val();
        if( link.substring(0, 8) != "https://") {
            return('URL, must begin with "https://".');
        }
    }
    return "";
}

/*
// WSL: Can't work with ajax directly because of XSS issues. To fix, use a php script that calls 'ping'.
$('#fireToURL').blur( function() {
  $('#url-alert').remove();

  $.ajax({
    type: 'HEAD',
    url: $('#fireToURL').val(),
    success: function() {
      // page exists
      $('#fireToURL').css("color", "black");
    },
    error: function() {
      // page does not exist
      //alert("Invalid URL");
      $('#fireToURL').after("<div id='url-alert' style='color: red; opacity: 0.6'><i>(Invalid URL)</i></div>");
      $('#fireToURL').css("color", "red");
    }
  });
});
*/

$("#useRetainerMode").trigger("click");



});
