// PROCESS VARS
var processCount = 0;
var processExists = false;

// Elements
var progressBar;
var processes;


var page_ready = page_ready === undefined ? true : page_ready;



window.onbeforeunload = function(){

	if (processExists) return true;

};




$(document).ready(function() {

	// Elements
	progressBar = $('.progress-bar');

	// Update the process count
	processCount = $('.progress-bar > .process:not(.done)').length;

});




// FUNCTION: Start Process
function newProcess(preventWindowClose, processDescription) { // Add timeout function here !!!


	// Default
	preventWindowClose = assignDefault(preventWindowClose, true);
	processDescription = assignDefault(processDescription, "");


	var newProcessID = processCount + 1;


	// Stop auto-refresh, and abort the current request
	if (typeof stopAutoRefresh === "function") stopAutoRefresh();
	if (typeof stopNotificationAutoRefresh === "function") stopNotificationAutoRefresh();


	// Add the new process
	progressBar.append('<div class="progress process" data-process-id="'+ newProcessID +'" data-process-desc="'+ processDescription +'"></div>');

	// The new process
	var process = $('.process[data-process-id="'+ newProcessID +'"]:not(.done)');

	// Reset the progress bar
	process.css('width', '0%');


	// Update the process count
	processCount = $('.progress-bar > .process:not(.done)').length;


	// Prevent closing window
	if (preventWindowClose) {

		processExists = true;
		console.log('PROCESS EXISTS');

	}


	console.log('Process Count: ', processCount);
	return newProcessID;
}

function editProcess(processID, percentage) {

	var process = $('.process[data-process-id="'+ processID +'"]:not(.done)');

	// Change the progress bar
	process.css('width', percentage+'%');


	console.log('Process Count: ', processCount);
}


// FUNCTION: Finishes the process
function endProcess(processID) {

	var process = $('.process[data-process-id="'+ processID +'"]:not(.done)');


	// Fill the progress bar
	process.css('width', '100%').addClass('done');


	// Remove the process
	process.fadeOut(1000, function() {
		$(this).remove();
	});


	// Update the process count
	processCount = $('.progress-bar > .process:not(.done)').length;


	// If no process left
	if (processCount == 0) {

		// Allow closing window
		processExists = false;
		console.log('PROCESS NOT EXISTS');


		// Restart auto-refresh
		if (typeof startAutoRefresh === "function" && page_ready) startAutoRefresh();
		if (typeof startNotificationAutoRefresh === "function" && page_ready) startNotificationAutoRefresh();

	}


	// Update the pins list tab
	if (typeof updatePinsList === "function" && pinsListOpen) updatePinsList();


	console.log('Process Count: ', processCount);

}