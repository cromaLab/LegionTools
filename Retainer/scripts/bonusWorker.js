$(document).ready(function() {
    $( "form" ).on( "submit", function( event ) {
        event.preventDefault();
        if( confirm("Bonus $" + $("#bonusAmount").val() + " to worker " + $("#workerId").val() + "?") ){
            $.ajax({
                type: 'POST',
                url: 'Retainer/php/processHIT.php',
                data: $('form').serialize(),
                success: function (d) {
                  // alert( $('form').serialize());
                  console.log(d);
                  if(d == "True"){
                    $("#bonusedHistory").append($("#workerId").val() + " : $" + $("#bonusAmount").val() + "</br>");
                  }
                  else alert("Bonus failed");
                  alert(d);
                }
            });
        }
    });

});
