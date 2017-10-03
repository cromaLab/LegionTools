$(document).ready(function() {
    console.log('.:: taskLanding.js');

    var url = decodeURIComponent(gup('url'));
    if(url.indexOf('?') === -1){
      url = url + "?";
    }
    else url = url + "&";
    url = url + "workerId=" + gup("workerId") + "&assignmentId=" + gup('assignmentId') + "&hitId=" + gup('hitId') + "&turkSubmitTo=" + gup('turkSubmitTo');
    url = url.split("&amp;&amp;").join("&");

    var requireUniqueWorkers = gup('requireUniqueWorkers');

    // alert(url + " " + requireUniqueWorkers);

    // Redirect if no legit MTurk worker, or if requester has not required unique workers for the HIT
    if(gup('assignmentId') == "ASSIGNMENT_ID_NOT_AVAILABLE" || requireUniqueWorkers != "true"){
        window.location.replace(url);
    }
    // Otherwise check that worker has not yet completed the HIT
    else{
        $.ajax({
            type: 'POST',
            url: 'Retainer/php/uniqueWorkers.php',
            data: {workerId: gup("workerId"), task: gup('task'), assignQualification: true, turkSubmitTo: gup('turkSubmitTo'), dbName: gup('dbName')},
            success: function (d) {
                alert(d);
                window.location.replace(url);
            }
        });
    }
});
