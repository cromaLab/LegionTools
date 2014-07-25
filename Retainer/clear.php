<html>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/convInterface/retainer/scripts/gup.js"></script>
<script type="text/javascript"/>
	function remove() {
		$.ajax({
			url: "php/clearWID.php",
			type: "POST",
			data: {workerId: gup("workerId")},
			dataType: "text",
			success: function(d) {
				alert("Successfully removed worker's ID from DB");
			},
		});
	}

	$(document).ready( function() {
		$('#remove').click( function() {
			remove();
		});
	});
</script>

Hi.

<HR/>

<input type="button" id="remove" value="Click to Clear Worker From Online DB"/>


</html>
