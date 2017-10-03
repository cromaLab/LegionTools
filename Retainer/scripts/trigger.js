// This file contains click handlers for HTML elements

// Main options in Recruiting Panel
var sandbox = true; // Whether sandbox is used
var mode; // Retainer, auto or direct mode (as reflected in tabs on Recruiting Panel)

// Note: All click event handlers are collected in this giant function
$(document).ready( function() {

    var retainerLocation = "Retainer/";

// Statusbar to write status messages to (replaces alerts that were used earlier)
    var statusbar = document.getElementById("statusbar");

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

    $('#updateTask').hide();
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
                statusbar.innerHTML = "Sending number of workers to loadTask in updateSessionList() failed";
            }
        });
    }

// Login button - fetches credentials and logs in
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
                setInterval(function(){updateSessionsList()}, 30000);
            },
            fail: function() {
                statusbar.innerHTML = "ajax POST to php/login.php failed";
            }
        });
    });

    $("#debugButton").on("click", function(event) {
        // console.log("Button create user clicked")
        // $('#bonusModal').modal('show');
        $.ajax({
            url: "Retainer/php/BonusDbHelper.php",
            type: "POST",
            async: true,
            data: {
                task: $("#taskSession").val(),
                useSandbox: sandbox,
                accessKey: $("#accessKey").val(),
                secretKey: $("#secretKey").val()
            },
            dataType: "json",
            success: function (d) {
                console.log(d);
            }, error: function (a, b, c) {
                console.log("ERROR DEBUG");
            }
        });

    });

// When Add New Experiment is clicked, calls addNewTask.php to write experiment to DB
    $("#addNewTask").on("click", function(event){
        event.preventDefault();
        if($("#hitTitle").val() == ""){
            statusbar.innerHTML = "Please enter an experiment name.";
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
                statusbar.innerHTML = "ajax POST to php/addNewTask.php failed";
            }
        });
    });

// Update prices when min or max price is changed
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
                statusbar.innerHTML = "ajax POST to php/updatePrice.php failed";
            }
        });
    });

// Update target number of assignable HITs if it is changed via AJAX call to php/updateTargetNumWorkers.php
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
                statusbar.innerHTML = "ajax POST to php/updateTargetNumWorkers.php failed";
            }
        });

        // if($("#currentTarget").val() <= 0){
        //     $('#stopRecruiting').attr('disabled','disabled');
        //     $('#startRecruiting').removeAttr('disabled');
        // }
    });

// When Stop recruiting button is clicked => AJAX call to php/stopRecruiting.php
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
                statusbar.innerHTML = "ajax POST to php/stopRecruiting.php failed";
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

