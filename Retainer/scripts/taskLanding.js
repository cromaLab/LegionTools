$(document).ready(function() {


    var url = decodeURIComponent(gup('url'));
    if(url.indexOf('?') === -1){
      url = url + "?";
    }
    else url = url + "&";
    url = url + "workerId=" + gup("workerId") + "&assignmentId=" + gup('assignmentId') + "&hitId=" + gup('hitId') + "&turkSubmitTo=" + gup('turkSubmitTo');
    url = url.split("&amp;&amp;").join("&");


    var requireUniqueWorkers = gup('requireUniqueWorkers');

    // alert(url + " " + requireUniqueWorkers);

    if(gup('assignmentId') == "ASSIGNMENT_ID_NOT_AVAILABLE" || requireUniqueWorkers != "true"){
        window.location.replace(url);
    }

    else{
        $.ajax({
            type: 'POST',
            url: 'Retainer/php/uniqueWorkers.php',
            data: {workerId: gup("workerId"), task: gup('task'), assignQualification: true, turkSubmitTo: gup('turkSubmitTo')},
            success: function (d) {
                // alert(d);
                window.location.replace(url);
            }
        });
    }
});
