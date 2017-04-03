function startWriteNumOnline(){
    setInterval( function() {
        // var task = gup('task') ? gup('task') : "default";
        var task = $("#taskSession").val();
            $.ajax({
                url: "Retainer/php/ajax_whosonline.php",
                type: "POST",
                data: {task: task, role: "trigger", accessKey: $("#accessKey").val(), secretKey: $("#secretKey").val()},
                dataType: "text",
                success: function(d) {
                    //
                    // document.getElementById("numOnline").innerHTML= "There are " + d + " worker(s) online for this task";
                    $("#numOnline").text(d);
                
                   // // at this point, we should split up the "d" 
                   // // first value is the count of numbers, so ignore that one
                   // var wIds = d.split(" ")[1].split("\n"); 
                   // //console.log(wIds); 

                   // // loop through wIds (ignore last element because that will be an empty string)
                   // var elemStr = "<table><tr> <th> Checkbox </th> <th> <center> WorkerId </center> </th>";
                   // for(var i=0; i<wIds.length; i++){
                   //     if(wIds[i] != ""){
                   //         elemStr += '<tr> <td> <input id="box_'+wIds[i]+'" type="checkbox" name="WorkerCheckBox"> </td>' +
                   //                 '<td>'+wIds[i]+'</td></tr>';
                   //     }                        
                   // }
                   // elemStr += "</table>"; 
                   // console.log(elemStr); 
                   // $("#dynamicCheckBoxes").html( elemStr );
                
                
                },
            fail: function() {
                alert("setOnline failed!")
            },
        });
    }, 1000);
}
