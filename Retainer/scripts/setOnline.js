$(document).ready(function() {
setInterval( function() {
    var worker = gup("workerId");
    var assignment = gup("assignmentId");
    var task = gup('task') ? gup('task') : "default";
    //if( assignment != "ASSIGNMENT_ID_NOT_AVAILABLE" ) {
        $.ajax({
            url: "php/ajax_whosonline.php",
            data: {task: task, worker: worker, role: "crowd", dbName: gup('dbName')},
            dataType: "text",
            success: function(d) {
                //
            },
            fail: function() {
                alert("setOnline failed!")
            },
        });
    //}
}, 3000);
});
