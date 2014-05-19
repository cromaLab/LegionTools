function getMoney(worker){
	var money = 0;
	$.ajax({
				async: false,
				url: "/convInterface/retainer/php/getTimeWaited.php",
				data: {workerId: worker},
				dataType: "text",
				success: function(d) {
					//
					alert("d: " + d)
					money = Math.round(.02 * d/60.0 * 100.0)/100.0; //assuming 2 cents/min

				},
			fail: function() {
				alert("setLive failed!")
			},
	});

	return money;
}