// When Start recruiting button is clicked => AJAX call to php/startRecruiting.php
    $("#startRecruiting").on("click", function(event){
        event.preventDefault();

        var problem = validateTaskInfo();
        if(problem != ""){
            statusbar.innerHTML = "ERROR: please update " + problem;
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
                    // statusbar.innerHTML = d;

                    if(mode == "retainer"){
                        // Start the recruiting tool
                        var tutPageUrl = encodeURI($("#tutPage").val());
                        var waitPageUrl = encodeURI($("#waitPage").val());
                        var instrPageUrl = encodeURI($("#instrPage").val());
                        $.ajax({
                            url: "Overview/turk/getAnswers.php",
                            type: "POST",
                            async: true,
                            data: {task: $("#taskSession").val(), useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(), mode: "retainer", requireUniqueWorkers: $("#requireUniqueWorkers").is(':checked'), tutPageUrl: tutPageUrl, waitPageUrl: waitPageUrl, instrPageUrl: instrPageUrl},
                            dataType: "text",
                            success: function(d) {
                                //console.log(d);
                            },
                            fail: function() {
                                statusbar.innerHTML = "Sending number of workers in getAnswers() failed";
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
                                statusbar.innerHTML = "Sending number of workers in getAnswers() pt.2 failed";
                            }
                        });
                    }
                },
                fail: function() {
                    statusbar.innerHTML = "Sending number of workers failed";
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

// When Post Hits button is clicked in direct (classic) mode
    $("#postHITs").on("click", function(event){
        event.preventDefault();

        var problem = validateTaskInfo();
        if(problem != ""){
            statusbar.innerHTML = "ERROR: please update " + problem;
        }
        else {
            if(mode == "direct"){
                var urlEscaped = $("#sendToURL").val().split("&").join("&amp;&amp;");

                // Start the recruiting tool
                var a = urlEscaped.split("\\").filter(function(el) {return el.length != 0});

                $('#postHITs').attr('disabled','disabled');
                $('#postHITs').text("Posting...");
                $('#expireHITs').attr('disabled','disabled');

                for (i=0; i<a.length; i++) {
                    //statusbar.innerHTML = "Posting: " + a[i];
                    $.ajax({
                        url: "Overview/turk/getAnswers.php",
                        type: "POST",
                        async: true,
                        data: {
                            task: $("#taskSession").val(),
                            useSandbox: sandbox,
                            accessKey: $("#accessKey").val(),
                            secretKey: $("#secretKey").val(),
                            mode: "direct",
                            url: a[i],
                            price: $("#price").val(),
                            numHITs: $("#numHITs").val(),
                            numAssignments: $("#numAssignments").val(),
                            requireUniqueWorkers: $("#requireUniqueWorkers").is(':checked'),
                            accessKey: $("#accessKey").val(),
                            secretKey: $("#secretKey").val()
                        },
                        dataType: "text",
                        success: function (d) {
                            statusbar.innerHTML = "Posted " + $("#numHITs").val() + " HITs";
                            $('#postHITs').text("Post HITs");
                            $('#postHITs').removeAttr('disabled');
                            $('#expireHITs').removeAttr('disabled');
                        },
                        fail: function () {
                            statusbar.innerHTML = "ajax POST to php/getAnswers.php failed";
                        }
                    });
                }
            }

            // $('#yesSandbox').attr('disabled','disabled');
            // $('#noSandbox').attr('disabled','disabled');
        }

    });

// Expire All Hits buttons in direct mode
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
                // statusbar.innerHTML = d;
                statusbar.innerHTML = "HITs expired";
                $('#expireHITs').text("Expire All HITs");
                $('#postHITs').removeAttr('disabled');
                $('#expireHITs').removeAttr('disabled');
            },
            fail: function() {
                statusbar.innerHTML = "ajax POST to php/expireHITs.php failed";
            }
        });

        // $('#yesSandbox').attr('disabled','disabled');
        // $('#noSandbox').attr('disabled','disabled');
    });

