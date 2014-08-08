$(document).ready(function() {

    var url = decodeURIComponent(gup('url'));
    var requireUniqueWorkers = gup('requireUniqueWorkers');

    alert(url + " " + requireUniqueWorkers);

    if(gup('assignmentId') == "ASSIGNMENT_ID_NOT_AVAILABLE" || requireUniqueWorkers != "true"){
        window.location.replace(url);
    }

    else{
        $.ajax({
            type: 'POST',
            url: 'Retainer/php/uniqueWorkers.php',
            data: {workerId: gup("workerId"), task: gup('task'), assignQualification: true},
            success: function (d) {
                // alert(d);
                window.location.replace(url);
            }
        });
    }
});
