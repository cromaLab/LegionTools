$(document).ready(function() {

setInterval( function() {
    // var task = gup('task') ? gup('task') : "default";
    var task = $("#taskSession").val();
        $.ajax({
            url: "php/ajax_whosonline.php",
            type: "POST",
            data: {task: task, role: "trigger"},
            dataType: "text",
            success: function(d) {
                //
                // document.getElementById("numOnline").innerHTML= "There are " + d + " worker(s) online for this task";
                $("#numOnline").text(d);
            },
        fail: function() {
            alert("setOnline failed!")
        },
    });
    }, 1000);
});
