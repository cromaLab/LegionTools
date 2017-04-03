var pointsPerDollar = 20000;
var pointMapping = {"hit": 100};
var min_money = 0.00; //minimum amount of money earned to allow submission

var useRetainerTool = true; //set to true if the retainer tool is used.
// var getTimeWaitedURL = "http://roc.cs.rochester.edu/LegionJS/LegionTools/Retainer/php/getTimeWaited.php"; //the URL of the tool to get the time a worker has waited in the retainer tool.
var getTimeWaitedURL = "https://141.212.108.210/LegionTools/Retainer/php/getTimeWaited.php"; //the URL of the tool to get the time a worker has waited in the retainer tool.
var centsPerSecondWaited = .05; //the numbers of cents to award the worker for each second waited.
var submitInstructionsText = "Thanks for your help on this task! Please click submit to receive payment.";

//contains URL parameters.
var Control = {
usePoints: false, //if "points=true" then the point system will be used. 
useAnim: false, //if "anim=true" then animation will be used. 
useCorrect: false, //if "correct=true" then the variable "useCorrect" will be set to true. You may use this in your project to determine if workers should be given differing point amounts that depend on their performance.

init: function() {
	Control.usePoints = gup('points') == 'true' ? true : false;
	Control.useAnim = gup('anim') == 'true' ? true : false;
	Control.useCorrect = gup('correct') == 'true' ? true : false;

	// LegionJS TESTING: This controls the appearance of the score box 
	if( !Control.usePoints ) {
		// Then hide the scorebox
		$('#legion-score').hide();
	}

},


    
}

$(document).ready(function() {
    Control.init();

    $("#legion-submit").removeAttr("DISABLED");
});