// Combobox on Load Panel that contains experiments - Populates text fields with values
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
                statusbar.innerHTML = "ajax POST to php/loadTask.php failed";
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

    /**
     * Called when UPDATE button in Load Panel is clicked (makes AJAX call to updateTask.php)
     */
    $("#updateTask").on("click", function(event){
        event.preventDefault();
        $.ajax({
            url: retainerLocation + "php/updateTask.php",
            type: "POST",
            async: false,
            data: {taskTitle: $("#hitTitle").val(), taskDescription: $("#hitDescription").val(), taskKeywords: $("#hitKeywords").val(), task: $("#taskSession").val(), country: $("#country").val(), percentApproved: $("#percentApproved").val(), accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
            dataType: "text",
            success: function(d) {
                statusbar.innerHTML = "Update success";
            },
            fail: function() {
                statusbar.innerHTML = "ajax POST to php/updateTask.php failed";
            }
        });
    });

    /**
     * Called when Load HITs button is clicked
     */
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
                statusbar.innerHTML = "done";
                $('#hitsList').unblock();
                hits = d;
                console.log(d);

                //Fade out all the old hits, then add the new ones.
                $('#hitsList').children().fadeOut(500).promise().then(function() {
                    $('#hitsList').empty();
                    var counter = 100;
                    console.log("Reset counter: " + counter);

                    // statusbar.innerHTML = hits;
                    for (var i in hits) {
                        console.log("Found HIT " + i);
                        var hit = hits[i];
                        var numAssignments = hit.NumResults;
                        for(var j = 0; j < numAssignments; j++){
                            console.log("Found assignment " + j);
                            if(hit.hasOwnProperty("Assignment")){
                                console.log("Found property Assignment in assignment");

                                if(numAssignments == 1) var assignment = hit.Assignment;
                                else var assignment = hit.Assignment[j];
                                var listId = "hit" + counter;
                                counter = counter + 1;
                                console.log("Increased counter: " + counter);

                                if(assignment.hasOwnProperty("AssignmentStatus")) {
                                    //var answer = assignment.Answer; // If legion.js was used, bonus will be stored in XML of assignment answer
                                    //var bonus = $(answer).find("FreeText").text().substring(1);
                                    //console.log("workerId " + assignment.WorkerId);
                                    //var bonus = getMoney(assignment.WorkerId);
                                    var bonus = 0;
                                    var centsPerWaiting = 0.05;
                                    //console.log("bonus " + bonus);
                                    if (isNaN(bonus)) bonus = 0; //make sure bonus is a number


                                    if (assignment.AssignmentStatus == "Submitted") {
                                        console.log("Status is submitted. Adding approve and reject button.");
                                        console.log("WorkerID: " + assignment.WorkerId + ", AssignmentID: " + assignment.AssignmentId +
                                           ", HitID: " + assignment.HITId + ", ListID: " + listId);
                                        $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + assignment.WorkerId + " AssignmentId: " + assignment.AssignmentId + "<br /><button type='button' onclick = 'approveHit(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + assignment.HITId + "&quot;, &quot;" + listId + "&quot;, &quot;" + bonus + "&quot;, &quot;" + assignment.WorkerId + "&quot;)' class='approveButton btn btn-success btn-sm'>Approve</button>&nbsp;&nbsp;<button type='button' onclick = 'rejectHit(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + assignment.HITId + "&quot;, &quot;" + listId + "&quot;)' class='rejectButton btn btn-danger btn-sm'>Reject</button></li>");
                                    }
                                    else if(assignment.AssignmentStatus == "Approved") {
                                        console.log("Status is approved.");
                                        var DEFAULT_BONUS = 0.00;
                                        var bonusAmount = DEFAULT_BONUS;

                                        // Extract bonus suggestion from response in DB
                                        console.log("Calling AJAX for bonus.");
                                        $.ajax({
                                                url: 'Retainer/api/api.php/bonus/get',
                                                type: 'POST',
                                                async: false,
                                                data: {useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val(),
                                                    workerId: assignment.WorkerId, assignmentId: assignment.AssignmentId},
                                                success: function(data, status) {
                                                    console.log("Bonus successfully extracted: ");

                                                    var returnValue = JSON.parse(data); // JavaScript parser (JQuery might be $.parseJSON(data);)
                                                    bonusAmount = returnValue['bonusAmount'];
                                                    if (isNaN(returnValue['bonusAmount'])) bonusAmount = DEFAULT_BONUS;

                                                    // Use extracted bonus suggestion if extraction was successful
                                                    console.log("WorkerID: " + assignment.WorkerId + ", AssignmentID: " + assignment.AssignmentId +
                                                    ", Bonus amount: " + bonusAmount + ", HitID: " + assignment.HITId +
                                                        ", ListID: " + listId);
                                                    $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + assignment.WorkerId + " AssignmentId: " + assignment.AssignmentId + "<br/>&nbsp;&nbsp;<button type='button' onclick = 'disposeHit(&quot;" + assignment.HITId + "&quot;, &quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>&nbsp;&nbsp;<button type='button' onclick = 'showBonusModal(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + bonusAmount + "&quot;)' class='bonusModalButton btn btn-warning btn-sm'>Edit Bonus</button>&nbsp;&nbsp;<button type='button' onclick = 'bonusImmediately(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + bonusAmount + "&quot;)' class='bonusQuickButton btn btn-warning btn-sm'>Bonus: $" + bonusAmount + "</button></li>");
                                                },
                                                error: function(xhr, desc, err) {
                                                    console.log("Error: Could not extract bonus, using default.");
                                                    console.log(xhr);
                                                    console.log("Details: " + desc + "\nError:" + err);

                                                    // Use default bonus if error occurred
                                                    $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + assignment.WorkerId + " AssignmentId: " + assignment.AssignmentId + "<br /><button type='button' onclick = 'disposeHit(&quot;" + assignment.HITId + "&quot;, &quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>&nbsp;&nbsp;<button type='button' onclick = 'showBonusModal(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + DEFAULT_BONUS + "&quot;)' class='bonusModalButton btn btn-warning btn-sm'>Bonus</button>&nbsp;&nbsp;<button type='button' onclick = 'bonusImmediately(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + DEFAULT_BONUS + "&quot;)' class='bonusQuickButton btn btn-warning btn-sm'>Bonus: $" + DEFAULT_BONUS + "</button></li>");
                                                }
                                            });

                                        // $.ajax({
                                        //     url: 'Retainer/php/BonusHelper.php',
                                        //     type: 'POST',
                                        //     async: false,
                                        //     data: {'action': 'extractBonus', 'param': assignment.Answer},
                                        //     success: function(data, status) {
                                        //         console.log("Bonus successfully extracted.");
                                        //
                                        //         var returnValue = JSON.parse(data); // JavaScript parser (JQuery might be $.parseJSON(data);)
                                        //         bonusAmount = returnValue['bonusAmount'];
                                        //         if (isNaN(returnValue['bonusAmount'])) bonusAmount = DEFAULT_BONUS;
                                        //
                                        //         // Use extracted bonus suggestion if extraction was successful
                                        //         console.log("WorkerID: " + assignment.WorkerId + ", AssignmentID: " + assignment.AssignmentId +
                                        //         ", Bonus amount: " + bonusAmount + ", HitID: " + assignment.HITId +
                                        //             ", ListID: " + listId);
                                        //         $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + assignment.WorkerId + " AssignmentId: " + assignment.AssignmentId + "<br/>&nbsp;&nbsp;<button type='button' onclick = 'disposeHit(&quot;" + assignment.HITId + "&quot;, &quot;" + listId + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>&nbsp;&nbsp;<button type='button' onclick = 'showBonusModal(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + bonusAmount + "&quot;)' class='bonusModalButton btn btn-warning btn-sm'>Edit Bonus</button>&nbsp;&nbsp;<button type='button' onclick = 'bonusImmediately(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + bonusAmount + "&quot;)' class='bonusQuickButton btn btn-warning btn-sm'>Bonus: $" + bonusAmount + "</button></li>");
                                        //     },
                                        //     error: function(xhr, desc, err) {
                                        //         console.log("Error: Could not extract bonus, using default.");
                                        //         console.log(xhr);
                                        //         console.log("Details: " + desc + "\nError:" + err);
                                        //
                                        //         // Use default bonus if error occurred
                                        //         $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + assignment.WorkerId + " AssignmentId: " + assignment.AssignmentId + "<br /><button type='button' onclick = 'disposeHit(&quot;" + assignment.HITId + "&quot;, &quot;" + listId + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>&nbsp;&nbsp;<button type='button' onclick = 'showBonusModal(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + DEFAULT_BONUS + "&quot;)' class='bonusModalButton btn btn-warning btn-sm'>Bonus</button>&nbsp;&nbsp;<button type='button' onclick = 'bonusImmediately(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;, &quot;" + DEFAULT_BONUS + "&quot;)' class='bonusQuickButton btn btn-warning btn-sm'>Bonus: $" + DEFAULT_BONUS + "</button></li>");
                                        //     }
                                        // });
                                        // BEWARE: bonusAmount will be DEFAULT_VALUE here, since AJAX is asynchronous, i.e
                                        //         it will not wait for callbacks but directly continue here
                                    }
                                    else if(assignment.AssignmentStatus == "Rejected") {
                                        $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + assignment.WorkerId + " AssignmentId: " + assignment.AssignmentId + "<br /><button type='button' onclick = 'disposeHit(&quot;" + assignment.HITId + "&quot;, &quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button>&nbsp;&nbsp;<button type='button' onclick = 'unrejectHit(&quot;" + assignment.AssignmentId + "&quot;, &quot;" + assignment.WorkerId + "&quot;, &quot;" + listId + "&quot;)' class='approveButton btn btn-warning btn-sm'>Unreject</button></li>");
                                    }

                                    //counter++;
                                }
                            }
                        }
                    }
                });
            },
            error: function(req, status, error) {
                statusbar.innerHTML = "ajax POST to php/getHits.php failed";
                console.log('ajax POST to php/getHits.php:');
                console.log('req',req);
                console.log('status',status);
                console.log('error',JSON.stringify(error));
                // console.log(req.responseText);
            }
        });
    });

    $("#sendBonusButtonInModal").on("click", function(event) {
        console.log("Bonus button clicked");
        // alert('Values: ' + document.getElementById('modalBonusAmount').value + ' ' +
        //     document.getElementById('modalBonusReason').value + ' ' +
        //     document.getElementById('modalBonusAssignmentId').value + ' ' +
        //     document.getElementById('modalBonusWorkerId').value
        // );

        console.log("Calling AJAX for bonus.");
        $.ajax({
            url: 'Retainer/php/BonusHelper.php',
            type: 'POST',
            data: {'action': 'executeBonus', bonusAmount: document.getElementById('modalBonusAmount').value,
                assignmentId: document.getElementById('modalBonusAssignmentId').value,
                workerId: document.getElementById('modalBonusWorkerId').value,
                bonusReason: document.getElementById('modalBonusReason').value,
                useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val() },
            success: function(data, status) {
                console.log("Success: Bonus extracted.");
            },
            error: function(xhr, desc, err) {
                console.log("Error: Could not extract bonus, using default.");
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });

        // $.ajax({
        //     url: "Retainer/php/BonusHelper.php",
        //     type: "POST",
        //     async: true,
        //     data: {id: assignmentId, workerId: workerId, operation: "Unreject", useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
        //     success: function(d) {
        //         //alert("Sending number of workers succeeded [unreject]");
        //         $("#" + listId + ".approveButton").fadeOut( function() { $(this).remove(); });
        //     },
        //     fail: function() {
        //         //alert("Sending number of workers failed [unreject]");
        //     }
        // });
    });

// Approve All Loaded Hits button
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

// Dispose All Loaded Hits button on right-hand side
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

// Clear Entire Queue (Pays Workers) button on right-hand side in Retainer mode
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
                statusbar.innerHTML = "setOnline failed!";
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
                    //statusbar.innerHTML = "Fire successful";
                },
                fail: function() {
                    statusbar.innerHTML = "Clear queue failed!";
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
                    statusbar.innerHTML = "ajax POST to php/updateReleased.php failed";
                }
            });
        }
    }

