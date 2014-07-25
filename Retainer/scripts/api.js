$(document).ready( function() {
    //
    function call(link, numFire, task) {
        //
	   // Check if the link already contains a header
    	if( link.substring(0, 7) != "http://" && link.substring(0, 8) != "https://"  && link.substring(0,3) != "../" ) {
    		// If not, add one
    		//link = link.substring(7);
    		link = "http://" + link;
    	}

    	$.ajax({
            url: "php/setFire.php",
            type: "POST",
            data: {url: link, task: task},
            dataType: "text",
            success: function(d) {
                //
                //alert("Fire successful");
            },
            fail: function() {
                alert("Fire failed!")
            },
    	});

    	$.ajax({
            url: "php/updateReleased.php",
            type: "POST",
            data: {url: link, max: numFire, task: task},
            dataType: "text",
            success: function(d) {

            },
            fail: function() {
                alert("Sending number of workers failed")
            },
    	});
    });

    function query_available(task) {
        $.ajax({
            url: "php/ajax_whosonline.php",
            type: "POST",
            data: {task: task, role: "trigger"},
            dataType: "text",
            success: function(d) {
                //
                return d;
            },
            fail: function() {
                alert("setOnline failed!")
            },
        });
    }


});
