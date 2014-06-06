$(document).ready(function() {
	setInterval( function() {
		var worker     = gup("workerId"),
			assignment = gup("assignmentId"),
			task       = gup('task') ? gup('task') : "default";

		//if( assignment === "ASSIGNMENT_ID_NOT_AVAILABLE" ) {
		// 		return;
	    // }
		$.ajax({
			url: "php/ajax_whosonline.php",
			dataType: "text",
			data: {
				task       : task,
				worker     : worker,
				assignment : assignment,
				role       : "crowd"
			},
			success: function() { },
			fail: function() {
				throw {
					name: 'counterfailed',
					message:'something important here'
				}
			}
		});
	}, 3000);
});
// Wow i think i wrote this one???