// Route! button on right-hand side in Retainer mode - seems "fire" means routing workers, not disposing of them
    $("#fireWorkers").on("click", function(event){
        event.preventDefault();

        var task = $("#taskSession").val();
        var link  = $("#fireToURL").val();
        var numFire  = $("#numFire").val();

        if( link.substring(0, 8) != "https://") {
            statusbar.innerHTML = 'ERROR: link must begin with "https://". No workers will be fired.';
            return;
        }
        else if( numFire == "" ) {
            statusbar.innerHTML = 'ERROR: number of workers to fire must be specified. No workers will be fired.';
            return;
        }

        var r = confirm("Route " + numFire + " workers to: " + link + " ?");
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
                            statusbar.innerHTML = "ajax POST to php/updateReleased.php failed";
                        }
                    });
                },
                fail: function() {
                    statusbar.innerHTML = "Clear queue failed!";
                }
            });
        }
    });

// If user clicks on Sandbox or Live switch between MTurk sandbox and productive reflected in sandbox variable
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
                statusbar.innerHTML = "ajax POST to php/updateWaitingInstructions.php failed";
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

// Checkbox on Recruiting Panel under credentials
    $("#requireUniqueWorkers").change(function() {
        if(this.checked) {
            console.log('Requiring unique workers!');
            if(confirm("Notice: If you want to require unique workers, " +
                            "your MTurk Access and Secret keys must be temporarily stored on the server. " +
                            "\n\n" +
                            "You can delete your keys from the server at any time by pressing the Delete Keys button."
                            ))
            {
                console.log('Require unique workers notice: accepted.');
                alert("Unique workers required. Your keys have been temporarily stored to the server.");
                $.ajax({
                    url: retainerLocation + "php/tempKeyStore.php",
                    type: "POST",
                    data: {reset: 0, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
                    dataType: "text",
                    async: false,
                    success: function(d) {
                        statusbar.innerHTML = d;
                    },
                    fail: function() {
                        statusbar.innerHTML = "requireUniqueWorkers ajax POST to php/tempKeyStore.php failed";
                    }
                });
                $.ajax({
                    url: retainerLocation + "php/uniqueWorkers.php",
                    type: "POST",
                    data: {task: $("#taskSession").val(), useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
                    dataType: "text",
                    success: function(d) {
                        statusbar.innerHTML = d;
                    },
                    fail: function() {
                        statusbar.innerHTML = "requireUniqueWorkers ajax POST to php/uniqueWorkers.php failed";
                    }
                });
            } else {
                console.log('Require unique workers notice: rejected.');
                alert("Unique workers NOT required. Your keys have NOT been saved to the server.");                
                $('#requireUniqueWorkers').prop('checked', false); 
            }
        } else {
            console.log('Requiring unique workers!');            
        }
    });

// Red 'Reset History' button next to Require Unique Workers checkbox
    $("#resetUniqueWorkers").on("click", function(event) {
        event.preventDefault();
        if(confirm("Are you sure you want to reset your history of unique workers?")){
            $.ajax({
                url: retainerLocation + "php/uniqueWorkers.php",
                type: "POST",
                data: {task: $("#taskSession").val(), reset: true, useSandbox: sandbox, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
                dataType: "text",
                success: function(d) {
                    statusbar.innerHTML = "Reset success";
                },
                fail: function() {
                    statusbar.innerHTML = "resetUniqueWorkers ajax POST to php/uniqueWorkers.php failed";
                }
            });
        }
    });

// Blue 'Delete Keys' button next to Require Unique Workers checkbox
$("#deleteMturkKeys").on("click", function(event) {
    event.preventDefault();
    if(confirm("Are you sure you delete your Mturk Access and Secret Keys from the server?" +
                "\n\n" +
                "This will disable require unique workers."))
    {
        $.ajax({
            url: retainerLocation + "php/tempKeyStore.php",
            type: "POST",
            data: {reset: 1, accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
            dataType: "text",
            success: function(d) {
                statusbar.innerHTML = "Keys deleted successfully";
                $('#requireUniqueWorkers').prop('checked', false);        
            },
            fail: function() {
                statusbar.innerHTML = "resetUniqueWorkers ajax POST to php/tempKeyStore.php failed";
            }
        });
    }
});


// Copy button on Load Panel - will ask for a new unique task name and duplicate database entry under that name
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
                statusbar.innerHTML = "Copied to " + newTask;
            },
            fail: function() {
                statusbar.innerHTML = "Copying failed";
            }
        });
    });

