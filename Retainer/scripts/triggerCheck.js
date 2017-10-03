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
			if(data != null && data != "") {
				//var r = confirm("Now transfering you to " + data);
				clearInterval(interval);
				//alert("Now transferring you to the task");
				alert("Now transferring you to the task");

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
				    var moneyOwed = getMoney(gup('workerId'));
					
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
					url += "&min=" + gup('min');
					url += "&getMoneyOwed=" + moneyOwed;
                    url += "&requireUniqueWorkers=" +gup('requireUniqueWorkers');
                    url += "&dbName=" + gup("dbName"); 
					url += "&task=" + gup('task');  // WSL: I don't think we use this information, and passing the retainer task name can conflict if the user's params include 'task'. Removed for now. SWL: I added it back cuz I need to use this to revoke qualification for people who are cleared from the pool. 
                    //alert(window.location);
                    //alert(window.location.href);
                    //$('.myIframe').attr('src', url); 
                    //$('.myIframe').css('height', $(window).height()+'px'); 
                    // alert(url); 
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
