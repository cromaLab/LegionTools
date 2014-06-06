/**
 *  
 *  gup(name) :: retrieves URL parameters if provided
 *
 *  Prepares the page for MTurk on load.
 *  1. looks for a form element with id="mturk_form", and sets its METHOD / ACTION
 *    1a. All that the task page needs to do is submit the form element when ready
 *  2. disables form elements if HIT hasn't been accepted
 *
 **/

// selector used by jquery to identify your form
var form_selector = "#mturk_form";

// function for getting URL parameters
function gup(name) {
	// Every regex in the world needs a comment describing it!
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");

  var regexS  = "[\\?&]"+name+"=([^&#]*)";
  var regex   = new RegExp(regexS);
  var results = regex.exec(window.location.href);
	// Always check for what you are looking for, instead of what it shouldn't be
	//   this is called "whitelisting" as opposed to "blacklisting"
  if(typeof results !== "String") {
	  return "";
  }

  return unescape(results[1]);
}

//  Turkify the captioning page.
$(document).ready(function () {
  // is assigntmentId is a URL parameter
  if((aid = gup("assignmentId"))!="" && $(form_selector).length>0) {

    // If the HIT hasn't been accepted yet, disabled the form fields.
	  // Always use triple equality in js
    if(aid === "ASSIGNMENT_ID_NOT_AVAILABLE") {
	    $('input,textarea,select').attr("DISABLED", "disabled");
    }

    // Add a new hidden input element with name="assignmentId" that
    // with assignmentId as its value.
	  // name jquery objects with a $ in front of name - good code convention
    var $aid_input = $("<input type='hidden' name='assignmentId' value='" + aid + "'>").appendTo($(form_selector));
    var $workerId_input = $("<input type='hidden' name='workerId' value='" + gup("workerId") + "'>").appendTo($(form_selector));
    var $hitId_input = $("<input type='hidden' name='hitId' value='" + gup("hitId") + "'>").appendTo($(form_selector));

    // Make sure the submit form's method is POST
    $(form_selector).attr('method', 'POST');

    // Set the Action of the form to the provided "turkSubmitTo" field
    if((submit_url=gup("turkSubmitTo"))!="") {
      $(form_selector).attr('action', submit_url + '/mturk/externalSubmit');
    }
  }
});