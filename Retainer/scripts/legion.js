
var _score = 0;

var pointRecord = {};

function initPointRecord() {
    for (x in pointMapping) {
        pointRecord[x] = 1;
    }
}

function legion_reset_actions() {
    initPointRecord();
}
initPointRecord();

function legion_reward(action, element) {
	if(pointMapping[action]) {
    	if(action!="abstain" && action!="punish_post" && action!="punish_vote"){	// && action!="vote"){
			var scoreIncrement = Math.floor(pointMapping[action] / pointRecord[action]);

        	pointRecord[action]*=1;
        }else{
        	if(action=="punish_post" || action=="punish_vote"){
				var scoreIncrement = pointMapping[action];
			}else if(action=="abstain"){
        		var scoreIncrement = parseInt($("#votes-allowed").text()) * 5;
        	}	
        }

	if(typeof(element)==undefined || (Control.usePoints && !Control.useAnim) ) {
	    _score += scoreIncrement;
	    updateScore();

	} else {

            // animate the points coming out of it

	    var curr_x = $(element).offset().left + Math.floor($(element).width()/2);
	    var curr_y = $(element).offset().top + Math.floor($(element).height()/2);

	    var c = $("<div>"+scoreIncrement+"</div>");
            c.width(150);

            c.css('position', 'absolute');
            c.offset({top: curr_y, left: curr_x});

            if(action=="punish_vote" || action=="punish_post"){
            	c.addClass('legion-score-display-r');
            }else{
	            c.addClass('legion-score-display-a');
	        }
            c.css('opacity', 0.0);

	        $(document.body).append(c);

	        var dest_x = $("#legion-points").offset().left;
	        var dest_y = $("#legion-points").offset().top;

	        var diff_x = dest_x - curr_x;
	        var diff_y = dest_y - curr_y;

            $(c).animate({
		        top:  ((diff_y < 0) ? "-=": "+=") + Math.abs(diff_y),
		        opacity: Control.useAnim ? 1.0 : 0.01
            }, 1600, function() {
		        c.css('width', 'auto');
		        $(c).animate({
                    opacity: Control.useAnim ? 0.5 : 0.01,
		            left: ((diff_x < 0) ? "-=": "+=") + Math.abs(diff_x)
		        }, {
                    duration: 1200,
                    complete: function() {
			            c.remove();
				if( Control.usePoints ) {
			            _score += scoreIncrement;
			            updateScore();
				}
                    },
                    step: function(now, fx) {
			            if(fx.prop == "opacity") {
                            /*var offset = (now - fx.start);
                              var range = (fx.end - fx.start);
                              
                              var frac = offset / range;
                              var abs = (1 / children);
                              var val = Math.floor(frac / abs);
                              
                              for(var i=score_add.length-val; i<score_add.length; i++) {
				              _score += score_add[i];
				              score_add[i] = 0;
                              }
                              updateScore();*/
			            }
                    }
		        });                
            });
	    }
    }
}

//  Turkify the page.
$(document).ready(function () {
        if(Control.usePoints) $('#sidebar').prepend($('<div id="legion-score"><span id="legion-instructions-top" class="legion-instructions">You have earned ~$<span id="legion-money">0.00</span></span><br/><span class="legion-points" id="legion-points">--</span><br/><span id="legion-instructions-bottom" class="legion-instructions">(depending on quality check)</span></div>'));

    if(gup("assignmentId")!="") {
        // create form
        $('#instructions').append($('<div id="legion-submit-div"><p id="legion-submit-instructions">' + submitInstructionsText + '</p><form id="legion-submit-form"><input type="hidden" name="money" value="0" id="legion-money-field"><input type="hidden" name="session" value="' + gup('session') + '" id="legion-session-field"><input type="hidden" name="assignmentId" id="legion-assignmentId"><input id="legion-submit" type="button" value="Submit HIT"></div>'));

        var jobkey=gup("assignmentId");
        if(gup("hitId")!="") {
	        jobkey += "|" + gup("hitId");
        }

        if(gup("assignmentId") == "ASSIGNMENT_ID_NOT_AVAILABLE") {
	        $('input').attr("DISABLED", "true");
	        _allowSubmit = false;
        } else {
	        _allowSubmit = true;
        }
        $('#legion-assignmentId').attr('value', gup("assignmentId"));
        $("#legion-submit-form").attr('method', 'POST');
    

        if(gup("turkSubmitTo")!="") {
            $("#legion-submit-form").attr('action', gup("turkSubmitTo") + '/mturk/externalSubmit');
        }

        $("#legion-submit").attr("DISABLED", "true");
        $("#legion-submit").on("click", submitToTurk);
    }
});

// Wrapper for default alert behavior
//function submitToTurk(ev, opts) {
function submitToTurk(ev) {
    var m = Math.ceil(parseFloat($("#legion-money").text()) * 100.00) / 100.00; //Money earned from points
    var timeWaited = 0;
    if(useRetainerTool == true){
        $.ajax({
            type: 'GET',
            url: getTimeWaitedURL,
            async: false,
            data: "workerId="+gup("workerId"),
            success: function(d) {
                timeWaited = d;
                alert(timeWaited);
            },
            error: function() {
                setTurkMessage("message_error");
                //alert("Fail");
            }
        });

        var totalMoney = m + (timeWaited * centsPerSecondWaited); //adds the money earned by the worker for waiting in the retainer tool.
        $("#legion-money-field").attr("value", totalMoney);

	// if( opts["useAlert"] ) {
        	alert('Your HIT is being submitted. A quality check will be performed on your work, and you will be bonused up to $' + m + ' based on the results, in additon to $' + (timeWaited * centsPerSecondWaited) + ' for waiting. Generally, payments are processed within one hour.');
	// }
    }
 //    else {
	// if( opts["useAlert"] ) {
	// 	alert('Your HIT is being submitted. A quality check will be performed on your work, and you will be bonused up to $' + m + ' based on the results. Generally, payments are processed within one hour.');
	// }
    // }

    if(ev && typeof ev != "undefined") {
	ev.preventDefault();
    }
    $("#legion-submit-form").submit();

    return false;
}

function autoApproveHIT() {
    $.ajax({
	    type: 'POST',
	    url: auto_approve_url,
	    data: "a="+gup("assignmentId"),
	    success: function() {
            setTurkMessage("message_approved");
            autoApproveHIT();
	    },
	    error: function() {
            setTurkMessage("message_error");
	    }
    });
}


function setTurkMessage(id) {
    $("#submission_results .messages").hide();
    $("#"+id).show();
}

function updateScore() {
    $("#legion-points").html(Math.floor(_score));
    var m = Math.round(((_score / pointsPerDollar)) * 1000.0) / 1000.0;
    if(!/\./.test(m)) {
        m += ".000";
    } else if (/\.\d\d$/.test(m)) {
        m += "0";
    } else if (/\.\d$/.test(m)) {
        m += "00";
    }
    $("#legion-money").html(m);
    $("#legion-money-field").attr('value', m);

    if(parseFloat(m) > min_money) {
        $("#legion-submit").removeAttr("DISABLED");
    }
}
