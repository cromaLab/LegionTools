/**
 *  
 *  gup(name) :: retrieves URL parameters if provided
 *
 *  Prepares the page for MTurk on load.
 *  1. looks for a form element with id="mturk_form", and sets its METHOD / ACTION
 *    1a. All that the task page needs to do is submit the form element when ready
 *  2. disables form elements if HIT hasn't been accepted
 * 
 *
 *
 *  Copyright 2017  -  Walter S. Lasecki, Jeffrey P. Bigham
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 *  and associated documentation files (the "Software"), to deal in the Software without
 *  restriction, including without limitation the rights to use, copy, modify, merge, publish,
 *  distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
 *  Software is furnished to do so, subject to the following conditions:
 *  The above copyright notice and this permission notice shall be included in all copies or
 *  substantial portions of the Software.
 * 
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
 *  BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 *  DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 **/

// selector used by jquery to identify your form
var form_selector = "#mturk_form";

// function for getting URL parameters
function gup(name) {
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.href);
  if(results == null)
    return "";
  else return unescape(results[1]);
}

//  Turkify the captioning page.
$(document).ready(function () {
  // is assigntmentId is a URL parameter
  if((aid = gup("assignmentId"))!="" && $(form_selector).length>0) {

    // If the HIT hasn't been accepted yet, disabled the form fields.
    if(aid == "ASSIGNMENT_ID_NOT_AVAILABLE") {
	    $('input,textarea,select').attr("DISABLED", "disabled");
    }

    // Add a new hidden input element with name="assignmentId" that
    // with assignmentId as its value.
    var aid_input = $("<input type='hidden' name='assignmentId' value='" + aid + "'>").appendTo($(form_selector));

    // Make sure the submit form's method is POST
    $(form_selector).attr('method', 'POST');

    // Set the Action of the form to the provided "turkSubmitTo" field
    if((submit_url=gup("turkSubmitTo"))!="") {
      $(form_selector).attr('action', submit_url + '/mturk/externalSubmit');
    }
  }
});
