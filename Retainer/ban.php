<html>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/convInterface/retainer/scripts/gup.js"></script>
<script type="text/javascript"/>
	function ban() {
		$.ajax({
			url: "php/banWID.php",
			type: "POST",
			data: {workerId: gup("workerId")},
			dataType: "text",
			success: function(d) {
				alert("Successfully banned worker's ID");
			},
		});
	}

	$(document).ready( function() {
		$('#ban').click( function() {
			ban();
		});
	});
</script>

Hi. URL param: "workerId=&ltworkerId&gt"

<HR/>

<input type="button" id="ban" value="Click to ban worker"/>


</html>
