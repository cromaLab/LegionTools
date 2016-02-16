
<!DOCTYPE html>
<html>
<head>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="scripts/gup.js"></script>
</head>

<body>
<!--     <p style="color: red">Please accept the HIT before continuing.</p> -->

<p><iframe id="myFrame" src="" width="100%" class="myTutorialIframe"></iframe></p>

<input type="hidden" id="counter" value="0">


<script type="text/javascript" language="javascript"> 
// point to given instructions page, or default if no param is found
var task = gup('thirdPartyTutUrl');

//alert(task); 

$('.myTutorialIframe').attr('src', task);
$('.myTutorialIframe').css('height', $(window).height()+'px');

document.getElementById('myFrame').onload = function() {
    // check to see what the hidden element's value is
    // if it is 2, that means the src changed a second time (first time is because we load the third party page)
    // so we can now send them to wait.php
    // if counter is true, that means this is the SECOND time src has changed (i.e., tutorial is done) 

    if ($("#counter").val() == 0) {
        $("#counter").val(1).triggerHandler('change');  
    } else {
        $("#counter").val(2).triggerHandler('change');  
    }
}


$(document).ready(function () {
    
    // check to see where the counter value is 

    $(function() {
        var $counter = $('[id$="counter"]'); 

        $counter.on("change", function() {
            var counterVal = $("#counter").val(); 
            if (counterVal == 2) {
                url = "wait.php?"; 
                url += "&workerId=" + gup('workerId');
                url += "&assignmentId=" + gup('assignmentId');
                url += "&hitId=" + gup('hitId');
                url += "&turkSubmitTo=" + gup('turkSubmitTo');
                url += "&task=" + gup('task');
                url += "&min=" + gup('min');
                url += "&instructions=" +  gup('instructions');
                url += "&dbName=" +  gup('dbName');
                url += "&thirdPartyInstrUrl=" + gup('thirdPartyInstrUrl'); 
                //alert(url); 
                window.location = url;    
            }

        }).triggerHandler("change"); 
    });



    //$("#counter").on("change", function () {
    //alert("changed counter value!"); 
    //var counterVal = $("#counter").val(); 
    //if (counterVal == 2) {
    //    url = "wait.php?"; 
    //    url += "&workerId=" + gup('workerId');
        //    url += "&assignmentId=" + gup('assignmentId');
        //    url += "&hitId=" + gup('hitId');
        //    url += "&turkSubmitTo=" + gup('turkSubmitTo');
        //    url += "&task=" + gup('task');
        //    url += "&min=" + gup('min');
        //    url += "&instructions=" +  gup('instructions');
        //    url += "&dbName=" +  gup('dbName');
        //    alert(window.location.href); 
        //    //alert(url); 
        //    //window.location = url;    

        //}
        //}); 

}); 




</script>

</body>

</html>

