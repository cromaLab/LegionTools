
<!DOCTYPE html>
<html>
<head>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="scripts/gup.js"></script>
	<script type="text/javascript" src="scripts/retainer.js" ></script>
</head>

<body>
<!--     <p style="color: red">Please accept the HIT before continuing.</p> -->

 
<p><iframe src="instructions.php" width="100%" class="myIframe"></iframe></p>

<script type="text/javascript" language="javascript"> 
	// point to given instructions page, or default if no param is found
    var task = gup('instructions') ? gup('instructions') : "instructions.php";
	$('.myIframe').attr('src', task);
	$('.myIframe').css('height', $(window).height()+'px');

    function remoteAlert(msg) {
        alert($("#blah").val()); 
        $("#blah").val(2); 
        alert($("#blah").val()); 
    };

</script>

    <input type="hidden" id="blah" value="1">
   
</body>

</html>
 
