var firstCheck = true;

var interval = setInterval( function() {
    //alert("URL: " + window.location.href.slice(window.location.href.indexOf('?') + 1))
    var assignment = gup("assignmentId");
    //if( assignment != "ASSIGNMENT_ID_NOT_AVAILABLE" ) {
	$.ajax({
		type: 'POST',
		async:false,
		url: 'php/triggerCheck.php',
		data: {task: gup('task') ? gup('task') : "default", first: firstCheck, dbName: gup('dbName')},
		success: function(data) {
			// alert(data);
			if(data != null && data != "") {
				//var moneyOwed = getMoney(gup('workerId'));
				//var r = confirm("Now transfering you to " + data);
				clearInterval(interval);
				alert("Now transfering you to the task");

				var send = false;
				url = data;
				$.ajax({
					type: 'POST',
					async:false,
					url: 'php/releasedCheck.php',
					data: {url: url, task: gup('task') ? gup('task') : "default", dbName: gup('dbName')},
					success: function(data) {
						if(data == "true") send = true;
					}
				});
				
				if(send == true){
					updateTime(gup('workerId'));
					if( data.indexOf('?') != -1 ) {
						url += "&";
					}
					else {
						url += "?";
					}

					url += "workerId=" + gup('workerId');
					url += "&assignmentId=" + gup('assignmentId');
					url += "&hitId=" + gup('hitId');
					url += "&turkSubmitTo=" + gup('turkSubmitTo');
					url += "&task=" + gup('task');
					url += "&min=" + gup('min');

					window.location = url;
				}
				else{
					alert("Sorry, enough workers have already responded.")
					window.location.reload();
				}
			}

			firstCheck = false;
		}
	});
    //}
}, 3000);