// Red Delete button on Load Panel, deletes selected experiment from database
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
                    statusbar.innerHTML = "Deleted " + $("#taskSession").val();
                    $("#taskSessionLoad").val("---");
                },
                fail: function() {
                    statusbar.innerHTML = "Deleting failed";
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
                statusbar.innerHTML = "ajax POST to php/loadTask.php failed";
            }
        });
        if(taskData.done == "1"){
            window.clearInterval(isStoppedRecruitingInterval);
            $('#startRecruiting').html('Start recruiting');
            $('#stopRecruiting').html('Stop recruiting');
            $('#startRecruiting').removeAttr('disabled');
            $('#stopRecruiting').attr('disabled','disabled');
            statusbar.innerHTML = "Recruiting stopped. If this was an unwanted, please make sure there is money in your account.";
            return true;
        }
        else return false;
    }

// Validates a task definition in DB is well-specified
    function validateTaskInfo(){
        // Retrieve task info from database via AJAX call to php/loadTask.php
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
                statusbar.innerHTML = "ajax POST to php/loadTask.php failed";
            }
        });

        // Check data fields not null - if one is, return its name to indicate problem
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

        // In direct mode make sure HTTPS is used
        if(mode == "direct"){
            var link  = $("#sendToURL").val();
            if( link.substring(0, 8) != "https://") {
                return('URL, must begin with "https://".');
            }
        }

        // Return empty string => OK
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
     //statusbar.innerHTML = "Invalid URL";
     $('#fireToURL').after("<div id='url-alert' style='color: red; opacity: 0.6'><i>(Invalid URL)</i></div>");
     $('#fireToURL').css("color", "red");
     }
     });
     });
     */

    $("#useRetainerMode").trigger("click");



});
