function updateTime(worker)
{
	$.ajax({
            url: "php/updateEndTime.php",
            async: false,
            data: {workerId: worker},
            dataType: "text",
            success: function(d) {
                //
                
            },
        fail: function() {
            alert("updating end time failed!")
        },
    });
}