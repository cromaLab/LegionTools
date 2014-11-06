<!DOCTYPE html>
<html>
<head>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="scripts/gup.js"></script>
    <script type="text/javascript" src="scripts/updateEndTime.js"></script>
    <script type="text/javascript" src="scripts/getMoneyOwed.js"></script>
    <script type="text/javascript" src="scripts/triggerCheck.js"></script>
    <script type="text/javascript" src="scripts/setOnline.js"></script>
</head>

<body>
    <p>Please keep this page open until the task begins. To earn your pay for waiting, you must be available as soon as you are alerted. Depending on your browser you will be alerted in different ways, but keep an eye out for a pop-up or flashing tab in your browser. Since you will be able to see the alert from other tabs, you are free to take other tasks while you wait, <b>as long as you do NOT close this tab</b>, as that will remove you from the waiting area.</p>

<p><iframe src="instructions.php" width="100%" class="myIframe"></iframe></p>
<script type="text/javascript" language="javascript"> 
	// point to given instructions page, or default if no param is found
	var task = gup('instructions') ? gup('instructions') : "instructions.php?task=" + gup("task");
    var task = task + "&dbName=" + gup("dbName");
	$('.myIframe').attr('src', task);
	
	$('.myIframe').css('height', $(window).height()+'px');
</script>
</body>

</html>
 
