$(document).ready( function() {

    // $('#send_to_tutorial_button').click(function() {
    //     clearQueue('https://roc.cs.rochester.edu/convInterface/videocoding/tutorial/tutorial.php?justTutorial=true');
    // });

    $("#addNewTask").on("click", function(event){
        event.preventDefault();
        $.ajax({
            url: "php/addNewTask.php",
            type: "POST",
            async: false,
            data: {taskTitle: $("#hitTitle").val(), taskDescription: $("#hitDescription").val(), taskKeywords: $("#hitKeywords").val(), task: $("#taskSession").val()},
            dataType: "text",
            success: function(d) {
                
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });
    });

    $("#updatePrice").on("click", function(event){
        event.preventDefault();
        $.ajax({
            url: "php/updatePrice.php",
            type: "POST",
            async: false,
            data: {task: $("#taskSession").val(), min_price: $("#minPrice").val(), max_price: $("#maxPrice").val()},
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
            url: "php/updateTargetNumWorkers.php",
            type: "POST",
            async: false,
            data: {task: $("#taskSession").val(), target_workers: $("#currentTarget").val()},
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
            url: "php/stopRecruiting.php",
            type: "POST",
            async: false,
            data: {task: $("#taskSession").val()},
            dataType: "text",
            success: function(d) {
                
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });

        $('#stopRecruiting').attr('disabled','disabled');
        $('#startRecruiting').removeAttr('disabled');
    });

    $("#startRecruiting").on("click", function(event){
        event.preventDefault();

        $.ajax({
            url: "php/startRecruiting.php",
            type: "POST",
            async: false,
            data: {task: $("#taskSession").val()},
            dataType: "text",
            success: function(d) {
                
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });

        // Start the recruiting tool
        $.ajax({
            url: "../Overview/turk/getAnswers.php",
            type: "POST",
            async: true,
            data: {task: $("#taskSession").val()},
            dataType: "text",
            success: function(d) {
alert(d);
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });

        $('#startRecruiting').attr('disabled','disabled');
        $('#stopRecruiting').removeAttr('disabled');
    });

    $("#loadTask").on("click", function(event){
        event.preventDefault();

        var taskData;
        $.ajax({
            url: "php/loadTask.php",
            type: "POST",
            async: false,
            data: {task: $("#taskSession").val()},
            dataType: "json",
            success: function(d) {
                taskData = d;
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });

        $("#hitTitle").val(taskData.task_title);
        $("#hitDescription").val(taskData.task_description);
        $("#hitKeywords").val(taskData.task_keywords);
        $("#minPrice").val(taskData.min_price);
        $("#maxPrice").val(taskData.max_price);
        $("#currentTarget").val(taskData.target_workers);

        if(taskData.done == "1"){
            $('#stopRecruiting').attr('disabled','disabled');
            $('#startRecruiting').removeAttr('disabled');
        }
        else if(taskData.done == "0"){
            $('#startRecruiting').attr('disabled','disabled');
            $('#stopRecruiting').removeAttr('disabled');
        }


    });

    $("#updateTask").on("click", function(event){
        event.preventDefault();
        $.ajax({
            url: "php/updateTask.php",
            type: "POST",
            async: false,
            data: {taskTitle: $("#hitTitle").val(), taskDescription: $("#hitDescription").val(), taskKeywords: $("#hitKeywords").val(), task: $("#taskSession").val()},
            dataType: "text",
            success: function(d) {
                
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });
    });

    $("#reloadHits").on("click", function(event){
        event.preventDefault();
        var hits = getHits($("#taskSession").val());
        $("#hitsList").empty();
        var counter = 0;
        for (var request in hits) {
            var hitInfo = hits[request];
            var listId = "hit" + counter;
            // alert(obj.Title);
            if(hitInfo.Assignment.AssignmentStatus == "Submitted")
                $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + hitInfo.Assignment.WorkerId + " HITId: " + hitInfo.Assignment.HITId + " <button type='button' onclick = 'approveHit(&quot;" + hitInfo.Assignment.AssignmentId + "&quot;, &quot;" + hitInfo.Assignment.HITId + "&quot;, &quot;" + listId + "&quot;)' class='approveButton btn btn-success btn-sm'>Approve</button> <button type='button' onclick = 'rejectHit(&quot;" + hitInfo.Assignment.AssignmentId + "&quot;, &quot;" + hitInfo.Assignment.HITId + "&quot;, &quot;" + listId + "&quot;)' class='rejectButton btn btn-danger btn-sm'>Reject</button></li>");

            else
                $("#hitsList").append("<li id= '" + listId + "' class='list-group-item'>Worker: " + hitInfo.Assignment.WorkerId + " HITId: " + hitInfo.Assignment.HITId + " <button type='button' onclick = 'disposeHit(&quot;" + hitInfo.Assignment.HITId + "&quot;, &quot;" + listId + "&quot;)' class='disposeButton btn btn-warning btn-sm'>Dispose</button></li>");
            counter++;
        }
    });

$("#approveAll").on("click", function(event){
    event.preventDefault();
    
    $('#hitsList li').each(function() {
        var id = this.id;
        setTimeout(function(){
            $("#" + id + " .approveButton").trigger("click");
        },250);
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
        url: "php/ajax_whosonline.php",
        type: "POST",
        async: false,
        data: {task: task, role: "trigger"},
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
            url: "php/setFire.php",
            type: "POST",
            async: false,
            data: {url: link, task: task},
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
            url: "php/updateReleased.php",
            type: "POST",
            async: false,
            data: {url: link, max: numOnline, task: task},
            dataType: "text",
            success: function(d) {
                
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });
    }
}

$("#fireWorkers").on("click", function(){
    event.preventDefault();

    var task = $("#taskSession").val();
    var link  = $("#fireToURL").val();
    var numFire  = $("#numFire").val();

    if( link.substring(0, 7) != "http://" && link.substring(0, 8) != "https://"  && link.substring(0,3) != "../" ) {
        link = "https://" + link;
    }

    var r = confirm("Fire " + numFire + " workers to: " + link + " ?");
    if(r == true){
        $.ajax({
            url: "php/setFire.php",
            type: "POST",
            async: false,
            data: {url: link, task: task},
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
            url: "php/updateReleased.php",
            type: "POST",
            async: false,
            data: {url: link, max: numFire, task: task},
            dataType: "text",
            success: function(d) {
                
            },
            fail: function() {
                alert("Sending number of workers failed");
            }
        });
    }
});


});
